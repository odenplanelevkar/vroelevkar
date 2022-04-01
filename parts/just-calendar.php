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

  <div class="calendar_container">

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
        <!-- <tr>
          <td><p>1</p></td>
          <td>
            <p>2</p>
            <div class="markings">
              <div class="sale"></div>
              <div class="themeday"></div>
              <div class="sale"></div>
            </div>
          </td>
          <td><p>3</p></td>
          <td><p>4</p></td>
          <td><p>5</p></td>
          <td><p>6</p></td>
        </tr> -->
      </tbody>

    </table>

  </div>


  <?php

  // Get all events
  global $wpdb;

  if (current_user_can('administrator') || current_user_can('elevkaren') ){
      // Get all events
      $all_events = $wpdb->get_results('SELECT * FROM vro_events WHERE kommitte_host_id IS NULL');
      $kommitte_events = $wpdb->get_results('SELECT * FROM vro_events WHERE kommitte_host_id IS NOT NULL');

      $is_admin = 1;
  } else {
    // Only get events that has been published
    $all_events = $wpdb->get_results('SELECT * FROM vro_events WHERE visibility="a" AND kommitte_host_id IS NULL');
    $kommitte_events = $wpdb->get_results('SELECT * FROM vro_events WHERE kommitte_host_id IS NOT NULL AND visibility="a"');

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

  foreach( $kommitte_events as $ke ){
    if ( $wpdb->get_row('SELECT * FROM vro_kommiteer_members WHERE kommitee_id = '. $ke->kommitte_host_id .' AND user_id = ' . get_current_user_id() ) != NULL ){
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
    console.log('hello');
    var allEventTypes = <?php echo $json_event_types; ?>;
    var isAdmin = <?php echo $is_admin; ?>;
  </script>

  <script src="<?php echo get_bloginfo('template_directory') ?>/js/forms.js" charset="utf-8"></script>
  <script src="<?php echo get_bloginfo('template_directory') ?>/js/datepicker.js" charset="utf-8"></script>
  <script src="<?php echo get_bloginfo('template_directory') ?>/js/timepicker.js" charset="utf-8"></script>
  <script src="<?php echo get_bloginfo('template_directory') ?>/js/modal.js" charset="utf-8"></script>
  <script src="<?php echo get_bloginfo('template_directory') ?>/js/calendar.js" charset="utf-8"></script>

  <script type="text/javascript">
    window.onload = highlightLink('link-kalender');
  </script>
