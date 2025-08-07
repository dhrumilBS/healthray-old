<?php

namespace Elementor;

if (! defined('ABSPATH')) exit;

class ML_Slider_Controls
{
	public function get_slider_btn_controls($obj)
	{
		$obj->start_controls_section(
			'section_control',
			[
				'label' => __('Slider Control', 'my-element'),
			]
		);

		$obj->add_control(
			'desk_num',
			[
				'label' => __('Desktop number', 'my-element'),
				'type' => Controls_Manager::NUMBER,
				'default' => __('3', 'my-element'),
			]
		);
		$obj->add_control(
			'lap_num',
			[
				'label' => __('Laptop number', 'my-element'),
				'type' => Controls_Manager::NUMBER,
				'default' => __('3', 'my-element'),

			]
		);
		$obj->add_control(
			'tab_num',
			[
				'label' => __('Tablet number', 'my-element'),
				'type' => Controls_Manager::NUMBER,
				'default' => __('2', 'my-element'),
			]
		);
		$obj->add_control(
			'mob_num',
			[
				'label' => __('Mobile number', 'my-element'),
				'type' => Controls_Manager::NUMBER,
				'default' => __('1', 'my-element'),
				'separator' => 'after',
			]
		);
		$obj->add_control(
			'autoplay',
			[
				'label'      => __('Autoplay', 'my-element'),
				'type'       => Controls_Manager::SELECT,
				'default'    => 'true',
				'options'    => [
					'true'       => __('True', 'my-element'),
					'false'          => __('False', 'my-element'),
				],
			]
		);
		$obj->add_control(
			'loop',
			[
				'label'      => __('Loop', 'my-element'),
				'type'       => Controls_Manager::SELECT,
				'default'    => 'true',
				'options'    => [
					'true'       => __('True', 'my-element'),
					'false'          => __('False', 'my-element'),
				],
			]
		);
		$obj->add_control(
			'nav_arrow',
			[
				'label'      => __('Navigation Arrow', 'my-element'),
				'type'       => Controls_Manager::SELECT,
				'default'    => 'false',
				'options'    => [
					'true'       => __('True', 'my-element'),
					'false'          => __('False', 'my-element'),
				],
			]
		);
		$obj->add_control(
			'dots',
			[
				'label'      => __('Dots', 'my-element'),
				'type'       => Controls_Manager::SELECT,
				'default'    => 'true',
				'options'    => [
					'true'       => __('True', 'my-element'),
					'false'          => __('False', 'my-element'),
				],
				'separator' => 'after',
			]
		);
		$obj->add_responsive_control(
			'margin',
			[
				'label' => __('Space Between Slides', 'elementor'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 30,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
			]
		);

		$obj->add_responsive_control(
			'margin_dots',
			[
				'label' => __('Margin Top', 'elementor'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => 'px',
					'size' => 30,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .owl-dots' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$obj->end_controls_section();

		$obj->start_controls_section(
			'section__f0xx',
			[
				'label' => __('Slider Style', 'healthray'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$obj->add_control(
			'head_dot',
			[
				'label' => __('Owl dot', 'healthray'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$obj->add_control(
			'owldotact_color',
			[
				'label' => __('Active Color', 'healthray'),

				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .owl-carousel .owl-dots .owl-dot.active' => 'background: {{VALUE}};',
				],
			]
		);

		$obj->add_control(
			'owldot_color',
			[
				'label' => __('Inactive Color', 'healthray'),

				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .owl-carousel .owl-dots .owl-dot' => 'background: {{VALUE}};',
				],
			]
		);

		$obj->add_control(
			'owldot_hover',
			[
				'label' => __('Hover Color', 'healthray'),

				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .owl-carousel .owl-dots .owl-dot:hover' => 'background: {{VALUE}};',
				],
			]
		);
		$obj->end_controls_section();
	}
}
