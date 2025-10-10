<?PHP

namespace Elementor;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Ml_Swiper_Widget extends Widget_Base
{
	public function get_name()
	{
		return 'swiper_widget';
	}
	public function get_title()
	{
		return __('Swiper Slider', 'custom-elementor-widgets');
	}
	public function get_categories()
	{
		return ['my-element'];
	}
	public function get_style_depends()
	{
		return ['owl.carousal'];
	}
	public function get_script_depends()
	{
		return ['owl.carousal'];
	}

	protected function register_controls()
	{
		$this->start_controls_section(
			'content_section',
			[
				'label' => __('Content', 'custom-elementor-widgets'),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'gallery',
			[
				'label' => esc_html__('Add Images', 'textdomain'),
				'type' => \Elementor\Controls_Manager::GALLERY,
				'show_label' => false,
				'default' => [],
			]
		);
		$this->add_control('per_slider_item', [
			'label' => __('Per Slider Item', 'my-element'),
			'type' => Controls_Manager::SELECT,
			'default' => '2',
			'options' => [
				'1' => __('1', 'my-element'),
				'2' => __('2', 'my-element')
			],
		]);
		$this->add_control('caption', [
			'label' => __('caption', 'my-element'),
			'type' => Controls_Manager::SWITCHER,
			'label_on' => __('Show', 'my-element'),
			'label_off' => __('Hide', 'my-element'),
			'default' => 'yes',
			'return_value' => 'yes',
			'separator' => 'before',
		]);
		$this->end_controls_section();

		$btn = new ML_Slider_Controls();
		$btn->get_slider_btn_controls($this);
	}
	protected function render()
	{
		$settings = $this->get_settings_for_display();
		$this->add_render_attribute('slider', 'data-dots', $settings['dots']);
		$this->add_render_attribute('slider', 'data-nav', $settings['nav_arrow']);
		$this->add_render_attribute('slider', 'data-desk_num', $settings['desk_num']);
		$this->add_render_attribute('slider', 'data-lap_num', $settings['lap_num']);
		$this->add_render_attribute('slider', 'data-tab_num', $settings['tab_num']);
		$this->add_render_attribute('slider', 'data-mob_num', $settings['mob_num']);
		$this->add_render_attribute('slider', 'data-mob_sm', $settings['mob_num']);
		$this->add_render_attribute('slider', 'data-autoplay', $settings['autoplay']);
		$this->add_render_attribute('slider', 'data-loop', $settings['loop']);
		$this->add_render_attribute('slider', 'data-margin', $settings['margin']['size']);
?>
		<style id="swiper_widget-css">
			.elementor-widget-swiper_widget .global-client-logo-slider {
				display: flex;
				overflow: hidden;
			}

			.elementor-widget-swiper_widget .global-client-logo-slider .item {
				width: <?= 100 / $settings['mob_num']; ?>%;
				flex-shrink: 0;
				margin-right: <?= $settings['margin']['size']; ?>
			}

			.elementor-widget-swiper_widget .global-client-logo-slider .owl-item .item {
				width: auto;
				margin: 0
			}

			.elementor-widget-swiper_widget .global-client-logo-slider.owl-loaded {
				flex-direction: column;
			}

			.elementor-widget-swiper_widget .global-client-logo-slider img {
				object-fit: contain;
				height: 150px;
				max-width: 170px;
				margin: 0 auto;
			}

			@media screen and (min-width: 767px) {
				.elementor-widget-swiper_widget .global-client-logo-slider .item {
					width: <?= 100 / $settings['lap_num']; ?>%;
				}
			}

			@media screen and (min-width: 991px) {
				.elementor-widget-swiper_widget .global-client-logo-slider .item {
					width: <?= 100 / $settings['desk_num']; ?>%;
				}
			}
		</style>
		<div class="global-client-logo-slider owl-carousel owl-theme" <?= $this->get_render_attribute_string('slider'); ?>>
			<?php
			$html = '';
			foreach ($settings['gallery'] as $i => $image) {

				$html .= '<div class="img-slide">';
				$html .=  wp_get_attachment_image($image['id'], 'full');

				if ($settings['caption'] == 'yes') {
					$caption = wp_get_attachment_caption($image['id']) ? wp_get_attachment_caption($image['id']) : '<code>Enter Caption</code>';
					$html .= "<p class='image-title'>" .  $caption . "</p>";
				}
				$html .= "</div>";


				if ($settings['per_slider_item'] == 2) {
					if ($i % 2 == 1) {
						$res =  "<div class='item'>" . $html . "</div>";
						echo  $res;
						$html = '';
					}
				} else {
					$res = "<div class='item'>" . $html . "</div>";
					echo  $res;
					$html = '';
				}
			}
			?>
		</div>
        <div style="display: none;">
			<?php $rows = array_chunk($settings['gallery'], ceil(count($settings['gallery']) / 2)); ?>
			<?php foreach ($rows as $i => $row) { ?>
				<div class="item-row row-<?= $i; ?>">
					<?php foreach ($row as $item) { ?>
						<div class="item">
							<div class="img-slide">
								<?= wp_get_attachment_image($item['id'], 'full'); ?>
								<p class='image-title'><?= wp_get_attachment_caption($item['id']); ?></p>
							</div>
						</div>
					<?php } ?>
				</div>
			<?php } ?>
		</div>
        
<?php
	}
}

Plugin::instance()->widgets_manager->register(new Ml_Swiper_Widget());