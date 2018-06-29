<?php
/*
 Plugin Name: Movie Management
 Description: Manage movies and ratings of each movies.
 Author: Mohini Gupta
 Version: 0.1
 Text Domain: movie_management_domain
 License: GPL
*/

if (!class_exists('WP_Movie_Management')) {

	class WP_Movie_Management
	{
		public function __construct()
		{
			add_action( 'init', array( $this, 'create_movie_post_type'), 0 );
			add_action( 'add_meta_boxes', array( $this, 'add_rating_meta_box' ) );
			add_action( 'wp_head', array( $this, 'wp_robots', -1 ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'custom_movies_scripts' ) );
			add_filter( 'the_content', array( $this, "render_rating" ) );
			add_action( 'wp_ajax_nopriv_rate_movie', array( $this, 'save_movie_rating' ) );
			add_action( 'wp_ajax_rate_movie', array( $this, 'save_movie_rating' ) );
		}

 		function create_movie_post_type() {
 			$labels = array(
 				'name'                  => _x( 'Movies', 'Movies', 'movie_management_domain' ),
 				'singular_name'         => _x( 'Movie', 'Movie', 'movie_management_domain' ),
 				'menu_name'             => __( 'Movies', 'movie_management_domain' ),
 				'name_admin_bar'        => __( 'Movie', 'movie_management_domain' ),
 				'archives'              => __( 'Movie Archives', 'movie_management_domain' ),
 				'attributes'            => __( 'Movie Attributes', 'movie_management_domain' ),
 				'parent_item_colon'     => __( 'Parent Movie:', 'movie_management_domain' ),
 				'all_items'             => __( 'All Movies', 'movie_management_domain' ),
 				'add_new_item'          => __( 'Add New Movie', 'movie_management_domain' ),
 				'add_new'               => __( 'Add New', 'movie_management_domain' ),
 				'new_item'              => __( 'New Movie', 'movie_management_domain' ),
 				'edit_item'             => __( 'Edit Movie', 'movie_management_domain' ),
 				'update_item'           => __( 'Update Movie', 'movie_management_domain' ),
 				'view_item'             => __( 'View Movie', 'movie_management_domain' ),
 				'view_items'            => __( 'View Movies', 'movie_management_domain' ),
 				'search_items'          => __( 'Search Movie', 'movie_management_domain' ),
 				'not_found'             => __( 'Not found', 'movie_management_domain' ),
 				'not_found_in_trash'    => __( 'Not found in Trash', 'movie_management_domain' ),
 				'featured_image'        => __( 'Featured Image', 'movie_management_domain' ),
 				'set_featured_image'    => __( 'Set featured image', 'movie_management_domain' ),
 				'remove_featured_image' => __( 'Remove featured image', 'movie_management_domain' ),
 				'use_featured_image'    => __( 'Use as featured image', 'movie_management_domain' ),
 				'insert_into_item'      => __( 'Insert into movie', 'movie_management_domain' ),
 				'uploaded_to_this_item' => __( 'Uploaded to this movie', 'movie_management_domain' ),
 				'items_list'            => __( 'Movies list', 'movie_management_domain' ),
 				'items_list_navigation' => __( 'Movies list navigation', 'movie_management_domain' ),
 				'filter_items_list'     => __( 'Filter movies list', 'movie_management_domain' ),
 				);
 			$args = array(
 				'label'                 => __( 'Movie', 'movie_management_domain' ),
 				'description'           => __( 'Manage movies', 'movie_management_domain' ),
 				'labels'                => $labels,
 				'supports'              => array( 'title', 'editor', 'thumbnail' ),
 				'hierarchical'          => false,
 				'public'                => true,
 				'show_ui'               => true,
 				'show_in_menu'          => true,
 				'menu_position'         => 5,
 				'show_in_admin_bar'     => true,
 				'show_in_nav_menus'     => true,
 				'can_export'            => true,
 				'has_archive'           => true,
 				'exclude_from_search'   => false,
 				'publicly_queryable'    => true,
 				'capability_type'       => 'post',
 				);
 			register_post_type( 'movies', $args );
 		}

 		function add_rating_meta_box() {
 			add_meta_box( 'meta-box-rating', __( 'Rating', 'movie_management_domain' ), array( $this, 'wp_rating_callback'), 'movies' );
 		}

 		function wp_rating_callback( $post ) {
 			$rating = get_post_meta( $post->ID, 'rating', true );
 			echo '<h3>'. (!empty($rating) ? $rating: 0) .'</h3>';
 		}

 		function wp_robots() {
 			if ( is_singular( 'movies' ) ) {
 				echo "<meta name='robots' content='noindex, nofollow' />";
 			}
 		}

 		function custom_movies_scripts() {
 			global $post;
 			if ( is_singular( 'movies' ) ) {
 				$rating = get_post_meta( $post->ID, 'rating', true );
 				wp_localize_script( 'jquery', 'movie_rating_object', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'post_id' => $post->ID, 'initial_rating' => (!empty($rating) ? $rating : 0), 'read_only' => (!empty($rating) ? true : false) ) );
 				wp_enqueue_style( 'star-rating-svg-style', plugins_url( '/star-rating-svg/star-rating-svg.css', __FILE__ ), array() );
 				wp_enqueue_script( 'star-rating-svg-script', plugins_url( '/star-rating-svg/jquery.star-rating-svg.min.js', __FILE__ ), array(), true, true );
 				wp_enqueue_script( 'star-rating-svg-common-script', plugins_url( '/common.js', __FILE__ ), array(), true, true );
 				wp_enqueue_style( 'star-rating-svg-common-style', plugins_url( '/common.css', __FILE__ ), array() );
 			}
 		}

 		function render_rating($content) {
 			global $post;
 			if ( is_singular( 'movies' ) ) {
 				$content .= '<span id="movie-rating"></span><span class="live-rating"></span>';
 			}

 			return $content;
 		}

 		function save_movie_rating() {
 			update_post_meta( $_POST['id'], 'rating', $_POST['rating'] );
 		}
 	}
 }

// Create object
$movie_obj = new WP_Movie_Management();