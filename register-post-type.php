<?php

/*
 * Setup Custom Post Types for WordPress
 */
    function sp2016_create_custom_post() {

        // Social Posts
        $labels = array(
		    'name' 				=> 'Social Posts',
			'all_items'    		=> 'All Social Posts',
		    'singular_name' 	=> 'Social Post',
		    'add_new' 			=> 'Add New Post',
		    'add_new_item' 		=> 'Add New Post',
		    'edit' 				=> 'Edit',
		    'edit_item' 		=> 'Edit Post',
		    'new_item' 			=> 'New Post',
		    'view' 					=> 'View Post',
		    'view_item' 			=> 'View Post',
		    'search_items' 			=> 'Search Post',
		    'not_found' 			=> 'No posts found',
		    'not_found_in_trash' 	=> 'No posts found in Trash'
        );
        $args = array(
			'labels'             	=> $labels,
			'public'		     	=> true,
			'publicly_queryable' 	=> true,
			'exclude_from_search'	=> true,
			'show_in_menu'       	=> true,
			'query_var'          	=> true,
			'capability_type'    	=> 'post',
			'has_archive'        	=> false,
			'menu_icon'			 	=> 'dashicons-networking',
			'hierarchical'       	=> false,
			'menu_position'      	=> 23,
			'supports'           	=> array(
				'title',
				'editor',
				'thumbnail',
				'page-attributes',
				'post-formats',
				'author'
			),
			'taxonomies' => array('category'),
			'rewrite'			 => array(
				'slug'	=> 'socials',
			)
        );
        register_post_type( 'sp-social', $args );

    }
    add_action( 'init', 'sp2016_create_custom_post', 10 );

/*
 * add column to display type of social post
 */

    function custom_columns( $column, $post_id ) {
        if ( get_post_type($post_id) !== 'sp-social' ) return;

    	switch ( $column ) {
    		case 'type':
    			echo get_post_meta( $post_id, '_custom_social_status', true );
    			break;
    	}
    }
    add_action( 'manage_posts_custom_column' , 'custom_columns', 10, 2 );

    function set_social_columns($columns) {
        unset($columns['post_type']);
        $columns['type'] = 'Type';
        return $columns;
    }
    add_filter('manage_sp-social_posts_columns' , 'set_social_columns');

?>