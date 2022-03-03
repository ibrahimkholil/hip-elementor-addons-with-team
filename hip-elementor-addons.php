<?php

/**
 * Plugin Name: Hip Elementor Addons
 * Description: Elementors Addons by Hip Creative, Inc.
 * Plugin URI:  https://hip.agency/
 * Version:     1.4.0
 * Author:      Hip Creative
 * Author URI:  https://hip.agency/
 */

use Elementor\Hip_Team_Elementor_Widget;
use Elementor\Plugin;
use HipAddons\Includes\Addons_Integration;

if (!defined('ABSPATH')) {
	exit;
}

// Define Constants.
define('HIP_ADDONS_VERSION', '1.4.0');
define('HIP_ADDONS_URL', plugins_url('/', __FILE__));
define('HIP_ADDONS_PATH', plugin_dir_path(__FILE__));
define('HIP_ADDONS_FILE', __FILE__);
define('HIP_ADDONS_BASENAME', plugin_basename(HIP_ADDONS_FILE));

/*
  * Load plugin core files
  */
require_once HIP_ADDONS_PATH . 'includes/class-pa-core.php';
require_once HIP_ADDONS_PATH . 'admin/includes/admin-helper.php';
require_once HIP_ADDONS_PATH . 'includes/addons-integration.php';

$addons = new Addons_Integration();

final class Hip_Elementor_Addons
{

	private static $_instance = null;

	public static function instance()
	{
		if (is_null(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __construct()
	{
		add_action('plugins_loaded', [$this, 'init']);

	}

	public function init()
	{
		add_action('elementor/controls/controls_registered', [$this, 'init_controls']);
		add_action('elementor/widgets/widgets_registered', [$this, 'init_team_widget']);
		add_action('elementor/elements/categories_registered', [$this, 'init_category'] );


	}
	/**
	 * Init widget
	 */
	public function init_team_widget()
	{

		if (function_exists('get_field')) :
			require_once(__DIR__ . '/team/hip-team.php');
			Plugin::instance()->widgets_manager->register_widget_type(new Hip_Team_Elementor_Widget());
		endif;
	}
	/**
	 * Init category section
	 */
	public function init_category()
	{
		Elementor\Plugin::instance()->elements_manager->add_category(
			'hip',
			[
				'title' => __('Hip Team', 'hip')
			],
			1
		);
	}
	public function init_controls()
	{
		require_once(__DIR__ . '/controls/hip-mobile-nav-menu-options.php');
		new Hip_Mobile_Nav_Menu_Options();
	}
}

//Copyright Year shortcode
add_shortcode('hip_year', function () {
	return date('Y');
});

add_action('wp_footer', array ($addons, 'googleMapsJS'));


Hip_Elementor_Addons::instance();
