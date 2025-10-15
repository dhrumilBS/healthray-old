<main class="min-h-screen bg-background">
    <!-- ========================= HERO SECTION ========================== -->
    <section class="sec-padded hero-section border-bottom">
        <div class="container">
            <div class="heading">
                <div class="badge">Orthopedics</div>
                <h1> <?= the_title(); ?> </h1>
                <div class="lead"> <?= the_content(); ?> </div>
            </div>
        </div>
    </section>

    <section class="sec-padded hero-image-section border-bottom">
        <div class="container">
            <div class="image-wrapper">
                <!-- Transforming Patient Care at Metropolitan Orthopedics -->
                <?= get_the_post_thumbnail(get_the_ID(), 'full'); ?>
            </div>
        </div>
    </section>

    <!-- ========================= METRICS SECTION ========================== -->
    <section class="sec-padded metrics-section border-bottom">
        <div class="container">
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

    <!-- ========================= CHALLENGE SECTION ========================== -->
    <section class="main-sec">
        <?php if (have_rows('text_block')) { ?>
            <?php while (have_rows('text_block')) {
                the_row(); ?>
                <section class="sec-padded border-bottom">
                    <div class="container narrow">
                        <?php if (get_sub_field('image')) {
                            echo "<div class=\"img-wrap\">";
                            echo wp_get_attachment_image(get_sub_field('image'), 'full');
                            echo "</div>";
                        } ?>
                        <div class="heading">
                            <h2 class="heading-lg"><?= get_sub_field('title'); ?></h2>
                        </div>
                        <div class="text-lg"><?= get_sub_field('text'); ?> </div>
                    </div>
                </section>
            <?php } ?>
        <?php } ?>
    </section>

    <!-- ========================= TESTIMONIAL SECTION ========================== -->
    <section class="sec-padded quote-section border-bottom">
        <div class="container narrow">
            <div class="quote-box">
                <blockquote class="quote-text"> <?= get_field('quote_text'); ?> </blockquote>
                <div class="quote-author">
                    <div class="author-name"><?= get_field('quote_person_name'); ?></div>
                    <div class="author-title"><?= get_field('quote_person_title'); ?></div>
                </div>
            </div>
        </div>
    </section>

    <!-- ========================= RESULTS SECTION ========================== -->
    <section class="sec-padded results-section border-bottom">
        <div class="container narrow">
            <div class="heading">
                <h2 class="heading-lg"><?= get_field('result_title'); ?></h2>
            </div>
            <div class="text-lg"><?= get_field('result_text'); ?></div>
        </div>
    </section>
</main>
<?php echo do_shortcode('[elementor-template id="26869"]'); ?>