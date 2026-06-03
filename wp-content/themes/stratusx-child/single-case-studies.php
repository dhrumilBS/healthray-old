<main class="min-h-screen bg-background">
	<section class="sec-padded case-hero text-center border-bottom ">
		<div class="container">
			<div class="heading">
				<h1> <?= the_title(); ?> </h1>
				<div class="lead"> <?= the_content(); ?> </div>
			</div>
			<?php $heroLists = get_field('hero_list'); ?>
			<ul class="hero__list">
				<?php
				// print_r($heroLists);	
				foreach ($heroLists as $list) {
					echo "<li>{$list['text']}</li>";
				}
				?>
			</ul>
		</div>
	</section>

	<section class="sec-padded case-hero-image-section border-bottom">
		<div class="container">
			<div class="image-wrapper">
				<?= get_the_post_thumbnail(get_the_ID(), 'full'); ?>
			</div>
		</div>
	</section>

	<section class="sec-padded case-metrics border-bottom">
		<div class="container">
			<div class="heading">
				<h2 class="text-center"><?= get_field('metrics_title'); ?></h2>
				<p class="text-center"><?= get_field('metrics_text'); ?></p>
			</div>
			<?php if (have_rows('metrics')) { ?>
				<div class="grid-3 gap-lg">
					<?php while (have_rows('metrics')) {
						the_row(); ?>
						<div class="card card-metric">
							<div class="icon-wrapper">
								<?= get_sub_field('icon'); ?>
							</div>
							<div class="metric-value"><?= get_sub_field('title'); ?></div>
							<div class="metric-label"><?= get_sub_field('text'); ?> </div>
						</div>
					<?php } ?>
				</div>
			<?php } ?>
		</div>
	</section>

	<?php if (get_field('enable_intro_section')) : ?>

		<section class="sec-padded case-intro border-bottom">
			<div class="container narrow">

				<?php if ($title = get_field('intro_title')) : ?>
					<div class="text-center heading mb-4">
						<h2 class="case-title"><?= esc_html($title); ?></h2>
					</div>
				<?php endif; ?>

				<?php if ($content = get_field('intro_content')) : ?>
					<div class="case-intro__content">
						<?= wp_kses_post($content); ?>
					</div>
				<?php endif; ?>

			</div>
		</section>

	<?php endif; ?>

	<section class="sec-padded case-challenge border-bottom">
		<div class="container">
			<div class="text-center heading mb-5">
				<h2 class="case-title"><?= get_field('challange_title') ?></h2>
				<div class="case-desc"><?= get_field('challange_text') ?> </div>
			</div>
			<div class="row align-items-center flex">
				<div class="col-lg-5">
					<div class="img-wrap"> <?= wp_get_attachment_image(get_field('challange_image'), 'full') ?> </div>
				</div>
				<div class="offset-lg-1 col-lg-6 text-start">
					<?php if (get_field('challanges')) { ?>
						<div class="challenge-list">
							<?php foreach (get_field('challanges') as $i => $challenge) { ?>
								<div class="<?= ($i == 0) ? 'read-more-wrapper is-active' : 'read-more-wrapper'  ?>">
									<div class="challenge-item">
										<div class="read-more-toggle">
											<div class="block-title">
												<h5 class="title"><?= $challenge['title']; ?></h5>
											</div>
										</div>
										<div class="read-more-text">
											<div class="block-content">
												<?= $challenge['text']; ?>
											</div>
										</div>
									</div>
								</div>
							<?php } ?>
						</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</section>

	<section class="sec-padded case-solution border-bottom">
		<div class="container">
			<div class="text-center heading mb-5">
				<h2 class="title"><?= get_field('solution_title') ?></h2>
				<div class="subtitle"><?= get_field('solution_text') ?> </div>
			</div>
			<div class="row align-items-center">
				<?php
				$solutions = get_field('solutions');
				if (!empty($solutions) && is_array($solutions)) :
				?>
					<div class="col-lg-6">
						<div class="solution-list">
							<?php foreach ($solutions as $item) : ?>
								<div class="solution-item">
									<span class="solution-item__icon">
										<svg xmlns="http://www.w3.org/2000/svg" width="22px" height="22px" viewBox="0 0 20 21" fill="none">
											<g clip-path="url(#clip0_1858_12100)">
												<mask id="mask0_1858_12100" style="mask-type:luminance" maskUnits="userSpaceOnUse" x="0" y="0" width="20" height="21">
													<path d="M20 0.297363H0V20.2974H20V0.297363Z" fill="white"></path>
												</mask>
												<g mask="url(#mask0_1858_12100)">
													<path d="M19.5135 9.1173L18.1494 7.7532C17.8259 7.42971 17.5613 6.78951 17.5613 6.33028V4.40064C17.5613 3.48219 16.8125 2.73341 15.894 2.73341H13.9644C13.5052 2.73341 12.865 2.46873 12.5415 2.14524L11.1774 0.781137C10.5304 0.134152 9.46941 0.134152 8.82017 0.781137L7.45607 2.14524C7.13257 2.46873 6.49011 2.73341 6.03315 2.73341H4.10351C3.18732 2.73341 2.43627 3.48219 2.43627 4.40064V6.33028C2.43627 6.78724 2.1716 7.42971 1.8481 7.7532L0.484006 9.1173C-0.165242 9.76428 -0.165242 10.8252 0.484006 11.4745L1.8481 12.8386C2.1716 13.1621 2.43627 13.8045 2.43627 14.2615V16.1911C2.43627 17.1073 3.18732 17.8584 4.10351 17.8584H6.03315C6.49237 17.8584 7.13257 18.123 7.45607 18.4465L8.82017 19.8106C9.46715 20.4576 10.5281 20.4576 11.1774 19.8106L12.5415 18.4465C12.865 18.123 13.5052 17.8584 13.9644 17.8584H15.894C16.8102 17.8584 17.5613 17.1073 17.5613 16.1911V14.2615C17.5613 13.8023 17.8259 13.1621 18.1494 12.8386L19.5135 11.4745C20.1605 10.8275 20.1605 9.76655 19.5135 9.1173Z" fill="#2AA7FF"></path>
													<path d="M8.888 14.0149L5.26172 10.3886L6.84977 8.80057L8.888 10.8388L13.1477 6.5791L14.7358 8.16716L8.888 14.0149Z" fill="white"></path>
												</g>
											</g>
										</svg>
									</span>

									<div class="solution-item__content">

										<h4 class="solution-item__title">
											<?= esc_html($item['title'] ?? ''); ?>
										</h4>

										<?php if (!empty($item['text'])) : ?>
											<div class="solution-item__text">
												<?= wp_kses_post($item['text']); ?>
											</div>
										<?php endif; ?>

										<?php if (!empty($item['sub_points']) && is_array($item['sub_points'])) : ?>
											<div class="solution-item__subpoints">
												<?php foreach ($item['sub_points'] as $sub) : ?>
													<div class="solution-subpoint">
														<?php if (!empty($sub['sub_point_title'])) : ?>
															<div class="solution-subpoint__title">
																<?= esc_html($sub['sub_point_title']); ?>
															</div>
														<?php endif; ?>

														<?php if (!empty($sub['sub_point_text'])) : ?>
															<div class="solution-subpoint__text">
																<?= wp_kses_post($sub['sub_point_text']); ?>
															</div>
														<?php endif; ?>
													</div>
												<?php endforeach; ?>
											</div>
										<?php endif; ?>

									</div>
								</div>
							<?php endforeach; ?>

						</div>
					</div>
				<?php endif; ?>

				<div class="col-lg-6 text-center">
					<?= wp_get_attachment_image(get_field('solution_image'), 'full'); ?>
				</div>

			</div>
		</div>
	</section>

	<section class="sec-padded case-outcome border-bottom">
		<div class="container">

			<div class="heading text-center">
				<h2><?php if (!empty(get_field('kpis_title'))) {
						echo get_field('kpis_title');
					} else {
						echo "The Digital Journey of Universal Hospitals with Healthray";
					} ?></h2>
				<p><?php if (!empty(get_field('kpis_text'))) {
						echo get_field('kpis_text');
					} else {
						echo "Universal Hospitals partnered with Healthray to transform their healthcare operations with AI-powered automation and unified patient management. The results demonstrate how digital innovation elevated efficiency, clinical accuracy, and patient satisfaction across the entire care ecosystem.";
					} ?></p>
			</div>

			<?php if (have_rows('kpis')) { ?>
				<div class="outcome-grid">
					<?php while (have_rows('kpis')) {
						the_row(); ?>
						<div class="outcome-item">
							<div class="outcome-icon">
								<?= get_sub_field('icon'); ?>
							</div>
							<div class="outcome-content">
								<strong><?= get_sub_field('title'); ?></strong>
								<?= get_sub_field('text'); ?>
							</div>
						</div>
					<?php } ?>
				</div>
			<?php } ?>
		</div>
	</section>

	<?php if (get_field('enable_hms_recommendation')) : ?>

		<section class="sec-padded case-hms-recommendation border-bottom">
			<div class="container narrow">

				<div class="heading text-center">
					<h2><?= esc_html(get_field('hms_recommendation_title')); ?></h2>
				</div>

				<div class="case-hms-recommendation__content text-lg mb-3">
					<?= wp_kses_post(get_field('hms_recommendation_content')); ?>
				</div>

				<div class="heading">
					<h3><?= esc_html(get_field('hms_advice_title')); ?></h3>
				</div>

				<div class="case-hms-recommendation__advice">
					<?= wp_kses_post(get_field('hms_advice_content')); ?>
				</div>

			</div>
		</section>

	<?php endif; ?>


	<section class="sec-padded quote-section border-bottom">
		<div class="container narrow">
			<div class="heading">
				<h2 class="text-center">What Our Clients Say</h2>
			</div>
			<div class="quote-box">
				<blockquote class="quote-text"> <?= get_field('quote_text'); ?> </blockquote>
				<div class="quote-author">
					<div class="author-name"><?= get_field('quote_person_name'); ?></div>
					<div class="author-title"><?= get_field('quote_person_title'); ?></div>
				</div>
			</div>
		</div>
	</section>

	<section class="sec-padded case-results border-bottom">
		<div class="container narrow">
			<div class="heading">
				<h2 class="heading-lg"><?= get_field('result_title'); ?></h2>
			</div>
			<div class="text-lg"><?= get_field('result_text'); ?></div>
		</div>
	</section>
</main>

<script>
	document.querySelectorAll(".read-more-wrapper").forEach(function(wrapper) {

		const hoverToggle = wrapper.querySelector(".read-more-toggle");
		const readMoreText = wrapper.querySelector(".read-more-text");
		if (wrapper.classList.contains("is-active") && readMoreText) {
			readMoreText.style.maxHeight = readMoreText.scrollHeight + "px";
		}
		if (hoverToggle) {
			hoverToggle.addEventListener("mouseenter", function() {
				document.querySelectorAll(".read-more-wrapper.is-active").forEach(function(item) {
					item.classList.remove("is-active");
				});

				document.querySelectorAll(".read-more-text").forEach(function(txt) {
					txt.style.maxHeight = "0";
				});

				wrapper.classList.add("is-active");
				if (readMoreText) {
					const dynamicHeight = readMoreText.scrollHeight;
					readMoreText.style.maxHeight = dynamicHeight + "px";
				}
			});
		}
	});
</script>
<?php echo do_shortcode('[elementor-template id="26869"]'); ?>