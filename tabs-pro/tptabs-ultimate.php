<?php
/*
	Plugin Name: Tptabs Ultimate Shortcodes
	Plugin URI: https://themepoints.com/tabultimate/
	Description: Tab Shortcode Ultimate is yet another simple, responsive, lightweight jQuery tabs plugin for creating responsive tabbed panels with unlimited options 
	Version: 2.0.4
	Author: Themepoints
	Author URI: https://themepoints.com/
	Text Domain: tptabs-ultimate
	Domain Path: /languages
	License: GPLv2
*/

if( !defined( 'ABSPATH' ) ){
    exit;
}

// -----------------------------------------------------------------------------
// 1. FREEMIUS SDK INITIALIZATION
// -----------------------------------------------------------------------------
if ( ! function_exists( 'tp_tptabsfree_fs' ) ) {
	/**
	 * Create a helper function for easy Freemius SDK access.
	 *
	 * @return Freemius
	 */
	function tp_tptabsfree_fs() {
		global $tp_tptabsfree_fs;

		if ( ! isset( $tp_tptabsfree_fs ) ) {
			// Include Freemius SDK.
			require_once dirname( __FILE__ ) . '/freemius/start.php';

			$tp_tptabsfree_fs = fs_dynamic_init( array(
				'id'             => '39401',
				'slug'           => 'tabs-pro',
				'type'           => 'plugin',
				'public_key'     => 'pk_e8bddb09d26903de9656b02af1581',
                'is_premium'          => false,
                'premium_suffix'      => 'Pro',
                'has_premium_version' => true,
                'has_addons'          => false,
                'has_paid_plans'      => true,
                'is_org_compliant'    => false,
                'menu'                => array(
                    'slug'           => 'edit.php?post_type=tp_tab_pro',
                    'support'        => false,
                ),
			) );
		}

		return $tp_tptabsfree_fs;
	}

	// Init Freemius.
	tp_tptabsfree_fs();

	// Signal that Freemius was loaded.
	do_action( 'tp_tptabsfree_fs_loaded' );
}


/**
 * Global helper function to check if Pro license is active.
 *
 * @return bool
 */
function tc_tabs_is_pro() {
	return tp_tptabsfree_fs()->can_use_premium_code();
}

// -----------------------------------------------------------------------------
// 2. MAIN PLUGIN BOOTSTRAP CLASS
// -----------------------------------------------------------------------------

/**
 * Main Plugin Bootstrap Class.
 */
final class TPTabsfree {

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	const VERSION = '2.0.4';

	/**
	 * Option key for storing the installation timestamp.
	 *
	 * @var string
	 */
	const INSTALL_KEY = 'tptabsfree_installed_time';

	/**
	 * Instance of this class.
	 *
	 * @var TPTabsfree|null
	 */
	private static $instance = null;

	/**
	 * Main TPTabsfree Instance.
	 *
	 * @return TPTabsfree
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Private constructor to enforce Singleton pattern.
	 */
	private function __construct() {
		$this->define_constants();
		$this->load_dependencies();
		$this->init_hooks();
	}

	/**
	 * Define Plugin Constants.
	 *
	 * @return void
	 */
	private function define_constants() {
		define( 'TPTABSFREE_VERSION', self::VERSION );
		define( 'TCACCORDION_FILE', __FILE__ );
		define( 'TPTABSFREE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		define( 'TPTABSFREE_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
		define( 'TPTABSFREE_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
	}

	/**
	 * Include Required Class Files.
	 *
	 * @return void
	 */
	private function load_dependencies() {
		// Admin Metabox Manager
		require_once TPTABSFREE_PLUGIN_PATH . 'admin/class-tptabsfree-metabox.php';

		// Frontend Renderer Engine
		require_once TPTABSFREE_PLUGIN_PATH . 'theme/class-tptabsfree-renderer.php';

		// Shortcode Handler
		require_once TPTABSFREE_PLUGIN_PATH . 'inc/class-tptabsfree-shortcode.php';

	}

	/**
	 * Register WordPress Hooks.
	 *
	 * @return void
	 */
	private function init_hooks() {
		// Register activation hook
		register_activation_hook( TCACCORDION_FILE, array( $this, 'activate' ) );

		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_frontend_assets' ) );
		// add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_filter( 'plugin_action_links_' . TPTABSFREE_PLUGIN_BASENAME, array( $this, 'add_plugin_action_links' ) );
	}

	/**
	 * Plugin Activation Callback.
	 * Sets installation timestamp if it doesn't already exist.
	 *
	 * @return void
	 */
	public function activate() {
		if ( ! get_option( self::INSTALL_KEY ) ) {
			update_option( self::INSTALL_KEY, time() );
		}
	}

	/**
	 * Get the installation timestamp.
	 *
	 * @return int Timestamp in seconds.
	 */
	public static function get_installation_time() {
		return (int) get_option( self::INSTALL_KEY, time() );
	}

	/**
	 * Load Translation Text Domain.
	 *
	 * @return void
	 */
	public function load_textdomain() {
		load_plugin_textdomain(
			'tptabs-ultimate',
			false,
			dirname( TPTABSFREE_PLUGIN_BASENAME ) . '/languages'
		);
	}

	/**
	 * Register Frontend Scripts and Styles.
	 *
	 * @return void
	 */
	public function register_frontend_assets() {

		wp_register_style(
			'tptabsfree-font-awesome',
			TPTABSFREE_PLUGIN_URL . 'assets/css/font-awesome.css',
			array(),
			TPTABSFREE_VERSION
		);

		wp_register_style(
			'tptabsfree-tabs-engine',
			TPTABSFREE_PLUGIN_URL . 'assets/css/tp-tabs-engine.css',
			array(),
			TPTABSFREE_VERSION
		);

		wp_register_script(
			'tptabsfree-tabs-engine',
			TPTABSFREE_PLUGIN_URL . 'assets/js/tp-tabs-engine.js',
			array( 'jquery' ),
			TPTABSFREE_VERSION,
			true
		);

	}

	/**
	 * Register Plugin Admin Settings.
	 *
	 * @return void
	 */
	// public function register_settings() {
	// 	register_setting(
	// 		'custom_accordion_options_setting',
	// 		'themepoints_accordion_theme'
	// 	);

	// 	register_setting(
	// 		'custom_accordion_options_setting',
	// 		'accordion_content_font_pages'
	// 	);
	// }

	/**
	 * Add custom action links on the Plugins page.
	 *
	 * @param array $links Existing action links.
	 * @return array Modified action links.
	 */
	public function add_plugin_action_links( $links ) {
		if ( tc_tabs_is_pro() ) {
		    return $links;
		}
		
		// Dynamically use Freemius pricing/upgrade link if active
		$upgrade_url = function_exists( 'tp_tptabsfree_fs' ) ? tp_tptabsfree_fs()->get_upgrade_url() : 'https://themepoints.com/tabultimate/';

		$custom_links = array(
			sprintf(
				'<a href="%s" style="color:#d63638;font-weight:600;">%s</a>',
				esc_url( $upgrade_url ),
				esc_html__( 'Buy Pro!', 'tptabs-ultimate' )
			),
		);

		return array_merge( $links, $custom_links );
	}
}

/**
 * Global function instance accessor.
 *
 * @return TPTabsfree
 */
function tptabsfree() {
	return TPTabsfree::instance();
}

// Initialize the plugin.
tptabsfree();