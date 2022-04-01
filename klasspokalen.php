<?php

/**
 * Template Name: Klasspokalen
 */

 // Do not show page for gamers only
 if (current_user_can('gamer')){
   wp_redirect( '/' );
 }

// Show this page to all logged in users
if (! is_user_logged_in() ){
  wp_redirect( '/wp-login.php' );
} else {


global $wpdb;

$classes = $wpdb->get_results('SELECT * FROM vro_classes ORDER BY points DESC');
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
      ?>

      <!-- ***********************************
      * ERROR HANDLING
      *************************************-->
      <?php show_error_alert(); ?>

      <!--
      * Dashboard
      --------------------------------------->
      <section id="dashboard">

        <div class="top-bar">
          <h2>Klasspokalen</h2>
          <p><?php echo current_time('d M Y, D'); ?></p>
        </div>

        <div class="podium">

          <?php if (count($classes) > 1){ ?>
            <div class="second">
              <p>2</p>
              <p><?php echo $classes[1]->name; ?></p>
            </div>
          <?php } ?>

          <?php if (count($classes) > 0){ ?>
            <div class="first">
              <p>1</p>
              <p><?php echo $classes[0]->name; ?></p>
            </div>
          <?php } ?>

          <?php if (count($classes) > 0){ ?>
            <div class="third">
              <p>3</p>
              <p><?php echo $classes[2]->name; ?></p>
            </div>
          <?php } ?>

          <img class="bigcircle" src="<?php echo get_bloginfo('template_directory') ?>/img/blueelipse.png" alt="">
          <img class="smallercircle" src="<?php echo get_bloginfo('template_directory') ?>/img/roseelipse.png" alt="">
        </div>

        <?php if (current_user_can('administrator') || current_user_can('elevkaren') ){ ?>
        <div class="bow">

          <div class="box white md toplist">

            <h4>Topplista</h4>

            <?php for($i = 0; $i < count($classes); $i++){ ?>
              <a href="/panel/medlemmar?c_id=<?php echo $classes[$i]->id; ?>" class="top-row">
                <p><?php echo $i+1 . '. ' . $classes[$i]->name; ?></p>
                <div class="points">
                  <p><?php echo $classes[$i]->points; ?>p</p>
                </div>

              </a>
            <?php } // End for loop ?>

          </div>

          <div class="box green sm min-space allow-overflow">

            <div class="">

              <h4>Ge klasspoäng</h4>

              <form class="" action="<?php echo (get_bloginfo('template_directory') . '/scripts/handle_classes.inc.php'); ?>" method="post" autocomplete="off">
                <?php

                if (isset($_GET['class_points'])) {

                  $class_point_check = $_GET['class_points'];

                  if ($class_point_check == 'empty') {
                    echo '<p class="error">Du måste fylla i alla värden!</p>';
                  }
                  elseif ($class_point_check == 'noclassfound') {
                    echo '<p class="error">Ingen klass hittades!</p>';
                  }
                  elseif ($class_point_check == 'success') {
                    echo '<p class="success">Poängen har lagts till!</p>';
                  }

                }

                ?>

                <div class="autocomplete">
                  <input id="class-name" type="text" name="class-name" value="" placeholder="Klass..." required>
                </div>

                <input type="number" name="add-points" value="" placeholder="+/-Poäng..." required min="-1000" max="1000">
                <input type="text" name="callback" value="/panel/klasspokalen" hidden>

                <button class="btn lg" type="submit" name="give_class_points">Ge poäng</button>
              </form>



              <?php
              // AUTOCOMPLETE
              global $wpdb;

              $results = $wpdb->get_results('SELECT name FROM vro_classes ORDER BY points DESC');

              if (count($results) > 0){
                $top_class = $results[0];
              }

              echo '<script type="text/javascript">';
              echo 'var jsonclasses = ' . json_encode($results);
              echo '</script>'

              ?>

              <script>
              var classes = getArrayFromColumn(jsonclasses, 'name');

              autocomplete(document.getElementById("class-name"), classes, 'Denna klass är ännu inte skapad');
              </script>

            </div>

          </div>

        </div>
      <?php } else { ?>

        <div class="row">

          <div class="box white lg toplist">

            <h4>Topplista</h4>

            <?php for($i = 0; $i < count($classes); $i++){ ?>
              <a class="top-row">
                <p><?php echo $i+1 . '. ' . $classes[$i]->name; ?></p>
                <div class="points">
                  <p><?php echo $classes[$i]->points; ?>p</p>
                </div>
              </a>
            <?php } // End for loop ?>

          </div>

        </div>

      <?php } ?>

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
      window.onload = highlightLink('link-klasspokalen');
    </script>

    <?php
    // End if admin
    }
    ?>

<?php get_footer(); ?>
