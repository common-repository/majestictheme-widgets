<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Testimonials widget class
 *
 * @author   MajesticTheme
 * @category Widgets
 * @version  1.0.0
 * @extends  MTW_Widget
 */
class MTW_Testimonials extends MTW_Widget {
	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->show_title         = false;
		$this->widget_cssclass    = 'mtw-widgets widget-mtw-testimonials';
		$this->widget_description = __( 'Create testimonials carousel.', 'mtw' );
		$this->widget_id          = 'mtw_testimonials';
		$this->widget_name        = __( 'MTW: Testimonials', 'mtw' );
		$this->settings           = array(
			'testimonials' => array(
				'type'   => 'group',
				'label'  => __( 'Testimonials:', 'mtw' ),
				'fields' => array(
					'name' => array(
						'type'  => 'text',
						'std'   => '',
						'label' => __( 'Name:', 'mtw' )
					),
					'role' => array(
						'type'  => 'text',
						'std'   => '',
						'label' => __( 'Job Role:', 'mtw' )
					),
					'photo' => array(
						'type'  => 'image',
						'std'   => 0,
						'label' => __( 'Photo:', 'mtw' )
					),
					'statement' => array(
						'type'  => 'textarea',
						'std'   => '',
						'label' => __( 'Statement:', 'mtw' )
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
		if ( empty( $instance['testimonials'] ) ) {
			return;
		}

		$this->widget_start( $args, $instance );
		echo '<div class="owl-carousel owl-theme mt-center mt-testimonial-wrapper mtw-js-testimonials">';

		foreach ( $instance['testimonials'] as $testimonial ) :
			$name      = $this->get_config_val( $testimonial['name'] );
			$role      = $this->get_config_val( $testimonial['role'] );
			$photo     = $this->get_config_val( $testimonial['photo'] );
			$statement = $this->get_config_val( $testimonial['statement'] );
			$photo     = wp_get_attachment_image_url( $photo, 'full' );
			?>
			<div class="item">
				<div class="mt-testimonial-shadow mt-testimonial-dark">
					<div class="mt-media">
						<div class="mt-media-left mt-media-middle">
							<div class="mt-thumb">
								<img class="img-responsive" src="<?php echo esc_url( $photo ); ?>" alt="<?php esc_attr( $name ) ?>">
							</div>
						</div>
						<div class="mt-media-body mt-media-middle mt-center">
							<i class="fa fa-quote-right" aria-hidden="true"></i>
							<p><?php echo esc_html( $statement ); ?></p>
						</div>
					</div>
					<div class="mt-testimonial-author-wrapper">
						<div class="mt-testimonial-author">
							<span class="h5 mt-uppercase"><?php echo esc_html( $name ); ?></span> <small><?php echo esc_html( $role ); ?></small>
						</div>
					</div>
				</div>
			</div>
			<?php
		endforeach;

		echo '</div>';
		$this->widget_end( $args );
	}
}
