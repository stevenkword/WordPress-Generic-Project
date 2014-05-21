<?php

class Generic_Loader {

	// Version
	const VERSION                     = '1.0.0';
	const VERSION_OPTION              = 'generic_loader_version';
	const REVISION                    = '20140520'; //yyyymmdd
	const TEXT_DOMAIN                 = 'generic';

	protected static $current_version = false;
	private static $instance          = false;

	public $admin_notices             = array();
	public $plugin_dependencies       = array();

	/**
	 * Implement singleton
	 *
	 * @uses self::setup
	 * @return self
	 */
	public static function instance() {
		if ( ! is_a( self::$instance, __CLASS__ ) ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 */
	private function __construct() {
		global $wp_version;
		// Version Check
		if( $version = get_option( self::VERSION_OPTION, false ) ) {
			self::$current_version = $version;
		} else {
			self::$current_version = VERSION;
			add_option( self::VERSION_OPTION, self::$current_version );
		}

		// Load Features
		self::load_features();

		// Check for plugin dependencies
		add_action( 'after_setup_theme', array( $this, 'register_plugin_dependencies' ) );
		add_action( 'admin_init', array( $this, 'check_plugin_dependencies' ) );
		add_action( 'admin_notices', array( $this, 'action_admin_notices' ) );

		// Perform updates if necessary
		add_action( 'init', array( $this, 'action_init_check_version' ) );

		// Posts 2 Posts ( Many to Many relationships ). Sets up connections between custom post types
		add_action( 'p2p_init', array( $this, 'action_p2p_init_register_connections' ) );
	}

	/**
	 * Clone
	 *
	 * @since 1.0.0
	 */
	private function __clone() { }

	/**
	 * [load_features description]
	 * @return [type] [description]
	 */
	private function load_features() {
		/* Staff Feature */
		//require get_template_directory() . '/features/staff/class-staff.php';

		/* Tours Feature */
		//require get_template_directory() . '/features/tours/class-tours.php';

		/* Testimonials Feature */
		//require get_template_directory() . '/features/testimonials/class-testimonials.php';
	}

	/**
	 * On plugin activation
	 *
	 * @uses flush_rewrite_rules()
	 * @since 1.0.0
	 * @return null
	 */
	public function activate() {
		//Generic_Staff::action_init_register_post_types();
		//Generic_Testimonials::action_init_register_post_types();
		//Generic_Tours::action_init_register_post_types();
		//Generic_Tours::action_init_register_taxonomies();
		flush_rewrite_rules();
	}

	/**
	 * On plugin deactivation
	 *
	 * @uses flush_rewrite_rules()
	 * @since 1.0.0
	 * @return null
	 */
	public function deactivate() {
		flush_rewrite_rules();
	}

	/**
	 * Version Check
	 *
	 *
	 *
	 * @since 1.0.0
	 */
	public function action_init_check_version() {
		// Check if the version has changed and if so perform the necessary actions
		if ( ! isset( self::$version ) || self::$version < self::VERSION ) {

			// Perform updates if necessary
			// e.g. if( '2.0.0' > $this->version ) {
			//	do_the_things();
			// }

			// Update the version information in the database
			update_option( self::VERSION_OPTION, self::VERSION );
		}
	}

	/**
	 * Sets up Posts 2 Posts connections between custom post types
	 *
	 * @return [type] [description]
	 */
	public function action_p2p_init_register_connections() {

		if( function_exists( 'p2p_register_connection_type' ) ) {
			// Connect Staff to Tours
			p2p_register_connection_type( array(
				'name' => 'staff_to_tours',
				'from' => Generic_Staff::POST_TYPE_SLUG,
				'to'   => Generic_Tours::POST_TYPE_SLUG
			) );

			// Connect Testimonials to Tours
			p2p_register_connection_type( array(
				'name' => 'testimonials_to_tours',
				'from' => Generic_Testimonials::POST_TYPE_SLUG,
				'to'   => Generic_Tours::POST_TYPE_SLUG
			) );

			// Connect Testimonials to Staff
			p2p_register_connection_type( array(
				'name' => 'testimonials_to_staff',
				'from' => Generic_Testimonials::POST_TYPE_SLUG,
				'to'   => Generic_Staff::POST_TYPE_SLUG
			) );
		}

	}

	/**
	 ** DEPENDENCY MANAGEMENT EXPIRIMENTATION
	 **/

	/**
	 * Define feature dependencies
	 *
	 * [register_plugin_dependencies description]
	 * @return [type] [description]
	 */
	public function register_plugin_dependencies() {

		/*
		$active_plugins = get_option( 'active_plugins', array() );
		var_dump( $active_plugins );
		 */

		/*
		  0 => string 'gravityforms/gravityforms.php' (length=29)
		  1 => string 'add-meta-tags/add-meta-tags.php' (length=31)
		  2 => string 'custom-metadata/custom_metadata.php' (length=35)
		  3 => string 'jetpack/jetpack.php' (length=19)
		  4 => string 'posts-to-posts/posts-to-posts.php' (length=33)
		  5 => string 'revslider/revslider.php' (length=23)
		  6 => string 'share-this/sharethis.php' (length=24)
		  7 => string 'wordpress-importer/wordpress-importer.php' (length=41)
		 */

		// Posts 2 Posts
		$plugin_dependencies[] = array(
			'slug'        => 'posts-to-posts',
			'name'        => 'Posts 2 Posts',
			'path'        => 'posts-to-posts/posts-to-posts.php',
			'description' => 'Tour Guide Connections'
		);

		// Custom Metadata Manager
		$plugin_dependencies[] = array(
			'slug'        => 'custom-metadata',
			'name'        => 'Custom Metadata Manager',
			'path'        => 'custom-metadata/custom_metadata.php',
			'description' => 'Staff Details'
		);

		$this->plugin_dependencies = $plugin_dependencies;
	}

	/**
	 * [check_plugin_dependencies description]
	 * @return [type] [description]
	 */
	public function check_plugin_dependencies() {

		foreach( $this->plugin_dependencies as $dependency ) {

			if( ! isset( $dependency[ 'path' ] ) || ! is_plugin_active( $dependency[ 'path' ] ) ) {
				$plugin_install_url   = admin_url( 'plugin-install.php?tab=search&s=' . $dependency[ 'slug' ] );
				$plugin_install_link  = '<a href="' . esc_url( $plugin_install_url ) . '">Install</a>';

				$plugin_activate_url  = admin_url( 'plugins.php?plugin_status=inactive#' . $dependency[ 'slug' ] );
				$plugin_activate_link = '<a href="' . esc_url( $plugin_activate_url ) . '">Activate</a>';

				$this->admin_notices[] = __( 'The <em>' . $dependency['description'] . '</em> feature is dependent on the <em>' . $dependency['name'] . '</em> plugin. Please ' . $plugin_install_link . ' and ' . $plugin_activate_link . '.', self::TEXT_DOMAIN );

				$this->depenencies_present = false;
			}
		}
	}

	/**
	 * Display the admin notices for missing dependencies
	 *
	 * [action_admin_notices description]
	 * @return [type] [description]
	 */
	public function action_admin_notices() {
		if( 0 < count( $this->admin_notices ) ) {
			echo '<div class="error">';
			foreach( $this->admin_notices as $notice ) {
				echo "<p>$notice</p>";
			}
			echo '</div>';
		}
	}


} // Class
Generic_Loader::instance();

// On Activation
register_activation_hook( __FILE__, array( 'Generic_Loader', 'activate' ) );

// On DeActivation
register_deactivation_hook( __FILE__, array( 'Generic_Loader', 'deactivate' ) );