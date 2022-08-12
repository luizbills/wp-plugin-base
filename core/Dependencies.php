<?php

namespace Your_Namespace\Core;

use Your_Namespace\Core\Config;
use Your_Namespace\Core\Loader;

abstract class Dependencies {
	protected static $dependencies;
	protected static $initialized = false;

	public static function init () {
		if ( self::$initialized ) {
			throw new \Error( __CLASS__ . ' already initialized' );
		}

		$root = Config::get( 'DIR' );
		self::$dependencies = include_once $root . '/dependencies.php';
		if ( ! is_array( self::$dependencies ) ) {
			throw new \Error( $root . '/dependencies.php must return an Array' );
		}

		\add_action( 'plugins_loaded', [ __CLASS__, 'maybe_start_plugin' ], 0 );

		self::$initialized = true;
	}

	public static function maybe_start_plugin () {
		$result = self::check_dependencies();

		//var_dump( $result ); die;

		if ( $result['success'] ) {
			do_action( Loader::get_hook_start_plugin() );
		} else {
			self::display_notice_missing_deps( $result['messages'] );
		}
	}

	public static function check_dependencies () {
		$result = [
			'success' => null,
			'messages' => [],
		];
		$errors = 0;

		foreach ( self::$dependencies as $key => $dep ) {
			$check = $dep['check'] ?? null;
			$message = $dep['message'] ?? null;
			$message = is_callable( $message ) ? call_user_func( $message ) : $message;

			// check the message
			if ( ! is_string( $message ) || '' === trim( $message ) ) {
				$id = is_integer( $key ) ? '#' . ( 1 + $key ) : $key;
				throw new \Error( "Dependency $id has an invalid 'message': its must be a string and and it cannot be empty." );
			}

			// check the requirement
			$found = false;
			if ( is_string( $check ) ) {
				$found = self::handle_shortcut( $check );
			} elseif ( is_callable( $check ) ) {
				$found = call_user_func( $check );
			} else {
				$found = $check;
			}

			$result['messages'][] = [
				'text' => $message,
				'is_error' => ! boolval( $found ),
			];

			if ( ! boolval( $found ) ) $errors++;
		}

		$result['success'] = ( 0 === $errors );

		return $result;
	}

	protected static function handle_shortcut ( $string ) {
		$parts = explode( ':', $string );
		$value = implode( ':', array_slice( $parts, 1 ) );
		switch ( $parts[0] ) {
			case 'class':
				return class_exists( $value );
			case 'function':
				return function_exists( $value );
			case 'plugin':
				if ( ! function_exists( 'is_plugin_active' ) ) {
					include_once \ABSPATH . 'wp-admin/includes/plugin.php';
				}
				return \is_plugin_active( $value );
			case 'module': // alias for 'extension'
			case 'extension':
				return extension_loaded( $value );
			case 'const': // alias for 'defined'
			case 'defined':
				return defined( $value );
			case 'wp': // alias for 'wordpress'
			case 'wordpress':
				return version_compare( get_bloginfo( 'version' ), $value, '>=' );
		}
	}

	protected static function display_notice_missing_deps ( $messages ) {
		if ( ! \is_admin() ) return;
		if ( ! \current_user_can( 'install_plugins' ) ) return;
		if ( 0 === ( $messages ) ) return;

		\add_action( 'admin_notices', function () use ( $messages ) {
			$allowed_html = [
				'a' => [ 'href' => [], 'title' => [] ],
				'span' => [ 'class' => [], 'style' => [] ],
				'br' => [],
				'em' => [],
				'strong' => [],
			];

			echo "<div class='notice notice-error'><p>";
			echo sprintf(
				/* translators: %s is replaced with plugin name */
				__( 'Missing dependencies for %s:', 'your_text_domain' ),
				"<strong>" . Config::get( 'NAME' ) . "</strong>",
			);

			foreach ( $messages as $message ) {
				$icon = $message['is_error'] ? 'no-alt' : 'yes';
				$color = $message['is_error'] ? '#e03131' : '#2b8a3e';
				$line = \sprintf(
					'<br>%s<span style="color:%s"><span class="dashicons dashicons-%s"> </span>%s</span>',
					\str_repeat( '&nbsp;', 4 ),
					$color,
					$icon,
					$message['text']
				);
				echo \wp_kses( $line, $allowed_html );
			}

			echo '</p></div>';
		} );
	}
}
