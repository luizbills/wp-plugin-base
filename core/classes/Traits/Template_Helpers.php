<?php

namespace Your_Namespace\Core\Traits;

use Your_Namespace\Core\Config;

trait Template_Helpers {
	use Config_Helpers;

	// remove evil tags: script, style, link, iframe
	public static function safe_html ( $html ) {
		// remove all script and style tags with code
		$html = \preg_replace( '/<(script|style)[^>]*?>.*?<\/\\1>/si', '', $html );
		// remove any script, style, link and iframe tags
		$html = \preg_replace( '/<(script|style|iframe|link)[^>]*?>/si', '', $html );
		return $html;
	}

	// TEMPLATE RENDERER
	public static function get_template ( $path, $args = [] ) {
		$args = \apply_filters( self::prefix( 'get_template_args' ), $args, $path );
		$dir = \trim( Config::get( 'TEMPLATES_DIR', 'templates' ), '/' );
		$full_path = Config::get( 'DIR' ) . "/{$dir}/$path" . ( ! self::str_ends_with( $path, '.php' ) ? '.php' : '' );
		$full_path = apply_filters( self::prefix( 'get_template_full_path' ), $full_path, $path );
		$html = '';
		try {
			\extract( $args );
			\ob_start();
			require $full_path;
			$html = \ob_get_clean();
		} catch ( \Throwable $e ) {
			if ( self::get_defined( 'WP_DEBUG' ) && current_user_can( 'administrator' ) ) {
				$error = wp_slash( "Error while rendering template '$path': " . $e->getMessage() );
				$html = '<script>alert("' . esc_js( $error ) . '")</script>';
			} else {
				throw new \Error( $e );
			}
		}
		return $html;
	}
}
