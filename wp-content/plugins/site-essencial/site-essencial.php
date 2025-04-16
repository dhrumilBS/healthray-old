<?php
/*
* Plugin Name: Site Essencial
* Description: Site Essencial is a plugin that adds essential features to your WordPress site.
* Version: 1.0.0
* Author: Site Essencial
*/

add_theme_support('automatic-feed-links');

remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');

add_filter('wp_lazy_loading_enabled', '__return_false');
add_filter('wpcf7_ajax_loader', '__return_false');

add_filter('manage_page_posts_columns', function ($columns) {
    return array_merge($columns, ['thumb-img' => __('Image', 'textdomain')]);
});
add_action('manage_page_posts_custom_column', function ($column_key, $post_id) {
    if ($column_key == 'thumb-img') {
        $feat_image = wp_get_attachment_url(get_post_thumbnail_id($post_id));
        if ($feat_image) {
            echo "<img src='" . $feat_image . "' width='80' height='80' />";
        } else {
            echo "Not Set";
        }
    }
}, 10, 2);
add_action('add_attachment', 'site_essential_my_set_image_meta_upon_image_upload');
function site_essential_my_set_image_meta_upon_image_upload($post_ID)
{
    if (wp_attachment_is_image($post_ID)) {
        $my_image_title = get_post($post_ID)->post_title;
        $my_image_title = preg_replace('%\s*[_\s]+\s*%', ' ',  $my_image_title);
        $my_image_title = ucwords($my_image_title);
        $my_image_meta = array(
            'ID'        => $post_ID,
            'post_title'    => $my_image_title,
        );
        update_post_meta($post_ID, '_wp_attachment_image_alt', $my_image_title);
        wp_update_post($my_image_meta);
    }
}

// Auto add featured image
// add_action('the_post', 'site_essential_auto_set_featured_image');
function site_essential_auto_set_featured_image()
{
    global $post;

    if (get_field('best_software_statecity_image')) {
        $statecity_img = get_field('best_software_statecity_image');
        set_post_thumbnail($post, $statecity_img);
    }

    $featured_image_exists = has_post_thumbnail($post->ID);
    if (!$featured_image_exists) {
        $attached_image = get_children("post_parent=$post->ID&post_type=attachment&post_mime_type=image&numberposts=1");
        if ($attached_image) {
            foreach ($attached_image as $attachment_id => $attachment) {
                set_post_thumbnail($post->ID, $attachment_id);
            }
        }
    }
}


add_action('after_setup_theme', 'site_essential_site_essential_setup');
function site_essential_setup()
{
    if (!current_user_can('administrator') && !is_admin()) {
        show_admin_bar(false);
    }
}

add_filter('feed_link', 'site_essential_remove_category_from_feed_url');
function site_essential_remove_category_from_feed_url($feed_url)
{
    $feed_url = str_replace('/category/', '/', $feed_url);
    return $feed_url;
}
function site_essential_remove_pages_from_search()
{
    global $wp_post_types;
    $wp_post_types['page']->exclude_from_search = true;
    if (isset($wp_post_types['wpcf7r_action']))
        $wp_post_types['wpcf7r_action']->exclude_from_search = true;
}

function site_essential_defer_parsing_of_js($url)
{
    if (is_user_logged_in()) return $url; //don't break WP Admin
    if (FALSE === strpos($url, '.js')) return $url;
    if (strpos($url, 'jquery.min.js') !== false) return $url;
    return str_replace(' src', ' defer src', $url);
}
add_filter('script_loader_tag', 'site_essential_defer_parsing_of_js', 10);
