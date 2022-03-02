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

if ( class_exists( 'Wistia_Videos_DB' ) ) {
	$db = new Wistia_Videos_DB( WISTIA_VIDEOS_NAME, WISTIA_VIDEOS_VERSION );
} else {
	include_once plugin_dir_path( __FILE__ ) . 'class-wistia-videos-templates.php';
	$db = new Wistia_Videos_DB();
}

$thumbnail      = get_the_post_thumbnail_url( $post->ID );
$author_title   = get_the_title( $post->ID );
$author_excerpt = get_the_excerpt( $post->ID );

if ( isset( $_POST ) ) {
    if ( isset( $_POST['updated'] ) ) {
        $templates->handle_form();
    }
}

get_header();

?>

<div class="author-container pb-5">
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <div class="author-header text-center">
                    <?php if ( $thumbnail ) : ?>
                    <img src="<?php echo $thumbnail; ?>" alt="<?php echo $author_title; ?>">
                    <?php else : ?>
                    <img src="<?php echo site_url() . '/wp-content/plugins/wistia-videos1.2.0/img/img_placeholder_avatar.jpg'?>" alt="<?php echo $author_title; ?>">
                    <?php endif; ?>
                    <h5><?php echo $author_title ?></h5>
                    <p class="text-bold"><?php echo $author_excerpt; ?></p>
                </div>
            </div>
            <div class="col-md-9">
                <div class="author-body pb-3">
                    <?php the_content(); ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <hr class="author-section-break">
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="related-videos pt-3">
                    <h3 class="mb-3">Videos by <?php echo $author_title; ?></h3>
                    <?php $templates->get_author_videos( $post->ID ); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php

get_footer();

/**
 * TODO:
 * 1) get all videos where $post->ID = author_id
 *  a) Need all videos to check get_field('authors') against author $post->ID
 */
