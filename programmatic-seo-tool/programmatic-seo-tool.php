<?php
/**
 * Plugin Name: WP Programmatic SEO Tool
 * Description: Analyzes word count and keyword density in published posts.
 * Version: 1.0
 * Author: Evgenija Butleska
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Add Admin Menu
function wppseo_add_admin_menu() {
    add_menu_page(
        'WP Programmatic SEO Tool',    
        'WP SEO Tool',                
        'manage_options',             
        'wp_programmatic_seo_tool',   
        'wppseo_admin_page',           
        'dashicons-chart-bar',        
        30                            
    );
}
add_action( 'admin_menu', 'wppseo_add_admin_menu' );

// Admin page content
function wppseo_admin_page() {
    ?>
    <div class="wrap">
        <h1>WP Programmatic SEO Tool</h1>
        <p>Here are the word count and keyword density analysis for all your posts:</p>
        <?php wppseo_render_table(); ?>
    </div>
    <?php
}

// Analyze posts and calculate word count and keyword density
function wppseo_analyze_posts() {
    $args = array(
        'post_type'   => 'post',
        'post_status' => 'publish',
        'numberposts' => -1,
    );
    
    $posts = get_posts( $args );
    $results = [];

    foreach ( $posts as $post ) {
        $content = strip_tags( $post->post_content );
        $word_count = str_word_count( $content );
        $keyword_density = wppseo_calculate_keyword_density( $content, 'your-keyword' ); // Replace with dynamic input

        $results[] = [
            'title' => $post->post_title,
            'word_count' => $word_count,
            'keyword_density' => $keyword_density
        ];
    }

    return $results;
}

// Calculate keyword density
function wppseo_calculate_keyword_density( $content, $keyword ) {
    $words = str_word_count( strtolower( $content ), 1 );
    $keyword_count = array_count_values( $words )[$keyword] ?? 0;
    $word_count = count( $words );

    return ( $word_count > 0 ) ? ( $keyword_count / $word_count ) * 100 : 0;
}

// Register REST API endpoint
function wppseo_register_api_endpoints() {
    register_rest_route( 'wppseo/v1', '/post-analysis/', array(
        'methods' => 'GET',
        'callback' => 'wppseo_get_post_analysis',
        'permission_callback' => 'wppseo_api_permission_check',
    ));
}
add_action( 'rest_api_init', 'wppseo_register_api_endpoints' );

// Callback for REST API endpoint
function wppseo_get_post_analysis() {
    $analysis_data = wppseo_analyze_posts();
    return new WP_REST_Response( $analysis_data, 200 );
}

// Permission check for REST API endpoint
function wppseo_api_permission_check() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return new WP_REST_Response( 'Unauthorized', 403 );
    }
    return true;
}

// Enqueue scripts and styles for both admin and frontend
function wppseo_enqueue_scripts() {
    wp_enqueue_style( 'datatables-style', 'https://cdn.datatables.net/2.2.1/css/dataTables.dataTables.min.css' );
    wp_enqueue_script( 'datatables-script', 'https://cdn.datatables.net/2.2.1/js/dataTables.min.js', array( 'jquery' ));

    wp_enqueue_style( 'wppseo-style', plugin_dir_url( __FILE__ ) . 'assets/style.css' );
    wp_enqueue_script( 'wppseo-script', plugin_dir_url( __FILE__ ) . 'assets/script.js', array( 'jquery' ), null, true );
   
}
add_action( 'admin_enqueue_scripts', 'wppseo_enqueue_scripts' );
add_action( 'wp_enqueue_scripts', 'wppseo_enqueue_scripts' );

// Render table template
function wppseo_render_table() {
    $analysis_data = wppseo_analyze_posts();

    if ( empty( $analysis_data ) ) {
        echo '<p>No posts found.</p>';
        return;
    }

    ?>
    <table id="pca-table" class="wppseo-frontend-table">
        <thead>
            <tr>
                <th>Post Title</th>
                <th>Word Count</th>
                <th>Keyword Density (%)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ( $analysis_data as $data ) : ?>
                <tr>
                    <td><?php echo esc_html( $data['title'] ); ?></td>
                    <td><?php echo esc_html( $data['word_count'] ); ?></td>
                    <td><?php echo esc_html( $data['keyword_density'] ); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php
}

// Shortcode to display the table on the frontend
function wppseo_display_table() {
    ob_start();
    wppseo_render_table();
    return ob_get_clean();
}
add_shortcode( 'wppseo_table', 'wppseo_display_table' );
