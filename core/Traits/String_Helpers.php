<?php

namespace Your_Namespace\Core\Traits;

trait String_Helpers {
    public static function str_length ( $string, $encoding = null ) {
		return \mb_strlen( $string, $encoding ? $encoding : 'UTF-8' );
	}

	public static function str_lower ( $string, $encoding = null ) {
		return \mb_strtolower( $string, $encoding ? $encoding : 'UTF-8' );
	}

	public static function str_upper ( $string, $encoding = null ) {
		return \mb_strtoupper( $string, $encoding ? $encoding : 'UTF-8' );
	}

	public static function str_before ( $string, $search ) {
		return '' === $search ? $string : \explode( $search, $string )[0];
	}

	public static function str_after ( $string, $search ) {
		return '' === $search ? $string : \array_reverse( \explode( $search, $string, 2 ) )[0];
	}

	public static function str_starts_with ( $string, $search ) {
		return self::str_after( $string, $search ) !== $string;
	}

	public static function str_ends_with ( $string, $search ) {
		return self::str_before( $string, $search ) !== $string;
	}

	// usage: `h::str_mask( 'XXX.XXX.XXX-XX', '83699642062' ); // outputs 836.996.420-62`
	public static function str_mask ( $string, $mask, $symbol = 'X' ) {
		$result = '';
		for ( $i = 0, $k = 0; $i < \strlen( $mask ); ++$i ) {
			if ( $mask[ $i ] === $symbol ) {
				if ( isset( $string[ $k ] ) ) $result .= $string[ $k++ ];
			} else {
				$result .= $mask[ $i ];
			}
		}
		return $result;
	}
}