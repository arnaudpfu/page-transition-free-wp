<?php
/**
 * Session class management
 *
 * @package WPPageTransitionFree
 * @since   5.8.1
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WPPATR_Basic_Builder' ) ) {

	/**
	 * Manage the DOM of the BB
	 *
	 * @version 5.4.1
	 */
	class WPPATR_Basic_Builder {

		/**
		 * Handle the DOM of the BB
		 *
		 * @version 5.4.1
		 * @return void
		 */
		public static function create() {
			?>
			<div id="basic-builder">

				<div class="builder-title">
					<h1 id="epanel-title"><?php esc_html_e( 'WP Page Transition', 'wp-page-transition-free' ); ?></h1><span>by <strong><a href="https://fluent-interface.com" target="_blank">Fluent Interface</a></strong></span>
				</div>

				<div id="basic-builder-menu">
					<ul class="settings-tab-btn-container">
						<li class="settings-tab-btn active" tab-type='button' tab-family='transition-settings' tab-link='transition'><?php esc_html_e( 'Transition', 'wp-page-transition-free' ); ?></li>
						<li class="settings-tab-btn" tab-type='button' tab-family='transition-settings' tab-link='settings'><?php esc_html_e( 'Settings', 'wp-page-transition-free' ); ?></li>
					</ul>
				</div>

				<div class="transition-panel">

					<div class="settings-tab active" tab-type='tab' tab-family='transition-settings' tab-link='transition'>

						<?php self::comparative_table(); ?>

						<h2><?php esc_html_e( 'Select a transition :', 'wp-page-transition-free' ); ?></h2>

						<div class="transition-library" radio-selector>

							<div class="transition-none layout-container transition-layout-container" radio-unit>
								<div class="layout-ratio">
									<div class="layout-item" style='background-color:var(--darker);'>
										<video src="<?php echo esc_url( WPPATR_PLUGIN_URL ); ?>admin/assets/transition-render/fade.m4v" muted type="video/mp4" style="opacity:0;pointer-event:none;"></video>
										<h3 class="layout-title"><?php esc_html_e( 'None', 'wp-page-transition-free' ); ?></h3>
									</div>
									<svg class="none-option-cross" aria-hidden="true" focusable="false" data-prefix="far" data-icon="times-circle" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-inline--fa fa-times-circle fa-w-16 fa-5x"><path fill="currentColor" d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm0 448c-110.5 0-200-89.5-200-200S145.5 56 256 56s200 89.5 200 200-89.5 200-200 200zm101.8-262.2L295.6 256l62.2 62.2c4.7 4.7 4.7 12.3 0 17l-22.6 22.6c-4.7 4.7-12.3 4.7-17 0L256 295.6l-62.2 62.2c-4.7 4.7-12.3 4.7-17 0l-22.6-22.6c-4.7-4.7-4.7-12.3 0-17l62.2-62.2-62.2-62.2c-4.7-4.7-4.7-12.3 0-17l22.6-22.6c4.7-4.7 12.3-4.7 17 0l62.2 62.2 62.2-62.2c4.7-4.7 12.3-4.7 17 0l22.6 22.6c4.7 4.7 4.7 12.3 0 17z" class=""></path></svg>
									<div class="layout-data">
										<div class="transition-data-overlay">
											<input type="hidden" value='{"css":{"main":"","in":{"from":"","to":"","time":""},"out":{"from":"","to":"","time":""}},"html":""}'>
										</div>
										<div class="transition-data-page">
											<input type="hidden" value='{"in":"","out":""}'>
										</div>
									</div>
								</div>
							</div>

							<?php

							$saved_overlay = get_option( 'wppatr-overlay', '' );
							$saved_page    = get_option( 'wppatr-page', '' );

							$saved_overlay_json = json_decode( $saved_overlay );
							$saved_page_json    = json_decode( $saved_page );

							foreach ( WPPATR_Library::get_transitions() as $transition ) {
								$page_json    = json_decode( $transition['page']['code'] );
								$overlay_json = json_decode( $transition['overlay']['code'] );

								$custom_class = self::compare_pages( $saved_page_json, $page_json ) && self::compare_overlays( $saved_overlay_json, $overlay_json ) ? ' active' : '';
								?>

									<div class="layout-container transition-layout-container<?php echo esc_html( $custom_class ); ?>" radio-unit>
										<div class="layout-ratio">
											<div class="layout-item">
												<video src="<?php echo esc_url( WPPATR_PLUGIN_URL ) . 'admin/assets/transition-render/' . esc_attr( $transition['renderer'] ); ?>" loop muted autoplay type="video/mp4"></video>
												<h3 class="layout-title"><?php echo esc_html( $transition['title'] ); ?></h3>
											</div>
											<div class="layout-data">
												<div class="transition-data-overlay">
													<input type="hidden" value="<?php echo esc_attr( addslashes( $transition['overlay']['code'] ) ); ?>">
												</div>
												<div class="transition-data-page">
													<input type="hidden" value="<?php echo esc_attr( addslashes( $transition['page']['code'] ) ); ?>">
												</div>
											</div>
										</div>
									</div>

									<?php
							}

							?>

							<div radio-data>
								<input type="hidden" name="wppatr-overlay" value="<?php echo esc_attr( $saved_overlay ); ?>">
								<input type="hidden" name="wppatr-page" value="<?php echo esc_attr( $saved_page ); ?>">								
							</div>

						</div>

						<div style="clear:both;"></div>

						<h2><?php esc_html_e( 'Select a loader :', 'wp-page-transition-free' ); ?></h2>

						<div class="transition-library" radio-selector>

							<div class="layout-container loader-layout-container" radio-unit>
								<div class="layout-ratio">
									<div class="layout-item">
										<svg class="none-option-cross" aria-hidden="true" focusable="false" data-prefix="far" data-icon="times-circle" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-inline--fa fa-times-circle fa-w-16 fa-5x"><path fill="currentColor" d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm0 448c-110.5 0-200-89.5-200-200S145.5 56 256 56s200 89.5 200 200-89.5 200-200 200zm101.8-262.2L295.6 256l62.2 62.2c4.7 4.7 4.7 12.3 0 17l-22.6 22.6c-4.7 4.7-12.3 4.7-17 0L256 295.6l-62.2 62.2c-4.7 4.7-12.3 4.7-17 0l-22.6-22.6c-4.7-4.7-4.7-12.3 0-17l62.2-62.2-62.2-62.2c-4.7-4.7-4.7-12.3 0-17l22.6-22.6c4.7-4.7 12.3-4.7 17 0l62.2 62.2 62.2-62.2c4.7-4.7 12.3-4.7 17 0l22.6 22.6c4.7 4.7 4.7 12.3 0 17z" class=""></path></svg>
										<input type="hidden" value='{"css":{"anim":"","time":""},"html":""}'>
									</div>
								</div>
							</div>

							<?php

							$saved_loader      = get_option( 'wppatr-loader', '' );
							$saved_loader_json = json_decode( $saved_loader );

							foreach ( WPPATR_Library::get_loaders() as $loader ) {
								$loader_object = json_decode( $loader['code'] );
								$css           = $loader_object->css->time . $loader_object->css->anim;
								$html          = $loader_object->html;

								$custom_class = null !== $saved_loader_json && ( $saved_loader_json->css->time . $saved_loader_json->css->anim ) === $css ? ' active' : '';
								?>

									<div class="layout-container loader-layout-container<?php echo esc_html( $custom_class ); ?>" radio-unit>
										<div class="layout-ratio">
											<div class="layout-item">
												<style type="text/css">
												<?php echo esc_html( $css ); ?>
												</style>
												<?php echo wp_kses_post( $html ); ?>
												<input type="hidden" value="<?php echo esc_attr( addslashes( $loader['code'] ) ); ?>">
											</div>
										</div>
									</div>

									<?php
							}

							?>

							<div radio-data>
								<input type="hidden" name="wppatr-loader" value="<?php echo esc_attr( $saved_loader ); ?>">	
							</div>
							

						</div>

						<div style="clear:both;"></div>

					</div>

					<div class="settings-tab" tab-type='tab' tab-family='transition-settings' tab-link='settings'>

						<h2><?php esc_html_e( 'Options', 'wp-page-transition-free' ); ?></h2>

						<div class="transition-options">

							<div class="transition-option">
								<div class="option-label">
									<?php esc_html_e( 'Activate plugin (display created by Fluent Interface during transitions)', 'wp-page-transition-free' ); ?>
								</div>
								<div class="switch-option-container">
									<?php self::switcher( get_option( 'wppatr-active-transitions', '1' ), 'wppatr-active-transitions' ); ?>                                        
								</div>
							</div>

							<div class="transition-option">
								<div class="option-label">
									<?php esc_html_e( 'Selector of your page container', 'wp-page-transition-free' ); ?>
								</div>
								<div class="page-selector-container">
									<input type="text" name="wppatr-page-selector" value="<?php echo esc_attr( get_option( 'wppatr-page-selector', '' ) ); ?>" spellcheck="false" autocomplete="off">
									<div id="calculate-page-selector" class="calculate-page-selector">
										<span><?php esc_html_e( 'Calculate', 'wp-page-transition-free' ); ?></span>
									</div>                                        
								</div>
							</div>

							<div class="transition-option">
								<div class="option-label">
									<?php esc_html_e( 'Links able to trigger transitions', 'wp-page-transition-free' ); ?>
								</div>
								<input type="text" name="wppatr-links" value="<?php echo esc_attr( get_option( 'wppatr-links', 'a' ) ); ?>" spellcheck="false" autocomplete="off">
							</div>

							<div class="transition-option">
								<div class="option-label">
									<?php esc_html_e( 'Links that you want remove of your selection', 'wp-page-transition-free' ); ?>
								</div>
								<input type="text" name="wppatr-not-links" value="<?php echo esc_attr( get_option( 'wppatr-not-links', '' ) ); ?>" spellcheck="false" autocomplete="off">
							</div>

							<div class="transition-option">
								<div class="option-label">
									<?php esc_html_e( 'Remove scroll bar during transitions', 'wp-page-transition-free' ); ?>
								</div>
								<div class="switch-option-container">
									<?php self::switcher( get_option( 'wppatr-remove-scroll-bar', '' ), 'wppatr-remove-scroll-bar' ); ?>                                        
								</div>
							</div>
						</div>

					</div>

				</div>

				<div class="main-save-container">
					<?php wp_nonce_field( 'wppatrnonce_save_options', 'wppatr-nonce' ); ?>
					<button name="save" class="save-transition-form-btn"><?php esc_html_e( 'Save Changes', 'wp-page-transition-free' ); ?></button>
					<span class="save-comment-method">
						<p><?php esc_html_e( 'or Ctrl + S', 'wp-page-transition-free' ); ?></p>
					</span>
				</div>

				<div id="basic-builder-context-menu">
					<ul>
						<li class="context-option"><?php esc_html_e( 'Remove', 'wp-page-transition-free' ); ?></li>
					</ul>
				</div>

			</div>
			<?php
		}

		/**
		 * Display a comparative table
		 *
		 * @return void
		 */
		public static function comparative_table() {
			?>

				<div class="table" id="comparative-options">

					<div class="table-labels">
						<div class="table-label table-cell"><?php esc_html_e( 'Define links able to trigger transitions', 'wp-page-transition-free' ); ?></div>
						<div class="table-label table-cell"><?php esc_html_e( 'Remove scroll bar', 'wp-page-transition-free' ); ?></div>
						<div class="table-label table-cell"><?php esc_html_e( 'Number of premade loaders', 'wp-page-transition-free' ); ?></div>
						<div class="table-label table-cell"><?php esc_html_e( 'Number of transition templates', 'wp-page-transition-free' ); ?></div>
						<div class="table-label table-cell"><?php esc_html_e( 'Load the next page during the transition', 'wp-page-transition-free' ); ?></div>
						<div class="table-label table-cell"><?php esc_html_e( 'Import loaders with HTML & CSS code', 'wp-page-transition-free' ); ?></div>
						<div class="table-label table-cell"><?php esc_html_e( 'Use a different transition to welcome your users', 'wp-page-transition-free' ); ?></div>
						<div class="table-label table-cell"><?php esc_html_e( 'Create your own loader from scratch', 'wp-page-transition-free' ); ?></div>
						<div class="table-label table-cell"><?php esc_html_e( 'Create custom transitions with the Visual Builder', 'wp-page-transition-free' ); ?></div>
					</div>
					<div class="table-column free-plugin">
						<div class="column-title">
							<div><?php esc_html_e( 'Free', 'wp-page-transition-free' ); ?></div>
						</div>
						<div class="answer-label table-cell"><?php self::display_table_icon( 'valid' ); ?></div>
						<div class="answer-label table-cell"><?php self::display_table_icon( 'valid' ); ?></div>
						<div class="answer-label table-cell number-cell">1</div>
						<div class="answer-label table-cell number-cell">2</div>
						<div class="answer-label table-cell"><?php self::display_table_icon( 'error' ); ?></div>
						<div class="answer-label table-cell"><?php self::display_table_icon( 'error' ); ?></div>
						<div class="answer-label table-cell"><?php self::display_table_icon( 'error' ); ?></div>
						<div class="answer-label table-cell"><?php self::display_table_icon( 'error' ); ?></div>
						<div class="answer-label table-cell"><?php self::display_table_icon( 'error' ); ?></div>
					</div>
					<div class="table-column premium-plugin">
						<div class="column-title">
							<div><?php esc_html_e( 'Premium', 'wp-page-transition-free' ); ?></div>
							<a href="https://fluent-interface.com/" target="_blank"><?php esc_html_e( 'Get it now !', 'wp-page-transition-free' ); ?></a>
						</div>
						<div class="answer-label table-cell"><?php self::display_table_icon( 'valid' ); ?></div>
						<div class="answer-label table-cell"><?php self::display_table_icon( 'valid' ); ?></div>
						<div class="answer-label table-cell number-cell">27</div>
						<div class="answer-label table-cell number-cell">24</div>
						<div class="answer-label table-cell"><?php self::display_table_icon( 'valid' ); ?></div>
						<div class="answer-label table-cell"><?php self::display_table_icon( 'valid' ); ?></div>
						<div class="answer-label table-cell"><?php self::display_table_icon( 'valid' ); ?></div>
						<div class="answer-label table-cell"><?php self::display_table_icon( 'valid' ); ?></div>
						<div class="answer-label table-cell"><?php self::display_table_icon( 'valid' ); ?></div>
					</div>
					<div class="comparative-end"></div>

				</div>

			<?php
		}

		/**
		 * Display an icon
		 *
		 * @param string $icon_name Name of the icon.
		 * @return void
		 */
		public static function display_table_icon( $icon_name ) {
			?>
				<img src="<?php echo esc_url( WPPATR_PLUGIN_URL ); ?>admin/assets/<?php echo esc_html( $icon_name ); ?>.svg">
			<?php
		}

		/**
		 * Compare two pages
		 *
		 * @param object $first_page First page object.
		 * @param object $second_page Second page object.
		 * @return boolean True if pages are the same.
		 */
		public static function compare_pages( $first_page, $second_page ) {
			$both_set = null !== $first_page && null !== $second_page;
			if ( ! $both_set ) {
				return false;
			}

			$same_css = $first_page->in === $second_page->in && $first_page->out === $second_page->out;

			return $same_css;
		}

		/**
		 * Compare two overlay
		 *
		 * @param object $first_overlay First overlay object.
		 * @param object $second_overlay Second overlay object.
		 * @return boolean True if overlays are the same.
		 */
		public static function compare_overlays( $first_overlay, $second_overlay ) {
			$both_set = null !== $first_overlay && null !== $second_overlay;
			if ( ! $both_set ) {
				return false;
			}

			$same_css_main = $first_overlay->css->main === $second_overlay->css->main;
			$same_css_in   = $first_overlay->css->in->from === $second_overlay->css->in->from && $first_overlay->css->in->to === $second_overlay->css->in->to && $first_overlay->css->in->time === $second_overlay->css->in->time;
			$same_css_out  = $first_overlay->css->out->from === $second_overlay->css->out->from && $first_overlay->css->out->to === $second_overlay->css->out->to && $first_overlay->css->out->time === $second_overlay->css->out->time;

			return $same_css_main && $same_css_in && $same_css_out;
		}

		/**
		 * Display a mere switcher
		 *
		 * @param string         $value Default value of the switcher.
		 * @param boolean|string $name Name of the switcher.
		 * @return void
		 */
		public static function switcher( $value, $name ) {
			?>

			<label class="switch">
				<input type="hidden" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>">
				<div>
					<span></span>
				</div>
			</label>

			<?php
		}

	}
}
