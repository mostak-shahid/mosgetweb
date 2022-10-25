<?php 
$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
if ( !is_user_logged_in() ) {
    if (get_post_type( get_the_ID() ) == 'post') {
        $redirect_link = preg_replace('/api/i', 'blog', $actual_link); 
    } 
    else {
        $redirect_link = "https://getwebinc.com/";
    } 

    if ( wp_redirect( $redirect_link ) ) {
        exit;
    }
}
?>
  <?php get_header()?>
   <div class="container">
    <?php if ( have_posts() ) :?>
        <?php while ( have_posts() ) : the_post(); ?>
        
            <div class="card h-100">
                <div class="card-body">
                    <?php if ( has_post_thumbnail() ) : ?>
                    <img src="<?php echo get_the_post_thumbnail_url(get_the_ID(),'full') ?>" class="card-img-top" alt="<?php echo get_the_title() ?>">
                    <?php endif?>
                    <h5 class="card-title"><?php echo get_the_title() ?></h5>
                    <p class="card-text"><?php the_content() ?></p>
                    <?php edit_post_link( __( 'Edit', 'textdomain' ), '<span>', '</span>', null, 'btn btn-primary btn-edit-post-link' );?>
                </div>
            </div>
        
        <?php endwhile;?>
    <div class="pagination-wrapper">
    <?php
    the_posts_pagination( array(
        'show_all' => false,
        'screen_reader_text' => " ",
        'prev_text'          => 'Prev',
        'next_text'          => 'Next',
    ));
    ?>
    </div>
    <?php endif;
    
    /*
	 * Output comments wrapper if it's a post, or if comments are open,
	 * or if there's a comment number – and check for password.
	 */
	if ( ( is_single() || is_page() ) && ( comments_open() || get_comments_number() ) && ! post_password_required() ) :
		?>

		<div class="comments-wrapper section-inner">

			<?php comments_template(); ?>

		</div><!-- .comments-wrapper -->

    <?php endif?>
</div>
<?php get_footer()?>