<?php 

if ( !defined( 'ABSPATH' ) ) exit;

return [
    [
        'id' => 'dpi_eva_feed_url',
        'title' => 'Eva Feed URL',
        'section' => 'dpi_eva_feed_options',
        'inputType' => 'url'
    ],
	[
        'id' => 'dpi_eva_subscribe_link',
        'title' => 'Eva Subcribe Link',
        'section' => 'dpi_eva_feed_options',
        'inputType' => 'url'
    ],
	[
        'id' => 'dpi_eva_primary_color',
        'title' => 'Primary Color (Titles)',
        'section' => 'dpi_eva_feed_options',
        'inputType' => 'color',
		'default' => '#0f3343'
    ],
	[
        'id' => 'dpi_eva_secondary_color',
        'title' => 'Secondary Color (Buttons)',
        'section' => 'dpi_eva_feed_options',
        'inputType' => 'color',
		'default' => '#00608b'
    ],
];