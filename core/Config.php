<?php

namespace Your_Namespace\Core;

abstract class Config {
	protected static $values = [];

	public static function init ( $main_file ) {
		if ( self::get_size() > 0 ) {
			throw new \Error( __CLASS__ . ' already initialized' );
		}

		$root = dirname( $main_file );
		$config = require $root . '/config.php';

		if ( ! is_array( $config ) ) {
			throw new \Error( $root . '/config.php must return an Array' );
		}

		foreach ( $config as $key => $value ) {
			$key = \mb_strtoupper( $key );
			if ( 'SLUG' === $key ) {
				$value = self::sanitize_slug( $value );
			}
			self::$values[ $key ] = $value;
		}

		$slug = isset( self::$values[ 'SLUG' ] ) ? self::$values[ 'SLUG' ] : false;
		if ( ! $slug || ! is_string( $slug ) ) {
			throw new \Error( $root . '/config.php must define a string SLUG (Recommended: only alphanumeric and dashes)' );
		}

		$prefix = isset( self::$values[ 'PREFIX' ] ) ? self::$values[ 'PREFIX' ] : false;
		if ( ! $prefix || ! is_string( $prefix ) ) {
			throw new \Error( $root . '/config.php must define a string PREFIX (only alphanumeric and underscores)' );
		}

		self::$values[ 'FILE'] = $main_file;
		self::$values[ 'DIR' ] = $root;

		$data = \get_file_data( $main_file, [ 'Plugin Name', 'Version' ] );
		self::$values[ 'NAME' ] = __( $data[0], 'your_text_domain' );
		self::$values[ 'VERSION' ] = $data[1] ? $data[1] : '0.0.0';
	}

	public static function set ( $key, $value ) {
		$key = mb_strtoupper( $key );
		if ( isset( self::$values[ $key ] ) ) {
			throw new \Error( __METHOD__ . ": Key \"$key\" has already been assigned. No key can be assigned more than once." );
		}
		self::$values[ $key ] = $value;
		return $value;
	}

	public static function get ( $key, $default = null ) {
		$key = \mb_strtoupper( $key );
		$value = isset( self::$values[ $key ] ) ? self::$values[ $key ] : $default;
		if ( null === $value ) {
			throw new \Error( __METHOD__ . ": Undefined key $key" );
		}
		return $value;
	}

	public static function get_size () {
		return count( self::$values );
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
