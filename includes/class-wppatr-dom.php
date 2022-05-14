<?php
/**
 * Create the DOM transition class
 *
 * @package WPPageTransitionFree
 * @since   5.8.1
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WPPATR_Dom' ) ) {

	/**
	 * Create the DOM setup during transitions
	 *
	 * @version 5.4.1
	 */
	class WPPATR_Dom {

		/**
		 * Enqueue the transition DOM
		 *
		 * @version 5.4.1
		 * @return void
		 */
		public static function create() {

			if ( ! WPPageTransitionFree::divi_page_builder_is_activ() && '1' === get_option( 'wppatr-active-transitions', '1' ) ) {
				add_action( 'wp_head', array( self::class, 'style' ) );
				add_action( 'wp_body_open', array( self::class, 'elements' ) );
				if ( get_option( 'wppatr-remove-scroll-bar', '1' ) === '1' ) {
					self::add_body_block_scroll_class();
				}
			}

		}

		/**
		 * Display the DOM style
		 *
		 * @version 5.4.1
		 * @return void
		 */
		public static function style() {

			$page_container    = get_option( 'wppatr-page-selector', 'body' );
			$active_transition = self::get_transition();

			if ( isset( $active_transition ) ) {
				$loader_css  = new stdClass();
				$overlay_css = new stdClass();
				$page_css    = new stdClass();

				$loader_object  = JSON_decode( $active_transition->loader );
				$overlay_object = JSON_decode( $active_transition->overlay );
				$page_object    = JSON_decode( $active_transition->page );

				if ( '' === $active_transition->loader ) {
					$loader_css->anim = '';
					$loader_css->time = '';
				} elseif ( null !== $loader_object ) {
					$loader_css = $loader_object->css;
				} else {
					$loader_css = null;
				}

				if ( '' === $active_transition->overlay ) {
					$in_obj  = new stdClass();
					$out_obj = new stdClass();

					$in_obj->from = '';
					$in_obj->in   = '';
					$in_obj->time = '';

					$out_obj->from = '';
					$out_obj->in   = '';
					$out_obj->time = '';

					$overlay_css->main = '';
					$overlay_css->in   = $in_obj;
					$overlay_css->out  = $out_obj;
				} elseif ( null !== $overlay_object ) {
					$overlay_css = $overlay_object->css;
				} else {
					$overlay_css = null;
				}

				if ( '' === $active_transition->page ) {
					$page_css->in  = '';
					$page_css->out = '';
				} elseif ( null !== $page_object ) {
					$page_css = $page_object;
				} else {
					$page_css = null;
				}

				?>
					<style type="text/css" id="wppatr-style">
						<?php echo esc_html( $page_container ); ?> {
							transition-timing-function: ease-out;
							transition-property: all;
							transition-duration: 1000ms;
							transition-delay: 0ms;
							transform-origin: 50vw 50vh;
							<?php echo esc_html( WPPATR_Autoprefixer::compile( $page_css->in ) ); ?>
						}
						<?php echo esc_html( $page_container ); ?>.page-loaded,
						.page-loaded <?php echo esc_html( $page_container ); ?> {
							opacity: 1;
							transform: none;
							transform-origin: 50vw 50vh;
						}
						<?php echo esc_html( $page_container ); ?>.change-page,
						.change-page 
						<?php echo esc_html( $page_container ); ?> {
							<?php echo esc_html( WPPATR_Autoprefixer::compile( $page_css->out ) ); ?>
						}
						<?php
						if ( null !== $overlay_css ) {
							echo esc_html( WPPATR_Autoprefixer::compile( $overlay_css->main . $overlay_css->in->from . $overlay_css->in->to . $overlay_css->in->time . $overlay_css->out->from . $overlay_css->out->to . $overlay_css->out->time ) );
						}
						?>
						<?php
						if ( null !== $loader_css ) {
							echo esc_html( WPPATR_Autoprefixer::compile( $loader_css->anim . $loader_css->time ) );
						}
						?>
					</style>
					<noscript>
						<style type="text/css">
							<?php echo esc_html( $page_container ); ?> {
								transform-origin: unset !important;
								opacity: 1 !important;
								transform: none !important;
							}
							#transition-container{
								display: none !important;
							}
						</style>
					</noscript>
				<?php
				self::display_flint_notice();
			}

		}

		/**
		 * Dipslay a flint notice
		 *
		 * @version 5.4.1
		 * @return void
		 */
		public static function display_flint_notice() {
			?>
			<div id="flint-notice" style="display:block !important;opacity:1 !important;position:fixed !important;top:20px !important;right:20px !important;z-index:999999999999999999999999999999999999999999999999999999999999 !important;user-select:none !important;background-color:white !important;padding:5px !important;border-radius:3px !important;font-family:'Inter', sans-serif !important;font-weight:normal !important;color:black !important;">
				Transition created with <a href="https://fluent-interface.com/" style="display:inline !important;opacity:1 !important;font-size:12px !important;color:#ff68a2 !important;">WP Page Transition</a>
			</div>
			<?php
		}

		/**
		 * Display the DOM elements
		 *
		 * @version 5.4.1
		 * @return void
		 */
		public static function elements() {

			$active_transition = self::get_transition();

			if ( isset( $active_transition ) ) {
				$loader_html  = '';
				$overlay_html = '';

				$loader_object  = JSON_decode( $active_transition->loader );
				$overlay_object = JSON_decode( $active_transition->overlay );

				if ( '' !== $active_transition->loader && null !== $loader_object ) {
					$loader_html = $loader_object->html;
				}

				if ( '' !== $active_transition->overlay && null !== $overlay_object ) {
					$overlay_html = $overlay_object->html;
				}

				?>
				<div id="transition-container" class="wppatr-transition-container init init-time">
					<div id="loader-setup" class="wppatr-loader-setup">
						<?php
						if ( '' !== $loader_html ) {
							echo wp_kses_post( html_entity_decode( $loader_html ) );
						}
						?>
					</div>
					<div id="overlay-setup" class="wppatr-overlay-setup">
						<?php
						if ( '' !== $overlay_html ) {
							echo wp_kses_post( html_entity_decode( $overlay_html ) );
						}
						?>
					</div>
				</div>
				<?php
			}

		}

		/**
		 * Get the built transition
		 *
		 * @return object The saved transition
		 */
		public static function get_transition() {
			$transition          = new stdClass();
			$transition->page    = get_option( 'wppatr-page', '' );
			$transition->overlay = get_option( 'wppatr-overlay', '' );
			$transition->loader  = get_option( 'wppatr-loader', '' );
			return $transition;
		}

		/**
		 * Add scroll block class to the body class filter
		 *
		 * @version 5.4.1
		 * @return void
		 */
		public static function add_body_block_scroll_class() {
			add_filter(
				'body_class',
				function( $classes ) {
					return array_merge( $classes, array( 'scroll-block' ) );
				}
			);
		}

	}

	WPPATR_Dom::create();

}
