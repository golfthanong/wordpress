<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Theme Options Field: Text
 *
 * Simple text field.
 *
 * @var $name  string Field name
 * @var $id    string Field ID
 * @var $field array Field options
 *
 * @param $field ['title'] string Field title
 * @param $field ['description'] string Field title
 * @param $field ['placeholder'] string Field placeholder
 *
 * @var $value string Current value
 */

// Hidden result field
$hidden_atts = array(
	'name' => $name,
	'type' => 'hidden',
	'value' => $value,
);

// Field for editing in Visual Composer
if ( isset( $field['us_vc_field'] ) ) {
	// Note: Through the field which has a class `wpb_vc_param_value` Visual Composer receives the final value
	$hidden_atts['class'] = 'wpb_vc_param_value';
}

// TODO: Move to `/usof/templates/field.php`
if ( is_array( $hidden_atts['value'] ) ) {
	$hidden_atts['value'] = rawurlencode( json_encode( $hidden_atts['value'] ) );
}

// Output
$output = '<input'. us_implode_atts( $hidden_atts ) .'/>';

// By default we display the default value
if ( is_array( $value ) AND isset( $value['default'] ) ) {
	$value = $value['default'];
}

// Text input field
$input_atts = array(
	'type' => 'text',
	'value' => $value,
);
if ( ! empty( $field['placeholder'] ) ) {
	$input_atts['placeholder'] = $field['placeholder'];
}
$input_text = '<input'. us_implode_atts( $input_atts ) .'/>';

// Hidden tempalte for dynamic value indication
if ( $with_dynamic_values = us_arr_path( $field, 'dynamic_values' ) ) {
	$popup_id = us_uniqid( /* length */6 );

	$output .= '<div class="usof-form-input-group">';
	$output .= $input_text;
	$output .= '<div class="usof-form-input-dynamic-value hidden" data-popup-show="' . esc_attr( $popup_id ) . '">';
	$output .= '<span class="usof-form-input-dynamic-value-title"></span>';
	$output .= '<button type="button" class="action_remove_dynamic_value ui-icon_close" title="' . esc_attr( us_translate( 'Remove' ) ) . '"></button>';
	$output .= '</div>'; // .usof-form-input-dynamic-value

	$output .= '<div class="usof-form-input-group-controls">';
	$show_button_atts = array(
		'class' => 'fas fa-database',
		'data-popup-show' => $popup_id,
		'title' => __( 'Select Dynamic Value', 'us' ),
	);
	$output .= '<button' . us_implode_atts( $show_button_atts ) . '></button>';
	$output .= '</div>'; // .usof-form-input-group-controls
	$output .= '</div>'; // .usof-form-input-group

	// Predefined global dynamic variables
	$popup_group_buttons = array(
		__( 'Global Values', 'us' ) => array(
			'{{the_title}}' => __( 'Post Title', 'us' ),
			'{{post_type_singular}}' => __( 'Post Type (singular)', 'us' ),
			'{{post_type_plural}}' => __( 'Post Type (plural)', 'us' ),
			'{{comment_count}}' => __( 'Comments amount of the current post', 'us' ),
		),
	);

	// Extend the dynamic of variables from the filters
	$popup_group_buttons = (array) apply_filters( 'us_dynamic_variables', $popup_group_buttons );

	// Add popup to output
	$output .= us_get_template( 'usof/templates/popup', /* popup vars */array(
		'popup_id' => $popup_id,
		'popup_group_buttons' => $popup_group_buttons,
	) );

} else {
	$output .= $input_text;
}

echo $output;
