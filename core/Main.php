<?php

namespace Your_Namespace\Core;

use Your_Namespace\Core\Config;

abstract class Main {
	protected static $classes_to_load = [];
	protected static $dependencies = [];

	// == CORE HELPERS ==
	public static function start_plugin ( $main_file ) {
		if ( Config::get( 'FILE', '' ) ) {
			throw new \Exception( __CLASS__ . ' already initialized' );
		}

		if ( ! file_exists( $main_file ) ) {
			throw new \Exception( 'Invalid plugin main file path in ' . __CLASS__ );
		}

		Config::init( $main_file );
		self::load_plugin_textdomain( $main_file, 'your_text_domain' );
		Dependencies::init( $main_file );
		Loader::init( $main_file );
	}

	public static function load_plugin_textdomain ( $main_file, $text_domain ) {
		$dir = Config::get( 'LANGUAGES_DIR', 'languages' );
		\load_plugin_textdomain(
			$text_domain,
			false,
			\dirname( \plugin_basename( $main_file ) ) . "/$dir/"
		);
	}
}
