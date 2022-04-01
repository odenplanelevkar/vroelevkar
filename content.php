<?php

  // Check if post is already archived
if ( get_post_status( get_the_ID() ) != 'archive' && metadata_exists('post', get_the_ID(), 'expire_date') ) {
  // Archive old notifications

  // Check if the expire date has passed
  if (strtotime( get_post_meta( get_the_ID(), 'expire_date', true) ) < strtotime('now')){
    // Archive notification
    ?>

    <form action="<?php echo (get_bloginfo('template_directory') . '/scripts/handle_notification.inc.php'); ?>" method="post" hidden>
      <button name="delete_notification" value="<?php echo get_the_ID(); ?>" type="submit" id="auto-archive-button" hidden></button>
    </form>

    <script type="text/javascript">
    window.addEventListener('DOMContentLoaded', function(){
      document.getElementById('auto-archive-button').click();
    });


    </script>

    <?php

  }
}

?>

<div class="row">

  <article class="blog-post box white lg">

    <?php if ( metadata_exists('post', get_the_ID(), 'kommitte_name') ) : ?>

      <?php

      if (!is_user_logged_in()){
        wp_redirect( '/wp-login.php' );
      }

      $is_chairman = false;

      ?>


      <p class="blog-post-category"><?php echo get_post_meta( get_the_ID(), 'kommitte_name')[0]; ?></p>

      <?php
      // Check if current user is the chairman
      global $wpdb;

      $chairman = $wpdb->get_var('SELECT chairman FROM vro_kommiteer WHERE name = "' . get_post_meta( get_the_ID(), 'kommitte_name')[0] . '"');

      if ( $chairman == get_current_user_id() ){
        $is_chairman = true;
      }

      ?>
    <?php endif; ?>

    <?php if ( metadata_exists('post', get_the_ID(), 'month') ) : ?>
      <p class="blog-post-category"><?php echo get_post_meta( get_the_ID(), 'month' )[0]; ?></p>
    <?php endif; ?>

    <h3 class="blog-post-title"><?php the_title(); ?></h3>
    <p class="blog-post-date">Publicerades: <?php echo get_the_date(); ?></p>

    <!-- <?php if ( metadata_exists('post', get_the_ID(), 'expire_date') ) : ?>
      <p class="blog-post-date">Arkiveras den: <?php echo get_post_meta( get_the_ID(), 'expire_date', true); ?></p>
    <?php endif; ?> -->


      <p class="text-preview"><?php echo substr(get_the_excerpt(), 0, 349); ?></p>

      <!-- only show read more link if there is more content than fits in the notification -->
    <?php if (get_the_excerpt() != get_the_content() or strlen(get_the_excerpt()) > 350 ) : ?>
      <a class="blog-post-link" href="<?php the_permalink() ?>">Läs mer</a>
    <?php endif; ?>

    <?php if (current_user_can('administrator') || current_user_can('elevkaren') || $is_chairman ): ?>
    <div class="delete-button">
      <form action="<?php echo (get_bloginfo('template_directory') . '/scripts/handle_notification.inc.php'); ?>" method="post">
        <button name="archive_notification" value="<?php echo get_the_ID(); ?>" type="submit" onclick="return confirm('Är du säker på att vill arkivera detta inlägg?')"><img src="<?php echo get_bloginfo('template_directory') ?>/img/archive.png"></button>
      </form>

      <form action="<?php echo (get_bloginfo('template_directory') . '/scripts/handle_notification.inc.php'); ?>" method="post">
        <button class="description" name="delete_notification" value="<?php echo get_the_ID(); ?>" type="submit" onclick="return confirm('Är du säker på att vill ta bort detta inlägg?')">
          <img src="<?php echo get_bloginfo('template_directory') ?>/img/wrong.png">
        </button>
      </form>
    </div>

  <?php endif; ?>

  </article>

</div>
