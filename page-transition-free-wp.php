<?php
/**
 * Plugin Name: Page Transition Free for WP
 *
 * Description: Free version of the WP Page Transition plugin. It will allow you to create beautiful fluid and smooth transitions between your website's pages !
 * Version:     1.0.1
 * Author:      Fluent Interface
 * Author URI:  https://fluent-interface.com
 * Text Domain: wp-page-transition
 * Domain Path: /languages
 * Requires at least: 5.4
 * Requires PHP: 7.0
 * License:     GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.en.html
 * Tags: Animation, Page transition, Loader, Fluid, Speed, Transition, Ajax, Fluent
 *
 * @package  WPPageTransitionFree
 */

defined( 'ABSPATH' ) || exit;

/** Path to the plugin
*
* @since 5.8.1
* @var string
*/
defined( 'WPPATR_PATH' ) || define( 'WPPATR_PATH', esc_url( plugin_dir_path( __FILE__ ) ) );

/** Url leading to the plugin : 'http://domain.com/wp-content/plugins/wp-page-transition/'
*
* @since 5.8.1
* @var string
*/
defined( 'WPPATR_PLUGIN_URL' ) || define( 'WPPATR_PLUGIN_URL', esc_url( plugin_dir_url( __FILE__ ) ) );

if ( ! class_exists( 'WPPageTransitionFree' ) ) {

	/**
	 * Run the WP Page Transition plugin
	 *
	 * @version 5.4.1
	 * @todo separate the page selector calculator in an other distinct object
	 */
	class WPPageTransitionFree {

		/**
		 * Run the plugin
		 *
		 * @version 5.4.1
		 * @return void
		 */
		public static function run() {

			self::includes();

			add_action( 'init', array( self::class, 'languages' ) );

			if ( ! self::divi_page_builder_is_activ() ) {
				add_action( 'wp_enqueue_scripts', array( self::class, 'assets' ) );
			}

			register_activation_hook( __FILE__, array( self::class, 'activation' ) );
			register_deactivation_hook( __FILE__, array( self::class, 'deactivation' ) );
			add_action( 'switch_theme', array( self::class, 'change_theme' ) );

			$plugin_basename = plugin_basename( __FILE__ );
			add_filter( "plugin_action_links_$plugin_basename", array( self::class, 'add_settings_link' ) );

		}

		/**
		 * Include files
		 *
		 * @version 5.4.1
		 * @return void
		 */
		public static function includes() {

			include esc_url( WPPATR_PATH ) . 'includes/class-wppatr-library.php';
			include esc_url( WPPATR_PATH ) . 'includes/class-wppatr-js-object.php';
			include esc_url( WPPATR_PATH ) . 'includes/class-wppatr-autoprefixer.php';
			include esc_url( WPPATR_PATH ) . 'includes/class-wppatr-dom.php';
			include esc_url( WPPATR_PATH ) . 'admin/includes/class-wppatr-ajax-call.php';
			include esc_url( WPPATR_PATH ) . 'admin/includes/class-wppatr-basic-builder.php';
			include esc_url( WPPATR_PATH ) . 'admin/class-wppatr-admin.php';
		}

		/**
		 * Enqueue assets on front pages
		 *
		 * @version 5.4.1
		 * @return void
		 */
		public static function assets() {

			wp_enqueue_style( 'transition', esc_url( WPPATR_PLUGIN_URL ) . 'css/transition.css', '', '1.0.0' );

			wp_register_script( 'transition-duration', esc_url( WPPATR_PLUGIN_URL ) . 'js/transition-duration.js', '', '1.0.0', true );
			wp_register_script( 'page-transition', esc_url( WPPATR_PLUGIN_URL ) . 'js/page-transition.js', '', '1.0.0', true );

			wp_register_script( 'wppatr', esc_url( WPPATR_PLUGIN_URL ) . 'js/wppatr.js', array( 'transition-duration', 'page-transition' ), '1.0.0', true );
			WPPATR_JS_Object::send_frontend( 'wppatr' );
			wp_enqueue_script( 'wppatr' );

		}

		/**
		 * Initialize plugin on activation
		 *
		 * @version 5.4.1
		 * @return void
		 */
		public static function activation() {

			self::define_page_selector();
			flush_rewrite_rules();
		}

		/**
		 * Delete data saved in the DB
		 *
		 * @version 5.4.1
		 * @return void
		 */
		public static function deactivation() {

			flush_rewrite_rules();
		}

		/**
		 * Change theme setup
		 *
		 * @version 5.4.1
		 * @return void
		 */
		public static function change_theme() {

			self::define_page_selector();
		}

		/**
		 * Load translations
		 *
		 * @version 5.4.1
		 * @return void
		 */
		public static function languages() {

			load_plugin_textdomain( 'wp-page-transition-free', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		}

		/**
		 * Add link to the plugin page on the plugin dashboard
		 *
		 * @version 5.4.1
		 * @param array $links html of the current links.
		 * @return array
		 */
		public static function add_settings_link( $links ) {

			$settings_link = '<a href="https://fluent-interface.com/" id="wppatr-premium-link" target="_blank">' . __( 'Premium', 'wp-page-transition-free' ) . '</a><style>#wppatr-premium-link{color: #f8669e;text-transform: none;font-weight:700;transition: all .3s ease-in-out;position: relative;}#wppatr-premium-link:after{content: "";position: absolute;left: 0;bottom: -6px;height: 3px;width: 0;transition: all .3s cubic-bezier(0.16, 0.98, 0.59, 1.15);background-color: #f8669e;}#wppatr-premium-link:hover{color: #f5327d;}#wppatr-premium-link:hover:after{width: 100%;background-color:color: #f5327d;}</style>';
			array_unshift( $links, $settings_link );

			$settings_link = '<a href="options-general.php?page=wp_page_transition_free">Settings</a>';
			array_unshift( $links, $settings_link );
			return $links;
		}

		/**
		 * Check if the Divi page builder is activ
		 *
		 * @version 5.4.1
		 * @return bool true if activated
		 */
		public static function divi_page_builder_is_activ() {

			return wp_get_theme()->name !== 'Divi' && key_exists( 'page_id', $_GET ) && key_exists( 'et_fb', $_GET ) && key_exists( 'PageSpeed', $_GET );
		}

		/**
		 * Define the value of the page selector option
		 *
		 * @return void
		 */
		public static function define_page_selector() {

			$page_selector = 'body';
			$theme_names   = array( wp_get_theme()->name, wp_get_theme()->parent_theme );

			foreach ( $theme_names as $theme_name ) {
				if ( 'body' === $page_selector ) {
					switch ( $theme_name ) {
						case 'Divi':
							$page_selector = '#page-container';
							break;
						case 'Astra':
							$page_selector = '#page';
							break;
						case 'Ultra':
							$page_selector = '#pagewrap';
							break;
						case 'Avada':
							$page_selector = '#wrapper';
							break;
						case 'Twenty Twenty-One':
							$page_selector = '#page';
							break;
					}
				} else {
					break;
				}
			}

			update_option( 'wppatr-page-selector', $page_selector );

		}

	}

	WPPageTransitionFree::run();

}
