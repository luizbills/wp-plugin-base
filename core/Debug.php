<?php

namespace Your_Namespace\Core;

use Your_Namespace\Core\Config;

abstract class Debug {
	public static function throw_if ( $condition, $message, $error_code = -1, $exception_class = null ) {
		if ( $condition ) {
			if ( \is_callable( $message ) ) {
				$message = $message();
			}
			if ( ! $exception_class ) {
				$exception_class = \RuntimeException::class;
			}
			throw new $exception_class( $message, (int) $error_code );
		}
	}

	public static function log () {
		if ( ! defined( 'WP_DEBUG_LOG' ) || ! WP_DEBUG_LOG ) return;
		$output = [];
		foreach ( \func_get_args() as $arg ) {
			$output[] = self::format_value( $arg );
		}
		$slug = Config::get( 'SLUG' );
		\error_log( "[$slug] " . \implode( ' ', $output ) );
	}

	public static function format_value ( $value ) {
		if ( \is_object( $value ) || \is_array( $value ) ) {
			$value = \print_r( $value, true );
		}
		elseif ( \is_bool( $value ) ) {
			$value = $value ? 'BOOL(TRUE)' : 'BOOL(FALSE)';
		}
		elseif ( '' === $value ) {
			$value = 'EMPTY_STRING';
		}
		elseif ( null === $value ) {
			$value = 'NULL';
		}
		return $value;
	}
}
