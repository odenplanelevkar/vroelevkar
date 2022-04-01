<!-- KAREN ADD KARTYPES ETC -->

<div class="row">

  <div class="box green lg">

    <h4>Lägg till ny roll i elevkåren</h4>
    <form autocomplete="off" action="<?php echo (get_bloginfo('template_directory') . '/scripts/handle_karen.inc.php'); ?>" method="POST">
      <input type="text" name="position_name" value="" placeholder="Namn på rollen..." required>
      <div class="yes-no">
        <label>Kan flera studenter ha denna roll?</label>
        <input type="radio" name="is_unique" value="True" checked> <label>Ja</label>
        <input type="radio" name="is_unique" value="False"> <label>Nej</label>
      </div>

      <div class="yes-no">
        <label>Kommer denna roll vara knytet till ett utskott?</label>
        <input type="radio" name="is_linked_utskott" value="True" checked> <label>Ja</label>
        <input type="radio" name="is_linked_utskott" value="False"> <label>Nej</label>
      </div>

     <button type="submit" name="add_new_position_type" class="btn lg">Skapa ny rolltyp</button>
   </form>

  </div>

</div>

<div class="row">

  <div class="box green lg allow-overflow">

    <h4>Lägg till ny elev i roll</h4>
    <form autocomplete="off" action="<?php echo (get_bloginfo('template_directory') . '/scripts/handle_members.inc.php'); ?>" method="POST">
      <div class="select-group">

        <label for="">Rolltyp: </label>
        <select name="ae_event_type">
          <?php

          // Get all events type
          global $wpdb;

          $position_types = $wpdb->get_results('SELECT * FROM vro_position_types');

          if (empty($position_types)){
            echo '<option value="none">Inga rolltyper skapade.</option>';
          } else {

            foreach ($position_types as $pt) {
              echo '<option value="'. $pt->id .'">'. $pt->name .'</option>';
            }

          }
          ?>

        </select>
      </div>

      <div class="autocomplete">
        <input type="text" name="student_name" id="student_name" value="" placeholder="Namn..." required>
      </div>


     <button type="submit" name="add_new_user" class="btn lg">Lägg till elev i utskott</button>
   </form>

   <?php

   global $wpdb;

   // Get the number of all members
   $all_students = get_users(array(
     'meta_key' => 'class_id'
   ));

   // Get first and last name from every student
   $first_last_array = array();
   foreach($all_students as $s){
     array_push($first_last_array, get_user_meta( $s->ID, 'nickname', true));
   }

   echo '<script type="text/javascript">';
   echo 'var jsonstudents = ' . json_encode($first_last_array);
   echo '</script>'

   ?>

   <script>
   // var jsonstudents = getArrayFromColumn(jsonstudents, 'display_name');

   autocomplete(document.getElementById("student_name"), jsonstudents, 'Inga elever hittades');
   </script>

  </div>

</div>
