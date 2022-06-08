# PHP & WordPress Snippets

## Add an plugin action link

```php
// __FILE__ is the plugin main file
add_filter(
	'plugin_action_links_' . plugin_basename( __FILE__ ),
	function ( $actions ) {
		$label = 'Settings';
		$dest_url = esc_url( 'your_url' );
		return array_merge( [ "<a href=\"$dest_url\">$label</a>" ], $actions );
	}
);
```

See: [plugin_action_links_{$plugin_file}](https://developer.wordpress.org/reference/hooks/plugin_action_links_plugin_file/)

## Check user capability or role

```php
user_can( $user_id, 'editor' ); // role
user_can( $user_id, 'install_plugins' ); // capability
```
See: [user_can](https://developer.wordpress.org/reference/functions/user_can/), [current_user_can](https://developer.wordpress.org/reference/functions/current_user_can/)

## Check a date string

Uses [date_convert_format](#convert-date-format)

```php
function is_date ( $date, $format = 'Y-m-d' ) {
	return $date === date_convert_format( $date, $format, $format );
}

// usage
is_date( '2022-03-04' ); // => true
is_date( '2022-03-33' ); // => false (invalid day: 33)
is_date( '2022-03', 'Y-m' ); // => true
```

## Convert date format

```php
function date_convert_format ( $date, $to, $from = 'Y-m-d' ) {
	$datetime = DateTime::createFromFormat( $from, $date );
	// h::throw_if( ! $datetime, "$date is not a valid date in format $from" );
	return $datetime->format( $to );
}

// usage
echo date_convert_format( '2022-03-04', 'd/m/Y' ); // => 04/03/2022
```

## Regex: remove anything from a string except numbers

```php
$str = '1a-2b-3c-4d';
echo preg_replace( '/[^0-9]/', '', $str ); // => 1234 
```

## Regex: remove anything from a string except letters

```php
$str = 'Olá Посетитель #4321';
echo preg_replace( "/[^\pL]/u", '', $str ); // => OláПосетитель
```

*Note: `\pL` is a Unicode property shortcut. It can also be written as `\p{L}` or `\p{Letter}`. It matches any kind of letter (case insensitive) from any language. [reference](https://www.regular-expressions.info/unicode.html#category)*
