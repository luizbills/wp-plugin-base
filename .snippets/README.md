# WordPress Plugins Snippets

## Add an bulk action

```php
$screen_id = 'edit-shop-order'; // WooCommerce Orders

add_filter( 'bulk_actions-' . $screen_id, 'prefix_register_actions' );
function prefix_register_actions ( $actions ) {
	$actions[ 'YOUR_ACTION_ID' ] = __( 'Gerar Etiquetas do Correios' );
	return $actions;
}

add_filter( 'handle_bulk_actions-edit-post', 'prefix_handle_actions', 10, 3 );
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
	$datetime = \DateTime::createFromFormat( $from, $date );
	if ( $datetime ) {
		return $datetime->format( $to );
	}
	return null;
}

// usage
echo date_convert_format( '2022-03-04', 'd/m/Y' ); // => 04/03/2022
```

## Convert array in object

```php 
$arr = [ 'name' => 'Luiz' ];
$obj = (object) $arr;
echo $obj->name; // => Luiz
```

## Get the current WordPress page URL

```php
function get_current_url ( $query_args = false ) {
	global $wp;
	$host = $_SERVER['HTTP_HOST'];
	$path = isset( $wp->request ) ? $wp->request : '';
	$protocol = isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
	$url = "{$protocol}://{$host}/{$path}";
	return $query_args ? \add_query_arg( $_REQUEST, $url ) : $url;
}
```

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
echo build_tag_atts( [ 'id' => 'a', 'data xyz' => 'b' ] ); // id="a" data-xyz="b"
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
