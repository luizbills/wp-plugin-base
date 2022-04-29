<?php

namespace Your_Namespace\Core;

use Your_Namespace\Core\Helpers as h;

final class Helpers {
    protected static $config = [];

    public static function config_set ( $key, $value ) {
        $key = mb_strtoupper( $key );
        h::throw_if( isset( h::$config[ $key ] ), __CLASS__ . ": Key \"$key\" has already been assigned. No key can be assigned more than once." );
        h::$config[ $key ] = $value;
    }

    public static function config_get ( $key = false, $default = false ) {
        if ( ! $key ) {
            return h::$config; // return all keys
        }
        $key = mb_strtoupper( $key );
        if ( isset( h::$config[ $key ] ) ) return h::$config[ $key ];
        h::throw_if( false === $default, __CLASS__ . ": Undefined config key: $key" );
        return $default;
    }

    // PLUGIN SLUG AND PREFIX HELPER
    public static function get_slug () {
        return h::config_get( 'SLUG' );
    }

    public static function prefix ( $appends = '' ) {
        return h::config_get( 'PREFIX' ) . $appends;
    }

    // PLUGIN VERSION
    public function get_plugin_version () {
        $data = \get_file_data( h::config_get( 'FILE' ), [ 'Version' ] );
        return $data[0];
    }

    // DATABASE OPTIONS
    public static function update_option ( $key, $value ) {
        if ( null === $value || false === $value ) {
            return \delete_option( h::prefix( $key ) );
        }
        return \update_option( h::prefix( $key ), $value );
    }

    public static function get_option ( $key, $default = false ) {
        return \get_option( h::prefix( $key ), $default );
    }

    // CACHE/TRANSIENTS
    public static function set_transient ( $transient, $value, $expiration = 0 ) {
        $key = h::get_transient_key( $transient );
        if ( null === $value || false === $value ) {
            return \delete_transient( $key );
        }
        if ( is_callable( $value ) ) {
            $value = \call_user_func( $value );
        }
        return \set_transient( $key, $value, $expiration );
    }

    public static function get_transient ( $transient, $default = false ) {
        $key = h::get_transient_key( $transient );
        $value = \get_transient( $key );
        return null === $value || false === $value ? $default : $value;
    }

    public static function remember ( $transient, $expiration, $callback ) {
        $key = h::get_transient_key( $transient );
        $value = h::get_transient( $key );
        if ( null === $value ) {
            $value = call_user_func( $callback );
            \set_transient( $key, $value, $expiration );
        }
        return $value;
    }

    public static function get_transient_key ( $transient ) {
        return h::prefix( $transient ) . '_' . h::get_plugin_version();
    }

    // ARRAY HELPERS
    public static function array_get ( $arr, $key, $default = false ) {
        // usage #1: `h::array_get( $arr, 'x' ); // $arr['x']`
        // usage #2: `h::array_get( $arr, [ 'x', 'y' ] ); // $arr['x']['y']`
        $keys = is_array( $key ) ? $key : [ $key ];
        foreach ( $keys as $k ) {
            if ( is_array( $arr ) && isset( $arr[ $k ] ) ) {
                $arr = $arr[ $k ];
            } else {
                return $default;
            }
        }
        return $arr;
    }

    // DEBUG HELPERS
    public static function throw_if ( $condition, $message, $error_code = -1, $exception_class = null ) {
        if ( $condition ) {
            if ( \is_callable( $message ) ) {
                $message = $message();
            }
            if ( ! $exception_class || ! class_exists( $exception_class ) ) {
                $exception_class = \RuntimeException::class;
            }
            throw new $exception_class( $message, (int) $error_code );
        }
    }

    public static function get_wp_error_message ( $wp_error, $code = '' ) {
        return \is_wp_error( $wp_error ) ? $wp_error->get_error_message( $code ) : '';
    }

    public static function dd ( $value, $pre = true ) {
        if ( $pre ) echo '<pre>';
        var_dump( $value );
        if ( $pre ) echo '</pre>';
        die;
    }

    public static function log () {
        if ( ! defined( 'WP_DEBUG_LOG' ) || ! WP_DEBUG_LOG ) return;
        $output = [];
        foreach ( func_get_args() as $arg ) {
            $value = '';
            if ( is_object( $arg ) || is_array( $arg ) ) {
                $value = print_r( $arg, true );
            }
            elseif ( is_bool( $arg ) ) {
                $value = $arg ? 'bool(true)' : 'bool(false)';
            }
            elseif ( '' === $arg ) {
                $value = 'empty_string';
            }
            elseif ( null === $arg ) {
                $value = 'NULL';
            }
            else {
                $value = $arg;
            }
            $output[] = $value;
        }
        $slug = h::get_slug();
        array_merge( [ "[$slug]" ], $output );
        error_log( implode( ' ', $output ) );
    }

    // == YOUR CUSTOM HELPERS (ALWAYS STATIC) ==
    // public static function foo () {
    //     return 'bar';
    // }
}
