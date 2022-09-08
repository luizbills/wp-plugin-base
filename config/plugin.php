<?php
/*
In this file you can declare useful (and immutable) values that will be used in different places in the plugin. By default, values like the plugin's **SLUG** and **PREFIX** will already be declared (you can change them if you want, but you can't delete them).

Internally, the plugin will also declare the following values:
  - "NAME": The plugin name.
  - "VERSION": The plugin version.
  - "FILE": The main file absolute path (`/main.php`).
  - "DIR": The plugin directory absolute path.

To get these values, you should use the `config_get`. See core/Traits/Config_Helpers.php to learn more.
*/

// prevents your PHP files from being executed via direct browser access
defined( 'WPINC' ) || exit( 1 );

return [
	'SLUG' => 'your_plugin_slug',
	'PREFIX' => 'your_plugin_prefix',
	'TEMPLATES_DIR' => 'templates',
];
