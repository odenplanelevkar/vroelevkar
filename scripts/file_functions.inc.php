<?php

// Include wp_core
require_once(explode("wp-content", __FILE__)[0] . "wp-load.php");

// Include helpful functions
include_once('helpful_functions.php');

function check_if_null( $values ) {
  foreach ($values as $v) {
    if ($v == NULL) {
      return true;
    }
  }

  return false;
}

function create_header( $values ) {
  $text = '|';

  foreach ($values as $v) {
    $column_length = strlen($v) + 2;
    $text .= str_pad($v, $column_length, ' ', STR_PAD_BOTH) . '|';
  }

  $text .= "\n";

  return $text;
}

function fill_column( $column_header, $value ) {
  $text = '';

  if ($value == NULL) {
    // FORMAT FÖDELSEÅR CORRECTLY
    if ($column_header == 'FÖDELSEÅR') {
      $text = ('|' . str_pad('X', strlen($column_header), ' ', STR_PAD_BOTH) );
    } else {
      $text = ('|' . str_pad('X', strlen($column_header) + 2, ' ', STR_PAD_BOTH) );
    }

  } else {
    if ($column_header == 'FÖDELSEÅR') {
      $text = ('|' . str_pad(' ', strlen($column_header), ' ', STR_PAD_BOTH) );
    } else {
      $text = ('|' . str_pad(' ', strlen($column_header) + 2, ' ', STR_PAD_BOTH) );

    }
  }

  if ($column_header == 'KÖN') {
    $text = substr($text, 0, -1);
  }

  return $text;
}

/*
  Character codes:
    å - 195, 165
*/

function get_pad_length( $pad, $name ) {
  $name_char = str_split($name);
  $wierd_chars = array( ord('å'), ord('ä'), ord('ö'), ord('é') );

  foreach ($name_char as $c) {
    $ord_ch = ord($c);

    //var_dump( $c . ' - ' . $ord_ch );

    if (in_array($ord_ch, $wierd_chars)) {
      $pad += 1;
    }
  }

  return $pad;
}

if (isset($_POST['download-member-report'])) {

  global $wpdb;

  $filename = "medlemsrapport.txt";
  $filepath = '../misc/' . $filename;

  // filename for download
  $current_date = date('Y-m-d');
  $download_filename = "medlemsrapport-$current_date.txt";

  // Get all students
  $students = $wpdb->get_results("SELECT * FROM vro_users");

  // Download
  header('Content-Type: application/txt');
  header('Content-Disposition: attachment; filename="' . $download_filename . '";');

  // Open a new csv file
  $f = fopen( 'php://output', 'w' );
  // $f = fopen( $filepath, 'w' );
  fputs($f, str_pad('SAKNAD MEDLEMSINFORMATION', 100, '-', STR_PAD_BOTH) . "\n");
  fputs($f, "  - X i rutan betyder att informationen SAKNAS\n\n");

  $name_pad = 35;
  $headers = "|" . str_pad('NAMN', $name_pad, ' ', STR_PAD_BOTH) . create_header( array('KLASS', 'HEMSIDEKONTO', 'TELEFON', 'MAIL', 'FOLKBOKFÖRD  STAD', 'DATUM MEDLEM', 'KÖN', 'PROGRAM', 'FÖDELSEÅR') );
  fputs($f, $headers . str_pad('', strlen($headers) - 5, '-') . "\n");

  // Go through and add a row for each student
  foreach ($students as $s) {

    // Check if student is memebr
    if ($s->status != 'y') {
      continue;
    }

    // Check if there are any missing values for this student
    if (check_if_null( array( $s->phonenumber, $s->date_member, $s->gender, $s->registered_city, $s->program, $s->email, $s->birthyear ) ) == false) {
      continue;
    }

    $class_name = get_classname_by_id( $s->class_id );

    // Get fullname of student and pad extra for names with å,ä,ö and é
    $fullname = "$s->first_name $s->last_name";
    $pad_length = get_pad_length($name_pad - 1, $fullname);

    // Write the name, and pad extra for wierd chars
    $text_row = '';
    $text_row .= '| ' . str_pad( $fullname, $pad_length) . '|';
    $text_row .= str_pad($class_name, 7, ' ', STR_PAD_BOTH);

    // Write the colums
    $text_row .= fill_column( 'HEMSIDEKONTO', $s->wpuser_id );
    $text_row .= fill_column( 'TELEFON', $s->phonenumber );
    $text_row .= fill_column( 'MAIL', $s->email );
    $text_row .= fill_column( 'FOLKBOKFÖRD STAD', $s->registered_city );
    $text_row .= fill_column( 'DATUM MEDLEM', $s->date_member );
    $text_row .= fill_column( 'KÖN', $s->gender );
    $text_row .= fill_column( 'PROGRAM', $s->program );
    $text_row .= fill_column( 'FÖDELSEÅR', $s->birthyear );

    // Sepparator
    $text_row .= "|\n";
    $text_row .= str_pad('', strlen($headers) - 5, '-') . "\n";

    // Write whole line to file
    fputs($f, $text_row);

  }

  // Redirect
  exit();

}

else if (isset($_POST['download-member-list'])) {

  global $wpdb;

  // filename for download
  $filename = "medlemslista.csv";
  $filepath = '../misc/' . $filename;

  $current_date = date('Y-m-d');
  $download_filename = "medlemslista-$current_date.csv";

  // Get all students
  $students = $wpdb->get_results("SELECT * FROM vro_users");

  // The csv column headers
  // $csv_top = "*Förnamn,*Efternamn,*Födelseår,*Kön,*Folkbokförd stad,*Inträdesdatum,*Telefon/mobil,*E-postadress,Utbildningsprogram (Fullt namn),Årskurs (1-3)\n";

  // Download
  header('Content-Type: application/csv');
  header('Content-Disposition: attachment; filename="' . $download_filename . '";');

  // Open a new csv file
  $f = fopen( 'php://output', 'w' );

  // fputs( $f, $csv_top );

  // Go through and add a row for each student
  foreach ($students as $s) {

    // Check if student is memebr
    if ($s->status != 'y') {
      continue;
    }

    // Check required field
    if (check_if_empty(array( $s->first_name, $s->last_name, $s->date_member, $s->phonenumber, $s->email, $s->program, $s->gender )) == false) {
      continue;
    }

    $first = $s->first_name;
    $last = $s->last_name;
    $birthyear = ($s->birthyear != NULL) ? $s->birthyear : get_birthyear_by_email( $s->email ); // If no birthyear supplied, generate one from vrg email
    $gender = ($s->gender != NULL) ? $s->gender : 'Annat'; // Default gender to Annat
    $city = ($s->registered_city != NULL) ? $s->registered_city : 'Stockholm'; // Default folkbokförd stad to Stockholm

    // Convert registered date to YYYY-MM-DD format
    $date = strtotime( $s->date_member );
    $registered = date( 'Y-m-d', $date );

    $phonenumber = $s->phonenumber;
    $email = $s->email;
    $program = $s->program;
    $grade = get_student_grade( $s );

    $student_row = array( $first, $last, $birthyear, $gender, $city, $registered, $phonenumber, $email, $program, $grade );

    fputcsv($f, $student_row, ',');

  }

  // Redirect
  exit();
  //send_header( '/panel/medlemmar' );

} else {
  send_header('/panel/dashboard');
}
