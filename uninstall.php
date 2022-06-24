<?php
/*
Always check for the constant WP_UNINSTALL_PLUGIN in uninstall.php before doing anything. This protects against direct access.
The constant will be defined by WordPress during the uninstall.php invocation.
The constant is NOT defined when uninstall is performed by `register_uninstall_hook`.
Reference: https://developer.wordpress.org/plugins/plugin-basics/uninstall-methods/#method-2-uninstall-php
*/
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit( 1 );
}

// $option_name = 'your_plugin_option';
// delete_option( $option_name );

//// Use `delete_site_option` in Multisite. Note: In Multisite, looping through all blogs to delete options can be very resource intensive.
// delete_site_option( $option_name );

// global $wpdb;
// $wpdb->query(
// 	"DROP TABLE IF EXISTS {$wpdb->prefix}yourtable" // drop a custom db table
// );
