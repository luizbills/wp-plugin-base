<?php
/*
Plugin Name: your_plugin_name
Version: 1.0.0
Description: your_plugin_description
Author: your_author_name
Author URI: your_author_uri
Update URI: false

Text Domain: your_text_domain
Domain Path: /languages

License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

// prevents your PHP files from being executed via direct browser access
defined( 'WPINC' ) || exit();

// uncomment to load your plugin translations
// load_plugin_textdomain( 'your_text_domain', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

try {
	// check composer autoload
	$composer_autoload = __DIR__ . '/vendor/autoload.php';
	if ( ! file_exists( $composer_autoload ) ) {
		throw new \Error( $composer_autoload . ' does not exist' );
	}
	include_once $composer_autoload;
} catch ( Throwable $e ) {
	add_action( 'admin_notices', function () use ( $e ) {
		list( $plugin_name ) = get_file_data( __FILE__, [ 'plugin name' ] );
		$message = sprintf(
			esc_html__( 'Error on plugin %s activation: %s', 'your_text_domain' ),
			'<strong>' . esc_html( $plugin_name ) . '</strong>',
			'<br><code>' . esc_html( $e->getMessage() ) . '</code>'
		);
		echo "<div class='notice notice-error'><p>$message</p></div>";
	} );
}

// run the plugin
\Your_Namespace\Core\Main::start_plugin( __FILE__ );
