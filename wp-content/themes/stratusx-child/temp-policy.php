<?php
/* Template Name: Policy */
?>
<div class="content-editor">
    <?php the_content(); ?>
</div>


<script src='https://code.jquery.com/jQuery-3.6.4.js'> </script>
<script>
    jQuery(document).ready(function($) {
        const base = '.content-editor ol.main-list > li';

        // Level 1: direct ol/ul children of each main-list li
        $(base + ' :is(ol, ul)').addClass('level-1').attr('role', 'list-1');

        // Level 2: direct ol/ul children of level-1 li's only
        $(base + ' :is(ol, ul).level-1 > li  :is(ol, ul)').addClass('level-2').attr('role', 'list-2');

        // Level 3: direct ol/ul children of level-2 li's only
        $(base + ' :is(ol, ul).level-1 > li  :is(ol, ul).level-2 > li > :is(ol, ul)').addClass('level-3').attr('role', 'list-3');
    });
</script>