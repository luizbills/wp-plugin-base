<?php

namespace Your_Namespace;

use Your_Namespace\Helpers as h;
use Your_Namespace\Core\Config;
use Your_Namespace\Core\Debug;

abstract class Helpers {
	// CONFIG SETTER AND GETTER
	public static function config_get ( $key, $default = null ) {
		return Config::get( $key, $default );
	}

	public static function config_set ( $key, $value ) {
		return Config::set( $key, $value );
	}

	// PLUGIN SLUG AND PREFIX
	public static function get_slug () {
		return h::config_get( 'SLUG' );
	}

	public static function prefix ( $appends = '' ) {
		return h::config_get( 'PREFIX' ) . $appends;
	}

	// PLUGIN DIR URL PREPENDER
	public static function plugin_url ( $path = '' ) {
		// usage: `echo h::plugin_url( 'assets/js/app.js' );`
		return \plugins_url( $path, h::config_get( 'FILE' ) );
	}

	// WP OPTIONS
	public static function update_option ( $key, $value ) {
		if ( null === $value ) {
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
		if ( null === $value ) {
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

	// ARRAY
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

	// DEBUG
	public static function dd ( $value, $pre = true ) {
		if ( $pre ) echo '<pre>';
		var_dump( $value );
		if ( $pre ) echo '</pre>';
		die;
	}

	public static function log () {
		return call_user_func_array( [ Debug::class, 'log' ], func_get_args() );
	}

	public static function throw_if ( $condition, $message, $error_code = -1, $exception_class = null ) {
		return Debug::throw_if( $condition, $message, $error_code, $exception_class );
	}

	public static function get_wp_error_message ( $wp_error, $code = '' ) {
		return \is_wp_error( $wp_error ) ? $wp_error->get_error_message( $code ) : '';
	}

	// == YOUR CUSTOM HELPERS (ALWAYS STATIC) ==
	// public static function foo () {
	//     return 'bar';
	// }
}
