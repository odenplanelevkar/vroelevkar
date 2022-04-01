<?php

  // Include wp_core
  require_once(explode("wp-content", __FILE__)[0] . "wp-load.php");

  // Include helpful functions
  include_once('helpful_functions.php');

  global $wpdb;



  /*****************************************
  * Create new kommitee application
  *****************************************/

  // Create database entry
  if (isset($_POST['new_kommitee'])) {

    global $wpdb;

    $new_name = test_input( $_POST['namn'] );
    $new_description = test_input( $_POST['description'] );

    // Go through all values and check if they are empty
    check_if_empty( array($new_name, $new_description), '/panel/kommiteer?application=empty' );

    // Check if there already is a kommitée with that name
    check_if_entry_exists('vro_kommiteer', 'name', $new_name, "/panel/kommiteer?application=nametaken&the_description=$new_description");

    // Check if studentshell exists
    $studentshell_id = check_studentshell( get_current_user_id(), '/panel/kommiteer?application=nostudentshell' );

    // Create a new array that will hold all the arguments to create a new kommitee
    $kommitee = array();

    $kommitee['name'] = $new_name;
    $kommitee['description'] = $new_description;
    $kommitee['chairman'] = $studentshell_id;

    insert_record( 'vro_kommiteer', $kommitee, 'DB insertion failed: failed to add new kommitte in new_kommittee' );

    // Logg action
    $log_description = 'Lade till kommittén ' . $kommitee['name'] . ' med beskrivningen ' . $kommitee['description'] . ' med ordförande ' . $new_name;
    add_log( 'Kommittéer', $log_description, get_current_user_id() );

    // Set the chairman as a new member
    $new_kommitee = $wpdb->get_results('SELECT * FROM vro_kommiteer WHERE name="'. $new_name .'"');

    $kommitee = array();

    $kommitee['user_id'] = $studentshell_id;
    $kommitee['kommitee_id'] = $new_kommitee[0]->id;
    $kommitee['status'] = 'y';

    insert_record( 'vro_kommiteer_members', $kommitee, 'DB insertion failed: failed to add new kommitte member in new_kommittee' );

    send_header('/panel/kommiteer?application=success');

  }

  elseif (isset($_POST['add_new_kommitte'])) {

    global $wpdb;

    $return = '/panel/kommiteer?add_new';

    $kommitte_name = test_input( $_POST['kommitte_name'] );
    $description = test_input( $_POST['description'] );
    // $chairman_name = test_input( $_POST['chairman_name'] );
    $chairman_id = check_number_value( test_input( $_POST['chairman_id'] ), $return);

    check_if_empty( array($kommitte_name, $description, $chairman_id), '/panel/kommiteer?add_new=empty');

    // Check if there already is a kommitée with that name
    check_if_entry_exists( 'vro_kommiteer', 'name', $kommitte_name, "/panel/kommiteer?add_new=nametaken&the_description=$description&chairman=$chairman_name");

    // Get the student with the supplied nickname
    // $student = get_studentshell_from_text($chairman_name, '/panel/kommiteer?add_new=nostudentfound');

    // Get student from chairman id
    $student = $wpdb->get_row("SELECT * FROM vro_users WHERE id = $chairman_id");
    if (!$student) {
      send_header( $return . '=noStudentFound' );
    }

    // Create a new array that will hold all the arguments to create a new kommitee
    $kommitte = array();

    $kommitte['name'] = $kommitte_name;
    $kommitte['description'] = $description;
    $kommitte['chairman'] = (int)$student->id;
    $kommitte['status'] = 'y';

    insert_record('vro_kommiteer', $kommitte, 'DB insertion failed: failed to add new kommitte in add_new_kommittee');

    // Logg action
    $log_description = 'Lade till kommittén ' . $kommitte['name'] . ' med beskrivningen ' . $kommitte['description'] . ' med ordförande ' . $chairman_name;
    add_log( 'Kommittéer', $log_description, get_current_user_id() );

    // Set the chairman as a new member
    $new_kommitee = $wpdb->get_row('SELECT * FROM vro_kommiteer WHERE name="'. $kommitte_name .'"');

    $kommitee_member = array();

    $kommitee_member['user_id'] = (int)$student->id;
    $kommitee_member['kommitee_id'] = (int)$new_kommitee->id;
    $kommitee_member['status'] = 'y';

    insert_record('vro_kommiteer_members', $kommitee_member, 'DB insertion failed: failed to add new chairman in new kommitté in add_new_kommittee');

    send_header('/panel/kommiteer?add_new=success');

  }

  elseif (isset($_POST['accept_kommitee']) || isset($_POST['deny_kommitee'])) {

    /*****************************************
    * Change status
    *****************************************/

    global $wpdb;

    // Check if accept button is pressed for a kommitée application
    if (isset($_POST['accept_kommitee']) && !empty($_POST['accept_kommitee'])){

      // Change the specified kommitée to official
      $wpdb->query( $wpdb->prepare('UPDATE vro_kommiteer SET status = "y" WHERE id = %s', $_POST['accept_kommitee']));

      // Send mail
      $kommitte = $wpdb->get_row('SELECT * FROM vro_kommiteer WHERE id = ' . $_POST['accept_kommitee']);

      $chairman = $wpdb->get_row("SELECT * FROM vro_users WHERE id = $kommitte->chairman");
      if ($chairman) {
        $answer = test_input( $_POST['komm_answer'] );
        if (empty($answer)) {
          $answer = 'Din kommittéansökan har godkänts.';
        }

        wp_mail( $chairman->email, 'Din kommittéansökan har godkänts!', $answer );
      }

      // Logg action
      $log_description = 'Accepterade kommittén ' . $kommitte->name;
      add_log( 'Kommittéer', $log_description, get_current_user_id() );

      // Redirect with success message
      send_header( '/panel/kommiteer?change=success' );

    }

    // Check if eny buttons are pressed
    elseif (isset($_POST['deny_kommitee']) && !empty($_POST['deny_kommitee']) ){

      // Change the specified kommitée to be denied
      $wpdb->query( $wpdb->prepare('UPDATE vro_kommiteer SET status = "n" WHERE id = %s', $_POST['deny_kommitee']));

      // Send mail
      $kommitte = $wpdb->get_var('SELECT chairman FROM vro_kommiteer WHERE id = ' . $_POST['deny_kommitee']);

      $chairman = $wpdb->get_row("SELECT * FROM vro_users WHERE id = $kommitte->chairman");
      if ($chairman) {
        $answer = test_input( $_POST['komm_answer'] );
        if (empty($answer)) {
          $answer = 'Din kommittéansökan har nekats.';
        }

        wp_mail( $chairman->email, 'Din kommittéansökan har nekats', $answer );
      }

      // Logg action
      $log_description = 'Nekade kommittén ' . $kommitte->name;
      add_log( 'Kommittéer', $log_description, get_current_user_id() );

      // Redirect with success message
      send_header('/panel/kommiteer?change=success');

    }

  }

  elseif (isset($_POST['accept_kommitee_member']) || isset($_POST['deny_kommitee_member'])) {

    /*****************************************
    * Change status
    *****************************************/
    global $wpdb;

    // Check kommité id
    $k_id = $_POST['kid'];

    $member_message = test_input( $_POST['kommitee_member_answer'] );

    $k_id = check_number_value( $k_id, '/panel/kommiteer?komitte_member' );

    // Get the kommitté name
    $kommitte_name = ($wpdb->get_row('SELECT * FROM vro_kommiteer WHERE id='. $k_id))->name;

    // Check if accept button is pressed for a kommitée application
    if (isset($_POST['accept_kommitee_member']) && !empty($_POST['accept_kommitee_member']) && isset($_POST['kid'])) {

      $u_id = $_POST['accept_kommitee_member'];

      $u_id = check_number_value( $u_id, "Location: /panel/kommiteer?k_id=$k_id&komitte_member" );

      // Change the specified wanting member to official member
      $accept_sql = 'UPDATE vro_kommiteer_members SET status = "y" WHERE kommitee_id= '. $k_id .' AND user_id = '. $u_id;
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
      $log_description = 'Accepterade kommittémedlemmen med id ' . $u_id . ' till kommittén ' . $kommitte_name;
      add_log( 'Kommittéer', $log_description, get_current_user_id() );

      // Redirect with success message
      send_header("/panel/kommiteer?k_id=$k_id&komitte_member=success");

    }

    // Check if eny buttons are pressed
    elseif (isset($_POST['deny_kommitee_member']) && !empty($_POST['deny_kommitee_member']) && isset($_POST['kid']) ){

      $u_id = $_POST['deny_kommitee_member'];

      $u_id = check_number_value( $u_id, "Location: /panel/kommiteer?k_id=$k_id&komitte_member" );

      // Change the specified wanting member to official member
      $deny_sql = 'UPDATE vro_kommiteer_members SET status = "n" WHERE kommitee_id= '. $k_id .' AND user_id = '. $u_id;
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
      $log_description = 'Nekade kommittémedlemmen med id ' . $u_id . ' till kommittén ' . $kommitte_name;
      add_log( 'Kommittéer', $log_description, get_current_user_id() );

      // Redirect with success message
      send_header("/panel/kommiteer?k_id=$k_id&komitte_member=success");

    }

  }

  elseif (isset($_POST['apply_for_kommitte'])){

    global $wpdb;

    // Get the id's
    $k_id = test_input( $_POST['kommitte_id'] );
    $student_id = test_input( $_POST['student_id'] );

    // INPUT VALIDATION
    $k_id = check_number_value( $k_id, '/panel/kommiteer?apply_kommitte');

    $student_id = check_number_value( $student_id, "/panel/kommiteer?k_id=$k_id&apply_kommitte" );

    // Check if this user already has sent an application
    if ( count($wpdb->get_results('SELECT * FROM vro_kommiteer_members WHERE user_id='. $student_id .' AND kommitee_id='. $k_id .'')) > 0 ) {
      header("Location: /panel/kommiteer?k_id=$k_id&apply_kommitte=alreadythere");
      exit();
    } else {

      // Insert an application
      $new_application = array();

      $new_application['user_id'] = $student_id;
      $new_application['kommitee_id'] = $k_id;
      $new_application['status'] = 'w';

      // Insert the new suggestion into the database
      insert_record('vro_kommiteer_members', $new_application, 'DB insertion failed: failed to add new kommitte member application in apply_for_kommitte');

      // Logg action
      $log_description = $student_id . ' skickade en medlemsansökan till kommittén med id ' . $k_id;
      add_log( 'Kommittéer', $log_description, get_current_user_id() );

      send_header( "/panel/kommiteer?k_id=$k_id&apply_kommitte=success" );

    }

  }

  elseif (isset($_POST['leave_kommitte'])){
    global $wpdb;

    // Get the id's
    $k_id = test_input( $_POST['kommitte_id'] );
    $student_id = test_input( $_POST['student_id'] );

    // INPUT VALIDATION
    $k_id = check_number_value( $k_id, "panel/kommiteer?leave_kommitte" );
    $student_id = check_number_value( $student_id, "Location: /panel/kommiteer?k_id=$k_id&leave_kommitte" );

    $kommitte = $wpdb->get_row('SELECT * FROM vro_kommiteer WHERE id='. $k_id );

    //Check if chairman
    if ($student_id == $kommitte->chairman) {
      send_header("/panel/kommiteer?k_id=$k_id&leave_kommitte=ischairman");
    }

    // Delete the student from the record
    $wpdb->delete( 'vro_kommiteer_members', array( 'kommitee_id' => $k_id, 'user_id' => $student_id ) );

    // Logg action
    $log_description = $student_id . ' lämnade kommittén med id ' . $k_id;
    add_log( 'Kommittéer', $log_description, get_current_user_id() );

    send_header("/panel/kommiteer?k_id=$k_id&leave_kommitte=success");
  }

  elseif (isset($_POST['change_kommitte_information'])){

    global $wpdb;

    $return = '/panel/kommiteer?alter_information';

    $k_id = test_input( $_POST['k_id'] );
    $k_id = check_number_value( $k_id, $return );

    $return = "/panel/kommiteer?k_id=$k_id&alter_information";

    $kommitte = $wpdb->get_row('SELECT * FROM vro_kommiteer WHERE id='. $k_id );

    $new_name = test_input( $_POST['kommitte_name'] );

    if (!empty($new_name) && $kommitte->name != $new_name) {
      // Check if new name exists¨
      check_if_entry_exists('vro_kommiteer', 'name', $new_name, $return);
      update_record( 'vro_kommiteer', 'name', $new_name, 'id', $k_id, "/panel/kommiteer?k_id=$k_id&alter_information=failed");

      // Logg action
      $log_description = 'Information av kommitté ' . $k_id . ' ändrades till namnet ' . $new_name;
      add_log( 'Kommittéer', $log_description, get_current_user_id() );
    }

    $new_description = test_input( $_POST['kommitee_description'] );

    if (!empty($new_description)) {

      // Update the kommitté with new information
      update_record( 'vro_kommiteer', 'description', $new_description, 'id', $k_id, "/panel/kommiteer?k_id=$k_id&alter_information=failed");

      // Logg action
      $log_description = 'Information av kommitté ' . $k_id . ' ändrade beskrivning till ' . $new_description;
      add_log( 'Kommittéer', $log_description, get_current_user_id() );
    }

    send_header( "/panel/kommiteer?k_id=$k_id&alter_information=success" );

  }

  elseif (isset($_POST['change_chairman'])){

    global $wpdb;

    $return = '/panel/kommiteer?alter_chairman';

    $k_id = test_input( $_POST['k_id'] );
    $new_chairman_name = test_input( $_POST['new_chairman_name'] );
    $new_chairman_id = test_input( $_POST['chairman_id'] );

    $k_id = check_number_value( $k_id, "/panel/kommiteer?alter_chairman" );
    $new_chairman_id = check_number_value( $new_chairman_id, "/panel/kommiteer?k_id=$k_id&alter_chairman" );

    // Get the student with the supplied nickname
    // $student = get_student_from_nickname( $new_chairman_name, "/panel/kommiteer?k_id=$k_id&alter_chairman=nostudentfound" );

    $student = $wpdb->get_row("SELECT * FROM vro_users WHERE id = $new_chairman_id");
    if (!$student) {
      send_header( $return . '=noStudentFound' );
    }

    // Check if student is member
    if (count($wpdb->get_results("SELECT * FROM vro_kommiteer_members WHERE kommitee_id = $k_id AND user_id = $new_chairman_id")) < 1) {
      // If not, make em member
      $new_member = array();
      $new_member['kommitee_id'] = $k_id;
      $new_member['user_id'] = $new_chairman_id;
      $new_member['status'] = 'y';
      insert_record( 'vro_kommiteer_members', $new_member, 'DB insertion failed: add chairman as member in change_chairman' );
    }

    update_record('vro_kommiteer', 'chairman', $student->id, 'id', $k_id, "/panel/kommiteer?k_id=$k_id&alter_chairman=failed");

    // Logg action
    $log_description = 'Ordförande för kommittén med id ' . $k_id . ' ändrades till ' . get_full_studentname( $student );
    add_log( 'Kommittéer', $log_description, get_current_user_id() );

    send_header( "/panel/kommiteer?k_id=$k_id&alter_chairman=success" );

  }

  elseif (isset($_POST['add_member'])) {

    global $wpdb;

    $return = '/panel/kommiteer?add_member';

    $student_id = test_input( $_POST['student_id'] );
    $k_id = test_input( $_POST['kommitte_id'] );

    // Check if the k_id was supplied
    $k_id = check_number_value( $k_id, "/panel/kommiteer?add_member");

    // CHeck if a student name was supplied
    check_if_empty( array($student_id), "/panel/kommiteer?k_id=$k_id&add_member=nostudent" );

    if (!check_if_entry_exists('vro_kommiteer', 'id', $k_id)) {
      send_header("/panel/kommiteer?k_id=$k_id&add_member=nokommittefound");
    }

    // Get the student with the supplied nickname
    // $student = get_student_from_nickname($student_name, "/panel/kommiteer?k_id=$k_id&add_member=nostudentfound");
    $student = $wpdb->get_row("SELECT * FROM vro_users WHERE id = $student_id");
    if (!$student) {
      send_header( $return . '=noStudentFound' );
    }

    // Check if student already exists
    if (count($wpdb->get_results('SELECT * FROM vro_kommiteer_members WHERE kommitee_id = ' . $k_id . ' AND user_id = '. $student->id)) > 0){
      header("Location: /panel/kommiteer?k_id=$k_id&add_member=studentalreadyadded");
      exit();
    }

    // Add student to kommitté
    $new_member = array();

    $new_member['user_id'] = $student->id;
    $new_member['kommitee_id'] = $k_id;
    $new_member['status'] = 'y';

    // Insert the new suggestion into the database
    insert_record('vro_kommiteer_members', $new_member, "DB insertion failed: failed to add new student in add_member");

    // Logg action
    $log_description = $student->id . ' lades till i kommittén ' . $k_id;
    add_log( 'Kommittéer', $log_description, get_current_user_id() );

    send_header("/panel/kommiteer?k_id=$k_id&add_member=success");

  }

  elseif ( isset($_POST['remove_kommitte']) ){

    $k_id = $_POST['k_id'];
    $k_id = check_number_value( $k_id, '"/panel/kommiteer?remove_kommitte' );

    // Remoe all student records in the kommitt+r
    remove_record( 'vro_kommiteer_members', 'kommitee_id', $k_id, 'DB deletion failed: failed to remove member in remove_kommitte' );

    // Remove the actual kommitt+e
    remove_record( 'vro_kommiteer', 'id', $k_id, 'DB deletion failed: failed to remove kommitte in remove_kommitte' );

    // Duccess!
    send_header("/panel/kommiteer?remove_kommitte=success");

  }

  elseif ( isset($_POST['update_information']) ){

    $k_id = $_POST['k_id'];
    $k_id = check_number_value( $k_id, '"/panel/kommiteer?update_information' );

    $return = "/panel/kommiteer?k_id=$k_id&update_information=";

    $information = test_input( $_POST['information'] );
    $information = ($information != NULL) ? str_replace("\n", "<br>",trim($information)) : NULL;

    update_record( 'vro_kommiteer', 'information', $information, 'id', $k_id, $return . "failed");

    // Duccess!
    send_header($return . "success");

  }

  else {
    send_header("/panel/kommiteer?kommitte=error");
  } // End post
