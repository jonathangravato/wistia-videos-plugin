<?php
/**
 * The public-specific functionality of the plugin.
 *
 * @link       http://gravatodesign.com
 * @since      1.1.0
 *
 * @package    Wistia_Videos
 */

/**
 * The public-specific functionality of the plugin.
 *
 * @package    Wistia_Videos
 * @author     Jonathan Gravato <jonathan@gravatodesign.com>
 */

/**
 * Wisita Videos CPT
 */
class Wisita_Videos_CPT {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * __construct
	 *
	 * @param string $plugin_name    Name of the plugin.
	 * @param string $version Version of the plugin.
	 * @return void
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register Wistia Video Authors custom post type.
	 *
	 * @return void
	 */
	public function wistia_videos_cpt_and_tax() {

		// CPT Trial School Videos

		$labels = array(
			'name'                  => _x( 'Trial School Videos', 'Post Type General Name', 'wistia-videos' ),
			'singular_name'         => _x( 'Trial School Video', 'Post Type Singular Name', 'wistia-videos' ),
			'menu_name'             => __( 'Trial School Videos', 'wistia-videos' ),
			'name_admin_bar'        => __( 'Trial School Videos', 'wistia-videos' ),
			'archives'              => __( 'Item Archives', 'wistia-videos' ),
			'attributes'            => __( 'Item Attributes', 'wistia-videos' ),
			'parent_item_colon'     => __( 'Parent Item:', 'wistia-videos' ),
			'all_items'             => __( 'All Items', 'wistia-videos' ),
			'add_new_item'          => __( 'Add New Item', 'wistia-videos' ),
			'add_new'               => __( 'Add New', 'wistia-videos' ),
			'new_item'              => __( 'New Item', 'wistia-videos' ),
			'edit_item'             => __( 'Edit Item', 'wistia-videos' ),
			'update_item'           => __( 'Update Item', 'wistia-videos' ),
			'view_item'             => __( 'View Item', 'wistia-videos' ),
			'view_items'            => __( 'View Items', 'wistia-videos' ),
			'search_items'          => __( 'Search Item', 'wistia-videos' ),
			'not_found'             => __( 'Not found', 'wistia-videos' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'wistia-videos' ),
			'featured_image'        => __( 'Featured Image', 'wistia-videos' ),
			'set_featured_image'    => __( 'Set featured image', 'wistia-videos' ),
			'remove_featured_image' => __( 'Remove featured image', 'wistia-videos' ),
			'use_featured_image'    => __( 'Use as featured image', 'wistia-videos' ),
			'insert_into_item'      => __( 'Insert into item', 'wistia-videos' ),
			'uploaded_to_this_item' => __( 'Uploaded to this item', 'wistia-videos' ),
			'items_list'            => __( 'Items list', 'wistia-videos' ),
			'items_list_navigation' => __( 'Items list navigation', 'wistia-videos' ),
			'filter_items_list'     => __( 'Filter items list', 'wistia-videos' ),
		);
		$args = array(
			'label'                 => __( 'Trial School Video', 'wistia-videos' ),
			'description'           => __( 'Trial School videos imported from Wistia', 'wistia-videos' ),
			'labels'                => $labels,
			'supports'              => array( 'title', 'editor' ),
			'taxonomies'            => array( 'video_types', 'video_series', 'video_topics' ),
			'hierarchical'          => false,
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 5,
			'menu_icon'             => 'dashicons-video-alt3',
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => true,
			'can_export'            => true,
			'has_archive'           => false,
			'exclude_from_search'   => true,
			'publicly_queryable'    => true,
			'capability_type'       => 'page',
			'show_in_rest'          => true,
		);
		register_post_type( 'trial_school_videos', $args );

		// Register Video Type Taxonomy

		$labels = array(
			'name'                       => _x( 'Video Types', 'Taxonomy General Name', 'wistia-videos' ),
			'singular_name'              => _x( 'Video Type', 'Taxonomy Singular Name', 'wistia-videos' ),
			'menu_name'                  => __( 'Video Type', 'wistia-videos' ),
			'all_items'                  => __( 'All Items', 'wistia-videos' ),
			'parent_item'                => __( 'Parent Item', 'wistia-videos' ),
			'parent_item_colon'          => __( 'Parent Item:', 'wistia-videos' ),
			'new_item_name'              => __( 'New Item Name', 'wistia-videos' ),
			'add_new_item'               => __( 'Add New Item', 'wistia-videos' ),
			'edit_item'                  => __( 'Edit Item', 'wistia-videos' ),
			'update_item'                => __( 'Update Item', 'wistia-videos' ),
			'view_item'                  => __( 'View Item', 'wistia-videos' ),
			'separate_items_with_commas' => __( 'Separate items with commas', 'wistia-videos' ),
			'add_or_remove_items'        => __( 'Add or remove items', 'wistia-videos' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'wistia-videos' ),
			'popular_items'              => __( 'Popular Items', 'wistia-videos' ),
			'search_items'               => __( 'Search Items', 'wistia-videos' ),
			'not_found'                  => __( 'Not Found', 'wistia-videos' ),
			'no_terms'                   => __( 'No items', 'wistia-videos' ),
			'items_list'                 => __( 'Items list', 'wistia-videos' ),
			'items_list_navigation'      => __( 'Items list navigation', 'wistia-videos' ),
		);
		$args = array(
			'labels'                     => $labels,
			'hierarchical'               => false,
			'public'                     => true,
			'show_ui'                    => true,
			'show_admin_column'          => true,
			'show_in_nav_menus'          => true,
			'show_tagcloud'              => true,
		);
		register_taxonomy( 'video_type', array( 'trial_school_videos' ), $args );

		// Register Video Series Taxonomy

		$labels = array(
			'name'                       => _x( 'Video Series', 'Taxonomy General Name', 'wistia-videos' ),
			'singular_name'              => _x( 'Video Series', 'Taxonomy Singular Name', 'wistia-videos' ),
			'menu_name'                  => __( 'Video Series', 'wistia-videos' ),
			'all_items'                  => __( 'All Items', 'wistia-videos' ),
			'parent_item'                => __( 'Parent Item', 'wistia-videos' ),
			'parent_item_colon'          => __( 'Parent Item:', 'wistia-videos' ),
			'new_item_name'              => __( 'New Item Name', 'wistia-videos' ),
			'add_new_item'               => __( 'Add New Item', 'wistia-videos' ),
			'edit_item'                  => __( 'Edit Item', 'wistia-videos' ),
			'update_item'                => __( 'Update Item', 'wistia-videos' ),
			'view_item'                  => __( 'View Item', 'wistia-videos' ),
			'separate_items_with_commas' => __( 'Separate items with commas', 'wistia-videos' ),
			'add_or_remove_items'        => __( 'Add or remove items', 'wistia-videos' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'wistia-videos' ),
			'popular_items'              => __( 'Popular Items', 'wistia-videos' ),
			'search_items'               => __( 'Search Items', 'wistia-videos' ),
			'not_found'                  => __( 'Not Found', 'wistia-videos' ),
			'no_terms'                   => __( 'No items', 'wistia-videos' ),
			'items_list'                 => __( 'Items list', 'wistia-videos' ),
			'items_list_navigation'      => __( 'Items list navigation', 'wistia-videos' ),
		);
		$args = array(
			'labels'                     => $labels,
			'hierarchical'               => false,
			'public'                     => true,
			'show_ui'                    => true,
			'show_admin_column'          => true,
			'show_in_nav_menus'          => true,
			'show_tagcloud'              => true,
		);
		register_taxonomy( 'video_series', array( 'trial_school_videos' ), $args );

		// Register Video Series Taxonomy

		$labels = array(
			'name'                       => _x( 'Video Topics', 'Taxonomy General Name', 'wistia-videos' ),
			'singular_name'              => _x( 'Video Topic', 'Taxonomy Singular Name', 'wistia-videos' ),
			'menu_name'                  => __( 'Video Topics', 'wistia-videos' ),
			'all_items'                  => __( 'All Items', 'wistia-videos' ),
			'parent_item'                => __( 'Parent Item', 'wistia-videos' ),
			'parent_item_colon'          => __( 'Parent Item:', 'wistia-videos' ),
			'new_item_name'              => __( 'New Item Name', 'wistia-videos' ),
			'add_new_item'               => __( 'Add New Item', 'wistia-videos' ),
			'edit_item'                  => __( 'Edit Item', 'wistia-videos' ),
			'update_item'                => __( 'Update Item', 'wistia-videos' ),
			'view_item'                  => __( 'View Item', 'wistia-videos' ),
			'separate_items_with_commas' => __( 'Separate items with commas', 'wistia-videos' ),
			'add_or_remove_items'        => __( 'Add or remove items', 'wistia-videos' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'wistia-videos' ),
			'popular_items'              => __( 'Popular Items', 'wistia-videos' ),
			'search_items'               => __( 'Search Items', 'wistia-videos' ),
			'not_found'                  => __( 'Not Found', 'wistia-videos' ),
			'no_terms'                   => __( 'No items', 'wistia-videos' ),
			'items_list'                 => __( 'Items list', 'wistia-videos' ),
			'items_list_navigation'      => __( 'Items list navigation', 'wistia-videos' ),
		);
		$args = array(
			'labels'                     => $labels,
			'hierarchical'               => false,
			'public'                     => true,
			'show_ui'                    => true,
			'show_admin_column'          => true,
			'show_in_nav_menus'          => true,
			'show_tagcloud'              => true,
		);
		register_taxonomy( 'video_topic', array( 'trial_school_videos' ), $args );
		
		// CPT Video Authors

		$labels = array(
			'name'                  => _x( 'Video Authors', 'Post Type General Name', 'wistia-videos' ),
			'singular_name'         => _x( 'Video Author', 'Post Type Singular Name', 'wistia-videos' ),
			'menu_name'             => __( 'Wistia Video Authors', 'wistia-videos' ),
			'name_admin_bar'        => __( 'Wistia Video Authors', 'wistia-videos' ),
			'archives'              => __( 'Wistia Video Authors Archives', 'wistia-videos' ),
			'attributes'            => __( 'Wistia Video Author Attributes', 'wistia-videos' ),
			'parent_item_colon'     => __( 'Parent Item:', 'wistia-videos' ),
			'all_items'             => __( 'All Wistia Video Authors', 'wistia-videos' ),
			'add_new_item'          => __( 'Add New Author', 'wistia-videos' ),
			'add_new'               => __( 'Add New', 'wistia-videos' ),
			'new_item'              => __( 'New Author', 'wistia-videos' ),
			'edit_item'             => __( 'Edit Author', 'wistia-videos' ),
			'update_item'           => __( 'Update Author', 'wistia-videos' ),
			'view_item'             => __( 'View Author', 'wistia-videos' ),
			'view_items'            => __( 'View Authors', 'wistia-videos' ),
			'search_items'          => __( 'Search Authors', 'wistia-videos' ),
			'not_found'             => __( 'Not found', 'wistia-videos' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'wistia-videos' ),
			'featured_image'        => __( 'Featured Image', 'wistia-videos' ),
			'set_featured_image'    => __( 'Set featured image', 'wistia-videos' ),
			'remove_featured_image' => __( 'Remove featured image', 'wistia-videos' ),
			'use_featured_image'    => __( 'Use as featured image', 'wistia-videos' ),
			'insert_into_item'      => __( 'Insert into Author', 'wistia-videos' ),
			'uploaded_to_this_item' => __( 'Uploaded to this Author', 'wistia-videos' ),
			'items_list'            => __( 'Authors list', 'wistia-videos' ),
			'items_list_navigation' => __( 'Authors list navigation', 'wistia-videos' ),
			'filter_items_list'     => __( 'Filter Authors list', 'wistia-videos' ),
		);
		$args   = array(
			'label'               => __( 'Video Author', 'wistia-videos' ),
			'description'         => __( 'Authors for Trial School videos from Wistia', 'wistia-videos' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => 'edit.php?post_type=trial_school_videos',
			'menu_icon'           => 'dashicons-id',
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'page',
			'show_in_rest'        => true,
		);
		register_post_type( 'video_authors', $args );

	}

}
