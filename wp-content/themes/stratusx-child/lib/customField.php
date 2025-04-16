<?php
if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group([
        'key' => 'group_custom_text_field',
        'title' => 'Custom Field Section',
        'fields' => [
            [
                'key' => 'field_custom_text',
                'label' => 'PopUp Form Shortcode',
                'name' => 'popupFormShortcode',
                'type' => 'text',
                'required' => 0,
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'option-page',
                ],
            ],
        ],
        'label_placement' => 'left',
    ]);
    acf_add_local_field_group([
        'key' => 'repeater_image_field',
        'title' => 'Image Slider Section',
        'fields' =>  [
            [
                'key' => 'field_image_slider',
                'label' => 'Image Slider',
                'name' => 'imageSlider',
                'type' => 'repeater',
                'required' => 0,
                'sub_fields' => [
                    [
                        'key' => 'field_image',
                        'label' => 'Image',
                        'name' => 'image',
                        'type' => 'image',
                    ],
                    [
                        'key' => 'field_image_caption',
                        'label' => 'Image caption',
                        'name' => 'imageCaption',
                        'type' => 'wysiwyg',
                    ]
                ]
            ]

        ],

        'location' => [
            [
                [
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'option-page',
                ],
            ],
        ],
        'label_placement' => 'left',
    ]);
}

if (function_exists('acf_add_options_page')) {
    acf_add_options_page(array(
        'page_title' => 'Theme Options',
        'menu_title' => 'Options',
        'menu_slug' => 'option-page',
        'capability' => 'edit_posts',
        'redirect' => false,
    ));
}
