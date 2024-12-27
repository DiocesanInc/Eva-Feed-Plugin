<?php

namespace Eva\Plugin;
// prevent direct access
if (!defined('ABSPATH')) exit;

// singleton!
class Controller
{

    // singleton instance
    private static $instance = false;

    // global plugin objects
    public $Transport;

    // options config, make these global so other objects can refer to them
    protected $evaFeedUrl = 'dpi_eva_feed_url';
    protected $evaSubscribeLink = 'dpi_eva_subscribe_link';
    protected $evaPrimaryColor = 'dpi_eva_primary_color';
    protected $evaSecondaryColor = 'dpi_eva_secondary_color';
    protected $feedTable = 'dpi_eva_items';

    /**
     * Get or create controller instance
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new Controller();
        }

        return self::$instance;
    }

    /**
     * Register our custom post type for housing feed items
     */
    public function registerCPT()
    {

        // Set UI labels for Custom Post Type
        $labels = array(
            'name'                => _x('Eva Feed Item', 'Post Type General Name', 'diocesan'),
            'singular_name'       => _x('Eva Feed Item', 'Post Type Singular Name', 'diocesan'),
            'menu_name'           => __('Eva Feed Items', 'diocesan'),
            'parent_item_colon'   => __('Parent Eva Feed Item', 'diocesan'),
            'all_items'           => __('All Eva Feed Items', 'diocesan'),
            'view_item'           => __('View Eva Feed Item', 'diocesan'),
            'add_new_item'        => __('Add New Eva Feed Item', 'diocesan'),
            'add_new'             => __('Add New Eva Feed Item', 'diocesan'),
            'edit_item'           => __('Edit Eva Feed Item', 'diocesan'),
            'update_item'         => __('Update Eva Feed Item', 'diocesan'),
            'search_items'        => __('Search Eva Feed Items', 'diocesan'),
            'not_found'           => __('Not Found', 'diocesan'),
            'not_found_in_trash'  => __('Not found in Trash', 'diocesan'),
        );

        // Set other options for Custom Post Type
        $args = array(
            'label'               => __(DPI_EVA_FEED_CPT, 'diocesan'),
            'description'         => __('Eva Feed Items', 'diocesan'),
            'menu_icon'              => 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4NCjwhLS0gR2VuZXJhdG9yOiBBZG9iZSBJbGx1c3RyYXRvciAyMy4wLjEsIFNWRyBFeHBvcnQgUGx1Zy1JbiAuIFNWRyBWZXJzaW9uOiA2LjAwIEJ1aWxkIDApICAtLT4NCjxzdmcgdmVyc2lvbj0iMS4xIiBpZD0iTGF5ZXJfMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgeD0iMHB4IiB5PSIwcHgiDQoJIHZpZXdCb3g9IjAgMCA0MCAzNCIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgNDAgMzQ7IiB4bWw6c3BhY2U9InByZXNlcnZlIj4NCjxzdHlsZSB0eXBlPSJ0ZXh0L2NzcyI+DQoJLnN0MHtmaWxsOnVybCgjU1ZHSURfMV8pO30NCjwvc3R5bGU+DQo8bGluZWFyR3JhZGllbnQgaWQ9IlNWR0lEXzFfIiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSIgeDE9IjAuNjQwNSIgeTE9IjE3IiB4Mj0iMzkuMzU5NSIgeTI9IjE3Ij4NCgk8c3RvcCAgb2Zmc2V0PSIwIiBzdHlsZT0ic3RvcC1jb2xvcjojOTk2NkJGIi8+DQoJPHN0b3AgIG9mZnNldD0iMSIgc3R5bGU9InN0b3AtY29sb3I6IzYxNzRDOSIvPg0KPC9saW5lYXJHcmFkaWVudD4NCjxwYXRoIGNsYXNzPSJzdDAiIGQ9Ik0zNi41Niw1LjNjLTAuNjMsMC0yLjA0LDAtMy44OSwwYy02LjI2LTAuMDEtMTcuOTMtMC4wMi0yMS43OSwwQzguMDQsNS4zMiw1LjQ0LDYuMzEsMy41NSw4LjENCgljLTEuODcsMS43Ny0yLjkxLDQuMTgtMi45MSw2Ljc3YzAsNS4yMyw0LjIyLDkuNDksOS40Myw5LjU3djIuNTNjMCwwLjY5LDAuNDIsMS4zMiwxLjA2LDEuNmMwLjIyLDAuMSwwLjQ1LDAuMTQsMC42NywwLjE0DQoJYzAuNDEsMCwwLjgtMC4xNSwxLjExLTAuNDRjMC42NC0wLjYsMy40OS0zLjI1LDMuODQtMy41N2wwLjAyLTAuMDJjMC4yNS0wLjIzLDAuMjUtMC4yMywwLjU2LTAuMjNoNC44NmMxLjU3LDAsMi45LTAuMzYsMy44NC0xLjA1DQoJYzAuODktMC42NSwxLjQtMS41NywxLjQtMi41MmMwLTAuMjctMC4wNC0wLjUyLTAuMTEtMC43NmgwLjQ0YzEuNTcsMCwyLjktMC4zNiwzLjg0LTEuMDVjMC44OS0wLjY1LDEuNC0xLjU3LDEuNC0yLjUyDQoJYzAtMC4yOS0wLjA1LTAuNTctMC4xMy0wLjgzYzEuMDYtMC4xNCwxLjk2LTAuNDcsMi42NS0wLjk4YzAuODktMC42NSwxLjQtMS41NywxLjQtMi41MmMwLTAuNDItMC4wOS0wLjgtMC4yNi0xLjE0DQoJYzAuNDktMC4xNywwLjkzLTAuMzksMS4zMS0wLjY3YzAuODktMC42NSwxLjQtMS41NywxLjQtMi41MkMzOS4zNiw2LjM5LDM4LjE4LDUuMywzNi41Niw1LjN6IE0zNi44OSw4Ljk0DQoJYy0wLjQ0LDAuMzItMS4yNywwLjctMi43NiwwLjdjMCwwLDAsMCwwLDBjMCwwLTAuMDEsMC0wLjAxLDBIMTAuODJjLTIuNDIsMC00LjM1LDEuMDUtNS4zMSwyLjg4Yy0wLjE2LDAuMzEtMC4yOSwwLjY1LTAuMzksMC45OQ0KCWMtMC4xMywwLjQ4LDAuMTUsMC45OSwwLjYzLDEuMTJjMC40OCwwLjEzLDAuOTktMC4xNSwxLjEyLTAuNjNjMC4wNi0wLjIyLDAuMTQtMC40MywwLjI1LTAuNjNjMC42NC0xLjIzLDEuOTUtMS45LDMuNjktMS45aDIzLjI5DQoJYzAuNiwwLDAuOTgsMC4yOSwwLjk4LDAuNzZjMCwwLjM3LTAuMjQsMC43NS0wLjY1LDEuMDVjLTAuNDQsMC4zMi0xLjI3LDAuNy0yLjc2LDAuN2gtOS4zNmMtMC41LDAtMC45MSwwLjQxLTAuOTEsMC45MQ0KCWMwLDAuNSwwLjQxLDAuOTEsMC45MSwwLjkxaDcuODhjMC42LDAsMC45OCwwLjI5LDAuOTgsMC43NmMwLDAuMzctMC4yNCwwLjc1LTAuNjUsMS4wNWMtMC40NCwwLjMyLTEuMjcsMC43LTIuNzYsMC43aC0zLjEyDQoJYzAsMCwwLDAtMC4wMSwwYzAsMCwwLDAsMCwwSDIwYy0wLjUsMC0wLjkxLDAuNDEtMC45MSwwLjkxYzAsMC41LDAuNDEsMC45MSwwLjkxLDAuOTFoNC42M2MwLjYsMCwwLjk3LDAuMjksMC45NywwLjc2DQoJYzAsMC4zNy0wLjI0LDAuNzUtMC42NSwxLjA1Yy0wLjQ0LDAuMzItMS4yNywwLjctMi43NiwwLjdoLTQuODZjLTAuOTQsMC0xLjMyLDAuMjgtMS43OCwwLjdsLTAuMDIsMC4wMg0KCWMtMC4zMywwLjMtMi43NywyLjU4LTMuNjQsMy4zOHYtMi4zNWMwLTEuMDItMC42LTEuNjgtMS42MS0xLjc1Yy0wLjAyLDAtMC4wNCwwLTAuMDcsMGMtNC4yNywwLTcuNzUtMy40OC03Ljc1LTcuNzUNCgljMC00LjM5LDMuNjItNy43Miw4LjQzLTcuNzVjMy44NS0wLjAyLDE1LjUxLTAuMDEsMjEuNzcsMGMxLjg2LDAsMy4yNiwwLDMuOSwwYzAuNiwwLDAuOTgsMC4yOSwwLjk4LDAuNzYNCglDMzcuNTQsOC4yNSwzNy4zLDguNjMsMzYuODksOC45NHoiLz4NCjwvc3ZnPg0K',
            'labels'              => $labels,
            // Features this CPT supports in Post Editor
            'supports'            => array('title', 'editor', 'excerpt', 'author', 'thumbnail', 'revisions', 'custom-fields'),
            // You can associate this CPT with a taxonomy or custom taxonomy.
            'taxonomies'          => array('theme', 'post_tag'),
            /* A hierarchical CPT is like Pages and can have
			* Parent and child items. A non-hierarchical CPT
			* is like Posts.
			*/
            'rewrite'              => array('slug' => DPI_EVA_FEED_CPT),
            'hierarchical'        => true,
            'public'              => true,
            'show_ui'             => false,
            'show_in_menu'        => false,
            'show_in_nav_menus'   => false,
            'show_in_admin_bar'   => false,
            'menu_position'       => 5,
            'can_export'          => true,
            'has_archive'         => true,
            'exclude_from_search' => false,
            'publicly_queryable'  => true,
            'capability_type'     => 'post',
        );


        // Registering your Custom Post Type
        register_post_type(DPI_EVA_FEED_CPT, $args);

        // Reset permalinks so our feed items will work
        flush_rewrite_rules();
    }

    public function registerEVAStreamCPT()
    {
        // Set UI labels for Custom Post Type
        $labels = array(
            'name'                => _x('Eva Stream', 'Post Type General Name', 'diocesan'),
            'singular_name'       => _x('Eva Stream', 'Post Type Singular Name', 'diocesan'),
            'menu_name'           => __('Eva Streams', 'diocesan'),
            'parent_item_colon'   => __('Parent Eva Stream', 'diocesan'),
            'all_items'           => __('All Eva Streams', 'diocesan'),
            'view_item'           => __('View Eva Streams', 'diocesan'),
            'add_new_item'        => __('Add New Eva Stream', 'diocesan'),
            'add_new'             => __('Add New Eva Stream', 'diocesan'),
            'edit_item'           => __('Edit Eva Stream', 'diocesan'),
            'update_item'         => __('Update Eva Stream', 'diocesan'),
            'search_items'        => __('Search Eva Streams', 'diocesan'),
            'not_found'           => __('Not Found', 'diocesan'),
            'not_found_in_trash'  => __('Not found in Trash', 'diocesan'),
        );

        // Set other options for Custom Post Type
        $args = array(
            'label'               => __(DPI_EVA_STREAM_CPT, 'diocesan'),
            'description'         => __('Eva Streams', 'diocesan'),
            'menu_icon'              => 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4NCjwhLS0gR2VuZXJhdG9yOiBBZG9iZSBJbGx1c3RyYXRvciAyMy4wLjEsIFNWRyBFeHBvcnQgUGx1Zy1JbiAuIFNWRyBWZXJzaW9uOiA2LjAwIEJ1aWxkIDApICAtLT4NCjxzdmcgdmVyc2lvbj0iMS4xIiBpZD0iTGF5ZXJfMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgeD0iMHB4IiB5PSIwcHgiDQoJIHZpZXdCb3g9IjAgMCA0MCAzNCIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgNDAgMzQ7IiB4bWw6c3BhY2U9InByZXNlcnZlIj4NCjxzdHlsZSB0eXBlPSJ0ZXh0L2NzcyI+DQoJLnN0MHtmaWxsOnVybCgjU1ZHSURfMV8pO30NCjwvc3R5bGU+DQo8bGluZWFyR3JhZGllbnQgaWQ9IlNWR0lEXzFfIiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSIgeDE9IjAuNjQwNSIgeTE9IjE3IiB4Mj0iMzkuMzU5NSIgeTI9IjE3Ij4NCgk8c3RvcCAgb2Zmc2V0PSIwIiBzdHlsZT0ic3RvcC1jb2xvcjojOTk2NkJGIi8+DQoJPHN0b3AgIG9mZnNldD0iMSIgc3R5bGU9InN0b3AtY29sb3I6IzYxNzRDOSIvPg0KPC9saW5lYXJHcmFkaWVudD4NCjxwYXRoIGNsYXNzPSJzdDAiIGQ9Ik0zNi41Niw1LjNjLTAuNjMsMC0yLjA0LDAtMy44OSwwYy02LjI2LTAuMDEtMTcuOTMtMC4wMi0yMS43OSwwQzguMDQsNS4zMiw1LjQ0LDYuMzEsMy41NSw4LjENCgljLTEuODcsMS43Ny0yLjkxLDQuMTgtMi45MSw2Ljc3YzAsNS4yMyw0LjIyLDkuNDksOS40Myw5LjU3djIuNTNjMCwwLjY5LDAuNDIsMS4zMiwxLjA2LDEuNmMwLjIyLDAuMSwwLjQ1LDAuMTQsMC42NywwLjE0DQoJYzAuNDEsMCwwLjgtMC4xNSwxLjExLTAuNDRjMC42NC0wLjYsMy40OS0zLjI1LDMuODQtMy41N2wwLjAyLTAuMDJjMC4yNS0wLjIzLDAuMjUtMC4yMywwLjU2LTAuMjNoNC44NmMxLjU3LDAsMi45LTAuMzYsMy44NC0xLjA1DQoJYzAuODktMC42NSwxLjQtMS41NywxLjQtMi41MmMwLTAuMjctMC4wNC0wLjUyLTAuMTEtMC43NmgwLjQ0YzEuNTcsMCwyLjktMC4zNiwzLjg0LTEuMDVjMC44OS0wLjY1LDEuNC0xLjU3LDEuNC0yLjUyDQoJYzAtMC4yOS0wLjA1LTAuNTctMC4xMy0wLjgzYzEuMDYtMC4xNCwxLjk2LTAuNDcsMi42NS0wLjk4YzAuODktMC42NSwxLjQtMS41NywxLjQtMi41MmMwLTAuNDItMC4wOS0wLjgtMC4yNi0xLjE0DQoJYzAuNDktMC4xNywwLjkzLTAuMzksMS4zMS0wLjY3YzAuODktMC42NSwxLjQtMS41NywxLjQtMi41MkMzOS4zNiw2LjM5LDM4LjE4LDUuMywzNi41Niw1LjN6IE0zNi44OSw4Ljk0DQoJYy0wLjQ0LDAuMzItMS4yNywwLjctMi43NiwwLjdjMCwwLDAsMCwwLDBjMCwwLTAuMDEsMC0wLjAxLDBIMTAuODJjLTIuNDIsMC00LjM1LDEuMDUtNS4zMSwyLjg4Yy0wLjE2LDAuMzEtMC4yOSwwLjY1LTAuMzksMC45OQ0KCWMtMC4xMywwLjQ4LDAuMTUsMC45OSwwLjYzLDEuMTJjMC40OCwwLjEzLDAuOTktMC4xNSwxLjEyLTAuNjNjMC4wNi0wLjIyLDAuMTQtMC40MywwLjI1LTAuNjNjMC42NC0xLjIzLDEuOTUtMS45LDMuNjktMS45aDIzLjI5DQoJYzAuNiwwLDAuOTgsMC4yOSwwLjk4LDAuNzZjMCwwLjM3LTAuMjQsMC43NS0wLjY1LDEuMDVjLTAuNDQsMC4zMi0xLjI3LDAuNy0yLjc2LDAuN2gtOS4zNmMtMC41LDAtMC45MSwwLjQxLTAuOTEsMC45MQ0KCWMwLDAuNSwwLjQxLDAuOTEsMC45MSwwLjkxaDcuODhjMC42LDAsMC45OCwwLjI5LDAuOTgsMC43NmMwLDAuMzctMC4yNCwwLjc1LTAuNjUsMS4wNWMtMC40NCwwLjMyLTEuMjcsMC43LTIuNzYsMC43aC0zLjEyDQoJYzAsMCwwLDAtMC4wMSwwYzAsMCwwLDAsMCwwSDIwYy0wLjUsMC0wLjkxLDAuNDEtMC45MSwwLjkxYzAsMC41LDAuNDEsMC45MSwwLjkxLDAuOTFoNC42M2MwLjYsMCwwLjk3LDAuMjksMC45NywwLjc2DQoJYzAsMC4zNy0wLjI0LDAuNzUtMC42NSwxLjA1Yy0wLjQ0LDAuMzItMS4yNywwLjctMi43NiwwLjdoLTQuODZjLTAuOTQsMC0xLjMyLDAuMjgtMS43OCwwLjdsLTAuMDIsMC4wMg0KCWMtMC4zMywwLjMtMi43NywyLjU4LTMuNjQsMy4zOHYtMi4zNWMwLTEuMDItMC42LTEuNjgtMS42MS0xLjc1Yy0wLjAyLDAtMC4wNCwwLTAuMDcsMGMtNC4yNywwLTcuNzUtMy40OC03Ljc1LTcuNzUNCgljMC00LjM5LDMuNjItNy43Miw4LjQzLTcuNzVjMy44NS0wLjAyLDE1LjUxLTAuMDEsMjEuNzcsMGMxLjg2LDAsMy4yNiwwLDMuOSwwYzAuNiwwLDAuOTgsMC4yOSwwLjk4LDAuNzYNCglDMzcuNTQsOC4yNSwzNy4zLDguNjMsMzYuODksOC45NHoiLz4NCjwvc3ZnPg0K',
            'labels'              => $labels,
            // Features this CPT supports in Post Editor
            'supports'            => array(
                'title',
                'author',
                'revisions',
                'custom-fields'
            ),
            // You can associate this CPT with a taxonomy or custom taxonomy.
            'taxonomies'          => array('theme', 'post_tag'),
            /* A hierarchical CPT is like Pages and can have
			* Parent and child items. A non-hierarchical CPT
			* is like Posts.
			*/
            'rewrite'              => array('slug' => DPI_EVA_STREAM_CPT),
            'hierarchical'        => true,
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_nav_menus'   => true,
            'show_in_admin_bar'   => true,
            'menu_position'       => 6,
            'can_export'          => true,
            'has_archive'         => true,
            'exclude_from_search' => false,
            'publicly_queryable'  => true,
            'capability_type'     => 'post',
        );


        // Registering your Custom Post Type
        register_post_type(DPI_EVA_STREAM_CPT, $args);

        // Reset permalinks so our feed items will work
        flush_rewrite_rules();
    }

    public function eva_stream_feed_url_metabox()
    {
        add_meta_box(
            'eva_stream_feed_url',
            'Feed URL',
            [$this, 'eva_stream_feed_url_metabox_callback'],
            DPI_EVA_STREAM_CPT,
            'normal',
            'high'
        );
    }

    function eva_stream_subscription_url_metabox()
    {
        add_meta_box(
            'eva_stream_subscription_url',
            'Subscription URL',
            [$this, 'eva_stream_subscription_url_metabox_callback'],
            DPI_EVA_STREAM_CPT,
            'normal',
            'high'
        );
    }

    function eva_stream_shortcode_metabox()
    {
        add_meta_box(
            'eva_stream_shortcode',
            'Shortcode',
            [$this, 'eva_stream_shortcode_metabox_callback'],
            DPI_EVA_STREAM_CPT,
            'normal',
            'high'
        );
    }

    function eva_stream_feed_url_metabox_callback($post)
    {
        wp_nonce_field(basename(__FILE__), 'eva_stream_feed_url_nonce');
        $value = get_post_meta($post->ID, '_eva_stream_feed_url', true);
        echo '<input type="text" id="eva_stream_feed_url" class="large-text" name="eva_stream_feed_url" value="' . esc_attr($value) . '">';
    }

    function eva_stream_subscription_url_metabox_callback($post)
    {
        wp_nonce_field(basename(__FILE__), 'eva_stream_subscription_url_nonce');
        $value = get_post_meta($post->ID, '_eva_stream_subscription_url', true);
        echo '<input type="text" id="eva_stream_subscription_url" class="large-text" name="eva_stream_subscription_url" value="' . esc_attr($value) . '">';
    }

    function eva_stream_shortcode_metabox_callback($post)
    {
        echo "<p>[eva_feed id='$post->ID']</p>";
    }

    function eva_stream_save_metabox_data($post_id)
    {
        // Check if our nonce is set.
        if (!isset($_POST['eva_stream_feed_url_nonce']) || !isset($_POST['eva_stream_subscription_url_nonce'])) {
            return $post_id;
        }

        // Verify that the nonce is valid.
        if (!wp_verify_nonce($_POST['eva_stream_feed_url_nonce'], basename(__FILE__)) || !wp_verify_nonce($_POST['eva_stream_subscription_url_nonce'], basename(__FILE__))) {
            return $post_id;
        }

        // Check if the current user has permission to edit the post.
        if (!current_user_can('edit_post', $post_id)) {
            return $post_id;
        }

        // Save the feed URL metabox data.
        if (isset($_POST['eva_stream_feed_url'])) {
            update_post_meta($post_id, '_eva_stream_feed_url', sanitize_text_field($_POST['eva_stream_feed_url']));
        } else {
            delete_post_meta($post_id, '_eva_stream_feed_url');
        }

        // Save the subscription URL metabox data.
        if (isset($_POST['eva_stream_subscription_url'])) {
            update_post_meta($post_id, '_eva_stream_subscription_url', sanitize_text_field($_POST['eva_stream_subscription_url']));
        } else {
            delete_post_meta($post_id, '_eva_stream_subscription_url');
        }
    }

    /**
     * Load up necessary plugin objects based on the current view
     */
    public function init()
    {

        if (is_admin()) {
            new PluginPage([
                'pageTitle' => 'Eva Feed Options',
                'menuTitle' => 'Eva Feed',
                'capability' => 'manage_options'
            ]);

            add_action('add_meta_boxes_' . DPI_EVA_STREAM_CPT, [$this, "eva_stream_feed_url_metabox"]);
            add_action('add_meta_boxes_' . DPI_EVA_STREAM_CPT, [$this, "eva_stream_subscription_url_metabox"]);
            add_action('add_meta_boxes_' . DPI_EVA_STREAM_CPT, [$this, "eva_stream_shortcode_metabox"]);
            add_action('save_post_' . DPI_EVA_STREAM_CPT, [$this, 'eva_stream_save_metabox_data']);
        } else {
            $this->Transport = new Transport();
            new Shortcodes();
        }
    }

    /**
     * Attempt to retrieve user input feed URL from options table, otherwise return false
     */
    public function getFeedUrl($id = null)
    {
        if ($id) {
            $url = get_post_meta($id, '_eva_stream_feed_url', true);
        } else {
            $url = get_option($this->evaFeedUrl, false);
        }

        return $url ? $url : false;
    }

    /**
     * Attempt to retrieve user input subscribe link from options table, otherwise return false
     */
    public function getSubscribeLink($id = null)
    {

        if ($id) {
            $url = get_post_meta($id, '_eva_stream_subscription_url', true);
        } else {
            $url = get_option($this->evaSubscribeLink, false);
        }


        return $url ? $url : false;
    }

    /**
     * Attempt to retrieve user input color from options table, otherwise return false
     */
    public function getStyleColor($option)
    {
        switch ($option) {
            case 'primary':
                $color = get_option($this->evaPrimaryColor, false);
                break;
            case 'secondary':
                $color = get_option($this->evaSecondaryColor, false);
                break;
        }

        if (!$color) {
            return false;
        }

        return $color;
    }

    /**
     * Return Feed Table name
     */
    public function getFeedTable()
    {
        return $this->feedTable;
    }
}
