<?php

/**
 * Template Name: Arkiv
 */

 // Do not show page for gamers only
 if (current_user_can('gamer')){
   wp_redirect( '/' );
 }

// Show this page only to admin or Elevkåren
if (! is_user_logged_in() ){
  wp_redirect( '/' );
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="<?php echo get_bloginfo('template_directory') ?>/js/autocomplete.js" charset="utf-8"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
  </head>
  <body>

    <div class="container">

      <!-- ***********************************
      * ERROR HANDLING
      *************************************-->
      <?php show_error_alert(); ?>

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
          <h2>Arkiv</h2>
          <p><?php echo current_time('d M Y, D'); ?></p>
        </div>

        <div class="archive-links">
          <a class="btn" href="#karbrev">Se alla kårbrev</a>
          <a class="btn" href="#kommitte">Se alla kommitténotiser</a>
          <a class="btn" href="https://drive.google.com/drive/folders/1NKwQTcbvxk_5yNe1PL0u5hBrrOjCNQbY" target="_blank">Se alla bilder</a>
          <?php   if (current_user_can('administrator') || current_user_can('elevkaren') ): ?>
            <a class="btn" href="#visselpipor">Se alla visselpipor</a>
          <?php endif; ?>
        </div>


        <h2 id="karbrev" class="archive-title">Kårbrev</h2>

        <?php display_karbrev( 0, false, true ); ?>

        <h2 id="kommitte" class="archive-title">Kommittéenotiser</h2>

        <?php

        // Show arhived kommitténotifications
        display_kommitte_notifications( 0, false, true );

        ?>

        <?php if (current_user_can('administrator') || current_user_can('elevkaren') ): ?>
        <h2 id="visselpipor" class="archive-title">Visselpipor</h2>

        <?php $results = $wpdb->get_results('SELECT * FROM vro_visselpipan WHERE status = "a"');

        // Add a new row and box for every suggestion
        foreach ($results as $r)
        {

          $student = get_studentshell_by_wpuser_id( $r->user_id );
          $fullname = get_full_studentname( $student );
          $class_name = get_classname_by_id( $student->class_id );

          echo '<div class="row">';
            echo '<div class="box white lg">';
              echo '<div class="see-more">';
                echo '<h4>' . $r->subject . '</h4>';
              echo '</div>';
              echo "<p><i>Från: $fullname - $class_name</i></p>";
              echo '<p>' . $r->text . '</p>';
            echo '</div>';
          echo '</div>';

        } // End foreach

        ?>
      <?php endif; ?>

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
    window.onload = highlightLink('link-arkiv');
  </script>

  <?php
  // End if admin
  }
  ?>

<?php get_footer(); ?>
