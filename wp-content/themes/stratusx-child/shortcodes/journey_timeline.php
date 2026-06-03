<?php
add_shortcode('journey_timeline', 'journey_timeline_shortcode');
function journey_timeline_shortcode()
{
    // ACF fields
    $heading    = get_field('timeline_heading');
    $subheading = get_field('timeline_subheading');
    $milestones = get_field('journey_milestones');

    if (!$milestones) {
        return '<p>No milestones found. Add some in ACF.</p>';
    }

    ob_start();
?>
    <style>
        .journey-title { text-align: center; }
        .journey-subtitle { text-align: center; max-width: 900px; margin: 0 auto 30px!important; }
        .timeline { position: relative; }
        .timeline-line { position: absolute; top: 0; left: 50%; transform: translateX(-50%); width: 4px; height: 100%; background: linear-gradient(to bottom, #152ce1, #1b3c74, #ff7e00); border-radius: 2px; z-index: 1; }
        .timeline-item { display: flex; position: relative; flex-wrap: wrap; }
        .timeline-item.reverse { flex-direction: row-reverse; }
        .timeline-card { border: 2px solid #eaeaea; border-radius: 20px; padding: 20px; box-shadow: 0 3px 12px rgba(0, 0, 0, 0.1); transition: all 0.3s ease; flex: 1 1 47%; max-width: 47%; background-color: #FFF; 
        }
        .timeline-card:hover { border-color: #152ce1; transform: translateY(-5px); box-shadow: 0 6px 20px rgba(0, 180, 216, 0.25); }
        .timeline-year { color: #152ce1; display: flex; margin-bottom: 12px; align-items: center; font-weight: 700; font-size: 18px; }
        .timeline-year img { margin-right: 8px; }
        .timeline-card h3 { display: flex; flex-direction: column; font-size: 1.25rem; }
        .timeline-dot-container { position: absolute; left: 50%; transform: translateX(-50%); z-index: 2; display: flex; justify-content: center; align-items: center; width: 20px; height: 20px; top: 50%; }
        .timeline-dot { width: 10px; height: 10px; background-color: #1b3c74; border-radius: 50%; z-index: 2; }
        @media (max-width: 480px) { 
            .timeline-item { margin-bottom: 20px; }
            .timeline-dot-container { display: none; }
            .timeline-line { display: none; }
            .timeline-card { max-width: 100%; }
            .timeline-item.reverse { flex-direction: column; }
        }
    </style>

    <section>
        <div class="container">

            <?php if ($heading) : ?>
                <h2 class="journey-title"><?php echo esc_html($heading); ?></h2>
            <?php endif; ?>

            <?php if ($subheading) : ?>
                <p class="journey-subtitle"><?php echo esc_html($subheading); ?></p>
            <?php endif; ?>

            <div class="timeline">
                <div class="timeline-line"></div>

                <?php foreach ($milestones as $index => $m) :
                    $reverse = $index % 2 !== 0 ? 'reverse' : '';
                ?>
                    <div class="timeline-item <?php echo $reverse; ?>">
                        <div class="timeline-card">

                            <div class="timeline-year">
                                <?php if (!empty($m['year'])) : ?>
                                    <img src="https://healthray.com/wp-content/uploads/2025/10/calendar.svg" alt="Calendar" width="20" height="20" aria-hidden="true">
                                <?php endif; ?>

                                <?php echo esc_html($m['year']); ?>
                            </div>

                            <h3><?php echo esc_html($m['title']); ?></h3>
                            <p><?php echo esc_html($m['desc']); ?></p>
                        </div>

                        <div class="timeline-dot-container">
                            <div class="timeline-dot"></div>
                        </div>
                    </div>
                <?php endforeach; ?>

            </div>
        </div>
    </section>

<?php
    return ob_get_clean();
}