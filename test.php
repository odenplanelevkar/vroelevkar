<?php

// Show this page only to admin
if (! is_user_logged_in() ){
  wp_redirect( '/' );
} else {

// if (isset($_POST['upload_image'])){
//
//   if (move_uploaded_file($_FILES['file']['tmp_name'], get_bloginfo('template_directory') . "uploaded-images". $_FILES["file"]['name'])) {
//        // image will get uploaded here
//   }
//
// }

// $ch = curl_init();
// curl_setopt($ch, CURLOPT_URL, "https://api.sl.se/api2/realtimedeparturesV4.json?key=e65c80f983114b93b9db0523c81bb59e&siteid=9192&timewindow=10");
// curl_setopt($ch, CURLOPT_POST, 1);
// curl_setopt($ch, CURLOPT_POSTFIELDS, POST_DATA);
// $result = curl_exec($ch);
//
// print_r($result);
// curl_close($ch);

// $curl = curl_init();
// curl_setopt($curl, CURLOPT_URL, 'https://api.sl.se/api2/realtimedeparturesV4.json?key=e65c80f983114b93b9db0523c81bb59e&siteid=9117&timewindow=10');
// $result = curl_exec($curl);
// var_dump($result);
//
//
// $curl = curl_init();
// curl_setopt($curl, CURLOPT_URL, 'https://api.sl.se/api2/typeahead.json?key=80e078e73d144691922d25320d52a6c9&searchstring=Universitetet&stationsonly=true&maxresults=10');
// $result = curl_exec($curl);
// var_dump($result);





// echo $result_json['trip']['LegList']['Leg'][0]['destination'];

// var_dump($result);

// $str_json = file_get_contents('php://input');
// var_dump($str_json);
//
// global $wbdb;
//
// // Create a new array that will hold all the arguments to create a new visselpipan suggestion
// $rooms = array();
//
// $rooms['rooms'] = $str_json;
//
// // Insert the new suggestion into the database
// if($wpdb->insert(
//     'vroregon_testrooms',
//     $rooms
// ) == false) {
//   wp_die('database insertion failed');
// }

?>

<?php

  get_header();

 ?>





 <a href="/panel/dashboard/" class="btn lg">Admin</a>

 <?php

 if (isset($_POST['save_user'])) {

   $profile_image_name = time() . '_' . $_FILES['profileImage']['name'];

   $target = $_SERVER['DOCUMENT_ROOT'] . '/uploaded-images/' . $profile_image_name;

   move_uploaded_file( $_SERVER['DOCUMENT_ROOT'] . $_FILES['profileImage']['tmp_name'], $target );

 }

?>


<form class="" action="/test" method="post" enctype="multipart/form-data">
  <p>Profile image</p>
  <input type="file" name="profileImage" id="profileImage" value="">

  <button type="submit" class="btn" name="save_user">Save profile image</button>
</form>



 <?php

 // $travel_url = 'https://api.sl.se/api2/TravelplannerV3_1/trip.json?key=471f7b533072422587300653963192ad&originExtId=9117&destExtId=9203&products=8&lines=50';
 // $curl = curl_init();
 // curl_setopt($curl, CURLOPT_URL, $travel_url);
 // curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
 // $result = curl_exec($curl);
 // curl_close($curl);
 //
 // $json = json_decode($result, true);
 // // echo $json['Trip'][0]['LegList']['Leg'][0]['Origin']['time'];
 //
 // foreach ($json['Trip'] as $trip) {
 //   echo '<h2>' . $trip['LegList']['Leg'][0]['Origin']['name'] . ': '. $trip['LegList']['Leg'][0]['Origin']['time'] .' - ' . $trip['LegList']['Leg'][0]['Destination']['name'] . ': '. $trip['LegList']['Leg'][0]['Destination']['time'] .'</h2>';
 // }

?>

 <script src="<?php echo get_bloginfo('template_directory') ?>/js/autocomplete.js" charset="utf-8"></script>

 <section id="forms-test">

   <form action="<?php echo (get_bloginfo('template_directory') . '/scripts/handle_visselpipan.inc.php'); ?>" method="post">

     <h2>Visselpipan</h2>

     <?php // Show error messages

     if (isset($_GET['visselpipa'])) {

       $visselpipa_check = $_GET['visselpipa'];

       if ($visselpipa_check == 'empty') {
         echo '<p class="error">Du måste fylla i alla värden!</p>';
       }
       elseif ($visselpipa_check == 'success') {
         echo '<p class="success">Ditt förslag har skickats!</p>';
       }

     }

    ?>

     <input type="text" name="subject" placeholder="Rubrik..." required>
     <textarea name="text" placeholder="Förslag..." required></textarea>

     <button name="new_visselpipa" class="btn lg" type="submit">Skicka</button>

   </form>

   <h2>Kommitéer</h2>
   <form action="<?php echo (get_bloginfo('template_directory') . '/scripts/handle_kommiteer.inc.php'); ?>" method="post">

     <h2>Ny Kommitée</h2>

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

     <input type="text" name="namn" placeholder="Namn..." required>

     <?php

      if (isset($_GET['the_description'])) {
        echo '<textarea name="description" placeholder="Beskrivning..." required>'. $_GET['the_description'] .'</textarea>';
      } else {
        echo '<textarea name="description" placeholder="Beskrivning..." required></textarea>';
      }
     ?>

     <button name="new_kommitee" class="btn lg" type="submit">Skicka</button>

   </form>

   <form autocomplete="off" action="<?php echo (get_bloginfo('template_directory') . '/scripts/handle_members.inc.php'); ?>" method="POST">
     <input style="display:none">
     <input type="text" name="first_name" value="" placeholder="Förnamn..." required>
     <input type="text" name="last_name" value="" placeholder="Efernamn..." required>
     <input type="email" name="email_address" value="" placeholder="Skolmail..." required>
     <input type="password" name="password" value="" placeholder="Lösenord..." required>
    <div class="autocomplete">
      <input id="class" type="text" name="class_name" placeholder="Klass..." required>
    </div>
    <button type="submit" name="add_new_user" class="btn lg">Lägg till användare</button>
  </form>


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

var request = new Request('https://api.sl.se/api2/realtimedeparturesV4.json?key=e65c80f983114b93b9db0523c81bb59e&siteid=9192&timewindow=10');

fetch(request, {mode: 'no-cors'})
  .then(response => {
    return response.json();
  })
  .then(data => {
    console.log(data);
  })
  .catch(err => {
    console.log('error');
  })

  // rooms = [
  //
  //   new room(0, 0, 'you wake up on a...', [
  //     {
  //       text: 'option 1 herre',
  //       cmd: 'tp',
  //       values: [0, 1]
  //     },
  //     {
  //       text: 'option 2 herre',
  //       cmd: 'tp',
  //       values: [0, 1]
  //     },
  //     {
  //       text: 'option 2 herre',
  //       cmd: 'tp',
  //       values: [0, 1]
  //     },
  //     {
  //       text: 'option 2 herre',
  //       cmd: 'tp',
  //       values: [0, 1]
  //     },
  //
  //     {
  //       text: 'option 2 herre',
  //       cmd: 'tp',
  //       values: [0, 1]
  //     }
  //
  //   ]),
  // //------------------------------------------------
  //   new room(0, 1, 'new place omg', [
  //     {
  //       text: 'new option 1 herre',
  //       cmd: 'tp',
  //       values: [1, 1]
  //     },
  //     {
  //       text: 'new option 2 herre',
  //       cmd: 'tp',
  //       values: [1, 1]
  //     },
  //     {
  //       text: 'new option 3 herre',
  //       cmd: 'info',
  //       values: ['info info waow!']
  //     },
  //
  //   ]),
  //   new room(1, 1, 'even newer place', [
  //     {
  //       text: 'new option 1 herre',
  //       cmd: 'tp',
  //       values: [3, 3]
  //     },
  //     {
  //       text: 'new option 2 herre',
  //       cmd: 'tp',
  //       values: [3, 3]
  //     },
  //     {
  //       text: 'new option 3 herre',
  //       cmd: 'info',
  //       values: ['info info waow!']
  //     },
  //   ]),
  //     new room(3, 3, 'even newer place', [
  //       {
  //         text: 'new option 1 herre',
  //         cmd: 'tp',
  //         values: [3, 4]
  //       },
  //       {
  //         text: 'new option 2 herre',
  //         cmd: 'tp',
  //         values: [1, 1]
  //       },
  //       {
  //         text: 'new option 3 herre',
  //         cmd: 'info',
  //         values: ['info info waow!']
  //       },
  //
  //     ]),
  //     new room(3, 4, 'even newer place', [
  //       {
  //         text: 'new option 1 herre',
  //         cmd: 'tp',
  //         values: [3, 3]
  //       },
  //       {
  //         text: 'new option 2 herre',
  //         cmd: 'tp',
  //         values: [3, 3]
  //       },
  //       {
  //         text: 'new option 3 herre',
  //         cmd: 'info',
  //         values: ['info info waow!']
  //       },
  //
  //     ])

  //]//slut på rooms arrayn



</script>

<form enctype="multipart/form-data" action="/test" method="post">
  <label id="img">image: <input type="file" name="img" id='media'/></label>
  <button type="submit" name="button" name="upload_image">Upload</button>
</form>

 </section>

 <!-- <script src="<?php echo get_bloginfo('template_directory') ?>/js/modal.js" charset="utf-8"></script> -->


 <?php

get_footer();

  ?>

<?php } ?>
