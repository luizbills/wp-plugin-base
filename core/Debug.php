<?php

namespace Your_Namespace\Core;

use Your_Namespace\Core\Config;

abstract class Debug {
	public static function throw_if ( $condition, $message, $error_code = -1, $exception_class = null ) {
		if ( $condition ) {
			if ( \is_callable( $message ) ) {
				$message = $message();
			}
			if ( ! $exception_class || ! \class_exists( $exception_class ) ) {
				$exception_class = \RuntimeException::class;
			}
			throw new $exception_class( $message, (int) $error_code );
		}
	}

	public static function log () {
		if ( ! defined( 'WP_DEBUG_LOG' ) || ! WP_DEBUG_LOG ) return;
		$output = [];
		foreach ( \func_get_args() as $arg ) {
			$value = '';
			if ( \is_object( $arg ) || \is_array( $arg ) ) {
				$value = \print_r( $arg, true );
			}
			elseif ( \is_bool( $arg ) ) {
				$value = $arg ? 'bool(true)' : 'bool(false)';
			}
			elseif ( '' === $arg ) {
				$value = 'empty_string';
			}
			elseif ( null === $arg ) {
				$value = 'NULL';
			}
			else {
				$value = $arg;
			}
			$output[] = $value;
		}
		$slug = Config::get( 'SLUG' );
		\error_log( "[$slug] " . \implode( ' ', $output ) );
	}
}
