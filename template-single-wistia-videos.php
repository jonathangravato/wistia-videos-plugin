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

if ( isset( $_POST ) ) {
    if ( isset( $_POST['updated'] ) ) {
        $templates->handle_form();
    }
}

get_header();

$video_hash = get_field( 'hashed_id' );
$authors    = get_field( 'video_authors' );
$topics     = get_the_terms( $post->ID, 'video_topic' );
$series     = get_the_terms( $post->ID, 'video_series' );
$member     = wp_get_current_user();

$args = array(
	'video_id' => get_the_ID(),
	'user_id'  => $member->ID,
);

$is_user_favorite = $db->get_record( $args, 'favorites' );

?>
<div class="single-video-container mb-5">
	<div class="container">
		<div class="row">
			<div class="col-12">
				<div class="video-container">
					<script src="https://fast.wistia.com/embed/medias/<?php echo esc_html( $video_hash ); ?>.jsonp" async></script>
					<script src="https://fast.wistia.com/assets/external/E-v1.js" async></script>
					<div class="wistia_responsive_padding" style="padding:56.25% 0 0 0;position:relative;">
						<div class="wistia_responsive_wrapper" style="height:100%;left:0;position:absolute;top:0;width:100%;">
							<div class="wistia_embed wistia_async_<?php echo esc_html( $video_hash ); ?> videoFoam=true" style="height:100%;position:relative;width:100%">
								<div class="wistia_swatch" style="height:100%;left:0;opacity:0;overflow:hidden;position:absolute;top:0;transition:opacity 200ms;width:100%;">
									<img src="https://fast.wistia.com/embed/medias/<?php echo esc_html( $video_hash ); ?>/swatch" style="filter:blur(5px);height:100%;object-fit:contain;width:100%;" alt="" aria-hidden="true" onload="this.parentNode.style.opacity=1;" />
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row my-3">
			<div class="col-12">
				<?php the_title( '<h1 class="h2">', '</h1>' ); ?>
			</div>
		</div>
		<div class="row mb-3">
			<div class="col-12">
				<div class="video-buttons d-lg-flex">
                    
                    <div>
                        <form class="single-video-form" method="POST">
                            <?php wp_nonce_field( 'wistia_videos_update', 'wistia_videos_form' ); ?>
                            <input type="hidden" name="updated" value="true">
                            <input type="hidden" name="video_id" value="<?php echo esc_html( $post->ID ); ?>">
                            <input type="hidden" name="user_id" value="<?php echo esc_html( $member->ID ); ?>">
							<?php if ( ! $is_user_favorite ) : ?>
                            <div><span class="fa fa-heart mr-1 gold"></span><input type="submit" style="height: 30px; border: none; padding: 0 10px;" name="add_to_favorites" value="Add to Favotires"></div>
							<?php else : ?>
							<div><span class="fa fa-heart mr-1 text-danger"></span><span class="text-danger"><a class="mr-2 text-danger" href="/dashboard">View Favorites</a></span></div>
							<?php endif; ?>
                        </form>
                    </div>
                    <?php
                    if ( $series ) {
                        ?>

                        <form class="single-video-form" method="POST">
                            <?php wp_nonce_field( 'wistia_videos_update', 'wistia_videos_form' ); ?>
                            <input type="hidden" name="updated" value="true">
                            <input type="hidden" name="redirect" value="true">
                            <span class="mx-1">Series: </span>
                            <?php foreach ( $series as $the_series ) { ?>
                                <input type="submit" name="search_series" value="<?php echo esc_html( $the_series->name ); ?>">
                            <?php } ?>
                        </form>

                        <?php
                    }
                    if ( $topics ) {
                        ?>

                        <form class="single-video-form" method="POST">
                            <?php wp_nonce_field( 'wistia_videos_update', 'wistia_videos_form' ); ?>
                            <input type="hidden" name="updated" value="true">
                            <input type="hidden" name="redirect" value="true">
                            <span class="mx-1">Topics: </span>
                            <?php foreach ( $topics as $topic ) { ?>
                                <input type="submit" name="search_topics" value="<?php echo esc_html( $topic->name ); ?>">
                            <?php } ?>
                        </form>

                        <?php
                    }
                    ?>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-8">
				<div class="row">
					<div class="col-12">
						<div class="video-content">
							<h5>Video Description</h5>
							<?php
							if ( get_the_content() ) :
								the_content();
							else :
								echo '<p>No Description</p>';
							endif;
							?>
							
							<div class="video-resources mt-3">
								<h5>Video Resources</h5>
							<?php
							if ( have_rows( 'video_files' ) ) :
								echo '<div class="row">';
								while ( have_rows( 'video_files' ) ) :
									the_row();
									$file = get_sub_field( 'video_file' );
									?>
									<div class="col-md-3">
										<div class="video-info-document text-center">
											<a href="<?php echo esc_url( $file['link'] ); ?>" target="_blank">
												<span class="fa fa-file-text"></span><br><h6 class="text-bold text-capitalize mt-3"><?php echo esc_html( $file['title'] ); ?></h6>
											</a>
										</div>
									</div>
									<?php
								endwhile;
								echo '</div>';
							else :
								?>
								<p>No Video Resources.</p>
								<?php
							endif;
							?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-4">
				
				<?php
				if ( ! $authors ) :
					?>
					<p>There are no authors set for this video.</p>
					<?php
				else :
					if ( count( $authors ) > 1 ) {
						echo '<h5>Authors</h5>';
					} else {
						echo '<h5>Author</h5>';
					}
					foreach ( $authors as $author ) :
						if ( $author->post_excerpt ) {
							$author_title = $author->post_title . ' of ' . $author->post_excerpt;
						} else {
							$author_title = $author->post_title;
						}
						?>
						<div class="video-author mr-2 mb-2 text-center">
							<div class="author-image">
								<?php
								if ( get_the_post_thumbnail( $author->ID, 'thumbnail' ) ) {
									echo get_the_post_thumbnail(
										$author->ID,
										'thumbnail',
										array(
											'alt'   => $author_title,
											'title' => $author_title,
										)
									);
								} else {
									echo '<img src="' . esc_url( site_url() ) . '/wp-content/plugins/trial-school-videos/img/img_placeholder_avatar.jpg" alt="' . esc_html( $author_title ) . '" title="' . esc_html( $author_title ) . '">';
								}
								?>
							</div>
							<div class="author-info">
								<h5 class="text-light h6 mt-1">
									<?php
									echo esc_html( $author->post_title );
									?>
									<br>
									<?php
									if ( $author->excerpt ) :
										?>
									<span class="author-firm">
											<?php
											echo esc_html( $author->post_excerpt );
											?>
										</span>
											<?php
										endif;
									?>
								</h5>
							</div>
							<div class="author-profile">
								<a href="
                                <?php
                                echo esc_url( get_the_permalink( $author->ID ) );
                                ?>
                                " class="btn btn-sm btn-primary">View Profile</a>
							</div>
						</div>
						<?php
					endforeach;
				endif;
				?>
			</div>
		</div>
		<div class="row mt-3">
			<div class="col-12">
				<h5>Related Videos</h5>
				<div class="related-videos">
					<?php $templates->get_related_videos( $post->ID ); ?>
				</div>
			</div>
		</div>
	</div>
</div>

<?php

get_footer();
