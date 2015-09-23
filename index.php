<?php
/**
 * Front to the WordPress application. This file doesn't do anything, but loads
 * wp-blog-header.php which does and tells WordPress to load the theme.
 *
 * @package WordPress
 */
/**
 * Tells WordPress to load the WordPress theme and output it.
 *
 * @var bool
 */
#ini_set('display_errors',1);
#error_reporting(E_ALL);
#define('WP_DEBUG', true);
#define('WP_DEBUG_LOG', true);
#header("content-type:text/html;charset=utf-8");
#echo "网站维护中，请期待!";exit;
define('WP_USE_THEMES', true);

/** Loads the WordPress Environment and Template */
require( dirname( __FILE__ ) . '/wp-blog-header.php' );
