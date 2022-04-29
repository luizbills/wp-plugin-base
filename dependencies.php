<?php

use Your_Namespace\Core\Helpers as h;

return [
    'php' => [
        'message' => function () {
            $req_version = h::config_get( 'REQUIRED_PHP_VERSION', false );
            return "Atualize o PHP para a versão $req_version ou superior";
        },
        'check' => function () {
            $req_version = h::config_get( 'REQUIRED_PHP_VERSION', false );
            $serv_version = \preg_replace( '/[^0-9\.]/', '', PHP_VERSION );
            return \version_compare( $serv_version, $req_version, '>=' );
        }
    ],
    'woocommerce' => [
        'message' => __( 'You need install and activate the WooCommerce plugin.', '' ),
        'check' => function () {
            $req_version = h::config_get( 'REQUIRED_PHP_VERSION', '' );
            $server_version = $req_version ? \preg_replace( '/[^0-9\.]/', '', PHP_VERSION ) : false;
            return $req_version && $server_version ? \version_compare( $server_version, $req_version, '>=' ) : true;
        }
    ],
];
