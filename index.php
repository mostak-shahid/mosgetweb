<?php get_header()?>
   <div class="container">
    <?php if ( have_posts() ) :?>
    
    <div class="row">
        <?php while ( have_posts() ) : the_post(); ?>
        <div class="col-lg-3 col-sm-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <?php if ( has_post_thumbnail() ) : ?>
                    <img src="<?php echo get_the_post_thumbnail_url(get_the_ID(),'full') ?>" class="card-img-top" alt="<?php echo get_the_title() ?>">
                    <?php endif?>
                    <h5 class="card-title"><?php echo get_the_title() ?></h5>
                    <p class="card-text"><?php echo get_the_excerpt() ?></p>
                    <?php edit_post_link( __( 'Edit', 'textdomain' ), '<span>', '</span>', null, 'btn btn-primary btn-edit-post-link' );?>
                </div>
            </div>
        </div>
        <?php endwhile;?>
    </div>
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
    <?php endif;?>
</div>
<?php get_footer()?>