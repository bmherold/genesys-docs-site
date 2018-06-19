<?php
/**
 * Provide the view for a metabox
 *
 * @since       1.0.0
 *
 * @package     Yoda_WP
 * @subpackage  Yoda_WP/admin/partials
 */
global $post;
if ( empty( $post ) ) { return; }
if ( 'announcement' != $post->post_type ) { return; }
$this->meta = get_post_custom( $post->ID );

wp_nonce_field( $this->plugin_name, 'announcement_settings' );

$atts                   = array();
$atts['class']          = 'widefat';
$atts['description']    = '';
$atts['id']             = 'announcement-url';
$atts['label']          = 'URL';
$atts['name']           = 'announcement-url';
$atts['placeholder']    = '';
$atts['type']           = 'text';
$atts['value']          = '';
if ( ! empty( $this->meta[$atts['id']][0] ) ) {
    $atts['value'] = $this->meta[$atts['id']][0];
}
apply_filters( $this->plugin_name . '-field-' . $atts['id'], $atts );
?><p><?php
include( plugin_dir_path( __FILE__ ) . $this->plugin_name . '-admin-field-text.php' );
?></p><?php

$atts                   = array();
$atts['class']          = 'widefat';
$atts['description']    = '';
$atts['id']             = 'announcement-permissions';
$atts['label']          = 'Permissions to Show To';
$atts['name']           = 'announcement-permissions';
$atts['placeholder']    = '';
$atts['type']           = 'text';
$atts['value']          = '';
if ( ! empty( $this->meta[$atts['id']][0] ) ) {
    $atts['value'] = $this->meta[$atts['id']][0];
}
apply_filters( $this->plugin_name . '-field-' . $atts['id'], $atts );
?><p><?php
include( plugin_dir_path( __FILE__ ) . $this->plugin_name . '-admin-field-text.php' );
?></p><?php

$atts                   = array();
$atts['class']          = '';
$atts['description']    = '';
$atts['id']             = 'announcement-show-once';
$atts['label']          = 'Show Once?';
$atts['name']           = 'announcement-show-once';
$atts['placeholder']    = '';
$atts['type']           = 'checkbox';
$atts['value']          = '';
if ( ! empty( $this->meta[$atts['id']][0] ) ) {
    $atts['value'] = $this->meta[$atts['id']][0];
}
apply_filters( $this->plugin_name . '-field-' . $atts['id'], $atts );
?><p><?php
include( plugin_dir_path( __FILE__ ) . $this->plugin_name . '-admin-field-checkbox.php' );
?></p>
