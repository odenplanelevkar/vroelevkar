<?php

/**
 * Template Name: Visselpipan
 */

 // Do not show page for gamers only
 if (current_user_can('gamer')){
   wp_redirect( '/' );
 }

// CHECK IF LOGGED IN
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
    <script src="<?php echo get_bloginfo('template_directory') ?>/js/forms.js" charset="utf-8"></script>
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
      ?>

      <!--
      * Dashboard
      --------------------------------------->
      <section id="dashboard">

        <div class="top-bar">
          <h2>Visselpipan</h2>
          <p><?php echo current_time('d M Y, D'); ?></p>
        </div>

          <p>Här kan du skicka förslag till elevkåren på saker som du vill förändra!</p>


        <?php

          // Only show the event types for admins
          if (current_user_can('administrator') || current_user_can('elevkaren') ){


          // Get all suggestions
          global $wpdb;

          $results = $wpdb->get_results('SELECT * FROM vro_visselpipan WHERE status = "w"');

         ?>

        <div class="banner">

          <?php if (count($results) == 1){ ?>
            <h3><?php echo count($results); ?> nytt förslag!</h3>
          <?php } else { ?>
            <h3><?php echo count($results); ?> nya förslag!</h3>
          <?php } ?>

          <img src="<?php echo get_bloginfo('template_directory') ?>/img/chatright.png" alt="" class="chatright">
          <img src="<?php echo get_bloginfo('template_directory') ?>/img/chatleft.png" alt="" class="chatleft">
        </div>

          <?php

          // Add a new row and box for every suggestion
          foreach ($results as $r)
          {

            $student = get_studentshell_by_wpuser_id( $r->user_id );
            $fullname = get_full_studentname( $student );
            $class_name = get_classname_by_id( $student->class_id );

            $phpdate = strtotime($student->created);
            $date_created = date('Y/m/d H:i', $phpdate);

            echo '<div class="row">';
              echo '<div class="box white lg">';
                echo '<div class="see-more">';
                  echo '<h4>' . $r->subject . '</h4>';
                  echo '<div>';
                    echo '<button onclick="showAnswerForm('. $r->id .')">Svara &#8594;</button>';
                  echo '</div>';
                echo '</div>';

                echo "<p><i><b>$fullname $class_name</b> - $date_created</i></p>";
                echo '<p>' . $r->text . '</p>';

                echo '<div class="answer" id="' . $r->id .'">';

                  echo '<hr>';

                  echo '<h4>Svar</h4>';

                  echo '<form action="'. get_bloginfo('template_directory') . '/scripts/handle_visselpipan.inc.php' . '" method="post">';
                    echo '<textarea name="visselpipaSvar" placeholder="Svar..." required></textarea>';
                    echo '<input name="visselpipaId" value='. $r->id .' hidden>';

                    echo '<button class="btn lg" type="submit" name="answerVisselpipa">Skicka</button>';
                  echo '</form>';

                echo '</div>';

              echo '</div>';
            echo '</div>';

          } // End foreach

        } // End check admin

          ?>

      <div class="row">

        <div class="box lg green">

          <h4>Skicka ett nytt förslag</h4>

          <form action="<?php echo (get_bloginfo('template_directory') . '/scripts/handle_visselpipan.inc.php'); ?>" method="post">

            <?php // Show error messages

            if (isset($_GET['visselpipa'])) {

              $visselpipa_check = $_GET['visselpipa'];

              if ($visselpipa_check == 'empty') {
                echo '<p class="error">Du måste fylla i alla fält!</p>';
              }
              elseif ($visselpipa_check == 'success') {
                echo '<p class="success">Ditt förslag har skickats!</p>';
              }

            }

           ?>

            <input type="text" name="subject" placeholder="Rubrik..." required>

            <div class="text-limited-root">
              <textarea name="text" placeholder="Förslag..." required onkeyup="checkForm(this, visselpipa_char_count, 300)"></textarea>
              <p id="visselpipa_char_count">300</p>
            </div>

            <button name="new_visselpipa" class="btn lg" type="submit">Skicka</button>

          </form>

        </div>

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
      window.onload = highlightLink('link-visselpipan');
    </script>

    <?php
    // End if admin
    }
    ?>

<?php get_footer(); ?>
