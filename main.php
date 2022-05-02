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

try {
	$composer_autoload = __DIR__ . '/vendor/autoload.php';
	if ( ! file_exists( $composer_autoload ) ) {
		throw new \Exception ( $composer_autoload . ' does not exist' );
	}
	include_once $composer_autoload;
	\Your_Namespace\Core\Main::start_plugin( __FILE__ );
} catch ( \Throwable $e ) {
	\add_action( 'admin_notices', function () use ( $e ) {
		list( $plugin_name ) = \get_file_data( __FILE__, [ 'plugin name' ] );
		$message = \sprintf(
			'Error on plugin %s activation: %s',
			"<strong>$plugin_name</strong>",
			'<br><code>' . \esc_html( $e->getMessage() ) . '</code>'
		);
		echo "<div class='notice notice-error'><p>$message</p></div>";
	} );
}
