<?php

namespace Your_Namespace\Core\Traits;

use Your_Namespace\Core\Config;

trait Debug_Helpers {
	public static function dd ( ...$values ) {
		if ( ! WP_DEBUG ) return;
		foreach ( $values as $v ) {
			echo '<pre>';
			var_dump( $v );
			echo '</pre>';
		}
		die;
	}

	public static function log ( ...$values ) {
		$debug_log_enabled = WP_DEBUG && defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG;
		if ( ! $debug_log_enabled ) return;
		$message = '';
		foreach ( $values as $value ) {
			if ( \is_string( $value ) ) {
				$message .= $value;
			} else {
				ob_start();
				var_dump( $value );
				$message .= ob_get_clean();
			}
			$message .= ' ';
		}
		$slug = Config::get( 'SLUG' );
		\error_log( "[$slug] $message" );
	}
}
