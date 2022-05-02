<?php

use Your_Namespace\Helpers as h;

return [
	'php' => [
		'message' => function () {
			$req_version = h::config_get( 'REQUIRED_PHP_VERSION', false );
			return __( "You need update the server PHP to version $req_version or more recent", 'your_text_domain' );
		},
		'check' => function () {
			$req_version = h::config_get( 'REQUIRED_PHP_VERSION', false );
			$serv_version = \preg_replace( '/[^0-9\.]/', '', PHP_VERSION );
			return $req_version && $serv_version ? \version_compare( $serv_version, $req_version, '>=' ) : true;
		}
	],

// 	'woocommerce' => [
// 		'message' => __( 'You need install and activate the WooCommerce plugin.', 'your_text_domain' ),
// 		'check' => function () {
// 			return \function_exists( 'WC' ); 
// 		}
// 	],
];
