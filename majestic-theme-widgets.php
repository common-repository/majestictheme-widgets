<?php
/**
 * Plugin Name: MajesticTheme Widgets
 * Description: Essential widgets for themes by MajesticTheme
 * Plugin URI: https://majestictheme.com/majestictheme-widgets/
 * Author: MajesticTheme
 * Author URI: https://majestictheme.com/
 * Version: 1.1.0
 * License: GPL2
 * Text Domain: mtw
 * Domain Path: lang/
 */

/*
	Copyright (C) Year  MajesticTheme  Email

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

class MTW {

	/**
	 * Singalaton instance
	 * 
	 * @var null
	 */
	private static $instance = null;

	/**
	 * Constructor
	 */
	private function __construct() {
		$this->includes();

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		add_action( 'widgets_init', array( $this, 'register_widgets' ) );

		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
	}

	/**
	 * Load plugin textdomain.
	 *
	 * @since 1.0.0
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'mtw', false, basename( dirname( __FILE__ ) ) . '/lang' ); 
	}

	public function register_widgets() {
		$widgets = array(
			'MTW_Member',
			'MTW_Blurb',
			'MTW_Testimonials',
			'MTW_Clients',
			'MTW_Skillbar',
		);
	
		foreach ( $widgets as $widget ) {
			register_widget( $widget );
		}
	}

	public function includes() {
		require $this->get_path() . 'inc/functions.php';
		require $this->get_path() . 'widgets/abstract-mtw-widget.php';
		require $this->get_path() . 'widgets/class-mtw-member.php';
		require $this->get_path() . 'widgets/class-mtw-blurb.php';
		require $this->get_path() . 'widgets/class-mtw-clients.php';
		require $this->get_path() . 'widgets/class-mtw-testimonials.php';
		require $this->get_path() . 'widgets/class-mtw-skillbar.php';
	}

	public function enqueue_scripts() {
		wp_enqueue_style(
			'font-awesome',
			$this->get_url() . 'assets/vendor/font-awesome/css/font-awesome.min.css',
			null,
			null
		);

		wp_enqueue_style(
			'owl.carousel',
			$this->get_url() . 'assets/vendor/owl/assets/owl.carousel.min.css',
			null,
			null
		);

		wp_enqueue_style(
			'owl.theme.default',
			$this->get_url() . 'assets/vendor/owl/assets/owl.theme.default.min.css',
			null,
			null
		);

		wp_enqueue_style(
			'mtw-front',
			$this->get_url() . 'assets/css/front.css',
			null,
			null
		);

		wp_enqueue_script(
			'owl.carousel',
			$this->get_url() . 'assets/vendor/owl/owl.carousel.min.js',
			array( 'jquery' ),
			null,
			true
		);

		wp_enqueue_script(
			'mtw-front',
			$this->get_url() . 'assets/js/front.js',
			array( 'owl.carousel' ),
			null,
			true
		);
	}

	public function enqueue_admin_scripts( $hook ) {
		if ( 'widgets.php' === $hook ) {
			wp_enqueue_style(
				'mtw-admin',
				$this->get_url() . 'assets/css/admin.css',
				array( 'wp-color-picker' ),
				null
			);

			wp_enqueue_script(
				'mtw-admin',
				$this->get_url() . 'assets/js/admin.js',
				array( 'jquery', 'jquery-ui-accordion', 'jquery-ui-sortable', 'wp-color-picker' ),
				null,
				true
			);

			wp_localize_script(
				'mtw-admin',
				'mtw',
				array(
					'imgAddText' => esc_html__( 'Add Image', 'mtw' ),
					'imgReplaceText' => esc_html__( 'Replace Image', 'mtw' ),
				)
			);
		}
	}

	/**
	 * Get or create singalaton instance
	 * 
	 * @return object MTW object
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * Plugin directory path
	 * 
	 * @return string Directory path
	 */
	public function get_path() {
		return plugin_dir_path( __FILE__ );
	}

	public function get_url() {
		return plugin_dir_url( __FILE__ );
	}

	/**
	 * Get widget template path
	 * 
	 * @return string
	 */
	public function get_template_path() {
		return 'mtw-templates' . DIRECTORY_SEPARATOR;
	}
	
}

function MTW() {
	return MTW::get_instance();
}

// Initialize
MTW();
