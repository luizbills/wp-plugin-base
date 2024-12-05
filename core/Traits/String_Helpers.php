<?php

namespace Your_Namespace\Core\Traits;

trait String_Helpers {

	/**
	 * @param string $string
	 * @param string $encoding
	 * @return int<0, max>
	 */
	public static function str_length ( $string, $encoding = 'UTF-8' ) {
		return \mb_strlen( $string, $encoding );
	}

	/**
	 * @param string $string
	 * @param string $encoding
	 * @return string
	 */
	public static function str_lower ( $string, $encoding = 'UTF-8' ) {
		return \mb_strtolower( $string, $encoding );
	}

	/**
	 * @param string $string
	 * @param string $encoding
	 * @return string
	 */
	public static function str_upper ( $string, $encoding = 'UTF-8' ) {
		return \mb_strtoupper( $string, $encoding );
	}

	/**
	 * @param string $string
	 * @param string $encoding
	 * @return bool
	 */
	public static function str_contains ( $string, $search, $encoding = 'UTF-8' ) {
		return $search !== '' && mb_strpos( $string, $search, 0, $encoding ) !== false;
	}

	/**
	 * @param string $string
	 * @param string $search
	 * @return string
	 */
	public static function str_before ( $string, $search ) {
		return '' === $search ? $string : \explode( $search, $string )[0];
	}

	/**
	 * @param string $string
	 * @param string $search
	 * @return string
	 */
	public static function str_after ( $string, $search ) {
		return '' === $search ? $string : \array_reverse( \explode( $search, $string, 2 ) )[0];
	}

	/**
	 * @param string $string
	 * @param string $search
	 * @return bool
	 */
	public static function str_starts_with ( $string, $search ) {
		return self::str_after( $string, $search ) !== $string;
	}

	/**
	 * @param string $string
	 * @param string $search
	 * @return bool
	 */
	public static function str_ends_with ( $string, $search ) {
		return self::str_before( $string, $search ) !== $string;
	}

	/**
	 * Usage: h::str_mask( 'XXX.XXX.XXX-XX', '83699642062' ); // outputs 836.996.420-62
	 *
	 * @param string $string
	 * @param string $mask
	 * @param string $symbol
	 * @return string
	 */
	public static function str_mask ( $string, $mask, $symbol = 'X' ) {
		$result = '';
		for ( $i = 0, $k = 0; $i < self::str_length( $mask ); ++$i ) {
			if ( $mask[ $i ] === $symbol ) {
				if ( isset( $string[ $k ] ) ) $result .= $string[ $k++ ];
			} else {
				$result .= $mask[ $i ];
			}
		}
		return $result;
	}
}
