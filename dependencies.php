<?php

use Your_Namespace\Helpers as h;

defined( 'WPINC' ) || exit( 1 );

// Each index of `$deps` array must be a array with 'check' and 'message'
// 'check' must be a callable or a string
// 'message' must be a string or callable that returns a string
$deps = [];

// See some examples:

// Requires WooCommerce plugin
/*
$deps[] = [
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

// always returns your dependencies or an empty array
return $deps;
