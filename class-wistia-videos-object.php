<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://trialschool.org
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
 * @author     Jonathan Gravato <jonathan@newsomelaw.com>
 */

 /**
  * Wistia Videos Object class to deal with API and db functions.
  */
class Wistia_Videos_Object {

	public function __construct() {

		require_once plugin_dir_path( __FILE__ ) . 'class-wistia-videos-api.php';

		$api = new Wistia_Videos_API( 'WISITA_VIDEOS_NAME', 'WISITA_VIDEOS_VERSION' );

		require_once 'vendor/autoload.php';

		$api_key = $api->get_api_key();

		/* Instantiate the Wistia API */
		$params = array(
			'token' => $api_key,
		);

		$this->client = new \Automattic\Wistia\Client( $params );

	}

	/**
	 * Video count in Wistia Account.
	 *
	 * @return int $videos_count  The amount of videos in the account.
	 */
	public function video_count() {

		$wistia_account = $this->client->show_account();
		$videos_count   = $wistia_account->mediaCount;

		return $videos_count;

	}

	/**
	 * Instantiates the Wistia API and gets all videos.
	 *
	 * @return void
	 */
	public function get_wistia_videos() {

		global $wp_object_cache;

		$wp_object_cache->flush();

		$video_count = $this->video_count();
		$pages       = 1;

		if ( $video_count > 100 ) {

			do {

				$remainder   = $video_count - 100;
				$video_count = $remainder;
				$pages       = ++$pages;

			} while ( $remainder > 100 );

		}

		$page = 1;

		$videos_arr = array();

		while ( $pages > 0 ) {

			$params = array(
				'page' => $page,
			);

			$wistia_videos_obj = $this->client->list_medias( $params );

			foreach ( $wistia_videos_obj as $video ) {
				array_push( $videos_arr, $video );
			}

			$pages = --$pages;
			$page  = ++$page;

		}

		return $videos_arr;

	}

}
