<?php
/**
 ** Generic Feature
 ** Version 1.0.0
 **/
define( 'GENERIC_FEATURE_VERSION', 1 );
class Generic_Feature {

	const OPTION_VERSION  = 'generic_feature_version';
	const SCRIPTS_VERSION = 1;

	// Post Types
	const POST_TYPE_SLUG     = 'generic-feature';
	const POST_TYPE_NAME     = 'Generic Features';
	const POST_TYPE_SINGULAR = 'Generic Feature';
	const POST_TYPE_CAP      = 'post';

	private $version = false;

	// Define and register singleton
	private static $instance = false;
	public static function instance() {
		if( ! self::$instance ) {
			self::$instance = new self;
			self::$instance->setup();
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 */
	private function __construct() { }

	/**
	 * Clone
	 *
	 * @since 1.0.0
	 */
	private function __clone() { }

	/**
	 * Add actions and filters
	 *
	 * @uses add_action, add_filter
	 * @since 1.0.0
	 */
	function setup() {

		// Version Check
		if( $version = get_option( self::OPTION_VERSION, false ) ) {
			$this->version = $version;
		} else {
			$this->version = GENERIC_FEATURE_VERSION;
			add_option( self::OPTION_VERSION, $this->version );
		}

		// Add Meta Boxes
		if( '3.0' <= $wp_version ) { // The `add_meta_boxes` action hook did not exist until WP 3.0
			add_action( 'add_meta_boxes', array( $this, 'action_add_meta_boxes' ) );
		} else {
			add_action( 'admin_init', array( $this, 'action_add_meta_boxes' ) );
		}

		add_action( 'init', array( $this, 'action_init_check_version' ) );
		add_action( 'init', array( $this, 'action_init_register_post_types' ) );
	}

	/**
	 * Version Check
	 *
	 * @since 1.0.0
	 */
	function action_init_check_version() {
		// Check if the version has changed and if so perform the necessary actions
		if ( ! isset( $this->version ) || $this->version <  GENERIC_FEATURE_VERSION ) {
			// Do version upgrade tasks here
			update_option( self::OPTION_VERSION, GENERIC_FEATURE_VERSION );
		}
	}

	/**
	 * Add the Generic Feature metabox
	 *
	 * @uses add_meta_box
	 * @since 1.0.0
	 * @return null
	 */
	function action_add_meta_boxes() {
		add_meta_box( self::POST_TYPE_SLUG . '-metabox', self::POST_TYPE_SINGULAR . __( ' Meta Box' ), array( $this, 'render_metabox' ), self::POST_TYPE_SLUG, 'side', 'default' );
	}

	/**
	 * Remder the Generic Feature metabox
	 *
	 * @since 1.0.0
	 * @return null
	 */
	function render_metabox() {
		echo '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus malesuada ullamcorper.</p>';
	}

	/**
	 * Register custom post type(s)
	 *
	 * @uses register_post_type
	 * @since 1.0.0
	 * @return null
	 */
	public function action_init_register_post_types() {
		// Register the post type
		register_post_type( self::POST_TYPE_SLUG, array(
			'labels' => array(
				'name'          => __( self::POST_TYPE_NAME ),
				'singular_name' => __( self::POST_TYPE_SINGULAR ),
				'add_new_item'  => __( 'Add New ' . self::POST_TYPE_SINGULAR ),
				'edit_item'     => __( 'Edit ' . self::POST_TYPE_SINGULAR ),
				'new_item'      => __( 'New ' . self::POST_TYPE_SINGULAR ),
				'view_item'     => __( 'View ' . self::POST_TYPE_SINGULAR ),
				'search_items'  => __( 'Search' . self::POST_TYPE_NAME ),
			),
			'public'          => true,
			'capability_type' => self::POST_TYPE_CAP,
			'has_archive'     => true,
			'show_ui'         => true,
			'show_in_menu'    => true,
			'hierarchical'    => true,
			'supports'        => array( 'title', 'editor', 'page-attributes', 'thumbnail' ),
		) );
	}

} // Class
Generic_Feature::instance();
