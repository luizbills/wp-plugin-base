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

	public static function get_plugin_version ( $raw = false ) {
		$version = Config::get( 'VERSION' );
		return $raw ? $version : preg_replace( '/[^0-9.]/', '', $version );
	}

	public static function enqueue_js ( $handle, $src, $deps = [], $version = false, $args = [] ) {
		$plugin_asset = false;
		if ( ! h::str_starts_with( $src, 'http://' ) && ! h::str_starts_with( $src, 'https://' ) ) {
			$assets_dir = trim( Config::get( 'ASSETS_DIR', 'assets' ), '/' );
			$filename = array_shift( explode( $src, '?' ) );
			$plugin_asset = Config::get( 'DIR' ) . '/' . $assets_dir . '/js/' . ltrim( $filename, '/' );
			$src = h::plugin_url( $assets_dir . '/js/' . ltrim( $src, '/' ) );
			if ( h::get_defined( 'WP_DEBUG' ) && file_exists( $plugin_asset ) ) {
				$version = filemtime( $plugin_asset );
			}
		}
		return wp_enqueue_script( $handle, $src, $deps, $version, $args );
	}

	public static function enqueue_css ( $handle, $src, $deps = [], $version = false, $media = 'all' ) {
		$plugin_asset = false;
		if ( ! h::str_starts_with( $src, 'http://' ) && ! h::str_starts_with( $src, 'https://' ) ) {
			$assets_dir = trim( Config::get( 'ASSETS_DIR', 'assets' ), '/' );
			$filename = array_shift( explode( $src, '?' ) );
			$plugin_asset = Config::get( 'DIR' ) . '/' . $assets_dir . '/css/' . ltrim( $filename, '/' );
			$src = h::plugin_url( $assets_dir . '/css/' . ltrim( $src, '/' ) );
			if ( h::get_defined( 'WP_DEBUG' ) && file_exists( $plugin_asset ) ) {
				$version = filemtime( $plugin_asset );
			}
		}
		return wp_enqueue_style( $handle, $src, $deps, $version, $media );
	}

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

	public static function get_transient ( $transient, $default = false ) {
		$value = false;
		if ( Config::get( 'CACHE_ENABLED', true ) ) {
			$key = self::get_transient_key( $transient );
			$value = \get_transient( $key );
		}
		return false !== $value ? $value : $default;
	}

	public static function get_transient_key ( $transient ) {
		return self::prefix( $transient ) . '_' . self::get_plugin_version();
	}
}
