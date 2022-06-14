<?php

namespace Your_Namespace\Core;

abstract class Config {
	protected static $values = [];

	public static function init ( $main_file ) {
		if ( count( self::$values ) > 0 ) {
			throw new \Exception( __CLASS__ . ' already initialized' );
		}

		$root = dirname( $main_file );
		self::set( 'FILE', $main_file );
		self::set( 'DIR', $root );

		$data = \get_file_data( $main_file, [ 'Plugin Name', 'Version' ] );
		self::set( 'NAME', $data[0] );
		self::set( 'VERSION', $data[1] );

		if ( file_exists( $root . '/config.php' ) ) {
			$values = include $root . '/config.php';
			foreach ( $values as $k => $v ) {
				if ( 'SLUG' === \mb_strtoupper( $k ) ) {
					$v = self::sanitize_slug( $v );
				}
				self::set( $k, $v );
			}
			$slug = self::get( 'SLUG' );
			if ( ! self::get( 'PREFIX', false ) ) {
				self::set( 'PREFIX', str_replace( '-', '_', $slug ) . '_' );
			}
		}

		if ( \file_exists( $root . '/composer.json' ) ) {
			$json_raw = \file_get_contents( $root . '/composer.json' );
			$composer = \json_decode( $json_raw );
			$php_version = null;
			try {
				$php_version = $composer ? $composer->require->php : false;
			} catch ( \Throwable $e ) {}
			if ( $php_version ) {
				$php_version = \preg_replace( '/[^0-9\.]/', '', $php_version );
				self::set( 'REQUIRED_PHP_VERSION', $php_version );
			}
		}
	}

	public static function set ( $key, $value ) {
		$key = mb_strtoupper( $key );
		if ( isset( self::$values[ $key ] ) ) {
			throw new \Exception( __CLASS__ . ": Key \"$key\" has already been assigned. No key can be assigned more than once." );
		}
		self::$values[ $key ] = $value;
		return $value;
	}

	public static function get ( $key = null, $default = null ) {
		$key = mb_strtoupper( $key );
		$value = $default;
		if ( isset( self::$values[ $key ] ) ) {
			$value = self::$values[ $key ];
		} 
		if ( null === $default ) {
			throw new \Exception( __CLASS__ . ": Undefined config key: $key" );
		}
		$value = \apply_filters( self::get( 'PREFIX' ) . 'config_get', $value, $key );
		$value = \apply_filters( self::get( 'PREFIX' ) . 'config_get_' . $key, $value, $key );
		return $value;
	}
	
	public static function sanitize_slug ( $string, $sep = '-' ) {
		$slug = \strtolower( \remove_accents( $string ) ); // Convert to ASCII
		// Standard replacements
		$slug = \str_replace( [ ' ', '_', '-' ], $sep, $slug );
		// Replace all non-alphanumeric by $separator
		$slug = \preg_replace( "/[^a-z0-9\\$sep]/", $sep, $slug );
		// Replace any more than one $separator in a row
		$slug = \preg_replace( "/\\$sep+/", $sep, $slug );
		// Remove last $separator if at the end
		$slug = \trim( $slug, $sep );
		return $slug;
	}
}
