<?php

namespace Your_Namespace\Core;

abstract class Main {
	protected static $classes_to_load = [];
	protected static $dependencies = [];

	public static function start_plugin ( $main_file ) {
		if ( ! file_exists( $main_file ) ) {
			throw new \Error( 'Invalid plugin main file path in ' . __CLASS__ );
		}

		Config::init( $main_file );
		Loader::init();
		Dependencies::init();
	}
}
