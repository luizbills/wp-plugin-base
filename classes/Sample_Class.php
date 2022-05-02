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
		h::log( 'Started plugin ' . h::config_get( 'NAME' ) . ' v' . h::get_plugin_version() );
	}
}
