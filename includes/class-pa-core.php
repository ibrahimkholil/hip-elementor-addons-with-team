<?php
/**
 * PA Core.
 */

namespace HipAddons\Includes;

if ( ! class_exists( 'PA_Core' ) ) {

	/**
	 * Intialize and Sets up the plugin
	 */
	class PA_Core {

		/**
		 * Member Variable
		 *
		 * @var instance
		 */
		private static $instance = null;

		/**
		 * Sets up needed actions/filters for the plug-in to initialize.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @return void
		 */
		public function __construct() {

			// Autoloader.
			spl_autoload_register( array( $this, 'autoload' ) );

			// Run plugin and require the necessary files.
			add_action( 'plugins_loaded', array( $this, 'premium_addons_elementor_setup' ) );

			// Load Elementor files.
			add_action( 'elementor/init', array( $this, 'elementor_init' ) );

			add_action( 'elementor/elements/categories_registered', array( $this, 'register_widgets_category' ), 9 );

		}

		/**
		 * AutoLoad
		 *
		 * @since 3.20.9
		 * @param string $class class.
		 */
		public function autoload( $class ) {

			if ( 0 !== strpos( $class, 'HipAddons' ) ) {
				return;
			}

			$class_to_load = $class;

			if ( ! class_exists( $class_to_load ) ) {
				$filename = strtolower(
					preg_replace(
						array( '/^HipAddons\\\/', '/([a-z])([A-Z])/', '/_/', '/\\\/' ),
						array( '', '$1-$2', '-', DIRECTORY_SEPARATOR ),
						$class_to_load
					)
				);

				$filename = HIP_ADDONS_PATH . $filename . '.php';

				if ( is_readable( $filename ) ) {
					include $filename;
				}
			}
		}

		/**
		 * Installs translation text domain and checks if Elementor is installed
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @return void
		 */
		public function premium_addons_elementor_setup() {

			// Load plugin textdomain.
			$this->load_domain();

			// load plugin necessary files.
			$this->load_files();


		}


		/**
		 * Require initial necessary files
		 *
		 * @since 2.6.8
		 * @access public
		 *
		 * @return void
		 */
		public function load_files() {

			\HipAddons\Admin\Includes\Admin_Helper::get_instance();

		}

		/**
		 * Load plugin translated strings using text domain
		 *
		 * @since 2.6.8
		 * @access public
		 *
		 * @return void
		 */
		public function load_domain() {

			load_plugin_textdomain( 'premium-addons-for-elementor' );

		}

		/**
		 * Elementor Init
		 *
		 * Initialize plugin after Elementor is run.
		 *
		 * @since 2.6.8
		 * @access public
		 *
		 * @return void
		 */
		public function elementor_init() {

			Addons_Integration::get_instance();

			if ( version_compare( ELEMENTOR_VERSION, '2.0.0' ) < 0 ) {

				\Elementor\Plugin::instance()->elements_manager->add_category(
					'hip-addons',
					array(
						'title' => 'HIP Addons',
					),
					1
				);
			}

		}

		/**
		 * Register Widgets Category
		 *
		 * Register a new category for Premium Addons widgets
		 *
		 * @since 4.0.0
		 * @access public
		 *
		 * @param object $elements_manager elements manager.
		 */
		public function register_widgets_category( $elements_manager ) {

			$elements_manager->add_category(
				'hip-addons',
				array(
					'title' => 'HIP Addons',
				),
				1
			);

		}


		/**
		 * Creates and returns an instance of the class
		 *
		 * @since 2.6.8
		 * @access public
		 *
		 * @return object
		 */
		public static function get_instance() {

			if ( ! isset( self::$instance ) ) {

				self::$instance = new self();

			}

			return self::$instance;
		}

	}
}

if ( ! function_exists( 'pa_core' ) ) {

	/**
	 * Returns an instance of the plugin class.
	 *
	 * @since  1.0.0
	 * @return object
	 */
	function pa_core() {
		return PA_Core::get_instance();
	}
}

pa_core();
