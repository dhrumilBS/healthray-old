<section class="blog-hero hero-section">
	<div class="container">
		<div class="heading text-center">
			<h1>Healthray Events Showcase</h1>
			<p>Discover our healthcare innovation events, webinars, and conferences.</p>
		</div>
	</div>
</section>

<?php $class = (!have_posts()) ? "no-results-section" : ''; ?>
<section class="inner-container blog-container sec-padded <?= $class; ?>">
	<?php if (!have_posts()) { ?>
		<div class="text-center">
			<?php get_template_part('template-parts/section', '404'); ?>
		</div>
	<?php } else { ?>
		<div class="container">

			<div style="max-width: 500px; margin: 0 auto 24px;">
				<?php get_search_form(); ?>
			</div>

			<section class="events-container">

				<div class="event-filters-group">
					<div class="event-filters">
						<button class="filter-btn active" data-filter="all">All Events</button>
						<button class="filter-btn" data-filter="status-upcoming">Upcoming</button>
						<button class="filter-btn" data-filter="status-ongoing">Ongoing</button>
						<button class="filter-btn" data-filter="status-past">Past</button>
						<button class="filter-btn" data-filter="status-archived">Archived</button>
					</div>
				</div>

				<div class="events-grid parallax-container">
					<?php
					while (have_posts()) {
						the_post();

						$start_date = get_field('event_start_date');
						$end_date   = get_field('event_end_date');

						$today = strtotime(date('Y-m-d'));
						$date  = '';
						$status = 'archived';

						if ($start_date) {
							$start_time = strtotime($start_date);
							$end_time   = $end_date ? strtotime($end_date) : $start_time;

							$start_day   = date_i18n('j', $start_time);
							$start_month = date_i18n('F', $start_time);
							$start_year  = date_i18n('Y', $start_time);

							if ($end_date && $end_date !== $start_date) {
								$end_day   = date_i18n('j', $end_time);
								$end_month = date_i18n('F', $end_time);
								$end_year  = date_i18n('Y', $end_time);

								if ($start_month === $end_month && $start_year === $end_year) {
									$date = "{$start_month} {$start_day}–{$end_day}, {$start_year}";
								} elseif ($start_year === $end_year) {
									$date = "{$start_month} {$start_day} – {$end_month} {$end_day}, {$start_year}";
								} else {
									$date = "{$start_month} {$start_day}, {$start_year} – {$end_month} {$end_day}, {$end_year}";
								}
							} else {
								$date = "{$start_month} {$start_day}, {$start_year}";
							}

							if ($today < $start_time) {
								$status = 'upcoming';
							} elseif ($today >= $start_time && $today <= $end_time) {
								$status = 'ongoing';
							} elseif (($today - $end_time) <= (60 * 24 * 60 * 60)) {
								$status = 'past';
							}
						}

						$event = [
							'title'        => get_the_title(),
							'type'         => get_field('event_type'),
							'description'  => get_the_content('', true),
							'date'         => $date,
							'location'     => get_field('event_location'),
							'image'        => get_the_post_thumbnail('', 'full'),
							'status'       => ucfirst($status),
							'status_class' => 'status-' . $status,
						];
					?>

						<div <?= post_class("event-card {$event['status_class']}"); ?>>
							<div class="event-image">
								<?= $event['image']; ?>
							</div>

							<div class="event-content">
								<div class="event-tags">
									<span class="event-tag event-type"><?= esc_html($event['type']); ?></span>
									<span class="event-tag event-status <?= esc_attr($event['status_class']); ?>">
										<?= esc_html($event['status']); ?>
									</span>
									<?php show_admin_edit_button(); ?>
								</div>

								<h3 class="event-title"><?= esc_html($event['title']); ?></h3>

								<div class="event-description">
									<?= wp_kses_post($event['description']); ?>
								</div>

								<div class="event-meta">
									<div class="meta-name">
										<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#1b3c74" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar-days-icon lucide-calendar-days">
											<path d="M8 2v4" />
											<path d="M16 2v4" />
											<rect width="18" height="18" x="3" y="4" rx="2" />
											<path d="M3 10h18" />
											<path d="M8 14h.01" />
											<path d="M12 14h.01" />
											<path d="M16 14h.01" />
											<path d="M8 18h.01" />
											<path d="M12 18h.01" />
											<path d="M16 18h.01" />
										</svg>
										<?= esc_html($event['date']); ?>
									</div>

									<div class="meta-name">
										<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#1b3c74" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin-icon lucide-map-pin">
											<path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0" />
											<circle cx="12" cy="10" r="3" />
										</svg>
										<?= esc_html($event['location']); ?>
									</div>
								</div>
							</div>
						</div>

					<?php } ?>
				</div>

				<?= pagination_bar(); ?>
				<?php wp_reset_postdata(); ?>

			</section>

			<script>
				document.addEventListener("DOMContentLoaded", function() {
					const filterBtns = document.querySelectorAll('.filter-btn');
					const eventCards = document.querySelectorAll('.event-card');

					const noEventMsg = document.createElement('p');
					noEventMsg.textContent = 'No events found.';
					noEventMsg.className = 'no-events-message';
					noEventMsg.style.display = 'none';
					noEventMsg.style.textAlign = 'center';
					noEventMsg.style.padding = '20px';
					noEventMsg.style.fontWeight = '500';
					noEventMsg.style.color = '#555';

					const container = document.querySelector('.events-container');
					container.appendChild(noEventMsg);

					filterBtns.forEach(btn => {
						btn.addEventListener('click', () => {
							filterBtns.forEach(b => b.classList.remove('active'));
							btn.classList.add('active');

							const filter = btn.dataset.filter;
							let visibleCount = 0;

							eventCards.forEach(card => {
								if (filter === 'all' || card.classList.contains(filter)) {
									card.style.display = 'block';
									visibleCount++;
								} else {
									card.style.display = 'none';
								}
							});

							noEventMsg.style.display = visibleCount === 0 ? 'block' : 'none';
						});
					});
				});
			</script>

		</div>
	<?php } ?>
</section>