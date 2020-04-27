<?php
/**
 * Theme Styles and Scripts
 * */
if ( ! function_exists( 'et_get_theme_version' ) ) :
	function et_get_theme_version() {
		$theme_info = wp_get_theme();
		
		if ( is_child_theme() ) {
			$theme_info = wp_get_theme( $theme_info->parent_theme );
		}
		
		$theme_version = $theme_info->display( 'Version' );
		
		return $theme_version;
	}
endif;
$child_theme_version = et_get_theme_version();
define( 'DS_BUILDER_VERSION', $child_theme_version );


add_action( 'wp_enqueue_scripts', 'child_scripts', 18 );
function child_scripts() {
	global $wp_query;
	
	wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );

	// wp_enqueue_style( 'google-fonts-Playfair', '//fonts.googleapis.com/css?family=Playfair+Display:400,400i,700,700i&display=swap&subset=cyrillic', array(), '' );
	wp_enqueue_script('jquery');
	// TCF CODE
	wp_enqueue_style( 'custom-stylesheet', get_stylesheet_directory_uri() . '/assets/css/custom.css', array('parent-style'), '2.0.0', 'all' );
	// Scripts
	wp_enqueue_script( 'main-js', get_stylesheet_directory_uri() . '/assets/js/main.js', array('jquery'), '2.0.3', true);
	
	// we pass the parameters to main.js script cause we can get the parameters values only in PHP
	wp_localize_script( 'main-js', 'news_gallery_params', array(
		'ajaxurl' => site_url() . '/wp-admin/admin-ajax.php', // WordPress AJAX
		'posts' => json_encode( $wp_query->query_vars ), // everything about your loop is here
		'current_page' => $wp_query->query_vars['paged'] ? $wp_query->query_vars['paged'] : 1,
		'max_page' => $wp_query->max_num_pages,
		'category' => $wp_query->query_vars['category_name']
	) );
	
	// Maria CODE
	wp_enqueue_style( 'quiz-stylesheet', get_stylesheet_directory_uri() . '/assets/css/quiz.css', array( 'custom-stylesheet' ), '1.0.0', 'all' );
	wp_enqueue_script('quiz-js', get_stylesheet_directory_uri() . '/assets/js/quiz.js', array( 'main-js' ), '1.0.0', true);
	wp_localize_script( 'quiz-js', 'quiz_ajax_params', array(
		// URL to wp-admin/admin-ajax.php to process the request
		'quiz_ajaxurl' => admin_url( 'admin-ajax.php' ),
		// generate a nonce with a unique ID "quiz_pagination-special-string"
		// so that you can check it later when an AJAX request is sent
		'quiz_security' => wp_create_nonce( 'quiz_game-special-string' )
	) );


}
