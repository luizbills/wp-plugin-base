<?php

use Your_Namespace\Helpers as h;

return [
	'php' => [
		'message' => function () {
			$req_version = h::config_get( 'REQUIRED_PHP_VERSION', false );
			return sprintf (
				__( "Update your %s version to $req_version or later.", 'your_text_domain' ),
				'<strong>PHP</strong>'
			);
		},
		'check' => function () {
			$req_version = h::config_get( 'REQUIRED_PHP_VERSION', false );
			$serv_version = \preg_replace( '/[^0-9\.]/', '', PHP_VERSION );
			return $req_version && $serv_version ? \version_compare( $serv_version, $req_version, '>=' ) : true;
		}
	],

	// 'woocommerce' => [
	// 	'message' => sprintf (
	// 		__( 'Install and activate the %s plugin.', 'your_text_domain' ),
	// 		'<strong>WooCommerce</strong>'
	// 	),
	// 	'check' => function () {
	// 		return \function_exists( 'WC' );
	// 	}
	// ],
];
