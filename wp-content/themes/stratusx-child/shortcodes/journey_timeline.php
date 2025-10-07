<?php
function journey_timeline_shortcode()
{
    ob_start();
?>
    <style>
        .journey-title { margin-bottom: 1rem; text-align: center; }
        .journey-subtitle { font-size: 1.125rem; text-align: center; }
        .timeline { position: relative; }
        .timeline-line { position: absolute; top: 0; left: 50%; transform: translateX(-50%); width: 4px; height: 100%; background: linear-gradient(to bottom, #152ce1, #1b3c74, #ff7e00); border-radius: 2px; z-index: 1; }
        .timeline-item { display: flex; position: relative; flex-wrap: wrap; }
        .timeline-item.reverse { flex-direction: row-reverse; }
        .timeline-card { border: 2px solid #eaeaea; border-radius: 20px; padding: 20px; box-shadow: 0 3px 12px rgba(0, 0, 0, 0.1); transition: all 0.3s ease; flex: 1 1 42%; max-width: 42%; }
        .timeline-card:hover { border-color: #152ce1; transform: translateY(-5px); box-shadow: 0 6px 20px rgba(0, 180, 216, 0.25); }
        .timeline-card.empty-card { visibility: hidden; }
        .timeline-year { color: #152ce1; font-size: 1.5rem; font-weight: 700; display: flex; align-items: center; margin-bottom: 10px; }
        .timeline-year svg { margin-right: 8px; }
        .timeline-card h3 { font-size: 1.25rem; font-weight: 700; }
        .timeline-dot-container { position: absolute; left: 50%; transform: translateX(-50%); z-index: 2; display: flex; justify-content: center; align-items: center; width: 20px; height: 20px; top: 50%; }
        .timeline-dot { width: 10px; height: 10px; background: linear-gradient(to bottom right, #152ce1, #1b3c74); border: 4px solid #fff; border-radius: 50%; box-shadow: 0 0 10px rgba(0, 0, 0, 0.2); position: relative; z-index: 2; }
        .timeline-ping { position: absolute; top: 50%; left: 50%; width: 20px; height: 20px; transform: translate(-50%, -50%); border-radius: 50%; background: rgba(0, 180, 216, 0.3); animation: ping 1.5s infinite ease-out; }
        @keyframes ping {
            0% { transform: translate(-50%, -50%) scale(1); opacity: 1; }
            100% { transform: translate(-50%, -50%) scale(2.5); opacity: 0; }
        }

        @media (max-width: 640px) {
            .timeline-dot-container { display: none; }
            .timeline-line { display: none; }
            .timeline-card { max-width: 100%; }
            .timeline-item.reverse { flex-direction: column;  }
            .timeline-item { justify-content: center; text-align: center; margin-bottom: 30px; }
        }
    </style>
    <section>
        <div class="container">
            <h2 class="journey-title">Our Journey</h2>
            <p class="journey-subtitle">A timeline of our major milestones and achievements</p>

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
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                    <path d="M17.6562 1.5625H16.7188V0H15.1562V1.5625H4.84375V0H3.28125V1.5625H2.34375C1.05141 1.5625 0 2.61391 0 3.90625V17.6562C0 18.9486 1.05141 20 2.34375 20H17.6562C18.9486 20 20 18.9486 20 17.6562V3.90625C20 2.61391 18.9486 1.5625 17.6562 1.5625ZM18.4375 17.6562C18.4375 18.087 18.087 18.4375 17.6562 18.4375H2.34375C1.91297 18.4375 1.5625 18.087 1.5625 17.6562V7.34375H18.4375V17.6562ZM18.4375 5.78125H1.5625V3.90625C1.5625 3.47547 1.91297 3.125 2.34375 3.125H3.28125V4.6875H4.84375V3.125H15.1562V4.6875H16.7188V3.125H17.6562C18.087 3.125 18.4375 3.47547 18.4375 3.90625V5.78125Z" fill="#152CE1" />
                                    <path d="M2.96875 8.98438H4.53125V10.5469H2.96875V8.98438ZM6.09375 8.98438H7.65625V10.5469H6.09375V8.98438ZM9.21875 8.98438H10.7812V10.5469H9.21875V8.98438ZM12.3438 8.98438H13.9062V10.5469H12.3438V8.98438ZM15.4688 8.98438H17.0312V10.5469H15.4688V8.98438ZM2.96875 12.1094H4.53125V13.6719H2.96875V12.1094ZM6.09375 12.1094H7.65625V13.6719H6.09375V12.1094ZM9.21875 12.1094H10.7812V13.6719H9.21875V12.1094ZM12.3438 12.1094H13.9062V13.6719H12.3438V12.1094ZM2.96875 15.2344H4.53125V16.7969H2.96875V15.2344ZM6.09375 15.2344H7.65625V16.7969H6.09375V15.2344ZM9.21875 15.2344H10.7812V16.7969H9.21875V15.2344ZM12.3438 15.2344H13.9062V16.7969H12.3438V15.2344ZM15.4688 12.1094H17.0312V13.6719H15.4688V12.1094Z" fill="#152CE1" />
                                </svg>
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