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
}
