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
		$this->end_controls_section();
	}
	protected function render()
	{
		$settings = $this->get_settings_for_display();
?>
		<div style="display: block;">
			<?php foreach ((array_chunk($settings['gallery'], count($settings['gallery']) / 2)) as $i => $row) { ?>
				<div class="organization-logos item-row row-<?= $i; ?>">
					<?php foreach ($row as $item) { ?>
						<div class="logo-box item">
							<?= wp_get_attachment_image($item['id'], 'full'); ?>
							<p class='image-title'><?= wp_get_attachment_caption($item['id']); ?> </p>
						</div>
					<?php } ?>
				</div>
			<?php } ?>
		</div>
<?php
	}
}

Plugin::instance()->widgets_manager->register(new Ml_Swiper_Widget());