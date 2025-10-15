<?php

function register_case_studies_cpt()
{
    $labels = [
        'name'               => 'Case Studies',
        'singular_name'      => 'Case Study',
        'menu_name'          => 'Case Studies',
        'name_admin_bar'     => 'Case Study',
        'add_new'            => 'Add New',
        'add_new_item'       => 'Add New Case Study',
        'new_item'           => 'New Case Study',
        'edit_item'          => 'Edit Case Study',
        'view_item'          => 'View Case Study',
        'all_items'          => 'All Case Studies',
        'search_items'       => 'Search Case Studies',
        'not_found'          => 'No case studies found.',
        'not_found_in_trash' => 'No case studies found in Trash.'
    ];

    $args = [
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'capability_type'    => 'post',
        'has_archive'        => true,
        'rewrite'            => ['slug' => 'case-studies'],
        'hierarchical'       => false,
        'menu_position'      => 20,
        'menu_icon'          => 'dashicons-portfolio',
        'supports'           => ['title', 'author', 'editor', 'revisions', 'thumbnail', 'custom-fields'],
        'show_in_rest'       => true,
    ];
    register_post_type('case-studies', $args);
}
add_action('init', 'register_case_studies_cpt');

function register_whitepaper_cpt()
{
    $labels = [
        'name'                  => 'Whitepaper',
        'singular_name'         => 'Whitepaper',
        'menu_name'             => 'Whitepapers',
        'name_admin_bar'        => 'Whitepaper',
        'add_new'               => 'Add New',
        'add_new_item'          => 'Add New Whitepaper',
        'new_item'              => 'New Whitepaper',
        'edit_item'             => 'Edit Whitepaper',
        'view_item'             => 'View Whitepaper',
        'all_items'             => 'All Whitepapers',
        'search_items'          => 'Search Whitepapers',
        'not_found'             => 'No whitepapers found.',
        'not_found_in_trash'    => 'No whitepapers found in Trash.'
    ];

    $args = [
        'labels'                => $labels,
        'public'                => true,
        'publicly_queryable'    => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'query_var'             => true,
        'rewrite'               => ['slug' => 'whitepaper'],
        'capability_type'       => 'page',
        'has_archive'           => true,
        'hierarchical'          => false,
        'menu_position'         => 21,
        'menu_icon'             => 'dashicons-media-document',
        'supports'              => ['title', 'author', 'editor', 'revisions', 'thumbnail', 'custom-fields'],
        'show_in_rest'          => true,
    ];

    register_post_type('whitepaper', $args);

    $cat_labs = [
        'name' => 'Category',
        'singular_name' => 'Category',
        'menu_name' => 'Category',
        'all_items' => 'All Category',
        'edit_item' => 'Edit Category',
        'view_item' => 'View Category',
        'update_item' => 'Update Category',
        'add_new_item' => 'Add New Category',
        'new_item_name' => 'New Category Name',
        'parent_item' => 'Parent Category',
        'parent_item_colon' => 'Parent Category:',
        'search_items' => 'Search Category',
        'not_found' => 'No category found',
        'no_terms' => 'No category',
        'filter_by_item' => 'Filter by category',
        'items_list_navigation' => 'Category list navigation',
        'items_list' => 'Category list',
        'back_to_items' => '← Go to category',
        'item_link' => 'Category Link',
        'item_link_description' => 'A link to a category'
    ];
    register_taxonomy('whitepaper_category', ['whitepaper'], [
        'labels' => $cat_labs,
        'public' => true,
        'hierarchical' => true,
        'show_in_menu' => true,
        'show_in_rest' => true,
    ]);

    $topic_labs = [
        'name' => 'Topic',
        'singular_name' => 'Topic',
        'menu_name' => 'Topic',
        'all_items' => 'All Topic',
        'edit_item' => 'Edit Topic',
        'view_item' => 'View Topic',
        'update_item' => 'Update Topic',
        'add_new_item' => 'Add New Topic',
        'new_item_name' => 'New Topic Name',
        'parent_item' => 'Parent Topic',
        'parent_item_colon' => 'Parent Topic:',
        'search_items' => 'Search Topic',
        'not_found' => 'No Topic found',
        'no_terms' => 'No Topic',
        'filter_by_item' => 'Filter by Topic',
        'items_list_navigation' => 'Topic list navigation',
        'items_list' => 'Topic list',
        'back_to_items' => '← Go to Topic',
        'item_link' => 'Topic Link',
        'item_link_description' => 'A link to a Topic'
    ];
    register_taxonomy('whitepaper_topic', ['whitepaper'], [
        'labels' => $topic_labs,
        'public' => true,
        'hierarchical' => true,
        'show_in_menu' => true,
        'show_in_rest' => true,
    ]);
}
add_action('init', 'register_whitepaper_cpt');

function register_event_cpt()
{
    $labels = [
        'name'                  => _x('Events', 'Post Type General Name', 'stratus'),
        'singular_name'         => _x('Event', 'Post Type Singular Name', 'stratus'),
        'menu_name'             => __('Events', 'stratus'),
        'name_admin_bar'        => __('Event', 'stratus'),
        'add_new_item'          => __('Add New Event', 'stratus'),
        'edit_item'             => __('Edit Event', 'stratus'),
        'new_item'              => __('New Event', 'stratus'),
        'view_item'             => __('View Event', 'stratus'),
        'search_items'          => __('Search Events', 'stratus'),
        'not_found'             => __('No events found', 'stratus'),
        'not_found_in_trash'    => __('No events found in Trash', 'stratus'),
    ];

    $args = [
        'label'                 => __('Event', 'stratus'),
        'labels'                => $labels,
        'supports'              => ['title', 'editor', 'excerpt', 'thumbnail'],
        'public'                => true,
        'has_archive'           => true,
        'rewrite'               => ['slug' => 'events'],
        'show_in_rest'          => true,
        'menu_icon'             => 'dashicons-calendar-alt'
    ];

    register_post_type('event', $args);

    register_taxonomy(
        'event_category',
        'event',
        [
            'label' => __('Event Categories', 'stratus'),
            'rewrite' => ['slug' => 'event-category'],
            'hierarchical' => true,
            'show_in_rest' => true,
        ]
    );
    register_taxonomy(
        'event_status',
        'event',
        [
            'label' => __('Event Status', 'stratus'),
            'rewrite' => ['slug' => 'event-status'],
            'hierarchical' => true,
            'show_in_rest' => true,
        ]
    );
}
add_action('init', 'register_event_cpt');