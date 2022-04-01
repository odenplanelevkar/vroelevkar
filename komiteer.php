<?php

/**
 * Template Name: Kommiteer
 */

 // Do not show page for gamers only
 if (current_user_can('gamer')){
   wp_redirect( '/' );
 }

 // Show this page to all logged in users
 if (! is_user_logged_in() ){
   wp_redirect( '/wp-login.php' );
 } else {

  // Get access to all wordpress database funcitonality
  global $wpdb;

  // Get all kommitéer applications
  $results = $wpdb->get_results('SELECT * FROM vro_kommiteer WHERE status = "w"');

  // Get all acceppted commitees
  $kommiteer = $wpdb->get_results('SELECT * FROM vro_kommiteer WHERE status = "y" ORDER BY name');

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
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700&display=swap" rel="stylesheet">
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
        if (isset($_GET['k_id'])){
            // Show the single view
            require_once(get_template_directory() . "/parts/single_kommitee.php");
        } else {

        ?>

        <!-- <?php if (isset($_GET['remove_kommitte']) && $_GET['remove_kommitte'] == 'success') : ?>
          <script type="text/javascript">
          Swal.fire(
            'Succée!',
            'Kommittén är nu borttagen.',
            'success'
            )
          </script>
        <?php endif; ?> -->

        <!-- Show page title and current date -->
        <div class="top-bar">
          <h2>Kommittéer</h2>
          <p><?php echo current_time('d M Y, D'); ?></p>
        </div>

        <p>Här kan man se och gå med i alla kommittéer och ansöka om en ny kommitté!</p>

        <?php
        // Show this only to admins and working student in elevkaren
        if (current_user_can('administrator') || current_user_can('elevkaren') ){

        ?>

        <div class="banner">

          <!-- Change the message depending on singular or plural application number -->
          <?php if (count($results) == 1){ ?>
            <h3><?php echo count($results); ?> ny förfrågan!</h3>
          <?php } else { ?>
            <h3><?php echo count($results); ?> nya förfrågningar!</h3>
          <?php } ?>

          <!-- Chatbox images for style -->
          <img src="<?php echo get_bloginfo('template_directory') ?>/img/chatright.png" alt="" class="chatright">
          <img src="<?php echo get_bloginfo('template_directory') ?>/img/chatleft.png" alt="" class="chatleft">
        </div>

        <?php

        /************************
        *  Applications
        ************************/

        // Add a new row and box for every suggestion
        foreach ($results as $r)
        {

          $student = get_student_by_id( $r->chairman );
          $fullname = get_full_studentname( $student );
          $class_name = get_classname_by_id( $student->class_id );

          $phpdate = strtotime($student->created);
          $date_created = date('Y/m/d H:i', $phpdate);

          ?>
          <div class="row">'
            <div class="box white lg">
              <div class="see-more">
                <h4><?php echo $r->name ?></h4>
                  <div>
                  <button onclick="showAnswerForm(<?php echo $r->id ?>)">Svara &#8594;</button>
                </div>
              </div>

              <p><i><b><?php echo $fullname; ?> <?php echo $class_name ?></b> - <?php echo $date_created; ?></i></p>
              <p class="komm-desc"><?php echo $r->description ?></p>

              <div class="answer" id="<?php echo $r->id ?>">

                <hr>

                <h4>Svar</h4>

                <form action="<?php echo (get_bloginfo('template_directory') . '/scripts/handle_kommiteer.inc.php'); ?>" method="POST">
                  <textarea name="komm_answer" placeholder="Svar..."></textarea>

                  <button name="accept_kommitee" value="<?php echo $r->id ?>" class="btn" type="submit">Godkänn</button>
                  <button name="deny_kommitee" value="<?php echo $r->id ?>" class="btn red" type="submit">Avböj</button>
                </form>

              </div>

            </div>
          </div>
        <?php
      } // End foreach applications
    } // End check administartor
        ?>

        <!-- **************************
          BASIC INFORMATION
        **************************  -->

        <?php

        // archive_old_notifications();
        display_kommitte_notifications();

        ?>

        <div class="row">

          <!-- <div class="box white sm">
            <h4>Kommiteeansvarig</h4>
          </div> -->

          <div class="box white lg">
            <h4>Sök kommiteer</h4>
            <input type="search" placeholder="Kommitténamn..." name="keyword" id="keyword" onkeyup="fetch()"></input>
            <div id="loader" class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>

            <div id="datafetch"></div>

            <script type="text/javascript">
            function fetch(){

                jQuery.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    type: 'post',
                    data: { action: 'kommitte_data_fetch', keyword: jQuery('#keyword').val() },
                    beforeSend: function() {
                      if (document.getElementById('keyword').value.length == 1){
                        document.getElementById('loader').style.display = 'block';
                      }
                    },
                    success: function(data) {
                        jQuery('#datafetch').html( data );
                    },
                    complete: function() {
                      document.getElementById('loader').style.display = 'none';
                    }
                });

              }
            </script>


          </div>

        </div>

        <!-- **************************
          ALL KOMMITÉES
        **************************  -->

        <div class="row">

          <div class="box green lg">
            <h4>Alla kommittéer</h4>

            <div class="kommiteer">

              <?php
              // Show this only to admins and working student in elevkaren
              if (current_user_can('administrator') || current_user_can('elevkaren') ){
                ?>

              <!-- Always show the add new kommitée card -->
              <div class="kommitee alert">
                  <a href="#new-kommitte" class="add-btn lg">+</a>
                  <h5>Skapa ny kommitée</h5>
              </div>

            <?php } //End if admin ?>

              <?php

              // Go through all accepted kommitées and display their name and member count
              foreach ($kommiteer as $k){

                // Get the member count
                $member_count = count($wpdb->get_results('SELECT * FROM vro_kommiteer_members WHERE kommitee_id=' . $k->id . ' AND status="y"'));

              ?>

              <!-- Create new element to hold the information -->
              <div class="kommitee join">
                <a href="/panel/kommiteer?k_id=<?php echo $k->id; ?>">
                    <!-- Name -->
                    <h4><?php echo $k->name ?></h4>
                    <?php
                    // Check if current user is member in this kommitté,
                        // if they are --> display Jag är medlem,
                        // If they are chairman --> display Jag är ordförande!
                        // if they have sent an application, display --> förfrågan skickad,
                        // if they are not member att all --> display nothing
                    $studentshell_id = get_studentshell_id( get_current_user_id() );
                    $member_check = $wpdb->get_row('SELECT * FROM vro_kommiteer_members WHERE kommitee_id = '. $k->id . ' AND user_id = '. $studentshell_id );
                    if ($member_check != NULL && $wpdb->get_row('SELECT * FROM vro_kommiteer WHERE id = '. $k->id . ' AND chairman = '. $studentshell_id ) != NULL ){
                      echo '<p>Jag är ordförande!</p>';

                      // Check if there are any applications waiting
                      $application_number = count( $wpdb->get_results('SELECT * FROM vro_kommiteer_members WHERE kommitee_id = '. $k->id . ' AND status = "w"') );
                      if ( $application_number > 0 ){
                        if ($application_number == 1) {
                          echo '<p class="attention">'. $application_number .' ny förfrågan!<p>';
                        } else {
                          echo '<p class="attention">'. $application_number .' nya förfrågningar!<p>';
                        }

                      }
                    }
                    elseif ($member_check != NULL && $member_check->status == 'y'){
                      echo '<p>Jag är medlem!</p>';
                    } elseif ($member_check != NULL && $member_check->status == 'w'){
                      echo '<p>Förfrågan skickad!</p>';
                    }


                    ?>
                </a>

              </div>

            <?php } ?>

            </div>

          </div>

        </div>


        <!-- **************************
          ADD NEW KOMMITÉE
        **************************  -->

        <div class="row">

          <div class="box white lg">

            <h3>Ansök om en ny kommitté</h3>

            <form action="<?php echo (get_bloginfo('template_directory') . '/scripts/handle_kommiteer.inc.php'); ?>" method="post">

              <?php // Show error messages

              if (isset($_GET['application'])) {

                $application_check = $_GET['application'];

                if ($application_check == 'empty') {
                  echo '<p class="error">Du måste fylla i alla värden!</p>';
                }
                elseif ($application_check == 'nametaken') {
                  echo '<p class="error">Kommitéenamnet är redan taget!</p>';
                }
                elseif ($application_check == 'success') {
                  echo '<p class="success">Din förfrågan har skickats!</p>';
                }

              }

             ?>

              <input type="text" name="namn" placeholder="Namn på kommittéen..." required>

              <?php

               if (isset($_GET['the_description'])) { ?>
                 <div class="text-limited-root">
                   <textarea name="description" placeholder="Beskrivning av kommittéen..." required onkeyup="checkForm(this, event_description_char_count, 300)"><?php echo $_GET['the_description']; ?></textarea>
                   <p id="event_description_char_count">300</p>
                 </div>
               <?php } else {  ?>
                 <div class="text-limited-root">
                   <textarea name="description" placeholder="Beskrivning av kommittéen..." required onkeyup="checkForm(this, event_description_char_count, 300)"></textarea>
                   <p id="event_description_char_count">300</p>
                 </div>
              <?php } ?>

              <button name="new_kommitee" class="btn lg" type="submit">Skicka ansökan</button>

            </form>

          </div>

        </div>

        <?php
        // Show this only to admins and working student in elevkaren
        if (current_user_can('administrator') || current_user_can('elevkaren') ){
        ?>

        <div class="row" id="new-kommitte">

          <div class="box white lg allow-overflow">

            <h3>Skapa ny kommitté</h3>
            <form action="<?php echo (get_bloginfo('template_directory') . '/scripts/handle_kommiteer.inc.php'); ?>" method="post" autocomplete="off">

              <?php

              if (isset($_GET['add_new'])) {

                $application_check = $_GET['add_new'];

                if ($application_check == 'nostudentfound') {
                  echo '<p class="error">Antingen har denna elev inte registrerat sig eller så finns inte eleven i systemet.</p>';
                }

              }

              ?>

              <input type="text" name="kommitte_name" value="" placeholder="Namn på kommittén..." required>
              <div class="text-limited-root">
                <textarea name="description" placeholder="Beskrivning av kommittéen..." required onkeyup="checkForm(this, kommitte_description_char_count, 300)"></textarea>
                <p id="kommitte_description_char_count">300</p>
              </div>
              <div class="autocomplete">
                  <input type="text" name="chairman_name" value="" placeholder="Ordförande..." id="chairman-field" required>
                  <input type="text"  name="chairman_id" id="chairman-id-field" hidden>
              </div>

              <button type="submit" name="add_new_kommitte" class="btn lg">Skapa</button>
            </form>

          </div>

        </div>

        <?php

        // Get the number of all members
        $all_students = $wpdb->get_results("SELECT * FROM vro_users WHERE class_id IS NOT NULL");

        // Get first and last name from every student
        $first_last_array_full = array();
        foreach($all_students as $s){
          array_push($first_last_array_full, get_full_studentname( $s ));
        }

        // Get a full array
        $full_student_array = array();
        foreach ($all_students as $s) {
          array_push($full_student_array, get_full_student_array( $s ));
        }

        echo '<script type="text/javascript">';
        echo 'var jsonstudentsall = ' . json_encode($first_last_array_full). ';';
        echo 'var jsonstudentsfull = ' . json_encode($full_student_array) . ';';
        echo '</script>'

        ?>

        <script type="text/javascript">

          //autocomplete(document.getElementById("chairman-field"), jsonstudentsall, 'Inga elever hittades.');
          autocompleteFull(document.getElementById("chairman-field"), jsonstudentsfull, 'Inga elever hittades.', document.getElementById("chairman-id-field"));
        </script>


      <?php } // End is admin

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
      window.onload = highlightLink('link-kommiteer');
    </script>

<?php get_footer(); ?>
