<?php

define('ca_textdomain', 'ca_theme_textdomain');

/**
 * Theme Styles and Scripts
 * */
include_once 'inc/enqueue_scripts.php';

/**
 * Custom Hooks for Child Theme
 * */
// include_once 'inc/custom-hooks.php';

/**
 * Customizer additions.
 */
// include_once 'inc/customizer.php';

/**
 * Adds thumbnails to the entry in the admin table array('post','pages',)
 * */
// include_once 'inc/thumb_in_admin_column.php';

/**
 * Simple SEO class to create page metatags: title, description, robots, keywords, Open Graph.
 * */
// include_once 'inc/opengraph_doctype.php';

/**
 * Custom Modules for Divi Page Builder
 * */
// include_once 'gs-divi_module/custom-divi-module.php';

/**
 * Custom Modules TCF
 * */
include_once 'inc/custom-modules.php';

/**
 * GraphQL Shortcodes
 * */
// require_once 'graphql_shortcodes/graphql_connect.php';

/**
 * Custom Divi Main Options
 * */
function et_load_core_options() {
	require_once get_stylesheet_directory() . esc_attr( "/options_divi.php" );
}

/**
 * Dompdf is an HTML to PDF converter
 * */
// require_once 'vendor/autoload.php';

/**
 * Custom Quiz
 * */
require_once 'inc/quiz.php';
