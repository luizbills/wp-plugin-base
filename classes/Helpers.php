<?php

namespace Your_Namespace;

use Your_Namespace\Helpers as h;
use Your_Namespace\Core\Config;
use Your_Namespace\Core\Debug;

abstract class Helpers {
	// Get the value if set, otherwise return a default value or false. Prevents notices when data is not set.
	public static function get ( &$var, $default = null ) {
		return isset( $var ) ? $var : $default;
	}

	// returns FALSE if $var is null, empty array or empty string
	public static function filled ( $var ) {
		if ( null === $var ) return false;
		if ( is_string( $var ) && '' === trim( $var ) ) return false;
		if ( is_array( $var ) && 0 === count( $var ) ) return false;
		if ( is_object( $var ) && 0 === count( (array) $var ) ) return false;
		return \apply_filters( h::prefix( 'is_value_filled' ), true, $var );
	}

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

	public static function prefix ( $appends = '', $sanitize = true ) {
		$appends = $sanitize && $appends ? h::sanitize_slug( $appends, '_' ) : $appends;
		return h::config_get( 'PREFIX' ) . $appends;
	}

	// PLUGIN DIR URL PREPENDER
	public static function plugin_url ( $path = '' ) {
		// usage: `echo h::plugin_url( 'assets/js/app.js' );`
		return \plugins_url( $path, h::config_get( 'FILE' ) );
	}

	// PLUGIN VERSION
	public static function get_plugin_version () {
		return preg_replace( '/[^0-9.]/', '',  h::config_get( 'VERSION', '' ) );
	}

	// WP OPTIONS PREFIXED
	public static function update_option ( $key, $value ) {
		if ( ! h::filled( $value ) ) {
			return \delete_option( h::prefix( $key ) );
		}
		return \update_option( h::prefix( $key ), $value );
	}

	public static function get_option ( $key, $default = false ) {
		return \get_option( h::prefix( $key ), $default );
	}

	// CACHE/TRANSIENTS (DISABLED IF WP_DEBUG = TRUE)
	public static function set_transient ( $transient, $value, $expiration = 0 ) {
		if ( ! WP_DEBUG ) {
			$key = h::get_transient_key( $transient );
			if ( ! h::filled( $value ) ) {
				return \delete_transient( $key );
			}
			if ( is_callable( $value ) ) {
				$value = \call_user_func( $value );
			}
			\set_transient( $key, $value, $expiration );
		}
		return $value;
	}

	public static function get_transient ( $transient, $default = null ) {
		$key = h::get_transient_key( $transient );
		$value = \get_transient( $key );
		return ! h::filled( $value ) ? $default : $value;
	}

	public static function remember ( $transient, $expiration, $callback ) {
		$value = ! WP_DEBUG ? h::get_transient( $transient ) : null;
		if ( ! h::filled( $value ) ) {
			$value = call_user_func( $callback );
			if ( ! WP_DEBUG ) h::set_transient( $transient, $value, $expiration );
		}
		return $value;
	}

	public static function get_transient_key ( $transient ) {
		return h::prefix( $transient ) . '_' . h::get_plugin_version();
	}

	// DEBUG
	public static function dd ( $value, $pre = true ) {
		if ( $pre ) echo '<pre>';
		var_dump( $value );
		if ( $pre ) echo '</pre>';
		die;
	}

	public static function log ( $message = null, $context = [] ) {
		return call_user_func(
			[ Debug::class, 'log' ],
			$message,
			is_array( $context ) ? $context : [ $context ]
		);
	}

	public static function throw_if ( $condition, $message, $exception_class = null ) {
		return Debug::throw_if( $condition, $message, $exception_class );
	}

	public static function throw_wp_error ( $wp_error, $code = null, $exception_class = null ) {
		$exception_class = $exception_class ? $exception_class : \RuntimeException::class;
		if ( \is_wp_error( $wp_error ) ) throw new $exception_class( $wp_error->get_error_message( $code ) );
	}

	public static function nothrow ( $callback, $default = null ) {
		try {
			return $callback();
		} catch ( \Throwable $e ) {
			return $default;
		}
	}

	// SECURITY
	public static function sanitize_slug ( $string, $sep = '-' ) {
		return Config::sanitize_slug( $string, $sep );
	}

	public static function esc_unsafe_html ( $html ) {
		// remove all script and style tags with code
		$html = \preg_replace( '/<(script|style)[^>]*?>.*?<\/\\1>/si', '', $html );
		// remove any script, style, link and iframe tags
		$html = \preg_replace( '/<(script|style|iframe|link)[^>]*?>/si', '', $html );
		return $html;
	}

	// STRING
	public static function str_length ( $string, $encoding = null ) {
		return \mb_strlen( $string, $encoding ? $encoding : 'UTF-8' );
	}

	public static function str_lower ( $string, $encoding = null ) {
		return \mb_strtolower( $string, $encoding ? $encoding : 'UTF-8' );
	}

	public static function str_upper ( $string, $encoding = null ) {
		return \mb_strtoupper( $string, $encoding ? $encoding : 'UTF-8' );
	}

	public static function str_before ( $string, $search ) {
		return '' === $search ? $string : \explode( $search, $string )[0];
	}

	public static function str_after ( $string, $search ) {
		return '' === $search ? $string : \array_reverse( \explode( $search, $string, 2 ) )[0];
	}

	public static function str_starts_with ( $string, $search ) {
		return h::str_after( $string, $search ) !== $string;
	}

	public static function str_ends_with ( $string, $search ) {
		return h::str_before( $string, $search ) !== $string;
	}

	public static function str_mask ( $string, $mask, $symbol = 'X' ) {
		// usage: `h::str_mask( 'XXX.XXX.XXX-XX', '83699642062' ); // outputs 836.996.420-62`
		$result = '';
		$k = 0;
		for ( $i = 0; $i < \strlen( $mask ); ++$i ) {
			if ( $mask[ $i ] === $symbol ) {
				if ( isset( $string[ $k ] ) ) $result .= $string[ $k++ ];
			} else {
				$result .= $mask[ $i ];
			}
		}
		return $result;
	}

	// TEMPLATE RENDERER
	public static function get_template ( $path, $args = [] ) {
		$args = \apply_filters( h::prefix( 'get_template_args' ), $args, $path );
		$dir = \trim( h::config_get( 'TEMPLATES_DIR', 'templates' ), '/' );
		$absolute_path = h::config_get( 'DIR' ) . "/{$dir}/$path" . ( ! h::str_ends_with( $path, '.php' ) ? '.php' : '' );

		try {
			\extract( $args );
			\ob_start();
			include $absolute_path;
			return \ob_get_clean();
		} catch ( \Throwable $e ) {
			throw new \Exception( "Error while rendering template \"$path\": " . $e->getMessage() );
		}
	}

	// YOUR CUSTOM HELPERS (ALWAYS STATIC)
	// public static function foo () {
	//     return 'bar';
	// }
}
