<?php

namespace Elementor;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class ML_Product_Slider extends Widget_Base
{
	public function get_name()
	{
		return 'ml-product-slider';
	}
	public function get_title()
	{
		return __('Product Slider 1', 'my-elements');
	}
	public function get_categories()
	{
		return ['my-element-slider'];
	}
	public function get_style_depends()
	{
		return ['ml-product-slider', 'owl.carousal'];
	}
	public function get_script_depends()
	{
		return ['owl.carousal'];
	}
	protected function register_controls()
	{
		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__('Content', 'list-widget'),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);
		$reapeter = new \Elementor\Repeater();

		$reapeter->add_control(
			'image',
			[
				'label' => __('Choose Image', 'healthray'),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);
		$reapeter->add_control(
			'title_text',
			[
				'label' => __('Title', 'healthray'),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => __('This is sample title', 'healthray'),
				'placeholder' => __('Enter your title', 'healthray'),
				'label_block' => true,
			]
		);
		$reapeter->add_control(
			'description_text',
			[
				'label' => __('Text', 'healthray'),
				'type' => Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
				'default' => __('Enter your Description here', 'healthray'),
				'placeholder' => __('Enter your description', 'healthray'),
				'show_label' => true,
			]
		);
		$reapeter->add_control(
			'description_content',
			[
				'label' => __('Content', 'healthray'),
				'type' => Controls_Manager::WYSIWYG,
				'dynamic' => [
					'active' => true,
				],
				'default' => __('Enter your Description here', 'healthray'),
				'placeholder' => __('Enter your description', 'healthray'),
				'separator' => 'before',

				'show_label' => true,
			]
		);
		// reapeter
		$this->add_control(
			'reapeter',
			[
				'label' => __('Items', 'my-elements'),
				'type' => Controls_Manager::REPEATER,
				'fields' => $reapeter->get_controls(),
				'title_field' => '{{title_text}}'
			]
		);
		$this->end_controls_section();

		$btn = new ML_Slider_Controls();
		$btn->get_slider_btn_controls($this);

		$this->start_controls_section(
			'section__2p0vfh01x',
			[
				'label' => __('Style', 'healthray'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'label' => __('Title Typography', 'healthray'),
				'selector' => '{{WRAPPER}} .title_text',
			]
		);
		$this->add_control(
			'title_color',
			[
				'label' => esc_html__('Title Color', 'textdomain'),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .title_text' => 'color: {{VALUE}}',
				],
				'saperator' => 'after'
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'text_typography',
				'label' => __('Text Typography', 'healthray'),
				'selector' => '{{WRAPPER}} .text_style',
				'saperator' => 'after'
			]
		);
		$this->add_control(
			'content_color',
			[
				'label' => esc_html__('Content Color', 'textdomain'),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .text_style' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'content_typography',
				'label' => __('Content Typography', 'healthray'),
				'selector' => '{{WRAPPER}} .content_style',
				'saperator' => 'after'
			]
		);
		$this->add_control(
			'text_color',
			[
				'label' => esc_html__('Text Color', 'textdomain'),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .content_style' => 'color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_section();


		$this->start_controls_section(
			'section__2p0vfh01x',
			[
				'label' => __('Box Style', 'healthray'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_responsive_control(
			'box_radius',
			[
				'label' => __('Border Radius', 'healthray'),
				'type' => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .slide' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'border',
				'selector' => '{{WRAPPER}} .slide',
			]
		);

		$this->add_responsive_control(
			'box_padding',
			[
				'label' => __('Padding', 'healthray'),
				'type' => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .slide-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);

		$this->end_controls_section();
	}
	protected function render()
	{
		$settings = $this->get_settings();
		$tabs = $this->get_settings_for_display('reapeter');

		$this->add_render_attribute('slider', [
			'class'			=> "owl-carousel owl-theme",
			'data-dots'		=>	$settings['dots'],
			'data-nav'		=>	$settings['nav_arrow'],
			'data-desk_num'	=>	$settings['desk_num'],
			'data-lap_num'	=>	$settings['lap_num'],
			'data-tab_num'	=>	$settings['tab_num'],
			'data-mob_num'	=>	$settings['mob_num'],
			'data-mob_sm'	=>	$settings['mob_num'],
			'data-autoplay'	=>	$settings['autoplay'],
			'data-loop'		=>	$settings['loop'],
			'data-margin'	=>	$settings['margin']['size'],
		]);
?>

		<div class="slider">
			<div <?= $this->get_render_attribute_string('slider'); ?>>
				<?php foreach ($tabs as $index => $item) { ?>
					<div class="slide">
						<div class="slide-img">
							<?php if (!empty($item['image']['id'])) {
								echo wp_get_attachment_image($item['image']['id'], ['300', 'auto'], "", []);
							} else { ?>
								<img src="<?= $item['image']['url']; ?>">
							<?php } ?>
						</div>
						<?php if (!empty($item['title_text']) || !empty($item['description_content']) || !empty($item['description_text'])) { ?>
							<div class="slide-content">
								<?php if (!empty($item['title_text'])) { ?><h3 class="title_text"><?= esc_html($item['title_text']); ?></h3> <?php  } ?>
								<?php if (!empty($item['description_text'])) {
									echo "<p class='text_style'>" . $this->parse_text_editor($item['description_text']) . "</p>";
								}
								if (!empty($item['description_content'])) {
									echo  "<div class='content_style'>" . $this->parse_text_editor($item['description_content']) . "</div>";
								}
								?>
							</div>
						<?php } ?>
					</div>

				<?php } ?>
			</div>
		</div>
<?php
	}
}

Plugin::instance()->widgets_manager->register(new ML_Product_Slider());
