<?php

namespace Your_Namespace\Core\Traits;

use Your_Namespace\Core\Config;

trait WordPress_Helpers {

	/**
	 * Usage: `$script_url = h::plugin_url( 'assets/js/app.js' );`
	 *
	 * @param string $path
	 * @return string the link
	 */
	public static function plugin_url ( $path = '' ) {
		return \plugins_url( $path, Config::get( 'FILE' ) );
	}

	/**
	 * @param boolean $raw
	 * @return string The plugin version
	 */
	public static function get_plugin_version ( $raw = false ) {
		$version = Config::get( 'VERSION' );
		return $raw ? $version : preg_replace( '/[^0-9.]/', '', $version );
	}

	/**
	 * Saves a WordPress transient prefixed with the plugin slug.
	 *
	 * @see https://developer.wordpress.org/apis/transients/
	 * @see https://codex.wordpress.org/Easier_Expression_of_Time_Constants
	 * @param string $transient
	 * @param mixed $value
	 * @param integer $duration
	 * @return mixed
	 */
    public static function set_transient ( $transient, $value, $duration = 0 ) {
		if ( is_callable( $value ) ) {
			$value = \call_user_func( $value );
		}
		if ( Config::get( 'CACHE_ENABLED', true ) ) {
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

	/**
	 * @param string $transient
	 * @param mixed $default
	 * @return mixed
	 */
	public static function get_transient ( $transient, $default = false ) {
		$value = false;
		if ( Config::get( 'CACHE_ENABLED', true ) ) {
			$key = self::get_transient_key( $transient );
			$value = \get_transient( $key );
		}
		return false !== $value ? $value : $default;
	}

	/**
	 * @param string $transient
	 * @return string
	 */
	public static function get_transient_key ( $transient ) {
		return self::prefix( $transient ) . '_' . self::get_plugin_version();
	}
}
