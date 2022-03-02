<?php

/**
 * All WP Database hooks
 */
class Wistia_Videos_DB {

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
	 * Init the admin view and enqueue styles and scripts.
	 *
	 * @return void
	 */
	public function init() {

		$this->build_tables();

	}

	/**
	 * Build database tables for Wistia Videos
	 *
	 * @return void
	 */
	public function build_tables() {

		global $wpdb;

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$charset_collate = $wpdb->get_charset_collate();

		// Create the tables when the plugin is activated.
		$videos_table = $wpdb->prefix . 'wistia_videos_favorites';
		$sql          = "CREATE TABLE $videos_table (
							id bigint(20) NOT NULL AUTO_INCREMENT,
							video_id varchar(45) DEFAULT '' NOT NULL,
                            user_id varchar(45) DEFAULT '' NOT NULL,
							PRIMARY KEY  (id)
						) $charset_collate;";
		dbDelta( $sql );

	}

	/**
	 * Get all records from db table.
	 *
	 * @param  string $type      Defines the table we want to get all records from.
	 * @return array $results   All records from db table.
	 */
	public function get_records( $data, $type ) {

		global $wpdb;

		if ( ! $type ) {
			return false;
		}

		switch ( $type ) {
			case 'favorites':
				$results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}wistia_videos_favorites WHERE user_id = $data" );
				break;
			default:
				echo 'No table reference type set.';
				break;
		}

		if ( ! empty( $results ) ) {
			return $results;
		} else {
			return false;
		}

	}

	/**
	 * Get a single record from db table.
	 *
	 * @param  array  $data      Data array which includes values used to search for record type.
	 * @param  string $type     Defines the table we want to get all records from.
	 * @return array  $results   a single record from db table.
	 */
	public function get_record( $data, $type ) {

		global $wpdb;

		if ( ! $type ) {
			return false;
		}

		switch ( $type ) {
			case 'favorites':

				$user_id  = $data['user_id'];
				$video_id = $data['video_id'];

				$results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}wistia_videos_favorites WHERE video_id = $video_id AND user_id = $user_id" );
				break;
			default:
				echo 'No table reference type set.';
				break;
		}

		if ( ! empty( $results ) ) {
			return $results;
		} else {
			return false;
		}

	}

	/**
	 * Add record into db table.
	 *
	 * @param array  $data      Array of fields to enter into new record.
	 * @param string $type      Defines the table we want to get all records from.
	 * @return void
	 */
	public function add_record( $data, $type ) {

		global $wpdb;

		if ( ! $type || ! $data ) {
			return false;
		}

		switch ( $type ) {
			case 'favorites':
				$table_name = $wpdb->prefix . 'wistia_videos_favorites';
				$user_id    = $data['user_id'];
				$video_id   = $data['video_id'];

				$query = $wpdb->get_var( 'SELECT COUNT(*) FROM {$wpdb->prefix}wistia_videos_favorites WHERE user_id = $user_id and video_id = $video_id' );

				if ( '' !== $data ) {
					if ( null === $query ) {
						$wpdb->insert(
							$table_name,
							array(
								'video_id' => $video_id,
								'user_id'  => $user_id,
							)
						);
					}
				}
				break;

			default:
				echo 'No table reference type set.';
				break;

		}

	}

	/**
	 * Update record in db table.
	 *
	 * @param  string $type        Type of object we want to update.
	 * @param  string $column      Database columm we want to update.
	 * @param  string $video_id    Array of values we want to update.
	 * @param  string $new_value   The updated value for the selected column.
	 * @return void
	 */
	public function update_record( $type, $column, $video_id, $new_value ) {

		global $wpdb;

		if ( ! $type || ! $column || ! $video_id || ! $new_value ) {
			return false;
		}

		switch ( $type ) {
			case 'favorites':
				$table_name = $wpdb->prefix . 'wistia_videos';
				if ( 'title' === $column ) {
					$wpdb->update( $table_name, array( 'title' => $new_value ), array( 'video_id' => $video_id ) );
				} elseif ( 'description' === $column ) {
					$wpdb->update( $table_name, array( 'video_description' => $new_value ), array( 'video_id' => $video_id ) );
				}
				break;
			default:
				echo 'No table reference type set.';
				break;
		}

	}

	/**
	 * Delete record in db table.
	 *
	 * @param  string $data     ID of the record we want to delete.
	 * @param  string $type     Defines the table we want to work from.
	 * @return void
	 */
	public function delete_record( $data, $type ) {

		global $wpdb;

		if ( ! $data ) {
			return false;
		}

		switch ( $type ) {
			case 'favorites':
				$table_name = $wpdb->prefix . 'wistia_videos_favorites';
				$wpdb->delete(
					$table_name,
					array(
						'video_id' => $data['video_id'],
						'user_id'  => $data['user_id'],
					),
					array(
						'%s',
						'%s',
					),
				);
				break;
			default:
				echo 'No table reference type set.';
				break;
		}

	}

}
