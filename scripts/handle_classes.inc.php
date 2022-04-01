<?php

// Include wp_core
require_once(explode("wp-content", __FILE__)[0] . "wp-load.php");
require_once( ABSPATH . 'wp-admin/includes/user.php' );

// Include helpful functions
include_once('helpful_functions.php');

// Create database entry
if (isset($_POST['add_class'])) {

  global $wpdb;

  $return = '/panel/medlemmar?new_class';

  // Get and check the class name from the submitet form
  $class_name = check_post( $_POST['class-name'], $return . '=empty' );

  // Capitalise first letter
  $class_name = ucfirst($class_name);

  // Check if the class name already exits
  check_if_entry_exists('vro_classes', 'name', $class_name, $return . '=nametaken' );

  // Create a new array that will hold all the arguments to create a new visselpipan suggestion
  $new_class = array();
  $new_class['name'] = $class_name;

  // Insert the new class into the database
  insert_record('vro_classes', $new_class, 'DB insertion failed in add_class');

  // Logg action
  $log_description = 'Lade till klassen ' . $new_class['name'];
  add_log( 'Klass', $log_description, get_current_user_id() );

  send_header($return . '=success');

}

elseif (isset($_POST['give_class_points'])) {

  global $wpdb;

  $class_name = test_input( $_POST['class-name'] );
  $class_points = test_input( $_POST['add-points'] );
  $callback = test_input( $_POST['callback'] );

  if (empty($callback)){
    $callback = '/panel/dashboard';
  }

  if ( empty($class_name) || empty($class_points)){
    header("Location: $callback?class_points=empty");
    exit();
  } else {

    if (!is_numeric($class_points)) {
      header("Location: $callback?class_points=nan&class_name=$class_name");
      exit();
    }

    // Capitalise first letter
    $class_name = ucfirst($class_name);

    $class_record = $wpdb->get_row('SELECT * FROM vro_classes WHERE name="'. $class_name .'"');

    // Check if there already is a class with that name
    if ( !$class_record ) {
      header("Location: $callback?class_points=noclassfound");
      exit();
    } else {

      // Create a new array that will hold all the arguments to create a new visselpipan suggestion
      $current_points = (int)$class_record->points;

      $new_points = $current_points + (int)$class_points;

      if ($wpdb->update( 'vro_classes', array( 'points' => $new_points ), array( 'id' => $class_record->id) ) == false){

        send_error( $callback . '?class_points', 'Det gick inte att ändra klasspoängen.' );
        // wp_die('add class points failed'); DEV

      } else {

        // Logg action
        $log_description = 'Ändrade klassen '. $class_name .' med ' . $class_points . ' poäng.';
        add_log( 'Klasspokalen', $log_description, get_current_user_id() );

        header("Location: $callback?class_points=success");
        exit();

      }
    }

  }

}
elseif (isset($_POST['give_classpoints_internal'])){
  global $wpdb;

  $class_id = check_number_value(test_input( $_POST['c_id'] ), '/panel/medlemmar?give_classpoints');
  $class_points = check_number_value(test_input( $_POST['add-points'] ), '/panel/medlemmar/?c_id=$class_id&give_classpoints');

  $class_record = $wpdb->get_row('SELECT * FROM vro_classes WHERE id=' . $class_id);

    // Check if there already is a class with that name
    if ( !$class_record ) {
      header("Location: /panel/medlemmar/?c_id=$class_id&give_classpoints=noclassfound");
      exit();
    } else {

      // Create a new array that will hold all the arguments to create a new visselpipan suggestion
      $current_points = (int)$class_record->points;

      $new_points = $current_points + (int)$class_points;

      if ($wpdb->update( 'vro_classes', array( 'points' => $new_points ), array( 'id' => $class_record->id) ) == false){
        send_error( '/panel/medlemmar/?c_id=$class_id&give_classpoints', 'Det gick inte att ändra klasspoängen.' );
        // wp_die('add class points failed'); DEV
      } else {

        // Logg action
        $log_description = 'Ändrade klassen '. $class_record->name .' med ' . $class_points . ' poäng.';
        add_log( 'Klasspokalen', $log_description, get_current_user_id() );

        header("Location: /panel/medlemmar/?c_id=$class_id&give_classpoints=success");
        exit();

      }
    }

}

elseif (isset($_POST['remove_class'])) {

  global $wpdb;

  // Set default return url
  $return = '/panel/medlemmar/?remove_class=';

  // Get form parameter
  $c_id = check_number_value( test_input($_POST['c_id']), $return . 'badCid');

  // Get all student-shells in class
  $student_shells = $wpdb->get_results("SELECT * FROM vro_users WHERE class_id = $c_id");

  // Remove student-shells and wp-user
  foreach ($student_shells as $s) {

    // Save wp id
    $wp_id = $s->wpuser_id;

    // Remove records from kommittées
    $kommitte_chairmans = $wpdb->get_results("SELECT * FROM vro_kommiteer WHERE chairman = $s->id");

    // If chairman for a kommitte, set chairman to null
    foreach ($kommitte_chairmans as $chairman) {
      update_record('vro_kommiteer', 'chairman', NULL, 'id', $s->id, $return . "changeChairmanFailed");
    }

    $kommitte_records = $wpdb->get_results("SELECT * FROM vro_kommiteer_members WHERE user_id = $s->id");

    // Delete kommitte records
    remove_record( 'vro_kommiteer_members', 'user_id', $s->id, $return . 'could_not_delete_komitte_record' );

    // Remove student shell
    remove_record( 'vro_users', 'id', $s->id, $return . 'could_not_delete_elevskal' );

    // Remove wpuser
    wp_delete_user( $s->wpid );

  }

  // Remove the class
  remove_record('vro_classes', 'id', $c_id, 'failed to remove class in remove class');

  // Send back to medlem-page
  send_header( $return . 'success' );

}

else {
  header("Location: /panel/medlemmar?new_class=error");
  exit();
} // End post
