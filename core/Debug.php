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

	public static function log ( $message = null, $context = [] ) {
		$handled = \apply_filters( Config::get( 'PREFIX' ) . 'debug_log', null, $message, $context );
		if ( $handled ) {
			return $handled;
		}
		elseif ( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
			$slug = Config::get( 'SLUG' );
			$message = "[$slug] " . self::format_value( $message );
			if ( $context ) {
				$spaces = '    '; // 4 spaces
				$message .= \PHP_EOL . "{$spaces}Context {";
				foreach ( $context as $k => $v ) {
					$message .= \PHP_EOL . "{$spaces}{$spaces}{$k}: ". self::format_value( $v );
				}
				$message .= \PHP_EOL . "{$spaces}}";
			}
			\error_log( $message );
		}
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
