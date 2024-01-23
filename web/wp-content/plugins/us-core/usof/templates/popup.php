<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Popup for fields.
 *
 * @var $popup_id string The unique popup ID
 * @var $popup_content string The popup ccontent
 * @var $popup_group_buttons array Groups of buttons for selecting dynamic values
 */

// Get popup id
if ( ! isset( $popup_id ) OR empty( $popup_id ) ) {
	$popup_id = us_uniqid( /* length */6 );
}

// Get popup content
if ( ! isset( $popup_content ) ) {
	$popup_content = '';
}

// Buttons for selecting dynamic values
if ( is_array( $popup_group_buttons ) ) {
	foreach( $popup_group_buttons as $group_name => $buttons ) {
		if ( empty( $buttons ) ) {
			continue;
		}
		$popup_content .= '<div class="usof-popup-group">';
		$popup_content .= '<div class="usof-popup-group-title">' . strip_tags( $group_name ) . '</div>';
		$popup_content .= '<div class="usof-popup-group-values">';
		foreach ( $buttons as $value => $label ) {
			$button_atts = array(
				'class' => 'usof-popup-group-value',
				'data-dynamic-value' => $value,
				'data-dynamic-label' => (
					$group_name === __( 'Global Values', 'us' )
						? $label
						: sprintf( '%s: %s', $group_name, $label )
				),
			);
			$popup_content .= '<button' . us_implode_atts( $button_atts ) . '>';
			$popup_content .= strip_tags( $label );
			$popup_content .= '</button>';
		}
		$popup_content .= '</div>'; // .usof-popup-group-values
		$popup_content .= '</div>'; // .usof-popup-group
	}
}

// Output popup
$output ='<div class="usof-popup" data-popup-id="' . esc_attr( $popup_id ) . '">';

// Popup header
$output .= '<div class="usof-popup-header">';
$output .= '<div class="usof-popup-header-title">' . strip_tags( __( 'Select Dynamic Value', 'us' ) ) . '</div>';
$output .= '<button class="usof-popup-close ui-icon_close" title="' . esc_attr( us_translate( 'Close' ) ) . '"></button>';
$output .= '</div>'; // .usof-popup-header

// Popup body
$output .= '<div class="usof-popup-body">' . $popup_content . '</div>';

$output .= '<div class="usof-preloader"></div>';
$output .= '</div>'; // .usof-popup

echo $output;
