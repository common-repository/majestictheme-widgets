<?php
/**
 * Locate a template and return the path for inclusion.
 *
 * This is the load order:
 *
 *      yourtheme       /   $template_path  /   $template_name
 *      yourtheme       /   $template_name
 *      $default_path   /   $template_name
 *
 * @access public
 * @param string $template_name
 * @param string $template_path (default: '')
 * @param string $default_path (default: '')
 * @return string
 */
function mtw_locate_template( $template_name, $template_path = '', $default_path = '' ) {
	if ( ! $template_path ) {
		$template_path = MTW()->get_templates_path();
	}

	if ( ! $default_path ) {
		$default_path = MTW()->get_path() . 'templates' . DIRECTORY_SEPARATOR;
	}

	// Look within passed path within the theme - this is priority.
	$template = locate_template(
		array(
			trailingslashit( $template_path ) . $template_name,
			$template_name
		)
	);

	// Get default template/
	if ( ! $template ) {
		$template = $default_path . $template_name;
	}

	// Return what we found.
	return apply_filters( 'mtw_locate_template', $template, $template_name, $template_path );
}

/**
 * Get widget view passing attributes and including the file.
 *
 * @access public
 * @param string $template_name
 * @param array $args (default: array())
 * @param string $template_path (default: '')
 * @param string $default_path (default: '')
 */
function mtw_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
	if ( ! empty( $args ) && is_array( $args ) ) {
		extract( $args, EXTR_SKIP );
	}

	$located = mtw_locate_template( $template_name, $template_path, $default_path );

	if ( ! file_exists( $located ) ) {
		_doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $located ), '1.0' );
		return;
	}

	// Allow 3rd party plugin filter template file from their plugin.
	$located = apply_filters( 'mtw_get_template', $located, $template_name, $args, $template_path, $default_path );

	do_action( 'mtw_before_template_part', $template_name, $template_path, $located, $args );

	include( $located );

	do_action( 'mtw_after_template_part', $template_name, $template_path, $located, $args );
}

function mtw_get_arr_value( &$data, $default = '' ) {
	return isset( $data ) ? $data : $default;
}

function mtw_is_active_widget( $widget_base_id ) {
	return is_active_widget( false, false, $widget_base_id );
}

function mtw_has_any_active_widget() {
	return mtw_is_active_widget( 'mtw_advertise' ) || mtw_is_active_widget( 'mtw_author_bio' ) || mtw_is_active_widget( 'mtw_post_gallery' ) || mtw_is_active_widget( 'mtw_authors' );
}
