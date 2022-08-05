## WP Plugin Base

Powerful Boilerplate for WordPress Plugins ― Lightweight successor of [luizbills/wp-plugin-skeleton](https://github.com/luizbills/wp-plugin-skeleton)

## Requirements

- [PHP](http://php.net/) v7.4+
- [Git](https://git-scm.com/)
- [Composer](https://getcomposer.org/) (globally installed as `composer`)

## Install

Execute this script below in your `wp-content/plugins` to generate a new plugin boilerplate:

```bash
git clone \
  --branch main \
  --single-branch --no-tags \
  https://github.com/luizbills/wp-plugin-base.git .wp_plugin_base \
&& cd .wp_plugin_base && php .bin/install.php && sleep .1 \
&& cd $(cat install.log) \
&& rm -rf ../.wp_plugin_base/
```

## How to use

- Declare the plugin dependencies in the [`/dependencies.php`](/dependencies.php) file.
- Develop plugin features in `/classes` directory.
- To automatically initialize a class, declare the `__start` method on the class and add it to the `/loader.php` file. See [`classes/Sample_Class.php`](classes/Sample_Class.php) to learn more.
- Use the `h::config_get` method to get the values declared in the [`/config.php`](/config.php) file. You can also use it to get these other values:
    - `h::config_get( 'FILE' )` returns the plugin main file path (absolute path)
    - `h::config_get( 'DIR' )` returns the plugin directory (absolute path)
    - `h::config_get( 'NAME' )` returns the plugin name (from your plugin header in [`/main.php`](main.php#L3))
    - `h::config_get( 'VERSION' )` returns the plugin current version (from your plugin header in [`/main.php`](main.php#L5))
- Use the helpers already declared in the [`/classes/Helpers.php`](/classes/Helpers.php) or declare others there.
- Put your view files in the `/templates` directory and use the `echo h::get_template( 'your_template', [ 'foo' => 'bar' ] );` to display their. *Example: create a file `/templates/title.php` with the content `<h1><?= esc_html( $args['text'] ) ?></h1>` and when you need to show that html, call `echo h::get_template( 'title.php', [ 'text' => 'My Title' ] );`*.

*Check out our [snippets](/.snippets) for tips and tricks.*

### Shell scripts

- `composer run make-pot` creates a `.pot` file inside of `/languages` directory.
- `composer run build` creates a `.zip` file inside of `/wp-build` directory. Easy way to share or install your plugin on other WordPress.
- `composer run deploy` updates your SVN repository and release a new version on https://wordpress.org (you need setup the [`/scripts/svn-push`](/scripts/svn-push) file first). This script also update your plugin assets (icon, banner and screenshot) when necessary in the `/.wordpress-org` directory.
- `composer run update-trunk` updates the `/trunk` of your SVN repository on https://wordpress.org (you need setup the [`/scripts/svn-push`](/scripts/svn-push) file first). This script also update your plugin assets (icon, banner and screenshot) when necessary in the `/.wordpress-org` directory.

## Contributing

- [Create an issue](https://github.com/luizbills/wp-plugin-base/issues/new) for questions, suggestions or bugs.

## LICENSE

This repository is licensed under [MIT](https://choosealicense.com/licenses/mit/).

The generated plugin source code will have [GPLv3](/LICENSE) by default (feel free to change).
