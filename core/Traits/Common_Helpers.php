<?php

namespace Your_Namespace\Core\Traits;

use Your_Namespace\Core\Traits\Log_Helpers;
use Your_Namespace\Core\Config;

trait Common_Helpers {

	use Log_Helpers;

	/**
	 * Get the value if set, otherwise return a default value or `null`.
	 * Prevents notices when data is not set.
	 *
	 * @param mixed $var
	 * @param mixed $default
	 * @return mixed
	 */
	public static function get ( &$var, $default = null ) {
		return $var ?? $default;
	}

	/**
	 * Get the constant if set, otherwise return a default value.
	 *
	 * @param string $name The constant name
	 * @param mixed $default
	 * @return mixed
	 */
	public static function get_defined ( $name, $default = null ) {
		return defined( $name ) ? constant( $name ) : $default;
	}

	/**
	 * Returns `false` ONLY IF $var is null, empty array/object or empty string
	 * Note: `$var = false` returns `true` (because $var is filled with a boolean)
	 *
	 * @param mixed $var
	 * @return bool
	 */
	public static function filled ( $var ) {
		if ( null === $var ) return false;
		if ( is_string( $var ) && '' === trim( $var ) ) return false;
		if ( is_array( $var ) && 0 === count( $var ) ) return false;
		if ( is_object( $var ) && 0 === count( (array) $var ) ) return false;
		return \apply_filters( self::prefix( 'is_value_filled' ), true, $var );
	}

	/**
	 * @param string $string
	 * @param string $sep
	 * @return string
	 */
	public static function sanitize_slug ( $string, $sep = '-' ) {
		return Config::sanitize_slug( $string, $sep );
	}

	/**
	 * Appends the plugin prefix (defined in /config.php).
	 * Example: h::prefix( 'something' ) returns "your_prefix_something"
	 *
	 * @param string $appends
	 * @return string
	 */
	public static function prefix ( $appends = '' ) {
		return Config::get( 'PREFIX' ) . $appends;
	}
}
