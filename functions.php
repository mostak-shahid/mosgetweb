<?php

function mosgetweb_enqueue_scripts () {
    wp_enqueue_script( 'jquery' );

    wp_register_style( 'bootstrap.min', 'https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css' );
    wp_enqueue_style( 'bootstrap.min' );

    wp_register_style( 'style', get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'style' );

    wp_register_script( 'bootstrap.min', 'https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.bundle.min.js', 'jquery' );
    wp_enqueue_script( 'bootstrap.min' );
}
add_action( 'wp_enqueue_scripts', 'mosgetweb_enqueue_scripts' );

function mosgetweb_admin_enqueue_scripts () {
    wp_register_style( 'admin-style', get_template_directory_uri() . '/admin-style.css' );
    wp_enqueue_style( 'admin-style' );
}
add_action( 'admin_enqueue_scripts', 'mosgetweb_admin_enqueue_scripts' );

function mosgetweb_theme_add_editor_styles() {
    add_editor_style( 'admin-style.css' );
}
add_action( 'admin_init', 'mosgetweb_theme_add_editor_styles' );

/**/
// hooks your functions into the correct filters
function mos_add_mce_button() {
    // check user permissions
    if ( !current_user_can( 'edit_posts' ) &&  !current_user_can( 'edit_pages' ) ) {
        return;
    }
    // check if WYSIWYG is enabled
    if ( 'true' == get_user_option( 'rich_editing' ) ) {
        add_filter( 'mce_external_plugins', 'mos_add_tinymce_plugin' );
        add_filter( 'mce_buttons', 'mos_register_mce_button' );
    }
}
add_action( 'admin_head', 'mos_add_mce_button' );
// declare a script for the new button
// the script will insert the shortcode on the click event
function mos_add_tinymce_plugin( $theme_array ) {
    $theme_array['mos_mce_button'] = get_template_directory_uri() . '/js/mos-mce-button.js';
    return $theme_array;
}
// register new button in the editor
function mos_register_mce_button( $buttons ) {
    array_push( $buttons, 'mos_mce_button' );
    return $buttons;
}
//Button shortcode
function mos_button_fnc( $atts = array(), $content = '' ) {
    $atts = shortcode_atts( array(
        'url' => '#',
        'title' => 'Button',
        'class' => '',
    ), $atts, 'mos-embed' );
    ob_start();
    ?>
    <div class = "button-wrapper">
    <a class = "<?php echo $atts['class'] ?>" href = "<?php echo $atts['url'] ?>"><?php echo $atts['title'] ?></a>
    </div>
    <?php $html = ob_get_clean();
    return $html;
}
add_shortcode( 'mos-button', 'mos_button_fnc' );
// Disable WordPress sanitization to allow more than just $allowedtags from /wp-includes/kses.php.
remove_filter( 'pre_user_description', 'wp_filter_kses' );
// Add sanitization for WordPress posts.
add_filter( 'pre_user_description', 'wp_filter_post_kses' );

if ( ! function_exists( 'mosgetweb_setup' ) ) :

function mosgetweb_setup() {
    add_theme_support( 'title-tag' );

    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'woocommerce' );
    add_theme_support( 'wc-product-gallery-zoom' );
    add_theme_support( 'wc-product-gallery-lightbox' );
    add_theme_support( 'wc-product-gallery-slider' );
    //add_image_size( string $name, int $width, int $height, bool|array $crop = false );

    load_theme_textdomain( 'theme', get_template_directory() . '/languages' );
    register_nav_menus( array(
        'mainmenu' => __( 'Main Menu', 'mosgetweb' ),
        'mobilemenu' => __( 'Mobile Menu', 'mosgetweb' ),
    ) );
    add_theme_support( 'html5', array(
        'search-form', 'comment-form', 'comment-list', 'gallery', 'caption'
    ) );
    add_theme_support( 'post-formats', array(
        'aside', 'image', 'video', 'audio', 'quote', 'link', 'gallery', 'chat',
    ) );
}
endif;
add_action( 'after_setup_theme', 'mosgetweb_setup' );

//Sitemap

add_action( 'rest_api_init', 'fr_any_post_api_route' );
function fr_any_post_api_route() {
    register_rest_route( 'fr-all-url-api-route/v2', '/any-post-type/', array(
        'methods' => 'GET',
        'callback' => 'fr_get_content_by_slug',
        'args' => array()
    ) );
}

/**
*
* Get content by slug
*
* @param WP_REST_Request $request
* @return WP_REST_Response
*/
//callback function

function fr_get_content_by_slug() {
    $page_on_front = get_option( 'page_on_front' );
    $args = array(
        'post_type' => array( 'page', 'post' ),
        'post_status' => 'publish',
        'nopaging' => true,
        'orderby'   => 'meta_value',
        'order' => 'ASC',
    );
    $query = new WP_Query( $args );
    $posts = $query->get_posts();
    $output = array();
    $n = 0;
    foreach ( $posts as $post ) {
        $output[$n] = array(
            'id' => $post->ID,
            'modified' => $post->post_modified
        );
        if ( get_post_type( $post->ID ) == 'post' ) {
            $output[$n]['title'] = 'blog/'.$post->post_name;
        } else {
            $output[$n]['title'] = $post->post_name;
        }

        if ( $post->ID == $page_on_front ) {
            $output[$n]['title'] = '';
        }
        $n++;
    }
    wp_send_json( $output );
}
