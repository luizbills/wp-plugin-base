<?php

namespace Your_Namespace\Core;

use Your_Namespace\Core\Config;

abstract class Loader {
	protected static $classes;

	public static function init ( $main_file ) {
		if ( '' !== Config::get( 'PLUGIN_STARTED', '' ) ) {
			throw new \Error( __CLASS__ . ' already initialized' );
		}
		self::load_classes();
	}

	public static function start () {
		if ( false === Config::get( 'PLUGIN_STARTED', '' ) ) {
			throw new \Error( __CLASS__ . ' can not start' );
		}
		\do_action( self::get_hook() );
	}

	public static function get_hook () {
		$main_file = Config::get( 'FILE' );
		return 'start_' . $main_file;
	}

	public static function load_classes () {
		$root = Config::get( 'DIR' );
		self::$classes = include_once $root . '/loader.php';

		if ( ! is_array( self::$classes ) ) {
			throw new \Error( $root . '/loader.php must return an Array' );
		}

		foreach ( self::$classes as $index => $class ) {
			if ( ! is_array( $class ) ) {
				$class = [ $class, 10 ];
			} else {
				$class = [ $class[0], (int) $class[1] ];
			}
			self::$classes[ $index ] = $class;
		}

		\usort( self::$classes, function ( $a, $b ) {
			return $a[1] <=> $b[1];
		} );

		$main_file = Config::get( 'FILE' );
		foreach ( self::$classes as $item ) {
			$class_name = $item[0];
			$priority = $item[1];

			if ( ! \class_exists( $class_name ) ) {
				throw new \Error( 'class ' . $class_name . ' does not exist' );
			}

			$instance = new $class_name();
			\add_action( self::get_hook(), [ $instance, '__start' ], $priority );
		}
	}
}
