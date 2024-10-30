<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Clients widget class
 *
 * @author   MajesticTheme
 * @category Widgets
 * @version  1.0.0
 * @extends  MTW_Widget
 */
class MTW_Clients extends MTW_Widget {
	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->show_title         = false;
		$this->widget_cssclass    = 'mtw-widgets widget-mtw-clients';
		$this->widget_description = __( 'Create a list of client logos.', 'mtw' );
		$this->widget_id          = 'mtw_clients';
		$this->widget_name        = __( 'MTW: Clients', 'mtw' );
		$this->settings           = array(
			'clients' => array(
				'type'  => 'group',
				'std'   => 0,
				'label' => __( 'Client:', 'mtw' ),
				'fields' => array(
					'logo' => array(
						'type'  => 'image',
						'std'   => '',
						'label' => __( 'Logo:', 'mtw' )
					),
					'url' => array(
						'type'  => 'text',
						'std'   => '',
						'label' => __( 'URL:', 'mtw' )
					)
				)
			),
		);

		parent::__construct();
	}

	/**
	 * Output widget.
	 *
	 * @see WP_Widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		if ( empty( $instance['clients'] ) ) {
			return;
		}

		$this->widget_start( $args, $instance );
		
		echo '<div class="mt-clients-logo">';
		foreach ( $instance['clients'] as $client ) :
			$logo = $this->get_config_val( $client['logo'] );
			if ( ! ( $logo = wp_get_attachment_image_url( $logo, 'full' ) ) ) {
				continue;
			}
			$url = $this->get_config_val( $client['url'] );
			?>
			<a href="<?php echo esc_url( $url ); ?>" class="mt-client-item"><img src="<?php echo esc_url( $logo ); ?>"></a>
			<?php
		endforeach;
		echo '</div>';

		$this->widget_end( $args );
	}
}
