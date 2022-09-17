<?php

/**
 * Template Name: Medlemmar
 */

 // Do not show page for gamers only
 if (current_user_can('gamer')){
   wp_redirect( '/' );
 }

// CHECK IF LOGGED IN
if (! is_user_logged_in() || !(current_user_can('administrator') || current_user_can('elevkaren') ) ){
  wp_redirect( '/panel' );
} else {

// Get wordpress database functionality
global $wpdb;

// Get all classes
$classes = $wpdb->get_results('SELECT * FROM vro_classes ORDER BY SUBSTRING(name , 3, 2)');

// Get the current student
$current_student_id = $wpdb->get_var("SELECT id FROM vro_users WHERE wpuser_id = ". get_current_user_id() );

// Set if member
$is_member = $wpdb->get_var("SELECT status FROM vro_users WHERE id = $current_student_id");
$is_member = ($is_member == NULL) ? 'n' : $is_member;

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

    <!-- ***********************************
    * ERROR HANDLING
    *************************************-->
    <?php show_error_alert(); ?>
    <?php show_success_alert( 'remove_class', 'Succé!', 'Klassen och dess medlemmar togs bort.' ); ?>

    <script src="<?php echo get_bloginfo('template_directory') ?>/js/admin.js" charset="utf-8"></script>
    <script src="<?php echo get_bloginfo('template_directory') ?>/js/autocomplete.js" charset="utf-8"></script>

    <div class="container">

      <!--
      * Admin Navbar
      --------------------------------------->
      <?php
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

        <?php
        // Check if a single class should be displayed
        if (isset($_GET['c_id'])){
          // Load the single_class template to show specific class information
          require_once(get_template_directory() . "/parts/single_class.php");
        } else {

        ?>



        <div class="top-bar">
          <h2>Medlemmar</h2>
          <p><?php echo current_time('d M Y, D'); ?></p>
        </div>

        <?php

        global $wpdb;

        $current_year = (int)date('Y');

        $user_amount = count($wpdb->get_results('SELECT * FROM vro_users WHERE class_id IS NOT NULL'));
        $member_amount = count($wpdb->get_results("SELECT * FROM vro_users WHERE status = 'y'"));

        // Get users currently in school (OBS: MUST FIX WITH YEAR BUG)
        $school_user_amount = count($wpdb->get_results('SELECT * FROM vro_users WHERE class_id IS NOT NULL AND end_year > ' . ($current_year)));
        $school_member_amount = count($wpdb->get_results("SELECT * FROM vro_users WHERE status = 'y' AND end_year > " . ($current_year)));

        // Only do the calculation if there are any students
        if ($user_amount != 0){
          $percentage = round($member_amount / $user_amount * 100);
          $school_percentage = round($school_member_amount / $school_user_amount * 100);
        } else {
          $percentage = 0;
          $school_percentage = 0;
        }

        ?>

        <?php if (current_user_can('administrator') || current_user_can('elevkaren') ){ ?>

          <?php

          // Get all members
          $waiting_members = $wpdb->get_results("SELECT * FROM vro_users WHERE status = 'w'");
          ?>

        <div class="banner">

          <?php
            if (count($waiting_members) == 1){
              echo '<h3>1 ny medlemsförfrågan!</h3>';
            } else {
              echo '<h3>'. count($waiting_members) .' nya medlemsförfrågningar!</h3>';
            }
          ?>

          <img src="<?php echo get_bloginfo('template_directory') ?>/img/chatright.png" alt="" class="chatright">
          <img src="<?php echo get_bloginfo('template_directory') ?>/img/chatleft.png" alt="" class="chatleft">
        </div>

        <?php

        // Add a new row and box for every suggestion

        foreach ($waiting_members as $wm)
        {
          ?>
          <div class="row">


            <div class="box white lg">
              <div class="see-more">
                <h4><?php echo get_full_studentname( $wm ); ?></h4>
                  <div>
                  <button onclick="showAnswerForm(<?php echo $wm->id ?>)">Svara &#8594;</button>
                </div>
              </div>

              <div class="answer" id="<?php echo $wm->id; ?>">

                <hr>

                <h4>Svar</h4>

                <form action="<?php echo (get_bloginfo('template_directory') . '/scripts/handle_members.inc.php'); ?>" method="POST">
                  <textarea name="member_answer" placeholder="Svar..."></textarea>
                  <input name="student_id" value="<?php echo $wm->id; ?>" hidden>

                  <button name="accept_member" class="btn" type="submit">Godkänn</button>
                  <button name="quit_being_member" class="btn red" type="submit">Avböj</button>
                </form>

              </div>

            </div>

            </div>

        <?php
        } // ENd foreach

        ?>

        <div class="bow">

          <div class="box white lg center">

            <div class="first-place members">
              <p><b><?php echo $percentage; ?>%</b></p>
              <p><?php echo $member_amount ?> / <?php echo $user_amount ?></p>
              <p><b>Totala Medlemmar</b></p>
            </div>

            <div class="first-place members">
              <p><b><?php echo $school_percentage; ?>%</b></p>
              <p><?php echo $school_member_amount ?> / <?php echo $school_user_amount ?></p>
              <p><b>Medlemmar på skolan</b></p>
            </div>

          </div>

        </div>

        <div class="row">

          <div class="box white lg">
            <h4>Sök elev</h4>

            <input type="search" placeholder="Namn.." name="keyword" id="keyword" onkeyup="fetch()"></input>
            <div id="loader" class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>

            <div id="datafetch"></div>

            <script type="text/javascript">
            function fetch(){

              jQuery.ajax({
                  url: '<?php echo admin_url('admin-ajax.php'); ?>',
                  type: 'post',
                  data: { action: 'data_fetch', keyword: jQuery('#keyword').val() },
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

        <div class="row">

          <div class="box white lg">

            <div class="see-more">
              <h4>Klasser</h4>
              <?php if (current_user_can('administrator') || current_user_can('elevkaren') ){
                echo '<h4>Medlemmar</h4>';
              }
              ?>
            </div>

            <?php

            foreach($classes as $c){
              // Setup counters for all members and non-members
              $student_members = count($wpdb->get_results("SELECT * FROM vro_users WHERE class_id = $c->id AND status = 'y'"));
              $student_non_members = count($wpdb->get_results("SELECT * FROM vro_users WHERE class_id = $c->id AND status <> 'y'"));

            ?>

              <a href="/panel/medlemmar?c_id=<?php echo $c->id; ?>" class="class">
                <p class="class-name"><?php echo $c->name; ?></p>

                <!-- <?php if (current_user_can('administrator') || current_user_can('elevkaren') ){ ?>
                  <div class="member_count">
                    <p><?php echo $student_members; ?></p>
                    <img src="<?php echo get_bloginfo('template_directory') ?>/img/right.png">
                    <p><?php echo $student_non_members; ?></p>
                    <img src="<?php echo get_bloginfo('template_directory') ?>/img/wrong.png">
                  </div>
                <?php } ?> -->

                <?php if (current_user_can('administrator') || current_user_can('elevkaren') ){ ?>
                  <div class="member_count">
                    <p class="member-label"><span class="is-members"><?php echo $student_members; ?></span>/<?php echo $student_members + $student_non_members; ?></p>
                  </div>
                <?php } ?>

              </a>

          <?php } ?>


          </div>

        </div>

        <?php if (current_user_can('administrator') || current_user_can('elevkaren') ){ ?>

        <div class="row">

          <div class="box green lg">

            <h4>Skapa ny klass</h4>
            <form class="" method="post" action="<?php echo (get_bloginfo('template_directory') . '/scripts/handle_classes.inc.php'); ?>">
              <input type="text" name="class-name" value="" placeholder="Klassnamn...">

              <button class="btn lg" type="submit" name="add_class">Skapa klass</button>
            </form>

          </div>

        </div>

        <div class="row">

          <div class="box green lg allow-overflow" id="student-shell-box">

            <h4>Skapa nytt elevskal</h4>
            <form autocomplete="off" method="post" action="<?php echo (get_bloginfo('template_directory') . '/scripts/handle_members.inc.php'); ?>">

              <?php // Show error messages

              if (isset($_GET['new_studentshell'])) {
                // Scroll to this point
                ?> <script type="text/javascript"> scrollToElement('student-shell-box');</script> <?php

                $check = $_GET['new_studentshell'];

                if ($check == 'success') {
                  echo '<p class="success">Det nya elevskalet lades till!</p>';
                }
                elseif ($check == 'noclassid') {
                  echo '<p class="error">Den angivna klassen finns tyvärr inte</p>';
                }
              }

             ?>

              <input type="text" name="first-name" value="" placeholder="*Förnamn..." required>
              <input type="text" name="last-name" value="" placeholder="*Efternamn..." required>
              <input id="student-email-field" type="email" name="email" value="" placeholder="*Skolmail..." pattern="(.+?)vrg.se$" oninvalid="this.setCustomValidity(\'Använd elevens skolmail!\')" oninput="this.setCustomValidity(\'\')" required>
              <div class="autocomplete">
                <input id="class-name-field" type="text" name="class-name" value="" placeholder="*Klass..." required oninput="fillProgramName('class-name-field', 'program-name-field')">
              </div>
              <input id="program-name-field" type="text" name="program" value="" placeholder="*Utbildningsprogram..." required>
              <input id="phonenumber-field" type="text" name="phonenumber" value="" placeholder="Telefonnummer...">
              <input type="text" name="birthyear" value="" placeholder="Födelseår...">
              <input type="text" name="registered-city" value="Stockholm" placeholder="Folkbokförd stad...">

              <select class="form-select" name="class-name">
                <option value="">- Klass -</option>
                <option value="EK23A">EK23A</option>
                <option value="EK23B">EK23B</option>
                <option value="SB23">SB23</option>
                <option value="NA23A">NA23A</option>
                <option value="NA23B">NA23B</option>
                <option value="NA23C">NA23C</option>
                <option value="NA23D">NA23D</option>
                <option value="EK24A">EK24A</option>
                <option value="EK24B">EK24B</option>
                <option value="SB24">SB24</option>
                <option value="NA24A">NA24A</option>
                <option value="NA24B">NA24B</option>
                <option value="NA24C">NA24C</option>
                <option value="NA24D">NA24D</option>
                <option value="EK25A">EK25A</option>
                <option value="EK25B">EK25B</option>
                <option value="SB25">SB25</option>
                <option value="NA25A">NA25A</option>
                <option value="NA25B">NA25B</option>
                <option value="NA25C">NA25C</option>
                <option value="NA25D">NA25D</option>
              </select>
              
              <select class="form-select" name="gender">
                <option value="">- Kön -</option>
                <option value="Kvinna">Kvinna</option>
                <option value="Man">Man</option>
                <option value="Annat">Annat</option>
              </select>

              <button class="btn lg" type="submit" name="add_studentshell">Skapa elevskal</button>
            </form>

          </div>

        </div>

        <div class="row">

          <div class="box green lg">

            <h4>Ladda ned medlemsregister till Ebas</h4>
            <form action="<?php echo (get_bloginfo('template_directory') . '/scripts/file_functions.inc.php') ?>" method="post">
              <div class="button-group">
                <button class="btn lg white" type="submit" name="download-member-report">Ladda ned medlemsrapport</button>
                <button class="btn lg" type="submit" name="download-member-list" value="/panel/medlemmar">Ladda ned medlemsregister</button>
              </div>

            </form>


          </div>

        </div>

        <div class="row">

          <div class="box white lg">

            <h4>Nytt kalenderår</h4>
            <p>Om du trycker på denna knapp kommer alla medlemmars <b>medlemsstatus</b> att ändras till "icke medlem". Inget mail skickas och alla elevskal finns kvar!</p>
            <form action="<?php echo (get_bloginfo('template_directory') . '/scripts/handle_members.inc.php') ?>" method="post">
              <button class="btn lg" type="submit" name="reset-members-new-term" value="/panel/medlemmar" onclick="event.stopPropagation(); return confirm('Är du säker på att du vill starta ett nytt kalenderår och nollställa samtliga medlemmars medlemsstatus?');">Nytt kalenderår</button>
            </form>
          </div>

        </div>

      <?php } // end check admin to add new class ?>

      <?php } else { // End check admin ?>

        <?php if (current_user_can('administrator') || current_user_can('elevkaren') ){ ?>
        <div class="row">

          <div class="box white lg center">


            <div class="first-place members">
              <p><b><?php echo $percentage; ?>%</b></p>
              <p><b>Medlemmar</b></p>
            </div>

          </div>

        </div>
        <?php } ?>

      <?php } ?>

      <div class="row">

        <div class="box green lg">


          <?php
          if ($is_member == 'n'){
            echo '<h4>Ansök om att bli medlem i elevkåren</h4>';
          }
          elseif ($is_member == 'w'){
            echo '<h4>Dra tillbaka din medlemsansökan</h4>';
          }
          else {
            echo '<h4>Gå ut ur elevkåren</h4>';
          }
          ?>
          <form class="" action="<?php echo (get_bloginfo('template_directory') . '/scripts/handle_members.inc.php'); ?>" method="post">
            <input type="text" name="student_id" value="<?php echo $current_student_id; ?>" hidden>

            <?php

            if ($is_member == 'y' or $is_member == 'w'){
              echo '<p>Du behöver ta kontakt med styrelsen för att kunna gå ut ur elevkåren.</p>';
            } else {
              echo '<button class="btn lg" type="submit" name="apply_for_member">Klicka för att skicka en medlemsansökan!</button>';
            }

            ?>

          </form>

        </div>

      </div>


      <!-- SWEET ALERTS -->
      <?php if (isset($_GET['reset-members']) && $_GET['reset-members'] == 'success') : ?>
        <script type="text/javascript">
        Swal.fire(
          'Succée!',
          'Alla elevers medlemsskap har nollställts och de har fått mail om registrering eller återregistrering!',
          'success'
          )
        </script>
      <?php endif; ?>


    <?php } // End show single_class or overview ?>

      </section>

      <!--
      * Status View
      --------------------------------------->
      <?php
        require_once(get_template_directory() . "/parts/status-bar.php");
      ?>

    </div>


    <script type="text/javascript">
      window.onload = highlightLink('link-medlemmar');
    </script>

    <?php

    global $wpdb;

    $results = $wpdb->get_results('SELECT name FROM vro_classes');
    echo '<script type="text/javascript">';
    echo 'var jsonclasses = ' . json_encode($results);
    echo '</script>'

    ?>

    <script>
    var classes = getArrayFromColumn(jsonclasses, 'name');

    autocomplete(document.getElementById("class-name-field"), classes, 'Denna klass är ännu inte skapad');

    </script>



    <?php
    // End if admin
    }
    ?>

<?php get_footer(); ?>
