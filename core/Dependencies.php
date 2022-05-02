<?php

namespace Your_Namespace\Core;

use Your_Namespace\Core\Config;
use Your_Namespace\Core\Debug;
use Your_Namespace\Core\Loader;

abstract class Dependencies {
	protected static $dependencies;

	public static function init ( $main_file ) {
		Debug::throw_if(
			'' !== Config::get( 'PLUGIN_STARTED', '' ),
			__CLASS__ . ' already initialized'
		);

		$root = Config::get( 'DIR' );
		self::$dependencies = include_once $root . '/dependencies.php';

		Debug::throw_if(
			! is_array( self::$dependencies ),
			$root . '/dependencies.php must return an Array'
		);

		\add_action( 'plugins_loaded', [ __CLASS__, 'check_dependencies' ], 0 );
	}

	public static function check_dependencies () {
		$errors = [];
		$passed = [];

		foreach ( self::$dependencies as $key => $dep ) {
			Debug::log( 'checking ' . $key );

			$check = is_callable( $dep['check'] ) ? call_user_func( $dep['check'] ) : $dep['check'];
			$message = is_callable( $dep['message'] ) ? call_user_func( $dep['message'] ) : $dep['message'];

			Debug::log( 'Result:', $check );

			if ( ! $check ) {
				$errors[ $key ] = $message;
			} else {
				$passed[ $key ] = $message;
			}
		}

		self::$dependencies = null;

		if ( count( $errors ) > 0 ) {
			self::display_errors( $errors, $passed );
		}

		$should_start = 0 === count( $errors );

		if ( $should_start ) {
			Loader::start();
		}

		Config::set( 'PLUGIN_STARTED', $should_start );
	}

	protected static function display_errors ( $errors, $passed ) {
		if ( ! \is_admin() ) return;

		\add_action( 'admin_notices', function () use ( $errors, $passed ) {
			$allowed_html = [
				'a' => [ 'href' => [], 'title' => [] ],
				'span' => [ 'style' => [] ],
				'br' => [],
				'em' => [],
				'strong' => [],
			];

			echo "<div class='notice notice-error'><p>";
			echo sprintf(
				__( 'Missing dependencies for %s:', 'your_text_domain' ),
				"<strong>" . Config::get( 'NAME' ) . "</strong>",
			);

			foreach ( $errors as $error_message ) {
				$line = \sprintf(
					'<br>%s <span style="color:#e03131">✖ %s</span>',
					\str_repeat( '&nbsp;', 4 ),
					$error_message
				);
				echo \wp_kses( $line, $allowed_html );
			}

			foreach ( $passed as $error_message ) {
				$line = \sprintf(
					'<br>%s <span style="color:#2b8a3e">✔ %s</span>',
					\str_repeat( '&nbsp;', 4 ),
					$error_message
				);
				echo \wp_kses( $line, $allowed_html );
			}
			echo '</p></div>';
		} );
	}
}

