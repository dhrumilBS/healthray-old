<?php
/**
 * Advanced Flip Card Widget for Elementor
 * Fully-featured: typography, colors, borders, shadows, responsive, animations, icons, and more.
 */

namespace MyElements;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Utils;
use Elementor\Icons_Manager;
use Elementor\Plugin;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) exit;

class Flip_Card_Widget extends Widget_Base {

    public function get_name()  { return 'advanced_flip_card'; }
    public function get_title() { return esc_html__( 'Flip Card', 'my-elements' ); }
    public function get_icon()  { return 'eicon-flip-box'; }
    public function get_categories() { return [ 'my-element' ]; }
    public function get_keywords() { return [ 'flip', 'card', 'box', 'hover', '3d' ]; }

    // =========================================================================
    // CONTROLS
    // =========================================================================
    protected function register_controls() {

        // ─── FRONT CONTENT ────────────────────────────────────────────────────
        $this->start_controls_section( 'section_front_content', [
            'label' => esc_html__( '⬛ Front Side', 'my-elements' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control( 'front_icon', [
            'label'   => esc_html__( 'Icon', 'my-elements' ),
            'type'    => Controls_Manager::ICONS,
            'default' => [ 'value' => 'fas fa-star', 'library' => 'fa-solid' ],
        ]);

        $this->add_control( 'front_icon_position', [
            'label'   => esc_html__( 'Icon Position', 'my-elements' ),
            'type'    => Controls_Manager::SELECT,
            'default' => 'top',
            'options' => [
                'top'    => esc_html__( 'Above Title', 'my-elements' ),
                'bottom' => esc_html__( 'Below Title', 'my-elements' ),
            ],
            'condition' => [ 'front_icon[value]!' => '' ],
        ]);

        $this->add_control( 'front_title', [
            'label'       => esc_html__( 'Title', 'my-elements' ),
            'type'        => Controls_Manager::TEXT,
            'default'     => esc_html__( 'John Doe', 'my-elements' ),
            'label_block' => true,
        ]);

        $this->add_control( 'front_title_tag', [
            'label'   => esc_html__( 'Title HTML Tag', 'my-elements' ),
            'type'    => Controls_Manager::SELECT,
            'default' => 'h3',
            'options' => [
                'h1' => 'H1', 'h2' => 'H2', 'h3' => 'H3',
                'h4' => 'H4', 'h5' => 'H5', 'h6' => 'H6',
                'div' => 'div', 'span' => 'span', 'p' => 'p',
            ],
        ]);

        $this->add_control( 'front_desc', [
            'label'       => esc_html__( 'Description', 'my-elements' ),
            'type'        => Controls_Manager::TEXTAREA,
            'default'     => esc_html__( 'Architect & Engineer', 'my-elements' ),
            'rows'        => 4,
        ]);

        $this->end_controls_section();


        // ─── BACK CONTENT ─────────────────────────────────────────────────────
        $this->start_controls_section( 'section_back_content', [
            'label' => esc_html__( '⬜ Back Side', 'my-elements' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control( 'back_icon', [
            'label'   => esc_html__( 'Icon', 'my-elements' ),
            'type'    => Controls_Manager::ICONS,
            'default' => [ 'value' => 'fas fa-heart', 'library' => 'fa-solid' ],
        ]);

        $this->add_control( 'back_icon_position', [
            'label'   => esc_html__( 'Icon Position', 'my-elements' ),
            'type'    => Controls_Manager::SELECT,
            'default' => 'top',
            'options' => [
                'top'    => esc_html__( 'Above Title', 'my-elements' ),
                'bottom' => esc_html__( 'Below Title', 'my-elements' ),
            ],
            'condition' => [ 'back_icon[value]!' => '' ],
        ]);

        $this->add_control( 'back_title', [
            'label'       => esc_html__( 'Title', 'my-elements' ),
            'type'        => Controls_Manager::TEXT,
            'default'     => esc_html__( 'About Me', 'my-elements' ),
            'label_block' => true,
        ]);

        $this->add_control( 'back_title_tag', [
            'label'   => esc_html__( 'Title HTML Tag', 'my-elements' ),
            'type'    => Controls_Manager::SELECT,
            'default' => 'h3',
            'options' => [
                'h1' => 'H1', 'h2' => 'H2', 'h3' => 'H3',
                'h4' => 'H4', 'h5' => 'H5', 'h6' => 'H6',
                'div' => 'div', 'span' => 'span', 'p' => 'p',
            ],
        ]);

        $this->add_control( 'back_desc', [
            'label'       => esc_html__( 'Description', 'my-elements' ),
            'type'        => Controls_Manager::TEXTAREA,
            'default'     => esc_html__( 'Passionate creator, problem-solver, and lifelong learner.', 'my-elements' ),
            'rows'        => 4,
        ]);

        $this->add_control( 'back_button_heading', [
            'label'     => esc_html__( 'Button', 'my-elements' ),
            'type'      => Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_control( 'back_button_text', [
            'label'   => esc_html__( 'Button Text', 'my-elements' ),
            'type'    => Controls_Manager::TEXT,
            'default' => esc_html__( 'Learn More', 'my-elements' ),
        ]);

        $this->add_control( 'back_button_link', [
            'label'         => esc_html__( 'Button Link', 'my-elements' ),
            'type'          => Controls_Manager::URL,
            'placeholder'   => 'https://your-link.com',
            'show_external' => true,
            'default'       => [ 'url' => '' ],
        ]);

        $this->end_controls_section();


        // ─── SETTINGS ─────────────────────────────────────────────────────────
        $this->start_controls_section( 'section_settings', [
            'label' => esc_html__( '⚙ Settings', 'my-elements' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control( 'flip_direction', [
            'label'   => esc_html__( 'Flip Direction', 'my-elements' ),
            'type'    => Controls_Manager::SELECT,
            'default' => 'horizontal',
            'options' => [
                'horizontal' => esc_html__( 'Horizontal (Y-Axis)', 'my-elements' ),
                'vertical'   => esc_html__( 'Vertical (X-Axis)', 'my-elements' ),
            ],
        ]);

        $this->add_control( 'flip_trigger', [
            'label'   => esc_html__( 'Flip Trigger', 'my-elements' ),
            'type'    => Controls_Manager::SELECT,
            'default' => 'hover',
            'options' => [
                'hover' => esc_html__( 'Hover', 'my-elements' ),
                'click' => esc_html__( 'Click', 'my-elements' ),
            ],
        ]);

        $this->add_control( 'flip_duration', [
            'label'   => esc_html__( 'Flip Speed (seconds)', 'my-elements' ),
            'type'    => Controls_Manager::SLIDER,
            'range'   => [ 's' => [ 'min' => 0.1, 'max' => 3, 'step' => 0.1 ] ],
            'default' => [ 'size' => 0.6, 'unit' => 's' ],
            'size_units' => [ 's' ],
        ]);

        $this->add_responsive_control( 'card_height', [
            'label'      => esc_html__( 'Card Height', 'my-elements' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px', 'vh', 'em' ],
            'range'      => [
                'px' => [ 'min' => 100, 'max' => 800, 'step' => 10 ],
                'vh' => [ 'min' => 10,  'max' => 100, 'step' => 1  ],
                'em' => [ 'min' => 5,   'max' => 50,  'step' => 0.5 ],
            ],
            'default'    => [ 'size' => 300, 'unit' => 'px' ],
            'selectors'  => [
                '{{WRAPPER}} .afc-flip-card' => 'height: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control( 'content_align', [
            'label'     => esc_html__( 'Content Alignment', 'my-elements' ),
            'type'      => Controls_Manager::CHOOSE,
            'options'   => [
                'flex-start' => [ 'title' => esc_html__( 'Top',    'my-elements' ), 'icon' => 'eicon-v-align-top'    ],
                'center'     => [ 'title' => esc_html__( 'Middle', 'my-elements' ), 'icon' => 'eicon-v-align-middle' ],
                'flex-end'   => [ 'title' => esc_html__( 'Bottom', 'my-elements' ), 'icon' => 'eicon-v-align-bottom' ],
            ],
            'default'   => 'center',
            'selectors' => [
                '{{WRAPPER}} .afc-side-inner' => 'justify-content: {{VALUE}};',
            ],
        ]);

        $this->add_responsive_control( 'text_align', [
            'label'     => esc_html__( 'Text Alignment', 'my-elements' ),
            'type'      => Controls_Manager::CHOOSE,
            'options'   => [
                'left'   => [ 'title' => esc_html__( 'Left',   'my-elements' ), 'icon' => 'eicon-text-align-left'   ],
                'center' => [ 'title' => esc_html__( 'Center', 'my-elements' ), 'icon' => 'eicon-text-align-center' ],
                'right'  => [ 'title' => esc_html__( 'Right',  'my-elements' ), 'icon' => 'eicon-text-align-right'  ],
            ],
            'default'   => 'center',
            'selectors' => [
                '{{WRAPPER}} .afc-flip-card-front, {{WRAPPER}} .afc-flip-card-back' => 'text-align: {{VALUE}};',
            ],
        ]);

        $this->end_controls_section();


        // =========================================================================
        // STYLE TAB
        // =========================================================================

        // ─── CARD WRAPPER STYLE ───────────────────────────────────────────────
        $this->start_controls_section( 'section_style_card', [
            'label' => esc_html__( '🃏 Card', 'my-elements' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_responsive_control( 'card_padding', [
            'label'      => esc_html__( 'Content Padding', 'my-elements' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em', '%' ],
            'default'    => [ 'top' => '30', 'right' => '30', 'bottom' => '30', 'left' => '30', 'unit' => 'px', 'isLinked' => true ],
            'selectors'  => [
                '{{WRAPPER}} .afc-side-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_responsive_control( 'card_border_radius', [
            'label'      => esc_html__( 'Border Radius', 'my-elements' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%', 'em' ],
            'default'    => [ 'top' => '16', 'right' => '16', 'bottom' => '16', 'left' => '16', 'unit' => 'px', 'isLinked' => true ],
            'selectors'  => [
                '{{WRAPPER}} .afc-flip-card-front' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                '{{WRAPPER}} .afc-flip-card-back'  => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                '{{WRAPPER}} .afc-flip-card'        => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_group_control( Group_Control_Box_Shadow::get_type(), [
            'name'     => 'card_box_shadow',
            'label'    => esc_html__( 'Box Shadow', 'my-elements' ),
            'selector' => '{{WRAPPER}} .afc-flip-card-inner',
        ]);

        $this->add_control( 'card_hover_shadow_heading', [
            'label'     => esc_html__( 'Hover Shadow', 'my-elements' ),
            'type'      => Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_group_control( Group_Control_Box_Shadow::get_type(), [
            'name'     => 'card_hover_box_shadow',
            'label'    => esc_html__( 'Hover Box Shadow', 'my-elements' ),
            'selector' => '{{WRAPPER}} .afc-flip-card:hover .afc-flip-card-inner',
        ]);

        $this->end_controls_section();


        // ─── FRONT STYLE ──────────────────────────────────────────────────────
        $this->start_controls_section( 'section_style_front', [
            'label' => esc_html__( '⬛ Front Style', 'my-elements' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_group_control( Group_Control_Background::get_type(), [
            'name'     => 'front_background',
            'label'    => esc_html__( 'Background', 'my-elements' ),
            'types'    => [ 'classic', 'gradient' ],
            'selector' => '{{WRAPPER}} .afc-flip-card-front',
            'fields_options' => [
                'background' => [ 'default' => 'classic' ],
                'color'      => [ 'default' => '#1a1a2e' ],
            ],
        ]);

        $this->add_group_control( Group_Control_Border::get_type(), [
            'name'      => 'front_border',
            'label'     => esc_html__( 'Border', 'my-elements' ),
            'selector'  => '{{WRAPPER}} .afc-flip-card-front',
            'separator' => 'before',
        ]);

        // Front Icon
        $this->add_control( 'front_icon_style_heading', [
            'label'     => esc_html__( 'Icon', 'my-elements' ),
            'type'      => Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_responsive_control( 'front_icon_size', [
            'label'      => esc_html__( 'Icon Size', 'my-elements' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px', 'em' ],
            'range'      => [ 'px' => [ 'min' => 10, 'max' => 120 ] ],
            'default'    => [ 'size' => 40, 'unit' => 'px' ],
            'selectors'  => [
                '{{WRAPPER}} .afc-flip-card-front .afc-icon i'   => 'font-size: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .afc-flip-card-front .afc-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_control( 'front_icon_color', [
            'label'     => esc_html__( 'Icon Color', 'my-elements' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#ffffff',
            'selectors' => [
                '{{WRAPPER}} .afc-flip-card-front .afc-icon i'        => 'color: {{VALUE}};',
                '{{WRAPPER}} .afc-flip-card-front .afc-icon svg path' => 'fill: {{VALUE}};',
            ],
        ]);

        $this->add_responsive_control( 'front_icon_spacing', [
            'label'      => esc_html__( 'Icon Spacing', 'my-elements' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range'      => [ 'px' => [ 'min' => 0, 'max' => 60 ] ],
            'default'    => [ 'size' => 16, 'unit' => 'px' ],
            'selectors'  => [
                '{{WRAPPER}} .afc-flip-card-front .afc-icon' => 'margin-bottom: {{SIZE}}{{UNIT}};',
            ],
        ]);

        // Front Icon Background
        $this->add_control( 'front_icon_bg_toggle', [
            'label'        => esc_html__( 'Icon Background', 'my-elements' ),
            'type'         => Controls_Manager::POPOVER_TOGGLE,
            'label_off'    => esc_html__( 'Default', 'my-elements' ),
            'label_on'     => esc_html__( 'Custom', 'my-elements' ),
            'return_value' => 'yes',
        ]);

        $this->start_popover();

        $this->add_control( 'front_icon_bg_color', [
            'label'     => esc_html__( 'Background Color', 'my-elements' ),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .afc-flip-card-front .afc-icon' => 'background-color: {{VALUE}};',
            ],
            'condition' => [ 'front_icon_bg_toggle' => 'yes' ],
        ]);

        $this->add_responsive_control( 'front_icon_bg_size', [
            'label'      => esc_html__( 'Box Size', 'my-elements' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range'      => [ 'px' => [ 'min' => 20, 'max' => 150 ] ],
            'default'    => [ 'size' => 80, 'unit' => 'px' ],
            'selectors'  => [
                '{{WRAPPER}} .afc-flip-card-front .afc-icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
            ],
            'condition'  => [ 'front_icon_bg_toggle' => 'yes' ],
        ]);

        $this->add_responsive_control( 'front_icon_bg_radius', [
            'label'      => esc_html__( 'Border Radius', 'my-elements' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px', '%' ],
            'range'      => [ 'px' => [ 'min' => 0, 'max' => 100 ], '%' => [ 'min' => 0, 'max' => 50 ] ],
            'default'    => [ 'size' => 50, 'unit' => '%' ],
            'selectors'  => [
                '{{WRAPPER}} .afc-flip-card-front .afc-icon' => 'border-radius: {{SIZE}}{{UNIT}};',
            ],
            'condition'  => [ 'front_icon_bg_toggle' => 'yes' ],
        ]);

        $this->end_popover();

        // Front Title
        $this->add_control( 'front_title_style_heading', [
            'label'     => esc_html__( 'Title', 'my-elements' ),
            'type'      => Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_control( 'front_title_color', [
            'label'     => esc_html__( 'Title Color', 'my-elements' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#ffffff',
            'selectors' => [
                '{{WRAPPER}} .afc-flip-card-front .afc-title' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'front_title_typography',
            'label'    => esc_html__( 'Title Typography', 'my-elements' ),
            'selector' => '{{WRAPPER}} .afc-flip-card-front .afc-title',
        ]);

        $this->add_group_control( Group_Control_Text_Shadow::get_type(), [
            'name'     => 'front_title_text_shadow',
            'label'    => esc_html__( 'Title Text Shadow', 'my-elements' ),
            'selector' => '{{WRAPPER}} .afc-flip-card-front .afc-title',
        ]);

        $this->add_responsive_control( 'front_title_spacing', [
            'label'      => esc_html__( 'Title Spacing (Bottom)', 'my-elements' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px', 'em' ],
            'range'      => [ 'px' => [ 'min' => 0, 'max' => 80 ] ],
            'default'    => [ 'size' => 10, 'unit' => 'px' ],
            'selectors'  => [
                '{{WRAPPER}} .afc-flip-card-front .afc-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
            ],
        ]);

        // Front Description
        $this->add_control( 'front_desc_style_heading', [
            'label'     => esc_html__( 'Description', 'my-elements' ),
            'type'      => Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_control( 'front_desc_color', [
            'label'     => esc_html__( 'Description Color', 'my-elements' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => 'rgba(255,255,255,0.75)',
            'selectors' => [
                '{{WRAPPER}} .afc-flip-card-front .afc-desc' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'front_desc_typography',
            'label'    => esc_html__( 'Description Typography', 'my-elements' ),
            'selector' => '{{WRAPPER}} .afc-flip-card-front .afc-desc',
        ]);

        $this->add_group_control( Group_Control_Text_Shadow::get_type(), [
            'name'     => 'front_desc_text_shadow',
            'label'    => esc_html__( 'Description Text Shadow', 'my-elements' ),
            'selector' => '{{WRAPPER}} .afc-flip-card-front .afc-desc',
        ]);

        $this->end_controls_section();


        // ─── BACK STYLE ───────────────────────────────────────────────────────
        $this->start_controls_section( 'section_style_back', [
            'label' => esc_html__( '⬜ Back Style', 'my-elements' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_group_control( Group_Control_Background::get_type(), [
            'name'     => 'back_background',
            'label'    => esc_html__( 'Background', 'my-elements' ),
            'types'    => [ 'classic', 'gradient' ],
            'selector' => '{{WRAPPER}} .afc-flip-card-back',
            'fields_options' => [
                'background' => [ 'default' => 'classic' ],
                'color'      => [ 'default' => '#16213e' ],
            ],
        ]);

        $this->add_group_control( Group_Control_Border::get_type(), [
            'name'      => 'back_border',
            'label'     => esc_html__( 'Border', 'my-elements' ),
            'selector'  => '{{WRAPPER}} .afc-flip-card-back',
            'separator' => 'before',
        ]);

        // Back Icon
        $this->add_control( 'back_icon_style_heading', [
            'label'     => esc_html__( 'Icon', 'my-elements' ),
            'type'      => Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_responsive_control( 'back_icon_size', [
            'label'      => esc_html__( 'Icon Size', 'my-elements' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px', 'em' ],
            'range'      => [ 'px' => [ 'min' => 10, 'max' => 120 ] ],
            'default'    => [ 'size' => 40, 'unit' => 'px' ],
            'selectors'  => [
                '{{WRAPPER}} .afc-flip-card-back .afc-icon i'   => 'font-size: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .afc-flip-card-back .afc-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_control( 'back_icon_color', [
            'label'     => esc_html__( 'Icon Color', 'my-elements' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#ffffff',
            'selectors' => [
                '{{WRAPPER}} .afc-flip-card-back .afc-icon i'        => 'color: {{VALUE}};',
                '{{WRAPPER}} .afc-flip-card-back .afc-icon svg path' => 'fill: {{VALUE}};',
            ],
        ]);

        $this->add_responsive_control( 'back_icon_spacing', [
            'label'      => esc_html__( 'Icon Spacing', 'my-elements' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range'      => [ 'px' => [ 'min' => 0, 'max' => 60 ] ],
            'default'    => [ 'size' => 16, 'unit' => 'px' ],
            'selectors'  => [
                '{{WRAPPER}} .afc-flip-card-back .afc-icon' => 'margin-bottom: {{SIZE}}{{UNIT}};',
            ],
        ]);

        // Back Icon Background
        $this->add_control( 'back_icon_bg_toggle', [
            'label'        => esc_html__( 'Icon Background', 'my-elements' ),
            'type'         => Controls_Manager::POPOVER_TOGGLE,
            'label_off'    => esc_html__( 'Default', 'my-elements' ),
            'label_on'     => esc_html__( 'Custom', 'my-elements' ),
            'return_value' => 'yes',
        ]);

        $this->start_popover();

        $this->add_control( 'back_icon_bg_color', [
            'label'     => esc_html__( 'Background Color', 'my-elements' ),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .afc-flip-card-back .afc-icon' => 'background-color: {{VALUE}};',
            ],
            'condition' => [ 'back_icon_bg_toggle' => 'yes' ],
        ]);

        $this->add_responsive_control( 'back_icon_bg_size', [
            'label'      => esc_html__( 'Box Size', 'my-elements' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range'      => [ 'px' => [ 'min' => 20, 'max' => 150 ] ],
            'default'    => [ 'size' => 80, 'unit' => 'px' ],
            'selectors'  => [
                '{{WRAPPER}} .afc-flip-card-back .afc-icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
            ],
            'condition'  => [ 'back_icon_bg_toggle' => 'yes' ],
        ]);

        $this->add_responsive_control( 'back_icon_bg_radius', [
            'label'      => esc_html__( 'Border Radius', 'my-elements' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px', '%' ],
            'range'      => [ 'px' => [ 'min' => 0, 'max' => 100 ], '%' => [ 'min' => 0, 'max' => 50 ] ],
            'default'    => [ 'size' => 50, 'unit' => '%' ],
            'selectors'  => [
                '{{WRAPPER}} .afc-flip-card-back .afc-icon' => 'border-radius: {{SIZE}}{{UNIT}};',
            ],
            'condition'  => [ 'back_icon_bg_toggle' => 'yes' ],
        ]);

        $this->end_popover();

        // Back Title
        $this->add_control( 'back_title_style_heading', [
            'label'     => esc_html__( 'Title', 'my-elements' ),
            'type'      => Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_control( 'back_title_color', [
            'label'     => esc_html__( 'Title Color', 'my-elements' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#ffffff',
            'selectors' => [
                '{{WRAPPER}} .afc-flip-card-back .afc-title' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'back_title_typography',
            'label'    => esc_html__( 'Title Typography', 'my-elements' ),
            'selector' => '{{WRAPPER}} .afc-flip-card-back .afc-title',
        ]);

        $this->add_group_control( Group_Control_Text_Shadow::get_type(), [
            'name'     => 'back_title_text_shadow',
            'label'    => esc_html__( 'Title Text Shadow', 'my-elements' ),
            'selector' => '{{WRAPPER}} .afc-flip-card-back .afc-title',
        ]);

        $this->add_responsive_control( 'back_title_spacing', [
            'label'      => esc_html__( 'Title Spacing (Bottom)', 'my-elements' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px', 'em' ],
            'range'      => [ 'px' => [ 'min' => 0, 'max' => 80 ] ],
            'default'    => [ 'size' => 10, 'unit' => 'px' ],
            'selectors'  => [
                '{{WRAPPER}} .afc-flip-card-back .afc-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
            ],
        ]);

        // Back Description
        $this->add_control( 'back_desc_style_heading', [
            'label'     => esc_html__( 'Description', 'my-elements' ),
            'type'      => Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_control( 'back_desc_color', [
            'label'     => esc_html__( 'Description Color', 'my-elements' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => 'rgba(255,255,255,0.75)',
            'selectors' => [
                '{{WRAPPER}} .afc-flip-card-back .afc-desc' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'     => 'back_desc_typography',
            'label'    => esc_html__( 'Description Typography', 'my-elements' ),
            'selector' => '{{WRAPPER}} .afc-flip-card-back .afc-desc',
        ]);

        $this->add_group_control( Group_Control_Text_Shadow::get_type(), [
            'name'     => 'back_desc_text_shadow',
            'label'    => esc_html__( 'Description Text Shadow', 'my-elements' ),
            'selector' => '{{WRAPPER}} .afc-flip-card-back .afc-desc',
        ]);

        // ─── Button Style ─────────────────────────────────────────────────────
        $this->add_control( 'back_button_style_heading', [
            'label'     => esc_html__( 'Button', 'my-elements' ),
            'type'      => Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_responsive_control( 'button_spacing', [
            'label'      => esc_html__( 'Button Spacing (Top)', 'my-elements' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px', 'em' ],
            'range'      => [ 'px' => [ 'min' => 0, 'max' => 80 ] ],
            'default'    => [ 'size' => 20, 'unit' => 'px' ],
            'selectors'  => [
                '{{WRAPPER}} .afc-flip-card-back .afc-button' => 'margin-top: {{SIZE}}{{UNIT}};',
            ],
            'condition'  => [ 'back_button_text!' => '' ],
        ]);

        $this->start_controls_tabs( 'button_style_tabs', [
            'condition' => [ 'back_button_text!' => '' ],
        ]);

        $this->start_controls_tab( 'button_tab_normal', [
            'label' => esc_html__( 'Normal', 'my-elements' ),
        ]);

        $this->add_control( 'button_text_color', [
            'label'     => esc_html__( 'Text Color', 'my-elements' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#ffffff',
            'selectors' => [
                '{{WRAPPER}} .afc-button' => 'color: {{VALUE}}; fill: {{VALUE}};',
            ],
        ]);

        $this->add_group_control( Group_Control_Background::get_type(), [
            'name'     => 'button_background',
            'types'    => [ 'classic', 'gradient' ],
            'selector' => '{{WRAPPER}} .afc-button',
            'fields_options' => [
                'background' => [ 'default' => 'classic' ],
                'color'      => [ 'default' => '#0f3460' ],
            ],
        ]);

        $this->end_controls_tab();

        $this->start_controls_tab( 'button_tab_hover', [
            'label' => esc_html__( 'Hover', 'my-elements' ),
        ]);

        $this->add_control( 'button_hover_text_color', [
            'label'     => esc_html__( 'Text Color', 'my-elements' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#ffffff',
            'selectors' => [
                '{{WRAPPER}} .afc-button:hover' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_group_control( Group_Control_Background::get_type(), [
            'name'     => 'button_hover_background',
            'types'    => [ 'classic', 'gradient' ],
            'selector' => '{{WRAPPER}} .afc-button:hover',
            'fields_options' => [
                'background' => [ 'default' => 'classic' ],
                'color'      => [ 'default' => '#e94560' ],
            ],
        ]);

        $this->add_control( 'button_hover_border_color', [
            'label'     => esc_html__( 'Border Color', 'my-elements' ),
            'type'      => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .afc-button:hover' => 'border-color: {{VALUE}};',
            ],
        ]);

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_group_control( Group_Control_Typography::get_type(), [
            'name'      => 'button_typography',
            'selector'  => '{{WRAPPER}} .afc-button',
            'separator' => 'before',
            'condition' => [ 'back_button_text!' => '' ],
        ]);

        $this->add_group_control( Group_Control_Border::get_type(), [
            'name'      => 'button_border',
            'selector'  => '{{WRAPPER}} .afc-button',
            'condition' => [ 'back_button_text!' => '' ],
        ]);

        $this->add_responsive_control( 'button_border_radius', [
            'label'      => esc_html__( 'Border Radius', 'my-elements' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%' ],
            'default'    => [ 'top' => '8', 'right' => '8', 'bottom' => '8', 'left' => '8', 'unit' => 'px', 'isLinked' => true ],
            'selectors'  => [
                '{{WRAPPER}} .afc-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'condition'  => [ 'back_button_text!' => '' ],
        ]);

        $this->add_responsive_control( 'button_padding', [
            'label'      => esc_html__( 'Padding', 'my-elements' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em' ],
            'default'    => [ 'top' => '10', 'right' => '24', 'bottom' => '10', 'left' => '24', 'unit' => 'px', 'isLinked' => false ],
            'selectors'  => [
                '{{WRAPPER}} .afc-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'condition'  => [ 'back_button_text!' => '' ],
        ]);

        $this->add_group_control( Group_Control_Box_Shadow::get_type(), [
            'name'      => 'button_box_shadow',
            'selector'  => '{{WRAPPER}} .afc-button',
            'condition' => [ 'back_button_text!' => '' ],
        ]);

        $this->end_controls_section();

    } // end register_controls


    // =========================================================================
    // RENDER
    // =========================================================================
    protected function render() {
        $s = $this->get_settings_for_display();

        // Build inline style for transition duration
        $duration    = ! empty( $s['flip_duration']['size'] ) ? floatval( $s['flip_duration']['size'] ) : 0.6;
        $unit        = ! empty( $s['flip_duration']['unit'] ) ? $s['flip_duration']['unit'] : 's';
        $trans_style = 'transition: transform ' . $duration . $unit . ';';

        // Direction classes
        $direction_class = ( 'vertical' === $s['flip_direction'] ) ? 'afc-flip-vertical' : 'afc-flip-horizontal';

        // Trigger classes
        $trigger_class = ( 'click' === $s['flip_trigger'] ) ? 'afc-flip-click' : '';

        // Wrapper data attributes for JS
        $this->add_render_attribute( 'flip_card', [
            'class'            => [ 'afc-flip-card', $direction_class, $trigger_class ],
            'role'             => 'region',
            'aria-label'       => esc_attr( $s['front_title'] ),
        ]);

        $this->add_render_attribute( 'flip_card_inner', [
            'class' => 'afc-flip-card-inner',
            'style' => esc_attr( $trans_style ),
        ]);

        $front_tag = Utils::validate_html_tag( $s['front_title_tag'] );
        $back_tag  = Utils::validate_html_tag( $s['back_title_tag'] );

        // Button link attributes
        $btn_attrs = '';
        if ( ! empty( $s['back_button_link']['url'] ) ) {
            $this->add_link_attributes( 'back_button_link', $s['back_button_link'] );
            $btn_attrs = $this->get_render_attribute_string( 'back_button_link' );
        }
        ?>

        <div <?php echo $this->get_render_attribute_string( 'flip_card' ); ?>>
            <div <?php echo $this->get_render_attribute_string( 'flip_card_inner' ); ?>>

                <?php /* ── FRONT ── */ ?>
                <div class="afc-flip-card-front">
                    <div class="afc-side-inner">

                        <?php if ( ! empty( $s['front_icon']['value'] ) && 'top' === $s['front_icon_position'] ) : ?>
                            <span class="afc-icon">
                                <?php Icons_Manager::render_icon( $s['front_icon'], [ 'aria-hidden' => 'true' ] ); ?>
                            </span>
                        <?php endif; ?>

                        <?php if ( ! empty( $s['front_title'] ) ) : ?>
                            <<?php echo esc_html( $front_tag ); ?> class="afc-title">
                                <?php echo esc_html( $s['front_title'] ); ?>
                            </<?php echo esc_html( $front_tag ); ?>>
                        <?php endif; ?>

                        <?php if ( ! empty( $s['front_icon']['value'] ) && 'bottom' === $s['front_icon_position'] ) : ?>
                            <span class="afc-icon">
                                <?php Icons_Manager::render_icon( $s['front_icon'], [ 'aria-hidden' => 'true' ] ); ?>
                            </span>
                        <?php endif; ?>

                        <?php if ( ! empty( $s['front_desc'] ) ) : ?>
                            <p class="afc-desc"><?php echo wp_kses_post( $s['front_desc'] ); ?></p>
                        <?php endif; ?>

                    </div>
                </div>

                <?php /* ── BACK ── */ ?>
                <div class="afc-flip-card-back">
                    <div class="afc-side-inner">

                        <?php if ( ! empty( $s['back_icon']['value'] ) && 'top' === $s['back_icon_position'] ) : ?>
                            <span class="afc-icon">
                                <?php Icons_Manager::render_icon( $s['back_icon'], [ 'aria-hidden' => 'true' ] ); ?>
                            </span>
                        <?php endif; ?>

                        <?php if ( ! empty( $s['back_title'] ) ) : ?>
                            <<?php echo esc_html( $back_tag ); ?> class="afc-title">
                                <?php echo esc_html( $s['back_title'] ); ?>
                            </<?php echo esc_html( $back_tag ); ?>>
                        <?php endif; ?>

                        <?php if ( ! empty( $s['back_icon']['value'] ) && 'bottom' === $s['back_icon_position'] ) : ?>
                            <span class="afc-icon">
                                <?php Icons_Manager::render_icon( $s['back_icon'], [ 'aria-hidden' => 'true' ] ); ?>
                            </span>
                        <?php endif; ?>

                        <?php if ( ! empty( $s['back_desc'] ) ) : ?>
                            <p class="afc-desc"><?php echo wp_kses_post( $s['back_desc'] ); ?></p>
                        <?php endif; ?>

                        <?php if ( ! empty( $s['back_button_text'] ) ) : ?>
                            <a class="afc-button" <?php echo $btn_attrs; ?>>
                                <?php echo esc_html( $s['back_button_text'] ); ?>
                            </a>
                        <?php endif; ?>

                    </div>
                </div>

            </div><!-- /.afc-flip-card-inner -->
        </div><!-- /.afc-flip-card -->

        <?php $this->render_css(); ?>
        <?php $this->render_js(); ?>

        <?php
    }

    // =========================================================================
    // BASE CSS  (structural only — all design via Elementor selectors above)
    // =========================================================================
    private function render_css() {
        $uid = 'afc-' . $this->get_id();
        ?>
        <style id="<?php echo esc_attr( $uid ); ?>">
            /* ── Wrapper ── */
            .afc-flip-card {
                perspective: 1200px;
                width: 100%;
                cursor: pointer;
                -webkit-tap-highlight-color: transparent;
            }

            /* ── Inner ── */
            .afc-flip-card-inner {
                position: relative;
                width: 100%;
                height: 100%;
                transform-style: preserve-3d;
                will-change: transform;
            }

            /* ── Both sides ── */
            .afc-flip-card-front,
            .afc-flip-card-back {
                position: absolute;
                inset: 0;
                width: 100%;
                height: 100%;
                overflow: hidden;
                -webkit-backface-visibility: hidden;
                backface-visibility: hidden;
            }

            /* ── Side inner (flex layout) ── */
            .afc-side-inner {
                display: flex;
                flex-direction: column;
                align-items: center;
                width: 100%;
                height: 100%;
                box-sizing: border-box;
            }

            /* ── Horizontal flip (default) ── */
            .afc-flip-horizontal .afc-flip-card-back {
                transform: rotateY(180deg);
            }
            .afc-flip-horizontal:not(.afc-flip-click):hover .afc-flip-card-inner,
            .afc-flip-horizontal.afc-flipped .afc-flip-card-inner {
                transform: rotateY(180deg);
            }

            /* ── Vertical flip ── */
            .afc-flip-vertical .afc-flip-card-back {
                transform: rotateX(180deg);
            }
            .afc-flip-vertical:not(.afc-flip-click):hover .afc-flip-card-inner,
            .afc-flip-vertical.afc-flipped .afc-flip-card-inner {
                transform: rotateX(180deg);
            }

            /* ── Icon wrapper ── */
            .afc-icon {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }
            .afc-icon svg,
            .afc-icon i {
                display: block;
            }

            /* ── Headings / text reset ── */
            .afc-flip-card .afc-title {
                margin: 0 0 10px;
                line-height: 1.2;
            }
            .afc-flip-card .afc-desc {
                margin: 0;
                line-height: 1.6;
            }

            /* ── Button ── */
            .afc-button {
                display: inline-block;
                text-decoration: none;
                cursor: pointer;
                line-height: 1;
                transition: background-color 0.25s, color 0.25s, border-color 0.25s, box-shadow 0.25s;
            }
            .afc-button:hover {
                text-decoration: none;
            }
        </style>
        <?php
    }

    // =========================================================================
    // CLICK-TRIGGER JS  (minimal, no jQuery dependency)
    // =========================================================================
    private function render_js() {
        $s = $this->get_settings_for_display();
        if ( 'click' !== $s['flip_trigger'] ) return;
        ?>
        <script>
        (function () {
            var cards = document.querySelectorAll('.afc-flip-click');
            cards.forEach(function (card) {
                if (card.dataset.afcInit) return;
                card.dataset.afcInit = '1';
                card.addEventListener('click', function () {
                    this.classList.toggle('afc-flipped');
                });
                card.addEventListener('keydown', function (e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        this.classList.toggle('afc-flipped');
                    }
                });
                card.setAttribute('tabindex', '0');
            });
        })();
        </script>
        <?php
    }

    // =========================================================================
    // CONTENT TEMPLATE (Elementor editor live preview)
    // =========================================================================
    protected function content_template() {
        ?>
        <#
        var flipClass = settings.flip_direction === 'vertical' ? 'afc-flip-vertical' : 'afc-flip-horizontal';
        var triggerClass = settings.flip_trigger === 'click' ? 'afc-flip-click' : '';
        var duration = settings.flip_duration.size || 0.6;
        var unit = settings.flip_duration.unit || 's';
        var transStyle = 'transition: transform ' + duration + unit + ';';
        var frontTag = settings.front_title_tag || 'h3';
        var backTag  = settings.back_title_tag  || 'h3';

        var frontIconPos = settings.front_icon_position || 'top';
        var backIconPos  = settings.back_icon_position  || 'top';

        var iconHTML_front = '';
        if ( settings.front_icon && settings.front_icon.value ) {
            var iconView = new Proxy({}, { get: function(t,p) { return ''; } });
            iconHTML_front = '<span class="afc-icon"><i class="' + settings.front_icon.value + '"></i></span>';
        }
        var iconHTML_back = '';
        if ( settings.back_icon && settings.back_icon.value ) {
            iconHTML_back = '<span class="afc-icon"><i class="' + settings.back_icon.value + '"></i></span>';
        }
        #>
        <div class="afc-flip-card {{ flipClass }} {{ triggerClass }}">
            <div class="afc-flip-card-inner" style="{{ transStyle }}">

                <div class="afc-flip-card-front">
                    <div class="afc-side-inner">
                        <# if ( iconHTML_front && frontIconPos === 'top' ) { #>{{{ iconHTML_front }}}<# } #>
                        <# if ( settings.front_title ) { #>
                            <{{{ frontTag }}} class="afc-title">{{{ settings.front_title }}}</{{{ frontTag }}}>
                        <# } #>
                        <# if ( iconHTML_front && frontIconPos === 'bottom' ) { #>{{{ iconHTML_front }}}<# } #>
                        <# if ( settings.front_desc ) { #>
                            <p class="afc-desc">{{{ settings.front_desc }}}</p>
                        <# } #>
                    </div>
                </div>

                <div class="afc-flip-card-back">
                    <div class="afc-side-inner">
                        <# if ( iconHTML_back && backIconPos === 'top' ) { #>{{{ iconHTML_back }}}<# } #>
                        <# if ( settings.back_title ) { #>
                            <{{{ backTag }}} class="afc-title">{{{ settings.back_title }}}</{{{ backTag }}}>
                        <# } #>
                        <# if ( iconHTML_back && backIconPos === 'bottom' ) { #>{{{ iconHTML_back }}}<# } #>
                        <# if ( settings.back_desc ) { #>
                            <p class="afc-desc">{{{ settings.back_desc }}}</p>
                        <# } #>
                        <# if ( settings.back_button_text ) { #>
                            <a class="afc-button">{{{ settings.back_button_text }}}</a>
                        <# } #>
                    </div>
                </div>

            </div>
        </div>
        <?php
    }
}

// Register widget
Plugin::instance()->widgets_manager->register( new Flip_Card_Widget() );