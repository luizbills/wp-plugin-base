<?php

namespace Your_Namespace;

use Your_Namespace\Helpers as h;

final class Sample_Class {
	public function __start () {
		// This method is called automatically when all plugin dependencies are active.
		// Use this method to add your actions and filters hooks

		// example:
		\add_action( 'plugins_loaded', [ $this, 'init' ], 10 );
	}

	public function init () {
		$plugin_name = h::config_get( 'NAME' );
		$version = h::get_plugin_version();
		h::log( "INFO Running plugin \"$plugin_name\" v$version"  );
	}
}
