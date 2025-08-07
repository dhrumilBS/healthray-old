<?php

namespace Elementor;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class ZASliderClientReview extends Widget_Base
{
	public function get_name()
	{
		return 'za-slider-client-review';
	}

	public function get_title()
	{
		return __('Slider Client Review', 'my-elements');
	}

	public function get_categories()
	{
		return ['my-element-slider'];
	}

	protected function register_controls()
	{
		$i = 0;
		$this->start_controls_section(
			'section',
			[
				'label' => __('Client Review List', 'my-elements'),
			]
		);

		$doctor_reviews = new \Elementor\Repeater();

		// image
		$doctor_reviews->add_control(
			'client_image',
			[
				'label' => __('Client Image', 'my-elements'),
				'type' => Controls_Manager::MEDIA,
				'default' => ['url' => \Elementor\Utils::get_placeholder_image_src()]
			]
		);
		// name
		$doctor_reviews->add_control(
			'client_name',
			[
				'label' => __('Name', 'my-elements'),
				'type' => Controls_Manager::TEXT,
			],
		);
		// Text
		$doctor_reviews->add_control(
			'client_text',
			[
				'label' => __('Text', 'my-elements'),
				'type' => Controls_Manager::TEXTAREA,
			]
		);
		// Job
		$doctor_reviews->add_control(
			'client_job',
			[
				'label' => __('Job', 'my-elements'),
				'type' => Controls_Manager::TEXT,
			]
		);
		// Hospital Name
		$doctor_reviews->add_control(
			'client_hospital_name',
			[
				'label' => __('Hospital Name', 'my-elements'),
				'type' => Controls_Manager::TEXT,
			]
		);
		// Hospital Name
		$doctor_reviews->add_control(
			'client_hospital_address',
			[
				'label' => __('Hospital Address', 'my-elements'),
				'type' => Controls_Manager::TEXT,
			]
		);
		$doctor_reviews->add_control(
			'client_rating_value',
			[
				'label' => esc_html__('Rating', 'elementor'),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'step' => 0.5,
			]
		);
		$this->add_control(
			'client_reviews',
			[
				'label' => __('Items', 'my-elements'),
				'type' => Controls_Manager::REPEATER,
				'fields' => $doctor_reviews->get_controls(),
				'title_field' => '{{{ client_name }}}',
				'separator' => 'before',
			]
		);


		$this->add_control(
			'rating_icon',
			[
				'label' => esc_html__('Icon', 'elementor'),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'skin' => 'inline',
				'label_block' => false,
				'skin_settings' => [
					'inline' => [
						'icon' => [
							'icon' => 'eicon-star',
						],
					],
				],
				'default' => [
					'value' => 'eicon-star',
					'library' => 'eicons',
				],
				'separator' => 'before',
				'exclude_inline_options' => ['none'],
			]
		);
		$this->add_responsive_control(
			'icon_size',
			[
				'label' => esc_html__('Size', 'elementor'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'em' => [
						'min' => 0,
						'max' => 10,
						'step' => 0.1,
					],
					'rem' => [
						'min' => 0,
						'max' => 10,
						'step' => 0.1,
					],
				],
				'size_units' => ['px', 'em', 'rem', 'vw', 'custom'],
				'selectors' => [
					'{{WRAPPER}} .testimonial-rating.elementor-widget-rating' => '--e-rating-icon-font-size: {{SIZE}}{{UNIT}}',
				],
			]
		);
		$this->add_responsive_control(
			'icon_gap',
			[
				'label' => esc_html__('Icon Spacing', 'elementor'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'em' => [
						'min' => 0,
						'max' => 10,
						'step' => 0.1,
					],
					'rem' => [
						'min' => 0,
						'max' => 10,
						'step' => 0.1,
					],
				],
				'size_units' => ['px', 'em', 'rem', 'vw', 'custom'],
				'selectors' => [
					'{{WRAPPER}} .testimonial-rating.elementor-widget-rating' => '--e-rating-gap: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .testimonial-rating.elementor-widget-rating' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);
		$this->add_control(
			'icon_color',
			[
				'label' => esc_html__('Color', 'elementor'),
				'type' => Controls_Manager::COLOR,
				'selectors' => ['{{WRAPPER}} .testimonial-rating.elementor-widget-rating' => '--e-rating-icon-marked-color: {{VALUE}}'],
				'separator' => 'before',
			]
		);
		$this->add_control(
			'icon_unmarked_color',
			[
				'label' => esc_html__('Unmarked Color', 'elementor'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .testimonial-rating.elementor-widget-rating' => '--e-rating-icon-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();


		$this->start_controls_section(
			'section_setting',
			['label' => __('Settings', 'my-elements'),]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'border',
				'selector' => '{{WRAPPER}} .img-box img',
			]
		);
		$this->add_control(
			'border_radius',
			[
				'label' => __('Border Radius', 'elementor'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .img-box img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);

		$this->add_responsive_control(
			'column',
			[
				'label' => esc_html__('Column', 'my-elements'),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 7,
						'step' => 1,
					]
				],
				'devices' => ['desktop', 'tablet', 'mobile'],
				'desktop_default' => [
					'size' => 3,
				],
				'tablet_default' => [
					'size' => 2,
				],
				'mobile_default' => [
					'size' => 1,
				],
				'selectors' => [
					'{{WRAPPER}} .testimonial' => '--column: {{SIZE}};',
				],
			]
		);
		$this->add_responsive_control(
			'column_gap',
			[
				'label' => esc_html__('Column gap', 'my-elements'),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 12,
						'max' => 72,
						'step' => 4,
					]
				],
				'devices' => ['desktop', 'tablet', 'mobile'],
				'desktop_default' => [
					'size' => 24,
					'unit' => 'px',
				],
				'tablet_default' => [
					'size' => 16,
					'unit' => 'px',
				],
				'mobile_default' => [
					'size' => 12,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .testimonial' => '--gap: {{SIZE}}{{UNIT}}',
				],
			]
		);


		// Slide to show
		$item_to_show = range(1, 10);
		$item_to_show = array_combine($item_to_show, $item_to_show);
		$this->add_responsive_control(
			'item_to_show',
			[
				'label' => esc_html__('Item to Show', 'elementor'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => esc_html__('Default', 'elementor'),
				] + $item_to_show,
				'frontend_available' => true,
				'render_type' => 'template',
				'content_classes' => 'elementor-control-field-select-small',
			]
		);
		$this->end_controls_section();


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
				'label' => __('Doctor name Typography', 'elementor'),
				'name' => 'doctor_name_typography',
				'selector' => '{{WRAPPER}} .name',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __('Doctor Details Typography', 'elementor'),
				'name' => 'doctor_details_typography',
				'selector' => '{{WRAPPER}} .doctor-details',
			]
		);
		$this->end_controls_section();


		$btn = new ML_Slider_Controls();
		$btn->get_slider_btn_controls($this);
	}

	protected function get_rating_value($rating_value): float
	{
		$initial_value = 5;
		// $rating_value = $this->get_settings_for_display('rating_value');

		if ('' === $rating_value) {
			$rating_value = $initial_value;
		}

		$rating_value = floatval($rating_value);

		return round($rating_value, 2);
	}



	protected function get_icon_marked_width($icon_index, $a): string
	{
		$rating_value = $this->get_rating_value($a);
		$width = '0%';
		if ($rating_value >= $icon_index) {
			$width = '100%';
		} elseif (intval(ceil($rating_value)) === $icon_index) {
			$width = ($rating_value - ($icon_index - 1)) * 100 . '%';
		}
		return $width;
	}

	protected function get_icon_markup($a): string
	{
		$icon = $this->get_settings_for_display('rating_icon');
		ob_start();
		for ($index = 1; $index <= 5; $index++) {
			$this->add_render_attribute('icon_marked_' . $index, [
				'class' => 'e-icon-wrapper e-icon-marked',
			]);

			$icon_marked_width = $this->get_icon_marked_width($index, $a);
			if ('100%' !== $icon_marked_width) {
				$this->add_render_attribute('icon_marked_' . $index, [
					'style' => '--e-rating-icon-marked-width: ' . $icon_marked_width . ';',
				]);
			} ?>
			<div class="e-icon">
				<div <?php $this->print_render_attribute_string('icon_marked_' . $index); ?>>
					<?php echo Icons_Manager::try_get_icon_html($icon, ['aria-hidden' => 'true']); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped  
					?>
				</div>
				<div class="e-icon-wrapper e-icon-unmarked">
					<?php echo Icons_Manager::try_get_icon_html($icon, ['aria-hidden' => 'true']); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
					?>
				</div>
			</div>
		<?php }
		return ob_get_clean();
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

		$tabs = $this->get_settings_for_display('client_reviews');
		?>
		<style>
			/* ----------------------------- Doctor review ----------------------------- */
			.testimonial {
				display: flex;
				flex-wrap: wrap;
				justify-content: center;
				margin: 0 calc(var(--gap) / -2);
				--column: 3;
				--gap: 24px;
				--mb: 60px;
				gap: var(--gap);
			}

			.testimonial>.testimonial-box {
				width: calc((100% - (var(--gap) * (var(--column) - 1))) / var(--column));
					padding: 0 calc(var(--gap) / 2);
			    
			}
			.testimonial-box{
				display: flex;
				flex-direction: column;
				border-radius: 12px;
				position: relative;
				background-color: #fff;
				padding: 12px;
				border: 1px solid var(--hr-primary-color);
			}

			.testimonial-box .img-box {
				width: 100px;
				height: 100px;
				position: relative;
			}

			.testimonial-box .img-box div {
				height: 100%;
			}

			.testimonial-box .img-block img {
				display: inline-flex;
				width: 100%;
				height: 100%;
				transition: all 150ms linear;
				object-fit: cover;
			}

			.testimonial-box .text-block {
				flex-grow: 1;
			}

			.testimonial-box .text-block p {
				font-style: italic;
				line-height: 1.5;
				margin: 0 0 8px;
				color: #161c26;
			}

			.doctor-details {
				display: flex;
				gap: 12px;
				align-items: center;
				flex-shrink: 0;
			}

			.name {
				display: block;
				line-height: 1.5;
				margin: 0px;
				font-size: 0.875rem;
				color: #161C26;
			}

			.hospital-details span {
				font-weight: 400;
				font-size: 14px;
				line-height: 1.5;
				margin: 0px;
				color: #516173;
				display: block;
			}

			@media only screen and (max-width: 768px) {
				.testimonial-box .text-block {
					max-width: none;
					min-height: 145px;
				}
			}

			@media only screen and (max-width: 540px) {
				.testimonial-box .text-block {
					max-width: none;
					min-height: auto;
				}
			}

			/*! elementor - v3.20.0 - 26-03-2024 */
			.testimonial-box .testimonial-rating.elementor-widget-rating {
				--e-rating-gap: 4px;
				--e-rating-icon-font-size: 16px;
				--e-rating-icon-color: #ccd6df;
				--e-rating-icon-marked-color: #f0ad4e;
				--e-rating-icon-marked-width: 100%;
				--e-rating-justify-content: center;
				margin-bottom: 8px;
			}

			.testimonial-box .testimonial-rating.elementor-widget-rating .e-rating {
				display: flex;
			}

			.testimonial-box .testimonial-rating.elementor-widget-rating .e-rating-wrapper {
				display: flex;
				justify-content: inherit;
				flex-direction: row;
				flex-wrap: wrap;
				width: -moz-fit-content;
				width: fit-content;
				margin-block-end: calc(0px - var(--e-rating-gap));
				margin-inline-end: calc(0px - var(--e-rating-gap))
			}

			.testimonial-box .testimonial-rating.elementor-widget-rating .e-rating .e-icon {
				position: relative;
				margin-block-end: var(--e-rating-gap);
				margin-inline-end: var(--e-rating-gap)
			}

			.testimonial-box .testimonial-rating.elementor-widget-rating .e-rating .e-icon-wrapper.e-icon-marked {
				--e-rating-icon-color: var(--e-rating-icon-marked-color);
				width: var(--e-rating-icon-marked-width);
				position: absolute;
				z-index: 1;
				height: 100%;
				left: 0;
				top: 0;
				overflow: hidden
			}

			.testimonial-box .testimonial-rating.elementor-widget-rating .e-rating .e-icon-wrapper i {
				font-size: var(--e-rating-icon-font-size);
				color: var(--e-rating-icon-color);
			}

			.testimonial-box .testimonial-rating.elementor-widget-rating .e-rating .e-icon-wrapper svg {
				width: auto;
				height: var(--e-rating-icon-font-size);
				fill: var(--e-rating-icon-color)
			}
		</style>

		<div class="testimonial owl-carousel owl-theme" <?= $this->print_render_attribute_string('slider'); ?>>
			<?php
			foreach ($tabs as $op => $item) :
				if ($op < 6) {
					$this->add_render_attribute('widget', ['class' => 'e-rating']);
					$this->add_render_attribute('widget_wrapper', ['class' => 'e-rating-wrapper',]);
			?>
					<div class="testimonial-box">
						<div class="testimonial-rating elementor-widget-rating">
							<!-- icon -->
							<div <?php $this->print_render_attribute_string('widget'); ?>>
								<div <?php $this->print_render_attribute_string('widget_wrapper'); ?>>
									<?php echo $this->get_icon_markup($item['client_rating_value']); ?>
								</div>
							</div>
						</div>
						<div class="doctor-details">
							<div class="img-box">
								<?php
								if (!empty($item['client_image']['id'])) echo wp_get_attachment_image($item['client_image']['id'], [100, 100], false, array('class' => 'picture'));
								else echo "<img src='" . \Elementor\Utils::get_placeholder_image_src() . "'>";
								?>
							</div>
							<div class="hospital-details">
								<?php if (!empty($item['client_name'])) {  ?><h3 class="name"><?= esc_html($item['client_name']); ?></h3><?php } ?>
								<?php if (!empty($item['client_job'])) { ?><span><?= esc_html($item['client_job']); ?></span><?php } ?>
								<?php if (!empty($item['client_hospital_name'])) {  ?><span><strong> <?= esc_html($item['client_hospital_name']); ?> </strong></span><?php } ?>
							</div>
						</div>
						<!-- Quote -->
						<div class="text-block">
							<p class="text-block"><?= !empty($item['client_text']) ? esc_html($item['client_text']) : 'Default'; ?></p>
						</div>
					</div>

			<?php
				}
			endforeach;
			?>
		</div>
<?php
	}
}
Plugin::instance()->widgets_manager->register(new ZASliderClientReview());