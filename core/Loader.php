<?php

namespace Your_Namespace\Core;

abstract class Loader {
	protected static $classes;
	protected static $initialized = false;
	protected static $main_file;

	public static function init () {
		if ( self::$initialized ) {
			throw new \Error( __CLASS__ . ' already initialized' );
		}

		self::$main_file = Config::get( 'FILE' );
		self::load_classes();

		self::$initialized = true;
	}

	public static function get_hook_start_plugin () {
		return 'start_plugin_' . self::$main_file;
	}

	public static function load_classes () {
		$root = Config::get( 'DIR' );
		self::$classes = include_once $root . '/loader.php';

		if ( ! is_array( self::$classes ) ) {
			throw new \Error( $root . '/loader.php must return an Array' );
		}

		foreach ( self::$classes as $index => $class ) {
			if ( ! $class ) continue;
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

		$hook_start = self::get_hook_start_plugin();
		foreach ( self::$classes as $item ) {
			$class_name = $item[0];
			$priority = $item[1];

			if ( ! \class_exists( $class_name ) ) {
				throw new \Error( 'class ' . $class_name . ' does not exist' );
			}

			$instance = new $class_name();
			if ( \method_exists( $instance, '__start' ) ) {
				\add_action( $hook_start, [ $instance, '__start' ], $priority );
			}

			if ( \method_exists( $class_name, '__activation' ) ) {
				\register_activation_hook( self::$main_file, [ $class_name, '__activation' ] );
			}

			if ( \method_exists( $class_name, '__deactivation' ) ) {
				\register_deactivation_hook( self::$main_file, [ $class_name, '__deactivation' ] );
			}
		}
	}
}
