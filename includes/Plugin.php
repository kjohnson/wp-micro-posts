<?php

require_once plugin_dir_path( __FILE__ ) . 'API.php';
require_once plugin_dir_path( __FILE__ ) . 'PostType.php';

final class MicroPosts_Plugin
{
    private $version;
    private $url;
    private $dir;
    private $api;

    public function __construct( $version, $file )
    {
        $this->version = $version;
        $this->url = plugin_dir_url($file);
        $this->dir = plugin_dir_path($file);
        $this->api = new MicroPosts_API();

        MicroPosts_PostType::init( $this->config( 'post-type-args' ) );
    }

    /*
    |--------------------------------------------------------------------------
    | Getter Methods
    |--------------------------------------------------------------------------
    */

    public function api()
    {
      return $this->api;
    }

    public function version()
    {
        return $this->version;
    }

    public function url( $url = '' )
    {
        return trailingslashit( $this->url ) . $url;
    }

    public function dir( $path = '' )
    {
        return trailingslashit( $this->dir ) . $path;
    }

    public function config( $file_name )
    {
        return include $this->dir( 'includes/config/' . $file_name . '.php' );
    }

    public function template( $file, $args = array() )
    {
        $path = $this->dir( 'templates/' . $file );
        if( ! file_exists(  $path ) ) return '';
        extract( $args );
        ob_start();
        include $path;
        return ob_get_clean();
    }
}
