<?php

/**
 * Template for displaying single Event posts
 * File: single-event.php
 */
?>

<main id="primary" class="site-main container py-12">

	<?php
	while (have_posts()) :
		the_post();

		// Get custom fields (if using ACF or default)
		$event_start_date	= get_post_meta(get_the_ID(), 'event_start_date', true);
		$event_end_date		= get_post_meta(get_the_ID(), 'event_end_date', true);
		$event_location		= get_post_meta(get_the_ID(), 'event_location', true);
		$event_time			= get_post_meta(get_the_ID(), 'event_time', true);

		if ($event_start_date) {
			$start_day   = date_i18n('j', strtotime($event_start_date));
			$start_month = date_i18n('F', strtotime($event_start_date));
			$start_year  = date_i18n('Y', strtotime($event_start_date));

			if ($event_end_date && $event_end_date != $event_start_date) {
				$end_day   = date_i18n('j', strtotime($event_end_date));
				$end_month = date_i18n('F', strtotime($event_end_date));
				$end_year  = date_i18n('Y', strtotime($event_end_date));

				if ($start_month === $end_month && $start_year === $end_year) {
					echo $start_month . ' ' . $start_day . '–' . $end_day . ', ' . $start_year;
				}
				elseif ($start_year === $end_year) {
					echo $start_month . ' ' . $start_day . ' – ' . $end_month . ' ' . $end_day . ', ' . $start_year;
				}
				else {
					echo $start_month . ' ' . $start_day . ', ' . $start_year . ' – ' . $end_month . ' ' . $end_day . ', ' . $end_year;
				}
			} else {
				echo $start_month . ' ' . $start_day . ', ' . $start_year;
			}
		}
	?>
		<article id="post-<?php the_ID(); ?>" <?php post_class("event-single"); ?>>
			<header class="event-header mb-8">
				<?php if (has_post_thumbnail()) : ?>
					<div class="event-thumbnail mb-6">
						<?php the_post_thumbnail('large', ['class' => 'rounded-lg shadow-md']); ?>
					</div>
				<?php endif; ?>

				<h1 class="text-4xl font-bold mb-4"><?php the_title(); ?></h1>

				<ul class="event-meta text-lg text-muted-foreground mb-6">
					<?php if ($event_date) : ?>
						<li><strong>Date:</strong> <?php echo esc_html($event_date); ?></li>
					<?php endif; ?>
					<?php if ($event_time) : ?>
						<li><strong>Time:</strong> <?php echo esc_html($event_time); ?></li>
					<?php endif; ?>
					<?php if ($event_location) : ?>
						<li><strong>Location:</strong> <?php echo esc_html($event_location); ?></li>
					<?php endif; ?>
				</ul>
			</header>

			<div class="event-content prose max-w-none">
				<?php the_content(); ?>
			</div>
		</article>

		<div class="mt-12">
			<?php
			the_post_navigation([
				'prev_text' => '&laquo; %title',
				'next_text' => '%title &raquo;',
			]);
			?>
		</div>

	<?php endwhile; ?>

</main>