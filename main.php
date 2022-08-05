<?php
/*
Plugin Name: your_plugin_name
Plugin URI: https://github.com/your/repo
Description: your_plugin_description
Version: 1.0.0
Requires PHP: 7.4
Requires at least: 5.0
Author: your_author_name
Author URI: your_author_uri
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
defined( 'WPINC' ) || exit( 1 );

// uncomment below to load your plugin translations
// load_plugin_textdomain( 'your_text_domain', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

try {
	// check composer autoload
	$composer_autoload = __DIR__ . '/vendor/autoload.php';
	if ( ! file_exists( $composer_autoload ) ) {
		throw new \Error( $composer_autoload . ' does not exist' );
	}
	include_once $composer_autoload;
} catch ( Throwable $e ) {
	return add_action( 'admin_notices', function () use ( $e ) {
		if ( ! current_user_can( 'install_plugins' ) ) return;
		list( $plugin_name ) = get_file_data( __FILE__, [ 'plugin name' ] );
		$message = sprintf(
			/* translators: %1$s is replaced with plugin name and %2$s with an error message */
			esc_html__( 'Error on %1$s plugin  activation: %2$s', 'your_text_domain' ),
			'<strong>' . esc_html( $plugin_name ) . '</strong>',
			'<br><code>' . esc_html( $e->getMessage() ) . '</code>'
		);
		echo "<div class='notice notice-error'><p>$message</p></div>";
	} );
}

// run the plugin
\Your_Namespace\Core\Main::start_plugin( __FILE__ );
