<?php
/**
 * Abstract Widget Class
 *
 * @author   MajesticTheme
 * @category Widgets
 * @version  1.0.0
 * @extends  WP_Widget
 */
abstract class MTW_Widget extends WP_Widget {

	/**
	 * CSS class.
	 *
	 * @var string
	 */
	public $widget_cssclass;

	/**
	 * Widget description.
	 *
	 * @var string
	 */
	public $widget_description;

	/**
	 * Widget ID.
	 *
	 * @var string
	 */
	public $widget_id;

	/**
	 * Widget name.
	 *
	 * @var string
	 */
	public $widget_name;

	/**
	 * Settings.
	 *
	 * @var array
	 */
	public $settings;

	/**
	 * Title visibility
	 * 
	 * @var bool
	 */
	protected $show_title = true;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$widget_ops = array(
			'classname'                   => $this->widget_cssclass,
			'description'                 => $this->widget_description,
			'customize_selective_refresh' => true
		);

		parent::__construct( $this->widget_id, $this->widget_name, $widget_ops );

		add_action( 'save_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );
	}

	/**
	 * Get cached widget.
	 *
	 * @param  array $args
	 * @return bool true if the widget is cached otherwise false
	 */
	public function get_cached_widget( $args ) {

		$cache = wp_cache_get( apply_filters( 'mtw_cached_widget_id', $this->widget_id ), 'widget' );

		if ( ! is_array( $cache ) ) {
			$cache = array();
		}

		if ( isset( $cache[ $args['widget_id'] ] ) ) {
			echo $cache[ $args['widget_id'] ];
			return true;
		}

		return false;
	}

	/**
	 * Cache the widget.
	 *
	 * @param  array $args
	 * @param  string $content
	 * @return string the content that was cached
	 */
	public function cache_widget( $args, $content ) {
		wp_cache_set( apply_filters( 'mtw_cached_widget_id', $this->widget_id ), array( $args['widget_id'] => $content ), 'widget' );

		return $content;
	}

	/**
	 * Flush the cache.
	 */
	public function flush_widget_cache() {
		wp_cache_delete( apply_filters( 'mtw_cached_widget_id', $this->widget_id ), 'widget' );
	}

	/**
	 * Output the html at the start of a widget.
	 *
	 * @param  array $args
	 * @return string
	 */
	public function widget_start( $args, $instance ) {
		echo $args['before_widget'];

		if ( $this->show_title && ( $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base ) ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
	}

	/**
	 * Output the html at the end of a widget.
	 *
	 * @param  array $args
	 * @return string
	 */
	public function widget_end( $args ) {
		echo $args['after_widget'];
	}

	/**
	 * Updates a particular instance of a widget.
	 *
	 * @see    WP_Widget->update
	 * @param  array $new_instance
	 * @param  array $old_instance
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		if ( empty( $this->settings ) ) {
			return $instance;
		}

		$instance = $this->sanitize_field_data( $this->settings, $new_instance );

		$this->flush_widget_cache();

		return $instance;
	}

	protected function sanitize_field_data( $settings, $posted_data = array() ) {
		// Loop settings and get values to save.
		$sanitized_data = array();
		foreach ( $settings as $key => $setting ) {
			if ( ! isset( $setting['type'] ) ) {
				continue;
			}

			// Format the value based on settings type.
			switch ( $setting['type'] ) {
				case 'number' :
					$sanitized_data[ $key ] = absint( $posted_data[ $key ] );

					if ( isset( $setting['min'] ) && '' !== $setting['min'] ) {
						$sanitized_data[ $key ] = max( $posted_data[ $key ], $setting['min'] );
					}

					if ( isset( $setting['max'] ) && '' !== $setting['max'] ) {
						$sanitized_data[ $key ] = min( $posted_data[ $key ], $setting['max'] );
					}
				break;
				case 'textarea' :
					$sanitized_data[ $key ] = wp_kses( trim( wp_unslash( $posted_data[ $key ] ) ), wp_kses_allowed_html( 'post' ) );
				break;
				case 'checkbox' :
					$sanitized_data[ $key ] = empty( $posted_data[ $key ] ) ? 0 : 1;
				break;
				case 'image' :
					$sanitized_data[ $key ] = empty( $posted_data[ $key ] ) ? 0 : absint( $posted_data[ $key ] );
				break;
				case 'group' :
					$sanitized_data[ $key ] = $this->sanitize_group_field_data( $setting, $posted_data[ $key ] );
				break;
				default:
					$sanitized_data[ $key ] = sanitize_text_field( $posted_data[ $key ] );
				break;
			}
		}

		return $sanitized_data;
	}

	protected function sanitize_group_field_data( $settings, $posted_data ) {
		if ( empty( $settings['fields'] ) || empty( $posted_data ) ) {
			return;
		}
		// Remove the template
		array_pop( $posted_data );

		$sanitized_data = array();
		for ( $i = 0, $len = count( $posted_data ); $i < $len; $i++ ) {
			$index = $posted_data[ $i ]['index'];
			unset( $posted_data[ $i ]['index'] );
			$sanitized_data[ $index ] = $this->sanitize_field_data( $settings['fields'], $posted_data[ $i ] );
		}
		
		return $sanitized_data;
	}

	/**
	 * Check a variable existance and retrive data, check by reference
	 * and fallback to default
	 * 
	 * @param array $var Reference variable
	 * @return mixed Checked variable or fallback
	 */
	protected function get_config_val( &$var, $default = '' ) {
		return isset( $var ) ? $var : $default;
	}

	/**
	 * Print field extra css class
	 * 
	 * @param array $config Field configs
	 * @return void
	 */
	protected function echo_class( $config = array() ) {
		echo esc_attr( $this->get_config_val( $config['class'] ) );
	}

	/**
	 * Print field label
	 * 
	 * @param array $config Field configs
	 * @return void
	 */
	protected function echo_label( $config = array() ) {
		echo esc_html( $this->get_config_val( $config['label'] ) );
	}

	protected function generate_text_field_html( $key, $value = '', $config = array() ) {
		?>
		<p>
			<label for="<?php echo $this->get_field_id( $key ); ?>"><?php $this->echo_label( $config ); ?></label>
			<input class="widefat <?php $this->echo_class( $config ); ?>" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>" type="text" value="<?php echo esc_attr( $value ); ?>" />
		</p>
		<?php
	}

	protected function generate_color_field_html( $key, $value = '', $config = array() ) {
		?>
		<p class="mtw-field-wrap">
			<label for="<?php echo $this->get_field_id( $key ); ?>"><?php $this->echo_label( $config ); ?></label>
			<input class="widefat mtw-field-color <?php $this->echo_class( $config ); ?>" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>" type="text" value="<?php echo esc_attr( $value ); ?>" />
		</p>
		<script>
			jQuery( function() {
				if ( window.initColorField ) {
					window.initColorField();
				}
			} );
		</script>
		<?php
	}

	protected function generate_number_field_html( $key, $value = '', $config = array() ) {
		$step = $this->get_config_val( $config['step'] );
		$min  = $this->get_config_val( $config['min'] );
		$max  = $this->get_config_val( $config['max'] );
		?>
		<p>
			<label for="<?php echo $this->get_field_id( $key ); ?>"><?php $this->echo_label( $config ); ?></label>
			<input class="widefat <?php $this->echo_class( $config ); ?>" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>" type="number" step="<?php echo esc_attr( $step ); ?>" min="<?php echo esc_attr( $min ); ?>" max="<?php echo esc_attr( $max ); ?>" value="<?php echo esc_attr( $value ); ?>" />
		</p>
		<?php
	}

	protected function generate_textarea_field_html( $key, $value = '', $config = array() ) {
		?>
		<p>
			<label for="<?php echo $this->get_field_id( $key ); ?>"><?php $this->echo_label( $config ); ?></label>
			<textarea class="widefat <?php $this->echo_class( $config ); ?>" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>" cols="20" rows="3"><?php echo esc_textarea( $value ); ?></textarea>
		</p>
		<?php
	}

	protected function generate_checkbox_field_html( $key, $value = '', $config = array() ) {
		?>
		<p>
			<input class="checkbox <?php $this->echo_class( $config ); ?>" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $key ) ); ?>" type="checkbox" value="1" <?php checked( $value, 1 ); ?> />
			<label for="<?php echo $this->get_field_id( $key ); ?>"><?php $this->echo_label( $config ); ?></label>
		</p>
		<?php
	}

	protected function generate_select_field_html( $key, $value = '', $config = array() ) {
		$options = $this->get_config_val( $config['options'], array() );
		?>
		<p>
			<label for="<?php echo $this->get_field_id( $key ); ?>"><?php $this->echo_label( $config ); ?></label>
			<select class="widefat <?php $this->echo_class( $config ); ?>" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>">
				<?php foreach ( $options as $option_key => $option_value ) : ?>
					<option value="<?php echo esc_attr( $option_key ); ?>" <?php selected( $option_key, $value ); ?>><?php echo esc_html( $option_value ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<?php
	}

	protected function generate_image_field_html( $key, $value = '', $config = array() ) {
		?>
		<div class="media-widget-control mtw-image-field">
			<p class="mtw-image-field-label"><label for="<?php echo $this->get_field_id( $key ); ?>"><?php $this->echo_label( $config ); ?></label></p>
			<div class="media-widget-preview media_image mtw-image-field-ctrl mtw-image-field-preview">
				<?php if ( ! empty( $value ) && ( $image = wp_get_attachment_image_url( $value, 'thumbnail' ) ) ) : ?>
					<img src="<?php echo esc_url( $image ); ?>">
				<?php else : ?>
					<div class="attachment-media-view">
						<div class="placeholder"><?php esc_html_e( 'No image selected', 'mtw' ); ?></div>
					</div>
				<?php endif; ?>
			</div>
			<p class="media-widget-buttons">
				<button id="<?php echo $this->get_field_id( $key ); ?>" type="button" class="button mtw-image-field-btn mtw-image-field-ctrl"><?php esc_html_e( 'Add Image', 'mtw' ); ?></button>
			</p>
			<input class="mtw-image-field-id" name="<?php echo $this->get_field_name( $key ); ?>" type="hidden" value="<?php echo esc_attr( $value ); ?>">
		</div>
		<?php
	}

	protected function generate_group_field_html( $key, $value = '', $config = array() ) {
		if ( empty( $config['fields'] ) || ! is_array( $config['fields'] ) ) {
			return;
		} ?>
		<div class="mtw-group-field-wrapper">
			<div class="mtw-field-group-list">
				<?php if ( empty( $value ) ) : ?>
					<div class="mtw-field-group">
						<div class="mtw-field-group-header">
							<h4 class="mtw-field-title"><?php $this->echo_label( $config ); ?></h4>
						</div>
						<div class="mtw-field-group-inner">
							<?php
							foreach ( $config['fields'] as $field_key => $field ) {
								if ( ! isset( $field['type'] ) ) {
									continue;
								}
								$this->render_field_html( $field['type'], $key . '[0][' . $field_key . ']', $field, $value );
							}
							?>
							<input type="hidden" value="0" name="<?php echo $this->get_field_name( $key . '[0][index]' ); ?>" class="mtw-field-index">
							<div class="textright mtw-field-group-actions">
								<button type="button" class="button-link mtw-field-group-action-close"><?php esc_html_e( 'Close', 'mtw' ); ?></button> | <button type="button" class="button-link button-link-delete mtw-field-group-action-delete"><?php esc_html_e( 'Delete', 'mtw' ); ?></button>
							</div>
						</div>
					</div>
				<?php else : ?>
					<?php for ( $i = 0, $len = count( $value ); $i < $len; $i++ ) : ?>
						<div class="mtw-field-group">
							<div class="mtw-field-group-header">
								<h4 class="mtw-field-title"><?php $this->echo_label( $config ); ?></h4>
							</div>
							<div class="mtw-field-group-inner">
								<?php
								foreach ( $config['fields'] as $field_key => $field ) {
									if ( ! isset( $field['type'] ) ) {
										continue;
									}
									$this->render_field_html( $field['type'], $key . '[' . $i . '][' . $field_key . ']', $field, $value[ $i ], $field_key );
								}
								?>
								<input type="hidden" value="<?php echo esc_attr( $i ); ?>" name="<?php echo $this->get_field_name( $key . '[' . $i . '][index]' ); ?>" class="mtw-field-index">
								<div class="textright mtw-field-group-actions">
									<button type="button" class="button-link mtw-field-group-action-close"><?php esc_html_e( 'Close', 'mtw' ); ?></button> | <button type="button" class="button-link button-link-delete mtw-field-group-action-delete"><?php esc_html_e( 'Delete', 'mtw' ); ?></button>
								</div>
							</div>
						</div>
					<?php endfor; ?>
				<?php endif; ?>
			</div>
			<div id="mtw-field-group-template" class="mtw-field-group">
				<div class="mtw-field-group-header">
					<h4 class="mtw-field-title"><?php $this->echo_label( $config ); ?></h4>
				</div>
				<div class="mtw-field-group-inner">
					<?php
					foreach ( $config['fields'] as $field_key => $field ) {
						if ( ! isset( $field['type'] ) ) {
							continue;
						}
						$this->render_field_html( $field['type'], $key . '[%id%][' . $field_key . ']', $field, $value );
					}
					?>
					<input type="hidden" value="%id%" name="<?php echo $this->get_field_name( $key . '[%id%][index]' ); ?>" class="mtw-field-index">
					<div class="textright mtw-field-group-actions">
						<button type="button" class="button-link mtw-field-group-action-close"><?php esc_html_e( 'Close', 'mtw' ); ?></button> | <button type="button" class="button-link button-link-delete mtw-field-group-action-delete"><?php esc_html_e( 'Delete', 'mtw' ); ?></button>
					</div>
				</div>
			</div>
			<button type="button" class="button mtw-group-field-add"><?php esc_html_e( 'Add Item', 'mtw' ); ?></button>
		</div>
		<script>
			if ( window.initGroupFields ) {
				window.initGroupFields();
			}
		</script>
		<?php
	}

	private function render_field_html( $type, $key, $config = array(), $instance = array(), $group_key = '' ) {
		if ( empty( $config ) || ! is_array( $config ) ) {
			return;
		}

		$field_generator_method = 'generate_' . $type . '_field_html';
		if ( ! method_exists( $this, $field_generator_method ) ) {
			return;
		}

		if ( $group_key ) {
			$value = isset( $instance[ $group_key ] ) ? $instance[ $group_key ] : $this->get_config_val( $config['std'] );			
		} else {
			$value = isset( $instance[ $key ] ) ? $instance[ $key ] : $this->get_config_val( $config['std'] );
		}

		$this->{$field_generator_method}( $key, $value, $config );
	}

	/**
	 * Outputs the settings update form.
	 *
	 * @see   WP_Widget->form
	 * @param array $instance
	 */
	public function form( $instance ) {
		if ( empty( $this->settings ) ) {
			return;
		}

		foreach ( $this->settings as $key => $setting ) {
			if ( ! isset( $setting['type'] ) ) {
				continue;
			}
			$this->render_field_html( $setting['type'], $key, $setting, $instance );
		}
	}

}
