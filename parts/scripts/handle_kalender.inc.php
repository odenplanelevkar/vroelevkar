<?php

// Include wp_core
require_once(explode("wp-content", __FILE__)[0] . "wp-load.php");

// Include helpful functions
include_once('helpful_functions.php');

// Create database entry
if (isset($_POST['add_event_type'])) {

  global $wpdb;

  $name = test_input( $_POST['etName'] );
  $symbol = urldecode( test_input( $_POST['etSymbol'] ) );
  $bgColor = test_input( $_POST['etBgColor'] );
  $fgColor = test_input( $_POST['etFgColor'] );

  check_if_empty(array($name), '/panel/kalender?event_type=empty');

  if (check_if_entry_exists('vro_event_types', 'name', $name)){
      // Replace the # to %23 so it is safe to send in the url
      $bgColor = '%23' . substr($bgColor, 1);
      $fgColor = '%23' . substr($fgColor, 1);

      header("Location: /panel/kalender?event_type=nametaken&symbol=$symbol&bgColor=$bgColor&fgColor=$fgColor");
      exit();
    }
    // Create a new array that will hold all the arguments to create a new visselpipan suggestion
    $new_et = array();

    $new_et['name'] = $name;
    $new_et['symbol'] = $symbol;
    $new_et['bg_color'] = $bgColor;
    $new_et['fg_color'] = $fgColor;

    // Insert the new suggestion into the database
    insert_record('vro_event_types', $new_et, 'Db insertion failed in add_event_type');

    // Logg action
    $log_description = 'Lade till eventtypen '. $name .' med symbolen ' . $symbol . ' med förgrundsfärgen ' . $fgColor . ' med bakgrundsfärgen ' . $bgColor;
    add_log( 'Kalender', $log_description, get_current_user_id() );

    header("Location: /panel/kalender?event_type=success");
    exit();

}
elseif (isset($_POST['show_add_event_type'])) {
  header("Location: /panel/kalender?event_type=open");
  exit();
}

elseif (isset($_POST['remove_event_type'])) {

  global $wpdb;

  $etId = check_number_value( test_input( $_POST['remove_event_type'] ), '/panel/kalender?remove_event_type');

  // disable the specified event type
  if ($wpdb->update( 'vro_event_types', array( 'status' => 'n' ), array( 'id' => $etId ) ) == false){
    send_error( '/panel/kalender?remove_event_type', 'Det gick inte att ta bort eventtypen.' );
    // wp_die('database remove event type failed');
  } else {
    // Success!

    // Logg action
    $log_description = 'Avaktiverade eventtypen med id '. $etId;
    add_log( 'Kalender', $log_description, get_current_user_id() );

    header("Location: /panel/kalender?remove_event_type=success");
    exit();
  }

  // Delete the specified event type
  // if ($wpdb->delete( 'vro_event_types', array( 'id' => $etId ) ) == false ){
  //   // If it did not work, send back error
  //   wp_die('database deletion failed');
  // } else {
  //   // Success!
  //   header("Location: /panel/kalender?remove_event_type=success");
  //   exit();
  // }
}
elseif (isset($_POST['alter_event_type'])) {

  global $wpdb;

  $etId = check_number_value( test_input( $_POST['alter_event_type'] ), '/panel/kalender?alter_event_type');

  $name = test_input( $_POST['etName'] );
  $symbol = test_input( $_POST['etSymbol'] );
  $bgColor = test_input( $_POST['etBgColor'] );
  $fgColor = test_input( $_POST['etFgColor'] );

  check_if_empty(array($name), '/panel/kalender?alter_event_type=empty');

  // Check that no other event type has the same name
  if ( count($wpdb->get_results('SELECT * FROM vro_event_types WHERE name="'. $name .'" AND id != '. $etId .' ')) > 0 ) {
    // Replace the # to %23 so it is safe to send in the url
    $bgColor = '%23' . substr($bgColor, 1);
    $fgColor = '%23' . substr($fgColor, 1);
    $symbol = urlencode( $symbol );

    header("Location: /panel/kalender?alter_event_type=nametaken&id=$etId&symbol=$symbol&bgColor=$bgColor&fgColor=$fgColor");
    exit();
  }

  $updated_event = array(
    'name' => $name,
    'symbol' => $symbol,
    'bg_color' => $bgColor,
    'fg_color' => $fgColor
  );

  if ($wpdb->update( 'vro_event_types', $updated_event, array( 'id' => $etId ) ) == false){
    send_error( '/panel/kalender?alter_event_type', 'Det gick inte att ändra eventtypen.' );
    // wp_die('database alter event type failed'); DEV
  } else {
    // Success!

    // Logg action
    $log_description = 'Ändrade eventtypen med id '. $etId . ' och har nu namnet ' . $name .', symbolen ' . $symbol . ', förgrundsfärgen ' . $fgColor . ' och bakgrundsfärgen ' . $bgColor;
    add_log( 'Kalender', $log_description, get_current_user_id() );

    header("Location: /panel/kalender?alter_event_type=success");
    exit();
  }

}

elseif (isset($_POST['show_alter_event_type'])) {

  global $wpdb;

  $etId = check_number_value( test_input( $_POST['show_alter_event_type'] ), '/panel/kalender?show_alter_event_type');

  // Safe to use it in db call
  $selected_et = $wpdb->get_row('SELECT * FROM vro_event_types WHERE id=' . $et_id);

  if (!$selected_et){
    header("Location: /panel/kalender?show_alter_event_type=noetfound");
    exit();
  }

  // Send back all values to front page
  $bg_color = '%23' . substr($selected_et->bg_color, 1);
  $fg_color = '%23' . substr($selected_et->fg_color, 1);

  header("Location: /panel/kalender?show_alter_event_type=open&id=$selected_et->id&type_name=$selected_et->name&symbol=$selected_et->symbol&bgColor=$bg_color&fgColor=$fg_color");
  exit();

}

elseif (isset($_POST['add_event'])) {

  global $wpdb;

  $event_type_id = $_POST['ae_event_type'];
  $event_name = $_POST['ae_name'];
  $event_place = emptyToNull( $_POST['ae_place'] );
  $event_host = emptyToNull( $_POST['ae_host'] );
  $event_kommitte_id = test_input( $_POST['ae_host_kommitte'] );

  $event_start_date = $_POST['ae_start_date'];
  $event_start_time = $_POST['ae_start_time'];
  $event_end_date = $_POST['ae_end_date'];
  $event_end_time = $_POST['ae_end_time'];

  $event_description = emptyToNull( $_POST['ae_description'] );
  $event_visibility = $_POST['ae_visibility'];

  $is_kommitte_event = False;

  if ($event_type == 'none'){
    header("Location: /panel/kalender?add_event=noeventtype");
    exit();
  }

  if (empty($event_name)) {
    header("Location: /panel/kalender?add_event=noname");
    exit();
  }

  // Get name of event type
  $event_type = $wpdb->get_row('SELECT * FROM vro_event_types WHERE id = ' . $event_type_id);
  if ($event_type == NULL) {
    header("Location: /panel/kalender?add_event=noeventtype");
    exit();
  }

  // Get the kommitté
  if ($event_type->name == 'Kommittéevent') {

    // Check if kommitte_id is correct
    $kommitte = $wpdb->get_row('SELECT * FROM vro_kommiteer WHERE id = '. $event_kommitte_id);

    if ($kommitte == NULL) {
      header("Location: /panel/kalender?add_event=nokommitte");
      exit();
    }

    $event_host = $kommitte->name;
    $is_kommitte_event = True;

  }

  // TODO: check if end is before start

  // TODO: check if host exists if it is set

  // TODO: check if host exists if the visibility is set to utskott only

  // Create a new array that will hold all the arguments to create a new vevent
  $new_event =  array();

  $new_event['type'] = $event_type_id;
  $new_event['name'] = $event_name;
  $new_event['place'] = $event_place;
  $new_event['host'] = $event_host;

  if ($is_kommitte_event){
    $new_event['kommitte_host_id'] = $event_kommitte_id;
  }

  $new_event['start'] = $event_start_date . ' ' . $event_start_time;
  $new_event['end'] = $event_end_date . ' ' . $event_end_time;
  $new_event['description'] = $event_description;
  $new_event['visibility'] = $event_visibility;

  // echo $new_event['type'] . ' --- ' . $new_event['name'] . ' --- ' . $new_event['place'] . ' --- ' . $new_event['host'] . ' --- ' . $new_event['start'] . ' --- ' . $new_event['end'] . ' --- ' . $new_event['description'] . ' --- ' . $new_event['visibility'];

  // Insert the new suggestion into the database
  if($wpdb->insert(
      'vro_events',
      $new_event
  ) == false) {
    send_error( '/panel/kalender?add_event', 'Det gick inte att lägga till eventet.' );
    // wp_die('database insertion failed'); DEV
  }

  // Logg action
  $log_description = 'Lade till eventet ' . $new_event['name'] . ', eventtyp: ' . $new_event['type'] . ', plats: ' . $new_event['place'] . ', host: ' . $new_event['host'] . ', start: ' . $new_event['start'] . ', slut: ' . $new_event['end'] . ', beskrivning: ' . $new_event['description'] . ', synlighet: ' . $new_event['visibility'];
  add_log( 'Kalender', $log_description, get_current_user_id() );

  header("Location: /panel/kalender?add_event=success");
  exit();

}

elseif (isset($_POST['publish_event'])){

  global $wpdb;

  $event_id = test_input( $_POST['publish_event'] );

  $id_response = check_id( $event_id, 'vro_events' );

  if ($id_response[0] == false){
    header("Location: /panel/kalender?publish_event=". $id_response[1]);
    exit();
  }

  $event_id = (int)$event_id;

  if ($wpdb->update( 'vro_events', array('visibility' => 'a'), array( 'id' => $event_id ) ) == false){
    send_error( '/panel/kalender?publish_event', 'Det gick inte att publicera eventet.' );
    // wp_die('publish event failed'); DEV
  } else {
    // Success!

    // Logg action
    $log_description = 'Publicerade eventet med id ' . $event_id;
    add_log( 'Kalender', $log_description, get_current_user_id() );

    header("Location: /panel/kalender?publish_event=success");
    exit();
  }

}

elseif (isset($_POST['unpublish_event'])){

  global $wpdb;

  $event_id = test_input( $_POST['unpublish_event'] );

  $id_response = check_id( $event_id, 'vro_events' );

  if ($id_response[0] == false){
    header("Location: /panel/kalender?unpublish_event=". $id_response[1]);
    exit();
  }

  $event_id = (int)$event_id;

  if ($wpdb->update( 'vro_events', array('visibility' => 'e'), array( 'id' => $event_id ) ) == false){
    send_error( '/panel/kalender?unpublish_event', 'Det gick inte att avpublicera eventet.' );
    // wp_die('unpublish event failed');
  } else {
    // Success!

    // Logg action
    $log_description = 'Avpublicerade eventet med id ' . $event_id;
    add_log( 'Kalender', $log_description, get_current_user_id() );

    header("Location: /panel/kalender?unpublish_event=success");
    exit();
  }

}

elseif (isset($_POST['remove_event'])){

  global $wpdb;

  $event_id = test_input( $_POST['remove_event'] );

  $id_response = check_id( $event_id, 'vro_events' );

  if ($id_response[0] == false){
    header("Location: /panel/kalender?remove_event=". $id_response[1]);
    exit();
  }

  $event_id = (int)$event_id;

  if ($wpdb->delete( 'vro_events', array( 'id' => $event_id ) ) == false){
    send_error( '/panel/kalender?remove_event', 'Det gick inte att ta bort eventet.' );
    // wp_die('remove event failed'); DEV
  } else {
    // Success!

    // Logg action
    $log_description = 'Tog bort eventet med id ' . $event_id;
    add_log( 'Kalender', $log_description, get_current_user_id() );

    header("Location: /panel/kalender?remove_event=success");
    exit();
  }

}

else {
  header("Location: /panel/kalender");
  exit();
}
