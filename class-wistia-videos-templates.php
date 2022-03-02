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
 * Class to handle all plugin shortcodes.
 */
class Wistia_Videos_Templates {

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

		if ( class_exists( 'Wistia_Videos_DB' ) ) {
			$db = new Wistia_Videos_DB( WISTIA_VIDEOS_NAME, WISTIA_VIDEOS_VERSION );
		} else {
			include_once plugin_dir_path( __FILE__ ) . 'class-wistia-videos-db.php';
			$db = new Wistia_Videos_DB( WISTIA_VIDEOS_NAME, WISTIA_VIDEOS_VERSION );
		}
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->db          = $db;

	}

	/**
	 * Init the public view and enqueue styles and scripts.
	 *
	 * @return void
	 */
	public function init() {

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		
	}


	/**
	 * Register the JavaScript for the public area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		if ( ! is_admin() ) {
			wp_enqueue_style( $this->plugin_name . '-public', plugin_dir_url( __FILE__ ) . 'css/wistia-videos-public.css', array(), $this->version, 'all' );
			wp_register_script( $this->plugin_name . '-public', plugin_dir_url( __FILE__ ) . 'js/wistia-videos-public.js', array( 'jquery' ), $this->version, true );
			wp_enqueue_script( $this->plugin_name . '-public' );
			wp_enqueue_script( 'ajax-script', plugin_dir_url( __FILE__ ) . 'js/ajax-script.js', array( 'jquery' ), $this->version, true );
		}

	}

	/**
	 * Defining all shortcode hooks.
	 *
	 * @return void
	 */
	public function wistia_videos_page( $page_template ) {

		if ( is_page( 'video-library' ) ) {
			$page_template = plugin_dir_path( __FILE__ ) . 'template-search-wistia-videos.php';
		}

		return $page_template;
	}

	/**
	 * My_custom_template
	 *
	 * @param  mixed $single
	 * @return void
	 */
	public function single_wistia_videos( $single ) {

		global $post;

		/* Checks for single template by post type */
		if ( $post->post_type === 'trial_school_videos' ) {
			if ( file_exists( PLUGIN_PATH . '/template-single-wistia-videos.php' ) ) {
				return PLUGIN_PATH . '/template-single-wistia-videos.php';
			}
		}

		return $single;
	}

	/**
	 * My_custom_template
	 *
	 * @param  mixed $single
	 * @return void
	 */
	public function single_video_authors( $single ) {

		global $post;

		/* Checks for single template by post type */
		if ( $post->post_type === 'video_authors' ) {
			if ( file_exists( PLUGIN_PATH . '/template-single-wistia-video-authors.php' ) ) {
				return PLUGIN_PATH . '/template-single-wistia-video-authors.php';
			}
		}

		return $single;
	}

	/**
	 * Wistia Videos Favorites Shortcode
	 *
	 * @param  mixed $atts
	 * @return void
	 */
	public function wistia_videos_favorites( $atts ) {

		// Attributes
		$atts = shortcode_atts(
			array(
				'user_id' => 'null',
			),
			$atts,
			'favorite_videos'
		);

		$results = $this->db->get_records( $atts['user_id'], 'favorites' );

		$video_atts = array(
			'user_id' => $atts['user_id'],
		);
		
		ob_start();

		?>

		<div class="row favorites-container">

		<?php
		
		foreach ( $results as $result ) {
			$this->render_video( $result->video_id, true, $video_atts );
		}

		?>
		</div>
		<?php

		return ob_get_clean();

	}

	public function video_library_nav() {

		if ( isset( $_POST ) ) {
			if ( isset( $_POST['updated'] ) ) {

				$_SESSION['filter'] = 'topics';
				$_SESSION['video_topic'] = $_POST['search_topics'];

				$this->handle_form();
			}
		}

		ob_start();
		?>
		<form method="POST">
			<?php
			wp_nonce_field( 'wistia_videos_update', 'wistia_videos_form' );
			?>
				<input type="hidden" name="updated" value="true">
				<input type="hidden" name="redirect" value="true">
				<ul class="side-menu-list">
					<?php
					$topics = get_terms(
						array(
							'taxonomy'   => 'video_topic',
							'hide_empty' => 1,
						),
					);

					if ( ! empty( $topics ) ) {
						foreach ( $topics as $topic ) {
							echo '<li><input type="submit" name="search_topics" value="' . esc_html( $topic->name ) . '"></li>';
						}
					}
					?>
				</ul>
			</form>
		<?php
		return ob_get_clean();
	}

	/**
	 * Renders search bar for Wistia Videos
	 *
	 * @return void
	 */
	public function render_search_bar() {

		if ( isset( $_POST ) ) {
			if ( isset( $_POST['updated'] ) ) {
				$this->handle_form();
			}
		}

		?>

		<div class="row">
			<div class="col-12">
				<h3 class="mb-3">Search Videos</h3>
			</div>
			<div class="col-12 d-block d-md-flex">
				<form method="POST">
				<?php wp_nonce_field( 'wistia_videos_update', 'wistia_videos_form' ); ?>
					<input type="hidden" name="updated" value="true">
					<input type="hidden" name="search_authors" value="true">
					<div class="selector-container">
						<select name="wistia_video_authors" id="video-authors">
						<option data-display="Search by Author">Search by Author</option>
						<?php

						$current_authors = $this->get_available_authors();

						foreach ( $current_authors as $author ) {
							$author_slug = strtolower( str_replace( ' ', '-', $author[0] ) );
							
							$args = array(
								'name'        => $author_slug,
								'post_type'   => 'video_authors',
								'post_status' => 'publish',
							);

							$author_obj = get_posts( $args );

							foreach ( $author_obj as $the_author ) {
								echo '<option value="' . esc_html( $the_author->ID ) . '">' . esc_html( $the_author->post_title ) . '</option>';
							}
						}

						?>
						</select>
					</div>
					<!-- <div class="form-submit">
						<input type="submit" style="height: 30px; border: none; padding: 0 10px;" name="search_authors" value="Go">
					</div> -->
				</form>
			
				<form method="POST">
						<?php wp_nonce_field( 'wistia_videos_update', 'wistia_videos_form' ); ?>
					<input type="hidden" name="updated" value="true">
					<input type="hidden" name="search_topics" value="true">
					<div class="selector-container">
						<select name="video_topics" id="video-topics">
							<option data-display="Search by Topic">Search by Topic</option>
							<?php

							$topics = get_terms(
								array(
									'taxonomy'   => 'video_topic',
									'hide_empty' => 1,
								),
							);

							if ( ! empty( $topics ) ) {
								foreach ( $topics as $topic ) {
									echo '<option value="' . esc_html( $topic->term_id ) . '">' . esc_html( $topic->name ) . '</option>';
								}
							}

							?>
						</select>
					</div>
					<!-- <div class="form-submit">
						<input type="submit" style="height: 30px; border: none; padding: 0 10px;" name="search_topics" value="Go">
					</div> -->
				</form>
			
				<form method="POST">
					<?php wp_nonce_field( 'wistia_videos_update', 'wistia_videos_form' ); ?>
					<input type="hidden" name="updated" value="true">
					<input type="hidden" name="search_series" value="true">
					<div class="selector-container">
						<select name="video_series" id="video-series">
							<option data-display="Search by Series">Search by Series</option>
							<?php

							$all_series = get_terms(
								array(
									'taxonomy'   => 'video_series',
									'hide_empty' => 1,
								),
							);

							if ( ! empty( $all_series ) ) {
								foreach ( $all_series as $series ) {
									echo '<option value="' . esc_html( $series->term_id ) . '">' . esc_html( $series->name ) . '</option>';
								}
							}

							?>
						</select>
					</div>
					<!-- <div class="form-submit">
						<input type="submit" style="height: 30px; border: none; padding: 0 10px;" name="search_series" value="Go">
					</div> -->
				</form>
			</div>
		</div>

			<?php

	}

	/**
	 * Renders video grid populated by either featured videos or search results.
	 *
	 * @return void
	 */
	public function render_video_grid() {

		$filter = $_GET['filter'];

		if ( $filter ) {

			$filter_slug = $_GET['slug'];
			switch ( $filter ) {
				case 'series':
					$results = get_posts(
						array(
							'numberposts' => -1,
							'post_type'   => 'trial_school_videos',
							'tax_query'   => array(
								array(
									'taxonomy' => 'video_series',
									'field'    => 'slug',
									'terms'    => $filter_slug,
								),
							),
						)
					);
					break;
				case 'topic':
					$results = get_posts(
						array(
							'numberposts' => -1,
							'post_type'   => 'trial_school_videos',
							'tax_query'   => array(
								array(
									'taxonomy' => 'video_topic',
									'field'    => 'slug',
									'terms'    => $filter_slug,
								),
							),
						)
					);
					break;
			}

			echo '<div class="row library-video-container">';
	
			echo '<div class="col-12 mb-5"><h2>Videos</h2></div>';

			if ( '' === $results ) {
				echo '<p>No results found.</p>';
				return null;
			}

			foreach ( $results as $video ) {
	
				$args = array(
					'post_type' => 'trial_school_videos',
					'p'         => $video->ID,
				);

				$the_video = new WP_Query( $args );

				if ( $the_video->have_posts() ) {
					while ( $the_video->have_posts() ) {

						$the_video->the_post();

						$id = get_the_ID();

						$this->render_video( $id );

					}
					wp_reset_postdata();
				}
			}
		} else {
			if ( isset( $_SESSION['results'] ) ) {

				$videos = $_SESSION['results'];
	
				session_destroy();
	
				echo '<div class="row library-video-container">';
	
				echo '<div class="col-12 mb-5"><h2>Videos</h2></div>';
	
				if ( '' === $videos ) {
					echo '<p>No results found.</p>';
				}
	
				foreach ( $videos as $video ) {
	
					$args = array(
						'post_type' => 'trial_school_videos',
						'p'         => $video->ID,
					);
	
					$the_video = new WP_Query( $args );
	
					if ( $the_video->have_posts() ) {
						while ( $the_video->have_posts() ) {
	
							$the_video->the_post();
	
							$id = get_the_ID();
	
							$this->render_video( $id );
	
						}
						wp_reset_postdata();
					}
				}
	
				echo '</div>';
	
			} else {
				$args = array(
					'post_type'      => 'trial_school_videos',
					'post_status'    => 'publish',
					'posts_per_page' => -1,
					'orderby'        => 'title',
					'order'          => 'ASC',
				);
	
				$videos = new WP_Query( $args );
	
				echo '<div class="row library-video-container">';
	
				echo '<div class="col-12 mb-5"><h2>Featured Videos</h2></div>';
	
				while ( $videos->have_posts() ) {
	
					$videos->the_post();
	
					$id = get_the_ID();
	
					if ( true === get_field( 'featured', $id ) ) {
	
						$this->render_video( $id );
	
					} else {
						continue;
					}
				}
	
				wp_reset_postdata();
	
				echo '</div>';
	
				echo '<div class="col-12 mt-5"><h2>All Videos</h2></div>';
				echo '<div id="total-posts-count" class="col-12 mb-5"></div>';
				echo '<div id="all-videos" class="row library-video-container">';

				echo '</div>';
				echo '<div class="col-12 text-center"><button class="btn btn-primary" type="button" id="more-posts-button">Load More Videos</button></div>';
	
			}
		}
	}

	/**
	 * Render_video
	 *
	 * @param int $video_id ID of the video to be rendered.
	 * @return void
	 */
	public function render_video( $video_id, $favorites = false, $atts = null ) {

		$video_thumbnail = get_field( 'thumbnail', $video_id );
		$video_title     = get_the_title( $video_id );

				if ( true === $favorites ) :
					?>
					<div class="col-12 col-lg-6 mb-1">
						<div class="remove-video">
							<form method="POST">
								<?php wp_nonce_field( 'wistia_videos_update', 'wistia_videos_form' ) ?>
								<input type="hidden" name="updated" value="true">
								<input type="hidden" name="video_id" value="<?php echo esc_html( $video_id ); ?>">
								<input type="hidden" name="user_id" value="<?php echo esc_html( $atts['user_id'] ); ?>">
								<!-- <div class="remove-video-btn"></div> -->
								<button type="submit" name="remove_video">
									<span class="fa fa-minus-circle text-danger"></span>
								</button>
							</form>
						</div>

					<?php
				else :
					?>
					<div class="col-6 col-md-4 mb-1">
					<?php
				endif;
			?>
			<a href="<?php echo esc_url( get_the_permalink( $video_id ) ); ?>">
				<div class="video-card text-center">
					<img class="mb-1" src="<?php echo esc_url( $video_thumbnail ); ?>">
					<h4><?php echo esc_html( $video_title ); ?></h4>
				</div>
			</a>
		</div>

		<?php
	}


	/**
	 * Returns all videos by author.
	 *
	 * @return void
	 */
	public function get_author_videos( $post_id ) {

		$args = array(
			'post_type'      => 'trial_school_videos',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
		);

		$videos = new WP_Query( $args );

		echo '<div class="row">';

		while ( $videos->have_posts() ) {
			$videos->the_post();

			$id = get_the_ID();

			$authors = get_field( 'video_authors', $id );

			if ( $authors !== null ) {
				foreach ( $authors as $author ) {
					if ( $author->ID === $post_id ) {
						$this->render_video( $id );
					}
				}
			}
		}

		echo '</div>';

		wp_reset_postdata();

	}

	/**
	 * Returns all videos by author.
	 *
	 * @return void
	 */
    public function get_related_videos( $post_id ) {

		$args = array(
			'post_type'      => 'trial_school_videos',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
		);

		$videos = new WP_Query( $args );

		$post_series    = get_the_terms( $post_id, 'video_series' );
		$post_topics    = get_the_terms( $post_id, 'video_topic' );
		$post_terms     = array();
		$related_videos = array();
		$relevance_arr  = array();

		if ( $post_series ) {
			foreach ( $post_series as $series ) {
				array_push( $post_terms, $series->term_id );
			}
		} 
		
		if ( $post_topics ) {
			foreach ( $post_topics as $topic ) {
				array_push( $post_terms, $topic->term_id );
			}
		}

		echo '<div class="row">';

		while ( $videos->have_posts() ) {
			$relevance_score = 0;
			$videos->the_post();

			$id = get_the_ID();

			$video_series = get_the_terms( $id, 'video_series' );
			$video_topics = get_the_terms( $id, 'video_topic' );

			if ( $id !== $post_id ) {
				if ( $video_series ) {
					foreach ( $video_series as $series ) {
						if ( in_array( $series->term_id, $post_terms, true ) ) {
							//array_push( $related_videos, $id );
							++$relevance_score;
						}
					}
				}
				if ( $video_topics ) {
					foreach ( $video_topics as $topic ) {
						if ( in_array( $topic->term_id, $post_terms, true ) ) {
							//array_push( $related_videos, $id );
							++$relevance_score;
						}
					}
				}
				if ( $relevance_score > 0 ) {
					array_push(
						$relevance_arr,
						array(
							'video_id'  => $id,
							'relevance' => $relevance_score,
						),
					);
					$relevance = array_column( $relevance_arr, 'relevance' );
					array_multisort( $relevance, SORT_DESC, $relevance_arr );
				}
			}
		}

		wp_reset_postdata();

		$related_count = 6;
		$current_video = 0;
		while ( $related_count > 0 ) {
				$this->render_video( $relevance_arr[$current_video]['video_id'] );
				--$related_count;
				++$current_video;
		}
		echo '</div>';

	}
	/**
	 * Handles search forms on frontend.
	 *
	 * @return void
	 */
	public function handle_form() {

		if ( ! isset( $_SESSION ) ) {
			session_start();
		} 

		if ( ! isset( $_POST['wistia_videos_form'] ) || ! wp_verify_nonce( $_POST['wistia_videos_form'], 'wistia_videos_update' ) ) {

			?>

			<div class="error">
				<p>Sorry, your nonce was not correct. Please try again.</p>
			</div>

			<?php
			exit;

		} else {

			/* Handle form data. */

			if ( isset( $_POST['search_authors'] ) ) {
				if ( isset( $_POST['wistia_video_authors'] ) ) {

					$results = get_posts(
						array(
							'numberposts' => -1,
							'post_type'   => 'trial_school_videos',
							'meta_query'  => array(
								array(
									'key'     => 'video_authors',
									'value'   => '"' . sanitize_text_field( wp_unslash( $_POST['wistia_video_authors'] ) ) . '"',
									'compare' => 'LIKE',
								),
							),
						)
					);

					$_SESSION['results'] = $results;

				}
			} elseif ( isset( $_POST['search_topics'] ) ) {
				if ( isset( $_POST['video_topics'] ) ) {

					$results = get_posts(
						array(
							'numberposts' => -1,
							'post_type'   => 'trial_school_videos',
							'tax_query'   => array(
								array(
									'taxonomy' => 'video_topic',
									'field'    => 'term_id',
									'terms'    => sanitize_text_field( wp_unslash( $_POST['video_topics'] ) ),
								),
							),
						)
					);
					$_SESSION['results'] = $results;

				} else {

					$search_slug = strtolower( str_replace( ' ', '-', sanitize_text_field( wp_unslash( $_POST['search_topics'] ) ) ) );

					$results = get_posts(
						array(
							'numberposts' => -1,
							'post_type'   => 'trial_school_videos',
							'tax_query'   => array(
								array(
									'taxonomy' => 'video_topic',
									'field'    => 'slug',
									'terms'    => sanitize_text_field( $search_slug ),
								),
							),
						)
					);
					$_SESSION['results'] = $results;
				}
				if ( isset( $_POST['redirect'] ) ) {
					wp_redirect( site_url( '/video-library' ) );
				}
			} elseif ( isset( $_POST['search_series'] ) ) {
				if ( isset( $_POST['video_series'] ) ) {

					$results = get_posts(
						array(
							'posts_per_page' => -1,
							'post_type'      => 'trial_school_videos',
							'tax_query'      => array(
								array(
									'taxonomy' => 'video_series',
									'field'    => 'term_id',
									'terms'    => sanitize_text_field( wp_unslash( $_POST['video_series'] ) ),
								),
							),
						)
					);
					$_SESSION['results'] = $results;

				} else {

					$search_slug = strtolower( str_replace( ' ', '-', sanitize_text_field( wp_unslash( $_POST['search_series'] ) ) ) );

					$results = get_posts(
						array(
							'numberposts' => -1,
							'post_type'   => 'trial_school_videos',
							'tax_query'   => array(
								array(
									'taxonomy' => 'video_series',
									'field'    => 'slug',
									'terms'    => sanitize_text_field( $search_slug ),
								),
							),
						)
					);
					$_SESSION['results'] = $results;
				}

				if ( isset( $_POST['redirect'] ) ) {
					wp_redirect( site_url( '/video-library' ) );
				}

			} elseif ( isset( $_POST['add_to_favorites'] ) ) {
				if ( isset( $_POST['video_id'] ) && isset( $_POST['user_id'] ) ) {

					global $wpdb;

					$table_name = $wpdb->prefix . 'wistia_videos_favorites';
					$user_id    = $_POST['user_id'];
					$video_id   = $_POST['video_id'];

					$query = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM " . $table_name . " WHERE user_id = %s AND video_id = %s", array( $user_id, $video_id ) ) );

					if ( ! $query ) {
						$wpdb->insert(
							$table_name,
							array(
								'video_id' => $video_id,
								'user_id'  => $user_id,
							),
						);
					}
				}
			} elseif ( isset( $_POST['remove_video'] ) ) {
				if ( isset( $_POST['video_id'] ) && isset( $_POST['user_id'] ) ) {

					$data = array(
						'video_id' => sanitize_text_field( wp_unslash( $_POST['video_id'] ) ),
						'user_id'  => sanitize_text_field( wp_unslash( $_POST['user_id'] ) ),
					);
					$this->db->delete_record( $data, 'favorites' );
				}
			}
		}
	}

	/**
	 * Get_available_authors for the search field.
	 *
	 * @return void
	 */
	public function get_available_authors() {
		$args = array(
			'post_type'      => 'trial_school_videos',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'order'          => 'ASC',
			'orderby'        => 'title',
		);

		$videos = new WP_Query( $args );

		$current_authors = array();

		while ( $videos->have_posts() ) {
			$videos->the_post();

			$post_id       = get_the_ID();
			$video_authors = get_post_meta( $post_id, 'video_authors' );

			foreach ( $video_authors as $author ) {
				foreach ( $author as $author_id ) {
					array_push( $current_authors, $author_id );
				}
			}
		}

		$unique_authors  = array_unique( $current_authors );
		$authors  = array();

		foreach ( $unique_authors as $author ) {

			$the_author = get_post( $author );

			array_push(
				$authors,
				array(
					$the_author->post_title,
				)
			);

		}

		asort( $authors );

		return $authors;

		wp_reset_query();
	}

	/**
	 * Ajax next posts
	 *
	 * @return void
	 */
	public function ajax_next_posts() {

		/* Build query */
		$args = array(
			'post_type'   => 'trial_school_videos',
			'post_status' => 'publish',
			'orderby'     => 'title',
			'order'       => 'ASC',
			'tax_query'   => array(
				array(
					'taxonomy' => 'video_type',
					'field'    => 'slug',
					'terms'    => 'webinar',
				),
			),
		);

		/* Get offset */
		if( ! empty( $_GET['post_offset'] ) ) {
			$offset                 = $_GET['post_offset'];
			$args['offset']         = $offset;
			$args['posts_per_page'] = 12; // Also have to set posts_per_page, otherwise offset is ignored.
		}

		$count_results = '0';
		$query_results = new WP_Query( $args );

		/* Results found */
		if ( $query_results->have_posts() ) {
			$count_results = $query_results->found_posts;
			$results_html  = ''; // Start "saving" results' HTML.
			ob_start();
			while ( $query_results->have_posts() ) {
				$query_results->the_post();
				$id = get_the_ID();
				$this->render_video( $id );
			}
			$results_html = ob_get_clean(); // "Save" results' HTML as variable.
			// echo '<pre class="text-light">';
			// var_dump( $results_html );
			// echo '</pre>';
		}

		$response = array(); // Build ajax response.

		array_push( $response, $results_html, $count_results ); // 1. value is HTML of new posts and 2. is total count of posts.
		echo json_encode( $response );

		die(); // Always use die() in the end of ajax functions.
	}
	
	/**
	 * Allow cross origin requests between https:// and https://www
	 *
	 * @param  mixed $origins
	 * @return void
	 */
	public function add_allowed_origins( $origins ) {
		$origins[] = 'https://www.trialschool.org';
		return $origins;
	}

}
