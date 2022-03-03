<?php

class Hip_Mobile_Nav_Menu_Options extends \Elementor\Widget_Base {

	public function __construct() {
		parent::__construct();
		$this->init_control();
	}

	public function get_name() {
		return 'hip-nav-menu';
	}

	public function hip_register_controls( $element, $args ) {

		$element->start_controls_section(
			'hip-nav-addon-section',
			[
				'label' => __( 'HIP Mobile Menu Options', 'hip' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$element->add_control(
			'hip-menu-border-width',
			[
				'label' => __( 'Menu Border Width', 'hip' ),
				'devices' => [ 'tablet', 'mobile' ],
				'type' => \Elementor\Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
					],
				],
				'selectors' => [
					'(mobile) {{WRAPPER}} ul.elementor-nav-menu li' => 'border-bottom-width: {{SIZE}}{{UNIT}}; border-bottom-style: solid;',
					'(mobile) {{WRAPPER}} ul.elementor-nav-menu li:last-child' => 'border-bottom: none;',
					'(tablet) {{WRAPPER}} ul.elementor-nav-menu li' => 'border-bottom-width: {{SIZE}}{{UNIT}}; border-bottom-style: solid;',
					'(tablet) {{WRAPPER}} ul.elementor-nav-menu li:last-child' => 'border-bottom: none;'
				],
				'separator' => 'before',
			]
		);

		$element->add_control(
			'hip-menu-border-color',
			[
				'label' => __( 'Menu Border Color', 'hip' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'(mobile) {{WRAPPER}} ul.elementor-nav-menu li' => 'border-bottom-color: {{VALUE}}; border-bottom-style: solid;',
					'(mobile) {{WRAPPER}} ul.elementor-nav-menu li:last-child' => 'border-bottom: none;',
					'(tablet) {{WRAPPER}} ul.elementor-nav-menu li' => 'border-bottom-color: {{VALUE}}; border-bottom-style: solid;',
					'(tablet) {{WRAPPER}} ul.elementor-nav-menu li:last-child' => 'border-bottom: none;'
				],
			]
		);

		$element->add_control(
			'hip-child-menu-bgcolor',
			[
				'label' => __( 'Child Menu Background', 'hip' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'(mobile) {{WRAPPER}} ul.elementor-nav-menu--dropdown li a' => 'background-color: {{VALUE}} !important;',
					'(tablet) {{WRAPPER}} ul.elementor-nav-menu--dropdown li a' => 'background-color: {{VALUE}} !important;',
				],
			]
		);

		$element->add_control(
			'hip-child-menu-bgcolor-hover',
			[
				'label' => __( 'Child Menu Background on Hover', 'hip' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'(mobile) {{WRAPPER}} ul.elementor-nav-menu--dropdown li a:hover' => 'background-color: {{VALUE}} !important;',
					'(tablet) {{WRAPPER}} ul.elementor-nav-menu--dropdown li a:hover' => 'background-color: {{VALUE}} !important;',
				],
			]
		);

		$element->add_control(
			'hip-child-menu-txcolor',
			[
				'label' => __( 'Child Menu Text Color', 'hip' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'(mobile) {{WRAPPER}} ul.elementor-nav-menu--dropdown li a' => 'color: {{VALUE}} !important;',
					'(tablet) {{WRAPPER}} ul.elementor-nav-menu--dropdown li a' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$element->add_control(
			'hip-child-menu-txcolor-hover',
			[
				'label' => __( 'Child Menu Text Color on Hover', 'hip' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'(mobile) {{WRAPPER}} ul.elementor-nav-menu--dropdown li a:hover' => 'color: {{VALUE}} !important;',
					'(tablet) {{WRAPPER}} ul.elementor-nav-menu--dropdown li a:hover' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$element->add_control(
			'hip-menu-fullscreen',
			[
				'label' => __( 'Menu FullScreen (Yes/No)', 'hip' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
				'selectors' => [
					'(mobile) {{WRAPPER}} nav.elementor-nav-menu__container' => 'height: 100vh; max-height: 90vh;',
					'(tablet) {{WRAPPER}} nav.elementor-nav-menu__container' => 'height: 100vh; max-height: 90vh;',
				]
			]
		);

		$element->end_controls_section();
	}

	protected function init_control() {
		add_action( 'elementor/element/nav-menu/style_toggle/after_section_end', [ $this, 'hip_register_controls' ], 10, 2 );
	}

}
