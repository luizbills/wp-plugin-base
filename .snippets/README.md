# WordPress Plugins Snippets

## Add an bulk action

```php
$screen_id = 'edit-shop-order'; // WooCommerce Orders

add_filter( 'bulk_actions-' . $screen_id, 'prefix_register_actions' );
function prefix_register_actions ( $actions ) {
	$actions[ 'YOUR_ACTION_ID' ] = __( 'Gerar Etiquetas do Correios' );
	return $actions;
}

add_filter( 'handle_bulk_actions-' . $screen_id, 'prefix_handle_actions', 10, 3 );
function prefix_handle_actions ( $redirect_to, $action, $post_ids ) {
	if ( $action === 'YOUR_ACTION_ID' ) {
		foreach ( $post_ids as $postid ) {
			// do something
		}
	}
	return $redirect_to;
}
```

See: [https://make.wordpress.org/core/2016/10/04/custom-bulk-actions/](https://make.wordpress.org/core/2016/10/04/custom-bulk-actions/)

## Add an plugin action link

```php
// __FILE__ is the plugin main file
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'prefix_add_plugin_action_link' );
function prefix_add_plugin_action_link ( $actions ) {
	$label = 'Settings';
	$dest_url = esc_url( 'your_url' );
	return array_merge( [ "<a href=\"$dest_url\">$label</a>" ], $actions );
}
```

See: [plugin_action_links_{$plugin_file}](https://developer.wordpress.org/reference/hooks/plugin_action_links_plugin_file/)

## Add a notice in the admin panel

```php
add_action( 'admin_notices', 'prefix_admin_notice_error' );
function prefix_admin_notice_error () {
	?>
	<div class="notice notice-error is-dismissible">
		<p><?= esc_html__( 'Error!', 'your-text-domain' ); ?></p>
	</div>
	<?php
}
```

In order to display a notice, echo a div with the class `notice` and one of the following classes:

* `notice-error` – will display the message with a white background and a red left border.
* `notice-warning` – will display the message with a white background and a yellow/orange left border.
* `notice-success` – will display the message with a white background and a green left border.
* `notice-info` – will display the message with a white background a blue left border.
* Optionally use `is-dismissible` to add a closing icon to your message via JavaScript. Its behavior, however, applies only on the current screen. It will not prevent a message from re-appearing once the page re-loads, or another page is loaded.

See: [admin_notices](https://developer.wordpress.org/reference/hooks/admin_notices/)

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
function date_convert_format ( $datestring, $to, $from = 'Y-m-d' ) {
	$datetime = \DateTime::createFromFormat( $from, $datestring );
	if ( $datetime && $datetime->format( $from ) === $datestring ) {
		return $datetime->format( $to );
	}
	return null;
}

// usage
echo date_convert_format( '2022-03-04', 'U' ); // => the timestamp
echo date_convert_format( '2022-03-04', 'd/m/Y' ); // => 04/03/2022
echo date_convert_format( '2022-13-04', 'd/m/Y' ); // => null (month 13 does not exists)
```

## Convert array in object

```php 
$arr = [ 'name' => 'Luiz' ];
$obj = (object) $arr;
echo $obj->name; // => Luiz
```

## Extract a slice of an array, given a list of keys.

```php
// data example
$arr = [
	'a' => 1,
	'b' => 2,
	'c' => 3,
];

// usage
$only_bc = wp_array_slice_assoc( $arr, [ 'b', 'c' ] ); // => [ 'b' => 2, 'c' => 3 ]
```

See: [wp_array_slice_assoc](https://developer.wordpress.org/reference/functions/wp_array_slice_assoc/)

## Extract (plucks) a certain field out of each object or array in an array.

```php
// data example
$pages = get_pages();

// usage
$titles = wp_list_pluck( $pages, 'post_title' ); // returns an array of page titles
```

See: (wp_list_pluck)[https://developer.wordpress.org/reference/functions/wp_list_pluck/]
	
## Get the current WordPress page URL

```php
function get_current_url ( $query_args = null ) {
	global $wp;
	if ( ! $wp ) {
		error_log( 'WARNING `' . __FUNCTION__ . '` should not be called before "parse_request" hook' );
		return null;
	}
	$protocol = isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
	$url = "{$protocol}://{$_SERVER['HTTP_HOST']}/{$wp->request}";
	return $query_args ? \add_query_arg( $query_args, $url ) : $url;
}

// usage
echo get_current_url(); // the current page url
echo get_current_url( $_GET ); // the current page url with query arguments
```

## Human-readable file size

```php
$size_in_bytes = 4567;
$decimals = 1; // default is 2
echo size_format( $size_in_bytes, $decimals ); // => 4,5 KB
```

See: [size_format](https://developer.wordpress.org/reference/functions/size_format/)

## Interpolates an array of values into the string/message placeholders.

```php
function str_interpolate ( $message, $context = [] ) {
	// build a replacement array with braces around the context keys
	$replace = [];
	foreach ($context as $key => $val) {
		// check that the value can be cast to string
		if ( ! is_array( $val ) && ( ! is_object( $val ) || method_exists( $val, '__toString' ) ) ) {
			$replace['{' . $key . '}'] = $val;
		}
	}
	// interpolate replacement values into the message and return
	return \strtr( $message, $replace );
}

// usage
echo str_interpolate( "User {username} created", [ 'username' => 'bolivar' ] );
// => User bolivar created
```

Credits: [PSR-3: Logger Interface ](https://www.php-fig.org/psr/psr-3/#12-message)

## Parse tag attributes from an array

```php
function build_tag_atts ( $arr ) {
	$atts = [];
	foreach ( $arr as $key => $value) {
		$atts[] = sanitize_title_with_dashes( $key ) . '="' . esc_attr( $value ) . '"';
	}
	return \implode( ' ', $atts );
}

// usage
echo build_tag_atts( [ 'id' => 'a', 'data xyz' => 'b' ] ); // => id="a" data-xyz="b"
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
