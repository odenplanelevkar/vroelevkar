<?php

// Include wp_core
require_once(explode("wp-content", __FILE__)[0] . "wp-load.php");

// Include helpful functions
include_once('helpful_functions.php');

// Create database entry
if (isset($_POST['new_visselpipa'])) {

  global $wpdb;

  $subject = test_input( $_POST['subject'] );
  $text = test_input( $_POST['text'] );

  if ( empty($subject) || empty($text) ){
    header("Location: /panel/visselpipan?visselpipa=empty");
    exit();
  } else {

    // Create a new array that will hold all the arguments to create a new visselpipan suggestion
    $suggestion = array();

    $suggestion['user_id'] = get_current_user_id();
    $suggestion['subject'] = $subject;
    $suggestion['text'] = $text;

    // Insert the new suggestion into the database
    if($wpdb->insert(
        'vro_visselpipan',
        $suggestion
    ) == false) {
      send_error( '/panel/visselpipan?visselpipa', 'Det gick inte att skicka visselpipan.' );
      // wp_die('database insertion failed');
    }

    // Logg action
    $log_description = 'Lade till visselpipan med titeln ' . $subject . ' och texten ' . substr($text, 0, 280);
    add_log( 'Visselpipan', $log_description, get_current_user_id() );

    header("Location: /panel/visselpipan?visselpipa=success");
    exit();

  }

} elseif (isset($_POST['answerVisselpipa'])) {

  // w - waiting
  // a - archive

  global $wpdb;

  $visselpipa_id = test_input( $_POST['visselpipaId'] );
  $answer = test_input( $_POST['visselpipaSvar'] );

  if ( empty($visselpipa_id) or empty($answer)) {
    header("Location: /panel/visselpipan?respond=empty");
    exit();
  }

  if (!is_numeric($visselpipa_id)) {
    header("Location: /panel/visselpipan?respond=nan");
    exit();
  }

  $visselpipa = $wpdb->get_row('SELECT * FROM vro_visselpipan WHERE id='. $visselpipa_id);

  if ($visselpipa){

    // Change the specified visselpipa to go into archives
    $wpdb->query( $wpdb->prepare('UPDATE vro_visselpipan SET status = "a" WHERE id = %s', $visselpipa_id));

    $sender_user = get_user_by( 'ID', $visselpipa->user_id );
    if ($sender_user){

      wp_mail( $sender_user->user_email, 'Din visselpipa behandlas!', $answer );

      // Logg action
      $log_description = 'Svarade på visselpipan med id ' . $visselpipa_id . ' med svaret ' . substr($answer, 0, 280);
      add_log( 'Visselpipan', $log_description, get_current_user_id() );

      header("Location: /panel/visselpipan?respond=success");
      exit();

    }

  }

}

else {
  header("Location: /panel/visselpipan?visselpipa=error");
  exit();
} // End post
