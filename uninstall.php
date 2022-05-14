<?php
/**
 * Uninstallation setup
 *
 * @package WPPageTransitionFree
 * @since   5.8.1
 */

defined( 'ABSPATH' ) || exit;

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

global $wpdb;

$options = array(
	'wppatr-links',
	'wppatr-not-links',
	'wppatr-page-selector',
	'wppatr-remove-scroll-bar',
	'wppatr-page',
	'wppatr-overlay',
	'wppatr-loader',
	'wppatr-active-transitions',
);

foreach ( $options as $option ) {

	delete_option( $option );

	if ( is_multisite() ) {
		delete_site_option( $option );
	}
}

wp_cache_flush();
