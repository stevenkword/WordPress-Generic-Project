<?php
/**
 ** Generic Widget
 ** Version 1.0.0
 **/
define( 'GENERIC_WIDGET_VERSION', 1 );
class Generic_Widget extends WP_Widget {

	const OPTION_VERSION  = 'generic_widget_version';
	const SCRIPTS_VERSION = 1;

	protected $version = false;

	/* Define and register singleton */
	private static $instance = false;
	public static function instance() {
		if( ! self::$instance ) {
			self::$instance = new Generic_Widget;
		}
		return self::$instance;
	}

	/**
	 * Clone
	 *
	 * @since 1.0.0
	 */
	private function __clone() { }

	/*
	 * Add actions and filters
	 *
	 * @uses add_action, add_filter
	 * @since 1.0.0
	 */
	function __construct() {

		global $wp_version;

		// Version Checking
		if( $version = get_option( self::OPTION_VERSION, false ) ) {
			$this->version = $version;
		} else {
			$this->version = GENERIC_WIDGET_VERSION;
			add_option( self::OPTION_VERSION, $this->version );
		}
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @since 1.0.0
	 */
	public function widget( $args, $instance ) {
		// outputs the content of the widget
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @since 1.0.0
	 */
 	public function form( $instance ) {
		// outputs the options form on admin
	}

	/**
	 * Processes widget options to be saved
	 *
	 * @since 1.0.0
	 */
	public function update( $new_instance, $old_instance ) {
		// processes widget options to be saved
	}

	/**
	 * Version Checking
	 *
	 * @since 1.0.0
	 */
	function action_init_check_version() {
		// Check if the version has changed and if so perform the necessary actions
		if ( ! isset( $this->version ) || $this->version <  GENERIC_WIDGET_VERSION ) {
			// Do version upgrade tasks here
			update_option( self::OPTION_VERSION, GENERIC_WIDGET_VERSION );
		}
	}

} // Class
Generic_Widget::instance();