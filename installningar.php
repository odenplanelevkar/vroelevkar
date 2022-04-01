<?php

/**
 * Template Name: Inställningar
 */

 // Do not show page for gamers only
 if (current_user_can('gamer')){
   wp_redirect( '/' );
 }

// Show this page only to admin
if (! is_user_logged_in() || !(current_user_can('administrator') || current_user_can('elevkaren') ) ){
  wp_redirect( '/panel' );
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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
  </head>
  <body>

    <div class="container">

      <!-- ***********************************
      * ERROR HANDLING
      *************************************-->
      <?php show_error_alert(); ?>

      <!--
      * Admin Navbar
      --------------------------------------->
      <?php
        require_once(get_template_directory() . "/parts/admin-navigation-bar.php");
      ?>

      <!--
      * Dashboard
      --------------------------------------->
      <section id="dashboard">

        <div class="top-bar">
          <h2>Inställningar</h2>
          <p><?php echo current_time('d M Y, D'); ?></p>
        </div>

        <div class="banner">
          <h3>Välkommen tillbaka Anna!</h3>
          <img src="<?php echo get_bloginfo('template_directory') ?>/img/chatright.png" alt="" class="chatright">
          <img src="<?php echo get_bloginfo('template_directory') ?>/img/chatleft.png" alt="" class="chatleft">
        </div>

      </section>

      <!--
      * Status View
      --------------------------------------->
      <?php
        require_once(get_template_directory() . "/parts/status-bar.php");
      ?>

    </div>

    <script src="<?php echo get_bloginfo('template_directory') ?>/js/admin.js" charset="utf-8"></script>
    <script type="text/javascript">
      window.onload = highlightLink('link-installningar');
    </script>

    <?php
    // End if admin
    }
    ?>

<?php get_footer(); ?>
