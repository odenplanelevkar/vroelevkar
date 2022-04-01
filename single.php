<?php get_header() ?>

      <main>

        <?php

          while ( have_posts() ) : the_post();

          get_template_part( 'content-post' );

          endwhile; // End of the loop.

        ?>

      </main>


  <?php get_footer(); ?>
