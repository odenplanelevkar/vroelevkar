<?php

/**
 * Template Name: Register-Link
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

    <form autocomplete="off" action="<?php echo (get_bloginfo('template_directory') . '/scripts/handle_members.inc.php'); ?>" method="POST">
      <?php

      // Check if form has been submited
      if (isset($_GET['add_user'])) {

        // Get the msg from the form
        $user_check = $_GET['add_user'];

        // Then check if there has been an error
        if ($user_check == 'empty'){
          echo '<p class="error">Du m??ste fylla i alla v??rden!</p>';
        }
        elseif ($user_check == 'nostudentshell'){
          echo '<p class="error">Mailaddressen finns inte systemet, v??nligen dubbelkolla att du skrivit r??tt. Annars kontakta jones.hussain@vroelevkar.se s?? l??gger vi till din mailaddress i systemet!</p>';
        }
        elseif ($user_check == 'invalidemail'){
          echo '<p class="error">Anv??nd din skolmail!</p>';
        }
        elseif ($user_check == 'InvalidEmailOrPassword') {
          echo '<p class="error">Din mail eller l??senord var felaktig!</p>';
        }
      }

      ?>

       <?php

       if (!isset($_GET['email'])){
         echo '<input type="email" name="email_address" value="" placeholder="Skolmail..." pattern="(.+?)vrg.se$" oninvalid="this.setCustomValidity(\'Anv??nd din skolmail!\')" oninput="this.setCustomValidity(\'\')" required>';
       } else {
         echo '<input type="email" name="email_address" value="'. $_GET['email'] .'" placeholder="Skolmail..." required>';
       }
      ?>

      <?php

      if (!isset($_GET['phonenumber'])){
        echo '<input type="tel" name="phonenumber" placeholder="Telefonnummer... (ex: 1234567890)" required>';
      } else {
        echo '<input type="tel" name="phonenumber" placeholder="Telefonnummer... (ex: 1234567890)" value="'. $_GET['phonenumber'] .'" required>';
      }
     ?>

      <input type="password" name="password" value="" placeholder="L??senord..." required>

      <p class="notice"><i><b>Notera:</b> En medlemsans??kan till k??ren kommer att skickas automatiskt vid registrering om du inte redan ??r medlem.</i></p>
      <!-- <label>Jag godk??nner <a href="#" target="_blank">medlemsvillkoren</a></label>  <input type="checkbox" required> -->
      <label>Jag godk??nner <a href="https://eur-lex.europa.eu/legal-content/EN/TXT/PDF/?uri=CELEX:32016R0679" target="_blank">GDPR</a></label>  <input type="checkbox" required>
      <!-- <br><label>Jag godk??nner anv??ndadet av cookies f??r att kunna f??rbli inloggad</label>  <input type="checkbox" required> -->


     <button type="submit" name="link_new_user" class="btn lg" value="/register">Registrera dig</button>
   </form>

		<p id="nav"><a href="/wp-login.php">Logga in</a> <span>|</span> <a href="/wp-login.php?action=lostpassword">Gl??mt l??senordet?</a></p>
    <p id="backtoblog"><a href="/">	??? Tillbaka till VRO-Elevkar		</a></p>
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

      autocomplete(document.getElementById("class"), classes, 'Denna klass ??r ??nnu inte skapad');
      </script>



</body>
</html>
