<?php
/**
 * _s Theme Customizer.
 *
 * @package _s
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function _s_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';
}
//add_action( 'customize_register', '_s_customize_register' );

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function _s_customize_preview_js() {
	wp_enqueue_script( '_s_customizer', get_template_directory_uri() . '/js/customizer.js', array( 'customize-preview' ), '20151215', true );
}
//add_action( 'customize_preview_init', '_s_customize_preview_js' );


// Add the theme configuration
Stratus_Kirki::add_config( 'stratus_theme', array(
    'capability'    => 'edit_theme_options',
    'option_type'   => 'theme_mod',
) );

// Create a Panel for our theme options.
Stratus_Kirki::add_panel( 'th_options', array(
    'priority'    => 10,
    'title'       => __( 'Theme Options', 'stratus' ),
    'description' => __( 'My Description', 'stratus' ),
) );

// HEADER & FOOTER SECTION

//remove_theme_mod( 'thmv_standard_header_switch');

$thmv_standard_header = 'on'; // Default to on for backwards compatibility.

// Turn off Standard for new versions.
if(thmv_get_theme_version() >= THMV_MIN_HFE_THEME_VERSION){
    $thmv_standard_header = '';
}


Stratus_Kirki::add_section( 'header_footer', array(
    'title'      => esc_attr__( 'Header & Footer', 'stratus' ),
    'priority'   => 2,
    'panel'          => 'th_options',
    'capability' => 'edit_theme_options',
) );

// HEADER & FOOTER SECTION - HFB Switch
/*Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'        => 'switch',
    'settings'    => 'thmv_hfb_switch',
    'label'       => esc_html__( 'Custom Header & Footer', 'stratus' ),
    //'description' => esc_html__( 'Show', 'stratus' ),
    'section'     => 'header_footer',
    'default'     => 'off',
    'priority'    => 10,
    'choices'     => array(
        'on'  => esc_attr__( 'Showing', 'stratus' ),
        'off' => esc_attr__( 'Hiding', 'stratus' ),
    ),
) );*/

// HEADER & FOOTER SECTION - HFB Notice
Stratus_Kirki::add_field( 'stratus_theme', [
    'type'        => 'custom',
    'settings'    => 'thmv_hfb_notice',
    'label'       => esc_html__( 'Global Templates', 'stratus' ),
    'section'     => 'header_footer',
    'default' => '<div class="th-theme-support">'.esc_html__('You can now build your header and footer in Elementor. Go to Dashboard > Stratus > Global Templates.', 'stratus').'</div><div class="th-theme-support">' . sprintf(__('<p class=""> <a href="%1$s" target="_blank">Learn more</p>', 'stratus'), 'https://themovation.helpscoutdocs.com/article/311-custom-header-footer') . '</div>',
    'priority'    => 10,

] );

// HEADER & FOOTER SECTION - Standard Switch
Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'        => 'switch',
    'settings'    => 'thmv_standard_header_switch',
    'label'       => esc_html__( 'Standard Header & Footer', 'stratus' ),
    'section'     => 'header_footer',
    'default'     => $thmv_standard_header,
    'priority'    => 10,
    'choices'     => array(
        'on'  => esc_attr__( 'Showing Settings', 'stratus' ),
        'off' => esc_attr__( 'Hiding Settings', 'stratus' ),
    ),
) );

// HEADER & FOOTER SECTION - Standard Header Switch Callback
$thmv_standard_header_switch_callback = array(
        array(
            'setting'  => 'thmv_standard_header_switch',
            'operator' => '==',
            'value'    => true,
        ),
        array(
            'setting'  => 'thmv_standard_header_switch',
            'operator' => '==',
            'value'    => 'on',
        )
);


Stratus_Kirki::add_field( 'stratus_theme', [
    'type'        => 'custom',
    'settings'    => 'thmv_standard_header_notice',
    'section'     => 'header_footer',
    'default' => '<div class="th-theme-support">'.esc_html__('Applies to standard header & footer only.', 'stratus').'</div><div class="th-theme-support">' . sprintf(__('<p class=""> <a href="%1$s" target="_blank">Learn more</p>', 'stratus'), 'https://themovation.helpscoutdocs.com/article/311-custom-header-footer#standard-header-footer') . '</div>',
    'priority'    => 10,
    'active_callback'    => array(
        array(
            $thmv_standard_header_switch_callback
        )
    ),
] );



// HEADER & FOOTER SECTION - Logo
Stratus_Kirki::add_field( 'stratus_theme', [
    'type'        => 'custom',
    'settings'    => 'thmv_customizer_header_logo',
    'section'     => 'header_footer',
    'default'     => '<div class="thmv-customizer-heading">' . __('Logo', 'stratus') . '</div><div class="thmv-customizer-divider"><span></span></div>',
    'priority'    => 10,
    'active_callback'    => array(
        $thmv_standard_header_switch_callback
    ),
] );


// HEADER & FOOTER SECTION - Logo - Enable Retina Support.
Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'        => 'switch',
    'settings'    => 'themo_retinajs_logo',
    'label'       => esc_html__( 'High-resolution/Retina Logo Support', 'stratus' ),
    'description' => esc_html__( 'Automatically serve up your high-resolution logo to devices that support them.', 'stratus' ),
    'section'     => 'header_footer',
    'default'     => '',
    'priority'    => 10,
    'choices'     => array(
        'on'  => esc_attr__( 'Enable', 'stratus' ),
        'off' => esc_attr__( 'Disable', 'stratus' ),
    ),
    'active_callback'    => array(
        $thmv_standard_header_switch_callback
    ),
) );

// HEADER & FOOTER SECTION - Logo - Height
Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'        => 'number',
    'settings'    => 'themo_logo_height',
    'label'       => esc_html__( 'Logo Height', 'stratus' ),
    'description' => esc_html__( 'Set height and then \'Publish\' BEFORE uploading your logo.', 'stratus' ),
    'section'     => 'header_footer',
    'default'     => 100,
    'choices'     => array(
        'min'  => '10',
        'max'  => '300',
        'step' => '1',
    ),
    'output' => array(
        array(
            'element'  => '#logo img',
            'property' => 'max-height',
            'units'    => 'px',
        ),
        array(
            'element'  => '#logo img',
            'property' => 'width',
            'value_pattern' => 'auto'
        ),
    ),
    'active_callback'    => array(
        $thmv_standard_header_switch_callback
    ),
) );

Stratus_Kirki::add_field( 'stratus_theme', [
    'type'        => 'custom',
    'settings'    => 'themo_logo_resize_help',
    'label'       => esc_html__('Resizing', 'stratus'),
    'section'     => 'header_footer',
    'default'     => '<div class="th-theme-support">' . __('To increase your logo size, set the new \'Logo Height\' above and \'Publish\' before you \'Remove\' and re-upload your logo. The theme resizes the logo during the upload process.', 'stratus') . '</div>',
    'priority'    => 10,
    'active_callback'    => array(
        $thmv_standard_header_switch_callback
    ),
] );

// HEADER & FOOTER SECTION - Logo - Logo Image
Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'        => 'image',
    'settings'    => 'themo_logo',
    'label'       => esc_html__( 'Logo', 'stratus' ),
    'description' => esc_html__( 'For retina support, upload a logo that is twice the height set above.', 'stratus' ) ,
    'section'     => 'header_footer',
    'default'     => '',
    'priority'    => 10,
    'active_callback'    => array(
        $thmv_standard_header_switch_callback
    ),
) );

// HEADER & FOOTER SECTION - Logo - Transparent Switch
Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'        => 'switch',
    'settings'    => 'themo_logo_transparent_header_enable',
    'label'       => esc_html__( 'Alternative logo', 'stratus' ),
    'description'       => esc_html__( 'Used as an option for transparency header', 'stratus' ),
    'section'     => 'header_footer',
    'default'     => 'on',
    'priority'    => 10,
    'choices'     => array(
        'on'  => esc_attr__( 'Enable', 'stratus' ),
        'off' => esc_attr__( 'Disable', 'stratus' ),
    ),
    'active_callback'    => array(
        $thmv_standard_header_switch_callback
    ),
) );

// HEADER & FOOTER SECTION - Logo - Transparent Logo
Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'        => 'image',
    'settings'    => 'themo_logo_transparent_header',
    'label'       => esc_html__( 'Alternative logo upload', 'stratus' ),
    'description' => esc_html__( 'For retina support, upload a logo that is twice the height set above.', 'stratus' ) ,
    'section'     => 'header_footer',
    'default'     => '',
    'priority'    => 10,
    'active_callback'    => array(
        array(
            array(
                'setting'  => 'themo_logo_transparent_header_enable',
                'operator' => '==',
                'value'    => true,
            ),
            array(
                'setting'  => 'themo_logo_transparent_header_enable',
                'operator' => '==',
                'value'    => 'on',
            )
        ),
        $thmv_standard_header_switch_callback,
    ),
) );


// HEADER & FOOTER SECTION - Menu
Stratus_Kirki::add_field( 'stratus_theme', [
    'type'        => 'custom',
    'settings'    => 'thmv_customizer_header_menu',
    'section'     => 'header_footer',
    'default'     => '<div class="thmv-customizer-heading">' . __('Menu', 'stratus') . '</div><div class="thmv-customizer-divider"><span></span></div>',
    'priority'    => 10,
    'active_callback'    => array(
        $thmv_standard_header_switch_callback
    ),
] );

// HEADER & FOOTER SECTION - Menu - Top Menu Margin

Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'        => 'number',
    'settings'    => 'themo_nav_top_margin',
    'label'       => esc_html__( 'Navigation Top Margin', 'stratus' ),
    'description' => esc_html__( 'Set top margin value for the navigation bar', 'stratus' ),
    'section'     => 'header_footer',
    'default'     => 19,
    'choices'     => array(
        'min'  => '0',
        'max'  => '300',
        'step' => '1',
    ),
    'output' => array(
        array(
            'element'  => '.navbar .navbar-nav',
            'property' => 'margin-top',
            'units'    => 'px',
        ),
        array(
            'element'  => '.navbar .navbar-toggle',
            'property' => 'top',
            'units'    => 'px',
        ),
        array(
            'element'  => '.themo_cart_icon',
            'property' => 'margin-top',
            'value_pattern' => 'calc($px + 12px)'
        ),
    ),
    'active_callback'    => array(
        $thmv_standard_header_switch_callback
    ),
) );

// HEADER & FOOTER SECTION - Menu - Menu Text
Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'        => 'typography',
    'settings'    => 'menu_typography',
    'label'       => esc_attr__( 'Menu Typography', 'stratus' ),
    'description' => esc_attr__( 'Select the typography options for your Menu.', 'stratus' ),
    'help'        => esc_attr__( 'The typography options you set here will override the Typography options for the main menu on your site.', 'stratus' ),
    'section'     => 'header_footer',
    'priority'    => 10,
    'default'     => array(
        'font-family'    => 'Lato',
        'variant'        => 'regular',
        'font-size'      => '15px',
        'color'          => '#333333',
    ),
    'output' => array(
        array(
            'element' => array( '.navbar .navbar-nav > li > a, .navbar .navbar-nav > li > a:hover, .navbar .navbar-nav > li.active > a, .navbar .navbar-nav > li.active > a:hover, .navbar .navbar-nav > li.active > a:focus,.banner[data-transparent-header="true"].headhesive--clone .navbar-nav > li > a, .navbar .navbar-nav > li.th-accent' ),
        ),
    ),
    'active_callback'    => array(
        $thmv_standard_header_switch_callback
    ),
) );


// HEADER & FOOTER SECTION - Header
Stratus_Kirki::add_field( 'stratus_theme', [
    'type'        => 'custom',
    'settings'    => 'thmv_customizer_header_header',
    'section'     => 'header_footer',
    'default'     => '<div class="thmv-customizer-heading">' . __('Header', 'stratus') . '</div><div class="thmv-customizer-divider"><span></span></div>',
    'priority'    => 10,
    'active_callback'    => array(
        $thmv_standard_header_switch_callback
    ),
] );

// HEADER & FOOTER SECTION - Header - Enable Dark Header
Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'        => 'radio',
    'settings'    => 'themo_header_style',
    'label'       => esc_html__( 'Style Header', 'stratus' ),
    'section'     => 'header_footer',
    'default'     => 'dark',
    'priority'    => 10,
    'choices'     => array(
        'dark'  => esc_attr__( 'Dark', 'stratus' ),
        'light' => esc_attr__( 'Light', 'stratus' ),
    ),
    'active_callback'    => array(
        $thmv_standard_header_switch_callback
    ),
) );

// HEADER & FOOTER SECTION - Header - Sticky Header
Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'        => 'switch',
    'settings'    => 'themo_sticky_header',
    'label'       => esc_html__( 'Sticky Header', 'stratus' ),
    'section'     => 'header_footer',
    'default'     => 'on',
    'priority'    => 10,
    'choices'     => array(
        'on'  => esc_attr__( 'Enable', 'stratus' ),
        'off' => esc_attr__( 'Disable', 'stratus' ),
    ),
    'active_callback'    => array(
        $thmv_standard_header_switch_callback
    ),
) );

// HEADER & FOOTER SECTION - Header - Top Nav Switch
Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'        => 'switch',
    'settings'    => 'themo_top_nav_switch',
    'label'       => esc_html__( 'Top Bar', 'stratus' ),
    'section'     => 'header_footer',
    'default'     => '',
    'priority'    => 10,
    'choices'     => array(
        'on'  => esc_attr__( 'Enable', 'stratus' ),
        'off' => esc_attr__( 'Disable', 'stratus' ),
    ),
    'active_callback'    => array(
        $thmv_standard_header_switch_callback
    ),
) );

// HEADER & FOOTER SECTION - Header - Top Nav Text
Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'     => 'textarea',
    'settings' => 'themo_top_nav_text',
    'label'    => esc_html__( 'Top Bar Text', 'stratus' ),
    'section'  => 'header_footer',
    'default'  => esc_attr__( 'Welcome', 'stratus' ),
    'priority' => 10,
    'active_callback'    => array(
        array(
            array(
                'setting'  => 'themo_top_nav_switch',
                'operator' => '==',
                'value'    => true,
            ),
            array(
                'setting'  => 'themo_top_nav_switch',
                'operator' => '==',
                'value'    => 'on',
            )
        ),
        $thmv_standard_header_switch_callback,
    ),
) );

// HEADER & FOOTER SECTION - Header - Icon Block
Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'        => 'repeater',
    'label'       => esc_attr__( 'Top Bar Icons', 'stratus' ),
    'description' => esc_html__( 'Use any', 'stratus' ). ' <a href="http://fontawesome.io/icons/" target="_blank">Font Awesome</a> icon (e.g.: fa fa-twitter). <a href="http://fontawesome.io/icons/" target="_blank">'.esc_html__( 'Full List Here', 'stratus' ).'</a>',
    'section'     => 'header_footer',
    'priority'    => 10,
    'row_label' => array(
        'type' => 'text',
        'value' => esc_attr__('Icon Block', 'stratus' ),
    ),
    'settings'    => 'themo_top_nav_icon_blocks',
    'default'     => array(
        array(
            'title' => esc_attr__( 'Contact Us', 'stratus' ),
            'themo_top_nav_icon'  => 'fa fa-envelope-open-o',
            'themo_top_nav_icon_url'  => 'mailto:contact@themovation.com',
            'themo_top_nav_icon_url_target'  => '',
        ),
        array(
            'title' => esc_attr__( 'How to Find Us', 'stratus' ),
            'themo_top_nav_icon'  => 'fa fa-map-o',
            'themo_top_nav_icon_url'  => '#',
            'themo_top_nav_icon_url_target'  => '',
        ),
        array(
            'title' => esc_attr__( '250-555-5555', 'stratus' ),
            'themo_top_nav_icon'  => 'fa fa-mobile',
            'themo_top_nav_icon_url'  => 'tel:250-555-5555',
            'themo_top_nav_icon_url_target'  => '',
        ),
        array(
            'themo_top_nav_icon'  => 'fa fa-twitter',
            'themo_top_nav_icon_url'  => 'http://twitter.com',
            'themo_top_nav_icon_url_target'  => '1',
        ),
    ),
    'fields' => array(
        'title' => array(
            'type'        => 'text',
            'label'       => esc_attr__( 'Link Text', 'stratus' ),
            'default'     => '',
        ),
        'themo_top_nav_icon' => array(
            'type'        => 'text',
            'label'       => esc_attr__( 'Icon', 'stratus' ),
            'default'     => '',
        ),
        'themo_top_nav_icon_url' => array(
            'type'        => 'text',
            'label'       => esc_attr__( 'Link URL', 'stratus' ),
            'default'     => '',
        ),
        'themo_top_nav_icon_url_target' => array(
            'type'        => 'checkbox',
            'label'       => esc_attr__( 'Open Link in New Window', 'stratus' ),
            'default'     => '',
        ),
    ),
    'active_callback'    => array(
        array(
            array(
                'setting'  => 'themo_top_nav_switch',
                'operator' => '==',
                'value'    => true,
            ),
            array(
                'setting'  => 'themo_top_nav_switch',
                'operator' => '==',
                'value'    => 'on',
            )
        ),
        $thmv_standard_header_switch_callback,
    ),
) );

// HEADER & FOOTER SECTION - Header - Cart
Stratus_Kirki::add_field( 'stratus_theme', [
    'type'        => 'custom',
    'settings'    => 'thmv_customizer_header_cart',
    'section'     => 'header_footer',
    'default'     => '<div class="thmv-customizer-heading">' . __('Cart', 'stratus') . '</div><div class="thmv-customizer-divider"><span></span></div>',
    'priority'    => 10,
    'active_callback'    => array(
        $thmv_standard_header_switch_callback
    ),
] );

// HEADER & FOOTER SECTION - Header - Cart Switch
Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'        => 'switch',
    'settings'    => 'themo_woo_show_cart_icon',
    'label'       => esc_html__( 'Show Cart Icon', 'stratus' ),
    'description' => __( 'Show / Hide shopping cart icon in header', 'stratus' ),
    'section'     => 'header_footer',
    'default'     => 'on',
    'priority'    => 10,
    'choices'     => array(
        'on'  => esc_attr__( 'Enable', 'stratus' ),
        'off' => esc_attr__( 'Disable', 'stratus' ),
    ),
    'active_callback'    => array(
        $thmv_standard_header_switch_callback
    ),
) );

// HEADER & FOOTER SECTION - Header - Cart Icon
Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'        => 'radio-buttonset',
    'settings'    => 'themo_woo_cart_icon',
    'label'       => esc_html__( 'Cart Icon', 'stratus' ),
    'description'        => esc_html__( 'Choose your shopping cart icon', 'stratus' ),
    'section'     => 'header_footer',
    'default'     => 'th-i-cart',
    'priority'    => 10,
    'choices'     => array(

        'th-i-cart'   => array(
            esc_attr__( 'Bag', 'stratus' ),
        ),
        'th-i-cart2'   => array(
            esc_attr__( 'Cart', 'stratus' ),
        ),
        'th-i-cart3'   => array(
            esc_attr__( 'Cart 2', 'stratus' ),
        ),
        'th-i-card'   => array(
            esc_attr__( 'Card', 'stratus' ),
        ),
        'th-i-card2'   => array(
            esc_attr__( 'Card 2', 'stratus' ),
        ),

    ),
    'active_callback'    => array(
        array(
            array(
                'setting'  => 'themo_woo_show_cart_icon',
                'operator' => '==',
                'value'    => true,
            ),
            array(
                'setting'  => 'themo_woo_show_cart_icon',
                'operator' => '==',
                'value'    => 'on',
            )
        ),
        $thmv_standard_header_switch_callback,
    )
));

// HEADER & FOOTER SECTION - Footer
Stratus_Kirki::add_field( 'stratus_theme', [
    'type'        => 'custom',
    'settings'    => 'thmv_customizer_header_footer',
    'section'     => 'header_footer',
    'default'     => '<div class="thmv-customizer-heading">' . __('Footer', 'stratus') . '</div><div class="thmv-customizer-divider"><span></span></div>',
    'priority'    => 10,
    'active_callback'    => array(
        $thmv_standard_header_switch_callback
    ),
] );

// HEADER & FOOTER SECTION - Footer - Copyright
Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'     => 'textarea',
    'settings' => 'themo_footer_copyright',
    'label'       => esc_html__( 'Footer Copyright', 'stratus' ),
    'section'     => 'header_footer',
    'priority' => 10,
    'active_callback'    => array(
        $thmv_standard_header_switch_callback
    ),
) );


// HEADER & FOOTER SECTION - Footer - Credit
Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'     => 'textarea',
    'settings' => 'themo_footer_credit',
    'label'       => esc_html__( 'Footer Credit', 'stratus' ),
    'section'     => 'header_footer',
    'priority' => 10,
    'default' => __( 'Made with <i class="fa fa-heart-o"></i> by <a href="http://themovation.com">Themovation</a>', 'stratus' ),
    'active_callback'    => array(
        $thmv_standard_header_switch_callback
    ),
) );

// HEADER & FOOTER SECTION - Footer - Widget Switch
Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'        => 'switch',
    'settings'    => 'themo_footer_widget_switch',
    'label'       => esc_html__( 'Footer Widgets', 'stratus' ),
    'description' => esc_html__( 'Show / Hide Footer widgets area', 'stratus' ),
    'section'     => 'header_footer',
    'default'     => 'on',
    'priority'    => 10,
    'choices'     => array(
        'on'  => esc_attr__( 'Enable', 'stratus' ),
        'off' => esc_attr__( 'Disable', 'stratus' ),
    ),
    'active_callback'    => array(
        $thmv_standard_header_switch_callback
    ),
) );

// HEADER & FOOTER SECTION - Footer - Footer Columns
Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'        => 'radio',
    'settings'    => 'themo_footer_columns',
    'label'       => esc_html__( 'Footer Widget Columns', 'stratus' ),
    'section'     => 'header_footer',
    'default'     => '3',
    'priority'    => 10,
    'choices'     => array(
        '1'   => esc_attr__( '1 Column', 'stratus' ),
        '2' => esc_attr__( '2 Columns', 'stratus' ),
        '3'  => esc_attr__( '3 Columns', 'stratus' ),
        '4'  => esc_attr__( '4 Columns', 'stratus' ),
    ),
    'active_callback'    => array(
        array(
            array(
                'setting'  => 'themo_footer_widget_switch',
                'operator' => '==',
                'value'    => true,
            ),
            array(
                'setting'  => 'themo_footer_widget_switch',
                'operator' => '==',
                'value'    => 'on',
            )
        ),
        $thmv_standard_header_switch_callback
    ),
) );

// HEADER & FOOTER SECTION - Footer - Footer Logo (Widget)
Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'        => 'image',
    'settings'    => 'themo_footer_logo',
    'label'       => esc_html__( 'Footer Logo', 'stratus' ),
    'description' => '<p>' . esc_html__( 'Upload the logo you would like to use in your footer widget.', 'stratus' ) . '</p>' ,
    'section'     => 'header_footer',
    'default'     => '',
    'priority'    => 10,
    'active_callback'    => array(
        $thmv_standard_header_switch_callback
    ),
) );


// HEADER & FOOTER SECTION - Footer - Footer Logo URL
Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'     => 'text',
    'settings' =>  'themo_footer_logo_url',
    'label'       => esc_html__( 'Footer Logo Link', 'stratus' ),
    'description' => esc_html__( 'e.g. mailto:hello@themovation.com, /contact, http://google.com:', 'stratus' ),
    'section'     => 'header_footer',
    'priority' => 10,
    'active_callback'    => array(
        $thmv_standard_header_switch_callback
    ),
) );


// HEADER & FOOTER SECTION - Footer - Footer Logo URL
Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'     => 'checkbox',
    'settings' =>  'themo_footer_logo_url_target',
    'label'       => esc_html__( 'Open Link in New Window', 'stratus' ),
    'section'     => 'header_footer',
    'priority' => 10,
    'active_callback'    => array(
        $thmv_standard_header_switch_callback
    ),
) );

// COLOR PANEL
Stratus_Kirki::add_section( 'color', array(
    'title'      => esc_attr__( 'Color', 'stratus' ),
    'priority'   => 2,
    'panel'          => 'th_options',
    'capability' => 'edit_theme_options',
) );

// Color : Primary
Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'        => 'color',
    'settings'    => 'color_primary',
    'label'       => esc_attr__( 'Primary Color', 'stratus' ),
    'description'       => esc_attr__( 'This color appears in button options, links, and some headings throughout the theme', 'stratus' ),
    'section'     => 'color',
    'default'     => '#045089',
    'priority'    => 10,
    'choices'     => array(
        'alpha' => true,
    ),
    'output' => array(

        array(
            'element'  => '.btn-cta-primary,.navbar .navbar-nav>li>a:hover:after,.navbar .navbar-nav>li.active>a:after,.navbar .navbar-nav>li.active>a:hover:after,.navbar .navbar-nav>li.active>a:focus:after,.headhesive--clone .navbar-nav > li > a:hover:after,.banner[data-transparent-header="true"].headhesive--clone .navbar-nav > li > a:hover:after,form input[type=submit],html .woocommerce a.button.alt,html .woocommerce-page a.button.alt,html .woocommerce a.button,html .woocommerce-page a.button,.woocommerce #respond input#submit.alt:hover,.woocommerce a.button.alt:hover,.woocommerce #respond input#submit.alt, .woocommerce button.button.alt, .woocommerce input.button.alt, .woocommerce button.button.alt:hover,.woocommerce input.button.alt:hover,.woocommerce #respond input#submit.disabled,.woocommerce #respond input#submit:disabled,.woocommerce #respond input#submit:disabled[disabled],.woocommerce a.button.disabled,.woocommerce a.button:disabled,.woocommerce a.button:disabled[disabled],.woocommerce button.button.disabled,.woocommerce button.button:disabled,.woocommerce button.button:disabled[disabled],.woocommerce input.button.disabled,.woocommerce input.button:disabled,.woocommerce input.button:disabled[disabled],.woocommerce #respond input#submit.disabled:hover,.woocommerce #respond input#submit:disabled:hover,.woocommerce #respond input#submit:disabled[disabled]:hover,.woocommerce a.button.disabled:hover,.woocommerce a.button:disabled:hover,.woocommerce a.button:disabled[disabled]:hover,.woocommerce button.button.disabled:hover,.woocommerce button.button:disabled:hover,.woocommerce button.button:disabled[disabled]:hover,.woocommerce input.button.disabled:hover,.woocommerce input.button:disabled:hover,.woocommerce input.button:disabled[disabled]:hover,.woocommerce #respond input#submit.alt.disabled,.woocommerce #respond input#submit.alt.disabled:hover,.woocommerce #respond input#submit.alt:disabled,.woocommerce #respond input#submit.alt:disabled:hover,.woocommerce #respond input#submit.alt:disabled[disabled],.woocommerce #respond input#submit.alt:disabled[disabled]:hover,.woocommerce a.button.alt.disabled,.woocommerce a.button.alt.disabled:hover,.woocommerce a.button.alt:disabled,.woocommerce a.button.alt:disabled:hover,.woocommerce a.button.alt:disabled[disabled],.woocommerce a.button.alt:disabled[disabled]:hover,.woocommerce button.button.alt.disabled,.woocommerce button.button.alt.disabled:hover,.woocommerce button.button.alt:disabled,.woocommerce button.button.alt:disabled:hover,.woocommerce button.button.alt:disabled[disabled],.woocommerce button.button.alt:disabled[disabled]:hover,.woocommerce input.button.alt.disabled,.woocommerce input.button.alt.disabled:hover,.woocommerce input.button.alt:disabled,.woocommerce input.button.alt:disabled:hover,.woocommerce input.button.alt:disabled[disabled],.woocommerce input.button.alt:disabled[disabled]:hover,p.demo_store,.woocommerce.widget_price_filter .ui-slider .ui-slider-handle,.th-conversion form input[type=submit],.th-conversion .with_frm_style input[type=submit],.th-pricing-column.th-highlight,.search-submit,.search-submit:hover,.widget .tagcloud a:hover,.footer .tagcloud a:hover,.btn-standard-primary-form form .frm_submit input[type=submit],.btn-standard-primary-form form .frm_submit input[type=submit]:hover,.btn-ghost-primary-form form .frm_submit input[type=submit]:hover,.btn-cta-primary-form form .frm_submit input[type=submit],.btn-cta-primary-form form .frm_submit input[type=submit]:hover,.th-widget-area form input[type=submit],.th-widget-area .with_frm_style .frm_submit input[type=submit],.elementor-widget-themo-header.elementor-view-stacked .th-header-wrap .elementor-icon,.elementor-widget-themo-service-block.elementor-view-stacked .th-service-block-w .elementor-icon',
            'property' => 'background-color',
        ),
        array(
            'element'  => 'a,.accent,.navbar .navbar-nav .dropdown-menu li.active a,.navbar .navbar-nav .dropdown-menu li a:hover,.navbar .navbar-nav .dropdown-menu li.active a:hover,.page-title h1,.inner-container>h1.entry-title,.woocommerce ul.products li.product .price,.woocommerce ul.products li.product .price del,.woocommerce .single-product .product .price,.woocommerce.single-product .product .price,.woocommerce .single-product .product .price ins,.woocommerce.single-product .product .price ins,.a2c-ghost.woocommerce a.button,.th-cta .th-cta-text span,.elementor-widget-themo-info-card .th-info-card-wrap .elementor-icon-box-title,.map-info h3,.th-pkg-content h3,.th-pricing-cost,#main-flex-slider .slides h1,.th-team-member-social a i:hover,.elementor-widget-toggle .elementor-toggle .elementor-toggle-title,.elementor-widget-toggle .elementor-toggle .elementor-toggle-title.active,.elementor-widget-toggle .elementor-toggle .elementor-toggle-icon,.elementor-widget-themo-header .th-header-wrap .elementor-icon,.elementor-widget-themo-header.elementor-view-default .th-header-wrap .elementor-icon,.elementor-widget-themo-service-block .th-service-block-w .elementor-icon,.elementor-widget-themo-service-block.elementor-view-default .th-service-block-w .elementor-icon,.elementor-widget-themo-header.elementor-view-framed .th-header-wrap .elementor-icon,.elementor-widget-themo-service-block.elementor-view-framed .th-service-block-w .elementor-icon',
            'property' => 'color',
        ),
        array(
            'element'  => '.btn-standard-primary,.btn-ghost-primary:hover,.pager li>a:hover,.pager li>span:hover,.a2c-ghost.woocommerce a.button:hover',
            'property' => 'background-color',
        ),
        array(
            'element'  => '.btn-standard-primary,.btn-ghost-primary:hover,.pager li>a:hover,.pager li>span:hover,.a2c-ghost.woocommerce a.button:hover,.btn-standard-primary-form form .frm_submit input[type=submit],.btn-standard-primary-form form .frm_submit input[type=submit]:hover,.btn-ghost-primary-form form .frm_submit input[type=submit]:hover,.btn-ghost-primary-form form .frm_submit input[type=submit]',
            'property' => 'border-color',
        ),
        array(
            'element'  => '.btn-ghost-primary,.btn-ghost-primary:focus,.th-portfolio-filters a.current,.a2c-ghost.woocommerce a.button,.btn-ghost-primary-form form .frm_submit input[type=submit]',
            'property' => 'color',
        ),
        array(
            'element'  => '.btn-ghost-primary,.th-portfolio-filters a.current,.a2c-ghost.woocommerce a.button,.elementor-widget-themo-header.elementor-view-framed .th-header-wrap .elementor-icon,.elementor-widget-themo-service-block.elementor-view-framed .th-service-block-w .elementor-icon',
            'property' => 'border-color',
        ),
        array(
            'element'  => 'form select:focus,form textarea:focus,form input:focus,.th-widget-area .widget select:focus,.search-form input:focus',
            'property' => 'border-color',
            'suffix' => '!important',
        ),
    ),
) );

// Color : Accent
Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'        => 'color',
    'settings'    => 'color_accent',
    'label'       => esc_attr__( 'Accent Color', 'stratus' ),
    'description'       => esc_attr__( 'This color appears in icons, button options, and a few details throughout the theme.', 'stratus' ),
    'section'     => 'color',
    'default'     => '#f96d64',
    'priority'    => 10,
    'choices'     => array(
        'alpha' => true,
    ),
    'output' => array(
        array(
            'element'  => '',
            'property' => 'color',
        ),
        array(
            'element'  => '.btn-cta-accent,.a2c-cta.woocommerce a.button,.a2c-cta.woocommerce a.button:hover,.btn-standard-accent-form form .frm_submit input[type=submit],.btn-standard-accent-form form .frm_submit input[type=submit]:hover,.btn-ghost-accent-form form .frm_submit input[type=submit]:hover,.btn-cta-accent-form form .frm_submit input[type=submit],.btn-cta-accent-form form .frm_submit input[type=submit]:hover',
            'property' => 'background-color',
        ),
        array(
            'element'  => 'body #booked-profile-page input[type=submit].button-primary,body table.booked-calendar input[type=submit].button-primary,body .booked-modal input[type=submit].button-primary,body table.booked-calendar .booked-appt-list .timeslot .timeslot-people button,body #booked-profile-page .booked-profile-appt-list .appt-block.approved .status-block',
            'property' => 'background',
            'suffix' => '!important',
        ),
        array(
            'element'  => 'body #booked-profile-page input[type=submit].button-primary,body table.booked-calendar input[type=submit].button-primary,body .booked-modal input[type=submit].button-primary,body table.booked-calendar .booked-appt-list .timeslot .timeslot-people button,.btn-standard-accent-form form .frm_submit input[type=submit],.btn-standard-accent-form form .frm_submit input[type=submit]:hover,.btn-ghost-accent-form form .frm_submit input[type=submit]:hover,.btn-ghost-accent-form form .frm_submit input[type=submit]',
            'property' => 'border-color',
            'suffix' => '!important',
        ),
        array(
            'element'  => '.btn-standard-accent,.btn-ghost-accent:hover',
            'property' => 'background-color',
        ),
        array(
            'element'  => '.btn-standard-accent,.btn-ghost-accent:hover',
            'property' => 'border-color',
        ),
        array(
            'element'  => '.btn-ghost-accent,.btn-ghost-accent:focus,.btn-ghost-accent-form form .frm_submit input[type=submit]',
            'property' => 'color',
        ),
        array(
            'element'  => '.btn-ghost-accent',
            'property' => 'border-color',
        ),
    ),
) );

//  TYPOGRAPHY SECTION
Stratus_Kirki::add_section( 'typography', array(
	'title'      => esc_attr__( 'Typography', 'stratus' ),
	'priority'   => 2,
	'capability' => 'edit_theme_options',
    'panel'          => 'th_options',
) );

/*
// Bundled Font : Ludicrous
Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'        => 'radio',
    'settings'    => 'headers_typography_ludicrous',
    'label'       => esc_html__( 'Bundled Headings Font', 'stratus' ),
    'description' => esc_attr__( 'Enable the bundled "Ludicrous" font for your main headings.', 'stratus' ),
    'section'     => 'typography',
    'default'     => 'on',
    'priority'    => 10,
    'choices'     => array(
        'on'  => esc_attr__( 'Enable', 'stratus' ),
        'off' => esc_attr__( 'Disable', 'stratus' ),
    ),


) );
*/

// Typography : Headings Text
Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'        => 'typography',
    'settings'    => 'headers_typography',
    'label'       => esc_attr__( 'Headings Typography', 'stratus' ),
    'description' => esc_attr__( 'Select the typography options for your headings.', 'stratus' ),
    'help'        => esc_attr__( 'The typography options you set here will override the Body Typography options for all headings on your site (post titles, widget titles etc).', 'stratus' ),
    'section'     => 'typography',
    'priority'    => 10,
    'default'     => array(
        'font-family'    => 'Lato',
        'variant'        => 'regular',
    ),
    'output' => array(
        array(
            'element' => array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', '.h1', '.h2', '.h3', '.h4', '.h5', '.h6' ),
        ),
    ),
) );

// Typography : Body Text
Stratus_Kirki::add_field( 'stratus_theme', array(
	'type'        => 'typography',
	'settings'    => 'body_typography',
	'label'       => esc_attr__( 'Body Typography', 'stratus' ),
	'description' => esc_attr__( 'Select the main typography options for your site.', 'stratus' ),
	'help'        => esc_attr__( 'The typography options you set here apply to all content on your site.', 'stratus' ),
	'section'     => 'typography',
	'priority'    => 10,
	'default'     => array(
		'font-family'    => 'Lato',
		'variant'        => 'regular',
		'font-size'      => '16px',
		'line-height'    => '1.65',
		'color'          => '#333333',
	),
	'output' => array(
		array(
			'element' => 'body,p,li',
		),
	),
) );






// Typography : Headings Text
Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'        => 'typography',
    'settings'    => 'additional_fonts_1',
    'label'       => esc_attr__( 'Include Additional Fonts', 'stratus' ),
    'description' => esc_attr__( 'Use these inputs if you want to include additional font families or font weights.', 'stratus' ),
    'section'     => 'typography',
    'priority'    => 10,
    'default'     => array(
        'font-family'    => 'Lato',
        'variant'        => '700',
    ),
) );

Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'        => 'typography',
    'settings'    => 'additional_fonts_2',
    'section'     => 'typography',
    'priority'    => 10,
    'default'     => array(
        'font-family'    => 'Lato',
        'variant'        => '300',
    ),
) );

// BLOG SECTION
Stratus_Kirki::add_section( 'blog', array(
    'title'      => esc_attr__( 'Blog', 'stratus' ),
    'priority'   => 2,
    'capability' => 'edit_theme_options',
    'panel'          => 'th_options',
) );

Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'        => 'switch',
    'settings'    => 'themo_automatic_post_excerpts',
    'label'       => esc_html__( 'Enable Automatic Post Excerpts', 'stratus' ),
    'description'       => esc_html__( 'This option affects the Blog widget and the blog templates', 'stratus' ),
    'section'     => 'blog',
    'default'     => 'on',
    'priority'    => 10,
    'choices'     => array(
        'on'  => esc_attr__( 'Enable', 'stratus' ),
        'off' => esc_attr__( 'Disable', 'stratus' ),
    ),
) );

// Blog. : Blog header switch
Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'        => 'switch',
    'settings'    => 'themo_blog_index_layout_show_header',
    'label'       => esc_html__( 'Blog Homepage Header', 'stratus' ),
    'description' => esc_html__( 'Show / Hide header for Blog Homepage', 'stratus' ),
    'section'     => 'blog',
    'default'     => 'on',
    'priority'    => 10,
    'choices'     => array(
        'on'  => esc_attr__( 'Enable', 'stratus' ),
        'off' => esc_attr__( 'Disable', 'stratus' ),
    ),
) );

// Blog : Blog Header Align
Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'        => 'radio-buttonset',
    'settings'    => 'themo_blog_index_layout_header_float',
    'label'       => esc_html__( 'Blog Homepage Header Position ', 'stratus' ),
    'section'     => 'blog',
    'default'     => 'centered',
    'priority'    => 10,
    'choices'     => array(

        'left'   => array(
            esc_attr__( 'Left', 'stratus' ),
        ),
        'centered'   => array(
            esc_attr__( 'Centered', 'stratus' ),
        ),
        'right'   => array(
            esc_attr__( 'Right', 'stratus' ),
        ),

    ),
    'active_callback'  => array(
        array(
            array(
                'setting'  => 'themo_blog_index_layout_show_header',
                'operator' => '==',
                'value'    => 1,
            ),
            array(
                'setting'  => 'themo_blog_index_layout_show_header',
                'operator' => '==',
                'value'    => 'on',
            )
        )
    )
) );

// Blog : Blog Sidebar Position
Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'        => 'radio-buttonset',
    'settings'    => 'themo_blog_index_layout_sidebar',
    'label'       => esc_html__( 'Blog Homepage Sidebar Position', 'stratus' ),
    'section'     => 'blog',
    'default'     => 'right',
    'priority'    => 10,
    'choices'     => array(

        'left'   => array(
            esc_attr__( 'Left', 'stratus' ),
        ),
        'full'   => array(
            esc_attr__( 'None', 'stratus' ),
        ),
        'right'   => array(
            esc_attr__( 'Right', 'stratus' ),
        ),

    ),
) );



// Blog. : Blog Single header switch
Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'        => 'switch',
    'settings'    => 'themo_single_post_layout_show_header',
    'label'       => esc_html__( 'Blog Single Page Header', 'stratus' ),
    'description' => esc_html__( 'Show / Hide Page header for Blog Single', 'stratus' ),
    'section'     => 'blog',
    'default'     => 'on',
    'priority'    => 10,
    'choices'     => array(
        'on'  => esc_attr__( 'Enable', 'stratus' ),
        'off' => esc_attr__( 'Disable', 'stratus' ),
    ),
) );

// Blog : Blog Single Header Align
Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'        => 'radio-buttonset',
    'settings'    => 'themo_single_post_layout_header_float',
    'label'       => esc_html__( 'Blog Single Page Header Position ', 'stratus' ),
    'section'     => 'blog',
    'default'     => 'centered',
    'priority'    => 10,
    'choices'     => array(
        'left'   => array(
            esc_attr__( 'Left', 'stratus' ),
        ),
        'centered'   => array(
            esc_attr__( 'Centered', 'stratus' ),
        ),
        'right'   => array(
            esc_attr__( 'Right', 'stratus' ),
        ),

    ),
    'active_callback'  => array(
        array(
            array(
                'setting'  => 'themo_single_post_layout_show_header',
                'operator' => '==',
                'value'    => 1,
            ),
            array(
                'setting'  => 'themo_single_post_layout_show_header',
                'operator' => '==',
                'value'    => 'on',
            )
        )
    )

) );

// Blog : Blog Single Sidebar Position
Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'        => 'radio-buttonset',
    'settings'    => 'themo_single_post_layout_sidebar',
    'label'       => esc_html__( 'Blog Single Sidebar Position', 'stratus' ),
    'section'     => 'blog',
    'default'     => 'right',
    'priority'    => 10,
    'choices'     => array(

        'left'   => array(
            esc_attr__( 'Left', 'stratus' ),
        ),
        'full'   => array(
            esc_attr__( 'None', 'stratus' ),
        ),
        'right'   => array(
            esc_attr__( 'Right', 'stratus' ),
        ),

    ),
) );


// Blog. : Default header switch
Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'        => 'switch',
    'settings'    => 'themo_default_layout_show_header',
    'label'       => esc_html__( 'Archives Header', 'stratus' ),
    'description' => esc_html__( 'Show / Hide header for Archives, 404, Search', 'stratus' ),
    'section'     => 'blog',
    'default'     => 'on',
    'priority'    => 10,
    'choices'     => array(
        'on'  => esc_attr__( 'Enable', 'stratus' ),
        'off' => esc_attr__( 'Disable', 'stratus' ),
    ),
) );

// Blog : Default Header Align
Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'        => 'radio-buttonset',
    'settings'    => 'themo_default_layout_header_float',
    'label'       => esc_html__( 'Archives Header Position ', 'stratus' ),
    'section'     => 'blog',
    'default'     => 'centered',
    'priority'    => 10,
    'choices'     => array(

        'left'   => array(
            esc_attr__( 'Left', 'stratus' ),
        ),
        'centered'   => array(
            esc_attr__( 'Centered', 'stratus' ),
        ),
        'right'   => array(
            esc_attr__( 'Right', 'stratus' ),
        ),

    ),
    'active_callback'  => array(
        array(
            array(
                'setting'  => 'themo_default_layout_show_header',
                'operator' => '==',
                'value'    => 1,
            ),
            array(
                'setting'  => 'themo_default_layout_show_header',
                'operator' => '==',
                'value'    => 'on',
            )
        )
    )

) );

// Blog : Default Sidebar Position
Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'        => 'radio-buttonset',
    'settings'    => 'themo_default_layout_sidebar',
    'label'       => esc_html__( 'Archives Sidebar Position', 'stratus' ),
    'section'     => 'blog',
    'default'     => 'right',
    'priority'    => 10,
    'choices'     => array(

        'left'   => array(
            esc_attr__( 'Left', 'stratus' ),
        ),
        'full'   => array(
            esc_attr__( 'None', 'stratus' ),
        ),
        'right'   => array(
            esc_attr__( 'Right', 'stratus' ),
        ),

    ),
) );

// Blog. : Category Masonry Style
Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'        => 'switch',
    'settings'    => 'themo_blog_index_layout_masonry',
    'label'       => esc_html__( 'Masonry Style for Category Pages', 'stratus' ),
    'section'     => 'blog',
    'default'     => 'on',
    'priority'    => 10,
    'choices'     => array(
        'on'  => esc_attr__( 'Enable', 'stratus' ),
        'off' => esc_attr__( 'Disable', 'stratus' ),
    ),
) );

// WOOCOMMERCE SECTION
Stratus_Kirki::add_section( 'woo', array(
    'title'      => esc_attr__( 'WooCommerce', 'stratus' ),
    'priority'   => 2,
    'panel'          => 'th_options',
    'capability' => 'edit_theme_options',
) );

// Woo : Header Switch
Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'        => 'switch',
    'settings'    => 'themo_woo_show_header',
    'label'       => esc_html__( 'Page Header', 'stratus' ),
    'description' => esc_html__( 'Show / Hide page header for woo categories, tags, taxonomies', 'stratus' ),
    'section'     => 'woo',
    'default'     => 'on',
    'priority'    => 10,
    'choices'     => array(
        'on'  => esc_attr__( 'Enable', 'stratus' ),
        'off' => esc_attr__( 'Disable', 'stratus' ),
    ),
) );

// Woo : Header Align
Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'        => 'radio-buttonset',
    'settings'    => 'themo_woo_header_float',
    'label'       => esc_html__( 'Align Page Header', 'stratus' ),
    'section'     => 'woo',
    'default'     => 'centered',
    'priority'    => 10,
    'choices'     => array(

        'left'   => array(
            esc_attr__( 'Left', 'stratus' ),
        ),
        'centered'   => array(
            esc_attr__( 'Centered', 'stratus' ),
        ),
        'right'   => array(
            esc_attr__( 'Right', 'stratus' ),
        ),
    ),
    'active_callback'  => array(
        array(
            array(
                'setting'  => 'themo_woo_show_header',
                'operator' => '==',
                'value'    => 1,
            ),
            array(
                'setting'  => 'themo_woo_show_header',
                'operator' => '==',
                'value'    => 'on',
            )
        )
    )
) );

// Woo : Sidebar Position
Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'        => 'radio-buttonset',
    'settings'    => 'themo_woo_sidebar',
    'label'       => esc_html__( 'Sidebar Position for Woo categories', 'stratus' ),
    'section'     => 'woo',
    'default'     => 'right',
    'priority'    => 10,
    'choices'     => array(

        'left'   => array(
            esc_attr__( 'Left', 'stratus' ),
        ),
        'full'   => array(
            esc_attr__( 'None', 'stratus' ),
        ),
        'right'   => array(
            esc_attr__( 'Right', 'stratus' ),
        ),

    ),
) );

// SLIDER SECTION
Stratus_Kirki::add_section( 'slider', array(
    'title'      => esc_attr__( 'Slider', 'stratus' ),
    'priority'   => 2,
    'capability' => 'edit_theme_options',
    'panel'          => 'th_options',
) );

// Slider : Autoplay
Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'        => 'switch',
    'settings'    => 'themo_flex_autoplay',
    'label'       => esc_attr__( 'Auto Play', 'stratus' ),
    'description' => esc_attr__( 'Start slider automatically', 'stratus' ),
    'section'     => 'slider',
    'default'     => 'on',
    'priority'    => 10,
    'choices'     => array(
        'on'  => esc_attr__( 'Enable', 'stratus' ),
        'off' => esc_attr__( 'Disable', 'stratus' ),
    ),
) );

// Slider : Animation
Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'        => 'radio',
    'settings'    => 'themo_flex_animation',
    'label'       => esc_html__( 'Animation', 'stratus' ),
    'description'        => esc_html__( 'Controls the animation type, "fade" or "slide".', 'stratus' ),
    'section'     => 'slider',
    'default'     => 'fade',
    'priority'    => 10,
    'choices'     => array(
        'fade'   => array(
            esc_attr__( 'Fade', 'stratus' ),
        ),
        'slide' => array(
            esc_attr__( 'Slide', 'stratus' ),
        ),
    ),
) );

// Slider : Easing
Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'        => 'radio',
    'settings'    => 'themo_flex_easing',
    'label'       => esc_html__( 'Easing', 'stratus' ),
    'description'        => esc_html__( 'Determines the easing method used in jQuery transitions.', 'stratus' ),
    'section'     => 'slider',
    'default'     => 'swing',
    'priority'    => 10,
    'choices'     => array(
        'swing'   => array(
            esc_attr__( 'Swing', 'stratus' ),
        ),
        'linear' => array(
            esc_attr__( 'Linear', 'stratus' ),
        ),
    ),
) );

// Slider : Animation Loop
Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'        => 'switch',
    'settings'    => 'themo_flex_animationloop',
    'label'       => esc_attr__( 'Animation Loop', 'stratus' ),
    'description' => esc_attr__( 'Gives the slider a seamless infinite loop.', 'stratus' ),
    'section'     => 'slider',
    'default'     => 'on',
    'priority'    => 10,
    'choices'     => array(
        'on'  => esc_attr__( 'Enable', 'stratus' ),
        'off' => esc_attr__( 'Disable', 'stratus' ),
    ),
) );

// Slider : Smooth Height
Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'        => 'switch',
    'settings'    => 'themo_flex_smoothheight',
    'label'       => esc_attr__( 'Smooth Height', 'stratus' ),
    'description' => esc_attr__( 'Animate the height of the slider smoothly for slides of varying height.', 'stratus' ),
    'section'     => 'slider',
    'default'     => 'on',
    'priority'    => 10,
    'choices'     => array(
        'on'  => esc_attr__( 'Enable', 'stratus' ),
        'off' => esc_attr__( 'Disable', 'stratus' ),
    ),
) );

// Slider : Slide Speed
Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'        => 'slider',
    'settings'    => 'themo_flex_slideshowspeed',
    'label'       => esc_html__( 'Slideshow Speed', 'stratus' ),
    'description'        => esc_html__( 'Set the speed of the slideshow cycling, in milliseconds', 'stratus' ),
    'section'     => 'slider',
    'default'     => 4000,
    'choices'     => array(
        'min'  => '0',
        'max'  => '15000',
        'step' => '100',
    ),
) );

// Slider : Animation Speed
Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'        => 'slider',
    'settings'    => 'themo_flex_animationspeed',
    'label'       => esc_html__( 'Animation Speed', 'stratus' ),
    'description' => esc_html__( 'Set the speed of animations, in milliseconds', 'stratus' ),
    'section'     => 'slider',
    'default'     => 550,
    'choices'     => array(
        'min'  => '0',
        'max'  => '1200',
        'step' => '50',
    ),
) );

// Slider : Randomize
Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'        => 'switch',
    'settings'    => 'themo_flex_randomize',
    'label'       => esc_attr__( 'Randomize', 'stratus' ),
    'description' => esc_attr__( 'Randomize slide order, on load', 'stratus' ),
    'section'     => 'slider',
    'default'     => '0',
    'priority'    => 10,
    'choices'     => array(
        'on'  => esc_attr__( 'Enable', 'stratus' ),
        'off' => esc_attr__( 'Disable', 'stratus' ),
    ),
) );

// Slider : Puse on hover
Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'        => 'switch',
    'settings'    => 'themo_flex_pauseonhover',
    'label'       => esc_attr__( 'Pause on Hover', 'stratus' ),
    'description' => esc_attr__( 'Pause the slideshow when hovering over slider, then resume when no longer hovering.', 'stratus' ),
    'section'     => 'slider',
    'default'     => 'on',
    'priority'    => 10,
    'choices'     => array(
        'on'  => esc_attr__( 'Enable', 'stratus' ),
        'off' => esc_attr__( 'Disable', 'stratus' ),
    ),
) );

// Slider : Touch
Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'        => 'switch',
    'settings'    => 'themo_flex_touch',
    'label'       => esc_attr__( 'Touch', 'stratus' ),
    'description' => esc_attr__( 'Allow touch swipe navigation of the slider on enabled devices.', 'stratus' ),
    'section'     => 'slider',
    'default'     => 'on',
    'priority'    => 10,
    'choices'     => array(
        'on'  => esc_attr__( 'Enable', 'stratus' ),
        'off' => esc_attr__( 'Disable', 'stratus' ),
    ),
) );

// Slider : Dir Nav
Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'        => 'switch',
    'settings'    => 'themo_flex_directionnav',
    'label'       => esc_attr__( 'Direction Nav', 'stratus' ),
    'description' => esc_attr__( 'Create previous/next arrow navigation.', 'stratus' ),
    'section'     => 'slider',
    'default'     => 'on',
    'priority'    => 10,
    'choices'     => array(
        'on'  => esc_attr__( 'Enable', 'stratus' ),
        'off' => esc_attr__( 'Disable', 'stratus' ),
    ),
) );

// Slider : Paging Control
Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'        => 'switch',
    'settings'    => 'themo_flex_controlNav',
    'label'       => esc_attr__( 'Paging Control', 'stratus' ),
    'description' => esc_attr__( 'Create navigation for paging control of each slide.', 'stratus' ),
    'section'     => 'slider',
    'default'     => 'on',
    'priority'    => 10,
    'choices'     => array(
        'on'  => esc_attr__( 'Enable', 'stratus' ),
        'off' => esc_attr__( 'Disable', 'stratus' ),
    ),
) );

// MISC. SECTION
Stratus_Kirki::add_section( 'misc', array(
    'title'      => esc_attr__( 'Misc.', 'stratus' ),
    'priority'   => 2,
    'panel'          => 'th_options',
    'capability' => 'edit_theme_options',
) );

// Misc. : Rounded Buttons
Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'        => 'radio',
    'settings'    => 'themo_button_style',
    'label'       => esc_html__( 'Button Style', 'stratus' ),
    'section'     => 'misc',
    'default'     => 'round',
    'priority'    => 10,
    'choices'     => array(
        'square'  => esc_attr__( 'Squared', 'stratus' ),
        'round'   => esc_attr__( 'Rounded', 'stratus' ),
    ),
    'output' => array(
        array(
            'element'  => '.simple-conversion form input[type=submit],.simple-conversion .with_frm_style input[type=submit],.search-form input',
            'property' => 'border-radius',
            'units'    => 'px',
            'value_pattern' => '5',
            'suffix' => '!important',
            'exclude' => array('round'),
        ),
        array(
            'element'  => '.nav-tabs > li > a',
            'property' => 'border-radius',
            'value_pattern' => '5px 5px 0 0',
            'exclude' => array('round'),
        ),
        array(
            'element'  => '.btn, .btn-cta, .btn-sm,.btn-group-sm > .btn, .btn-group-xs > .btn, .pager li > a,.pager li > span, .form-control, #respond input[type=submit], body .booked-modal button, .woocommerce #respond input#submit, .woocommerce a.button, .woocommerce button.button, .woocommerce input.button, .woocommerce div.product form.cart .button, .search-form input, .search-submit, .navbar .th-accent, .headhesive--clone.banner[data-transparent-header=\'true\'] .th-accent',
            'property' => 'border-radius',
            'units'    => 'px',
            'value_pattern' => '5',
            'exclude' => array('round'),
        ),
        array(
            'element'  => 'form input[type=submit],.with_frm_style .frm_submit input[type=submit],.with_frm_style .frm_submit input[type=button],.frm_form_submit_style, .with_frm_style.frm_login_form input[type=submit], .widget input[type=submit],.widget .frm_style_formidable-style.with_frm_style input[type=submit], .th-port-btn, body #booked-profile-page input[type=submit], body #booked-profile-page button, body table.booked-calendar input[type=submit], body table.booked-calendar button, body .booked-modal input[type=submit], body .booked-modal button,.th-widget-area form input[type=submit],.th-widget-area .with_frm_style .frm_submit input[type=submit],.th-widget-area .widget .frm_style_formidable-style.with_frm_style input[type=submit]',
            'property' => 'border-radius',
            'units'    => 'px',
            'value_pattern' => '5',
            'exclude' => array('round'),
        ),
    ),
) );

// Misc : Content Preloader
Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'        => 'switch',
    'settings'    => 'themo_preloader',
    'label'       => esc_html__( 'Content Preloader', 'stratus' ),
    'description'       => esc_html__( 'Enables preloader site wide.', 'stratus' ),
    'section'     => 'misc',
    'default'     => 'on',
    'priority'    => 10,
    'choices'     => array(
        'on'  => esc_attr__( 'Enable', 'stratus' ),
        'off' => esc_attr__( 'Disable', 'stratus' ),
    ),
) );


// Misc. : FBoxed mode vs full width
Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'        => 'switch',
    'settings'    => 'themo_boxed_layout',
    'label'       => esc_html__( 'Boxed Layout', 'stratus' ),
    'section'     => 'misc',
    'default'     => '',
    'priority'    => 10,
    'choices'     => array(
        'on'  => esc_attr__( 'Enable', 'stratus' ),
        'off' => esc_attr__( 'Disable', 'stratus' ),
    ),
) );

// Misc. : Boxed mode BG Colour
Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'        => 'color',
    'settings'    => 'th_boxed_bg_color', //themo_boxed_layout_background
    'label'       => esc_attr__( 'Background Color', 'stratus' ),
    'section'     => 'misc',
    'default'     => '#FFF',
    'priority'    => 10,
    'choices'     => array(
        'alpha' => true,
    ),
    'output' => array(
        array(
            'element'  => 'body',
            'property' => 'background-color',
        ),

    ),

    'active_callback'  => array(
        array(
            array(
                'setting'  => 'themo_boxed_layout',
                'operator' => '==',
                'value'    => 1,
            ),
            array(
                'setting'  => 'themo_boxed_layout',
                'operator' => '==',
                'value'    => 'on',
            )
        )
    )
) );

// Misc. : Boxed mode BG Image
Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'        => 'image',
    'settings'    => 'th_boxed_bg_image',
    'label'       => esc_html__( 'Background Image', 'stratus' ),
    'section'     => 'misc',
    'default'     => '',
    'priority'    => 10,
    'output' => array(
        array(
            'element'  => 'body',
            'property' => 'background-image',
        ),
        array(
            'element'  => 'body',
            'property' => 'background-attachment',
            'value_pattern' => 'fixed',
        ),
        array(
            'element'  => 'body',
            'property' => 'background-size',
            'value_pattern' => 'cover',
        ),

    ),
    'active_callback'  => array(
        array(
            array(
                'setting'  => 'themo_boxed_layout',
                'operator' => '==',
                'value'    => 1,
            ),
            array(
                'setting'  => 'themo_boxed_layout',
                'operator' => '==',
                'value'    => 'on',
            )
        )
    )
) );

// Misc. : Enable Retina Find Replace script.
Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'        => 'switch',
    'settings'    => 'themo_retinajs',
    'label'       => esc_html__( 'High-resolution/Retina Image Support', 'stratus' ),
    'description' => esc_html__( 'Automatically serve up high-resolution images to devices that support them.', 'stratus' ),
    'section'     => 'misc',
    'default'     => '',
    'priority'    => 10,
    'choices'     => array(
        'on'  => esc_attr__( 'Enable', 'stratus' ),
        'off' => esc_attr__( 'Disable', 'stratus' ),
    ),
) );

// Misc. : Retina Image Sizes Generator
Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'        => 'switch',
    'settings'    => 'themo_retina_support',
    'label'       => esc_html__( 'High-resolution/Retina Image Generator', 'stratus' ),
    'description' => esc_html__( 'Automatically generate high-resolution/retina image sizes (@2x) when uploaded to your Media Library.', 'stratus' ),
    'section'     => 'misc',
    'default'     => '',
    'priority'    => 10,
    'choices'     => array(
        'on'  => esc_attr__( 'Enable', 'stratus' ),
        'off' => esc_attr__( 'Disable', 'stratus' ),
    ),
) );


// Misc. : Custom Tour CPT Slug
Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'     => 'text',
    'settings' => 'themo_portfolio_rewrite_slug',
    'label'       => esc_html__( 'Portfolio Custom Slug', 'stratus' ),
    'description'       => esc_html__( 'Optionally change the permalink slug for the Portfolio custom post type', 'stratus' ),
    'section'     => 'misc',
    'priority' => 10,
) );

// Misc. : Event header switch
Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'        => 'switch',
    'settings'    => 'tribe_events_layout_show_header',
    'label'       => esc_html__( 'Events Header', 'stratus' ),
    'description' => esc_html__( 'Show / Hide header for Events pages', 'stratus' ),
    'section'     => 'misc',
    'default'     => 'on',
    'priority'    => 10,
    'choices'     => array(
        'on'  => esc_attr__( 'Enable', 'stratus' ),
        'off' => esc_attr__( 'Disable', 'stratus' ),
    ),
) );

// Misc. : Events Header Align
Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'        => 'radio-buttonset',
    'settings'    => 'tribe_events_layout_header_float',
    'label'       => esc_html__( 'Events Header Position ', 'stratus' ),
    'section'     => 'misc',
    'default'     => 'centered',
    'priority'    => 10,
    'choices'     => array(

        'left'   => array(
            esc_attr__( 'Left', 'stratus' ),
        ),
        'centered'   => array(
            esc_attr__( 'Centered', 'stratus' ),
        ),
        'right'   => array(
            esc_attr__( 'Right', 'stratus' ),
        ),

    ),
    'active_callback'  => array(
        array(
            array(
                'setting'  => 'tribe_events_layout_show_header',
                'operator' => '==',
                'value'    => 1,
            ),
            array(
                'setting'  => 'tribe_events_layout_show_header',
                'operator' => '==',
                'value'    => 'on',
            )
        )
    )
) );

// Misc. : Events Sidebar Position
Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'        => 'radio-buttonset',
    'settings'    => 'tribe_events_layout_sidebar',
    'label'       => esc_html__( 'Events Sidebar Position', 'stratus' ),
    'section'     => 'misc',
    'default'     => 'right',
    'priority'    => 10,
    'choices'     => array(

        'left'   => array(
            esc_attr__( 'Left', 'stratus' ),
        ),
        'full'   => array(
            esc_attr__( 'None', 'stratus' ),
        ),
        'right'   => array(
            esc_attr__( 'Right', 'stratus' ),
        ),

    ),
) );


// FOOTER SECTION
Stratus_Kirki::add_section( 'footer', array(
    'title'      => esc_attr__( 'Widgets', 'stratus' ),
    'priority'   => 2,
    'panel'      => 'th_options',
    'capability' => 'edit_theme_options',
) );



// Footer : Footer Social
Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'        => 'repeater',
    'label'       => esc_html__( 'Social Media Accounts', 'stratus' ),
    'description'        => esc_html__( 'For use with the "Social Icons" Widget. Add your social media accounts here. Use any', 'stratus' ). ' Social icon (e.g.: fa fa-twitter). <a href="http://fontawesome.io/icons/" target="_blank">'.esc_html__( 'Full List Here', 'stratus' ).'</a>',
    'section'     => 'footer',
    'priority'    => 10,
    'row_label' => array(
        'type' => 'text',
        'value' => esc_attr__('Social Icon', 'stratus' ),
    ),
    'settings'    => 'themo_social_media_accounts',
    'default'     => array(
        array(
            'title' => esc_attr__( 'Facebook', 'stratus' ),
            'themo_social_font_icon'  => 'fa fa-twitter',
            'themo_social_url'  => 'https://www.facebook.com',
            'themo_social_url_target'  => 1,
        ),
        array(
            'title' => esc_attr__( 'Twitter', 'stratus' ),
            'themo_social_font_icon'  => 'fa fa-twitter',
            'themo_social_url'  => 'https://twitter.com',
            'themo_social_url_target'  => 1,
        ),
        array(
            'title' => esc_attr__( 'Instagram', 'stratus' ),
            'themo_social_font_icon'  => 'fa fa-instagram',
            'themo_social_url'  => '#',
            'themo_social_url_target'  => 1,
        ),

    ),
    'fields' => array(
        'title' => array(
            'type'        => 'text',
            'label'       => esc_attr__( 'Name', 'stratus' ),
            'default'     => '',
        ),
        'themo_social_font_icon' => array(
            'type'        => 'text',
            'label'       => esc_attr__( 'Social Icon', 'stratus' ),
            'default'     => '',
        ),
        'themo_social_url' => array(
            'type'        => 'text',
            'label'       => esc_attr__( 'Social Link', 'stratus' ),
            'default'     => '',
        ),
        'themo_social_url_target' => array(
            'type'        => 'checkbox',
            'label'       => esc_attr__( 'Open Link in New Window', 'stratus' ),
            'default'     => '',
        ),
    )
) );

// Footer : Footer Payments Accepted
Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'        => 'repeater',
    'label'       => esc_html__( 'Payments Accepted', 'stratus' ),
    'description' => esc_html__( 'For use with the "Payments Accepted" Widget. Add your accepted payments types here.', 'stratus' ),
    'section'     => 'footer',
    'priority'    => 10,
    'row_label' => array(
        'type' => 'text',
        'value' => esc_attr__('Payment Info', 'stratus' ),
    ),
    'settings'    => 'themo_payments_accepted',
    'default'     => array(
        array(
            'title' => esc_attr__( 'Visa', 'stratus' ),
            'themo_payments_accepted_logo'  => '',
            'themo_payment_url'  => 'https://visa.com',
            'themo_payment_url_target'  => 1,
        ),
        array(
            'title' => esc_attr__( 'PayPal', 'stratus' ),
            'themo_payments_accepted_logo'  => '',
            'themo_payment_url'  => 'https://paypal.com',
            'themo_payment_url_target'  => 1,
        ),
        array(
            'title' => esc_attr__( 'MasterCard', 'stratus' ),
            'themo_payments_accepted_logo'  => '',
            'themo_payment_url'  => 'https://mastercard.com',
            'themo_payment_url_target'  => 1,
        ),
    ),
    'fields' => array(
        'title' => array(
            'type'        => 'text',
            'label'       => esc_attr__( 'Name', 'stratus' ),
            'default'     => '',
        ),
        'themo_payments_accepted_logo' => array(
            'type'        => 'image',
            'label'       => esc_attr__( 'Logo', 'stratus' ),
            'default'     => '',
        ),
        'themo_payment_url' => array(
            'type'        => 'text',
            'label'       => esc_attr__( 'Link', 'stratus' ),
            'default'     => '',
        ),
        'themo_payment_url_target' => array(
            'type'        => 'checkbox',
            'label'       => esc_attr__( 'Open Link in New Window', 'stratus' ),
            'default'     => '',
        ),
    )
) );

// Footer : Footer Contact Details
Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'        => 'repeater',
    'label'       => esc_html__( 'Contact Details', 'stratus' ),
    'description' => esc_html__( 'For use with the "Contact Info" Widget. Add your contact info here. Use any', 'stratus' ). ' <a href="http://fontawesome.io/icons/" target="_blank">Font Awesome</a> icon (e.g.: fa fa-twitter). <a href="http://fontawesome.io/icons/" target="_blank">'.esc_html__( 'Full List Here', 'stratus' ).'</a>',
    'section'     => 'footer',
    'priority'    => 10,
    'row_label' => array(
        'type' => 'text',
        'value' => esc_attr__('Contact Info', 'stratus' ),
    ),
    'settings'    => 'themo_contact_icons',
    'default'     => array(
        array(
            'title' => esc_attr__( 'contact@themovation.com', 'stratus' ),
            'themo_contact_icon'  => 'fa fa-envelope-open-o',
            'themo_contact_icon_url'  => 'mailto:contact@ourdomain.com',
            'themo_contact_icon_url_target'  => 1,
        ),
        array(
            'title' => esc_attr__( '1-800-222-4545', 'stratus' ),
            'themo_contact_icon'  => 'fa fa-mobile',
            'themo_contact_icon_url'  => 'tel:800-222-4545',
            'themo_contact_icon_url_target'  => 1,
        ),
        array(
            'title' => esc_attr__( 'Location', 'stratus' ),
            'themo_contact_icon'  => 'fa fa-map-o',
            'themo_contact_icon_url'  => '#',
            'themo_contact_icon_url_target'  => 0,
        ),

    ),
    'fields' => array(
        'title' => array(
            'type'        => 'text',
            'label'       => esc_attr__( 'Name', 'stratus' ),
            'default'     => '',
        ),
        'themo_contact_icon' => array(
            'type'        => 'text',
            'label'       => esc_attr__( 'Icon', 'stratus' ),
            'default'     => '',
        ),
        'themo_contact_icon_url' => array(
            'type'        => 'text',
            'label'       => esc_attr__( 'Link', 'stratus' ),
            'default'     => '',
        ),
        'themo_contact_icon_url_target' => array(
            'type'        => 'checkbox',
            'label'       => esc_attr__( 'Open Link in New Window', 'stratus' ),
            'default'     => '',
        ),
    )
) );

// START PLUGINS SECTION
Stratus_Kirki::add_section('plugins', array(
    'title' => esc_attr__('Plugins', 'stratus'),
    'priority' => 2,
    'panel' => 'th_options',
    'capability' => 'edit_theme_options',
));

Stratus_Kirki::add_field('stratus_theme', array(
    'type' => 'custom',
    'settings' => 'themo_plugins_heading',
    'label' => esc_html__('Enabling bundled plugins', 'stratus'),
    'section' => 'plugins',
    'priority' => 10,
    'default' => '<div class="th-theme-support">' . __('1 - Enable any of the listed bundled plugins.</p></p>2 - Publish your changes</p><p>3 - Follow the admin notice instructions on the WordPress dashboard to install.</p>', 'stratus') . '</div>',
));


// Plugins : WooCommerce
Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'        => 'switch',
    'settings'    => 'themo_tgmpa_woocommerce',
    'label'       => esc_html__( 'WooCommerce', 'stratus' ),
    'section'     => 'plugins',
    'default'     => '',
    'priority'    => 10,
    'choices'     => array(
        'on'  => esc_attr__( 'Enable', 'stratus' ),
        'off' => esc_attr__( 'Disable', 'stratus' ),
    ),
) );

// Plugins : Slider Revolution
Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'        => 'switch',
    'settings'    => 'themo_tgmpa_revslider',
    'label'       => esc_html__( 'Slider Revolution', 'stratus' ),
    'section'     => 'plugins',
    'default'     => '',
    'priority'    => 10,
    'choices'     => array(
        'on'  => esc_attr__( 'Enable', 'stratus' ),
        'off' => esc_attr__( 'Disable', 'stratus' ),
    ),
) );

// Plugins : Groovy Menu
Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'        => 'switch',
    'settings'    => 'themo_tgmpa_groovy_menu',
    'label'       => esc_html__( 'Groovy Menu', 'stratus' ),
    'section'     => 'plugins',
    'default'     => '',
    'priority'    => 10,
    'choices'     => array(
        'on'  => esc_attr__( 'Enable', 'stratus' ),
        'off' => esc_attr__( 'Disable', 'stratus' ),
    ),
) );

// Plugins : Master Slider Pro
Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'        => 'switch',
    'settings'    => 'themo_tgmpa_masterslider',
    'label'       => esc_html__( 'Master Slider Pro', 'stratus' ),
    'section'     => 'plugins',
    'default'     => '',
    'priority'    => 10,
    'choices'     => array(
        'on'  => esc_attr__( 'Enable', 'stratus' ),
        'off' => esc_attr__( 'Disable', 'stratus' ),
    ),
) );

// Plugins : Formidable
Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'        => 'switch',
    'settings'    => 'themo_tgmpa_formidable',
    'label'       => esc_html__( 'Formidable Forms', 'stratus' ),
    'section'     => 'plugins',
    'default'     => 'on',
    'priority'    => 10,
    'choices'     => array(
        'on'  => esc_attr__( 'Enable', 'stratus' ),
        'off' => esc_attr__( 'Disable', 'stratus' ),
    ),
) );

// Plugins : Simple Page Ordering
Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'        => 'switch',
    'settings'    => 'themo_tgmpa_simple_page_ordering',
    'label'       => esc_html__( 'Simple Page Ordering', 'stratus' ),
    'description' => esc_html__( 'Recommended for drag and drop sort ordering of custom post types.', 'stratus' ),
    'section'     => 'plugins',
    'default'     => 'on',
    'priority'    => 10,
    'choices'     => array(
        'on'  => esc_attr__( 'Enable', 'stratus' ),
        'off' => esc_attr__( 'Disable', 'stratus' ),
    ),
) );

/* Plugins : Widget Logic
Stratus_Kirki::add_field( 'stratus_theme', array(
    'type'        => 'switch',
    'settings'    => 'themo_tgmpa_widget_logic',
    'label'       => esc_html__( 'Widget Logic', 'stratus' ),
    'description' => esc_html__( 'Recommended for displaying/hiding widgets on specific pages and areas.', 'stratus' ),
    'section'     => 'plugins',
    'default'     => 'on',
    'priority'    => 10,
    'choices'     => array(
        'on'  => esc_attr__( 'Enable', 'stratus' ),
        'off' => esc_attr__( 'Disable', 'stratus' ),
    ),
) );
*/

// END PLUGINS SECTION

if ( defined('ENVATO_HOSTED_SITE') ) {
    // this is an envato hosted site so Skip
}else {
// SUPPORT SECTION
    Stratus_Kirki::add_section('support', array(
        'title' => esc_attr__('Theme Support', 'stratus'),
        'priority' => 2,
        'panel' => 'th_options',
        'capability' => 'edit_theme_options',
    ));

// Support : Custom
    Stratus_Kirki::add_field('stratus_theme', array(
        'type' => 'custom',
        'settings' => 'themo_help_heading',
        'label' => esc_html__('Yes, we offer support', 'stratus'),
        'section' => 'support',
        'priority' => 10,
        'default' => '<div class="th-theme-support">' . __('We want to make sure this is a great experience for you.</p> <p > If you have any questions, concerns or comments please contact us through the links below.', 'stratus') . '</div>',
    ));

    Stratus_Kirki::add_field('stratus_theme', array(
        'type' => 'custom',
        'settings' => 'themo_help_support_includes',
        'label' => esc_html__('Theme support includes', 'stratus'),
        'section' => 'support',
        'priority' => 10,
        'default' => '<div class="th-theme-support">' . __('<ul><li class="dashicons-before dashicons-yes">Availability of the author to answer questions</li><li class="dashicons-before dashicons-yes">Answering technical questions about item\'s features</li><li class="dashicons-before dashicons-yes">Assistance with reported bugs and issues</li><li class="dashicons-before dashicons-yes">Help with included 3rd party assets</li></ul>', 'stratus') . '</div>',
    ));

    Stratus_Kirki::add_field('stratus_theme', array(
        'type' => 'custom',
        'settings' => 'themo_help_support_not_includes',
        'label' => esc_html__('However, theme support does not include:', 'stratus'),
        'section' => 'support',
        'priority' => 10,
        'default' => '<div class="th-theme-support">' . __('<ul><li class="dashicons-before dashicons-no">Customization services</li><li class="dashicons-before dashicons-no">Installation services</li></ul>', 'stratus') . '</div>',
    ));

    Stratus_Kirki::add_field('stratus_theme', array(
        'type' => 'custom',
        'settings' => 'themo_help_support_links',
        'label' => esc_html__('Where to get help', 'stratus'),
        'section' => 'support',
        'priority' => 10,
        'default' => '<div class="th-theme-support">' . sprintf(__('<p class="dashicons-before dashicons-admin-links"> Check out our <a href="%1$s" target="_blank">helpful guides</a>, <a href="%2$s" target="_blank">online documentation</a> and <a href="%3$s" target="_blank">rockstar support</a>.</p>', 'stratus'), 'http://themovation.helpscoutdocs.com/', 'http://themovation.helpscoutdocs.com/', 'https://themovation.ticksy.com/') . '</div>',
    ));
}