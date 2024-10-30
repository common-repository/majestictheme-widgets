<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Member widget class
 *
 * @author   MajesticTheme
 * @category Widgets
 * @version  1.0.0
 * @extends  MTW_Widget
 */
class MTW_Member extends MTW_Widget {
	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->show_title         = false;
		$this->widget_cssclass    = 'mtw-widgets widget-mtw-member';
		$this->widget_description = __( 'Create a team member.', 'mtw' );
		$this->widget_id          = 'mtw_member';
		$this->widget_name        = __( 'MTW: Member', 'mtw' );
		$this->settings           = array(
			'title' => array(
				'type'  => 'text',
				'std'   => __( 'Member Name', 'mtw' ),
				'label' => __( 'Name:', 'mtw' )
			),
			'photo' => array(
				'type'  => 'image',
				'std'   => 0,
				'label' => __( 'Photo:', 'mtw' )
			),
			'role' => array(
				'type'  => 'text',
				'std'   => __( 'CTO', 'mtw' ),
				'label' => __( 'Role:', 'mtw' )
			),
			'bio' => array(
				'type'  => 'textarea',
				'std'   => '',
				'label' => __( 'Bio:', 'mtw' )
			),
		);

		foreach ( $this->get_social_media() as $social_key => $medium ) {
			if ( ! isset( $medium['label'] ) ) {
				continue;
			}
			$this->settings[ $social_key ] = array(
				'type'  => 'text',
				'std'   => '',
				'label' => sprintf( esc_html__( '%s URL', 'mtw' ), $medium['label'] )
			);
		}

		parent::__construct();
	}

	public function get_social_media() {
		$media = array(
			'facebook' => array(
				'label' => 'Facebook',
				'icon'  => 'fa fa-facebook',
			),
			'twitter' => array(
				'label' => 'Twitter',
				'icon'  => 'fa fa-twitter',
			),
			'youtube' => array(
				'label' => 'YouTube',
				'icon'  => 'fa fa-youtube',
			),
			'linkedin' => array(
				'label' => 'LinkedIn',
				'icon'  => 'fa fa-linkedin',
			),
			'google-plus' => array(
				'label' => 'Google Plus',
				'icon'  => 'fa fa-google-plus',
			),
			'dribbble' => array(
				'label' => 'Dribbble',
				'icon'  => 'fa fa-dribbble',
			),
		);

		return apply_filters( 'mtw_member_social_media', $media );
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
		$image = $this->get_config_val( $instance['photo'], 0 );
		$name  = $this->get_config_val( $instance['title'] );
		$bio   = $this->get_config_val( $instance['bio'] );
		$role  = $this->get_config_val( $instance['role'] );
		$social_links_html = '';

		foreach ( $this->get_social_media() as $social_key => $medium ) {
			if ( empty( $instance[ $social_key ] ) ) {
				continue;
			}
			$social_links_html .= sprintf( '<a href="%s" title="%s"><i class="%s"></i></a>',
				esc_url( $instance[ $social_key ] ),
				esc_attr( sprintf( esc_html__( '%s URL', 'mtw' ), $medium['label'] ) ),
				esc_attr( $medium['icon'] )
			);
		}

		$this->widget_start( $args, $instance );
		?>

		<div class="mt-team-member">
			<?php if ( ! empty( $image ) && ( $image = wp_get_attachment_image_url( $image, 'full' ) ) ) : ?>
				<div class="mt-thumb">
					<img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $name ); ?>">
					<div class="mt-team-info"><?php echo wp_kses_post( $bio ); ?></div>
				</div>
			<?php endif; ?>
			<h3 class="mt-team-title"><?php echo esc_html( $name ); ?></h3>
			<div class="mt-team-designation"><?php echo esc_html( $role ); ?></div>

			<?php if ( $social_links_html ) : ?>
				<div class="mt-team-socail-links">
					<?php echo $social_links_html; ?>
				</div>
			<?php endif; ?>
		</div>

		<?php
		$this->widget_end( $args );
	}
}
