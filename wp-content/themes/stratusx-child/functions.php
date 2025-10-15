<?php

require_once(__DIR__ . '/adharcard_form.php');

include(get_stylesheet_directory() . '/lib/widgets.php');
include(get_stylesheet_directory() . '/lib/customField.php');
include(get_stylesheet_directory() . '/lib/shortcodes.php');
include(get_stylesheet_directory() . '/lib/cpt.php');
// include(get_stylesheet_directory() . '/lib/customizer.php');

add_theme_support('automatic-feed-links');

remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');

add_filter('wp_lazy_loading_enabled', '__return_false');
add_filter('wpcf7_ajax_loader', '__return_false');
add_filter('big_image_size_threshold', '__return_false');
add_filter('wpseo_enable_xml_sitemap_transient_caching', '__return_true');

// =----------------------------------------------------------------------------= //
add_action('init', function () {
	if (isset($_GET['h']) && $_GET['h'] == 'true') {
		add_filter('show_admin_bar', '__return_true');
	} else {
		add_filter('show_admin_bar', '__return_false');
	}
});
// =----------------------------------------------------------------------------= //
add_action('init', function () {
	global $wp_post_types;
	$wp_post_types['page']->exclude_from_search = true;
	if (isset($wp_post_types['wpcf7r_action']))
		$wp_post_types['wpcf7r_action']->exclude_from_search = true;
});

// =----------------------------------------------------------------------------= //
add_action('init', function () {
	$shortcode_dir = get_stylesheet_directory() . '/shortcodes/';

	if (is_dir($shortcode_dir)) {
		foreach (glob($shortcode_dir . '*.php') as $file) {
			require_once $file;
		}
	}
});
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

// =----------------------------------------------------------------------------= //
add_action('add_attachment', 'my_set_image_meta_upon_image_upload');
function my_set_image_meta_upon_image_upload($post_ID)
{
	if (wp_attachment_is_image($post_ID)) {
		$my_image_title = get_post($post_ID)->post_title;
		$my_image_title = preg_replace('%\s*[_\s]+\s*%', ' ',  $my_image_title);
		$my_image_title = ucwords($my_image_title);
		$my_image_meta = array(
			'ID'		=> $post_ID,
			'post_title'	=> $my_image_title,
		);
		update_post_meta($post_ID, '_wp_attachment_image_alt', $my_image_title);
		wp_update_post($my_image_meta);
	}
}

// =----------------------------------------------------------------------------= //
add_action('wp_enqueue_scripts', 'wp_optimize_file');
function wp_optimize_file()
{
	wp_dequeue_style('themo-icons');
	wp_deregister_style('themo-icons');

	wp_dequeue_style('thhf-style');
	wp_deregister_style('thhf-style');

	wp_dequeue_style('global-styles');
	wp_deregister_style('global-styles');

	wp_dequeue_style('e-theme-ui-light');
	wp_deregister_style('e-theme-ui-light');

	wp_dequeue_style('wp-block-library');
	wp_deregister_style('wp-block-library');

	wp_dequeue_style('contact-form-7');
	wp_dequeue_script('contact-form-7');

	wp_dequeue_script('google-recaptcha');
	wp_deregister_script('google-recaptcha');
	// 	-----------------------------------------
	wp_enqueue_style('bootstrap', 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.8/css/bootstrap.min.css', array(), '1');
	wp_enqueue_script('utm', 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.8/js/bootstrap.min.js', [], '1');
	// 	----------------------------------------- 
	wp_enqueue_style('common-theme', get_stylesheet_directory_uri() . '/css/common.css', [], '1');
	wp_enqueue_style('roots_app',  get_template_directory_uri() . '/assets/css/app.css', array(), '1');
	wp_enqueue_style('roots_child-style', get_stylesheet_uri(), [], '1', 'all', 9999);
	// 	----------------------------------------- 
	wp_enqueue_style('abha-css', get_stylesheet_directory_uri() . '/css/abha-shortcode.css', array(), '1');
	wp_enqueue_script('abha-js', get_stylesheet_directory_uri() . '/js/abha-shortcode.js', array('jquery'), '1');
	wp_localize_script('abha-js', 'ajax_obj', admin_url('admin-ajax.php'));
	if (is_page(26834)) {
		wp_enqueue_style('abha-table', get_stylesheet_directory_uri() . '/css/abha-table.css', [], '1');
	}

	// 	----------------------------------------- 

	wp_enqueue_script('utm', get_stylesheet_directory_uri() . '/js/utm.js', [], '1');

	// 	----------------------------------------- 
	if (in_array('page-template-alternative', get_body_class())) {
		wp_enqueue_style('alternative', get_stylesheet_directory_uri() . '/css/alternative.css', [],  '1');
	}
	// 	----------------------------------------- 
	if (is_single()) {
		wp_enqueue_style('single-post', get_stylesheet_directory_uri() . '/css/single-css.css', [], '1');
	}
	// 	----------------------------------------- 
	if (is_page_template('temp-dpa.php')) {
		wp_enqueue_style('dpa-style', get_stylesheet_directory_uri() . '/css/dpa.css', [], '1.0');
	}
	// 	-----------------------------------------
	if (in_array('archive', get_body_class()) || is_single() || is_page(23517) || is_search() || is_page(61837) || is_home()) {
		wp_enqueue_style('custom', get_stylesheet_directory_uri() . '/css/custom.css', [], rand());
	}

	wp_enqueue_style(get_post_type() . '-template', get_stylesheet_directory_uri() . '/css/' . get_post_type() . '.css', [],  rand());
	// 	------- 61837 - Testimonial --------------------------------

	if (is_page(61837)) {
		wp_enqueue_style('testimonial', get_stylesheet_directory_uri() . '/css/testimonial.css', [], '1.2');
	}

	wp_enqueue_script('child-script', get_stylesheet_directory_uri() . '/js/script.js', [],  false);
}
// =----------------------------------------------------------------------------= //
function reading_time()
{
	global $post;
	$readingtime = ceil(str_word_count(strip_tags(get_post_field('post_content', $post->ID))) / 200);
	return $readingtime . ($readingtime == 1) ? " minute" : " minutes";
}


// =----------------------------------------------------------------------------= //
add_action('after_setup_theme', 'child_themo_setup');
function child_themo_setup()
{
	if (!current_user_can('administrator') && !is_admin()) {
		show_admin_bar(false);
	}

	register_nav_menus(['footer_navigation' => esc_html__('Footer Navigation', 'stratus'),]);
}
// =----------------------------------------------------------------------------= //
// add_filter('feed_link', 'remove_category_from_feed_url');
// function remove_category_from_feed_url($feed_url)
// {
// 	$feed_url = str_replace('/category/', '/', $feed_url);
// 	return $feed_url;
// }
// =----------------------------------------------------------------------------= //
function pagination_bar()
{
	global $wp_query;
	$total_pages = $wp_query->max_num_pages;
	$big = 999999999;
	if ($total_pages > 1) {
		$current_page = max(1, get_query_var('paged'));
		$pagelink = paginate_links(array(
			'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
			'format' => '?paged=%#%',
			'current' => $current_page,
			'total' => $total_pages,
			'prev_text' => '&laquo;',
			'next_text' => '&raquo;',
			'type' => 'array'
		)); ?>
		<nav class="post-nav">
			<ul class="pagination">
				<?php foreach ($pagelink as $page) {
					echo "<li>" . $page . "</li>";
				} ?>
			</ul>
		</nav>
	<?php } ?>
<?php
}
// =----------------------------------------------------------------------------= //

// =----------------------------------------------------------------------------= //
// add_filter('wpcf7_form_action_url', 'remove_unit_tag');
function remove_unit_tag($url)
{
	$remove_unit_tag = explode('/#', $url);
	$new_url = $remove_unit_tag[0];
	return $new_url;
}
// =----------------------------------------------------------------------------= //
add_filter('wpcf7_validate_text*', 'custom_email_confirmation_validation_filter', 20, 2);
function custom_email_confirmation_validation_filter($result, $tag)
{
	if ('your-website' == $tag->name) {
		$your_website = isset($_POST['your-website']) ? trim($_POST['your-website']) : '';
		$re = '/[(http(s)?):\/\/(www\.)?a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&=]*)/m';
		if (!(preg_match($re, $your_website))) {
			$result->invalidate($tag, "Are you sure this is the correct URL?");
		}
	}
	return $result;
}
// =----------------------------------------------------------------------------= //
// =----------------------------------------------------------------------------= //
function defer_parsing_of_js($url)
{
	if (is_user_logged_in()) return $url; //don't break WP Admin
	if (FALSE === strpos($url, '.js')) return $url;
	if (strpos($url, 'jquery.min.js') !== false) return $url;
	return str_replace(' src', ' defer src', $url);
}
add_filter('script_loader_tag', 'defer_parsing_of_js', 10);
// =----------------------------------------------------------------------------= //
add_action('wp_footer', 'footer_popup');
function footer_popup()
{ ?>

	<style>
		.footer-popup-wrap {
			max-width: 550px;
		}

		.footer-popup {
			padding: 20px;
		}

		.footer-popup .widget-container {
			text-align: center;
			margin-bottom: 12px;
		}

		.footer-popup .widget-container .heading-title {
			margin-bottom: 8px;
		}
	</style>
	<div id="popupBackground"></div>
	<div id="myPopup">
		<a id="closePopup">
			<svg class="x" width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
				<path d="M19.1723 6.4219C19.6129 5.98127 19.6129 5.26877 19.1723 4.83284C18.7316 4.3969 18.0191 4.39221 17.5832 4.83284L12.0051 10.411L6.42227 4.82815C5.98164 4.38752 5.26914 4.38752 4.8332 4.82815C4.39727 5.26877 4.39258 5.98127 4.8332 6.41721L10.4113 11.9953L4.82852 17.5781C4.38789 18.0188 4.38789 18.7313 4.82852 19.1672C5.26914 19.6031 5.98164 19.6078 6.41758 19.1672L11.9957 13.5891L17.5785 19.1719C18.0191 19.6125 18.7316 19.6125 19.1676 19.1719C19.6035 18.7313 19.6082 18.0188 19.1676 17.5828L13.5895 12.0047L19.1723 6.4219Z" fill="white" />
			</svg>
		</a>

		<div class="footer-popup-wrap">
			<div class="footer-popup">
				<div class="widget-heading">
					<div class="widget-container">
						<h2 class="heading-title">Secure Your Hospital’s Future <span style="font-size: 85%;font-weight: 500">Start With Healthray Today!</span></h2>
						<p class="description-text">Get in touch with us today for a personalized consultation and review designed specifically for doctors</p>
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
	<script id="popup-triger-script" type="text/javascript" defer>
		// 	28110 - Thank You Page
		var specificPageIds = [28110];
		currentPageId = <?= get_the_ID(); ?>;
		var popupTriggered = specificPageIds.includes(currentPageId);
		<?php if (is_user_logged_in()) { ?> popupTriggered = true;
		<?php } ?>
		jQuery(document).ready(function() {
			jQuery(window).on('load scroll', function() {
				if (!popupTriggered && jQuery(window).scrollTop() > (jQuery('body').height() / 2)) {
					jQuery('#popupBackground').fadeIn();
					jQuery('#myPopup').fadeIn().css('display', 'flex').hide().fadeIn();
					popupTriggered = true;
				}
			});
			jQuery('#closePopup').click(function() {
				closePopup();
			});
			jQuery(document).keydown(function(event) {
				if (event.keyCode == 27) {
					closePopup();
				}
			});

			function closePopup() {
				jQuery('#popupBackground').fadeOut();
				jQuery('#myPopup').fadeOut();
			}
			jQuery('.page-name').attr('type', 'hidden');
			setTimeout(function() {
				jQuery('.page-name').val('<?= get_the_title(); ?>');
			}, 500)
		});
	</script>
<?php }
// =----------------------------------------------------------------------------= //
add_action('wp_footer', function () {});
// =----------------------------------------------------------------------------= //

add_action('wp_ajax_get_whitepaper_pdf', 'secure_whitepaper_pdf');
add_action('wp_ajax_nopriv_get_whitepaper_pdf', 'secure_whitepaper_pdf');

function secure_whitepaper_pdf()
{
	if (! isset($_POST['nonce']) || ! wp_verify_nonce($_POST['nonce'], 'whitepaper_pdf_nonce')) {
		wp_send_json_error('Unauthorized');
	}

	$post_id = intval($_POST['post_id']);
	if (! $post_id) wp_send_json_error('Invalid post');

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

function custom_generate_toc_collapsible($atts) {
    global $post;

    $atts = shortcode_atts(array(
        'title' => 'On this Page',
        'depth' => 3,
    ), $atts, 'toc');

    $content = $post->post_content;

    preg_match_all('/<h([2-' . intval($atts['depth']) . '])[^>]*>(.*?)<\/h[2-' . intval($atts['depth']) . ']>/i', $content, $matches, PREG_SET_ORDER);

    if (empty($matches)) return '';

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
}
add_shortcode('toc', 'custom_generate_toc_collapsible');
