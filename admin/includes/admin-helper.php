<?php

/**
 * PA Admin Helper
 */

namespace HipAddons\Admin\Includes;

use HipAddons\Includes\Helper_Functions;

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Class Admin_Helper
 */
class Admin_Helper
{

	/**
	 * Admin settings tabs
	 *
	 * @var tabs
	 */
	private static $tabs = null;

	/**
	 * Class instance
	 *
	 * @var instance
	 */
	private static $instance = null;

	/**
	 * Premium Addons Settings Page Slug
	 *
	 * @var page_slug
	 */
	protected $page_slug = 'hip-elementor-addons';

	/**
	 * Elements List
	 *
	 * @var elements_list
	 */
	public static $elements_list = null;

	/**
	 * Integrations List
	 *
	 * @var integrations_list
	 */
	public static $integrations_list = null;

	/**
	 * Google maps prefixes
	 *
	 * @var google_localize
	 */
	private static $google_localize = null;

	/**
	 * Constructor for the class
	 */
	public function __construct()
	{

		// Insert admin settings submenus.
		$this->set_admin_tabs();
		add_action('admin_menu', array($this, 'add_menu_tabs'), 100);

		// Enqueue required admin scripts.
		add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));

		// Plugin Action Links.
		add_filter('plugin_action_links_' . HIP_ADDONS_BASENAME, array($this, 'insert_action_links'));

		// Register AJAX HOOKS.
		add_action('wp_ajax_hip_elements_settings', array($this, 'save_settings'));
		add_action('wp_ajax_hip_additional_settings', array($this, 'save_additional_settings'));
	}

	/**
	 * Get Elements List
	 *
	 * Get a list of all the elements available in the plugin
	 *
	 * @since 3.20.9
	 * @access private
	 *
	 * @return array widget_list
	 */
	private static function get_elements_list()
	{

		if (null === self::$elements_list) {

			self::$elements_list = $elements = array(
				'cat-1'  => array(
					'icon'     => 'all',
					'title'    => __('All Widgets', 'premium-addons-for-elementor'),
					'elements' => array(
						array(
							'key'      => 'premium-maps',
							'title'    => __('Google Maps', 'premium-addons-for-elementor'),
						),
					),
				),
				'cat-2'  => array(
					'icon'     => 'content',
					'title'    => __('Content Widgets', 'premium-addons-for-elementor'),
					'elements' => array(
						array(
							'key'      => 'premium-maps',
							'title'    => __('Google Maps', 'premium-addons-for-elementor'),
						),
					),
				),
			);
		}

		return self::$elements_list;
	}

	/**
	 * Get Integrations List
	 *
	 * Get a list of all the integrations available in the plugin
	 *
	 * @since 3.20.9
	 * @access private
	 *
	 * @return array integrations_list
	 */
	private static function get_integrations_list()
	{

		if (null === self::$integrations_list) {

			self::$integrations_list = array(
				'hip-map-api',
				'hip-map-disable-api',
				'hip-map-cluster',
				'hip-map-locale',
			);
		}

		return self::$integrations_list;
	}

	/**
	 * Admin Enqueue Scripts
	 *
	 * Enqueue the required assets on our admin pages
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function admin_enqueue_scripts()
	{

		wp_enqueue_style(
			'pa-admin-css',
			HIP_ADDONS_URL . 'admin/assets/css/admin.css',
			array(),
			HIP_ADDONS_VERSION,
			'all'
		);

		wp_enqueue_script(
			'hip-admin',
			HIP_ADDONS_URL . 'admin/assets/js/admin.js',
			array('jquery'),
			HIP_ADDONS_VERSION,
			true
		);

		$localized_data = array(
			'settings'               => array(
				'ajaxurl' => admin_url('admin-ajax.php'),
				'nonce'   => wp_create_nonce('pa-settings-tab'),
			),
		);

		wp_localize_script('hip-admin', 'hipAddonsSettings', $localized_data);
	}

	/**
	 * Insert action links.
	 *
	 * Adds action links to the plugin list table
	 *
	 * Fired by `plugin_action_links` filter.
	 *
	 * @param array $links plugin action links.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function insert_action_links($links)
	{
		$settings_link = sprintf('<a href="%1$s">%2$s</a>', admin_url('admin.php?page=' . $this->page_slug . '#tab=elements'), __('Settings', 'premium-addons-for-elementor'));

		$new_links = array($settings_link);

		$new_links = array_merge($links, $new_links);

		return $new_links;
	}

	/**
	 * Set Admin Tabs
	 *
	 * @access private
	 * @since 3.20.8
	 */
	private function set_admin_tabs()
	{

		$slug = $this->page_slug;

		self::$tabs = array(
			'integrations'    => array(
				'id'       => 'integrations',
				'slug'     => $slug . '#tab=integrations',
				'title'    => __('Integrations', 'premium-addons-for-elementor'),
				'href'     => '#tab=integrations',
				'template' => HIP_ADDONS_PATH . 'admin/includes/templates/integrations',
			),
		);
	}

	/**
	 * Add Menu Tabs
	 *
	 * Create Submenu Page
	 *
	 * @since 3.20.9
	 * @access public
	 *
	 * @return void
	 */
	public function add_menu_tabs()
	{

		$plugin_name = 'HIP Map';

		call_user_func(
			'add_menu_page',
			$plugin_name,
			$plugin_name,
			'manage_options',
			$this->page_slug,
			array($this, 'render_setting_tabs'),
			'',
			100
		);

		foreach (self::$tabs as $tab) {

			call_user_func(
				'add_submenu_page',
				$this->page_slug,
				$tab['title'],
				$tab['title'],
				'manage_options',
				$tab['slug'],
				'__return_null'
			);
		}

		remove_submenu_page($this->page_slug, $this->page_slug);
	}

	/**
	 * Render Setting Tabs
	 *
	 * Render the final HTML content for admin setting tabs
	 *
	 * @access public
	 * @since 3.20.8
	 */
	public function render_setting_tabs()
	{

?>
		<div class="pa-settings-wrap">
			<div class="pa-settings-tabs">
				<ul class="pa-settings-tabs-list">
					<?php
					foreach (self::$tabs as $key => $tab) {
						$link          = '<li class="pa-settings-tab">';
						$link     .= '<a id="pa-tab-link-' . esc_attr($tab['id']) . '"';
						$link     .= ' href="' . esc_url($tab['href']) . '">';
						$link .= '<i class="pa-dash-' . esc_attr($tab['id']) . '"></i>';
						$link .= '<span>' . esc_html($tab['title']) . '</span>';
						$link     .= '</a>';
						$link         .= '</li>';

						echo $link;
					}
					?>
				</ul>
			</div> <!-- Settings Tabs -->

			<div class="pa-settings-sections">
				<?php
				foreach (self::$tabs as $key => $tab) {
					echo wp_kses_post('<div id="pa-section-' . $tab['id'] . '" class="pa-section pa-section-' . $key . '">');
					include_once $tab['template'] . '.php';
					echo '</div>';
				}
				?>
			</div> <!-- Settings Sections -->
		</div> <!-- Settings Wrap -->
<?php
	}

	/**
	 * Save Settings
	 *
	 * Save elements settings using AJAX
	 *
	 * @access public
	 * @since 3.20.8
	 */
	public function save_settings()
	{

		check_ajax_referer('pa-settings-tab', 'security');

		if (!isset($_POST['fields'])) {
			return;
		}

		parse_str(sanitize_text_field($_POST['fields']), $settings);

		$defaults = self::get_default_elements();

		$elements = array_fill_keys(array_keys(array_intersect_key($settings, $defaults)), true);

		update_option('pa_save_settings', $elements);

		wp_send_json_success();
	}

	/**
	 * Save Integrations Control Settings
	 *
	 * Stores integration and version control settings
	 *
	 * @since 3.20.8
	 * @access public
	 */
	public function save_additional_settings()
	{

		check_ajax_referer('pa-settings-tab', 'security');

		if (!isset($_POST['fields'])) {
			return;
		}

		parse_str(sanitize_text_field($_POST['fields']), $settings);

		$new_settings = array(
			'hip-map-api'         => sanitize_text_field($settings['hip-map-api']),
			'hip-map-disable-api' => intval($settings['hip-map-disable-api'] ? 1 : 0),
			'hip-map-cluster'     => intval($settings['hip-map-cluster'] ? 1 : 0),
			'hip-map-locale'      => sanitize_text_field($settings['hip-map-locale']),
		);

		update_option('hip_maps_save_settings', $new_settings);

		wp_send_json_success($settings);
	}


	/**
	 * Get default Elements
	 *
	 * @since 3.20.9
	 * @access private
	 *
	 * @return $default_keys array keys defaults
	 */
	public static function get_default_elements()
	{

		$elements = self::get_elements_list();

		$keys = array();

		// Now, we need to fill our array with elements keys.
		foreach ($elements as $cat) {
			if (count($cat['elements'])) {
				foreach ($cat['elements'] as $elem) {
					array_push($keys, $elem['key']);
				}
			}
		}

		$default_keys = array_fill_keys($keys, true);

		return $default_keys;
	}

	/**
	 * Get Default Interations
	 *
	 * @since 3.20.9
	 * @access private
	 *
	 * @return $default_keys array default keys
	 */
	private static function get_default_integrations()
	{

		$settings = self::get_integrations_list();

		$default_keys = array_fill_keys($settings, true);

		return $default_keys;
	}

	/**
	 * Get Integrations Settings
	 *
	 * Get plugin integrations settings
	 *
	 * @since 3.20.9
	 * @access public
	 *
	 * @return array $settings integrations settings
	 */
	public static function get_integrations_settings()
	{

		$enabled_keys = get_option('hip_maps_save_settings', self::get_default_integrations());

		return $enabled_keys;
	}

	/**
	 * Creates and returns an instance of the class
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return object
	 */
	public static function get_instance()
	{

		if (!isset(self::$instance)) {

			self::$instance = new self();
		}

		return self::$instance;
	}

	public static function get_google_maps_prefixes()
	{

		if (null === self::$google_localize) {

			self::$google_localize = array(
				'ar'    => __('Arabic', 'premium-addons-for-elementor'),
				'eu'    => __('Basque', 'premium-addons-for-elementor'),
				'bg'    => __('Bulgarian', 'premium-addons-for-elementor'),
				'bn'    => __('Bengali', 'premium-addons-for-elementor'),
				'ca'    => __('Catalan', 'premium-addons-for-elementor'),
				'cs'    => __('Czech', 'premium-addons-for-elementor'),
				'da'    => __('Danish', 'premium-addons-for-elementor'),
				'de'    => __('German', 'premium-addons-for-elementor'),
				'el'    => __('Greek', 'premium-addons-for-elementor'),
				'en'    => __('English', 'premium-addons-for-elementor'),
				'en-AU' => __('English (australian)', 'premium-addons-for-elementor'),
				'en-GB' => __('English (great britain)', 'premium-addons-for-elementor'),
				'es'    => __('Spanish', 'premium-addons-for-elementor'),
				'fa'    => __('Farsi', 'premium-addons-for-elementor'),
				'fi'    => __('Finnish', 'premium-addons-for-elementor'),
				'fil'   => __('Filipino', 'premium-addons-for-elementor'),
				'fr'    => __('French', 'premium-addons-for-elementor'),
				'gl'    => __('Galician', 'premium-addons-for-elementor'),
				'gu'    => __('Gujarati', 'premium-addons-for-elementor'),
				'hi'    => __('Hindi', 'premium-addons-for-elementor'),
				'hr'    => __('Croatian', 'premium-addons-for-elementor'),
				'hu'    => __('Hungarian', 'premium-addons-for-elementor'),
				'id'    => __('Indonesian', 'premium-addons-for-elementor'),
				'it'    => __('Italian', 'premium-addons-for-elementor'),
				'iw'    => __('Hebrew', 'premium-addons-for-elementor'),
				'ja'    => __('Japanese', 'premium-addons-for-elementor'),
				'kn'    => __('Kannada', 'premium-addons-for-elementor'),
				'ko'    => __('Korean', 'premium-addons-for-elementor'),
				'lt'    => __('Lithuanian', 'premium-addons-for-elementor'),
				'lv'    => __('Latvian', 'premium-addons-for-elementor'),
				'ml'    => __('Malayalam', 'premium-addons-for-elementor'),
				'mr'    => __('Marathi', 'premium-addons-for-elementor'),
				'nl'    => __('Dutch', 'premium-addons-for-elementor'),
				'no'    => __('Norwegian', 'premium-addons-for-elementor'),
				'pl'    => __('Polish', 'premium-addons-for-elementor'),
				'pt'    => __('Portuguese', 'premium-addons-for-elementor'),
				'pt-BR' => __('Portuguese (brazil)', 'premium-addons-for-elementor'),
				'pt-PT' => __('Portuguese (portugal)', 'premium-addons-for-elementor'),
				'ro'    => __('Romanian', 'premium-addons-for-elementor'),
				'ru'    => __('Russian', 'premium-addons-for-elementor'),
				'sk'    => __('Slovak', 'premium-addons-for-elementor'),
				'sl'    => __('Slovenian', 'premium-addons-for-elementor'),
				'sr'    => __('Serbian', 'premium-addons-for-elementor'),
				'sv'    => __('Swedish', 'premium-addons-for-elementor'),
				'tl'    => __('Tagalog', 'premium-addons-for-elementor'),
				'ta'    => __('Tamil', 'premium-addons-for-elementor'),
				'te'    => __('Telugu', 'premium-addons-for-elementor'),
				'th'    => __('Thai', 'premium-addons-for-elementor'),
				'tr'    => __('Turkish', 'premium-addons-for-elementor'),
				'uk'    => __('Ukrainian', 'premium-addons-for-elementor'),
				'vi'    => __('Vietnamese', 'premium-addons-for-elementor'),
				'zh-CN' => __('Chinese (simplified)', 'premium-addons-for-elementor'),
				'zh-TW' => __('Chinese (traditional)', 'premium-addons-for-elementor'),
			);
		}

		return self::$google_localize;
	}
}
