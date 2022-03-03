<?php

namespace HipAddons\Includes;

use HipAddons\Includes\Helper_Functions;
use HipAddons\Admin\Includes\Admin_Helper;
use HipAddons\Widgets\Premium_Maps;

// Elementor Classes.
use Elementor\Core\Settings\Manager as SettingsManager;

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

/**
 * Class Addons_Integration.
 */
class Addons_Integration {

	/**
	 * Class instance
	 *
	 * @var instance
	 */
	private static $instance = null;

	/**
	 * Modules
	 *
	 * @var modules
	 */
	private static $modules = null;

	/**
	 * Maps Keys
	 *
	 * @var maps
	 */
	private static $maps = null;

	/**
	 * Initialize integration hooks
	 *
	 * @return void
	 */
	public function __construct() {

		self::$modules = Admin_Helper::get_default_elements();

		self::$maps = Admin_Helper::get_integrations_settings();


		add_action( 'elementor/editor/before_enqueue_styles', array( $this, 'enqueue_editor_styles' ) );

		add_action( 'elementor/widgets/widgets_registered', array( $this, 'widgets_area' ) );

		add_action( 'elementor/editor/before_enqueue_scripts', array( $this, 'enqueue_editor_scripts' ) );

		add_action( 'elementor/preview/enqueue_styles', array( $this, 'enqueue_preview_styles' ) );

		add_action( 'elementor/frontend/after_register_styles', array( $this, 'register_frontend_styles' ) );

		add_action( 'elementor/frontend/after_register_scripts', array( $this, 'register_frontend_scripts' ) );

	}


	/**
	 * Loads plugin icons font
	 *
	 * @since 1.0.0
	 * @access public
	 * @return void
	 */
	public function enqueue_editor_styles() {

		$theme = SettingsManager::get_settings_managers( 'editorPreferences' )->get_model()->get_settings( 'ui_theme' );

		// Enqueue required style for Elementor dark UI Theme.
		if ( 'dark' === $theme ) {

			wp_add_inline_style(
				'pa-editor',
				'.elementor-panel .elementor-control-section_pa_docs .elementor-panel-heading-title.elementor-panel-heading-title,
				.elementor-control-raw-html.editor-pa-doc a {
					color: #e0e1e3 !important;
				}
				[class^="pa-"]::after,
				[class*=" pa-"]::after {
					color: #fff;
				}'
			);

		}

		$badge_text = 'Map';

		$dynamic_css = sprintf( '[class^="pa-"]::after, [class*=" pa-"]::after { content: "%s"; }', $badge_text );

		wp_add_inline_style( 'pa-editor', $dynamic_css );

	}

	/**
	 * Register Frontend CSS files
	 *
	 * @since 2.9.0
	 * @access public
	 */
	public function register_frontend_styles() {

		$dir    = 'min-css';
		$suffix = '.min';


		wp_register_style(
			'premium-addons',
			HIP_ADDONS_URL . 'assets/frontend/' . $dir . '/premium-addons' . $suffix . '.css',
			array(),
			HIP_ADDONS_VERSION,
			'all'
		);

	}

	/**
	 * Enqueue Preview CSS files
	 *
	 * @since 2.9.0
	 * @access public
	 */
	public function enqueue_preview_styles() {

		wp_enqueue_style( 'premium-addons' );

	}

	/**
	 * Load widgets require function
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function widgets_area() {
		$this->widgets_register();
	}

	/**
	 * Requires widgets files
	 *
	 * @since 1.0.0
	 * @access private
	 */
	private function widgets_register() {

		$enabled_elements = self::$modules;

		foreach ( glob( HIP_ADDONS_PATH . 'widgets/*.php' ) as $file ) {

			$slug = basename( $file, '.php' );

			$enabled = isset( $enabled_elements[ $slug ] ) ? $enabled_elements[ $slug ] : '';

			if ( filter_var( $enabled, FILTER_VALIDATE_BOOLEAN ) || ! $enabled_elements ) {
				$this->register_addon( $file );
			}
		}

	}

	/**
	 * Registers required JS files
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function register_frontend_scripts() {

		$maps_settings = self::$maps;

		$locale = isset( $maps_settings['hip-map-locale'] ) ? $maps_settings['hip-map-locale'] : 'en';

		wp_register_script(
			'pa-maps',
			HIP_ADDONS_URL . 'assets/frontend/min-js/premium-maps.min.js',
			array( 'jquery'),
			HIP_ADDONS_VERSION,
			true
		);

		if ( $maps_settings['hip-map-cluster'] ) {
			wp_register_script(
				'google-maps-cluster',
				'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js',
				array(),
				HIP_ADDONS_VERSION,
				false
			);
		}
	}

	/**
	 * Enqueue editor scripts
	 *
	 * @since 3.2.5
	 * @access public
	 */
	public function enqueue_editor_scripts() {

		$map_enabled = isset( self::$modules['premium-maps'] ) ? self::$modules['premium-maps'] : 1;

		if ( $map_enabled ) {

			wp_enqueue_script(
				'pa-maps-finder',
				HIP_ADDONS_URL . 'assets/editor/js/pa-maps-finder.js',
				array( 'jquery' ),
				HIP_ADDONS_VERSION,
				true
			);

			wp_enqueue_script(
				'gen_loc',
				HIP_ADDONS_URL . 'assets/editor/js/editor.js',
				array( 'jquery' ),
				HIP_ADDONS_VERSION,
				true
			);

			$locations = Premium_Maps::getLocations();
			$locations_data = [];

			foreach($locations as $location) {
				array_push($locations_data, $location);
			}

			wp_localize_script('gen_loc', 'LOCATIONS', $locations_data);

		}

	}


	public function googleMapsJS()
	{
	   $map_enabled = isset( self::$modules['premium-maps'] ) ? self::$modules['premium-maps'] : 1;

	   if ( $map_enabled ) {

	   	$premium_maps_api = self::$maps['hip-map-api'];

	   	$locale = isset( self::$maps['hip-map-locale'] ) ? self::$maps['hip-map-locale'] : 'en';

	   	$disable_api = self::$maps['hip-map-disable-api'];

	   	if ( $disable_api && '1' !== $premium_maps_api ) {
				$api = sprintf( 'https://maps.googleapis.com/maps/api/js?key=%1$s&language=%2$s', $premium_maps_api, $locale );
         ?>
			<script>
             // Create the script tag, set the appropriate attributes
				 var script = document.createElement('script');
				 script.src = "<?php echo $api ?>";
				 script.async = true;
				 // Append the 'script' element to 'head'
				 if (document.querySelector('script[src^="https://maps.googleapis.com/maps/api/js"]') === null) {
					document.head.appendChild(script);
				}
			</script>

         <?php
	   	}
	   }
   }

	/**
	 *
	 * Register addon by file name.
	 *
	 * @access public
	 *
	 * @param  string $file            File name.
	 *
	 * @return void
	 */
	public function register_addon( $file ) {

		$widget_manager = \Elementor\Plugin::instance()->widgets_manager;

		$base  = basename( str_replace( '.php', '', $file ) );
		$class = ucwords( str_replace( '-', ' ', $base ) );
		$class = str_replace( ' ', '_', $class );
		$class = sprintf( 'HipAddons\Widgets\%s', $class );

		if ( class_exists( $class ) ) {
			$widget_manager->register_widget_type( new $class() );
		}
	}

	/**
	 *
	 * Creates and returns an instance of the class
	 *
	 * @since 1.0.0
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
