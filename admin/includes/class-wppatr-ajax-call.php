<?php
/**
 * Ajax call class management
 *
 * @package WPPageTransitionFree
 * @since   5.8.1
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WPPATR_Ajax_Call' ) ) {


	/**
	 * Handle Ajax Call
	 *
	 * @version 5.4.1
	 */
	class WPPATR_Ajax_Call {

		/**
		 * Hook the ajax action
		 *
		 * @version 5.4.1
		 */
		public static function install() {

			add_action( 'wp_ajax_wppatrnonce_save_options', array( self::class, 'update_form' ) );
			add_action( 'wp_ajax_nopriv_wppatrnonce_save_options', array( self::class, 'update_form' ) );

		}

		/**
		 * Emit an error and stop execution
		 *
		 * @version 5.4.1
		 * @return void
		 */
		public static function ajax_error() {
			wp_send_json( 'error' );
			wp_die();
		}

		/**
		 * Update the form
		 *
		 * @version 5.4.1
		 * @return void
		 */
		public static function update_form() {

			if ( ! DOING_AJAX || ! current_user_can( 'edit_theme_options' ) || ! isset( $_POST ) || ! key_exists( 'value', $_POST ) || ! key_exists( 'nonce', $_POST ) ) {
				self::ajax_error();
			}

			if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'wppatrnonce_save_options' ) ) {
				self::ajax_error();
			}

			$values = array();

			//phpcs:ignore
			wp_parse_str( wp_unslash( $_POST['value'] ), $values );

			$options = array( 'wppatr-links', 'wppatr-not-links', 'wppatr-page-selector', 'wppatr-remove-scroll-bar', 'wppatr-page', 'wppatr-overlay', 'wppatr-loader', 'wppatr-active-transitions' );

			if ( is_array( $values ) ) {
				foreach ( $values as $key => $value ) {
					if ( strstr( $key, 'wppatr-' ) && 'wppatr-nonce' !== $key && '_wp_http_referer' !== $key && in_array( $key, $options, true ) ) {
						if ( 'wppatr-loader' === $key || 'wppatr-overlay' === $key || 'wppatr-page' === $key ) {
							//phpcs:ignore : All the sanitize functions broke the plugin, how can I do so ?
							update_option( $key, stripslashes( $value ) );
						} else {
							update_option( $key, sanitize_text_field( stripslashes( $value ) ) );
						}
					}
				}
			}

			die();
		}

	}

}

WPPATR_Ajax_Call::install();
