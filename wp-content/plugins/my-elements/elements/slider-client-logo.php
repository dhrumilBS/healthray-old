<?PHP

namespace Elementor;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Ml_Slider_Client_Logo extends Widget_Base
{
	public function get_name() { return 'slider_client_logo'; }
	public function get_title() { return __('Client Logo Slider', 'my-element'); }
	public function get_categories() { return ['my-element-slider']; }
	public function get_icon() { return 'eicon-slides'; }
	protected function register_controls() { 
		$this->start_controls_section(
			'content_section',[
				'label' => __('Content', 'my-element'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'gallery', [
				'label' => esc_html__('Add Images', 'textdomain'),
				'type' => Controls_Manager::MEDIA,
				'show_label' => false,
				'default' => [],
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
<style id="slider-client-logo-css">
	.elementor-widget-slider-client-logo .global-client-logo-slider { display: flex;overflow: hidden;}
	.elementor-widget-slider-client-logo .global-client-logo-slider .item {width: <?= 100/$settings['mob_num']; ?>%;flex-shrink: 0; margin-right: <?= $settings['margin']['size'];?>}
	.elementor-widget-slider-client-logo .global-client-logo-slider .owl-item .item {width: auto; margin:0}
	.elementor-widget-slider-client-logo .global-client-logo-slider.owl-loaded {flex-direction: column;}
	@media screen and (min-width: 767px) {
		.elementor-widget-slider-client-logo .global-client-logo-slider .item {width:  <?= 100/$settings['lap_num']; ?>%;}
	}
	@media screen and (min-width: 991px) {
		.elementor-widget-slider-client-logo .global-client-logo-slider .item {width: <?=  100/$settings['desk_num']; ?>%;}
	}
</style>
<div class="global-client-logo-slider owl-carousel owl-theme" <?= $this->get_render_attribute_string('slider'); ?>>
	<?php
		$html = '';
		foreach ($settings['gallery'] as $i => $image) {
			$class = ($i % 2) == 0 ? "first" : "last";
			$caption = wp_get_attachment_caption($image['id']) ? wp_get_attachment_caption($image['id']) : '<code>Enter Caption</code>';
			$html .= '<div class="img-slide img-slide-' . $class . '">';
			$html .=  wp_get_attachment_image($image['id'], 'full');
			$html .= "<p class='image-title'>" .  $caption . "</p>";
			$html .= "</div>";

			if ($i % 2 == 1) {
				$res = '<div class="item">'.$html.'</div>';
				echo  $res;
				$html = '';
			}
		} ?>
</div>
<?php
	}
}

Plugin::instance()->widgets_manager->register(new Ml_Slider_Client_Logo());