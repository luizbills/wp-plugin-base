<?php
/*
Plugin Name: your_plugin_name
Plugin URI: https://github.com/your/repo
Description: your_plugin_description
Version: 1.0.0
Author: your_author_name
Author URI: your_author_uri
Requires PHP: 7.4
Requires at least: 5.0
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html
Text Domain: your_text_domain
Domain Path: /languages

your_plugin_name is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, version 3 of the License.

your_plugin_name is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.

You should have received a copy of the GNU General Public License
along with your_plugin_name. If not, see http://www.gnu.org/licenses/gpl-3.0.html
*/

// prevents your PHP files from being executed via direct browser access
defined( 'ABSPATH' ) || exit( 1 );

$autoload = __DIR__ . '/vendor/autoload.php';
if ( file_exists( $autoload ) ) {
	// composer autoload
	include $autoload;
	// start the plugin
	\Your_Namespace\Core\Main::start_plugin( __FILE__ );
} else {
	// display a error
	return add_action( 'admin_notices', function () {
		// error visible only for admin users
		if ( ! current_user_can( 'install_plugins' ) ) return;

		include_once ABSPATH . '/wp-includes/functions.php';
		list( $plugin_name ) = get_file_data( __FILE__, [ 'plugin name' ] );

		$message = sprintf(
			'Error on %1$s plugin activation: %2$s',
			'<strong>' . esc_html( $plugin_name ) . '</strong>',
			'<code>Autoload file not found</code><br><em>Download this plugin from WordPress repository and avoid downloading from other sources (Github, etc).</em>'
		);

		echo "<div class='notice notice-error'><p>$message</p></div>";
	} );
}
