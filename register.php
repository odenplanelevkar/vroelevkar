<?php

/**
 * Template Name: Register
 */

 ?>

 <!DOCTYPE html>
 <html lang="en" dir="ltr">
   <head>
     <meta charset="utf-8">
     <meta name="viewport" content="width=device-width; initial-scale=1.0;">
     <title></title>
     <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,700&display=swap" rel="stylesheet">
     <link rel="stylesheet" href="<?php echo get_bloginfo('template_directory') ?>/css/admin.css">
     <link rel="stylesheet" href="<?php echo get_bloginfo('template_directory') ?>/css/front.css">
     <link rel="stylesheet" href="<?php echo get_bloginfo('template_directory') ?>/css/custom-login-style.css">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
     <link rel="icon" href="<?php echo get_bloginfo('template_directory') ?>/img/logga.png" type="image/icon type">
     <script src="<?php echo get_bloginfo('template_directory') ?>/js/animate-page.js" charset="utf-8"></script>
     <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
     <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
   </head>
   <body class="front login">

     <!-- ***********************************
     * ERROR HANDLING
     *************************************-->
     <?php show_error_alert(); ?>

 <script src="<?php echo get_bloginfo('template_directory') ?>/js/autocomplete.js" charset="utf-8"></script>

 <div id="login">
		<a href="/" id="logga-register"> <img src="<?php echo get_bloginfo('template_directory') . '/img/vitfluga.png'; ?>"> </a>

    <form class="allow-overflow" autocomplete="off" action="<?php echo (get_bloginfo('template_directory') . '/scripts/handle_members.inc.php'); ?>" method="POST">
      <?php

      // Check if form has been submited
      if (isset($_GET['add_user'])) {

        // Get the msg from the form
        $user_check = $_GET['add_user'];

        // Then check if there has been an error
        if ($user_check == 'empty'){
          echo '<p class="error">Du måste fylla i alla värden!</p>';
        }
        elseif ($user_check == 'noclassfound'){
          echo '<p class="error">Klassen hittades inte!</p>';
        }
        elseif ($user_check == 'invalidemail'){
          echo '<p class="error">Använd din skolmail!</p>';
        }
        elseif ($user_check == 'emailexists'){
          echo '<p class="error">Mailaddressen är redan använd!</p>';
        }
        elseif ($user_check == 'invalidyear'){
          echo '<p class="error">Avångsåret stämmer inte!</p>';
        }

      }

      ?>

      <?php
       if (!isset($_GET['first_name'])){
         echo '<input type="text" name="first_name" value="" placeholder="Förnamn..." required>';
       } else {
         echo '<input type="text" name="first_name" value="'. $_GET['first_name'] .'" placeholder="Förnamn..." required>';
       }


       if (!isset($_GET['last_name'])){
         echo '<input type="text" name="last_name" value="" placeholder="Efternamn..." required>';
       } else {
         echo '<input type="text" name="last_name" value="'. $_GET['last_name'] .'" placeholder="Efternamn..." required>';
       }

       ?>
       <div class="autocomplete">
         <input id="class" type="text" name="class_name" value="" placeholder="Klass...">
       </div>

       <?php

       if (!isset($_GET['email'])){
         echo '<input type="email" name="email_address" value="" placeholder="Skolmail..." pattern="(.+?)vrg.se$" oninvalid="this.setCustomValidity(\'Använd din skolmail!\')" oninput="this.setCustomValidity(\'\')" required>';
       } else {
         echo '<input type="email" name="email_address" value="'. $_GET['email'] .'" placeholder="Skolmail..." required>';
       }
      ?>

      <?php

      if (!isset($_GET['phonenumber'])){
        echo '<input type="tel" name="phonenumber" placeholder="Telefonnummer... (ex: 1234567890)" required>';
      } else {
        echo '<input type="tel" name="phonenumber" placeholder="Telefonnummer... (ex: 1234567890)" value="'. $_GET['telefonnummer'] .'" required>';
      }
     ?>


      <!-- <input type="number" name="end_year" value="" placeholder="Avgångsår (ex. 2022)..." list="end_years">

      <datalist id="end_years">
        <option value="<?php echo date('Y', strtotime('+3 years')); ?>" />
        <option value="<?php echo date('Y', strtotime('+2 years')); ?>" />
        <option value="<?php echo date('Y', strtotime('+1 years')); ?>" />
        <option value="<?php echo date('Y'); ?>" />
      </datalist> -->

      <input type="password" name="password" value="" placeholder="Lösenord..." required>

      <p class="notice"><i><b>Notera:</b> En medlemsansökan till kåren kommer att skickas automatiskt vid registrering om du inte redan är medlem.</i></p>
      <label>Jag godkänner <a href="#" target="_blank">medlemsvillkoren</a></label>  <input type="checkbox" required>
      <br><label>Jag godkänner användadet av cookies för att kunna förbli inloggad</label>  <input type="checkbox" required>


     <button type="submit" name="register_new_user" class="btn lg" value="/register">Registrera dig</button>
   </form>

		<p id="nav"><a href="/wp-login.php">Logga in</a> <span>|</span> <a href="/wp-login.php?action=lostpassword">Glömt lösenordet?</a></p>
    <p id="backtoblog"><a href="/">	← Tillbaka till VRO-Elevkar		</a></p>
	</div>


      <?php

      global $wpdb;

      $results = $wpdb->get_results('SELECT name FROM vro_classes');
      echo '<script type="text/javascript">';
      echo 'var jsonclasses = ' . json_encode($results);
      echo '</script>'

      ?>

      <script>
      var classes = getArrayFromColumn(jsonclasses, 'name');

      autocomplete(document.getElementById("class"), classes, 'Denna klass är ännu inte skapad');
      </script>



</body>
</html>
