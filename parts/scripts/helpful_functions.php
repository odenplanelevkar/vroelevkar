<?php

require_once ABSPATH . '/wp-admin/includes/taxonomy.php';


// Form validation function
function test_input( $data ){
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

function is_student_admin() {
  if (current_user_can('administrator') || current_user_can('elevkaren')) {
    return true;
  } else {
    return false;
  }
}

/*****************************************
* Helper functions
*****************************************/
// LOGGING FUNCTION
function add_log( $log_source = NULL, $description = NULL, $user_id = NULL ) {

  // Add a log
  global $wpdb;

  // Create a new array that will hold all the arguments to create a new log
  $log = array();

  // Check if a log source has been provided and that it is not to long
  if ( $log_source == NULL || empty($log_source) || strlen($log_source) > 100 ){
    $log_source = '';
  }

  // If all good, add the argument
  $log['log_source'] = $log_source;

  // Check if a descriptionhas been provided and that it is not to long
  if ( $description == NULL || empty( $description ) ){
    $description = '';
  }

  if ( strlen($description) > 300 ) {
    $description = substr($description, 0, 280);
  }

  // If all good, add the argument
  $log['description'] = $description;

  // Check if an user id has been provided
  if ( $user_id != NULL && is_numeric($user_id) ){
    $log['user_id'] = $user_id;
  }

  if($wpdb->insert(
        'vro_log',
        $log
    ) == false) {
      // send_error( '/panel/visselpipan?visselpipa', 'Det gick inte att skicka visselpipan.' );
      // wp_die('database insertion failed in logging'); DEV
      return False;
  } else {
    return True;
  }

}

function send_header( $location ){
  header("Location: " . $location);
  exit();
}

function send_error( $return, $wperrormsg ) {
  // Add error log
  add_log( 'Error', $wperrormsg, get_current_user_id() );

  header("Location: " . $return . '=error&wperror=' . $wperrormsg );
  exit();
}

function get_studentshell_id( $wpuser_id ) {
  global $wpdb;

  $student = $wpdb->get_row("SELECT * FROM vro_users WHERE wpuser_id = '$wpuser_id'");
  if ($student == NULL) {
    return NULL;
  } else {
    return $student->id;
  }
}

function check_studentshell( $wpuser_id, $errLocation = false ) {
  $shell_id = get_studentshell_id( $wpuser_id );

  if ($shell_id == NULL) {
    if ($errLocation != false) {
      send_header( $errLocation );
    } else {
      return NULL;
    }
  } else {
    return $shell_id;
  }
}

function get_student_from_nickname( $student_name, $errLocation ) {
  // Get the student with the supplied nickname
  $args = array(
      'meta_query' => array(
          array(
              'key' => 'nickname',
              'value' => $student_name,
              'compare' => '=='
          )
      )
  );

  // Get the student
  $student = get_users($args, 0);

  // If no student found, exit with error msh, otherwise return the student
  if (count($student) < 1) {
    send_header( $errLocation );
  } else {
    return $student;
  }

}

function get_studentshell_from_nickname( $student_name, $errLocation ) {
  // Get the student with the supplied nickname
  // Get the student with the supplied nickname
  global $wpdb;

  $args = array(
      'meta_query' => array(
          array(
              'key' => 'nickname',
              'value' => $student_name,
              'compare' => '=='
          )
      )
  );

  // Get the student
  $student = get_users($args, 0);

  // If no student found, exit with error msh, otherwise return the student
  if (count($student) < 1) {
    send_header( $errLocation );
  } else {

    // Get the studentshell from this wp_user
    $studentshell = $wpdb->get_row("SELECT * FROM vro_users WHERE wpuser_id = $student->ID");
    if (!$studentshell) {
      send_header( $errLocation );
    } else {
      return $studentshell;
    }
  }

}

function get_student_by_id( $id ) {
  global $wpdb;

  $student = $wpdb->get_row("SELECT * FROM vro_users WHERE id = $id");
  if ($student) {
    return $student;
  } else {
    return NULL;
  }
}

function get_studentshell_by_wpuser_id( $wpuser_id ) {
  global $wpdb;

  $student = $wpdb->get_row("SELECT * FROM vro_users WHERE wpuser_id = $wpuser_id");
  if ($student) {
    return $student;
  } else {
    return NULL;
  }
}

function get_studentshell_from_text( $text, $errLocation ) {

  global $wpdb;

  $match = '';
  $count = preg_match('#\((.*?)\)#', $text, $match);

  if ($count < 1) {
    send_header( $errLocation );
  }

  $email = $match[1];

  // Get studentshell
  $studentshell = $wpdb->get_row("SELECT * FROM vro_users WHERE email = '$email'");

  if (!$studentshell) {
    send_header( $errLocation );
  } else {
    return $studentshell;
  }
}


function get_full_studentname( $student ) {
  return $student->first_name . ' ' . $student->last_name;
}

function get_full_studentname_from_id( $student_id ) {
  global $wpdb;

  $student = $wpdb->get_row("SELECT * FROM vro_users WHERE id = $student_id");
  if ($student) {
    return get_full_studentname($student);
  } else {
    return '';
  }
}

function get_full_student_array( $student ) {

  global $wpdb;

  // Get class name
  $class_name = $wpdb->get_var("SELECT name FROM vro_classes WHERE id = $student->class_id");

  $student_data = array();

  $student_data['id'] = $student->id;
  $student_data['first_name'] = $student->first_name;
  $student_data['last_name'] = $student->last_name;
  $student_data['birthyear'] = $student->birthyear;
  $student_data['gender'] = $student->gender;
  $student_data['registered_city'] = $student->registered_city;
  $student_data['phonenumber'] = $student->phonenumber;
  $student_data['email'] = $student->email;
  $student_data['program'] = $student->program;
  $student_data['end_year'] = $student->end_year;
  $student_data['class_name'] = $class_name;

  return $student_data;
}

function check_if_entry_exists( $table, $field, $value, $errLocation = false ) {
  global $wpdb;

  if ( count($wpdb->get_results("SELECT * FROM $table WHERE $field = '$value'")) > 0 ) {
    if ($errLocation){
      send_header( $errLocation );
    } else {
      return true;
    }
  } else {
    return false;
  }

}

function insert_record( $table, $record, $errMsg = false ) {

  global $wpdb;

  if( $wpdb->insert($table, $record) == false){
    send_error( '/panel?insert', $errMsg );
    // wp_die( $errMsg );
  }

}

function check_if_empty( $values, $errLocation = false ) {

  foreach( $values as $v ){
    if (empty($v)){
      if ($errLocation) {
        send_header( $errLocation );
      } else {
        return false;
      }
    }
  }

  return true;

}

function check_number_value ( $value, $errLocation ) {
  check_if_empty( array($value), $errLocation . '=empty' );

  if (!is_numeric($value)) {
    send_header( $errLocation . '=nan' );
  } else {
    return (int)$value;
  }
}

function update_record( $table, $field, $new_value, $check_field, $check_value, $errLocation = false ) {
  global $wpdb;

  if (!$wpdb->query( $wpdb->prepare('UPDATE '. $table .' SET '. $field .' = %s WHERE '. $check_field .' = %s', $new_value, $check_value))) {

    if ($errLocation) {
      send_header( $errLocation );
    } else {
      return false;
    }

  } else {
    return true;
  }
}

// Update a field will null for a given id
function update_with_null( $table, $field, $id, $errLocation = false ) {
  global $wpdb;

  if ($wpdb->update(
    $table,
    array(
        $field => null,
    ),
    array( 'id' => $id ),
    null,
    '%d'
  ) == false) {
    if ($errLocation) {
      send_header( $errLocation );
    } else {
      return false;
    }
  } else {
    return true;
  }

}

function remove_record( $table, $field, $value, $errMsg ){
  global $wpdb;

  // check if there are any records
  if (check_if_entry_exists($table, $field, $value)){
    if (!$wpdb->delete( $table, array( $field => $value ) ) ) {
      send_error( '/panel?remove', $errMsg );
      // wp_die( $errMsg );
    } else {
      return true;
    }
  }
}

function delete_record( $table, $options, $errMsg ) {
  global $wpdb;

  // Delete the student from the record
  if ($wpdb->delete( $table, $options) == false){
    send_error( '/panel?deletion', $errMsg );
    // wp_die( $errMsg );
  } else {
    return true;
  }

}

function emptyToNull( $data ){
  if ($data == '') {
    return null;
  } else {
    return $data;
  }
}

function got_post( $post_name ){
  if (isset($_POST[$post_name])){
    return true;
  } else {
    return false;
  }
}

function check_post( $post, $errMsg ) {
  check_if_empty( array($post), $errMsg );

  return test_input($post);
}

function translateWeekday($day){
  $english = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
  $swedish = array('Måndag', 'Tisdag', 'Onsdag', 'Torsdag', 'Fredag', 'Lördag', 'Söndag');

  return $swedish[array_search($day, $english)];
}

// Return class id from class name
function get_classid_from( $class_name ) {

  global $wpdb;

  // Make sure the first letter i capitilized
  $class_name = ucfirst( strtolower( $class_name ) );

  // Get class with name
  $class = $wpdb->get_row('SELECT * FROM vro_classes WHERE name="' . $class_name . '"');

  // No class was found
  if ($class == NULL) {
    return NULL;
  } else {
    return (int)$class->id;
  }

}

function get_classname_by_id( $class_id ) {

  global $wpdb;

  // Get class with id
  $class = $wpdb->get_row("SELECT * FROM vro_classes WHERE id=$class_id");

  // No class was found
  if ($class == NULL) {
    return NULL;
  } else {
    return $class->name;
  }
}

// Return end_year from class name
function get_endyear_from( $class_name ) {
  // Get the end year from the class name
  $yearFromClassName = substr($class_name, 2, 2);

  // Get the year in 4-digit form
  $yearFromClassName = date_create_from_format('y', $yearFromClassName);
  $yearFromClassName = $yearFromClassName->format('Y');

  $end_year = (int)$yearFromClassName;
  return $end_year;
}

function getStudentsInYear($year, $students) {
  // Check if the year is numeric
  if (!is_numeric($year)){
    return false;
  } else {
    $year = (int)$year;
  }

  // Check if the year supplied is 1, 2 or 3
  if (  !in_array($year, array(1, 2, 3)) ) {
    return false;
  }

  // Get the current year
  $current_year = date('Y');

  // Get the current month
  $current_month = date('m');

  // DEBUG
  // $current_month = date('m', strtotime('+6 months'));
  //
  // echo $current_year . ' ' . $current_month;

  // If it is before july, act as the year is the previous one
  if ((int)$current_month < 7){
    $current_year = (int)$current_year - 1;
  } else {
    $current_year = (int)$current_year;
  }

  $yearArray = array();

  foreach ($students as $s) {
    // check if end year meta data exists
    if ($s->end_year){

      // Get the difference, a.k.a the number of years left for this student
      $years_left =  $s->end_year - $current_year;

      // Get the grade they are in. ex. 3 years left means you are a 1:st grader, therefore 4 - 3 = 1, 4 - 2 years left = 2 etc.
      $grade = 4 - $years_left;

      // Check if this was the year asked for
      if ($grade == $year){
        array_push($yearArray, $s);
      }
    }
  }

  return $yearArray;
}

function get_birthyear_by_email( $email ) {

  // Get the endyear
  $end_year = substr($email, 10, 4);

  // Check if valid
  if (!is_numeric($end_year)) {
    return false;
  }

  $end_year = (int)$end_year;
  $birthyear = $end_year - 19;

  return $birthyear;

}

function get_student_grade( $student ) {

  // Get the current year
  $current_year = date('Y');

  // Get the current month
  $current_month = date('m');

  // If it is before july, act as the year is the previous one
  if ((int)$current_month < 7){
    $current_year = (int)$current_year - 1;
  } else {
    $current_year = (int)$current_year;
  }

  $class_name = get_classname_by_id( $student->class_id );

  if ($student->end_year == NULL){
    $end_year = get_endyear_from( $class_name );
  } else {
    $end_year = $student->end_year;
  }

  // Get the difference, a.k.a the number of years left for this student
  $years_left =  $end_year - $current_year;

  // Get the grade they are in. ex. 3 years left means you are a 1:st grader, therefore 4 - 3 = 1, 4 - 2 years left = 2 etc.
  $grade = 4 - $years_left;

  return $grade;

}

function is_event_today( $start_time, $end_time ) {
  $start_dmy = date('d M Y', $start_time);
  $end_dmy = date('d M Y', $end_time);
  $today_dmy = date('d M Y', time());

  // Event is only one day
  if ( $start_dmy == $end_dmy) {
    // Check if event is today
    if ($start_dmy == $today_dmy ) {
      return True;
    } else {
      return False;
    }
  }

  // Event is during multiple days, check if today is one of the event days
  if ( $today_dmy >= $start_dmy && $today_dmy <= $end_dmy ) {
    return True;
  }

  // Otherwise return false
  return False;
}

function check_id( $id, $table_name ) {
  if (empty($id)){
    return array(false, 'empty');
  }

  if (!is_numeric($id)){
    return array(false, 'nan');
  }

  global $wpdb;

  if ( count($wpdb->get_results('SELECT * FROM '. $table_name .' WHERE id="'. $id .'"')) < 1 ) {
    return array(false, 'norecord');
  }

  return array(true, (int)$id);
}

function value_exists_in_table( $value, $column_name, $table_name, $row_id = false ){

  global $wpdb;

  if ($row_id){

    if ( count($wpdb->get_results('SELECT * FROM '. $table_name .' WHERE '. $column_name .'="'. $value .'" AND id <>'. $row_id)) > 0 ) {
      return true;
    } else {
      return false;
    }

  } else {

    if ( count($wpdb->get_results('SELECT * FROM '. $table_name .' WHERE '. $column_name .'="'. $value .'"')) > 0 ) {
      return true;
    } else {
      return false;
    }

  }



}

function is_member( $u_id ){

  global $wpdb;

  $status = get_metadata('user', $u_id, 'status');

  return ($status[0] == 'y') ? true : false;

}

function is_chairman( $u_id ) {

  global $wpdb;

  $kommitte_names = array();

  $student_id = $wpdb->get_var("SELECT id FROM vro_users WHERE wpuser_id = $u_id");
  $kommittes = $wpdb->get_results('SELECT * FROM vro_kommiteer WHERE chairman = ' . $student_id . ' AND status="y"');

  foreach ( $kommittes as $k ) {
    array_push( $kommitte_names, array('id' => $k->id, 'name' => $k->name) );
  }

  return $kommitte_names;

}

function get_kommitte_cat_ids( $u_id ) {

  global $wpdb;

  $all_kommittes = $wpdb->get_results('SELECT * FROM vro_kommiteer_members WHERE user_id = '. $u_id . ' AND status="y"');

  $cat_array = array();

  foreach ($all_kommittes as $k){
    $cat_name = 'kommitte_' . $k->kommitee_id;

    if ( category_exists( $cat_name ) ){

      $cat_id = get_cat_ID( $cat_name );

      if ( $cat_id != 0 ) {
        array_push( $cat_array, $cat_id );
      }

    }
  }

  return $cat_array;

}

function display_karbrev( $amount = 0, $header = true, $edit = true ){

  require_once ABSPATH . '/wp-admin/includes/taxonomy.php';

  if (category_exists( 'karbrev' ) ) {
    $catArray = Array( get_cat_ID('karbrev') );
  } else {
    $catArray = Array();
  }

  if (count($catArray) > 0){

    $args = array(
        'category__in' => $catArray,
        'post_status' => 'publish',
        'post_type' => 'post',
        'orderby' => 'post_date',
        'posts_per_page' => $amount
    );

    // The Query
    $the_query = new WP_Query( $args );

      if ( $the_query->have_posts() ) : ?>

          <?php if ( $header ): ?>
          <div class="see-more blogposts-header">
            <h2>Kårbrev</h2>
            <div class="">
              <a href="/panel/arkiv#karbrev">See alla kårbrev &#8594;</a>
            </div>
          </div>
          <?php endif; ?>

          <?php while ( $the_query->have_posts() ) {
              $the_query->the_post();
              global $edit;
              get_template_part( 'content' );
          } ?>
      <?php endif;
    /* Restore original Post Data */
      wp_reset_postdata();

  }

}

// function archive_old_notification() {
//
//   global
//
// }

function display_kommitte_notifications( $amount = 0, $header = true, $archives = false ) {
  $cat_array = get_kommitte_cat_ids( get_current_user_id() );

  if (count($cat_array) > 0) {

    if ($archives == false) {
      $args = array(
          'category__in' => $cat_array,
          'post_status' => 'publish',
          'post_type' => 'post',
          'orderby' => 'post_date',
      );
    } else {
      $args = array(
          'category__in' => $cat_array,
          'post_status' => 'archive',
          'post_type' => 'post',
          'orderby' => 'post_date',
      );
    }


    // The Query
    $the_query = new WP_Query( $args );

    // The Loop
    if ( $the_query->have_posts() ) : ?>

        <?php if ( $header ): ?>
        <div class="see-more blogposts-header">
          <h2>Nya notiser</h2>
          <div class="">
            <a href="/panel/arkiv#kommitte">See alla notiser &#8594;</a>
          </div>
        </div>
      <?php endif; ?>

        <?php while ( $the_query->have_posts() ) {
            $the_query->the_post();
            global $edit;
            get_template_part( 'content' );
        } ?>
    <?php endif;
    /* Restore original Post Data */
    wp_reset_postdata();

  } // End if current student is part of any kommittées
}

function display_karen( $edit = false ){

  ?>

  <h2>Styrelsen</h2>
  <div class="row styrelsen" id="styrelsen">

    <?php

    // Get all events type
    global $wpdb;

    $styrelsen = $wpdb->get_results('SELECT * FROM vro_styrelsen');

    foreach ($styrelsen as $s) {

      $vro_student = get_student_by_id( $s->student );

      ?>

        <div class="box white chairman sm clickable">

        <div class="edit-image">
          <?php echo get_avatar( $vro_student->wpuser_id ); ?>
          <?php if ($edit) { ?>
            <button type="button" name="button" class="edit-styrelse" onclick="event.stopPropagation();"><img src="<?php echo get_bloginfo('template_directory'); ?>/img/editcircle.png"></button>
          <?php } ?>
        </div>


          <h3><?php echo $s->position_name; ?></h3>
          <p><?php echo get_full_studentname_from_id( $s->student ); ?></p>
          <input type="text" name="position-id" value="<?php echo $s->id; ?>" hidden>
          <input type="text" name="student-id" value="<?php echo $s->student; ?>" hidden>
          <input class="position-student-email" name="" value="<?php echo $vro_student->email; ?>" hidden>

          <?php if ($edit): ?>
          <button class="btn" type="button" name="button">Info</button>
          <?php endif; ?>
      </div>

      <?php
    }

    ?>
  </div>

  <h2>Utskotten</h2>
  <div class="row styrelsen" id="utskotten">

    <?php

    // Get all events type
    global $wpdb;

    $utskotten = $wpdb->get_results('SELECT * FROM vro_utskott');

    foreach ($utskotten as $u) {

      $vro_student = get_student_by_id( $u->chairman );

      ?>

      <?php
        if ($edit) {
          echo '<div class="box white chairman sm clickable">';
        } else {
          echo '<div class="box white chairman sm clickable">';
        }
      ?>

          <?php if ($edit) { ?>
            <div class="edit-image">
              <?php echo get_avatar( $vro_student->wpuser_id  ); ?>
              <button type="button" name="button" class="edit"><img src="<?php echo get_bloginfo('template_directory'); ?>/img/editcircle.png"></button>
            </div>
          <?php } else {
            echo get_avatar( $u->chairman );
          } ?>

          <h3><?php echo $u->name; ?></h3>
          <p>Ordförande: <?php echo get_full_studentname( $vro_student ); ?></p>
          <input class="utskott-id" type="text" name="utskott-id" value="<?php echo $u->id; ?>" hidden>
          <input type="text" name="chairman-id" value="<?php echo $u->chairman; ?>" hidden>
          <input class="utskott-description" type="text" name="" value="<?php echo $u->description; ?>" hidden>
          <input class="utskott-chairman-email" name="" value="<?php echo $vro_student->email; ?>" hidden>
          <button class="btn" type="button" name="button">Info</button>
      </div>

      <?php
    }

    ?>
  </div>

  <?php

}


// META DATA
function update_or_add_meta( $u_id, $key, $value ) {

  //CHeck if meta data exists
  if (metadata_exists( 'user', $u_id, $key )){

    // If so, update its value
    if ( update_user_meta( $u_id, $key, $value ) == false ){
      return false;
    }
  } else {
    // Otherwise, add a user meta with the supplied value

    if ( add_user_meta( $u_id, $key, $value ) == false){
      return false;
    }
  }

  return true;
}



function show_success_alert( $header, $msg ) {
  echo '<script type="text/javascript">Swal.fire("'. $header .'","'. $msg .'","success")</script>';
}

function show_error_alert() {
  if (isset($_GET['wperror'])) {
    $header = 'Oj, något gick fel!';
    $msg = test_input($_GET['wperror']);

    echo '<script type="text/javascript">Swal.fire("'. $header .'","'. $msg .'","error")</script>';
  }
}

function show_form_messages( $get_statement, $success_msg ) {

  if (isset($_GET[$get_statement])) {

    $check = $_GET[$get_statement];

    if ($check == 'empty') {
      echo '<p class="error">Du måste fylla i alla fält!</p>';
    }
    elseif ($check == 'success') {
      echo "<p class='success'>$success_msg</p>";
    }

  }

}
