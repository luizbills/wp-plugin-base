<?php

namespace Your_Namespace\Core\Traits;

use Your_Namespace\Core\Config;

trait Log_Helpers {

    /**
	 * @param mixed ...$values
	 * @return void
	 */
    public static function log ( ...$values ) {
		if ( ! WP_DEBUG || ! defined( 'WP_DEBUG_LOG' ) || ! WP_DEBUG_LOG ) return;

        $prefix = '[' . Config::get( 'SLUG' ) . ']';
        $message = '';

		foreach ( $values as $value ) {
			if ( \is_bool( $value ) ) {
				$message .= $value ? 'true' : 'false';
			} else {
				$message .= print_r( $value, true );
			}
			$message .= ' ';
		}

		\error_log( "$prefix $message" );
	}

    /**
	 * @param \Throwable|string $err
	 * @return string The error message
	 */
	public static function log_critical ( $err ) {
		if ( is_a( $err, \Throwable::class ) ) {
			$error = $err->getMessage();
			$file = $err->getFile();
			$line = $err->getLine();

			$message = sprintf(
				__( '%1$s in %2$s on line %3$s', 'woocommerce' ), $error, $file, $line,
			);
			$message .= PHP_EOL . 'Stack trace:' . PHP_EOL . $err->getTraceAsString() . PHP_EOL;
		} else {
			$message = $err;
		}

		self::log( $message );

		if ( function_exists( 'wc_get_logger' ) ) {
			$logger = \wc_get_logger();
			$prefix = '[' . Config::get( 'SLUG' ) . ']';
			$logger->critical( "$prefix $message", [ 'source' => 'fatal-errors' ]);
		}

		return $message;
	}
}
