<?php

/*
Plugin Name: DPI Eva Feed
Plugin URI: http://www.diocesan.com
Description: Integration with Evangelus
Version: 0.9.4
Author: Diocesan
Author URI: http://www.diocesan.com
License: GPLv2
*/

// prevent direct access
if (!defined('ABSPATH')) exit;

// constants
define('DPI_EVA_FEED_ROOT', __FILE__);
define('DPI_EVA_FEED_DIR', __DIR__);
define('DPI_EVA_FEED_VER', '0.9.4');
define('DPI_EVA_FEED_CPT', 'eva');
define('DPI_EVA_FEED_CPT_STREAM_ID_COL', 'eva-feed-stream-id');
define('DPI_EVA_STREAM_CPT', 'eva-stream');
define('DPI_EVA_STREAM_CPT_SHORTCODE_COL', 'eva-stream_shortcode_col');
define('DPI_EVA_STREAM_CPT_FEED_URL_COL', 'eva-stream_feed_url_col');
define('DPI_EVA_FEED_PLUGIN', plugin_basename(__FILE__));

// autoload plugin classes
require DPI_EVA_FEED_DIR . '/psr4-autoloader.php';

// add classes for media_sideload_image
require_once(ABSPATH . 'wp-admin/includes/media.php');
require_once(ABSPATH . 'wp-admin/includes/file.php');
require_once(ABSPATH . 'wp-admin/includes/image.php');

// initialize
add_action('init', function () {

    $plugin = Eva\Plugin\Controller::getInstance();
    $plugin->checkForUpdates();
    $plugin->registerCPT();
    $plugin->registerEVAStreamCPT();
    $plugin->init();
    check_eva_feed();
}, 0);

function add_custom_query_var($vars)
{
    $vars[] = "eva_stream_id";
    return $vars;
}
add_filter("query_vars", "add_custom_query_var");

function filter_eva_feed_items_by_eva_stream_id($query)
{
    if (is_admin() || !is_post_type_archive(DPI_EVA_FEED_CPT)) {
        return;
    }

    $eva_stream_id = get_query_var("eva_stream_id");
    if (!empty($eva_stream_id)) {
        $query->set("meta_key", "_eva_stream_id");
        $query->set("meta_value", $eva_stream_id);
    }
}
add_action("pre_get_posts", "filter_eva_feed_items_by_eva_stream_id");

//Add Custom Columns to Eva Stream CPT
add_filter('manage_' . DPI_EVA_STREAM_CPT . '_posts_columns', 'eva_stream_custom_columns');
function eva_stream_custom_columns($columns)
{
    $columns[DPI_EVA_STREAM_CPT_SHORTCODE_COL] = 'Shortcode';
    $columns[DPI_EVA_STREAM_CPT_FEED_URL_COL] = 'Feed URL';
    return $columns;
}

add_action('manage_' . DPI_EVA_STREAM_CPT . '_posts_custom_column', 'eva_stream_custom_column_data', 10, 2);

function eva_stream_custom_column_data($column_name, $post_id)
{
    switch ($column_name) {
        case DPI_EVA_STREAM_CPT_SHORTCODE_COL:
            $shortcode = "[eva_feed id='$post_id']";
            echo esc_html($shortcode);
            break;
        case DPI_EVA_STREAM_CPT_FEED_URL_COL:
            $feed_url = get_post_meta($post_id, '_eva_stream_id', true);
            echo esc_html($feed_url);
            break;
    }
}

//Add Custom Columns To Eva Feed Items CPT
add_filter('manage_' . DPI_EVA_FEED_CPT . '_posts_columns', 'eva_feed_item_custom_columns');
function eva_feed_item_custom_columns($columns)
{
    $columns[DPI_EVA_FEED_CPT_STREAM_ID_COL] = 'EVA Stream ID';
    return $columns;
}

add_action('manage_' . DPI_EVA_FEED_CPT . '_posts_custom_column', 'eva_feed_item_custom_column_data', 10, 2);

function eva_feed_item_custom_column_data($column_name, $post_id)
{
    switch ($column_name) {
        case DPI_EVA_FEED_CPT_STREAM_ID_COL:
            $stream_id = get_post_meta($post_id, '_eva_stream_id', true);
            echo esc_html($stream_id);
            break;
    }
}


// setup admin settings
add_action('admin_enqueue_scripts', 'eva_enqueue_color_picker');
function eva_enqueue_color_picker()
{
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('eva-settings', plugins_url('/js/eva-admin.js', __FILE__), array('wp-color-picker'), false, true);
}

function dpi_eva_feed_enqueue_block_editor_assets()
{
    wp_enqueue_style(
        'eva-feed-editor-styles',
        plugin_dir_url(__FILE__) . 'blocks/eva-feed-carousel/carousel.css',
        array(),
        filemtime(plugin_dir_path(__FILE__) . 'blocks/eva-feed-carousel/carousel.css')
    );

    wp_enqueue_script(
        "dpi-eva-feed-eva-feed-carousel-block",
        plugins_url("blocks/eva-feed-carousel/index.js", __FILE__),
        array("wp-blocks", "wp-element")
    );

    //get all posts of post type eva-stream
    $eva_streams = get_posts([
        "post_type" => DPI_EVA_STREAM_CPT,
        "posts_per_page" => -1
    ]);

    $options = array_map(function ($post) {
        return [
            "value" => $post->ID,
            "label" => $post->post_title,
        ];
    }, $eva_streams);

    wp_localize_script('dpi-eva-feed-eva-feed-carousel-block', 'dpi_eva_feed_vars', array(
        'eva_feed_streams' => $options,
    ));
}
add_action(
    "enqueue_block_editor_assets",
    "dpi_eva_feed_enqueue_block_editor_assets"
);


// use our custom template for eva CPT single & archive
add_filter('single_template', 'eva_custom_templates', 50, 1);
add_filter('archive_template', 'eva_custom_templates', 50, 1);
function eva_custom_templates($template)
{

    global $post;

    if (is_singular('eva')) {
        wp_enqueue_style('dpi-eva', plugins_url('/css/feed.css', __FILE__), null, DPI_EVA_FEED_VER, 'all');
        $slug = "single-eva";
        if (file_exists(get_template_directory() . '/templates/' . $slug . '.php')) {
            $template = get_template_directory() . '/templates/' . $slug . '.php';
        } else if (file_exists(get_template_directory() . '/' . $slug . '.php')) {
            $template = get_template_directory() . '/' . $slug . '.php';
        } else if (file_exists(get_stylesheet_directory() . '/templates/' . $slug . '.php')) {
            $template = get_stylesheet_directory() . '/templates/' . $slug . '.php';
        } else if (file_exists(get_stylesheet_directory() . '/' . $slug . '.php')) {
            $template = get_stylesheet_directory() . '/' . $slug . '.php';
        } else {
            $template = DPI_EVA_FEED_DIR . '/templates/single-eva.php';
        }
    }

    if (is_post_type_archive('eva')) {
        wp_enqueue_style('dpi-eva', plugins_url('/css/feed.css', __FILE__), null, DPI_EVA_FEED_VER, 'all');
        wp_enqueue_script('dpi-eva', plugins_url('/js/feed.js', __FILE__), null, DPI_EVA_FEED_VER, true);

        $slug = "archive-eva";

        if (file_exists(get_template_directory() . '/templates/' . $slug . '.php')) {
            $template = get_template_directory() . '/templates/' . $slug . '.php';
        } else if (file_exists(get_template_directory() . '/' . $slug . '.php')) {
            $template = get_template_directory() . '/' . $slug . '.php';
        } else if (file_exists(get_stylesheet_directory() . '/templates/' . $slug . '.php')) {
            $template = get_stylesheet_directory() . '/templates/' . $slug . '.php';
        } else if (file_exists(get_stylesheet_directory() . '/' . $slug . '.php')) {
            $template = get_stylesheet_directory() . '/' . $slug . '.php';
        } else {
            $template = DPI_EVA_FEED_DIR . '/templates/archive-eva.php';
        }
    }

    return $template;
}


// Schedule our cron
register_activation_hook(__FILE__, 'eva_feed_activate_plugin');

function eva_feed_activate_plugin()
{ // runs on plugin activation
    if (!wp_next_scheduled('eva_feed_cron_cache')) {
        wp_schedule_event(time(), 'hourly', 'eva_feed_cron_cache'); // eva_feed_cron_cache is a hook
    }

    $plugin = Eva\Plugin\Controller::getInstance();

    // Create database for tracking imported items
    $table_name = $plugin->getFeedTable();

    global $wpdb;

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
		id int(11) NOT NULL AUTO_INCREMENT,
		post_id  int(11) NOT NULL,
		item_url text NOT NULL,
		PRIMARY KEY  (id)
	) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
};

// Featured Image size for carousel
add_image_size('eva-carousel', 320, 176, true);

// hook the cron
add_action('eva_feed_cron_cache', 'check_eva_feed');

// function that will run houry
function check_eva_feed()
{
    global $wpdb;
	
	require_once ABSPATH . '/wp-admin/includes/post.php';  // Needed for post_exists()

    $plugin = Eva\Plugin\Controller::getInstance();

    $items = $wpdb->get_results("SELECT item_url FROM {$plugin->getFeedTable()}", "ARRAY_A");

    $imported = array();
    foreach ($items as $item) {
        $imported[] = $item['item_url'];
    }

    $args = array(
        'post_type' => DPI_EVA_STREAM_CPT,
        'posts_per_page' => -1,
    );

    $eva_stream_posts = get_posts($args);

    foreach ($eva_stream_posts as $esp) {
        $rss_feed = simplexml_load_file($plugin->getFeedUrl($esp->ID));

        if (!empty($rss_feed)) {
            $i = 0;

            foreach ($rss_feed->channel->item as $feed_item) {
                if ($i >= 10) break;

                if (!in_array((string)$feed_item->link, $imported, true)) {

					// Add it to our array of imported items
                    $imported[] = (string)$feed_item->link;

                    $s = explode("/", $feed_item->link);

                    $post_id = null;

                    $args = array(
                        'post_type' => DPI_EVA_FEED_CPT,
                        'post_status'       => 'publish',
                        'post_title'        => wp_strip_all_tags((string)$feed_item->title),
                        'post_date'         => date('Y-m-d H:i:s', strtotime($feed_item->pubDate)),

                        'meta_query' => array(
                            array(
                                'key' => 'eva_link',
                                'value' => (string)$feed_item->link,
                            )
                        )
                    );

                    $query = new WP_Query($args);

                    if (!$query->have_posts()) {
						
						// Remove all conditional tags from description
						$theContent = preg_replace('/(?=<!--)([\s\S]*?)-->/', '', (string)$feed_item->description );
						
                        // Create post object
                        $eva_item = array(
                            'post_title'        => wp_strip_all_tags((string)$feed_item->title),
                            'post_name'         => end($s),
                            'post_content'      => $theContent,
                            'post_date'         => date('Y-m-d H:i:s', strtotime($feed_item->pubDate)),
                            'post_status'       => 'publish',
                            'post_type'         => DPI_EVA_FEED_CPT
                        );
						
						$post_title = wp_strip_all_tags((string)$feed_item->title);
						
						// Double check if a post exists based on title
						if ( ! post_exists($post_title,'','',DPI_EVA_FEED_CPT) ) { 

							// Insert the post into the database
							$post_id = wp_insert_post($eva_item);
						}
                    }


                    if ($post_id) {
                        // Store the original link in case we want to use it
                        add_post_meta($post_id, "eva_link", (string)$feed_item->link, true);
                        //Store EVA Stream Post ID for referencing where it came from
                        add_post_meta($post_id, "_eva_stream_id", $esp->ID, true);

                        // Featured image - Image in feed, else Find first image
                        preg_match_all(
                            '/<img.+?src=[\'"]([^\'"]+)[\'"].*?>/i',
                            (string)$feed_item->description,
                            $matches
                        );
                        $url = ($feed_item->image->url) ? (string)$feed_item->image->url : $matches[1][0];
                        $desc = "";

                        // Copy image to media library
                        $image = media_sideload_image($url, $post_id, $desc, 'id');

                        // Set as featured image
                        if (!is_wp_error($image)) {
                            // Set image as post thumbnail
                            set_post_thumbnail($post_id, $image);
                        }

                        // Log the import entry so we don't keep importing it
                        $table = $plugin->getFeedTable();
                        $data = array('post_id' => $post_id, 'item_url' => (string)$feed_item->link);
                        $format = array('%d', '%s');
                        $wpdb->insert($table, $data, $format);
                    }
                }

                $i++;
            }
        }
    }
}

add_action("wp_trash_post", "delete_feed_item");

function delete_feed_item($post_id)
{
    if (get_post_type($post_id) === DPI_EVA_FEED_CPT) {
        global $wpdb;
        $plugin = Eva\Plugin\Controller::getInstance();

        // Create database for tracking imported items
        $table_name = $plugin->getFeedTable();

        $wpdb->delete($table_name, array("post_id" => $post_id), array("%d"));
    }
}

register_deactivation_hook(__FILE__, 'eva_feed_deactivation');

function eva_feed_deactivation()
{
    wp_clear_scheduled_hook('check_eva_feed_cache');
}

add_filter('the_content', 'remove_wpautop');

function remove_wpautop($content)
{
    global $post;
    if ($post->post_type === DPI_EVA_FEED_CPT) {
        remove_filter("the_content", "wpautop");
    }

    return $content;
}

add_filter( 'get_the_archive_title', 'eva_archive_title', 999);
function eva_archive_title ( $title ) {

    // eva_stream_id
    if( is_post_type_archive( DPI_EVA_FEED_CPT ) && $_GET['eva_stream_id']) {
        global $wpdb;

        $plugin = Eva\Plugin\Controller::getInstance();
        
        $rss_feed = simplexml_load_file($plugin->getFeedUrl( $_GET['eva_stream_id'] ) );
    
        $title =  $rss_feed->channel->title . " from Evangelus";
    }

    return $title;

};