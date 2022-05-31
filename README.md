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
&& cd $(cat .newplugin) \
&& rm -rf ../.wp_plugin_base/

```

## Contributing

- For features or bug fixes, follow the [CONTRIBUTING guide](CONTRIBUTING.md).
- Or create an issue for suggestions and other reports.

## LICENSE

GPL v3
