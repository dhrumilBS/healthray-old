<?php

namespace Elementor;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Ml_Slider_Logo extends Widget_Base
{
	public function get_name()
	{
		return 'slider_logo';
	}
	public function get_title()
	{
		return __('Slider Logo', 'my-elements');
	}
	public function get_icon()
	{
		return 'eicon-slides';
	}
	public function get_categories()
	{
		return ['my-element'];
	}
	public function get_style_depends()
	{
		return ['ml-slider-logo', 'owl.carousal'];
	}
	public function get_script_depends()
	{
		return ['owl.carousal'];
	}
	protected function register_controls()
	{

		$this->start_controls_section(
			'section_slideryguy',
			[
				'label' => __('Image Repeater', 'healthray'),
			]
		);
		$repeater = new Repeater();
		$repeater->add_control(
			'show_flag',
			[
				'label' => "Show Flag",
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('Show', 'textdomain'),
				'label_off' => esc_html__('Hide', 'textdomain'),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);
		$repeater->add_control(
			'flag_image',
			[
				'label' => __('Flag', 'healthray'),
				'type' => Controls_Manager::MEDIA,
				'condition' => [
					'show_flag' => 'yes',
				],
			]
		);
		$repeater->add_control(
			'caption',
			[
				'label' => esc_html__('Caption', 'textdomain'),
				'type' => \Elementor\Controls_Manager::TEXT,
			]
		);
		$repeater->add_control(
			'image',
			[
				'label' => __('Logo Image', 'healthray'),
				'type' => Controls_Manager::MEDIA,
			]
		);
		$this->add_control(
			'gallery',
			[
				'label' => __('Image Repeater', 'healthray'),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'title_field' => ' {{ caption  }}',
			]
		);
		$this->end_controls_section();
		
		// Item Style
		$this->start_controls_section(
			'section_dsj8fysdb_colors',
			[
				'label' => __('Item Style', 'my-elements'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
		'padding',
		[
			'label' => esc_html__( 'Padding ', 'textdomain' ),
			'type' => \Elementor\Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
			'default' => [
				'top' => 12,
				'right' => 12,
				'bottom' => 12,
				'left' => 12,
				'unit' => 'px',
				'isLinked' => false,
			],
			'selectors' => [
				'{{WRAPPER}} .item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		]
	);
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'border',
				'selector' => '{{WRAPPER}} .item',
			]
		);
		$this->add_control(
			'borer_radius',
			[
				'label' => esc_html__( 'Border Radius', 'textdomain' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'default' => [
					'top' => 12,
					'right' => 12,
					'bottom' => 12,
					'left' => 12,
					'unit' => 'px',
					'isLinked' => false,
				],
				'selectors' => [
					'{{WRAPPER}} .item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_section();
		
		// Caption Style
		$this->start_controls_section(
			'section_style_colors',
			[
				'label' => __('Content', 'my-elements'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __('Caption Typography', 'elementor'),
				'name' => 'doctor_name_typography',
				'selector' => '{{WRAPPER}} .image-title',
			]
		);
		$this->add_control(
			'Caption_color',
			[
				'label' => esc_html__( 'Caption Color', 'textdomain' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .image-title' => 'color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_section();
		
		$btn = new ML_Slider_Controls();
		$btn->get_slider_btn_controls($this);
		
	
		
	}
	protected function render()
	{
		$settings = $this->get_settings_for_display();
		$this->add_render_attribute('slider', [
			'class' => 'slider-client-logo owl-carousel owl-theme',
			'style' => "--slider-margin: {$settings['margin']['size']}px; --mob-columns: {$settings['mob_num']}; --lap-columns: {$settings['lap_num']}; --desk-columns: {$settings['desk_num']};",
			'data-dots' => $settings['dots'],
			'data-nav' => $settings['nav_arrow'],
			'data-desk_num' => $settings['desk_num'],
			'data-lap_num' => $settings['lap_num'],
			'data-tab_num' => $settings['tab_num'],
			'data-mob_num' => $settings['mob_num'],
			'data-mob_sm' => $settings['mob_num'],
			'data-autoplay' => $settings['autoplay'],
			'data-loop' => $settings['loop'],
			'data-margin' => $settings['margin']['size'],
		]);
?>
		<div <?= $this->get_render_attribute_string('slider'); ?>>
			<?php foreach ($settings['gallery'] as $i => $item) : ?>

				<?php if (isset($item['image']['id']) || !empty($item['image'])) : ?>
					<div class="item">
						<div class="img-slide">
							<div class="logo-wrap">
								<?php if ('yes' === $item['show_flag']) { ?>
									<span class="flage-img"> <?= wp_get_attachment_image($item['flag_image']['id'], 'full'); ?> </span>
								<?php } ?>
								<?= wp_get_attachment_image($item['image']['id'],  '', true, ['class' => "logo-img"]); ?>
							</div>
							<p class="image-title"> <?= wp_kses_post($item['caption']); ?> </p>
						</div>
					</div>
				<?php endif; ?>

			<?php endforeach; ?>
		</div>
<?php
	}
	
	protected function _content_template() {
    ?>
    <# 
    var margin = settings.margin ? settings.margin.size : 0; 
    #>

    <div class="slider-client-logo owl-carousel owl-theme" 
        style="--slider-margin: {{ margin }}px; --mob-columns: {{ settings.mob_num }}; --lap-columns: {{ settings.lap_num }}; --desk-columns: {{ settings.desk_num }};"
        data-dots="{{ settings.dots }}"
        data-nav="{{ settings.nav_arrow }}"
        data-desk_num="{{ settings.desk_num }}"
        data-lap_num="{{ settings.lap_num }}"
        data-tab_num="{{ settings.tab_num }}"
        data-mob_num="{{ settings.mob_num }}"
        data-mob_sm="{{ settings.mob_num }}"
        data-autoplay="{{ settings.autoplay }}"
        data-loop="{{ settings.loop }}"
        data-margin="{{ margin }}"
    >
        <# _.each( settings.gallery, function( item, i ) { #>
            <# if ( item.image && item.image.url ) { #>
                <div class="item">
                    <div class="img-slide">
                        <div class="logo-wrap">
                            <# if ( item.show_flag === 'yes' && item.flag_image && item.flag_image.url ) { #>
                                <span class="flage-img"><img src="{{ item.flag_image.url }}" alt=""></span>
                            <# } #>
                            <img class="logo-img" src="{{ item.image.url }}" alt="">
                        </div>
                        <p class="image-title">{{{ item.caption }}}</p>
                    </div>
                </div>
            <# } #>
        <# }); #>
    </div>
    <?php
}

}
Plugin::instance()->widgets_manager->register(new Ml_Slider_Logo());