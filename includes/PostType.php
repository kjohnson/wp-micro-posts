<?php

final class MicroPosts_PostType
{
    const POST_TYPE = 'micro_posts';

    private static $args;
    private static $post_type;

    public static function init( $args )
    {
        self::$args = $args;
        add_action( 'init',                array( __CLASS__, 'register'         ), 0 );
        add_action( 'wp_insert_post_data', array( __CLASS__, 'update_title'     ), 10, 2 );
        add_filter( 'post_row_actions',    array( __CLASS__, 'post_row_actions' ), 10, 2 );
        add_filter( 'the_title',           array( __CLASS__, 'the_title'        ), 10, 2 );
        add_filter( 'the_content',         array( __CLASS__, 'the_content'      ), 10, 2 );
    }

    /**
    * Register Custom Post type
    * TODO: Maybe check for WP_Error and Throw/Handle Exception.
    */
    public static function register()
    {
        if( ! self::$post_type ) {
            self::$post_type = register_post_type( self::POST_TYPE, self::$args );
        }
        return self::$post_type;
    }

    public static function update_title( $data, $postarr )
    {
      if( self::POST_TYPE == $data[ 'post_type' ] ){

        // Mirror the content as the title. Update with changes.
        $data[ 'post_title' ] = $data[ 'post_content' ];

        // Add timestamp slug, if not already set.
        $data[ 'post_name' ] = ( $data[ 'post_name' ] ) ? $data[ 'post_name' ] : time();
      }
      return $data;
    }

    public static function post_row_actions($actions, $post)
    {
        if ( self::POST_TYPE == $post->post_type ){
          // Remove 'Quick Edit' action.
          unset( $actions[ 'inline hide-if-no-js' ] );
        }
        return $actions;
    }

    public static function the_title( $title, $id = null )
    {
      if ( self::POST_TYPE == get_post_type( $id ) ) {
        $post = get_post( $id );
        if( ! is_wp_error( $post ) ){
            $title = date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $post->post_date ) );
        }
      }
      return $title;
    }

    public static function the_content( $content )
    {
        global $post;
        if ( ! is_feed() && self::POST_TYPE == $post->post_type && apply_filters( 'micro_posts_make_clickable', true ) ) {
            // Make hyperlinks clickable by default.
            $content = self::make_clickable( $content );
        }

        return $content;
    }

    // Replacement for https://codex.wordpress.org/Function_Reference/make_clickable
    public static function make_clickable( $text )
    {
      // Source http://stackoverflow.com/a/5341330
      $text = preg_replace('!(((f|ht)tp(s)?://)[-a-zA-Zа-яА-Я()0-9@:%_+.~#?&;//=]+)!i', '<a href="$1">$1</a>', $text);

      // Note: Parse hashtags after hyperlinks to avoid nested links.
      // Source http://stackoverflow.com/a/22202663
      $text = preg_replace('/(?<!\S)#([0-9a-zA-Z]+)/', '<a href="' . esc_url( add_query_arg( 's', urlencode( '#' ) . '$1', get_site_url() ) ) . '">#$1</a>', $text);

      return $text;
    }
}
