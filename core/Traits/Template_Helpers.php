<?php

namespace Your_Namespace\Core\Traits;

use Your_Namespace\Core\Config;

trait Template_Helpers {
	use Config_Helpers;

	/**
	 * Remove evil tags: script, style, link, iframe
	 *
	 * @param string $html
	 * @return string The sanitized html
	 */
	public static function safe_html ( $html ) {
		// remove all script and style tags with code
		$html = \preg_replace( '/<(script|style)[^>]*?>.*?<\/\\1>/si', '', $html );
		// remove any script, style, link and iframe tags
		$html = \preg_replace( '/<(script|style|iframe|link)[^>]*?>/si', '', $html );
		return $html;
	}

	/**
	 * Returns a template rendered with the arguments (2nd parameter).
	 *
	 * @param string $template_path
	 * @param array $args
	 * @return string - The rendered template
	 * @throws \Exception
	 */
	public static function get_template ( $template_path, $args = [] ) {
		$args = \apply_filters( self::prefix( 'get_template_args' ), $args, $template_path );
		$full_path = self::get_template_path( $template_path );
		$html = '';
		try {
			\extract( $args );
			\ob_start();
			require $full_path;
			$html = \ob_get_clean();
		} catch ( \Throwable $e ) {
			if ( self::get_defined( 'WP_DEBUG' ) && current_user_can( 'administrator' ) ) {
				$error = wp_slash( "Error while rendering template '$template_path': " . $e->getMessage() );
				$html = '<script>alert("' . esc_js( $error ) . '")</script>';
			} else {
				throw new \Exception( $e );
			}
		}
		return $html;
	}

	/**
	 * @param string $template_path
	 * @return string The template absolute path
	 */
	public static function get_template_path ( $template_path ) {
		$template_path .= '.php' === substr( $template_path, -4 ) ? '' : '.php';
		$full_path = self::get_templates_dir() . ltrim( $template_path, '/' );
		return apply_filters( self::prefix( 'get_template_full_path' ), $full_path, $template_path );
	}

	/**
	 * @return string The directory that contains the plugin templates.
	 */
	public static function get_templates_dir () {
		$templates = \trim( Config::get( 'TEMPLATES_DIR', 'templates' ), '/' );
		return Config::get( 'DIR' ) . "/{$templates}/";
	}
}
