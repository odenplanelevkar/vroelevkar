<?php

/**
 * Template Name: Karkortet
 */

// Show this page only to logged in members
// $is_member = get_metadata('user', get_current_user_id(), 'status');

// if (! is_user_logged_in() or $is_member[0] != 'y' ){
//   wp_redirect( '/' );
// } else {

// Do not show page for gamers only
if (current_user_can('gamer')){
  wp_redirect( '/' );
}

if (! is_user_logged_in() ){
  wp_redirect( '/wp-login.php' );
} else {

?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta lang="sv">
    <meta name="viewport" content="width=device-width; initial-scale=1.0;">

    <title>VRO Elevkår</title>

    <link rel="icon" href="<?php echo get_bloginfo('template_directory') ?>/img/logga.png" type="image/icon type">
    <link rel="stylesheet" href="<?php echo get_bloginfo('template_directory') ?>/css/admin.css">
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,700&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="<?php echo get_bloginfo('template_directory') ?>/js/autocomplete.js" charset="utf-8"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
  </head>
  <body>

    <!-- ***********************************
    * ERROR HANDLING
    *************************************-->
    <?php show_error_alert(); ?>

    <div class="container">

      <!-- ***********************************
      * NAVBAR
      *************************************-->

      <?php
      // Display a special navbar for admins
      if (current_user_can('administrator') || current_user_can('elevkaren') ){
        require_once(get_template_directory() . "/parts/admin-navigation-bar.php");
      } else {
        require_once(get_template_directory() . "/parts/member-navigation-bar.php");
      }

      require_once(get_template_directory() . "/scripts/helpful_functions.php");
      ?>

       <!-- ***********************************
       * DASHBOARD
       *************************************-->
      <section id="dashboard">

        <!-- Display header and current time -->
        <div class="top-bar">
          <h2>Kårkortet</h2>
          <p><?php echo current_time('d M Y, D'); ?></p>
        </div>

        <div class="karkortet-container">
          <img class="karkortet" src="<?php echo get_bloginfo('template_directory') . '/img/karkort.png' ?>" alt="">
          <p><?php echo get_user_meta(get_current_user_id(),'nickname',true); ?></p>
        </div>

      </section>


      <!-- ***********************************
      * STATUS BAR
      *************************************-->
      <?php
        require_once(get_template_directory() . "/parts/status-bar.php");
      ?>

    </div>


  <script src="<?php echo get_bloginfo('template_directory') ?>/js/admin.js" charset="utf-8"></script>
  <script type="text/javascript">
    window.onload = highlightLink('link-karkortet');
  </script>

  <?php
  // End if admin
  }
  ?>

<?php get_footer(); ?>
