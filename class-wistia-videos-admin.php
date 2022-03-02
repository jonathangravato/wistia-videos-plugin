<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://gravatodesign.com
 * @since      1.0.0
 *
 * @package    Wistia_Videos
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wistia_Videos
 * @author     Jonathan Gravato <jonathan@gravatodesign.com>
 */

/**
 * Wistia_Videos_Admin
 */
class Wistia_Videos_Admin {

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
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Init the admin view and enqueue styles and scripts.
	 *
	 * @return void
	 */
	public function init() {

		add_action( 'admin_menu', array( $this, 'wistia_videos_add_admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		$this->build_acf_fields();

		add_filter( 'manage_trial_school_videos_posts_columns', array( $this, 'add_acf_columns' ) );
		add_action( 'manage_trial_school_videos_posts_custom_column', array( $this, 'trial_school_videos_custom_column' ), 10, 2 );

	}


	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		if ( is_admin() ) {
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wistia-videos-admin.css', array(), $this->version, 'all' );
		}

	}

	/**
	 * Add custom admin columns to trial_school_videos cpt
	 *
	 * @param  mixed $columns
	 * @return void
	 */
	public function add_acf_columns( $columns ) {
		return array_merge(
			$columns,
			array(
				'video_authors' => __( 'Video Authors' ),
				'video_files'   => __( 'Video Files' ),
				'video_link'    => __( 'Video Link' ),
			)
		);
	}

	/**
	 * Add columns to to trial_school_videos cpt
	 *
	 * @param  mixed $column
	 * @param  mixed $post_id
	 * @return void
	 */
	public function trial_school_videos_custom_column( $column, $post_id ) {
		switch ( $column ) {
			case 'video_authors':
				if ( get_field( 'video_authors' ) ) {
					$authors = get_field( 'video_authors' );
					foreach ( $authors as $author ) {
						echo esc_html( $author->post_title ) . '<br>';
					}
				}
				break;
			case 'video_link':
				echo '<a href="https://trialschool.wistia.com/medias/' . esc_html( get_field( 'hashed_id', $post_id ) ) . '" target="_blank" rel="nofollow noopener">Video Link</a>';
				break;
			case 'video_files':
				$video_files = get_field( 'video_files' );
				break;

		}
	}


	/**
	 * Register Wista video menu
	 *
	 * @return void
	 */
	public function wistia_videos_add_admin_menu() {

		add_submenu_page(
			'edit.php?post_type=trial_school_videos',
			'Refresh Video List',
			'Refresh Video List',
			'manage_options',
			'wistia_videos_main_page',
			array( $this, 'wistia_videos_main_page' ),
			1,
		);

	}

	/**
	 * Callback for Wistia Videos main admin page.
	 *
	 * @return void
	 */
	public function wistia_videos_main_page() {

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html( __( 'You do not have sufficient permissions to access this page.' ) ) );
		}
		if ( $_POST ) {
			if ( $_POST['updated'] === 'true' ) {
				$this->handle_form();
			}
		}

		?>

		<div class="wrap">
			<h3>Wistia Videos</h3>
			<form method="POST">
				<?php wp_nonce_field( 'wistia_videos_update', 'wistia_videos_form' ); ?>
				<input type="hidden" name="updated" value="true">
				<div class="wistia-videos-submit-wrap">
					<input id="wistia-insert-post" type="submit" name="create_posts" value="Refresh Videos">
				</div>
			</form>

		</div>

		<?php

	}

	/**
	 * Handle Wistia Videos form.
	 *
	 * @return void
	 */
	public function handle_form() {
		if ( ! isset( $_POST['wistia_videos_form'] ) || ! wp_verify_nonce( $_POST['wistia_videos_form'], 'wistia_videos_update' ) ) {

			?>
			
			<div class="error">
				<p>Sorry, your nonce was not correct. Please try again.</p>
			</div>

			<?php
			exit;

		} else {

			if ( class_exists( 'Wistia_Videos_Object' ) ) {
				$obj = new Wistia_Videos_Object();
			} else {
				include_once plugin_dir_path( __FILE__ ) . 'class-wistia-videos-object.php';
				$obj = new Wistia_Videos_Object();
			}

			/* Handle form data. */

			if ( isset( $_POST['create_posts'] ) ) {

				global $wpdb;

				$videos = $obj->get_wistia_videos();

				$args = array(
					'post_type' => 'trial_school_videos',
					'nopaging'  => true,
				);

				$query = new WP_Query( $args );

				$video_ids = array();

				while ( $query->have_posts() ) {
					$query->the_post();
					array_push( $video_ids, get_field( 'video_id' ) );
				}

				foreach ( $videos as $video ) {

					$data = array(
						'post_title'   => $video->name,
						'post_content' => $video->description,
						'post_type'    => 'trial_school_videos',
					);

					if ( in_array( $video->id, $video_ids ) ) {
						continue;
					}

					$post_id = wp_insert_post( $data );

					$thumbnail = str_replace( '?image_crop_resized=200x120', '', $video->thumbnail->url );

					if ( function_exists( 'update_field' ) ) {
						update_field( 'video_id', $video->id, $post_id );
						update_field( 'hashed_id', $video->hashed_id, $post_id );
						update_field( 'project_id', $video->project->id, $post_id );
						update_field( 'thumbnail', $thumbnail, $post_id );
					}
				}
			}

			wp_redirect(admin_url('edit.php?post_type=trial_school_videos'));
		}

	}

	/**
	 * Build Trial School Videos ACF Fields.
	 *
	 * @return void
	 */
	public function build_acf_fields() {
		if ( function_exists( 'acf_add_local_field_group' ) ) :

			acf_add_local_field_group(
				array(
					'key'                   => 'group_611beb64c1683',
					'title'                 => 'Trial School Video Fields',
					'fields'                => array(
						array(
							'key'               => 'field_611c2b8308cd0',
							'label'             => 'Wistia Data',
							'name'              => '',
							'type'              => 'tab',
							'instructions'      => '',
							'required'          => 0,
							'conditional_logic' => 0,
							'wrapper'           => array(
								'width' => '',
								'class' => '',
								'id'    => '',
							),
							'placement'         => 'top',
							'endpoint'          => 0,
						),
						array(
							'key'               => 'field_611bec57257b4',
							'label'             => 'Video ID',
							'name'              => 'video_id',
							'type'              => 'text',
							'instructions'      => '',
							'required'          => 0,
							'conditional_logic' => 0,
							'wrapper'           => array(
								'width' => '',
								'class' => '',
								'id'    => '',
							),
							'default_value'     => '',
							'placeholder'       => '',
							'prepend'           => '',
							'append'            => '',
							'maxlength'         => '',
						),
						array(
							'key'               => 'field_611befd9257b5',
							'label'             => 'Hashed ID',
							'name'              => 'hashed_id',
							'type'              => 'text',
							'instructions'      => '',
							'required'          => 0,
							'conditional_logic' => 0,
							'wrapper'           => array(
								'width' => '',
								'class' => '',
								'id'    => '',
							),
							'default_value'     => '',
							'placeholder'       => '',
							'prepend'           => '',
							'append'            => '',
							'maxlength'         => '',
						),
						array(
							'key'               => 'field_611beff3257b6',
							'label'             => 'Project ID',
							'name'              => 'project_id',
							'type'              => 'text',
							'instructions'      => '',
							'required'          => 0,
							'conditional_logic' => 0,
							'wrapper'           => array(
								'width' => '',
								'class' => '',
								'id'    => '',
							),
							'default_value'     => '',
							'placeholder'       => '',
							'prepend'           => '',
							'append'            => '',
							'maxlength'         => '',
						),
						array(
							'key'               => 'field_611beffd257b7',
							'label'             => 'Thumbnail',
							'name'              => 'thumbnail',
							'type'              => 'text',
							'instructions'      => '',
							'required'          => 0,
							'conditional_logic' => 0,
							'wrapper'           => array(
								'width' => '',
								'class' => '',
								'id'    => '',
							),
							'default_value'     => '',
							'placeholder'       => '',
							'prepend'           => '',
							'append'            => '',
							'maxlength'         => '',
						),
						array(
							'key'               => 'field_611c2b5e08ccf',
							'label'             => 'Authors',
							'name'              => '',
							'type'              => 'tab',
							'instructions'      => '',
							'required'          => 0,
							'conditional_logic' => 0,
							'wrapper'           => array(
								'width' => '',
								'class' => '',
								'id'    => '',
							),
							'placement'         => 'top',
							'endpoint'          => 0,
						),
						array(
							'key'               => 'field_611d3470cdb0e',
							'label'             => 'Video Authors',
							'name'              => 'video_authors',
							'type'              => 'relationship',
							'instructions'      => '',
							'required'          => 0,
							'conditional_logic' => 0,
							'wrapper'           => array(
								'width' => '',
								'class' => '',
								'id'    => '',
							),
							'post_type'         => array(
								0 => 'video_authors',
							),
							'taxonomy'          => '',
							'filters'           => array(
								0 => 'search',
								1 => 'post_type',
								2 => 'taxonomy',
							),
							'elements'          => '',
							'min'               => '',
							'max'               => '',
							'return_format'     => 'object',
						),
						array(
							'key'               => 'field_611c2b3208cce',
							'label'             => 'Featured Video',
							'name'              => '',
							'type'              => 'tab',
							'instructions'      => '',
							'required'          => 0,
							'conditional_logic' => 0,
							'wrapper'           => array(
								'width' => '',
								'class' => '',
								'id'    => '',
							),
							'placement'         => 'top',
							'endpoint'          => 0,
						),
						array(
							'key'               => 'field_611c26aa5a8c7',
							'label'             => 'Featured',
							'name'              => 'featured',
							'type'              => 'true_false',
							'instructions'      => '',
							'required'          => 0,
							'conditional_logic' => 0,
							'wrapper'           => array(
								'width' => '',
								'class' => '',
								'id'    => '',
							),
							'message'           => '',
							'default_value'     => 0,
							'ui'                => 1,
							'ui_on_text'        => '',
							'ui_off_text'       => '',
						),
						array(
							'key'               => 'field_611c2b0808ccd',
							'label'             => 'Files',
							'name'              => '',
							'type'              => 'tab',
							'instructions'      => '',
							'required'          => 0,
							'conditional_logic' => 0,
							'wrapper'           => array(
								'width' => '',
								'class' => '',
								'id'    => '',
							),
							'placement'         => 'top',
							'endpoint'          => 0,
						),
						array(
							'key'               => 'field_611c2a6f08ccb',
							'label'             => 'Video Files',
							'name'              => 'video_files',
							'type'              => 'repeater',
							'instructions'      => '',
							'required'          => 0,
							'conditional_logic' => 0,
							'wrapper'           => array(
								'width' => '',
								'class' => '',
								'id'    => '',
							),
							'collapsed'         => '',
							'min'               => 0,
							'max'               => 0,
							'layout'            => 'table',
							'button_label'      => 'Add Files',
							'sub_fields'        => array(
								array(
									'key'               => 'field_611fde2198765',
									'label'             => 'video_file',
									'name'              => 'video_file',
									'type'              => 'file',
									'instructions'      => '',
									'required'          => 0,
									'conditional_logic' => 0,
									'wrapper'           => array(
										'width' => '',
										'class' => '',
										'id'    => '',
									),
									'return_format'     => 'array',
									'library'           => 'uploadedTo',
									'min_size'          => '',
									'max_size'          => '',
									'mime_types'        => '',
								),
							),
						),
					),
					'location'              => array(
						array(
							array(
								'param'    => 'post_type',
								'operator' => '==',
								'value'    => 'trial_school_videos',
							),
						),
					),
					'menu_order'            => 0,
					'position'              => 'normal',
					'style'                 => 'default',
					'label_placement'       => 'top',
					'instruction_placement' => 'label',
					'hide_on_screen'        => '',
					'active'                => true,
					'description'           => '',
				)
			);

			acf_add_local_field_group(
				array(
					'key' => 'group_612e75de44394',
					'title' => 'Dashboard Options',
					'fields' => array(
						array(
							'key' => 'field_612e76d6d2dea',
							'label' => 'Featured Content',
							'name' => 'featured_content',
							'type' => 'wysiwyg',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'default_value' => '',
							'tabs' => 'all',
							'toolbar' => 'full',
							'media_upload' => 1,
							'delay' => 0,
						),
					),
					'location' => array(
						array(
							array(
								'param' => 'page_template',
								'operator' => '==',
								'value' => 'templates/template-member-dashboard.php',
							),
						),
						array(
							array(
								'param' => 'page',
								'operator' => '==',
								'value' => '1748',
							),
						),
					),
					'menu_order' => 0,
					'position' => 'normal',
					'style' => 'default',
					'label_placement' => 'top',
					'instruction_placement' => 'label',
					'hide_on_screen' => '',
					'active' => true,
					'description' => '',
				)
			);

		endif;
	}

}
