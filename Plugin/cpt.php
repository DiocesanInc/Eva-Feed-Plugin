<?php
function dpi_eva_feed_types() {
	// Set UI labels for Custom Post Type
	$labels = array(
		'name'                => _x( 'Eva Feed Item', 'Post Type General Name', 'diocesan' ),
		'singular_name'       => _x( 'Eva Feed Item', 'Post Type Singular Name', 'diocesan' ),
		'menu_name'           => __( 'Eva Feed Items', 'diocesan' ),
		'parent_item_colon'   => __( 'Parent Eva Feed Item', 'diocesan' ),
		'all_items'           => __( 'All Eva Feed Items', 'diocesan' ),
		'view_item'           => __( 'View Eva Feed Item', 'diocesan' ),
		'add_new_item'        => __( 'Add New Eva Feed Item', 'diocesan' ),
		'add_new'             => __( 'Add New Eva Feed Item', 'diocesan' ),
		'edit_item'           => __( 'Edit Eva Feed Item', 'diocesan' ),
		'update_item'         => __( 'Update Eva Feed Item', 'diocesan' ),
		'search_items'        => __( 'Search Eva Feed Items', 'diocesan' ),
		'not_found'           => __( 'Not Found', 'diocesan' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'diocesan' ),
	);
	// Set other options for Custom Post Type	
	$args = array(
		'label'               => __( 'eva', 'diocesan' ),
		'description'         => __( 'Eva Feed Items', 'diocesan' ),
		'menu_icon'			  => 'dashicons-images-alt2',
		'labels'              => $labels,
		// Features this CPT supports in Post Editor
		'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'revisions',  ),		
		// You can associate this CPT with a taxonomy or custom taxonomy. 
		'taxonomies'          => array( 'theme', 'post_tag' ),
		'rewrite'			  => array( 'slug'=>'eva'),
		'hierarchical'        => true,
		'public'              => false,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 5,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'post',
	);	
	// Registering your Custom Post Type
	register_post_type( 'eva', $args );
}
/* Hook into the 'init' action so that the function
* Containing our post type registration is not 
* unnecessarily executed. 
*/
add_action( 'init', 'dpi_eva_feed_types', 0 );