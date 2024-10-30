<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Skillbar widget class
 *
 * @author   MajesticTheme
 * @category Widgets
 * @version  1.0.0
 * @extends  MTW_Widget
 */
class MTW_Skillbar extends MTW_Widget {
	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->show_title         = false;
		$this->widget_cssclass    = 'mtw-widgets widget-mtw-skillbar';
		$this->widget_description = __( 'Create a list of skillbar.', 'mtw' );
		$this->widget_id          = 'mtw_skillbar';
		$this->widget_name        = __( 'MTW: Skillbar', 'mtw' );
		$this->settings           = array(
			'skills' => array(
				'type'  => 'group',
				'std'   => 0,
				'label' => __( 'Skill:', 'mtw' ),
				'fields' => array(
					'name' => array(
						'type'  => 'text',
						'std'   => '',
						'label' => __( 'Name:', 'mtw' )
					),
					'competency' => array(
						'type'  => 'text',
						'std'   => '',
						'label' => __( 'Competency (in %):', 'mtw' )
					),
					'color' => array(
						'type'  => 'color',
						'std'   => '',
						'label' => __( 'Bar Color:', 'mtw' )
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
		if ( empty( $instance['skills'] ) ) {
			return;
		}

		$this->widget_start( $args, $instance );

		foreach ( $instance['skills'] as $skill ) :
			$name       = $this->get_config_val( $skill['name'] );
			$competency = $this->get_config_val( $skill['competency'], '50%' );
			$color      = $this->get_config_val( $skill['color'], '#ddd' );
			?>
			<div class="mt-progress">
				<div class="mt-progress-bar" style="background-color: <?php echo esc_attr( $color ); ?>;width: <?php echo esc_attr( $competency ); ?>;">
					<span class="mt-progress-title"><?php echo esc_html( $name ); ?></span>
					<span class="mt-progress-value"><?php echo esc_html( $competency ); ?></span>
				</div>
			</div>
			<?php
		endforeach;

		$this->widget_end( $args );
	}
}
