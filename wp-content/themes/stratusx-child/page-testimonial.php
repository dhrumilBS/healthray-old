<?php  ?>

<?php
$testimonials = [];
while( have_rows('testimonial', 'option') ): the_row(); 
      $name     = get_sub_field('name');
      $quote    = get_sub_field('quote');
      $image    = get_sub_field('image');
      $hospital = get_sub_field('hospital');
      $position = get_sub_field('position');
      $rating   = get_sub_field('rating'); // 1–5
      $testimonials[get_row_index()]['name'] = $name;
      $testimonials[get_row_index()]['quote'] = $quote;
      $testimonials[get_row_index()]['image'] = $image;
      $testimonials[get_row_index()]['hospital'] = $hospital;
      $testimonials[get_row_index()]['position'] = $position;
      $testimonials[get_row_index()]['rating'] = $rating;
endwhile;
?>

<section class="sec-padded hero-section testimonials">
    <div class="container">
        <div class="heading">
            <h1>What Our Doctors Say </h1>
            <p> Discover how our platform has transformed businesses and helped professionals achieve their goals. These authentic testimonials showcase the real impact we deliver. </p>
        </div>

        <div class="customer-proof-container">
            <div class="profile-stack"> <?php foreach ($testimonials as $i => $testimonial) { if ($i < 4) { echo wp_get_attachment_image($testimonial['image'], ["50", "50"]); } } ?> </div>
            <span class="proof-text"> Join 500+ satisfied customers </span>
        </div>
    </div>
</section>


<section class="testimonials-cards sec-padded-40">
    <div class="container">

    <div class="heading text-center">
        <h2>Trusted by Industry Leaders</h2>
        <p>From startups to enterprise companies, professionals across industries trust our platform to drive growth and streamline operations.</p>
    </div>
        <div class="testimonial-grid">
            <?php if ($testimonials): ?>
                <?php foreach ($testimonials as $testimonial):;
                    $name     = $testimonial['name'];
                    $quote    = $testimonial['quote'];
                    $image    = $testimonial['image'];
                    $hospital = $testimonial['hospital'];
                    $position = $testimonial['position'];
                    $rating   = $testimonial['rating'];

                    $quoteSVG  = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" height="24" width="24"><g fill="#3daded"><path d="m23.859026 16.8767757c-11.798316 0-21.359026 9.5607052-21.359026 21.3590298 0 11.1880608 8.5436096 20.1385117 19.3248348 21.1556053-2.2376099 4.0683899-6.1025782 8.3401871-13.0188351 12.408577-1.8307738 1.0170975-3.0512896 3.0512848-3.0512896 5.2888947 0 4.2718048 4.4752254 7.3230972 8.3401899 5.4923248 11.5949039-5.2889023 30.919733-17.9008942 30.919733-44.3454018.0000002-12.0017414-9.3572844-21.3590298-21.155607-21.3590298z"/><path d="m76.3412094 16.8767757c-11.798317 0-21.3590202 9.5607052-21.3590202 21.3590298 0 11.1880608 8.5436096 20.1385117 19.3248329 21.1556053-2.2376099 4.0683899-6.1025772 8.3401871-13.0188408 12.408577-1.8307762 1.0170975-3.0512848 3.0512848-3.0512848 5.2888947 0 4.2718048 4.4752197 7.3230972 8.3401909 5.4923248 11.5948944-5.2889023 30.9197235-17.9008942 30.9197235-44.3454018.2034302-12.0017414-9.3572845-21.3590298-21.1556015-21.3590298z"/></g></svg>';

                ?>

                    <?php if (!empty($name) || !empty($quote)): ?>
                        <div class="testimonial-card">
                            <div class="quote-sign left"><?= $quoteSVG; ?></div>
                            <p class="quote"><?= esc_html($quote); ?></p>
                            <div class="quote-sign right"><?= $quoteSVG; ?></div>


                            <div class="stars">
                                <?php
                                $maxStars = 5;
                                $rating   = floatval($rating);
                                $starSVG  = '<svg class="star-icon" fill="currentColor" width="22" height="22" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>';

                                for ($i = 1; $i <= $maxStars; $i++) {
                                    if ($rating >= $i) {
                                        echo '<span class="star full">' . $starSVG . '</span>';
                                    } elseif ($rating > $i - 1 && $rating < $i) {
                                        $percent = ($rating - ($i - 1)) * 100;
                                        echo '<span class="star partial">
                                            <span class="star-fill" style="width:' . $percent . '%">' . $starSVG . '</span>
                                            <span class="star-empty">' . $starSVG . '</span>
                                        </span>';
                                    } else {
                                        echo '<span class="star empty">' . $starSVG . '</span>';
                                    }
                                }
                                ?>
                            </div>

                            <div class="doctor-meta">
                                <?php if ($image):
                                    echo wp_get_attachment_image($image, 'full');
                                endif; ?>
                                <div class="doctor-detail">
                                    <div class="name"><?= esc_html($name); ?></div>
                                    <p class="hospital"> <?= $hospital; ?> </p>
                                    <p class="position"> <?= $position; ?> </p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<?= do_shortcode('[elementor-template id="26869"]'); ?>