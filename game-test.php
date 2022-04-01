<?php
/**
 * Template Name: Game-Test
 */


?>

<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0>
    <style> body {padding: 0; margin: 0;} </style>

    <link rel="icon" href="<?php echo get_bloginfo('template_directory') ?>/img/logga.png" type="image/icon type">
    <LINK REL=StyleSheet HREF="<?php echo get_bloginfo('template_directory') ?>/p5/code/style.css" TYPE="text/css" MEDIA=screen>
    <link href="https://fonts.googleapis.com/css2?family=VT323&display=swap" rel="stylesheet">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/js/all.min.js" charset="utf-8"></script>

  </head>
  <body>

    <!-- ***********************************
    * ERROR HANDLING
    *************************************-->
    <?php show_error_alert(); ?>

    <?php

    global $wpdb;

    $user = wp_get_current_user();
    $has_played = (count($wpdb->get_results("SELECT * FROM vroregon_players WHERE user_id = $user->ID")) > 0) ? true : false;

    ?>


      <div id="credits">

        <div class="choices">

        </div>

        <div class="creators">

          <div class="creator left">
            <img src="<?php echo get_bloginfo('template_directory') ?>/game-assets/creators/noah.png" alt="">
            <p>NOAH: SCRIPT WRITER</p>
          </div>

          <div class="creator right">
            <img src="<?php echo get_bloginfo('template_directory') ?>/game-assets/creators/iris.png" alt="">
            <p>IRIS: DESIGNER AND PLAY TESTER</p>
          </div>

          <div class="creator left">
            <img src="<?php echo get_bloginfo('template_directory') ?>/game-assets/creators/sofia.png" alt="">
            <p>SOFIA: DESIGNER AND PLAY TESTER</p>
          </div>

        </div>

      </div>




      <!-- SCRIPTS -->
      <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.2.2/gsap.min.js" charset="utf-8"></script>
      <script src="<?php echo get_bloginfo('template_directory') ?>/p5/code/credits.js"></script>


  </body>
</html>
