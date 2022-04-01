<?php

// Include wp_core
require_once(explode("wp-content", __FILE__)[0] . "wp-load.php");

// INclude heplful functions
require_once(get_template_directory() . "/scripts/helpful_functions.php");

/*****************************************
* Login
*****************************************/

// Custom Login
function my_custom_login() {
  echo '<link rel="stylesheet" type="text/css" href="' . get_bloginfo('template_directory') . '/css/custom-login-style.css" />';
}
add_action('login_head', 'my_custom_login');

add_filter( 'register_url', 'my_register_url' );
function my_register_url( $url ) {
    return '/register';
}

// Change logo url
function custom_login_logo_url() {
  return get_bloginfo( 'url' );
}
add_filter( 'login_headerurl', 'custom_login_logo_url' );

// Change login title
function custom_login_logo_url_title() {
  return 'Viktor Rydberg Odenplan';
}
add_filter( 'login_headertitle', 'custom_login_logo_url_title' );

// Change login redirect
function custom_login_redirect( $redirect_to, $request, $user ) {

  global $user;

  if ($_GET['redirect'] == 'game' ) {
    return '/game';
  }

  if ($request == '/game') {
    return $request;
  } else {
    // Check the user role of logged in user and send them to different default places
    if( isset( $user->roles ) && is_array( $user->roles ) ) {

      // If administrator, send them to wordpress dashboard
      if( in_array( "administrator", $user->roles ) ) {
        // return '/wp-admin';
        return '/panel/dashboard/';
      }

      // If part of elevkåren, send them to the elevkåren admin dashboard
      // if( in_array( "elevkaren", $user->roles ) ) {
      //   return '/panel/dashboard/';
      // }

      // Any other role, send to startscreen
      return '/panel/dashboard/';

    } else {

        // Send to the startscreen
        return '/panel/dashboard/';
      }
  }




}

add_filter("login_redirect", "custom_login_redirect", 10, 3);

// Add custom meta data on login
// function save_custom_meta_data( $user_id ) {
//   // Set user as member of elevkåren
//   add_user_meta( $user_id, 'status', 'y' );
//
//   // Set the class for the user
//   add_user_meta( $user_id, 'class_id', 1 );
// }
// add_action('user_register', 'save_custom_meta_data');

/*****************************************
* Roles
*****************************************/

// Add elevkåren role and configure it's capabilities
add_role( 'elevkaren', 'Elevkåren', array(
  'read' => true,
  'activate_plugins' => false,
  'edit_plugins' => false,
  'install_plugins' => false,
  'edit_users' => false,
  'manage_options' => false,
  'promote_users' => false,
  'remove_users' => false,
  'switch_themes' => false,
  'delete_site' => false,
  'edit_dashboard' => false
) );

add_role( 'gamer', 'Gamer' );

/*****************************************
* Database
*****************************************/
require_once(get_template_directory() . "/scripts/add_tables.php");

/*****************************************
* Ajax search
*****************************************/
// the ajax function
add_action('wp_ajax_data_fetch' , 'data_fetch');
add_action('wp_ajax_nopriv_data_fetch','data_fetch');
function data_fetch(){

    global $wpdb;

    $keyword = esc_attr( $_POST['keyword'] );

    // Get all students in vro_users
    $users = $wpdb->get_results('SELECT * FROM vro_users');

    foreach( $users as $u ) {
      $nickname = $u->first_name . ' ' . $u->last_name;

      if (stripos($nickname, $keyword) !== false ) {

        $email = $u->email;

        // Get class name
        $display_class_name = '';

        // Check if a class has been set for this student
        if ($u->class_id){
          // Get the class id
          $user_class_id = $u->class_id;

          // Check if there is a class with that class id
          if ($user_class = $wpdb->get_row('SELECT * FROM vro_classes WHERE id='. $user_class_id)) {
            // if so, get the class name
            $display_class_name = $user_class->name;
          }

        }

        // Check if student is member, waiting member or not to color code
        $div_class = ($u->status == 'y') ? 'member' : 'not-member';
        $div_class = ($u->status == 'w') ? 'waiting-member' : $div_class;

        // Check if student has registered or not
        $div_class .= ($u->wpuser_id == NULL) ? ' not-registered' : '';

        ?>
        <a href="/panel/medlemmar/?c_id=<?php echo $user_class_id; ?>#<?php echo $u->id; ?>">
          <p class="<?php echo $div_class ?>">
            <span><?php echo $nickname; ?></span>
            <span><?php echo $email; ?></span>
            <span><?php echo $u->phonenumber; ?></span>
            <span><?php echo $display_class_name; ?></span>
          </p>
        </a>
        <?php

      }
    }

    die();
}

add_action('wp_ajax_kommitte_data_fetch' , 'kommitte_data_fetch');
add_action('wp_ajax_nopriv_kommitte_data_fetch','kommitte_data_fetch');
function kommitte_data_fetch(){

    global $wpdb;

    $keyword = esc_attr( $_POST['keyword'] );

    // Get the number of all members
    $kommitter = $wpdb->get_results('SELECT * FROM vro_kommiteer WHERE status = "y"');


    foreach( $kommitter as $k ) {
      if (stripos($k->name, $keyword) !== false ) {
          ?>
            <a href="/panel/kommiteer/?k_id=<?php echo $k->id; ?>">
              <p class="member">
                <span><?php echo $k->name; ?></span>
              </p>
            </a>
         <?php
      }
    }

    die();
}

// the ajax function
add_action('wp_ajax_fetch_rooms' , 'fetch_rooms');
add_action('wp_ajax_nopriv_fetch_rooms','fetch_rooms');
function fetch_rooms(){

  // Access the wordpress database functions
  global $wpdb;

  $room_amount = count( $wpdb->get_results("SELECT * FROM vroregon_rooms") );

  // Check if there is a record saved
  if ( $room_amount == 0 ) {
    echo json_encode( array('rooms' => false) );
    die();
  }

  // Get the rooms stored in the database in a long string
  $rooms_string = $wpdb->get_var('SELECT rooms FROM vroregon_rooms WHERE id = 1');

  // Send the array back
  echo json_encode( array('rooms' => $rooms_string) );

  // Quit the function
  die();

}

// the ajax function
add_action('wp_ajax_save_rooms' , 'save_rooms');
add_action('wp_ajax_nopriv_save_rooms','save_rooms');
function save_rooms(){

  // Access the wordpress database functions
  global $wpdb;

  // Get the rooms stored in the database in a long string
  $rooms_string = $_POST['rooms_string'];
  $rooms_string = stripslashes($rooms_string);

  // Check if some rooms have been saved

  // Delete the existing record if there exists one
  if (count( $wpdb->get_results("SELECT * FROM vroregon_rooms") ) > 0) {
    if ($wpdb->delete( 'vroregon_rooms', array( 'id' => 1 ) ) == false) {
      echo json_encode( array('message' => 'Error: Could not delete rooms row') );
    }
  }

  // Create a new record and insert the room string
  $newRooms = array();
  $newRooms['id'] = 1;
  $newRooms['rooms'] = $rooms_string;

  if( $wpdb->insert('vroregon_rooms', $newRooms) == false){
    echo json_encode( array('message' => 'Error: Could not insert a new rooms row why') );
  } else {
    echo json_encode( array('message' => 'Success: Inserted a new rooms row') );
  }

  //Quit the function
  die();

}

// the ajax function
add_action('wp_ajax_save_sprites' , 'save_sprites');
add_action('wp_ajax_nopriv_save_sprites','save_sprites');
function save_sprites(){

  // Access the wordpress database functions
  global $wpdb;

  // Get the rooms stored in the database in a long string
  $sprite_string = $_POST['sprite_string'];
  $sprite_string = stripslashes($sprite_string);

  // Check if some rooms have been saved
  $sprite_amount = count( $wpdb->get_results("SELECT * FROM vroregon_rooms WHERE id = 2") );

  // Check if there is a record saved
  if ( $sprite_amount == 0 ) {
    // Create a new record and insert the room string
    $newSprite = array();
    $newSprite['id'] = 2;
    $newSprite['rooms'] = $sprite_string;

    insert_record( 'vroregon_rooms', $newSprite, 'Failed to insert a new sprite row in save_sprites() in functions.php' );

    echo json_encode( array('message' => 'Inserted a new sprite row') );

  } else {

    // Update the existing rooms row
    update_record( 'vroregon_rooms', 'rooms', $sprite_string, 'id', 2 );

    echo json_encode( array('message' => 'Updated sprite row') );

  }

  //Quit the function
  die();

}


// the ajax function
add_action('wp_ajax_save_player' , 'save_player');
add_action('wp_ajax_nopriv_fetch_player','save_player');
function save_player(){

  // Access the wordpress database functions
  global $wpdb;

  // Get the rooms stored in the database in a long string
  $player_string = $_POST['player_string'];
  $player_string = stripslashes($player_string);

  // Get the current user id
  $current_uid = get_current_user_id();

  // Check if a player object already exists
  if ( !check_if_entry_exists( 'vroregon_players', 'user_id', $current_uid ) ) {

    // Create new player entry
    $savedPlayer = array();
    $savedPlayer['user_id'] = $current_uid;
    $savedPlayer['player'] = $player_string;

    // Insert it into the database
    insert_record( 'vroregon_players', $savedPlayer, 'Failed to insert a new saved player in functions.php save_player()' );
    echo json_encode( array('message' => 'inserted a new player record') );

  } else {

    // Update the player class
    update_record( 'vroregon_players', 'player', $player_string, 'user_id', $current_uid );
    echo json_encode( array('message' => 'updated the record') );

  }

  // Quit the function
  die();

}

add_action('wp_ajax_get_saved_player' , 'get_saved_player');
add_action('wp_ajax_nopriv_get_saved_player','get_saved_player');
function get_saved_player() {

  global $wpdb;

  $current_uid = get_current_user_id();
  if ( $current_uid == 0){
    echo json_encode( array('error' => 'no user id') );
  } else {

    // Check if there is a saved player
    if (check_if_entry_exists( 'vroregon_players', 'user_id', $current_uid )) {
        // Get the player
        $saved_player = $wpdb->get_var('SELECT player FROM vroregon_players WHERE user_id = '. $current_uid);

        // Return it
        echo json_encode( array('player' => $saved_player) );
    } else {

      echo json_encode( array('player' => false) );

    }

  }

  die();

}

// the ajax function
add_action('wp_ajax_clear_player' , 'clear_player');
add_action('wp_ajax_nopriv_clear_player','clear_player');
function clear_player(){

  // Access the wordpress database functions
  global $wpdb;

  // Get the current user id
  $current_uid = get_current_user_id();

  // Check if a player object already exists
  if ( check_if_entry_exists( 'vroregon_players', 'user_id', $current_uid ) ) {

    // Create new player entry
    remove_record( 'vroregon_players', 'user_id', $current_uid, 'Kunde inte ta bort spelar-data från databasen' );

    echo json_encode( array('message' => 'removed player record') );

  }

  // Quit the function
  die();

}
