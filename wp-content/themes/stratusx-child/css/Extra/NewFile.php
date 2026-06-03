<?php

if (in_array('page-template-template-speciality', get_body_class())) {
// wp_enqueue_style('specality', get_stylesheet_directory_uri() . '/css/speciality.css', [], '1');
}


if (is_author()) {
// wp_enqueue_style('author-template', get_stylesheet_directory_uri() . '/css/author.css', [], '');
}


if (is_page(26834)) {
// wp_enqueue_style('abha-table', get_stylesheet_directory_uri() . '/css/abha-table.css', [], '1');
}

// 	----------------------------------------- 
if (!(is_user_logged_in())) {
// wp_enqueue_script('utm', get_stylesheet_directory_uri() . '/js/utm.js', [], '1');
}

// 	----------------------------------------- 
if (in_array('page-template-alternative', get_body_class())) {
// wp_enqueue_style('alternative', get_stylesheet_directory_uri() . '/css/alternative.css', [],  '1');
}
// 	----------------------------------------- 
if (is_single()) {
// wp_enqueue_style('single-post', get_stylesheet_directory_uri() . '/css/single-css.css', [], '1');
}
// 	----------------------------------------- 
if (is_page_template('temp-dpa.php')) {
// wp_enqueue_style('dpa-style', get_stylesheet_directory_uri() . '/css/dpa.css', [], '1.0');
}
// 	-----------------------------------------
if (in_array('archive', get_body_class()) || is_single() || is_page(23517) || is_page(32399) || is_search() || is_page(61837) || is_home() || is_page(65487)) {
wp_enqueue_style('custom', get_stylesheet_directory_uri() . '/css/custom.css', [], '1');
}

// wp_enqueue_style(get_post_type() . '-template', get_stylesheet_directory_uri() . '/css/' . get_post_type() . '.css', [],  '1.2';
// 	------- 61837 - Testimonial --------------------------------

if (is_page(61837)) {
// wp_enqueue_style('testimonial', get_stylesheet_directory_uri() . '/css/testimonial.css', [], '1.2');
}
// 	-------------------page-template-temp-faqs-----------------------------------------
if (in_array('page-template-temp-faqs', get_body_class())) {
// wp_enqueue_style('faqs', get_stylesheet_directory_uri() . '/css/faqs.css', [], '1.2');
}

?>