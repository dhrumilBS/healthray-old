<div class="wrap" role="document">
    <div class="content">
        <div class="inner-container th-no-sidebar">

            <?php if (have_posts()) : while (have_posts()) : the_post();

                    $event_title = get_the_title();
                    $event_image = get_the_post_thumbnail('', 'full');
                    $start_date  = get_field('event_start_date');
                    $end_date    = get_field('event_end_date');
                    $location    = get_field('event_location');

                    // Format date
                    $date = '';
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
                    } ?>

                    <!-- Event Heading -->
                    <section class="single-event-header text-center mt-5 mb-4">
                        <div class="container">
                            <h1 class="entry-title"><?= esc_html($event_title); ?></h1>
                        </div>
                    </section>

                    <!-- Featured Image -->
                    <?php if ($event_image) : ?>
                        <section class="single-event-image text-center mb-4">
                            <div class="container">
                                <div class="event-img-wrap">
                                    <?= $event_image; ?>
                                </div>
                            </div>
                        </section>
                    <?php endif; ?>

                    <!-- Event Details -->
                    <?php if ($date || $location) : ?>
                        <section class="single-event-meta text-center mb-5">
                            <div class="container">
                                <div class="event-meta-items d-inline-flex flex-wrap justify-content-center align-items-center gap-4 p-3 rounded-3 bg-light shadow-sm">

                                    <?php if ($date) : ?>
                                        <div class="event-date d-flex align-items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#1b3c74" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar-days-icon">
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
                                            <span><?= esc_html($date); ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($location) : ?>
                                        <div class="event-location d-flex align-items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#1b3c74" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin-icon">
                                                <path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0" />
                                                <circle cx="12" cy="10" r="3" />
                                            </svg>
                                            <span><?= esc_html($location); ?></span>
                                        </div>
                                    <?php endif; ?>

                                </div>
                            </div>
                        </section>
                    <?php endif; ?>

            <?php endwhile;
            endif; ?>

        </div><!-- /.inner-container -->
    </div><!-- /.content -->
</div><!-- /.wrap -->

<style>
    .single-event-header h1 { max-width: 900px; margin: 0 auto; }
    .single-event-image .event-img-wrap img { margin: 0 auto; border-radius: 16px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15); }
    .single-event-meta .event-meta-items { display: inline-flex; flex-wrap: wrap; justify-content: center; gap: 40px; font-size: 1.1rem; color: #333; }
    .single-event-meta .event-meta-items span { font-weight: 500; }
    @media (max-width:768px) { .single-event-meta .event-meta-items { gap: 20px; font-size: 1rem; } }
</style>