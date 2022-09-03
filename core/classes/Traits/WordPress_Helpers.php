<?php

namespace Your_Namespace\Core\Traits;

use Your_Namespace\Core\Config;

trait WordPress_Helpers {
	public static function plugin_url ( $path = '' ) {
		// usage: `$script_url = h::plugin_url( 'assets/js/app.js' );`
		return \plugins_url( $path, Config::get( 'FILE' ) );
	}

	public static function get_plugin_version ( $raw = false ) {
		$version = Config::get( 'VERSION' );
		return $raw ? $version : preg_replace( '/[^0-9.]/', '', $version );
	}

    public static function set_transient ( $transient, $value, $duration = 0 ) {
		if ( is_callable( $value ) ) {
			$value = \call_user_func( $value );
		}
		if ( self::config_get( 'CACHE_ENABLED', true ) ) {
			$key = self::get_transient_key( $transient );
			if ( ! self::filled( $value ) ) {
				return \delete_transient( $key );
			}
			else {
				$duration = \absint( $duration );
				$duration = $duration !== 0 ? $duration : \apply_filters(
					self::prefix( 'transient_max_duration' ),
					3 * MONTH_IN_SECONDS, // by default, max is 3 months
					$transient
				);
				\set_transient( $key, $value, $duration );
			}
		}
		return $value;
	}

	public static function get_transient ( $transient, $default = false ) {
		$key = self::get_transient_key( $transient );
		$value = \get_transient( $key );
		return false !== $value ? $value : $default;
	}

	public static function get_transient_key ( $transient ) {
		return self::prefix( $transient ) . '_' . self::get_plugin_version();
	}
}
