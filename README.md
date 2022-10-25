# Mos Getweb
## Base theme for getweb website

### Functions are used
- Add editor style
- Add editor button
- Rest API  
**Add editor style**
```
function mosgetweb_theme_add_editor_styles() {
    add_editor_style( 'admin-style.css' );
}
add_action( 'admin_init', 'mosgetweb_theme_add_editor_styles' );
```
 
**Add editor button**
###### function.php
```
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
```
###### mos-mce-button.js
```
(function() {
       tinymce.PluginManager.add('mos_mce_button', function( editor, url ) {
           editor.addButton('mos_mce_button', {
                       text: 'Button',
                       icon: false,
                       onclick: function() {
                         // change the shortcode as per your requirement
                          editor.insertContent('[mos-button url="#" title="Button" class="button"]');
                      }
             });
       });
})();
```
**Rest API**
```
add_action( 'rest_api_init', 'fr_any_post_api_route' );
function fr_any_post_api_route() {
    register_rest_route( 'fr-all-url-api-route/v2', '/any-post-type/', array(
        'methods' => 'GET',
        'callback' => 'fr_get_content_by_slug',
        'args' => array()
    ) );
}
```
```
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
```