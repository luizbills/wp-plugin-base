<?php
/*
In this file you must inform your plugin requirements. By default, the plugin will already check the server's PHP version (v7.4 or later) in the main.php file.

Each dependency is an array that must contain the following keys:
  - `check`: a function that should check if any requirements have been met.
  - `message`: a string (or function that returns a string) that will be displayed if the requirement is not met.
*/

// prevents your PHP files from being executed via direct browser access
defined( 'WPINC' ) || exit( 1 );

$dependencies = [];

// See an example:
/*
$dependencies[] = [
	'check' => function () {
		// return true if WooCommerce is activated
		return class_exists( 'WooCommerce' );
	},

	// The error message if WooCommerce is not activated
	'message' => sprintf(
		// translators: %s is replaced with a required plugin name
		__( 'Install and activate the %s plugin.', 'your_text_domain' ),
		'<strong>WooCommerce</strong>'
	),
];
*/

// Optional: you can use shortcuts instead of callables in 'check'
// Examples:
// 'check' => 'function:get_field' will check if 'get_field' function exists
// 'check' => 'class:Classic_Editor' will check if 'Classic_Editor' class exists
// 'check' => 'plugin:wp_tweaks/wp_tweaks.php' will check if 'WP Tweaks' plugin is activated
// 'check' => 'defined:WP_DEBUG' will check if 'WP_DEBUG' constant exists
// 'check' => 'wordpress:4.9' will check if the WordPress version is v4.9 or later
// 'check' => 'extension:curl' will check if 'curl' PHP module is installed on server

// You should returns all dependencies
return $dependencies;
