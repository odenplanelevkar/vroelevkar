<?php

/**
 * Template Name: Projektgrupper
 */

 // Do not show page for gamers only
 if (current_user_can('gamer')){
   wp_redirect( '/' );
 }

 // CHECK IF LOGGED IN SND ADMIN
 if (! is_user_logged_in() ){
   wp_redirect( '/panel' );
 } else {

  // Get access to all wordpress database funcitonality
  global $wpdb;

  // Get all projektgrupper
  $projektgrupper = $wpdb->get_results('SELECT * FROM vro_projektgrupper ORDER BY name');
  $studentshell_id = get_studentshell_id( get_current_user_id() );

  require_once(get_template_directory() . "/scripts/helpful_functions.php");

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
    <script src="<?php echo get_bloginfo('template_directory') ?>/js/forms.js" charset="utf-8"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="<?php echo get_bloginfo('template_directory') ?>/js/autocomplete.js" charset="utf-8"></script>
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
      ?>
      <!-- **************************
        DASHBOARD
      **************************  -->
      <section id="dashboard">

        <?php

        // Check if a single kommitée should be displayed or the dashboard style
        if (isset($_GET['p_id'])){
            // Show the single view
            require_once(get_template_directory() . "/parts/single_projektgrupp.php");
        } else {

        ?>

        <!-- Show page title and current date -->
        <div class="top-bar">
          <h2>Projektgrupper</h2>
          <p><?php echo current_time('d M Y, D'); ?></p>
        </div>

        <p>Här kan man se och gå med i projektgrupper!</p>

        <!-- **************************
          ALLA PROJEKTGRUPPER
        **************************  -->

        <div class="row">

          <div class="box green lg">
            <h4>Alla projektgrupper</h4>

            <div class="kommiteer">

              <!-- Always show the add new kommitée card -->
              <?php if (is_student_admin()): ?>
              <div class="kommitee alert">
                  <a href="#new-projektgrupp" class="add-btn lg">+</a>
                  <h5>Skapa ny projektgrupp</h5>
              </div>
            <?php endif; ?>

              <?php

              // Go through all accepted kommitées and display their name and member count
              foreach ($projektgrupper as $p){

              ?>

              <!-- Check visibility, only show locked ones to admins -->
              <?php if ( ($p->visibility == 'e' && (current_user_can('administrator') || current_user_can('elevkaren') )) || $p->visibility == 'a' ): ?>

                <?php
                  $projektgrupp_classes = 'kommitee join';
                  $projektgrupp_classes .= ($p->visibility == 'e') ? ' green' : '';
                ?>

              <!-- Create new element to hold the information -->
              <div class="<?php echo $projektgrupp_classes; ?>">
                <a href="/panel/projektgrupper?p_id=<?php echo $p->id; ?>">
                    <!-- Name -->
                    <h4><?php echo $p->name ?></h4>
                    <?php
                    // Check if current user is member in this kommitté,
                        // if they have joined --> display Jag är medlem,
                        // if they are not member att all --> display nothing

                    $member_check = $wpdb->get_row('SELECT * FROM vro_projektgrupper_members WHERE projektgrupp_id = '. $p->id . ' AND user_id = '. $studentshell_id );

                    if ($member_check->status == 'y'){
                      echo '<p>Jag är med!</p>';
                    }
                    elseif ($member_check->status == 'w'){
                      echo '<p>Förfrågan skickad</p>';
                    }

                    if (is_student_admin()) {
                      $application_number = count( $wpdb->get_results('SELECT * FROM vro_projektgrupper_members WHERE projektgrupp_id = '. $p->id . ' AND status = "w"') );
                      if ( $application_number > 0 ){
                        if ($application_number == 1) {
                          echo '<p class="attention">'. $application_number .' ny förfrågan!<p>';
                        } else {
                          echo '<p class="attention">'. $application_number .' nya förfrågningar!<p>';
                        }

                      }
                    }

                    ?>
                </a>

              </div>

            <?php endif; ?>

            <?php } ?>

            </div>

          </div>

        </div>


        <!-- **************************
          ADD NEW KOMMITÉE
        **************************  -->

        <?php if (current_user_can('administrator') || current_user_can('elevkaren') ): ?>

        <div class="row" id="new-projektgrupp">

          <div class="box white lg allow-overflow">

            <h3>Skapa ny projektgrupp</h3>
            <form action="<?php echo (get_bloginfo('template_directory') . '/scripts/handle_projektgrupper.inc.php'); ?>" method="post" autocomplete="off">
              <input type="text" name="p_name" value="" placeholder="Namn på projektgruppen..." required>
              <div class="text-limited-root">
                <textarea name="p_description" placeholder="Beskrivning av projektgruppen..." required onkeyup="checkForm(this, projektgrupp_description_char_count, 300)"></textarea>
                <p id="projektgrupp_description_char_count">300</p>
              </div>

              <select class="form-select" name="visibility" required>
                <option value="">- Synlighet -</option>
                <option value="e">Endast elevkåren</option>
                <option value="a">Alla</option>
              </select>

              <button type="submit" name="add_new_projektgrupp" class="btn lg">Skapa</button>
            </form>

          </div>

        </div>

      <?php endif; // end is admin ?>




      <?php

    } // End show single kommitté

    }// End if admin ?>


    </section>


      <!-- **************************
        STATUS BAR
      **************************  -->
      <?php
        require_once(get_template_directory() . "/parts/status-bar.php");
      ?>

    </div>

    <script src="<?php echo get_bloginfo('template_directory') ?>/js/admin.js" charset="utf-8"></script>
    <script type="text/javascript">
      window.onload = highlightLink('link-projektgrupper');
    </script>

<?php get_footer(); ?>
