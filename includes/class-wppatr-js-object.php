<?php
/**
 * JS object class
 *
 * @package WPPageTransitionFree
 * @since   5.8.1
 */

if ( ! class_exists( 'WPPATR_JS_Object' ) ) {

	/**
	 * Send data to the JS files
	 *
	 * @version 5.4.1
	 */
	class WPPATR_JS_Object {

		/**
		 * Send an object to the front JS file
		 *
		 * @version 5.4.1
		 * @param string $file name of the file.
		 * @return void
		 */
		public static function send_frontend( $file ) {
			$transition = array(
				'page'    => get_option( 'wppatr-page', '' ),
				'overlay' => get_option( 'wppatr-overlay', '' ),
				'loader'  => get_option( 'wppatr-loader', '' ),
			);

			$transitions_options = array(
				'active'          => get_option( 'wppatr-active-transitions', '1' ),
				'pageContainer'   => get_option( 'wppatr-page-selector', 'body' ),
				'links'           => get_option( 'wppatr-links', 'a' ),
				'notLinks'        => get_option( 'wppatr-not-links', '' ),
				'removeScrollBar' => get_option( 'wppatr-remove-scroll-bar', '1' ),
				'transition'      => $transition,
			);
			wp_localize_script( $file, 'Transition', $transitions_options );
		}

		/**
		 * Send an object to the admin JS file
		 *
		 * @version 5.4.1
		 * @param string $file name of the file.
		 * @return void
		 */
		public static function send_admin( $file ) {
			$url_data = array(
				'ajaxUrl'     => admin_url( 'admin-ajax.php' ),
				'getHomeUrl'  => get_home_url(),
				'activeTheme' => array( wp_get_theme()->name, wp_get_theme()->parent_theme ),
			);
			wp_localize_script( $file, 'WPPATR_Localize_Url', $url_data );
		}
	}
}
