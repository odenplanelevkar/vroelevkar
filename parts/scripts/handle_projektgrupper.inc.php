<?php

  // Include wp_core
  require_once(explode("wp-content", __FILE__)[0] . "wp-load.php");

  // Include helpful functions
  include_once('helpful_functions.php');

  global $wpdb;

  if (got_post('add_new_projektgrupp')) {

    // Set the return location
    $return = '/panel/projektgrupper?add_projektgrupp';

    // Get the submittet values
    $p_name = check_post( $_POST['p_name'], $return . '=empty' );
    $p_description = check_post( $_POST['p_description'], $return . '=empty' );
    $p_visibilty = check_post( $_POST['visibility'], $return . '=empty' );

    // Check if projektgrupp already exists
    check_if_entry_exists( 'vro_projektgrupper', 'name', $p_name );

    // Check visibility
    if ($p_visibilty != 'e' && $p_visibilty != 'a' ) {
      send_header( $return . '=badVisibility' );
    }

    // Insert the new one
    $projektgrupp = array();
    $projektgrupp['name'] = $p_name;
    $projektgrupp['description'] = $p_description;
    $projektgrupp['visibility'] = $p_visibilty;

    insert_record( 'vro_projektgrupper', $projektgrupp, 'DB insertion failed: Failed to add projektgrupp in add_new_projektgrupp' );

    send_header( $return . '=success' );
  }

  elseif (got_post('remove_projektgrupp')){

    // Set the return location
    $return = '/panel/projektgrupper?remove_projektgrupp';

    $p_id = $_POST['p_id'];
    $p_id = check_number_value( $p_id, $return );

    // Remoe all student records in the kommitt+r
    remove_record( 'vro_projektgrupper_members', 'projektgrupp_id', $p_id, 'DB deletion failed: failed to remove member in remove_projektgrupp' );

    // Remove the actual kommitt+e
    remove_record( 'vro_projektgrupper', 'id', $p_id, 'DB deletion failed: failed to remove projektgrupp in remove_projektgrupp' );

    // Duccess!
    send_header( $return . '=success' );

  }

  elseif (got_post('join_projektgrupp')) {

    // Set the return location
    $return = '/panel/projektgrupper?join_projektgrupp';

    $p_id = check_number_value( test_input( $_POST['p_id'] ), $return);

    // If p_id eists, change the return
    $return = "/panel/projektgrupper?p_id=$p_id&join_projektgrupp";

    $u_id = check_number_value( test_input( $_POST['u_id'] ), $return);

    // Add student to projektgrupp
    $member_projektgrupp = array();
    $member_projektgrupp['user_id'] = $u_id;
    $member_projektgrupp['projektgrupp_id'] = $p_id;

    insert_record('vro_projektgrupper_members', $member_projektgrupp, 'DB insertion failed: Failed to add student to projektgrupp in join_projektgrupp');

    send_header( $return . '=success' );

  }

  elseif (got_post('leave_projektgrupp')) {

    global $wpdb;

    // Get the id's
    $p_id = test_input( $_POST['projektgrupp_id'] );
    $student_id = test_input( $_POST['student_id'] );

    // INPUT VALIDATION
    $p_id = check_number_value( $p_id, "/panel/projektgrupper?leave_projektgrupp=noPid" );
    $return = "/panel/projektgrupper?p_id=$p_id&leave_projektgrupp";

    $student_id = check_number_value( $student_id, $return );

    delete_record('vro_projektgrupper_members', array( 'projektgrupp_id' => $p_id, 'user_id' => $student_id ), 'DB deletion failed: Failed to remove student from projektgrupp in leave_projektgrupp');

    // Logg action
    $log_description = $student_id . ' lämnade projektgrupp med id ' . $p_id;
    add_log( 'Projektgrupper', $log_description, get_current_user_id() );

    send_header( $return . '=success');
  }

  elseif (got_post('add_student')) {

    global $wpdb;

    $return = '/panel/projektgrupper?add_student';

    $student_id = test_input( $_POST['student_id'] );
    $p_id = test_input( $_POST['projektgrupp_id'] );

    // Check if the k_id was supplied
    $p_id = check_number_value( $p_id, $return);

    $return = "/panel/projektgrupper?p_id=$p_id&add_member=";

    // CHeck if a student name was supplied
    check_if_empty( array($student_id), $®eturn . "nostudent" );

    if (!check_if_entry_exists('vro_projektgrupper', 'id', $p_id)) {
      send_header( $return . "noProjektgruppFound");
    }

    // Get the student with the supplied nickname
    // $student = get_student_from_nickname($student_name, "/panel/kommiteer?k_id=$k_id&add_member=nostudentfound");
    $student = $wpdb->get_row("SELECT * FROM vro_users WHERE id = $student_id");
    if (!$student) {
      send_header( $return . '=noStudentFound' );
    }

    // Check if student already exists
    if (count($wpdb->get_results('SELECT * FROM vro_projektgrupper_members WHERE projektgrupp_id = ' . $p_id . ' AND user_id = '. $student->id)) > 0){
      send_header( $return . "studentalreadyadded" );
    }

    // Add student to kommitté
    $new_member = array();

    $new_member['user_id'] = $student->id;
    $new_member['projektgrupp_id'] = $p_id;
    $new_member['status'] = 'y';

    // Insert the new suggestion into the database
    insert_record('vro_projektgrupper_members', $new_member, "DB insertion failed: failed to add new student in add_member in projektgrupp");

    // Logg action
    $log_description = $student->id . ' lades till i projektgruppen ' . $p_id;
    add_log( 'Projektgrupper', $log_description, get_current_user_id() );

    send_header( $return .  "success");

  }

  elseif (isset($_POST['accept_kommitee_member']) || isset($_POST['deny_kommitee_member'])) {

    /*****************************************
    * Change status
    *****************************************/
    global $wpdb;

    // Check kommité id
    $k_id = $_POST['kid'];

    $member_message = test_input( $_POST['kommitee_member_answer'] );

    $k_id = check_number_value( $k_id, '/panel/projektgrupper?projektgrupp_member' );
    $return = "/panel/projektgrupper?p_id=$k_id&projekt_member";

    // Get the kommitté name
    $kommitte_name = ($wpdb->get_row('SELECT * FROM vro_projektgrupper WHERE id='. $k_id))->name;

    // Check if accept button is pressed for a kommitée application
    if (isset($_POST['accept_kommitee_member']) && !empty($_POST['accept_kommitee_member']) && isset($_POST['kid'])) {

      $u_id = $_POST['accept_kommitee_member'];

      $u_id = check_number_value( $u_id, $return );

      // Change the specified wanting member to official member
      $accept_sql = 'UPDATE vro_projektgrupper_members SET status = "y" WHERE projektgrupp_id= '. $k_id .' AND user_id = '. $u_id;
      $wpdb->query( $wpdb->prepare( $accept_sql ) );

      // Check if there was a message
      if ( !check_if_empty( array($member_message, $$kommitte_name) ) ) {
        if ($student = $wpdb->get_row("SELECT * FROM vro_users WHERE id = $u_id")) {
          // Send the mail
          $email_address = $student->email;
          wp_mail( $email_address, 'Din ansökan till '. $kommitte_name .' har godkänts', $member_message );
        }
      }

      // Logg action
      $log_description = 'Accepterade projektgruppmedlemmen med id ' . $u_id . ' till projektgruppen ' . $kommitte_name;
      add_log( 'Projektgrupper', $log_description, get_current_user_id() );

      // Redirect with success message
      send_header( $return . '=success' );

    }

    // Check if eny buttons are pressed
    elseif (isset($_POST['deny_kommitee_member']) && !empty($_POST['deny_kommitee_member']) && isset($_POST['kid']) ){

      $u_id = $_POST['deny_kommitee_member'];
      $return = "/panel/projektgrupper?p_id=$k_id&projekt_member";

      $u_id = check_number_value( $u_id, $return );

      // Change the specified wanting member to official member
      $deny_sql = 'UPDATE vro_projektgrupper_members SET status = "n" WHERE projektgrupp_id= '. $k_id .' AND user_id = '. $u_id;
      $wpdb->query( $wpdb->prepare( $deny_sql ) );

      // Check if there was a message
      if (!empty($member_message) && !empty($kommitte_name)){
        // Get the applying student
        if ($student = $wpdb->get_row("SELECT * FROM vro_users WHERE id = $u_id")) {
          // Send the mail
          $email_address = $student->email;
          wp_mail( $email_address, 'Din ansökan till '. $kommitte_name .' har nekats', $member_message );
        }
      }

      // Logg action
      $log_description = 'Nekade projektgruppmedlemmen med id ' . $u_id . ' till projektgruppen ' . $kommitte_name;
      add_log( 'Projektgrupper', $log_description, get_current_user_id() );

      // Redirect with success message
      send_header( $return );

    }

  }

  elseif (isset($_POST['apply_for_projektgrupp'])){

    global $wpdb;

    // Get the id's
    $k_id = test_input( $_POST['p_id'] );
    $student_id = test_input( $_POST['student_id'] );
    $motivation = test_input( $_POST['motivation'] );

    // INPUT VALIDATION
    $return = "/panel/projektgrupper?apply_for_projektgrupp";
    $k_id = check_number_value( $k_id, $return);

    $return = "/panel/projektgrupper?p_id=$k_id&apply_for_projektgrupp";

    $student_id = check_number_value( $student_id, $return );

    // Check if this user already has sent an application
    if ( count($wpdb->get_results('SELECT * FROM vro_projektgrupper_members WHERE user_id='. $student_id .' AND projektgrupp_id='. $k_id .'')) > 0 ) {
      header( $return . "=alreadythere");
      exit();
    } else {

      // Insert an application
      $new_application = array();

      $new_application['user_id'] = $student_id;
      $new_application['projektgrupp_id'] = $k_id;
      $new_application['status'] = 'w';
      $new_application['motivation'] = $motivation;

      // Insert the new suggestion into the database
      insert_record('vro_projektgrupper_members', $new_application, 'DB insertion failed: failed to add new projektgrupp member application in apply_for_projekgrupp');

      // Logg action
      $log_description = $student_id . ' skickade en medlemsansökan till projekgrupp med id ' . $k_id;
      add_log( 'Projektgrupper', $log_description, get_current_user_id() );

      send_header( $return . "=success" );

    }

  }

  elseif (isset($_POST['toggle_projektgrupp'])) {

    global $wpdb;

    $p_id = check_number_value( $_POST['p_id'], '/panel/projektgrupper?toggle-projektgrupp' );
    $return = "/panel/projektgrupper?p_id=$p_id&toggle-projektgrupp";

    $projektgrupp = $wpdb->get_row("SELECT * FROM vro_projektgrupper WHERE id = $p_id");

    // Toggle
    $new_visibility = ($projektgrupp->visibility == 'e') ? 'a' : 'e';

    // Update
    update_record( 'vro_projektgrupper', 'visibility', $new_visibility, 'id', $p_id, $return . '=failedToggle' );

    // Success!
    send_header( $return . '=success' );

  }
