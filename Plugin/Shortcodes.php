<?php

namespace Eva\Plugin;

// prevent direct access
if (!defined('ABSPATH')) exit;

class Shortcodes
{

    private $eva_feedShortcode = 'eva_feed';

    /**
     * Init, run wp hooks
     */
    public function __construct()
    {
        add_shortcode($this->eva_feedShortcode, [$this, 'shortcodeHandler']);
    }

    /**
     * Sanitize shortcode parameters
     */
    private function sanitizeParams(array $params)
    {
        return array_map(function ($dirty) {
            switch (gettype($dirty)) {
                case 'boolean':
                    return filter_var($dirty, FILTER_VALIDATE_BOOLEAN);
                case 'integer':
                    return round(filter_var($dirty, FILTER_SANITIZE_NUMBER_FLOAT));
                default:
                    return sanitize_text_field($dirty);
            }
        }, $params);
    }

    /**
     * Shortcode callback handler
     */
    public function shortcodeHandler($params, $content, $shortcode)
    {
        $Controller = Controller::getInstance();


        $args = ($this->sanitizeParams(shortcode_atts([
            'url' => $Controller->getFeedUrl($params["id"]),
            'subscribe' => $Controller->getSubscribeLink(),
            'title' => false,
            'format' => false,
            "stream_id" => $params["id"]
        ], $params)));

        if (!$args['url']) {
            return false;
        }

        switch ($shortcode) {

            case $this->eva_feedShortcode:
                $evaPosts = $Controller->Transport->getEvaPosts($args["stream_id"]);
                $evaSubscribeLink = $Controller->getSubscribeLink($args["stream_id"]);
                $archiveLink = home_url(DPI_EVA_FEED_CPT . "/?eva_stream_id=" . $args["stream_id"]);
                $evaPrimary = $Controller->getStyleColor('primary');
                $evaSecondary = $Controller->getStyleColor('secondary');
                wp_enqueue_style('font-open-sans', 'https://fonts.googleapis.com/css?family=Open+Sans:400,700');
                wp_enqueue_style('font-lato', 'https://fonts.googleapis.com/css?family=Lato:400,400italic,700,700italic');
                wp_enqueue_script('slick', plugins_url('../js/slick/slick.min.js', __FILE__), null, '1.0.0', true);
                wp_enqueue_style('slick-style', plugins_url('../js/slick/slick.css', __FILE__), null, '1.0.0', 'all');
                wp_enqueue_style('slick-theme', plugins_url('../js/slick/slick-theme.css', __FILE__), null, '1.0.0', 'all');
                wp_enqueue_style('dpi-eva', plugins_url('../css/feed.css', __FILE__), null, DPI_EVA_FEED_VER, 'all');
                wp_enqueue_script('dpi-eva', plugins_url('../js/feed.js', __FILE__), null, DPI_EVA_FEED_VER, true);
                ob_start();
                include DPI_EVA_FEED_DIR . '/templates/carousel.php';
                #include DPI_EVA_FEED_DIR . '/templates/debug.php';
                return ob_get_clean();
        }
    }
}
