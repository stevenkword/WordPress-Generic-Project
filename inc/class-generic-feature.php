<?php
/**
 ** Generic Feature
 **/
define( 'GENERIC_FEATURE_VERSION', 1 );
class Generic_Feature {

	const VERSION_OPTION  = 'generic_features_version';
	const SCRIPTS_VERSION = 1;

	/* Post Type */
	const POST_TYPE          = 'generic_feature';
	const POST_TYPE_SLUG     = 'feature';
	const POST_TYPE_NAME     = 'Features';
	const POST_TYPE_SINGULAR = 'Feature';
	const POST_TYPE_CAP      = 'post';

	var $version = false;

	/* Define and register singleton */
	private static $instance = false;
	public static function instance() {
		if( ! self::$instance ) {
			self::$instance = new Generic_Feature;
		}
		return self::$instance;
	}

	/**
	 * Clone
	 */
	private function __clone() { }

	/*
	 * Add actions and filters
	 *
	 * @uses add_action, add_filter
	 */
	function __construct() {

		// Versioning
		if( $version = get_option( self::VERSION_OPTION, false ) ) {
			$this->version = $version;
		} else {
			$this->version = GENERIC_FEATURE_VERSION;
			add_option( self::VERSION_OPTION, $this->version );
		}

		add_action( 'init', array( $this, 'action_init_check_version' ) );
		add_action( 'init', array( $this, 'action_init_register_post_types' ) );
	}

	/**
	 * Register the post types
	 *
	 * @uses register_post_type
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

	/**
	 * Version Checking
	 */
	function action_init_check_version() {
		// Check if the version and make necessary changes
		if ( $this->version !=  GENERIC_FEATURE_VERSION ) {
			// Do version upgrade tasks
			update_option( self::VERSION_OPTION, GENERIC_FEATURE_VERSION );
		}
	}
} // Class
Generic_Feature::instance();
