<?php

namespace Eva\Plugin;

// prevent direct access
if (!defined('ABSPATH')) exit;

class Transport
{


    /**
     * Retrieve Eva posts
     */
    public function getEvaPosts($stream_id)
    {

        $args = array(
            'post_type' => 'eva',
            'post_status' => 'publish',
            'posts_per_page' => 12,
            'orderby' => 'date',
            'order' => 'DESC',
            'meta_query' => array(
                array(
                    'key' => '_eva_stream_id',
                    'value' => $stream_id,
                )
            )
        );

        return get_posts($args);
    }
}
