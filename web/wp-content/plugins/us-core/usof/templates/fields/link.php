<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Theme Options Field: Link
 *
 * Link settings field
 *
 * @var $name string Field name
 * @var $id string Field ID
 * @var $field array Field options
 * @var $field['link_additional_values'] array List of options for exclusion
 */

// Field for the main result
$hidden_input_atts = array(
	'name' => $name,
	'type' => 'hidden',
	'value' => $value,
);

// Field for editing in Visual Composer
if ( isset( $field['us_vc_field'] ) ) {
	// Note: Through the field which has a class `wpb_vc_param_value` Visual Composer receives the final value
	$hidden_input_atts['class'] = 'wpb_vc_param_value';
}

// Output content
$output = '<div class="usof-link">';
$output .= '<input' . us_implode_atts( $hidden_input_atts ) . '>';
$output .= '<div class="usof-form-input-group">';

// Link input field
$input_atts = array(
	'class' => 'usof-link-input-url js_hidden',
	'name' => 'url',
	'placeholder' => us_translate( 'Paste URL or type to search' ),
	'type' => 'text',
	'data-nonce' => wp_create_nonce( 'usof_search_items_for_link' ),
);
$output .= '<input ' . us_implode_atts( $input_atts ) . '>';

// Hidden tempalte for dynamic value indication
if ( $with_dynamic_values = us_arr_path( $field, 'dynamic_values' ) ) {
	$popup_id = us_uniqid( /* length */6 );

	$output .= '<div class="usof-form-input-dynamic-value hidden" data-popup-show="' . esc_attr( $popup_id ) . '">';
	$output .= '<span class="usof-form-input-dynamic-value-title"></span>';
	$output .= '<button type="button" class="action_remove_dynamic_value ui-icon_close" title="' . esc_attr( us_translate( 'Remove' ) ) . '"></button>';
	$output .= '</div>'; // .usof-form-input-dynamic-value
}

$output .= '<div class="usof-form-input-group-controls">';
$output .= '<button class="action_toggle_menu fas fa-cog" title="' . esc_attr( us_translate( 'Link options' ) ) . '"></button>';

// Attributes for dynamic data button
if ( $with_dynamic_values ) {
	$show_button_atts = array(
		'class' => 'fas fa-database',
		'data-popup-show' => $popup_id,
		'title' => __( 'Select Dynamic Value', 'us' ),
	);
	$output .= '<button' . us_implode_atts( $show_button_atts ) . '></button>';
}
$output .= '</div>'; // .usof-form-input-group-controls
$output .= '</div>'; // .usof-link-input

// Link Posts search
$output .= '<div class="usof-link-search-results hidden">';
$output .= '<div class="usof-link-search-message hidden"></div>';
$output .= '</div>';

// Link attributes settings
$output .= '<div class="usof-link-attributes">';

// Target attribute
$output .= '<div class="usof-checkbox"><label>';
$output .= '<input type="checkbox" name="target" value="_blank">' . strip_tags( us_translate( 'Open link in a new tab' ) );
$output .= '</label></div>';

// Rel attribute
$output .= '<div class="usof-checkbox"><label>';
$output .= '<input type="checkbox" name="rel" value="nofollow">' . strip_tags( __( 'Add "nofollow" attribute' , 'us' ) );
$output .= '</label></div>';

// Title attribute
// Note: To bind a checkbox to a text field, use the prefix '{$checkbox_name}_value' in the field name to enter the value
$output .= '<div class="usof-checkbox"><label>';
$output .= '<input type="checkbox" name="title">' . strip_tags( us_translate( 'Title Attribute' ) );
$output .= '<input type="text" name="title_value" placeholder="' . esc_attr( us_translate( 'Text' ) ) . '">';
$output .= '</label></div>';

// Onclick attribute
// Note: To bind a checkbox to a text field, use the prefix '{$checkbox_name}_value' in the field name to enter the value
$output .= '<div class="usof-checkbox"><label>';
$output .= '<input type="checkbox" name="onclick">' . strip_tags( __( 'Onclick JavaScript event', 'us' ) );
$output .= '<input type="text" name="onclick_value" placeholder="return false">';
$output .= '</label></div>';

$output .= '</div>'; // .usof-link-attributes
$output .= '</div>'; // .usof-link

// Popup
if ( $with_dynamic_values ) {
	$popup_group_buttons = array();

	// Set link additional values
	if ( $link_additional_values = us_arr_path( $field, 'link_additional_values' ) ) {
		$popup_group_buttons[ __( 'Global Values', 'us' ) ] = (array) $link_additional_values;
	}

	// Set predefined values
	$popup_group_buttons[ __( 'Custom appearance in Grid', 'us' ) ] = array(
		'custom_field|us_tile_link' => __( 'Custom Link', 'us' ),
	);

	// Set testimonial author link, if Testimonials are enabled
	if ( us_get_option( 'enable_testimonials', 1 ) ) {
		$popup_group_buttons[ __( 'Testimonial', 'us' ) ][ 'custom_field|us_testimonial_link' ] = __( 'Author Link', 'us' );
	}

	// Extend the list of options from the filters
	$popup_group_buttons = (array) apply_filters( 'us_link_options', $popup_group_buttons );

	// Add popup to output
	$output .= us_get_template( 'usof/templates/popup', /* popup vars */array(
		'popup_id' => $popup_id,
		'popup_group_buttons' => $popup_group_buttons,
	) );
}
echo $output;
