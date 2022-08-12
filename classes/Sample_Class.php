<?php

namespace Your_Namespace;

use Your_Namespace\Helpers as h;

final class Sample_Class {
	public function __start () {
		// This method is called automatically when all plugin dependencies are active.
		// But, you need put this class in your /loader.php first

		// example: Use this method to add your actions and filters hooks
		\add_action( 'plugins_loaded', [ $this, 'init' ], 10 );
	}

	public function init () {
		$plugin_name = h::config_get( 'NAME' );
		$version = h::get_plugin_version();
		h::log( "INFO Running plugin \"$plugin_name\" v$version"  );
	}

	public static function __activation () {
		// This STATIC method is called automatically on plugin activation.
		$plugin_name = h::config_get( 'NAME' );
		h::log( "INFO plugin \"$plugin_name\" activated"  );
	}

	public static function __deactivation () {
		// This STATIC method is called automatically on plugin deactivation (not uninstall/deletation).
		$plugin_name = h::config_get( 'NAME' );
		h::log( "INFO plugin \"$plugin_name\" deactivated"  );
	}
}
