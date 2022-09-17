<?php

// Include wp_core
require_once(explode("wp-content", __FILE__)[0] . "wp-load.php");

// Include helpful functions
include_once('helpful_functions.php');

// Create database entry
if (isset($_POST['toggle_member'])) {

  global $wpdb;

  $return = 'Location: /panel/medlemmar?toggle_member=';

  $class_id = check_number_value( test_input( $_POST['c_id'] ), $return . 'badClassId');
  $student_id = check_number_value( test_input( $_POST['toggle_member'] ), $return . "badStudentId&c_id=$class_id");

  $student = $wpdb->get_row("SELECT * FROM vro_users WHERE id = $student_id");

  if ($student == NULL) {
    send_header( $return . 'nouser' );
  }

  $new_status = ($student->status == 'y') ? 'n' : 'y';

  update_record( 'vro_users', 'status', $new_status, 'id', $student->id, 'failedChangeStatus in handlemembers toggle member' );

  // Set date entered if not already set
  if ( $new_status == 'y' && $student->date_member == NULL ) {
    $date = date("Y-m-d H:i:s");
    update_record( 'vro_users', 'date_member', $date, 'id', $student->id, 'failedChangedatemember in handlemembers toggle member' );

    $log_description = 'Satte ingångsdatum för ' . $student_id . ' till ' . $date;
    add_log( 'Medlemmar', $log_description, get_current_user_id() );
  }

  // Success!

  // Logg action
  $log_description = 'Ändrade eleven ' . $student_id . ' medlemsstatus till ' . $new_status;
  add_log( 'Medlemmar', $log_description, get_current_user_id() );

  header("Location: /panel/medlemmar/?c_id=$class_id&toggle_member=success");
  exit();

}
// ADD NEW USER
elseif (isset($_POST['add_new_user'])) {
  global $wpdb;

  $first_name = test_input( $_POST['first_name'] );
  $last_name = test_input( $_POST['last_name'] );
  $email_address = $_POST['email_address'];
  $class_name = test_input( $_POST['class_name'] );
  $phonenumber = test_input( $_POST['phonenumber'] );

  // $end_year = test_input ( $_POST['end_year'] );
  $password = $_POST['password'];
  $class_id = $_POST['class_id'];

  // INPUT VALIDATION

  // Check if a class id or a class name has been supplied
  if (isset($class_id)) {
    if (!is_numeric($class_id)) {
      header("Location: /panel/medlemmar?add_user=nan");
      exit();
    }
    // Get class with id
    $class = $wpdb->get_row('SELECT * FROM vro_classes WHERE id=' . (int)$class_id);
    $class_name = $class->name;

  } elseif (isset($class_name)) {

    // Make sure the first letter i capitilized
    $class_name = ucfirst( strtolower( $class_name ) );

    // Get class with name
    $class = $wpdb->get_row('SELECT * FROM vro_classes WHERE name="' . $class_name . '"');

  } else {
    // No class name or id supplied
    header("Location: /panel/medlemmar?add_user=noclass");
    exit();
  }

  // Check if a class was found with the given class id or class name
  if (!$class) {
    header("Location: /panel/medlemmar?c_id=$class_id&add_user=noclassfound");
    exit();
  }

  if (!isset($first_name) or !isset($last_name) or !isset($email_address) or !isset($password) or !isset($phonenumber)){
    header("Location: /panel/medlemmar?c_id=$class_id&add_user=empty");
    exit();
  }

  // Check valid mail
  if (!filter_var($email_address, FILTER_VALIDATE_EMAIL)) {
    header("Location: /panel/medlemmar?c_id=$class_id&add_user=invalidemail&first_name=$first_name&last_name=$last_name");
    exit();
  }

  // Check mail ends with vrg.se
  if (! (substr($email_address, -6) == 'vrg.se')){
    header("Location: /panel/medlemmar?c_id=$class_id&add_user=invalidemail&first_name=$first_name&last_name=$last_name");
    exit();
  }

  // IF INPUT END YEAR SELF
  // CHeck the end year is numeric
  // if (!is_numeric($end_year)) {
  //   header("Location: /panel/medlemmar?c_id=$class_id&add_user=invalidyear&first_name=$first_name&last_name=$last_name&email=$email_address");
  //   exit();
  // }
  //
  // // Convert just in case
  // $end_year = (int)$end_year;
  //
  // // Check it is 4 digits
  // if ($end_year < 999 or $end_year > 9999){
  //   header("Location: /panel/medlemmar?c_id=$class_id&add_user=invalidyear&first_name=$first_name&last_name=$last_name");
  //   exit();
  // }

  // Get the end year from the class name
  $yearFromClassName = substr($class_name, 2, 2);

  // Get the year in 4-digit form
  $yearFromClassName = date_create_from_format('y', $yearFromClassName);
  $yearFromClassName = $yearFromClassName->format('Y');

  if (!is_numeric($yearFromClassName)){
    header("Location: /panel/medlemmar?c_id=$class_id&add_user=noyearfound&first_name=$first_name&last_name=$last_name");
    exit();
  }

  // Convert to int
  $end_year = (int)$yearFromClassName;

  // Check it is 4 digits
  if ($end_year < 999 or $end_year > 9999){
    header("Location: /panel/medlemmar?c_id=$class_id&add_user=invalidyear&first_name=$first_name&last_name=$last_name");
    exit();
  }

  if (username_exists( $email_address )){
    // Send error header
    header("Location: /panel/medlemmar?c_id=$class_id&add_user=emailexists");
    exit();
  } else {
    // Generate a 15 character long password with special characters
    // $password = wp_generate_password(16, true);

    $user_id = wp_create_user($email_address, $password, $email_address);

    wp_update_user(
      array(
        'ID'       => $user_id,
        'nickname' => $first_name . ' ' . $last_name
      )
    );

    // Default to not waiting member in the elevkår
    add_user_meta( $user_id, 'status', 'w' );

    // Set the class for the user
    add_user_meta( $user_id, 'class_id', $class->id );

    // Set the end year
    add_user_meta( $user_id, 'end_year', $end_year );

    // Set the phonenumber
    add_user_meta( $user_id, 'phonenumber', $phonenumber );

    // Set user role
    $user = new WP_User( $user_id );
    $user->set_role( 'subscriber' );

    // Mail the user
    // wp_mail( $email_address, 'Välkommen till Viktor Rydberg Odenplans hemsida!', 'Ditt lösenord är: ' . $password . '. Logga in för att ändra lösenordet.' );
    wp_mail( $email_address, 'Välkommen till Viktor Rydberg Odenplans hemsida!', 'Hej '. $first_name .'! Välkommen till Viktor Rydbergs Odenplans hemsida! Gå in på vroelevkar.se för att se matsedeln, ansöka till kommittéer och mycket mer!' );

    // Logg action
    $log_description = 'Lade till eleven ' . $first_name . ' ' . $last_name;
    add_log( 'Medlemmar', $log_description, get_current_user_id() );

    //Success!
    header("Location: /panel/medlemmar?c_id=$class->id&add_user=success");
    exit();
  }

}

elseif (isset($_POST['register_new_user'])) {
  global $wpdb;

  $first_name = test_input( $_POST['first_name'] );
  $last_name = test_input( $_POST['last_name'] );
  $email_address = $_POST['email_address'];
  $class_name = test_input( $_POST['class_name'] );
  $phonenumber = test_input( $_POST['phonenumber'] );
  // $end_year = test_input ( $_POST['end_year'] );
  $password = $_POST['password'];

  // INPUT VALIDATION

  // Check if a class id or a class name has been supplied
  if (isset($class_name)) {

    // Make sure the first letter i capitilized
    $class_name = ucfirst( strtolower( $class_name ) );

    // Get class with name
    $class = $wpdb->get_row('SELECT * FROM vro_classes WHERE name="' . $class_name . '"');

  } else {
    // No class name or id supplied
    header("Location: /register?add_user=noclass");
    exit();
  }

  // Check if a class was found with the given class id or class name
  if (!$class) {
    header("Location: /register?add_user=noclassfound");
    exit();
  }

  if (!isset($first_name) or !isset($last_name) or !isset($email_address) or !isset($password) or !isset($phonenumber) ){
    header("Location: /register?class_name=$class_name&add_user=empty");
    exit();
  }

  // Check valid mail
  if (!filter_var($email_address, FILTER_VALIDATE_EMAIL)) {
    header("Location: /register?class_name=$class_name&add_user=invalidemail&first_name=$first_name&last_name=$last_name");
    exit();
  }

  // Check mail ends with vrg.se
  if (! (substr($email_address, -6) == 'vrg.se')){
    header("Location: /register?class_name=$class_name&add_user=invalidemail&first_name=$first_name&last_name=$last_name");
    exit();
  }

  // IF INPUT END YEAR SELF
  // CHeck the end year is numeric
  // if (!is_numeric($end_year)) {
  //   header("Location: /register?class_name=$class_name&add_user=invalidyear&first_name=$first_name&last_name=$last_name&email=$email_address");
  //   exit();
  // }
  //
  // // Convert just in case
  // $end_year = (int)$end_year;
  //
  // // Check it is 4 digits
  // if ($end_year < 999 or $end_year > 9999){
  //   header("Location: /register?class_name=$class_name&add_user=invalidyear&first_name=$first_name&last_name=$last_name");
  //   exit();
  // }

  // Get the end year from the class name
  $yearFromClassName = substr($class_name, 2, 2);

  // Get the year in 4-digit form
  $yearFromClassName = date_create_from_format('y', $yearFromClassName);
  $yearFromClassName = $yearFromClassName->format('Y');

  if (!is_numeric($yearFromClassName)){
    header("Location: /register?class_name=$class_name&add_user=noyearfound&first_name=$first_name&last_name=$last_name");
    exit();
  }

  // Convert to int
  $end_year = (int)$yearFromClassName;

  // Check it is 4 digits
  if ($end_year < 999 or $end_year > 9999){
    header("Location: /register?class_name=$class_name&add_user=invalidyear&first_name=$first_name&last_name=$last_name");
    exit();
  }

  if (username_exists( $email_address )){
    // Send error header
    header("Location: /register?class_name=$class_name&add_user=emailexists");
    exit();
  } else {
    // Generate a 15 character long password with special characters
    // $password = wp_generate_password(16, true);

    $user_id = wp_create_user($email_address, $password, $email_address);

    wp_update_user(
      array(
        'ID'       => $user_id,
        'nickname' => $first_name . ' ' . $last_name
      )
    );

    // Default to waiting member in the elevkår
    add_user_meta( $user_id, 'status', 'w' );

    // Set the class for the user
    add_user_meta( $user_id, 'class_id', $class->id );

    // Set the end year
    add_user_meta( $user_id, 'end_year', $end_year );

    // Set the phonenumber
    add_user_meta( $user_id, 'phonenumber', $phonenumber );

    // Set user role
    $user = new WP_User( $user_id );
    $user->set_role( 'subscriber' );

    // Mail the user
    // wp_mail( $email_address, 'Välkommen till Viktor Rydberg Odenplans hemsida!', 'Ditt lösenord är: ' . $password . '. Logga in för att ändra lösenordet.' );
    wp_mail( $email_address, 'Välkommen till Viktor Rydberg Odenplans hemsida!', 'Hej '. $first_name .'! Välkommen till Viktor Rydbergs Odenplans hemsida! Gå in på vroelevkar.se för att se matsedeln, ansöka till kommittéer och mycket mer!' );

    // Login user
    wp_set_current_user( $user_id, $user->user_login );
    wp_set_auth_cookie( $user_id );
    do_action( 'wp_login', $user->user_login );

    // Logg action
    $log_description = 'Registrerade eleven ' . $first_name . ' ' . $last_name;
    add_log( 'Medlemmar', $log_description, get_current_user_id() );

    //Success!
    header("Location: /panel/dashboard?register=success");
    exit();
  }

}

elseif (isset($_POST['link_new_user'])) {

  global $wpdb;

  $return = '/register?add_user';
  $success_return = '/panel/dashboard?register=success';

  // Get the values
  $email_address = check_post( $_POST['email_address'], $return . '=empty' );
  $phonenumber = check_post( $_POST['phonenumber'], $return . "=empty&email=$email_address" );
  $password = $_POST['password'];
  check_if_empty( array($password), $return . "=empty&email=$email_address&phonenumber=$phonenumber" );

  // Check if a studenshell has been created
  $studentshell = $wpdb->get_row( "SELECT * FROM vro_users WHERE email = '$email_address'" );

  // No studentshell was found, send back error
  if ($studentshell == NULL) {
    send_header( $return . "=nostudentshell&phonenumber=$phonenumber" );
  }

  // Check if date entered has been set
  if ($studentshell->date_member == NULL) {
    // If not, set date entered and welcome to kåren

    // Update studentshell with current timestamp
    $date = date("Y-m-d H:i:s");
    update_record( 'vro_users', 'date_member', $date, 'id', $studentshell->id, $return . '=studentshelldateerror'  );

    // Set status to waiting
    update_record( 'vro_users', 'status', 'w', 'id', $studentshell->id, $return . '=studentShellFailedSetStatus' );

  } else {

    // Only do this to waiting and no members
    if ($studentshell->status != 'y') {

      // NOTE:
      // Set status to waiting OR YES HERE

      // If reregister or studentshell already set to member - set to yes. If registering first time - set to waiting
      $new_register_status = ($studentshell->wpuser_id != NULL || $studentshell->status == 'y') ? 'y' : 'w';

      // Check if already correct status
      if ($studentshell->status != $new_register_status) {
        update_record( 'vro_users', 'status', $new_register_status, 'id', $studentshell->id, $return . '=studentShellFailedSetStatus' );
      }

    }

  }

  // CHeck if there already exists a user with the student wpuser_id
  if ($studentshell->wpuser_id == NULL) {

    // Set phonenumber
    update_record( 'vro_users', 'phonenumber', $phonenumber, 'id', $studentshell->id, $return . '=failedSetPhonenumber' );

    // Create a new wp_user
    $user_id = wp_create_user($email_address, $password, $email_address);

    wp_update_user(
      array(
        'ID'       => $user_id,
        'nickname' => $studentshell->first_name. ' ' . $studentshell->last_name
      )
    );

    // Add studentshell_id as meta
    add_user_meta( $user_id, 'studentshell_id', $studentshell->id );

    // Set user role
    $user = new WP_User( $user_id );
    $user->set_role( 'subscriber' );

    // Log them in
    wp_set_current_user( $user_id, $user->user_login );
    wp_set_auth_cookie( $user_id );
    do_action( 'wp_login', $user->user_login );

    // Logg action
    $log_description = 'Registrerade eleven ' . $studentshell->first_name . ' ' . $studentshell->$last_name;
    add_log( 'Medlemmar', $log_description, get_current_user_id() );

    // Set studentshell wpuser_id
    update_record( 'vro_users', 'wpuser_id', $user_id, 'id', $studentshell->id, $return . '=studentshellwpiderror'  );

  }
  else {
    // NOTE: UPDATE OR CHECK PASSWORD?

    // CHECK PASSWORD
    if (user_pass_ok( $email_address, $password ) == false) {
      send_header($return . "=InvalidEmailOrPassword&email=$email_address&phonenumber=$phonenumber");
    }

    // NEW PASSWORD
    // wp_set_password( $password, $studentshell->wpuser_id );

    // Update phonenumber
    update_record( 'vro_users', 'phonenumber', $phonenumber, 'id', $studentshell->id, $return . '=failedUpdatePhonenumber' );

    // Log em in
    $wp_user = get_user_by('id', $studentshell->wpuser_id);
    wp_set_current_user( $studentshell->wpuser_id, $wp_user->user_login );
    wp_set_auth_cookie( $studentshell->wpuser_id );
    do_action( 'wp_login', $wp_user->user_login );

    // Logg action
    $log_description = 'Återregistrerade eleven ' . $first_name . ' ' . $last_name;
    add_log( 'Medlemmar', $log_description, get_current_user_id() );

    // Change successreturn to updated
    $success_return = '/panel/dashboard?reregister=success';
  }

  // Send to dashboard. Success!
  send_header( $success_return );

}

elseif (isset($_POST['add-game-user'])) {

  global $wpdb;

  $return = '/register-gamer?add_user';
  $success_return = '/game';

  // Get the values
  $email_address = check_post( $_POST['email_address'], $return . '=empty' );
  $password = $_POST['password'];
  check_if_empty( array($password), $return . "=empty&email=$email_address" );

  // Create a new wp_user
  $user_id = wp_create_user($email_address, $password, $email_address);

  wp_update_user(
    array(
      'ID'       => $user_id,
      'nickname' => $email_address
    )
  );

  // Set user role
  $user = new WP_User( $user_id );
  $user->set_role( 'gamer' );

  // Log them in
  wp_set_current_user( $user_id, $user->user_login );
  wp_set_auth_cookie( $user_id );
  do_action( 'wp_login', $user->user_login );

  // Logg action
  $log_description = 'Registrerade spelkontot med mailen ' . $email_address;
  add_log( 'Medlemmar', $log_description, get_current_user_id() );

  // Send to dashboard. Success!
  send_header( $success_return );

}

elseif(isset($_POST['quit_being_member'])) {

  $return = 'Location: /panel/medlemmar?quitmember=';

  // Get the user id
  $u_id = test_input( $_POST['student_id'] );

  if (empty($u_id) or !is_numeric($u_id)){
    header("Location: /panel/medlemmar?quitmember=baduid");
    exit();
  }

  $u_id = (int)$u_id;

  $member_answer = test_input( $_POST['member_answer'] );

  // Update their status to n, so they are not part of elevkåren any longer
  update_record('vro_users', 'status', 'n', 'id', $u_id, $return . 'updateFailed');

  $student = $wpdb->get_row("SELECT * FROM vro_users WHERE id = $u_id");
  if ($student->email) {
    wp_mail( $student->email, 'Din medlemsansökan har nekats', $member_answer);
  }

  // Logg action
  $log_description = 'Eleven ' . $u_id . ' skickade en gåuturkåren-ansökan';
  add_log( 'Medlemmar', $log_description, get_current_user_id() );

  header("Location: /panel/medlemmar?quitmember=success");
  exit();

}

elseif(isset($_POST['apply_for_member'])) {

  $return = '/panel/medlemmar?applymember=';

  // Get the user id
  $u_id = test_input( $_POST['student_id'] );

  if (empty($u_id) or !is_numeric($u_id)){
    header("Location: /panel/medlemmar?applymember=baduid");
    exit();
  }

  $u_id = (int)$u_id;

  update_record('vro_users', 'status', 'w', 'id', $u_id, $return . 'updateFailed');

  // Logg action
  $log_description = 'Eleven ' . $u_id . ' skickade en gåMedIKåren-ansökan';
  add_log( 'Medlemmar', $log_description, get_current_user_id() );

  header("Location: /panel/medlemmar?applymember=success");
  exit();

}

elseif(isset($_POST['accept_member'])) {

  $return = '/panel/medlemmar?acceptmember=';

  // Get the user id
  $u_id = test_input( $_POST['student_id'] );

  if (empty($u_id) or !is_numeric($u_id)){
    header("Location: /panel/medlemmar?applymember=baduid");
    exit();
  }

  $u_id = (int)$u_id;

  $member_answer = test_input( $_POST['member_answer'] );

  update_record('vro_users', 'status', 'y', 'id', $u_id, $return . 'acceptFailed');

  if (!empty($member_answer)){
    if ($student = $wpdb->get_row("SELECT * FROM vro_users WHERE id = $u_id")) {
      // Send the mail
      if ($student->email) {
        wp_mail( $student->email, 'Din medlemsansökan har godkänts', $member_answer);
      }
    }
  }

    // Logg action
    $log_description = 'Eleven ' . $u_id . ' medlemsansökan accepterades';
    add_log( 'Medlemmar', $log_description, get_current_user_id() );

    header("Location: /panel/medlemmar?acceptmember=success");
    exit();

}

elseif(isset($_POST['add_studentshell'])) {

  global $wpdb;

  $return = '/panel/medlemmar?new_studentshell';

  // Required meta
  $first = check_post( $_POST['first-name'], $return . '=empty');
  $last = check_post( $_POST['last-name'], $return . '=empty');
  $email = check_post( $_POST['email'], $return . '=empty');
  $class_name = check_post( $_POST['class-name'], $return . '=empty');
  $program = check_post( $_POST['program'], $return . '=empty');

  // Optional meta, if not supplied --> set to null
  $phonenumber = emptyToNull( test_input( $_POST['phonenumber'] ) );
  $birthyear = emptyToNull( test_input( $_POST['birthyear'] ) );
  $gender = emptyToNull( test_input( $_POST['gender'] ) );
  $registered_city = emptyToNull( test_input( $_POST['registered-city'] ) );

  // Get class_id from name
  $class_id = get_classid_from( $class_name );
  // Check if a class id was found
  if ($class_id == NULL) {
    send_header( $return . '=noclassid' );
  }

  // Get endyear
  $end_year = get_endyear_from( $class_name );
  if ($end_year == NULL) {
    send_header( $return . '=noendyear' );
  }

  // Start creating
  $new_studentshell = array();
  $new_studentshell['first_name'] = $first;
  $new_studentshell['last_name'] = $last;
  $new_studentshell['birthyear'] = $birthyear;
  $new_studentshell['gender'] = $gender;
  $new_studentshell['registered_city'] = $registered_city;
  $new_studentshell['phonenumber'] = $phonenumber;
  $new_studentshell['email'] = $email;
  $new_studentshell['program'] = $program;
  $new_studentshell['end_year'] = $end_year;
  $new_studentshell['class_id'] = $class_id;

  // Insert the new studentshell into the database
  insert_record('vro_users', $new_studentshell, 'DB insertion failed in add_studentshell');

  // Logg action
  $log_description = 'Lade till elevskalet för ' . $first . ' ' . $last;
  add_log( 'Elev', $log_description, get_current_user_id() );

  send_header($return . '=success');

}

elseif (isset($_POST['reset-members-new-term'])) {

  global $wpdb;

  $return = '/panel/medlemmar?reset-members=';

  // Set all userstatuses to default status
  $default_status = 'n';

  // Get all students
  $all_students = $wpdb->get_results("SELECT * FROM vro_users");

  // Go through every student
  foreach ($all_students as $s) {

    // Update status
    if ($s->status != $default_status) {
      // NOTE: CHANGE TO SILENT WARNING
      update_record('vro_users', 'status', $default_status, 'id', $s->id, $return . "changeStatusFailedFor$s->id");
    }

  }

  // Success!
  send_header( $return . 'success' );

}

elseif (isset($_POST['update-student-information'])) {

  global $wpdb;

  $c_id = check_number_value( $_POST['class-id'], '/panel/medlemmar?update-student=noClassId' );
  $return = "/panel/medlemmar?c_id=$c_id&update-student";

  $student_id = check_number_value( $_POST['the-student-id'], $return );
  $student = get_student_by_id( $student_id );
  if (!$student) {
    send_header( $return . 'noStudentFound' );
  }

  $info = array();

  // Default empty to null
  $info['firstname'] = test_input( $_POST['new-first-name'], $return . '=empty' );
  $info['lastname'] = test_input( $_POST['new-last-name'], $return . '=empty' );
  $info['phonenumber'] = emptyToNull( test_input( $_POST['new-phonenumber'], $return . '=empty' ) );
  $info['schoolmail'] = test_input( $_POST['new-schoolmail'], $return . '=empty' );
  $info['birthyear'] = emptyToNull( $_POST['new-birthyear'], $return . '=empty' );
  $info['gender'] = emptyToNull( test_input( $_POST['new-gender'], $return . '=empty' ) );
  $info['city'] = emptyToNull( test_input( $_POST['new-city'], $return . '=empty' ) );
  $info['program'] = emptyToNull( test_input( $_POST['new-program'], $return . '=empty' ) );



  // Check if birthyear is number
  if ($info['birthyear'] != NULL && !is_numeric($info['birthyear'])) {
    send_header($return . '=nanBirthyear');
  }

  if ($info['birthyear'] != NULL) {
    // Set birthyear as number
    $info['birthyear'] = (int)$info['birthyear'];
  }

  // Check if value is deviant, only then update it
  if ($student->first_name != $info['firstname']) {
    update_record( 'vro_users', 'first_name', $info['firstname'], 'id', $student_id, $return . '=notUpdateFirstName' );
  }

  if ($student->last_name != $info['lastname']) {
    update_record( 'vro_users', 'last_name', $info['lastname'], 'id', $student_id, $return . '=notUpdateLastName' );
  }

  if ($student->phonenumber != $info['phonenumber']) {
    if ($info['phonenumber'] == NULL) {
      update_with_null( 'vro_users', 'phonenumber', $student_id, $return . '=notUpdatePhonenumber' );
    } else {
      update_record( 'vro_users', 'phonenumber', $info['phonenumber'], 'id', $student_id, $return . '=notUpdatePhonenumber' );
    }
  }

  if ($student->email != $info['schoolmail']) {
    update_record( 'vro_users', 'email', $info['schoolmail'], 'id', $student_id, $return . '=notUpdateEmail' );
  }

  if ($student->birthyear != $info['birthyear']) {
    if ($info['birthyear'] == NULL) {
      update_with_null( 'vro_users', 'birthyear', $student_id, $return . '=notUpdateBirthyear' );
    } else {
      update_record( 'vro_users', 'birthyear', $info['birthyear'], 'id', $student_id, $return . '=notUpdateBirthyear' );
    }
  }

  if ($student->gender != $info['gender']) {
    if ($info['gender'] == NULL) {
      update_with_null( 'vro_users', 'gender', $student_id, $return . '=notUpdateGender' );
    } else {
      update_record( 'vro_users', 'gender', $info['gender'], 'id', $student_id, $return . '=notUpdateGender' );
    }
  }

  if ($student->registered_city != $info['city']) {
    if ($info['city'] == NULL) {
      update_with_null( 'vro_users', 'registered_city', $student_id, $return . '=notUpdateRegisteredCity' );
    } else {
      update_record( 'vro_users', 'registered_city', $info['city'], 'id', $student_id, $return . '=notUpdateRegisteredCity' );
    }
  }

  if ($student->program != $info['program']) {
    update_record( 'vro_users', 'program', $info['program'], 'id', $student_id, $return . '=notUpdateProgram' );

    if ($info['program'] == NULL) {
      update_with_null( 'vro_users', 'program', $student_id, $return . '=notUpdateProgram' );
    } else {
      update_record( 'vro_users', 'program', $info['program'], 'id', $student_id, $return . '=notUpdateProgram' );
    }
  }
  // Success!
  send_header( $return . '=success' );
}

elseif (isset($_POST['remove-student'])) {

  global $wpdb;

  $c_id = check_number_value( $_POST['class-id'], '/panel/medlemmar?remove-student=noClassId' );
  $return = "/panel/medlemmar?c_id=$c_id&remove-student";

  $student_id = check_number_value( $_POST['the-student-id'], $return );
  $student = get_student_by_id( $student_id );
  if (!$student) {
    send_header( $return . 'noStudentFound' );
  }

  // Delete student
  remove_record( 'vro_users', 'id', $student_id, $return . '=couldNotDelete' );

  // Success
  send_header( $return . 'success' );

}

elseif(isset($_Post['register_new_user_student_complete'])) {
  // kod från add_studentshell

  global $wpdb;

  $return = '/register?new_student';

  // Required meta
  $first = check_post( $_POST['first-name'], $return . '=empty');
  $last = check_post( $_POST['last-name'], $return . '=empty');
  $email = check_post( $_POST['email'], $return . '=empty');
  $class_name = check_post( $_POST['class-name'], $return . '=empty');
  $program = check_post( $_POST['program'], $return . '=empty');

  // Optional meta, if not supplied --> set to null
  $phonenumber = emptyToNull( test_input( $_POST['phonenumber'] ) );
  $birthyear = emptyToNull( test_input( $_POST['birthyear'] ) );
  $gender = emptyToNull( test_input( $_POST['gender'] ) );
  $registered_city = emptyToNull( test_input( $_POST['registered-city'] ) );

  // Get class_id from name
  $class_id = get_classid_from( $class_name );
  // Check if a class id was found
  if ($class_id == NULL) {
    send_header( $return . '=noclassid' );
  }

  // Get endyear
  $end_year = get_endyear_from( $class_name );
  if ($end_year == NULL) {
    send_header( $return . '=noendyear' );
  }

  // Start creating
  $new_studentshell = array();
  $new_studentshell['first_name'] = $first;
  $new_studentshell['last_name'] = $last;
  $new_studentshell['birthyear'] = $birthyear;
  $new_studentshell['gender'] = $gender;
  $new_studentshell['registered_city'] = $registered_city;
  $new_studentshell['phonenumber'] = $phonenumber;
  $new_studentshell['email'] = $email;
  $new_studentshell['program'] = $program;
  $password = $_POST['password'];
  $new_studentshell['end_year'] = $end_year;
  $new_studentshell['class_id'] = $class_id;

  // Insert the new studentshell into the database
  insert_record('vro_users', $new_studentshell, 'DB insertion failed in add_studentshell');

  // Logg action
  $log_description = 'Lade till elevskalet för ' . $first . ' ' . $last;
  add_log( 'Elev', $log_description, get_current_user_id() );

  // kod från register new_user
  // Check if a studenshell has been created
  $studentshell = $wpdb->get_row( "SELECT * FROM vro_users WHERE email = '$email_address'" );
// No studentshell was found, send back error
if ($studentshell == NULL) {
  send_header( $return . "=nostudentshell&phonenumber=$phonenumber" );
}

// Check if date entered has been set
if ($studentshell->date_member == NULL) {
  // If not, set date entered and welcome to kåren

  // Update studentshell with current timestamp
  $date = date("Y-m-d H:i:s");
  update_record( 'vro_users', 'date_member', $date, 'id', $studentshell->id, $return . '=studentshelldateerror'  );

  // Set status to waiting
  update_record( 'vro_users', 'status', 'w', 'id', $studentshell->id, $return . '=studentShellFailedSetStatus' );

} else }

  // Only do this to waiting and no members
  if ($studentshell->status != 'y') {

    // NOTE:
    // Set status to waiting OR YES HERE

    // If reregister or studentshell already set to member - set to yes. If registering first time - set to waiting
    $new_register_status = ($studentshell->wpuser_id != NULL || $studentshell->status == 'y') ? 'y' : 'w';

    // Check if already correct status
    if ($studentshell->status != $new_register_status) {
      update_record( 'vro_users', 'status', $new_register_status, 'id', $studentshell->id, $return . '=studentShellFailedSetStatus' );
    }

  }


$user_id = wp_create_user($email_address, $password, $email_address);

    wp_update_user(
      array(
        'ID'       => $user_id,
        'nickname' => $first_name . ' ' . $last_name
      )
    );

    // Default to waiting member in the elevkår
    add_user_meta( $user_id, 'status', 'w' );

    // Set the class for the user
    add_user_meta( $user_id, 'class_id', $class->id );

    // Set the end year
    add_user_meta( $user_id, 'end_year', $end_year );

    // Set the phonenumber
    add_user_meta( $user_id, 'phonenumber', $phonenumber );

    // Set user role
    $user = new WP_User( $user_id );
    $user->set_role( 'subscriber' );

    // Mail the user
    // wp_mail( $email_address, 'Välkommen till Viktor Rydberg Odenplans hemsida!', 'Ditt lösenord är: ' . $password . '. Logga in för att ändra lösenordet.' );
    wp_mail( $email_address, 'Välkommen till Viktor Rydberg Odenplans hemsida!', 'Hej '. $first_name .'! Välkommen till Viktor Rydbergs Odenplans hemsida! Gå in på vroelevkar.se för att se matsedeln, ansöka till kommittéer och mycket mer!' );

    // Login user
    wp_set_current_user( $user_id, $user->user_login );
    wp_set_auth_cookie( $user_id );
    do_action( 'wp_login', $user->user_login );

    // Logg action
    $log_description = 'Registrerade eleven ' . $first_name . ' ' . $last_name;
    add_log( 'Medlemmar', $log_description, get_current_user_id() );

    //Success!
    header("Location: /panel/dashboard?register=success");
    exit();


  // Kod från register new user

else {
  header("Location: /panel/medlemmar");
  exit();
} // End post
