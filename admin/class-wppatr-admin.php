<?php
/**
 * Admin main file
 *
 * @package WPPageTransitionFree
 * @since   5.8.1
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WPPATR_Admin' ) ) {

	/**
	 * Admin setup
	 *
	 * @version 5.4.1
	 */
	class WPPATR_Admin {

		const GENERALGROUP = 'general-options';

		/**
		 * Register the admin option page
		 *
		 * @version 5.4.1
		 */
		public static function register() {
			add_filter( 'upload_mimes', array( self::class, 'add_mime_types' ) );
			add_action( 'admin_menu', array( self::class, 'add_menu' ) );
			add_action( 'admin_init', array( self::class, 'register_settings' ) );
			add_action( 'wp_head', array( self::class, 'add_ie_meta' ) );
		}

		/**
		 * Add mime types
		 *
		 * @version 5.4.1
		 * @param array $mimes list of mimes.
		 * @return array
		 */
		public static function add_mime_types( $mimes ) {
			$mimes['svg'] = 'image/svg+xml';
			return $mimes;
		}

		/**
		 * Display meta IE compatibility
		 *
		 * @version 5.4.1
		 * @return void
		 */
		public static function add_ie_meta() {
			echo '<meta http-equiv="X-UA-Compatible" content="IE=edge">';
		}

		/**
		 * Add option page to the menu
		 *
		 * @version 5.4.1
		 * @return void
		 */
		public static function add_menu() {
			$icon            = 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4NCjxzdmcgdmVyc2lvbj0iMS4xIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB4PSIwcHgiIHk9IjBweCINCgkgdmlld0JveD0iMCAwIDExOC4zIDExNS44IiBzdHlsZT0iZW5hYmxlLWJhY2tncm91bmQ6bmV3IDAgMCAxMTguMyAxMTUuODsiIGZpbGw9IiNmZmYiIHhtbDpzcGFjZT0icHJlc2VydmUiPg0KPHBhdGggZmlsbD0iI2ZmZiIgY2xhc3M9InN0MCIgZD0iTTExOC4zLDU3LjlMNDkuOSwwTDI3LjEsMTguOGMwLDAsMTAsOS40LDEwLDM5YzAsMjkuNi0xMC4yLDM5LjEtMTAuMiwzOS4xbDIzLDE4LjlMMTE4LjMsNTcuOXogTTU0LjksNTguMQ0KCWMwLTIzLjMtNS0zMy4yLTUtMzMuMmwzOSwzM2wtMzguOSwzM0M0OS45LDkwLjksNTQuOSw4MS40LDU0LjksNTguMXoiLz4NCjxwYXRoIGZpbGw9IiNmZmYiIGNsYXNzPSJzdDAiIGQ9Ik0xNC42LDg2LjNMMCw3NGMwLDAsMy45LTYuOCwzLjktMTYuMkMzLjksNDguMywwLDQyLDAsNDJsMTQuNi0xMi41YzAsMCw2LjMsNy40LDYuMywyOC42UzE0LjYsODYuMywxNC42LDg2LjN6DQoJIi8+DQo8L3N2Zz4NCg==';
			$admin_menu_page = add_menu_page( 'WP Page Transition', 'Page transition', 'manage_options', 'wp_page_transition_free', array( self::class, 'render' ), $icon );

			// Load the script if option are loading.
			add_action( 'load-' . $admin_menu_page, array( self::class, 'load_admin_js' ) );
		}

		/**
		 * Load admin JS file
		 *
		 * This function is only called when our plugin's page loads.
		 * Unfortunately we can't just enqueue our scripts here - it's too early. So register against the proper action hook to do it.
		 *
		 * @version 5.4.1
		 * @return void
		 */
		public static function load_admin_js() {
			add_action( 'admin_enqueue_scripts', array( self::class, 'register_scripts' ) );
		}

		/**
		 * Enqueue admin JS file
		 *
		 * @version 5.4.1
		 * @return void
		 */
		public static function register_scripts() {

			if ( self::is_admin_page_transition_page() ) {
				wp_enqueue_style( 'inter', 'https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap', '', '1.0.0' );
				wp_enqueue_style( 'option-style', esc_url( WPPATR_PLUGIN_URL ) . 'admin/css/wppatr-admin.css', '', '1.0.0' );

				// functionjs.
				wp_register_script( 'functions', esc_url( WPPATR_PLUGIN_URL ) . 'admin/js/functions.js', array(), '1.0.0', true );
				// Durations.
				wp_register_script( 'transition-duration', esc_url( WPPATR_PLUGIN_URL ) . 'js/transition-duration.js', array(), '1.0.0', true );

				wp_register_script( 'page-selector-setup', esc_url( WPPATR_PLUGIN_URL ) . 'admin/js/page-selector-setup.js', array(), '1.0.0', true );

				$dependencies = array(
					'jquery',
					'functions', // rm-gulp.
					'transition-duration', // rm-gulp.
					'page-selector-setup', // rm-gulp.
				);

				wp_enqueue_media();
				// Basic Builder.
				wp_register_script( 'basic-builder', esc_url( WPPATR_PLUGIN_URL ) . 'admin/js/basic-builder.js', $dependencies, '1.0.0', true );
				WPPATR_JS_Object::send_admin( 'basic-builder' );

				wp_enqueue_script( 'basic-builder' );

			}
		}

		/**
		 * Check if the current page is the admin page option
		 *
		 * @return boolean True if it is the case
		 */
		public static function is_admin_page_transition_page() {
			return key_exists( 'page', $_GET ) && 'wp_page_transition_free' === $_GET['page'];
		}

		/**
		 * Register options
		 *
		 * @version 5.4.1
		 * @return void
		 */
		public static function register_settings() {

			register_setting( self::GENERALGROUP, 'wppatr-links', array( 'default' => 'a' ) );
			register_setting( self::GENERALGROUP, 'wppatr-not-links', array( 'default' => '' ) );
			register_setting( self::GENERALGROUP, 'wppatr-page-selector', array( 'default' => 'body' ) );
			register_setting( self::GENERALGROUP, 'wppatr-remove-scroll-bar', array( 'default' => '1' ) );
			register_setting( self::GENERALGROUP, 'wppatr-page', array( 'default' => '' ) );
			register_setting( self::GENERALGROUP, 'wppatr-overlay', array( 'default' => '' ) );
			register_setting( self::GENERALGROUP, 'wppatr-loader', array( 'default' => '' ) );
			register_setting( self::GENERALGROUP, 'wppatr-active-transitions', array( 'default' => '1' ) );

		}

		/**
		 * Display the option page DOM
		 *
		 * @version 5.4.1
		 * @return void
		 */
		public static function render() {
			?>

			<noscript>
				<style type="text/css">
					#page-transition-builder{
						display:none !important;
						pointer-events: none !important;
					}
					.wppatr-noscript-notice{
						border-radius: 3px;
						background-color: #2f2f2f;
						padding: 25px;
						font-family: 'Inter', 'Open sans', sans-serif;
						color: white;
						text-align: center;
						margin: 30px 30px 0 20px;
					}
				</style>
				<div class="wppatr-noscript-notice">
					<p><?php esc_html_e( 'You must activate JavaScript to use the page transition Builder.', 'wp-page-transition-free' ); ?></p>
				</div>
			</noscript>

			<div id="page-transition-builder">
				<div id="animation-backup" class="success-animation">
					<img src="<?php echo esc_url( WPPATR_PLUGIN_URL ) . 'admin/assets/ajax-loader.gif'; ?>" alt="loading" id="loading-image">
				</div>

				<a href="https://fluent-interface.com/" class="premium-link" target="_blank">Get premium version</a>

				<div class="top-save-container">
					<button class="save-transition-form-btn"><?php esc_html_e( 'Save Changes', 'wp-page-transition-free' ); ?></button>
				</div>

				<form id="form-builder" method="post" enctype="multipart/form-data">
					<?php WPPATR_Basic_Builder::create(); ?>
				</form>

			</div>

			<?php
		}
	}
}

WPPATR_Admin::register();
