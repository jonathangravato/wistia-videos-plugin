<?php
/**
 * Plugin Name: Trial School Videos
 * Description: Trial School Videos imported from Wistia account, set up as custom posts plus all templates for video lib., doc lib., and video authors.
 * Version: 1.2.1
 * Author: Jonathan Gravato
 * License: GPLv2 or later
 * Text Domain: wistia-videos
 *
 * @package wistia-videos
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Plugin name.
 * used to hold plugin name that can be used for constructors of plugin classes.
 */
define( 'WISTIA_VIDEOS_NAME', 'wistia-videos' );

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WISTIA_VIDEOS_VERSION', '1.2.1' );

define( 'PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Wistia Videos Singleton
 */

/**
 * Wistia_Videos
 */
class Wistia_Videos {

	/**
	 * Static property to hold the singleton instance.
	 *
	 * @var bool $instance
	 */
	public static $instance = false;

	/**
	 * Constructor for the class
	 *
	 * @return void
	 */
	private function __construct() {

		$this->define_admin_hooks();

		$this->define_template_hooks();

		$this->define_custom_post_types();

		$this->define_database_hooks();

		add_action( 'init', array( $this, 'enable_sessions' ), 1 );

	}

	/**
	 * If an instance exists, this returns it.  If not, it creates one and
	 * retuns it.
	 *
	 * @return Wistia_Videos
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Defines admin view hooks.
	 */
	public function define_admin_hooks() {

		require_once plugin_dir_path( __FILE__ ) . 'class-wistia-videos-admin.php';

		$admin = new Wistia_Videos_Admin( WISTIA_VIDEOS_NAME, WISTIA_VIDEOS_VERSION );

		$admin->init();

	}

	/**
	 * Defining all shortcode hooks.
	 *
	 * @return void
	 */
	public function define_template_hooks() {

		require_once plugin_dir_path( __FILE__ ) . 'class-wistia-videos-templates.php';

		$plugin_templates = new Wistia_Videos_Templates( WISTIA_VIDEOS_NAME, WISTIA_VIDEOS_VERSION );

		$plugin_templates->init();

		add_filter( 'page_template', array( $plugin_templates, 'wistia_videos_page' ) );

		add_filter( 'single_template', array( $plugin_templates, 'single_wistia_videos' ) );

		add_filter( 'single_template', array( $plugin_templates, 'single_video_authors' ) );

		add_shortcode( 'favorite_videos', array( $plugin_templates, 'wistia_videos_favorites' ) );

		add_shortcode( 'video_library_nav', array( $plugin_templates, 'video_library_nav' ) );

		add_action( 'wp_ajax_ajax_next_posts', array( $plugin_templates, 'ajax_next_posts' ) );

		add_filter( 'allowed_http_origins', array( $plugin_templates, 'add_allowed_origins') );

	}

	/**
	 * Defining custom post types for Wistia Video Authors and Topics
	 *
	 * @return void
	 */
	public function define_custom_post_types() {

		require_once plugin_dir_path( __FILE__ ) . 'class-wisita-videos-cpt.php';

		$plugin_cpt = new Wisita_Videos_CPT( WISTIA_VIDEOS_NAME, WISTIA_VIDEOS_VERSION );

		add_action( 'init', array( $plugin_cpt, 'wistia_videos_cpt_and_tax' ) );

	}

	/**
	 * Defining database hooks for frontend
	 *
	 * @return void
	 */
	public function define_database_hooks() {

		require_once plugin_dir_path( __FILE__ ) . 'class-wistia-videos-db.php';

		$plugin_db = new Wistia_Videos_DB( WISTIA_VIDEOS_NAME, WISTIA_VIDEOS_VERSION );

		$plugin_db->init();

	}

	/**
	 * Enable sessions.
	 *
	 * @return void
	 */
	public function enable_sessions() {

		if ( ! session_id() ) {
			session_start();
		}

	}

}

/* Instantiate our class */
$wistia_videos = Wistia_Videos::get_instance();
