<?php
function journey_timeline_shortcode()
{
    ob_start();
?>
    <style>
        .journey-title { margin-bottom: 1rem; text-align: center; }
        .journey-subtitle { font-size: 1.125rem; text-align: center; margin-bottom: 1.5rem; }
        .timeline { position: relative; }
        .timeline-line { position: absolute; top: 0; left: 50%; transform: translateX(-50%); width: 4px; height: 100%; background: linear-gradient(to bottom, #152ce1, #1b3c74, #ff7e00); border-radius: 2px; z-index: 1; }
        .timeline-item { display: flex; position: relative; flex-wrap: wrap; }
        .timeline-item.reverse { flex-direction: row-reverse; }
        .timeline-card { border: 2px solid #eaeaea; border-radius: 20px; padding: 20px; box-shadow: 0 3px 12px rgba(0, 0, 0, 0.1); transition: all 0.3s ease; flex: 1 1 47%; max-width: 47%; }
        .timeline-card:hover { border-color: #152ce1; transform: translateY(-5px); box-shadow: 0 6px 20px rgba(0, 180, 216, 0.25); }
        .timeline-card.empty-card { visibility: hidden; }
        .timeline-year { color: #152ce1; font-size: 1.5rem; font-weight: 700; display: flex; align-items: center; margin-bottom: 10px; }
        .timeline-year img { margin-right: 8px; }
        .timeline-card h3 { font-size: 1.25rem; font-weight: 700; }
        .timeline-dot-container { position: absolute; left: 50%; transform: translateX(-50%); z-index: 2; display: flex; justify-content: center; align-items: center; width: 20px; height: 20px; top: 50%; }
        .timeline-dot { width: 10px; height: 10px; background-color: #1b3c74; border-radius: 50%; z-index: 2; }
        @media (max-width: 480px) {
            .timeline-dot-container { display: none; }
            .timeline-line { display: none; }
            .timeline-card { max-width: 100%; }
            .timeline-item.reverse { flex-direction: column;  }
            .timeline-item { justify-content: center; text-align: center; margin-bottom: 30px; }
        }
    </style>
    <section>
        <div class="container">
            <h2 class="journey-title">Our Milestones</h2>
            <p class="journey-subtitle">A journey started with a simple promise to ourselves, which gave us the courage to keep pushing our limits and working towards excellence.</p>

            <div class="timeline">
            <div class="timeline-line"></div>

                <?php
                $milestones = [
                    ['year' => '2024', 'title' => 'Global Services', 'desc' => 'Currently, we have a team of 350+ staff members and are trusted by 1000+ medical professionals across the world.'],
                    ['year' => '2023', 'title' => 'AI-Powered Integration', 'desc' => 'Build generative AI tools to improve accuracy and proper guidance at each step.'],
                    ['year' => '2022', 'title' => 'Medical Solution', 'desc' => 'Embedded to third-party API and developing mobile solutions.'],
                    ['year' => '2021', 'title' => 'Launching New Products', 'desc' => 'Adopted by 200+ doctors and introduced LIMS and PMS product solutions.'],
                    ['year' => '2020', 'title' => 'HIPAA Security', 'desc' => 'Implementing scalable, secure solutions that adhere to HIPAA compliance standards.'],
                    ['year' => '2019', 'title' => 'The Beginning', 'desc' => 'Starting with a team of 30 members by the vision to advance medical services.'],
                ];

                foreach ($milestones as $index => $m) :
                    $reverse = $index % 2 !== 0 ? 'reverse' : '';
                ?>
                    <div class="timeline-item <?php echo $reverse; ?>">
                        <div class="timeline-card">
                            <div class="timeline-year">
                            <img src="https://healthray.com/wp-content/uploads/2025/10/calendar.svg" alt="Calendar Icon" width="20" height="20">
                                <?php echo $m['year']; ?>
                            </div>
                            <h3><?php echo $m['title']; ?></h3>
                            <p><?php echo $m['desc']; ?></p>
                        </div>
                        <div class="timeline-dot-container">
                            <div class="timeline-dot"></div>
                            <div class="timeline-ping"></div>
                        </div>
                    </div>
                <?php endforeach; ?>

            </div>
        </div>
    </section>

<?php
    return ob_get_clean();
}
add_shortcode('journey_timeline', 'journey_timeline_shortcode');