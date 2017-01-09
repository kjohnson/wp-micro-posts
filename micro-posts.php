<?php
/*
 * Plugin Name: Micro Posts
 * Plugin URI: http://kylebjohnson.me
 * Description: Adds a "Micro Blogging" post type.
 * Version: 1.0.0
 * Author: Kyle B. Johnson
 * Author URI: http://kylebjohnson.me
 * Text Domain: micro-posts
 * Domain Path: /lang/
 *
 * Copyright 2017 Kyle B. Johnson.
 */

if( ! function_exists( 'MicroPosts' ) ) {
    function MicroPosts()
    {
        static $instance;
        if( ! isset( $instance ) ) {
            require_once plugin_dir_path(__FILE__) . 'includes/plugin.php';
            $instance = new MicroPosts_Plugin( '1.0.0', __FILE__ );
        }
        return $instance;
    }
}
MicroPosts();
