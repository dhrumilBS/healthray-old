<?php

include(get_stylesheet_directory() . '/lib/widgets.php');
include(get_stylesheet_directory() . '/lib/customField.php');
include(get_stylesheet_directory() . '/lib/cpt.php');

add_action('after_setup_theme', function () {
    if (!current_user_can('administrator') && !is_admin()) {
        show_admin_bar(false);
    }

    register_nav_menus(['footer_navigation' => esc_html__('Footer Navigation', 'stratus'),]);
});

add_action('init', function () {
    $shortcode_dir = get_stylesheet_directory() . '/shortcodes/';

    if (is_dir($shortcode_dir)) {
        foreach (glob($shortcode_dir . '*.php') as $file) {
            require_once $file;
        }
    }
});

// =--------------------------------------------------------------------------= //
// assets
// =--------------------------------------------------------------------------= //

add_action('wp_enqueue_scripts', function () {

    wp_dequeue_style('themo-icons');
    wp_deregister_style('themo-icons');

    wp_dequeue_style('thhf-style');
    wp_deregister_style('thhf-style');

    wp_deregister_style('classic-theme-styles');
    wp_deregister_style('global-styles');

    wp_dequeue_style('e-theme-ui-light');
    wp_deregister_style('e-theme-ui-light');

    wp_dequeue_style('wp-block-library');
    wp_deregister_style('wp-block-library');

    wp_dequeue_style('contact-form-7');
    wp_dequeue_script('contact-form-7');

    wp_dequeue_style('font-awesome-5-all');
    wp_dequeue_style('font-awesome-4-shim');
    wp_dequeue_style('megamenu-fontawesome');
    wp_dequeue_style('font-awesome');
    wp_dequeue_style('hfe-social-share-icons-brands');
    wp_dequeue_style('hfe-social-share-icons-fontawesome');
    wp_dequeue_style('hfe-nav-menu-icons');
    wp_dequeue_style('hfe-widget-blockquote');

    wp_dequeue_script('font-awesome-4-shim');

    $defer_scripts = ['owl.carousal', 'child-script',];
    foreach ($defer_scripts as $handle) {
        wp_script_add_data($handle, 'defer', true);
    }

    // 	-----------------------------------------
    wp_enqueue_style('bootstrap', get_stylesheet_directory_uri() . '/css/bootstrap.min.css', array(), '1');
    wp_enqueue_script('bootstrap', get_stylesheet_directory_uri() . '/js/bootstrap.min.js', array(), '1', true);
    // 	----------------------------------------- 
    wp_enqueue_style('common-theme', get_stylesheet_directory_uri() . '/css/common.css', array(), '1');
    wp_enqueue_style('roots_app', get_template_directory_uri() . '/assets/css/app.css', array(), '1');
    wp_enqueue_style('roots_child-style', get_stylesheet_uri(), array(), '1', 'all', 9999);
    // 	----------------------------------------- 
    wp_enqueue_style('owl.carousal', get_stylesheet_directory_uri() . '/css/owl.carousel.min.css', array(), '1');
    wp_enqueue_script('owl.carousal', get_stylesheet_directory_uri() . '/js/owl.carousel.min.js', array('jquery'), '1', true);

    if (is_single()) {
        wp_enqueue_style('healthray-fonts', 'https://fonts.googleapis.com/css2?family=Sora:wght@600;700&display=swap', array(), null);
        wp_enqueue_style('single-post', get_stylesheet_directory_uri() . '/css/single.css', array(), '');
        wp_enqueue_style('custom', get_stylesheet_directory_uri() . '/css/custom.css', array(), '1');
    }

    // 	-----------------------------------------
    if (in_array('archive', get_body_class()) || is_page(23517) || is_page(32399) || is_search() || is_page(61837) || is_home() || is_page(65487)) {
        wp_enqueue_style('custom', get_stylesheet_directory_uri() . '/css/custom.css', array(), '1');
    }


    wp_enqueue_script('child-script', get_stylesheet_directory_uri() . '/js/script.js', ['jquery', 'contact-form-7'], true);
    wp_localize_script('child-script', 'siteData', [
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'pageId' => get_the_ID(),
        'pageTitle' => get_the_title(get_queried_object_id()),
        'isLoggedIn' => is_user_logged_in(),
        'pageIds' => [28110],
        'homeUrl' => home_url()
    ]);
});

function _version()
{
    if (isset($_GET['ver']) && !empty($_GET['ver'])) {
        return sanitize_text_field($_GET['ver']);
    }
    return '1';
}

add_filter('style_loader_src', function ($src, $handle) {
    $version = _version();
    $src = remove_query_arg('ver', $src);
    $src = add_query_arg('ver', $version, $src);
    return $src;
}, 9999, 2);

add_filter('script_loader_src', function ($src, $handle) {
    $version = _version();
    $src = remove_query_arg('ver', $src);
    $src = add_query_arg('ver', $version, $src);
    return $src;
}, 9999, 2);

// =----------------------------------------------------------------------------= //
// optimization
// =----------------------------------------------------------------------------= //

remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');

add_filter('big_image_size_threshold', '__return_false');
add_filter('wpseo_enable_xml_sitemap_transient_caching', '__return_true');

add_filter('wp_lazy_loading_enabled', '__return_false');
add_filter('wpcf7_ajax_loader', '__return_false');

add_filter('style_loader_tag', function ($html, $handle) {
    return str_replace("rel='stylesheet'", "rel='preload' as='style' onload=\"this.onload=null;this.rel='stylesheet'\"", $html);
}, 10, 2);

add_filter('script_loader_tag', function ($url) {
    if (is_user_logged_in())
        return $url; //don't break WP Admin
    if (FALSE === strpos($url, '.js'))
        return $url;
    if (strpos($url, 'jquery.min.js') !== false)
        return $url;
    return str_replace(' src', ' defer src', $url);
}, 10);

add_action('wp_head', function () {
    if (has_post_thumbnail()) {
        $image_id = get_post_thumbnail_id();
        $image = wp_get_attachment_image_src($image_id, 'full');

        if ($image) {
            echo '<link rel="preload" as="image" href="' . esc_url($image[0]) . '" fetchpriority="high">' . "\n";
        }
    }
}, -50);

add_action('wp_enqueue_scripts', function () {
    foreach (wp_scripts()->queue as $handle) {
        wp_scripts()->add_data($handle, 'defer', true);
    }
}, 999);

add_filter('wpseo_sitemap_entries_per_page', function ($entries) {
    return 5000;
});

// =----------------------------------------------------------------------------= //
// admin
// =----------------------------------------------------------------------------= //

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

add_action('init', function () {
    if (isset($_GET['h']) && $_GET['h'] == 'true') {
        add_filter('show_admin_bar', '__return_true');
    } else {
        add_filter('show_admin_bar', '__return_false');
    }
});

// =----------------------------------------------------------------------------= //
// ajax
// =----------------------------------------------------------------------------= //

add_action('wp_ajax_get_whitepaper_pdf', 'secure_whitepaper_pdf');
add_action('wp_ajax_nopriv_get_whitepaper_pdf', 'secure_whitepaper_pdf');

function secure_whitepaper_pdf()
{
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'whitepaper_pdf_nonce')) {
        wp_send_json_error('Unauthorized');
    }

    $post_id = intval($_POST['post_id']);
    if (!$post_id)
        wp_send_json_error('Invalid post');

    $pdf = get_field('whitepaper_pdf', $post_id);

    if ($pdf) {
        $file_path = get_attached_file($pdf['ID']);
        if (file_exists($file_path)) {
            $file_url = wp_get_attachment_url($pdf['ID']);
            wp_send_json_success(['url' => $file_url]);
        }
    }

    wp_send_json_error('File not found');
}

// =----------------------------------------------------------------------------= //
// schema
// =----------------------------------------------------------------------------= //

add_action('wp_head', function () {
    if (!is_single())
        return;

    global $post;
    if (!$post)
        return;

    $content = $post->post_content;
    preg_match('/(?:youtube\.com\/(?:embed\/|watch\?v=)|youtu\.be\/)([a-zA-Z0-9_\-]+)/', $content, $matches);
    $video_id = $matches[1] ?? null;

    if (!$video_id)
        return;

    $video_url = "https://www.youtube.com/watch?v={$video_id}";
    $embed_url = "https://www.youtube.com/embed/{$video_id}";
    $thumbnail = "https://i.ytimg.com/vi/{$video_id}/hqdefault.jpg";
    $video_desc = get_post_meta($post->ID, 'video_description', true);
    if (!$video_desc) {
        $video_desc = wp_trim_words(wp_strip_all_tags($post->post_content), 30);
    }

    $schema = [
        "@context" => "https://schema.org",
        "@type" => "VideoObject",
        "name" => get_the_title($post->ID),
        "description" => $video_desc,
        "thumbnailUrl" => [$thumbnail],
        "uploadDate" => get_the_date('c', $post->ID), // replace with real API later
        "duration" => "PT2M30S",
        "contentUrl" => $video_url,
        "embedUrl" => $embed_url,
        "mainEntityOfPage" => get_permalink($post->ID),
        "publisher" => [
            "@type" => "Organization",
            "name" => "Healthray",
            "logo" => [
                "@type" => "ImageObject",
                "url" => "https://healthray.com/wp-content/uploads/2024/02/Healthray-Logo.svg"
            ]
        ]
    ];

    echo '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';
});

// =----------------------------------------------------------------------------= //
// popup
// =----------------------------------------------------------------------------= //

add_action('wp_footer', function () { ?>
    <div id="popupBackground"></div>
    <div id="myPopup">
        <a id="closePopup">
            <svg class="x" width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M19.1723 6.4219C19.6129 5.98127 19.6129 5.26877 19.1723 4.83284C18.7316 4.3969 18.0191 4.39221 17.5832 4.83284L12.0051 10.411L6.42227 4.82815C5.98164 4.38752 5.26914 4.38752 4.8332 4.82815C4.39727 5.26877 4.39258 5.98127 4.8332 6.41721L10.4113 11.9953L4.82852 17.5781C4.38789 18.0188 4.38789 18.7313 4.82852 19.1672C5.26914 19.6031 5.98164 19.6078 6.41758 19.1672L11.9957 13.5891L17.5785 19.1719C18.0191 19.6125 18.7316 19.6125 19.1676 19.1719C19.6035 18.7313 19.6082 18.0188 19.1676 17.5828L13.5895 12.0047L19.1723 6.4219Z"
                    fill="white" />
            </svg>
        </a>
        <div class="footer-popup-wrap">
            <div class="footer-popup">
                <div class="widget-heading">
                    <div class="widget-container">
                        <h2 class="heading-title">Secure Your Hospital’s Future <span
                                style="font-size: 85%;font-weight: 500">Start With Healthray Today!</span></h2>
                        <p class="description-text">Get in touch with us today for a personalized consultation and review
                            designed specifically for doctors</p>
                    </div>
                </div>

                <?php
                if (!empty(get_field('popupFormShortcode', 'option'))) {
                    $form = get_field('popupFormShortcode', 'option');
                    echo do_shortcode($form);
                }
                ?>
            </div>
        </div>
    </div>
<?php });


// =----------------------------------------------------------------------------= //
// seo
// =----------------------------------------------------------------------------= //

add_filter('wpseo_canonical', function ($canonical) {
    if (is_paged() && get_query_var('paged') > 1) {
        return get_pagenum_link(get_query_var('paged'));
    }
    return $canonical;
});

add_action('init', function () {
    global $wp_post_types;
    $wp_post_types['page']->exclude_from_search = true;
    if (isset($wp_post_types['wpcf7r_action']))
        $wp_post_types['wpcf7r_action']->exclude_from_search = true;
});

// =----------------------------------------------------------------------------= //
// media
// =----------------------------------------------------------------------------= //

add_filter('wp_get_attachment_image_attributes', function ($attr, $attachment, $size) {
    $count = 0;
    $count++;
    if ($count < 4) {
        $attr['loading'] = 'eager';
        $attr['fetchpriority'] = 'high';
    }
    return $attr;
}, 10, 3);

add_action('add_attachment', function ($post_ID) {
    if (wp_attachment_is_image($post_ID)) {
        $my_image_title = get_post($post_ID)->post_title;
        $my_image_title = preg_replace('%\s*[_\s]+\s*%', ' ', $my_image_title);
        $my_image_title = ucwords($my_image_title);
        $my_image_meta = array(
            'ID' => $post_ID,
            'post_title' => $my_image_title,
        );
        update_post_meta($post_ID, '_wp_attachment_image_alt', $my_image_title);
        wp_update_post($my_image_meta);
    }
});



// =----------------------------------------------------------------------------= //
// toc & blog single progress bar
// =----------------------------------------------------------------------------= //

add_action('wp_body_open', function () {
    if (is_single()) {
        echo '<div id="hr-progress-bar" role="progressbar" aria-label="Reading progress" aria-valuemin="0" aria-valuemax="100"></div>';
    }
});

add_shortcode('toc', function ($atts) {
    global $post;
    $atts = shortcode_atts(array(
        'title' => 'On this Page',
        'depth' => 3,
    ), $atts, 'toc');
    $content = $post->post_content;
    preg_match_all('/<h([2-' . intval($atts['depth']) . '])[^>]*>(.*?)<\/h[2-' . intval($atts['depth']) . ']>/i', $content, $matches, PREG_SET_ORDER);
    if (empty($matches))
        return '';
    $toc = '<div class="custom-toc toc-collapsible">';
    $toc .= '<div class="custom-toc-header">' . esc_html($atts['title']) . ' <span class="toc-toggle-icon">&#9650;</span></div>';
    $toc .= '<div class="custom-toc-content">';
    $prev_level = 1;
    foreach ($matches as $match) {
        $level = intval($match[1]);
        $heading_text = strip_tags($match[2]);
        $id = sanitize_title($heading_text);
        $heading_tag = $match[0];
        $heading_with_id = preg_replace('/<h([1-3])/', '<h$1 id="' . $id . '"', $heading_tag, 1);
        $content = str_replace($heading_tag, $heading_with_id, $content);
        if ($level > $prev_level) {
            $toc .= str_repeat('<ul>', $level - $prev_level);
        } elseif ($level < $prev_level) {
            $toc .= str_repeat('</ul>', $prev_level - $level);
        }
        $toc .= "<li><a href='#$id'>$heading_text</a></li>";
        $prev_level = $level;
    }
    $toc .= str_repeat('</ul>', $prev_level - 1);
    $toc .= '</div></div>';
    return $toc;
});


// =----------------------------------------------------------------------------= //
function pagination_bar()
{
    global $wp_query;

    $total_pages = $wp_query->max_num_pages;
    if ($total_pages <= 1)
        return;

    $current_page = max(1, get_query_var('paged') ? get_query_var('paged') : get_query_var('page'));
    $range = 2;
    $show_items = ($range * 2) + 1;

    echo '<nav class="post-nav" aria-label="Pagination">';
    echo '<ul class="pagination">';
    if ($current_page > 1) {
        echo '<li><a class="prev page-numbers" href="' . get_pagenum_link($current_page - 1) . '">&laquo;</a></li>';
    }

    for ($i = 1; $i <= $total_pages; $i++) {
        if ($i == 1 || $i == $total_pages || ($i >= $current_page - $range && $i <= $current_page + $range)) {
            $active = ($i == $current_page) ? ' active' : '';
            if ($i == 1) {
                echo ($current_page == 1) ? '<li><span class="page-numbers current">1</span></li>' : '<li><a class="page-numbers" href="' . get_pagenum_link(1) . '">1</a></li>';
            } elseif ($i == $total_pages) {
                echo ($current_page == $i) ? '<li><span class="page-numbers current">' . $i . '</span></li>' : '<li><a class="page-numbers" href="' . get_pagenum_link($i) . '">' . $i . '</a></li>';
            } else {
                echo ($i == $current_page) ? '<li><span class="page-numbers current">' . $i . '</span></li>' : '<li><a class="page-numbers" href="' . get_pagenum_link($i) . '">' . $i . '</a></li>';
            }
        } elseif ($i == $current_page - $range - 1 || $i == $current_page + $range + 1) {
            echo '<li><span class="page-numbers">…</span></li>'; // Ellipsis
        }
    }

    if ($current_page < $total_pages) {
        echo '<li><a class="next page-numbers" href="' . get_pagenum_link($current_page + 1) . '">&raquo;</a></li>';
    }

    echo '</ul>';
    echo '</nav>';
}

function reading_time()
{
    global $post;
    $readingtime = ceil(str_word_count(strip_tags(get_post_field('post_content', $post->ID))) / 200);
    return $readingtime . ($readingtime == 1) ? " minute" : " minutes";
}

function show_admin_edit_button()
{
    global $post;
    if (!isset($post->ID))
        return;
    if (is_user_logged_in() && current_user_can('administrator')) {
        $edit_link = get_edit_post_link($post->ID);
        if ($edit_link) {
            echo '<a href="' . esc_url($edit_link) . '" class="edit-post-btn" style="display:inline-block;color:#0073aa;text-decoration:underline;">Edit Post</a>';
        }
    }
}