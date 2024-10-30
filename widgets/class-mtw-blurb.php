<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Blurb widget class
 *
 * @author   MajesticTheme
 * @category Widgets
 * @version  1.0.0
 * @extends  MTW_Widget
 */
class MTW_Blurb extends MTW_Widget {
	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->show_title         = false;
		$this->widget_cssclass    = 'mtw-widgets widget-mtw-blurb';
		$this->widget_description = __( 'Create a blurb to show features or similar thing.', 'mtw' );
		$this->widget_id          = 'mtw_blurb';
		$this->widget_name        = __( 'MTW: Blurb', 'mtw' );
		$this->settings           = array(
			'title' => array(
				'type'  => 'text',
				'std'   => __( 'Blurb Title', 'mtw' ),
				'label' => __( 'Title:', 'mtw' )
			),
			'image' => array(
				'type'  => 'image',
				'std'   => 0,
				'label' => __( 'Image:', 'mtw' )
			),
			'desc' => array(
				'type'  => 'textarea',
				'std'   => '',
				'label' => __( 'Description:', 'mtw' )
			)
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
		$image = $this->get_config_val( $instance['image'], 0 );
		$title = $this->get_config_val( $instance['title'] );
		$desc  = $this->get_config_val( $instance['desc'] );

		$this->widget_start( $args, $instance );
		?>

		<div class="mt-feature-list mt-center">
			<?php if ( $image && ( $image = wp_get_attachment_image_url( $image, 'full' ) ) ) : ?>
				<div class="mt-feature-list-img">
					<img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $title ); ?>">
				</div>
			<?php endif; ?>
			<h3 class="mt-feature-title"><?php echo esc_html( $title ); ?></h3>
			<div class="mt-feature-info"><?php echo wp_kses_post( $desc ); ?></div>
		</div>
		
		<?php
		$this->widget_end( $args );
	}
}
