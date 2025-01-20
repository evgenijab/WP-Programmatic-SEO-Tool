# WP Programmatic SEO Tool

## Description

The WP Programmatic SEO Tool plugin analyzes the word count and keyword density in your published posts, providing insights into the optimization of your content. This tool helps you track keyword usage and post length, aiding in SEO improvements.

The plugin includes an admin page to display the analysis in a table and a custom REST API endpoint to retrieve the same data programmatically.

## Features

- **Word Count**: Get the word count for all your published posts.
- **Keyword Density**: Track the density of a specified keyword in your posts.
- **DataTable Integration**: Display the analysis results in an interactive table using [DataTables](https://datatables.net/).
- **Custom REST API**: Access the analysis data via a secure REST API endpoint.

## Installation

1. Upload the `wp-programmatic-seo-tool` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.

## Usage

### Admin Page

Once activated, you'll find a new menu item called **WP SEO Tool** in the WordPress admin dashboard. From there, you can view the word count and keyword density analysis of all your published posts in an interactive table.

The table uses **DataTables** for enhanced functionality such as sorting, searching, and pagination. It provides an easy-to-read format for your post analysis.

### Frontend Shortcode

To display the analysis table on the frontend of your website, you can use the `[wppseo_table]` shortcode. This will render the table on any post or page where the shortcode is placed.

### REST API Endpoint

The plugin provides a secure custom REST API endpoint to access the word count and keyword density data programmatically.

**API Endpoint:**

GET /wp-json/wppseo/v1/post-analysis/


**Response:**

The response will return a JSON array with the analysis data for each published post, including the post title, word count, and keyword density percentage.

Example response:

```json
[
  {
    "title": "Post Title 1",
    "word_count": 350,
    "keyword_density": 1.5
  },
  {
    "title": "Post Title 2",
    "word_count": 420,
    "keyword_density": 2.1
  }
]
