## WP Plugin Base

Powerful Boilerplate for WordPress Plugins.

> The lightweight successor of [luizbills/wp-plugin-skeleton](https://github.com/luizbills/wp-plugin-skeleton)

## Requirements

- [PHP](http://php.net/) v7.4+
- [Git](https://git-scm.com/downloads)
- [Composer](https://getcomposer.org/download/) (globally installed as `composer`)
- [WP-CLI](https://wp-cli.org/#installing) (optional; only needed to generate the `.pot` file)

## Install

Open your terminal and execute the script below in your `wp-content/plugins` to generate a new plugin:

```bash
wp_plugin_base_clone_dir=".wp_plugin_base_$(date +%s)" \
&& git clone --branch main --single-branch --no-tags --quiet \
  https://github.com/luizbills/wp-plugin-base.git $wp_plugin_base_clone_dir \
&& cd $wp_plugin_base_clone_dir && php .bin/install.php && sleep .1 \
&& cd $(cat install.log) \
&& chmod -R +x ./scripts \
&& rm -rf "../$wp_plugin_base_clone_dir"
```

## Getting started

This boilerplace is organized into several files and folders. It is essential that you understand each of them.

### `/main.php`

This is the main plugin file with its headers (plugin name, version, license, etc). Open the file and fill in some remaining information, like the `Plugin URI` and `Description`.

If you want to keep translation files inside your plugin, you should uncomment the `load_plugin_textdomain` function.

### `/config.php`

In this file you can declare useful (and immutable) values that will be used in different places in the plugin. By default, values like the plugin's **SLUG** and **PREFIX** will already be declared (you can change them if you want, but you can't delete them).

Internally, the plugin will also declare the following keys:
- `NAME`: The plugin name.
- `VERSION`: The plugin version.
- `FILE`: The main file absolute path (`/main.php`).
- `DIR`: The plugin folder absolute path.

To get these values, you should use the `config_get` helper:

```php
use Your_Namespace\Helpers as h;

// example: print your plugin name
echo h::config_get( 'NAME' );
```

### `/dependencies.php`

In this file you must inform what your plugin needs to work. By default, the plugin will already check the server's PHP version (v7.4 or later).

Each dependency is an array that must contain the following keys:
- `check`: a function that should check if any requirements have been met.
- `message`: a string (or function that returns a string) that will be displayed if the requirement is not met.

Example: use the following code below to indicate that your plugin depends on the WooCommerce plugin to work.

```php
$deps[] = [
	'check' => function () {
		// checks if the `WooCommerce` class exists
		// that class only exists when the WooCommerce plugin is active.
		return class_exists( 'WooCommerce' );
	},

	// the message that will be shown if WooCommerce plugin is not activated.
	'message' => 'This plugin depends on WooCommerce plugin to works.'
];
```

If any dependencies are missing, the plugin will not work and a notice will be shown in the admin panel informing the reason (with the messages you declared).

Open the [/dependencies.php](/dependencies.php) to learn more.

### `/loader.php`

This file should return a array of classes that you want to run automatically, when the plugin is ready to work (if all dependencies in the `/dependencies.php` file are satisfied).

Each index in the array can be a class name or an array (with the class name and an integer to indicate priority). By default, each class has priority 10.

Example:
```php
// loader.php
return [
	My_Class_1::class, // priority = 10
	[ My_Class_2::class, 20 ], // priority = 20
	[ My_Class_3::class, 5 ], // priority = 5
];
```

In the example above, the classes will run in the following order: `My_Class_2` then `My_Class_1` and finally `My_Class_3`. Classes with higher priority are executed first.

### `/uninstall.php`

This file is automatically executed when your plugin is deleted. Use it to clean the database (if your plugin saved anything).

### `/core` folder

This folder has the core of our boilerplace (classes that initialize the plugin, check dependencies, load classes from `/loader.php`, etc). You don't need to understand the core classes, but you can take a look, it's all pretty simple.

The `/core/VERSION` file stores the boilerplate version. So you can update the files in the core folder using the `composer run upgrade-core` command (without having to do it manually).

### `/classes` folder

In this folder you will put the classes that control all features of your plugin: custom post types, settings pages, shortcodes, etc.

If you put a class in the `/loader.php` file, its `__start` method will be executed automatically when the plugin is ready to work. See the [`classes/Sample_Class.php`](classes/Sample_Class.php) to more details.

**NOTE:** You don't need to add all your classes in `/loader.php`. Only add the classes that need to run some code automatically or to listen WordPress actions and filters hooks.

### The `Helpers` class

The [`classes/Helpers.php`](/classes/Helpers.php) file contains several static methods that can be very useful in developing your plugin. To make it easier, I recommend that at the beginning of each class file you include `use Your_Namespace\Helpers as h;` to work as a shortcut `h` for the `Helpers` class.

**NOTE:** The `Helpers` class is not used in the boilerplace core, so you can modify or even delete it without any problem.

Take a look at some helper methods that you will enjoy using.

#### ➞ `h::config_get( $key, $default = null )`

Returns the values ​​you declared in the `/config.php` file. The first argument is the key and the second is a default value that will be returned if the key does not exist. However, if you don't want to declare a default value, the plugin will throw an exception (to help avoid typos).

#### ➞ `h::config_set( $key, $value )`

Temporarily save values, an alternative to global variables (no need to worry about prefixes). To get the saved values, just use `h::config_get`.

#### ➞ `h::get_defined( $constant, $default = null )`

Checks whether a constant (declared with `define` or `const` in a class) exists and returns its value. If it doesn't exist, returns a default value (the second argument, which is `null` by default).

#### ➞ `h::filled( $var )`

Returns `false` ONLY IF $var is `null`, `empty array`, `empty object` or `empty string`.

**Note:** `h::filled` works differently than `empty`.

```php
use Your_Namespace\Helpers as h;

h::filled( null ); // false
h::filled( [] ); // false (empty array)
h::filled( new \stdClass() ); // false (empty class)
h::filled( "" ); // false (empty string)
h::filled( "     " ); // false (strings are trimmed)
h::filled( false ); // true (it is filled with a boolean)
h::filled( 0 ); // true (it is filled with a number)

// ... Any other values ​​returns TRUE
```

#### ➞ `h::prefix( $string, $sanitize = true )`

Prepends the plugin PREFIX (defined in `/config.php`) in the `$string`. By default, the `$string` is sanitized (only numbers, letters and underlines)`.

```php
use Your_Namespace\Helpers as h;

echo h::prefix( 'foo bar baz' ) // "your_prefix_foo_bar_baz"
echo h::prefix( 'foo bar baz', false ) // "your_prefix_foo bar baz"
```

#### ➞ `h::dd( ...$vars )`

Useful to quick debug something. This helper will print all arguments with `var_dump` and immediately stop PHP execution with `die( 1 )`.

```php
h::dd( $var ) // print the $var content and exit
```

#### ➞ `h::log( ...$vars )`

Useful to quick logs something in `debug.log` (if your `WP_DEBUG` and `WP_DEBUG_LOG` are enabled).

```php
use Your_Namespace\Helpers as h;

h::log( $var ) // logs the $var in wp-content/debug.log by default
```

#### ➞ `h::throw_if( $condition, $message, $class = \Error::class )`

If the `$condition` is falsy, throws a exception (by default a `\Error` instance) with the `$message`.

```php
use Your_Namespace\Helpers as h;

h::throw_if(
	is_null( $my_var ),
	'My exception message'
);

// it is the same as
if ( is_null( $my_var ) ) {
	throw new \Error( 'My exception message' );
}

// also, you can use other exception classes (3rd argument)
h::throw_if(
	is_null( $my_var ),
	'My exception message',
	Your_Namespace\CustomException::class,
);
```

#### Other helpers

See the [`/core/Traits`](/core/Traits) folder to learn the helpers. They are all very simple codes to read and understand. If you think some that helpers could be even simpler, please [open an issue](https://github.com/luizbills/wp-plugin-base/issues/new).

### `/assets` folder

Put your frontend stuffs there: JavaScript, CSS, images ...

### `/.wordpress-org` folder

Put WordPress SVN repository stuffs there: Plugin icon, banner and screenshots.

**NOTE:** If you are not planning to publish your plugin, you can delete this folder.

### `/scripts` folder

This folder contains some scripts that are used in the commands explained below:

- `composer run make-pot` creates a `.pot` file inside of `/languages` directory.
- `composer run build` creates a `.zip` file inside of `/wp-build` directory. Easy way to share or install your plugin on other WordPress.
- `composer run upgrade-core` will update the `/core` folder of your plugin, pulling the latest changes from this Github repository.
- `composer run deploy` updates your SVN repository and release a new version on https://wordpress.org (you need setup the [`/scripts/svn-push`](/scripts/svn-push) file first). This script also update your plugin assets (icon, banner and screenshot) when necessary in the `/.wordpress-org` directory.
- `composer run update-trunk` updates the `/trunk` of your SVN repository on https://wordpress.org (you need setup the [`/scripts/svn-push`](/scripts/svn-push) file first). This script also update your plugin assets (icon, banner and screenshot) when necessary in the `/.wordpress-org` directory.

## Contributing

- [Create an issue](https://github.com/luizbills/wp-plugin-base/issues/new) for questions, suggestions or bugs.

## LICENSE

This repository is licensed under [MIT](https://choosealicense.com/licenses/mit/).

The generated plugin source code will have [GPLv3](/LICENSE) by default (feel free to change).
