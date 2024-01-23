<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * EFFECTS settings for shortcodes
 */

return array(

	'scroll_effect' => array(
		'switch_text' => __( 'Scrolling Effects', 'us' ),
		'type' => 'switch',
		'std' => 0,
		'classes' => 'beta',
		'cols' => 1, // for correct UI in WPBakery
		'group' => __( 'Effects', 'us' ),
		'context' => array( 'shortcode' ),
		'usb_preview' => TRUE,
	),

	// Vertical Shift
	'scroll_translate_y' => array(
		'switch_text' => __( 'Vertical Shift', 'us' ),
		'type' => 'switch',
		'std' => 0,
		'show_if' => array( 'scroll_effect', '=', 1 ),
		'group' => __( 'Effects', 'us' ),
		'context' => array( 'shortcode' ),
		'usb_preview' => TRUE,
	),
	'scroll_translate_y_direction' => array(
		'type' => 'radio',
		'std' => 'up',
		'options' => array(
			'up' => _x( 'Up', 'direction', 'us' ),
			'down' => _x( 'Down', 'direction', 'us' ),
		),
		'show_if' => array( 'scroll_translate_y', '=', 1 ),
		'group' => __( 'Effects', 'us' ),
		'usb_preview' => array(
			'attr' => 'data-translate_y_direction',
		),
	),
	'scroll_translate_y_speed' => array(
		'title' => __( 'Speed', 'us' ),
		'type' => 'slider',
		'std' => '0.5x',
		'options' => array(
			'x' => array(
				'min' => 0.1,
				'max' => 2.0,
				'step' => 0.1,
			),
		),
		'show_if' => array( 'scroll_translate_y', '=', 1 ),
		'group' => __( 'Effects', 'us' ),
		'context' => array( 'shortcode' ),
		'usb_preview' => array(
			'attr' => 'data-translate_y_speed',
		),
	),

	// Inertion
	'scroll_delay' => array(
		'title' => __( 'Delay', 'us' ),
		'type' => 'slider',
		'std' => '0.1s',
		'options' => array(
			's' => array(
				'min' => 0.0,
				'max' => 1.0,
				'step' => 0.1,
			),
		),
		'show_if' => array( 'scroll_translate_y', '=', 1 ),
		'group' => __( 'Effects', 'us' ),
		'context' => array( 'shortcode' ),
		'usb_preview' => array(
			'css' => '--scroll-delay',
		),
	),

	// Start/End Position
	'scroll_start_position' => array(
		'title' => __( 'Animation Start Position', 'us' ),
		'description' => __( 'Distance from the bottom screen edge, where the element starts its animation', 'us' ),
		'type' => 'slider',
		'std' => '0%',
		'options' => array(
			'%' => array(
				'min' => 0,
				'max' => 50,
				'step' => 5,
			),
		),
		'show_if' => array( 'scroll_translate_y', '=', 1 ),
		'classes' => 'desc_4',
		'cols' => 1, // for correct UI in WPBakery
		'group' => __( 'Effects', 'us' ),
		'context' => array( 'shortcode' ),
		'usb_preview' => array(
			'attr' => 'data-start_position',
		),
	),
	'scroll_end_position' => array(
		'title' => __( 'Animation End Position', 'us' ),
		'description' => __( 'Distance from the bottom screen edge, where the element ends its animation', 'us' ),
		'type' => 'slider',
		'std' => '100%',
		'options' => array(
			'%' => array(
				'min' => 50,
				'max' => 100,
				'step' => 5,
			),
		),
		'show_if' => array( 'scroll_translate_y', '=', 1 ),
		'classes' => 'desc_4',
		'cols' => 1, // for correct UI in WPBakery
		'group' => __( 'Effects', 'us' ),
		'context' => array( 'shortcode' ),
		'usb_preview' => array(
			'attr' => 'data-end_position',
		),
	),

);
