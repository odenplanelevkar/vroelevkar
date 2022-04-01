// foreach($all_events as $event) {
//   $event->name;
//   $td_id = explode(' ', $event->start)[0];
//
//   $event_type = $wpdb->get_row('SELECT * FROM vro_event_types WHERE id=' . $event->type);
//
//   ?>
//
//   <script type="text/javascript">
//     add_event_to_calendar("<?php echo $td_id; ?>", "<?php echo $event->name; ?>", "<?php echo $event_type->bg_color ?>", "<?php echo $event_type->fg_color ?>");
//   </script>
//
//   <?php
//
// }
