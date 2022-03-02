<?php
/**
 * Template for Wistia Videos Rendering
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

if ( class_exists( 'Wistia_Videos_Templates' ) ) {
	$templates = new Wistia_Videos_Templates( WISTIA_VIDEOS_NAME, WISTIA_VIDEOS_VERSION );
} else {
	include_once plugin_dir_path( __FILE__ ) . 'class-wistia-videos-templates.php';
	$templates = new Wistia_Videos_Templates();
}

get_header();

?>

<section class="memberSection facultyMembers">
		<div class="container">

            <div class="wistia-videos-search pb-5">

            <?php $templates->render_search_bar(); ?>

            </div>

            <?php $templates->render_video_grid(); ?>

        </div>

</section>

<?php

get_footer();
