<?php

namespace Elementor;

if (!defined('ABSPATH')) exit;
class Healthray_tabs extends Widget_Base
{
	public function get_name()
	{
		return __('healthray-tabs', 'my-element');
	}
	public function get_title()
	{
		return __('Healthray Tabs', 'my-element');
	}
	public function get_categories()
	{
		return ['my-element-slider'];
	}

	protected function register_controls()
	{
		$this->start_controls_section(
			'section_slideryguy',
			[
				'label' => __('Healthray tabs', 'my-element'),
			]
		);
	
		$repeater = new Repeater();

		$repeater->add_control(
			'image',
			[
				'label' => __('Choose Image', 'my-element'),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);
		$repeater->add_control(
			'tab_title',
			[
				'label' => __('Tab Title', 'my-element'),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => __('This is sample title', 'my-element'),
				'placeholder' => __('Enter your title', 'my-element'),
				'label_block' => true,
			]
		);
		$repeater->add_control(
			'tab_desc',
			[
				'label' => __('Description', 'my-element'),
				'type' => Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
				'default' => __('This is designation title', 'my-element'),
				'placeholder' => __('Enter your title', 'my-element'),
				'label_block' => true,
			]
		);

			$repeater->add_control(
				'tab_side_title',
				[
					'label' => __('Side Description Title', 'my-element'),
					'type' => Controls_Manager::TEXTAREA,
					'dynamic' => [
						'active' => true,
					],
					'default' => "Heading",
					'placeholder' => __('Enter your title', 'my-element'),
					'label_block' => true,
				]
			);
		$repeater->add_control(
			'tab_side_desc1',
			[
				'label' => __('Side Description Top', 'my-element'),
				'type' => Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => __('Enter your title', 'my-element'),
				'label_block' => true,
			]
		);
		$repeater->add_control(
			'tab_side_desc2',
			[
				'label' => __('Side Description Bottom', 'my-element'),
				'type' => Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => __('Enter your title', 'my-element'),
				'label_block' => true,
			]
		);
		$this->add_control(
			'tabs',
			[
				'label' => __('Tabs Items', 'my-element'),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'title_field' => '{{{ tab_title }}}',
			]
		);
		$this->end_controls_section();





		$this->start_controls_section(
			'section_tab_setting',
			[
				'label' => __('Tab Setting', 'my-element'),
			]
		);
		$this->add_responsive_control(
			'tab_width',
			[
				'label' => esc_html__('Width', 'elementor'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
				],
				'tablet_default' => [
					'unit' => '%',
				],
				'mobile_default' => [
					'unit' => '%',
				],
				'size_units' => ['px', '%', 'em', 'rem', 'vw', 'custom'],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 100,
					],
					'px' => [
						'min' => 1,
						'max' => 1000,
					],
					'vw' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tabs' => '--tab-width: {{SIZE}}{{UNIT}};',
				],
			]
		);


		$this->end_controls_section();





		$this->start_controls_section(
			'section_imgsetting',
			[
				'label' => __('Image Setting', 'my-element'),
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label' => esc_html__('Alignment', 'elementor'),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__('Left', 'elementor'),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__('Center', 'elementor'),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__('Right', 'elementor'),
						'icon' => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'width',
			[
				'label' => esc_html__('Width', 'elementor'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
				],
				'tablet_default' => [
					'unit' => '%',
				],
				'mobile_default' => [
					'unit' => '%',
				],
				'size_units' => ['px', '%', 'em', 'rem', 'vw', 'custom'],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 100,
					],
					'px' => [
						'min' => 1,
						'max' => 1000,
					],
					'vw' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .content img' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'space',
			[
				'label' => esc_html__('Max Width', 'elementor'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
				],
				'tablet_default' => [
					'unit' => '%',
				],
				'mobile_default' => [
					'unit' => '%',
				],
				'size_units' => ['px', '%', 'em', 'rem', 'vw', 'custom'],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 100,
					],
					'px' => [
						'min' => 1,
						'max' => 1000,
					],
					'vw' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .content img' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'height',
			[
				'label' => esc_html__('Height', 'elementor'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'em', 'rem', 'vh', 'custom'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 500,
					],
					'vh' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .content img' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'object-fit',
			[
				'label' => esc_html__('Object Fit', 'elementor'),
				'type' => Controls_Manager::SELECT,
				'condition' => [
					'height[size]!' => '',
				],
				'options' => [
					'' => esc_html__('Default', 'elementor'),
					'fill' => esc_html__('Fill', 'elementor'),
					'cover' => esc_html__('Cover', 'elementor'),
					'contain' => esc_html__('Contain', 'elementor'),
				],
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .content img' => 'object-fit: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'object-position',
			[
				'label' => esc_html__('Object Position', 'elementor'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'center center' => esc_html__('Center Center', 'elementor'),
					'center left' => esc_html__('Center Left', 'elementor'),
					'center right' => esc_html__('Center Right', 'elementor'),
					'top center' => esc_html__('Top Center', 'elementor'),
					'top left' => esc_html__('Top Left', 'elementor'),
					'top right' => esc_html__('Top Right', 'elementor'),
					'bottom center' => esc_html__('Bottom Center', 'elementor'),
					'bottom left' => esc_html__('Bottom Left', 'elementor'),
					'bottom right' => esc_html__('Bottom Right', 'elementor'),
				],
				'default' => 'center center',
				'selectors' => [
					'{{WRAPPER}} .content img' => 'object-position: {{VALUE}};',
				],
				'condition' => [
					'object-fit' => 'cover',
				],
			]
		);

		$this->end_controls_section();
	}
	protected function render()
	{
		$settings = $this->get_settings();
		$id_int = substr($this->get_id_int(), 0, 3);

?>
		<div class="tabs">
			<?php foreach ($settings['tabs'] as $key => $value) {
				if ($key == 0) {
					$class = " active";
				} else {
					$class = "";
				}
			?>
				<div class="tab hr-tab">
					<div class="tab-toggle<?= $class; ?>">
						<?php if (!empty($value['tab_title'])) { ?> <h3 class="tab-heading"> <?= $value['tab_title']; ?> </h3><?php } ?>
						<?php if (!empty($value['tab_desc'])) { ?> <div class="tab-desc description"> <?= $value['tab_desc']; ?> </div><?php } ?>
					</div>
					<?php if (1 == 2) { ?>
						<div class="tab-toggle-icon">
							<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
								<path d="M4.29657 14.291L9.47955 9.1705C9.7933 8.8606 9.97438 8.4288 9.97438 7.99049C9.97438 7.55218 9.7933 7.12038 9.47955 6.81048L4.30333 1.69668C3.92487 1.30955 3.93058 0.673708 4.31567 0.293264C4.70168 -0.0880928 5.34368 -0.0934837 5.73621 0.281069L10.9124 5.39087C11.6084 6.07983 11.9993 7.01344 11.9993 7.98682C11.9993 8.9602 11.6084 9.89381 10.9124 10.5828L5.72945 15.7066C5.28999 16.141 4.52059 16.0736 4.16995 15.5549C3.9094 15.1694 3.96462 14.6185 4.29657 14.291Z" fill="#477BFF" />
							</svg>
						</div>
					<?php } ?>
				</div>

				<div class="content<?= $class; ?>">
					<?php if (!empty($value['tab_side_title'])) { ?> <h3> <?= $value['tab_side_title']; ?> </h3><?php } ?>
					<?php if (!empty($value['tab_side_desc1'])) { ?> <p><?= $value['tab_side_desc1']; ?></p><?php } ?>
					<?php if (!empty($value['image'])) { ?>
						<figure>
							<?= wp_get_attachment_image($value['image']['id'], ['auto', 500]); ?>
						</figure>
					<?php } ?>
					<?php if (!empty($value['tab_side_desc2'])) { ?> <p><?= $value['tab_side_desc2']; ?></p><?php } ?>
				</div>
			<?php } ?>
		</div>
		<?php if (Plugin::$instance->editor->is_edit_mode()) : $this->load_js();
		endif; ?>
	<?php
	}

	private function load_js()
	{
	?>
		<script>
			function openTab(e) {
				jQuery('.tabs').css('min-height', jQuery('.tabs .content.active').outerHeight())
				jQuery('.content.active').outerHeight()
				e.find(".tab-toggle").on('click', function() {
					var content = jQuery(this).parent().next(".content"),
						activeItems = e.find(".active");

					if (!jQuery(this).hasClass('active')) {
						jQuery(this).add(content).add(activeItems).toggleClass('active');
						e.css('min-height', content.outerHeight());
					}
				});

				jQuery(window).on('load', function() {
					e.find(".tab-toggle").first().trigger('click');
				});
			}
			jQuery(".tabs").each(function() {
				openTab(jQuery(this));
			})
		</script>
<?php
	}
}

Plugin::instance()->widgets_manager->register_widget_type(new \Elementor\Healthray_tabs());
?>