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

		foreach ( self::$classes as $index => $item ) {
			if ( ! $item ) continue;
			if ( ! is_array( $item ) ) {
				$item = [ $item, 10 ];
			} else {
				$class = $item[0] ?? null;
				if ( ! $class ) continue;
				$item = [ $class, intval( $item[1] ?? 10 ) ];
			}
			self::$classes[ $index ] = $item;
		}

		\usort( self::$classes, function ( $a, $b ) {
			return $a[1] <=> $b[1];
		} );

		$hook_start = self::get_hook_start_plugin();
		foreach ( self::$classes as $item ) {
			$class_name = $item[0];
			$priority = $item[1];
			$loaded = false;

			if ( is_string( $class_name ) && ! \class_exists( $class_name ) ) {
				throw new \Error( 'class ' . $class_name . ' does not exist' );
			}

			$instance = is_string( $class_name ) ? new $class_name() : $class_name;
			$class_name = get_class( $instance );

			if ( \method_exists( $instance, '__start' ) ) {
				\add_action( $hook_start, [ $instance, '__start' ], $priority );
				$loaded = true;
			}

			if ( \method_exists( $class_name, '__activation' ) ) {
				\register_activation_hook( self::$main_file, [ $class_name, '__activation' ] );
				$loaded = true;
			}

			if ( \method_exists( $class_name, '__deactivation' ) ) {
				\register_deactivation_hook( self::$main_file, [ $class_name, '__deactivation' ] );
				$loaded = true;
			}

			if ( ! $loaded ) {
				throw new \Error( "class $class_name must have at least one of the following methods: __start, __activation (static) or __deactivation (static)" );
			}
		}
	}
}
