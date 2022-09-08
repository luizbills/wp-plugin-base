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
&& cd $wp_plugin_base_clone_dir && php .wppb/install.php && sleep .1 \
cd .. && rm -rf "./$wp_plugin_base_clone_dir"
```

## Upgrade

You can update the `core` directory of your plugin running the command `composer run upgrade-core`.

## Getting started

This boilerplace is organized into several files and directories. It is essential that you understand each of them.

### `/main.php`

This is the main plugin file with its headers (plugin name, version, license, etc). Open the file and fill in some remaining information, like the `Plugin URI` and `Description`.

If you want to keep translation files inside your plugin, you should uncomment the `load_plugin_textdomain` function.

### `/uninstall.php`

This file is automatically executed when your plugin is deleted. Use it to clean the database (if your plugin saved anything).

### `/config` directory

The `config` directory contains some plugin configuration files. It's a great idea to open and read through [all of these files](/config) and familiarize yourself with all of the options available to you.

### `/core` directory

This directory has the core of our boilerplace (classes that initialize the plugin, check dependencies, load classes from `/config/loader.php`, etc). You don't need to understand the core classes, but you can take a look, it's all pretty simple.

The `/core/VERSION` file stores the boilerplate version. So you can update the files in the core directory using the `composer run upgrade-core` command (without having to do it manually).

### `/classes` directory

In this directory you will put the classes that control all features of your plugin: custom post types, settings pages, shortcodes, etc.

If you put a class in the `/config/loader.php` file, its `__start` method will be executed automatically when the plugin is ready to work. See the [`classes/Sample_Class.php`](classes/Sample_Class.php) to more details.

**NOTE:** You don't need to add all your classes in `/config/loader.php`. Only add the classes that need to run some code automatically or to listen WordPress actions and filters hooks.

### The `Helpers` class

The [`classes/Helpers.php`](/classes/Helpers.php) file contains several static methods that can be very useful in developing your plugin. To make it easier, I recommend that at the beginning of each class file you include `use Your_Namespace\Helpers as h;` to work as a shortcut `h` for the `Helpers` class.

**NOTE:** The `Helpers` class is not used in the boilerplace core, so you can modify or even delete it without any problem.

Take a look at some helper methods that you will enjoy using.

#### ➞ `h::config_get( $key, $default = null )`

Returns the values ​​you declared in the `/config/plugin.php` file. The first argument is the key and the second is a default value that will be returned if the key does not exist. However, if you don't want to declare a default value, the plugin will throw an exception (to help avoid typos).

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

Prepends the plugin PREFIX (defined in `/config/plugin.php`) in the `$string`. By default, the `$string` is sanitized (only numbers, letters and underlines)`.

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

See the [`/core/Traits`](/core/Traits) directory to learn the helpers. They are all very simple codes to read and understand. If you think some that helpers could be even simpler, please [open an issue](https://github.com/luizbills/wp-plugin-base/issues/new).

### `/assets` directory

Put your frontend stuffs there: JavaScript, CSS, images ...

### `/.wordpress-org` directory

Put WordPress SVN repository stuffs there: Plugin icon, banner and screenshots.

**NOTE:** If you are not planning to publish your plugin, you can delete this directory.

### `/scripts` directory

This directory contains some scripts that are used in the commands explained below:

- `composer run make-pot` creates a `.pot` file inside of `/languages` directory.
- `composer run build` creates a `.zip` file inside of `/wp-build` directory. Easy way to share or install your plugin on other WordPress.
- `composer run upgrade-core` will update the `/core` directory of your plugin, pulling the latest changes from this Github repository.
- `composer run deploy` updates your SVN repository and release a new version on https://wordpress.org (you need setup the [`/scripts/svn-push`](/scripts/svn-push) file first). This script also update your plugin assets (icon, banner and screenshot) when necessary in the `/.wordpress-org` directory.
- `composer run update-trunk` updates the `/trunk` of your SVN repository on https://wordpress.org (you need setup the [`/scripts/svn-push`](/scripts/svn-push) file first). This script also update your plugin assets (icon, banner and screenshot) when necessary in the `/.wordpress-org` directory.

## Contributing

- [Create an issue](https://github.com/luizbills/wp-plugin-base/issues/new) for questions, suggestions or bugs.

## LICENSE

This repository is licensed under [MIT](https://choosealicense.com/licenses/mit/).

The generated plugin source code will have [GPLv3](/LICENSE) by default (feel free to change).
