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

		foreach ( self::$dependencies as $key => $dep ) {
			$check = is_callable( $dep['check'] ) ? call_user_func( $dep['check'] ) : $dep['check'];
			if ( ! $check ) {
				$errors[ $key ] = $dep['message'];
			}
		}

		self::$dependencies = null;

		if ( count( $errors ) > 0 ) {
			self::display_errors( $errors );
		}

		$should_start = 0 === count( $errors );

		if ( $should_start ) {
			Loader::start();
		}

		Config::set( 'PLUGIN_STARTED', $should_start );
	}

	protected static function display_errors ( $errors ) {
		if ( ! \is_admin() ) return;

		\add_action( 'admin_notices', function () use ( $errors ) {
			$allowed_html = [
				'a' => [
					'href' => [],
					'title' => [],
				],
				'br' => [],
				'em' => [],
				'strong' => [],
			];

			echo "<div class='notice notice-error'><p>";
			echo sprintf(
				'Missing dependencies for %s:',
				"<strong>" . Config::get( 'NAME' ) . "</strong>",
			);

			foreach ( $errors as $error_message ) {
				$line = \sprintf(
					'%s • %s',
					\str_repeat( '&nbsp;', 8 ),
					$error_message
				);
				echo \wp_kses( $line, $allowed_html );
			}
			echo '</p></div>';
		} );
	}
}

