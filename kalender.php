<?php

/**
 * Template Name: Kalender
 */

 // Do not show page for gamers only
 if (current_user_can('gamer')){
   wp_redirect( '/' );
 }

// CHECK IF LOGGED IN
if (! is_user_logged_in() ){
  wp_redirect( '/wp-login.php' );
} else {

// Get the current student
$current_student = wp_get_current_user();
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta lang="sv">
    <meta name="viewport" content="width=device-width; initial-scale=1.0;">

    <title>VRO Elevkår</title>

    <link rel="icon" href="<?php echo get_bloginfo('template_directory') ?>/img/logga.png" type="image/icon type">
    <link rel="stylesheet" href="<?php echo get_bloginfo('template_directory') ?>/css/admin.css">
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,700&display=swap" rel="stylesheet">
    <script src="<?php echo get_bloginfo('template_directory') ?>/js/admin.js" charset="utf-8"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
  </head>
  <body>

    <!-- ***********************************
    * ERROR HANDLING
    *************************************-->
    <?php show_error_alert(); ?>

    <div class="container">

      <!-- ***********************************
      * NAVBAR
      *************************************-->

      <?php
      // Display a special navbar for admins
      if (current_user_can('administrator') || current_user_can('elevkaren') ){
        require_once(get_template_directory() . "/parts/admin-navigation-bar.php");
      } else {
        require_once(get_template_directory() . "/parts/member-navigation-bar.php");
      }
      ?>

      <!--
      * Dashboard
      --------------------------------------->
      <section id="dashboard">

          <!-- <button data-modal-target="#modal" type="button" name="button">Open modal</button> -->

        <div class="top-bar">
          <h2>Kalender</h2>
          <p><?php echo current_time('d M Y, D'); ?></p>
        </div>

        <div class="banner">
          <h3>Välkommen tillbaka <?php echo get_user_meta($current_student->ID,'nickname',true); ?>!</h3>
          <img src="<?php echo get_bloginfo('template_directory') ?>/img/chatright.png" alt="" class="chatright">
          <img src="<?php echo get_bloginfo('template_directory') ?>/img/chatleft.png" alt="" class="chatleft">
        </div>

        <?php
        // Only show the event types for admins
        if (current_user_can('administrator') || current_user_can('elevkaren') ){
        ?>

        <div class="row">

          <div class="box green lg">
            <h4>Eventtyper</h4>

            <div class="event-types">

              <?php

              global $wpdb;

              $enabled_event_types = $wpdb->get_results('SELECT * FROM vro_event_types WHERE status="y"');

              foreach ($enabled_event_types as $et) {
                ?>

                <div class="event-type" style="background-color: <?php echo $et->bg_color; ?>" onclick="clickElement('alterEventTypeInput-<?php echo $et->id ?>')">

                  <p style="color: <?php echo $et->fg_color; ?>"><?php echo $et->name; ?></p>

                  <form action="<?php echo (get_bloginfo('template_directory') . '/scripts/handle_kalender.inc.php'); ?>" method="post">
                    <button class="btn add-btn deny" type="submit" name="remove_event_type" value="<?php echo $et->id ?>" onclick="event.stopPropagation(); return confirm('Är du säker på att du vill ta bort denna eventtyp?');">-</button>
                    <input hidden id="alterEventTypeInput-<?php echo $et->id; ?>" type="submit" name="show_alter_event_type" value="<?php echo $et->id ?>">
                  </form>

                </div>

                <?php
              }

              ?>

              <div class="event-type add-new">
                <p>Lägg till ny</p>
                <form class="" action="<?php echo (get_bloginfo('template_directory') . '/scripts/handle_kalender.inc.php'); ?>" method="post">

                <?php

                // echo $_SERVER['REQUEST_URI'];
                if (isset($_GET['show_alter_event_type']) or (isset($_GET['event_type']) && $_GET['event_type'] != 'open') ){
                  echo '<button class="btn add-btn" type="submit" name="show_add_event_type" id="addEtBtn">+</button>';
                } else {
                  echo '<button class="btn add-btn" type="button" name="button" onclick="showAnswerForm('. '\'add_event_type\'' . ')" id="addEtBtn">+</button>';
                }
                ?>

                </form>
              </div>
            </div>

            <div class="answer" id="add_event_type">

              <hr>

              <?php

              if (isset($_GET['show_alter_event_type']) or isset($_GET['alter_event_type'])) {
                echo '<h4>Uppdatera eventtyp</h4>';
              } else {
                echo '<h4>Lägg till ny eventtyp</h4>';
              }

              ?>

              <form action="<?php echo (get_bloginfo('template_directory') . '/scripts/handle_kalender.inc.php'); ?>" method="POST">

                <?php // Show error messages

                // Check if form has been submited
                if (isset($_GET['event_type'])) {

                  // Get the msg from the form
                  $et_check = $_GET['event_type'];

                  // If it is not successful, make sure to open the form again
                  if ($et_check != 'success') {
                    echo '<script type="text/javascript">showAnswerForm("add_event_type", updateEtPreview);</script>';

                    // Then check what type of error
                    if ($et_check == 'empty'){
                      echo '<p class="error">Du måste fylla i alla värden!</p>';
                    }
                    elseif ($et_check == 'nametaken'){
                      echo '<p class="error">Namnet är redan taget!</p>';

                    }

                  }

                }

                // Check if form has been submited
                else if (isset($_GET['alter_event_type'])) {

                  // Get the msg from the form
                  $aet_check = $_GET['alter_event_type'];

                  // If it is not successful, make sure to open the form again
                  if ($aet_check != 'success') {
                    echo '<script type="text/javascript">showAnswerForm("add_event_type", updateEtPreview);</script>';

                    // Then check what type of error
                    if ($aet_check == 'empty'){
                      echo '<p class="error">Du måste fylla i alla värden!</p>';
                    }
                    elseif ($aet_check == 'nametaken'){
                      echo '<p class="error">Namnet är redan taget!</p>';

                    }

                  }

                }

                // Check if an alter event type request has been made
                if (isset($_GET['show_alter_event_type']) ){
                  // Check if the form has to be opened
                  if ($_GET['show_alter_event_type'] == 'open'){
                    echo '<script type="text/javascript">showAnswerForm("add_event_type");</script>';
                  }
                }

               ?>

               <?php
               // Check if there is a saved name from previous try
               if (isset($_GET['type_name'])){
                 echo '<input value="'. $_GET['type_name'] .'" type="text" name="etName" placeholder="*Namn på eventtyp..." id="etName" onKeyUp="updateEtPreview()" required>';
               } else {
                 echo '<input type="text" name="etName" placeholder="*Namn på eventtyp..." id="etName" onKeyUp="updateEtPreview()" required>';
               }
               ?>

               <?php
               // Check if there is a saved symbol from previous try
               if (isset($_GET['symbol'])){
                 echo '<input value="'. urldecode( $_GET['symbol'] ) .'" type="text" name="etSymbol" placeholder="Symbol..." onkeyup="checkForm(this, false, 1)" class="optional">';
               } else {
                 echo '<input type="text" name="etSymbol" placeholder="Symbol..." onkeyup="checkForm(this, false, 1)" class="optional">';
               }
               ?>




                <p class="label"><b>Färg</b></p>
                <div class="choose-et-colors">
                  <label for="">Bakgrundsfärg: </label>

                  <?php
                  // Check if there is a saved bgColor from previous try
                  if (isset($_GET['bgColor'])){
                    echo '<input type="color" name="etBgColor" value="'. urldecode($_GET['bgColor']) .'" id="etBgColor" onChange="updateEtPreview()">';
                  } else {
                    echo '<input type="color" name="etBgColor" value="#38AA82" id="etBgColor" onChange="updateEtPreview()">';
                  }
                  ?>

                  <label for="">Textfärg: </label>

                  <?php
                  // Check if there is a saved fgColor from previous try
                  if (isset($_GET['fgColor'])){
                    echo '<input type="color" name="etFgColor" value="'. urldecode($_GET['fgColor']) .'" id="etFgColor" onChange="updateEtPreview()">';
                  } else {
                    echo '<input type="color" name="etFgColor" value="#ffffff" id="etFgColor" onChange="updateEtPreview()">';
                  }
                  ?>

                </div>


                <p class="label"><b>Preview:</b></p>
                <div class="event-type" id="etPreview">
                  <p id="etPreviewText">Eventtyp</p>
                  <!-- <button class="btn add-btn deny">-</button> -->
                </div>

                <?php

                // If user is trying to alter an event type,
                if (isset($_GET['show_alter_event_type']) or isset($_GET['alter_event_type'])){
                  echo '<button name="alter_event_type" value="'. $_GET['id'] .'" class="btn" type="submit">Uppdatera</button>';
                } else {
                  echo '<button name="add_event_type" value="" class="btn" type="submit">Lägg till</button>';
                }
                ?>

              </form>

            </div>

          </div>

        </div>

        <?php
      }// End check if user is admin, elevkår to show event types

        ?>


        <div class="events_view">

          <!-- MODAL FOR CLICKED EVENTS -->
          <div class="modal" id="modal">
            <div class="modal-header">
              <div class="title">
                Example modal
              </div>
              <button data-close-button class="close-button" type="button" name="button">&times;</button>
            </div>
            <div class="modal-body">
              sdfjsldkjf
            </div>
          </div>
          <div id="overlay"></div>

          <div class="calendar_container" id="week-calendar-container">

            <div class="calendar_checkboxes">
              <label>Elevkårens events: </label><input id="show-elevkaren-events-checkbox-week" type="checkbox" checked>
              <label>Kommittéevents: </label><input id="show-kommitte-events-checkbox-week" type="checkbox" checked>
            </div>

            <div class="calendar_top">

              <button onclick="week_calendar_previous()">&#x02190;</button>
              <h3 id="week-week">Vecka #</h3>
              <button onclick="week_calendar_next()" >&#x02192;</button>

            </div>

            <table id="week-calendar">

              <tbody id="week-calendar-body">

              </tbody>

            </table>

          </div>

          <div class="calendar_container" id="month-calendar-container">

            <div class="calendar_checkboxes">
              <label>Elevkårens events: </label><input id="show-elevkaren-events-checkbox" type="checkbox" checked>
              <label>Kommittéevents: </label><input id="show-kommitte-events-checkbox" type="checkbox" checked>
            </div>

            <div class="calendar_top">

              <button onclick="calendar_previous()">&#x02190;</button>
              <h3 id="monthAndYear">Month And Year</h3>
              <button onclick="calendar_next()" >&#x02192;</button>

            </div>

            <table id="calendar">

              <thead>
                <tr>
                  <th class="week">v.</th>
                  <th>Mån</th>
                  <th>Tis</th>
                  <th>Ons</th>
                  <th>Tor</th>
                  <th>Fre</th>
                </tr>
              </thead>

              <tbody id="calendar-body">

              </tbody>

            </table>

          </div>

        </div>

        <?php if (!current_user_can('administrator') && !current_user_can('elevkaren')) : ?>

        <div class="row">

          <div class="box green lg">
            <h4>Eventtyper</h4>

            <div class="event-types">

              <?php

              global $wpdb;

              $enabled_event_types = $wpdb->get_results('SELECT * FROM vro_event_types WHERE status="y"');

              foreach ($enabled_event_types as $et) {
                ?>

                <div class="event-type no-click" style="background-color: <?php echo $et->bg_color; ?>" onclick="clickElement('alterEventTypeInput-<?php echo $et->id ?>')">
                  <p style="color: <?php echo $et->fg_color; ?>"><?php echo $et->name; ?></p>
                </div>

                <?php
              }

              ?>

            </div>

          </div>

        </div>

      <?php endif; ?>


        <?php

        global $wpdb;

        // Check if student is chairman for a kommitté, the function returns a list of the names of kommittéer the student is chairman at, length of 0 if none
        $chairman_names = is_chairman( get_current_user_id() );

        // Check if the student is a chairman but not in elevkaren
        $is_only_chairman = (count($chairman_names) > 0 && ! (current_user_can('administrator') || current_user_can('elevkaren')) );

        // Check that there is an event type called Kommittévents
        $kommitte_event_type = $wpdb->get_row('SELECT * FROM vro_event_types WHERE name = "Kommittéevent"');
        if ($kommitte_event_type == NULL) {
          $kommitte_event_type_exists = False;
        } else {
          $kommitte_event_type_exists = True;
          $kommitte_event_type = $kommitte_event_type->id;
        }

        // Allow adding of events if admin, elevkåren or just a chairman but there adding Kommittéevents is allowed
        if ( current_user_can('administrator') || current_user_can('elevkaren') || ($is_only_chairman && $kommitte_event_type_exists) ){
        ?>

        <div class="row">

          <div class="box green lg" id="datetime-box">

            <?php
            if ($is_only_chairman) {
              echo '<h4>Lägg till nytt kommittéevent</h4>';
            } else {
              echo '<h4>Lägg till nytt event</h4>';
            }
            ?>


            <form action="<?php echo (get_bloginfo('template_directory') . '/scripts/handle_kalender.inc.php'); ?>" method="post">

            <!-- ae stands form add event -->
            <div class="select-group" id="event-type-select-container">


              <label for="">Eventtyp: </label>
              <select name="ae_event_type" id="event-type-select">
                <?php

                // Get all events type

                $event_types = $wpdb->get_results('SELECT * FROM vro_event_types WHERE status="y"');

                if (empty($event_types)){
                  echo '<option value="none">Inga eventyper tillgängliga. Skapa en ovan</option>';
                } else {

                  foreach ($event_types as $et) {
                    echo '<option value="'. $et->id .'">'. $et->name .'</option>';
                  }

                }
                ?>

              </select>

              <?php
              // If only chairman, auto-select kommittéevents and hide the select
              if ($is_only_chairman) {
                ?>
                <script type="text/javascript">
                  document.getElementById('event-type-select-container').style.display = 'none';
                  document.getElementById('event-type-select').value = <?php echo $kommitte_event_type; ?>;
                </script>
                <?php
              }
              ?>

            </div>

            <input type="text" name="ae_name" value="" placeholder="*Eventnamn..." required>

            <input type="text" name="ae_place" value="" placeholder="Plats...">

            <div class="datetime-pickers">

              <div class="datetime-picker">

                <p><b>Start</b></p>

                <div class="date-picker" id="start-datepicker">
                  <div class="selected-date"></div>
                  <input type="hidden" name="ae_start_date" value="" id="start_hidden_input"/>

                  <div class="dates">
                    <div class="month">
                      <div class="arrows prev-mth">&lt;</div>
                      <div class="mth"></div>
                      <div class="arrows next-mth">&gt;</div>
                    </div>

                    <div class="days">

                    </div>

                  </div>
                </div>

                <div class="timepicker" data-time="00:00" id="start-timepicker">
                  <input type="hidden" name="ae_start_time" value="00:00" id="start_time_hidden_input"/>
                  <div class="hour">
                    <div class="hr-up"></div>
                    <input type="number" class="hr" value="00" />
                    <div class="hr-down"></div>
                  </div>

                  <div class="separator">:</div>

                  <div class="minute">
                    <div class="min-up"></div>
                    <input type="number" class="min" value="00" />
                    <div class="min-down"></div>
                  </div>
                </div>

              </div>

              <div class="datetime-picker">

                <p><b>Slut</b></p>

                <div class="date-picker" id="end-datepicker">
                  <div class="selected-date"></div>
                  <input type="hidden" name="ae_end_date" value="" id="end_hidden_input"/>

                  <div class="dates">
                    <div class="month">
                      <div class="arrows prev-mth">&lt;</div>
                      <div class="mth"></div>
                      <div class="arrows next-mth">&gt;</div>
                    </div>

                    <div class="days">

                    </div>

                  </div>
                </div>

                <div class="timepicker" data-time="00:00" id="end-timepicker">
                  <input type="hidden" name="ae_end_time" value="00:00" id="end_time_hidden_input"/>
                  <div class="hour">
                    <div class="hr-up"></div>
                    <input type="number" class="hr" value="00" />
                    <div class="hr-down"></div>
                  </div>

                  <div class="separator">:</div>

                  <div class="minute">
                    <div class="min-up"></div>
                    <input type="number" class="min" value="00" />
                    <div class="min-down"></div>
                  </div>
                </div>

              </div>

            </div>

            <input type="text" name="ae_host" id="host-input" placeholder="Arrangör...">
            <div id="kommitte-name-select">

              <label for="">Kommitté: </label>
              <select name="ae_host_kommitte">
                <?php

                foreach ($chairman_names as $c) {
                  echo '<option value="'. $c['id'] .'">'. $c['name'] .'</option>';
                }

                ?>
              </select>

            </div>

            <script type="text/javascript">

              // Check if the user has selected kommittéevent, if so, show an area where they can select which kommitté
              var select = document.getElementById('event-type-select');

              function showKommitteInput() {

                var selectedValue = select.options[select.selectedIndex].text;

                if (selectedValue == 'Kommittéevent' ) {
                  document.getElementById('kommitte-name-select').style.display = 'inline-block';

                  // Empty host input and hide it
                  document.getElementById('host-input').style.display = 'none';
                  document.getElementById('host-input').value = '';
                } else {
                  // Hide kommitte selection
                  document.getElementById('kommitte-name-select').style.display = 'none';

                  // Show host input
                  document.getElementById('host-input').style.display = 'block';
                }
              }

              showKommitteInput();

              select.addEventListener('change', function() {
                showKommitteInput();
              });

            </script>

            <div class="text-limited-root">
              <textarea name="ae_description" placeholder="Beskrivning av eventet..." onkeyup="checkForm(this, event_description_char_count, 300)"></textarea>
              <p id="event_description_char_count">300</p>
            </div>

            <div class="select-group" id="event-visibility-container">
              <label for="">Syns för: </label>
              <select id="event-visibility-select" name="ae_visibility">
                <option value="e">Endast elevkåren</option>
                <!-- <option value="u">Endast aktuella utskottet</option>
                <option value="m">Alla medlemmar</option>
                <option value="l">Alla inloggade</option> -->
                <option value="a">Alla</option>
              </select>
            </div>

            <?php
            // Set default to show for everyone if only chairman
            if ($is_only_chairman) {
              ?>

              <script type="text/javascript">

                // Hide the select
                document.getElementById('event-visibility-container').style.display = 'none';

                // Set to a
                document.getElementById('event-visibility-select').value = 'a';


              </script>

              <?php
            }
            ?>



            <button class="btn lg" type="submit" name="add_event">Skapa</button>

            </form>

          </div>

        </div>

        <?php

      } // End add new event check if admin
         ?>


      </section>

      <!--
      * Status View
      --------------------------------------->
      <?php
        require_once(get_template_directory() . "/parts/status-bar.php");
      ?>

    </div>

    <?php

    // Get all events
    global $wpdb;

    /*  Type arguments described:
          u - only the hosting utskott
          e - only elevkåren
          m - only members of elevkåren
          k - only specified kommitée
          l - all logged in users
          a - all visitors
    */

    if (current_user_can('administrator') || current_user_can('elevkaren') ){
        // Get all events
        $all_events = $wpdb->get_results('SELECT * FROM vro_events WHERE kommitte_host_id IS NULL ORDER BY start, name');
        $kommitte_events = $wpdb->get_results('SELECT * FROM vro_events WHERE kommitte_host_id IS NOT NULL ORDER BY start, name');

        $is_admin = 1;
    } else {
      // Only get events that has been published
      $all_events = $wpdb->get_results('SELECT * FROM vro_events WHERE visibility="a" AND kommitte_host_id IS NULL ORDER BY start, name');
      $kommitte_events = $wpdb->get_results('SELECT * FROM vro_events WHERE kommitte_host_id IS NOT NULL AND visibility="a" ORDER BY start, name');

      $is_admin = 0;
    }

    $event_type_array = array();
    $all_event_types = $wpdb->get_results('SELECT * FROM vro_event_types');

    foreach($all_event_types as $et){
      $event_type_array += array(
        $ét->id => array($et->bg_color, $et->fg_color)
      );
    }

    // Get kommittéevents in those kommittées the student is in
    $allowed_kommitte_events = array();
    $current_student = $wpdb->get_row("SELECT * FROM vro_users WHERE wpuser_id = " . get_current_user_id());

    foreach( $kommitte_events as $ke ){
      if ( $wpdb->get_row('SELECT * FROM vro_kommiteer_members WHERE kommitee_id = '. $ke->kommitte_host_id .' AND user_id = ' . $current_student->id ) != NULL ){
        array_push($allowed_kommitte_events, $ke);
      }
    }

    $json_events = json_encode($all_events);
    $json_kommitte_events = json_encode($allowed_kommitte_events);
    $json_event_types = json_encode($all_event_types)

    ?>
    <script type="text/javascript">
      var actionLink = "<?php echo (get_bloginfo('template_directory') . '/scripts/handle_kalender.inc.php'); ?>";
      var allEvents = <?php echo $json_events; ?>;
      var kommitteEvents = <?php echo $json_kommitte_events; ?>;
      var allEventTypes = <?php echo $json_event_types; ?>;
      var isAdmin = <?php echo $is_admin; ?>;
    </script>

    <script src="<?php echo get_bloginfo('template_directory') ?>/js/forms.js" charset="utf-8"></script>
    <script src="<?php echo get_bloginfo('template_directory') ?>/js/datepicker.js" charset="utf-8"></script>
    <script src="<?php echo get_bloginfo('template_directory') ?>/js/timepicker.js" charset="utf-8"></script>
    <script src="<?php echo get_bloginfo('template_directory') ?>/js/modal.js" charset="utf-8"></script>
    <script src="<?php echo get_bloginfo('template_directory') ?>/js/calendar.js" charset="utf-8"></script>
    <script src="<?php echo get_bloginfo('template_directory') ?>/js/week-calendar.js" charset="utf-8"></script>

    <script type="text/javascript">

      function toggleEvent(allToggleEvents) {
        for (var i = 0; i < allToggleEvents.length; i++) {
          allToggleEvents[i].style.display = allToggleEvents[i].style.display == 'none' ? 'block' : 'none';
        }
      }

      // Check the checkboxes and hide the events depending on selection
      var kommitteEventsCheckbox = document.getElementById('show-kommitte-events-checkbox');
      kommitteEventsCheckbox.addEventListener('change', function() {
        var allKommitteEvents = document.querySelectorAll('.kommitte-event');
        toggleEvent(allKommitteEvents);
      });

      var elevkarenEventsCheckbox = document.getElementById('show-elevkaren-events-checkbox');
      elevkarenEventsCheckbox.addEventListener('change', function() {
        var allElevkarenEvents = document.querySelectorAll('.elevkaren-event');
        toggleEvent(allElevkarenEvents);
      });

      var kommitteEventsCheckbox = document.getElementById('show-kommitte-events-checkbox-week');
      kommitteEventsCheckbox.addEventListener('change', function() {
        var allKommitteEvents = document.querySelectorAll('.kommitte-event');
        toggleEvent(allKommitteEvents);
      });

      var elevkarenEventsCheckbox = document.getElementById('show-elevkaren-events-checkbox-week');
      elevkarenEventsCheckbox.addEventListener('change', function() {
        var allElevkarenEvents = document.querySelectorAll('.elevkaren-event');
        toggleEvent(allElevkarenEvents);
      });

    </script>

    <script type="text/javascript">
      window.onload = highlightLink('link-kalender');

      function updateEtPreview() {
        document.getElementById('etPreview').style.backgroundColor = document.getElementById('etBgColor').value;
        document.getElementById('etPreview').style.color = document.getElementById('etFgColor').value;
      }
    </script>

<?php get_footer(); ?>

<?php

} // End is logged in

 ?>
