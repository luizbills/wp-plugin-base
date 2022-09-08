<?php

namespace Your_Namespace\Core\Traits;

use Your_Namespace\Core\Config;

trait Common_Helpers {
	// Get the value if set, otherwise return a default value or `null`. Prevents notices when data is not set.
	public static function get ( &$var, $default = null ) {
		return $var ?? $default;
	}

	// Get the constant if set, otherwise return a default value or `null`.
	public static function get_defined ( $name, $default = null ) {
		return defined( $name ) ? constant( $name ) : $default;
	}

	// returns `false` ONLY IF $var is null, empty array/object or empty string
	// note: `$var = false` returns `true` (because $var is filled with a boolean)
	public static function filled ( $var ) {
		if ( null === $var ) return false;
		if ( is_string( $var ) && '' === trim( $var ) ) return false;
		if ( is_array( $var ) && 0 === count( $var ) ) return false;
		if ( is_object( $var ) && 0 === count( (array) $var ) ) return false;
		return \apply_filters( self::prefix( 'is_value_filled' ), true, $var );
	}

	// example: turns "Hello World" into "hello-world"
	public static function sanitize_slug ( $string, $sep = '-' ) {
		return Config::sanitize_slug( $string, $sep );
	}

	// appends the plugin prefix (defined in /plugin.php)
	// example: h::prefix( 'something' ) returns "your_prefix_something"
	public static function prefix ( $appends = '' ) {
		return Config::get( 'PREFIX' ) . $appends;
	}
}
