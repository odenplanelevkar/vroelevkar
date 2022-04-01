<?php

// Show this page to all logged in users
if (! is_user_logged_in() ){
  wp_redirect( '/' );
} else {

// Check if this page has been called
if (!isset($_GET['k_id'])) {
  wp_redirect('/panel/kommiteer');
  exit();
}

// Check if an id has been supplied
if (!is_numeric($_GET['k_id'])){
  header('Location: /panel/kommiteer?status=idnan');
  exit();
}

// Get access to the wordpress database
global $wpdb;

$k_id = (int)$_GET['k_id'];

$current_kommitee = $wpdb->get_row('SELECT * FROM vro_kommiteer WHERE id=' . $k_id);

$current_student_id = get_studentshell_id( get_current_user_id() );

// Get chairman
$chairman_id = $wpdb->get_var('SELECT chairman FROM vro_kommiteer WHERE id = ' . $k_id);
$chairman = $wpdb->get_row('SELECT * FROM vro_users WHERE id = ' . $chairman_id);

// Check if the logged in user is the chairman
$is_chairman = ($current_student_id == $chairman_id);

// Check if user already has applied / is in
$is_related_to_kommitte = count($wpdb->get_results('SELECT * FROM vro_kommiteer_members WHERE user_id='. $current_student_id .' AND kommitee_id='. $k_id .''));
$is_waiting = count($wpdb->get_results('SELECT * FROM vro_kommiteer_members WHERE user_id='. $current_student_id .' AND kommitee_id='. $k_id .' AND status="w"'));

// get all kommitee members
$all_members = $wpdb->get_results('SELECT * FROM vro_kommiteer_members WHERE kommitee_id=' . $k_id . ' AND status="y"');

?>

<!-- **************************
  BANNER
**************************  -->
<?php

$waiting_members = $wpdb->get_results('SELECT * FROM vro_kommiteer_members where kommitee_id = ' . $k_id . ' AND status = "w"' );

?>

<script src="<?php echo get_bloginfo('template_directory') ?>/js/autocomplete.js" charset="utf-8"></script>

<div class="top-bar">
  <h2><?php echo $current_kommitee->name; ?></h2>
  <p><?php echo current_time('d M Y, D'); ?></p>
</div>

<?php

if (current_user_can('administrator') || current_user_can('elevkaren') || $is_chairman ){

?>

<div class="banner">

  <!-- Change the message depending on singular or plural application number -->
  <?php if (count($waiting_members) == 1){ ?>
    <h3><?php echo count($waiting_members); ?> ny medlemsförfrågan!</h3>
  <?php } else { ?>
    <h3><?php echo count($waiting_members); ?> nya medlemsförfrågningar!</h3>
  <?php } ?>

  <img src="<?php echo get_bloginfo('template_directory') ?>/img/chatright.png" alt="" class="chatright">
  <img src="<?php echo get_bloginfo('template_directory') ?>/img/chatleft.png" alt="" class="chatleft">
</div>

<?php

// Add a new row and box for every suggestion

foreach ($waiting_members as $wait_member)
{
  $wm = $wpdb->get_row('SELECT * FROM vro_users WHERE id = ' . $wait_member->user_id);

  ?>
  <div class="row">

    <div class="box white lg">
      <div class="see-more">
        <h4><?php echo get_full_studentname( $wm ); ?></h4>
          <div>
          <button onclick="showAnswerForm(<?php echo $wm->id ?>)">Svara &#8594;</button>
        </div>
      </div>

      <div class="answer" id="<?php echo $wm->id; ?>">

        <hr>

        <h4>Svar</h4>

        <form autocomplete="off"  autocomplete="off"  action="<?php echo (get_bloginfo('template_directory') . '/scripts/handle_kommiteer.inc.php'); ?>" method="POST">
          <textarea name="kommitee_member_answer" placeholder="Svar..."></textarea>
          <input name="kid" value="<?php echo $k_id; ?>" hidden>

          <button name="accept_kommitee_member" value="<?php echo $wm->id ?>" class="btn" type="submit">Godkänn</button>
          <button name="deny_kommitee_member" value="<?php echo $wm->id ?>" class="btn red" type="submit">Avböj</button>
        </form>

      </div>

    </div>

  </div>

<?php
} // ENd foreach


} // End is admin, elevkår or chairman
?>

<!-- **************************
  BASIC INFORMATION
**************************  -->
<div class="row">

  <div class="box white lg">
    <?php if (current_user_can('administrator') || current_user_can('elevkaren') || $is_chairman ){?>

        <div class="see-more">
            <h4>Beskrivning</h4>
            <div>
              <button onclick="showAnswerForm('change_description')">Ändra kommittéinfo &#8594;</button>
            </div>
        </div>

        <p><?php echo $current_kommitee->description; ?></p>

        <div class="answer" id="change_description">

          <hr>

          <form autocomplete="off"  action="<?php echo (get_bloginfo('template_directory') . '/scripts/handle_kommiteer.inc.php'); ?>" method="POST">
            <p class="form-label">Namn på kommittén</p>
            <input type="text" name="kommitte_name" value="<?php echo $current_kommitee->name ?>">

            <p class="form-label">Beskrivning av kommittén</p>
            <div class="text-limited-root">
              <textarea name="kommitee_description" placeholder="Ny beskrivning..." required onkeyup="checkForm(this, event_description_char_count, 600)"></textarea>
              <p id="event_description_char_count">600</p>
            </div>
            <input name="k_id" value="<?php echo $k_id; ?>" hidden>

            <button name="change_kommitte_information" class="btn" type="submit">Ändra kommittéinfo</button>
          </form>

        </div>

    <?php } else { ?>
      <h4>Beskrivning</h4>
      <p><?php echo $current_kommitee->description; ?></p>
    <?php } ?>


  </div>

</div>

<?php  if (current_user_can('administrator') || current_user_can('elevkaren') || $is_chairman ) { ?>
  <div class="kommitee-row admin">
<?php } else { ?>
  <div class="kommitee-row">
<?php } ?>


  <div class="box white allow-overflow" id="chairman">
      <!-- <?php echo get_avatar( $chairman_id ); ?> -->
      <h4><?php echo get_full_studentname( $chairman ); ?></h4>
      <p>Ordförande</p>

      <?php  if (current_user_can('administrator') || current_user_can('elevkaren') || $is_chairman ) : ?>
      <button onclick="showAnswerForm('change_chairman')">Ändra ordförande &#8594;</button>

      <div class="answer allow-overflow" id="change_chairman">

        <hr>

        <form autocomplete="off"  action="<?php echo (get_bloginfo('template_directory') . '/scripts/handle_kommiteer.inc.php'); ?>" method="POST">
          <div class="autocomplete">
              <input type="text" name="new_chairman_name" value="" placeholder="Ny ordförande..." id="chairman-field">
              <input type="text"  name="chairman_id" id="chairman-id-field" hidden>
          </div>

          <input name="k_id" value="<?php echo $k_id; ?>" hidden>

          <button name="change_chairman" class="btn lg" type="submit">Ändra ordförande</button>
        </form>

      </div>
    <?php endif; ?>

  </div>

  <?php if (current_user_can('administrator') || current_user_can('elevkaren') || $is_chairman ){ ?>
  <div class="box white alert" id="add_member">
    <a href="#addNewMember" class="add-btn lg">+</a>
    <h5>Lägg till medlem</h5>
  </div>
<?php } ?>

  <div class="box green" id="send_message">
    <h3>Övrig information</h3>

    <?php if (current_user_can('administrator') || current_user_can('elevkaren') || $is_chairman){ ?>
      <form autocomplete="off" action="<?php echo (get_bloginfo('template_directory') . '/scripts/handle_kommiteer.inc.php'); ?>" method="post">
        <textarea name="information" placeholder="Här kan du som ordförande skriva in extra information såsom sociala medier etc."><?php echo ($current_kommitee->information != NULL) ? str_replace("<br>", "", $current_kommitee->information) : ""; ?></textarea>

        <input type="text" name="k_id" value="<?php echo $k_id; ?>" hidden>

        <button class="btn" type="submit" name="update_information">Uppdatera informationen</button>
      </form>
    <?php } else { ?>
      <?php echo ($current_kommitee->information != NULL && $current_kommitee->information != "") ? $current_kommitee->information : "Ingen extrainformation har specifierats av ordföranden."; ?>
    <?php } ?>
  </div>

</div>

<!-- Show join kommittée higher up and leave lower down -->
<?php if (!$is_related_to_kommitte) : ?>

<div class="row">

  <div class="box green lg">

    <h4>Ansök till denna kommitté</h4>

    <form autocomplete="off"  class="" action="<?php echo (get_bloginfo('template_directory') . '/scripts/handle_kommiteer.inc.php'); ?>" method="post">


      <input type="text" name="kommitte_id" value="<?php echo $k_id; ?>" hidden>
      <input type="text" name="student_id" value="<?php echo $current_student_id; ?>" hidden>

      <button class="btn lg" type="submit" name="apply_for_kommitte">Klicka för att skicka en ansökan!</button>

    </form>
  </div>

</div>

<?php endif; ?>

<!-- **************************
  ALL MEMBERS
**************************  -->
<div class="row">

  <div class="box white lg">
    <h4>Medlemmar</h4>

    <?php // Show error messages

    if (isset($_GET['leave_kommitte'])) {

      $kom_check = $_GET['leave_kommitte'];

      if ($kom_check == 'ischairman') {
        echo '<p class="error">Du måste göra någon annan till ordförande innan du kan lämna kommittéen!</p>';
      }

    }

   ?>

    <!-- <input type="search" placeholder="Medlem..."> -->

    <div class="kommitee_members">

      <?php

      foreach($all_members as $m)
      {

        $member = $wpdb->get_row('SELECT * FROM vro_users WHERE id = ' . $m->user_id);
      ?>

        <div class="kommitee_member">
          <div>
            <p><b><?php echo get_full_studentname( $member ); ?></b></p>
            <?php if (current_user_can('administrator') || current_user_can('elevkaren') || $is_chairman ): ?>
              <p><?php echo $member->email; ?></p>
            <?php endif; ?>

            <p><?php echo ($wpdb->get_row('SELECT * FROM vro_classes WHERE id=' . $member->class_id ))->name; ?></p>




            <?php if (current_user_can('administrator') || current_user_can('elevkaren') || $is_chairman ){ ?>
              <form autocomplete="off"  class="" action="<?php echo (get_bloginfo('template_directory') . '/scripts/handle_kommiteer.inc.php'); ?>" method="post">
                <input type="text" name="kommitte_id" value="<?php echo $k_id; ?>" hidden>
                <input type="text" name="student_id" value="<?php echo $member->id; ?>" hidden>

                <button type="submit" name="leave_kommitte" class="add-btn extra-btn deny" onclick="return confirm('Är du säker på att du vill ta bort denna medlem?');">-</button>
              </form>
            <?php } ?>
          </div>
        </div>

      <?php } ?>

    </div>
  </div>

</div>



<!-- **************************
  ADD NEW MEMBER
**************************  -->

<?php if (current_user_can('administrator') || current_user_can('elevkaren') || $is_chairman) : ?>
<div class="row">
  <div class="box green lg allow-overflow" id="addNewMember">

    <h4>Lägg till elev i kommittén</h4>

    <form autocomplete="off"  class="" action="<?php echo (get_bloginfo('template_directory') . '/scripts/handle_kommiteer.inc.php'); ?>" method="post">

      <div class="autocomplete">
        <input type="text" name="student_name" value="" placeholder="Elevens namn..." id="student-name-field">
        <input type="text"  name="student_id" id="student-id-field" hidden>
      </div>
      <input type="text" name="kommitte_id" value="<?php echo $k_id; ?>" hidden>

      <button type="submit" class="btn lg" name="add_member">Lägg till</button>

    </form>

  </div>
</div>
<?php endif; ?>

<?php if ($is_related_to_kommitte || $is_waiting) : ?>

<div class="row">

  <div class="box green lg">

    <?php
    if ($is_waiting) {
      echo '<h4>Dra tillbaka din kommittéförfrågan</h4>';
    }
    elseif ($is_related_to_kommitte){
      echo '<h4>Gå ut ur denna kommitté</h4>';
    }
    ?>

    <form autocomplete="off"  class="" action="<?php echo (get_bloginfo('template_directory') . '/scripts/handle_kommiteer.inc.php'); ?>" method="post">


      <input type="text" name="kommitte_id" value="<?php echo $k_id; ?>" hidden>
      <input type="text" name="student_id" value="<?php echo $current_student_id; ?>" hidden>

      <?php
      if ($is_waiting) {
        echo '<button class="btn lg red" type="submit" name="leave_kommitte" onclick="return confirm(\'Är du säker på att du vill dra tillbaka din förfrågan?\');">Klicka för att dra tillbaka din ansökan</button>';
      }
      elseif ($is_related_to_kommitte){
        echo '<button class="btn lg red" type="submit" name="leave_kommitte" onclick="return confirm(\'Är du säker på att du vill gå ut ur kommittén?\');">Klicka för att gå ut ur kommittén</button>';
      }

      ?>

    </form>
  </div>

</div>

<?php endif; ?>

<?php
// Only show the event types for admins
if (current_user_can('administrator') || current_user_can('elevkaren') ){
?>



<div class="row">
  <form autocomplete="off"  class="expand" action="<?php echo (get_bloginfo('template_directory') . '/scripts/handle_kommiteer.inc.php'); ?>" method="post">
    <input type="text" name="k_id" value=<?php echo $k_id; ?> hidden>
    <button class="btn lg red" type="submit" name="remove_kommitte" onclick="event.stopPropagation(); return confirm('Är du säker på att du vill ta bort denna kommitté?');">Ta bort denna kommitté</button>
  </form>
</div>

<?php } // End check admin ?>

<?php

global $wpdb;

$all_students = $wpdb->get_results("SELECT * FROM vro_users WHERE class_id IS NOT NULL");

// Get a full array
$full_student_array = array();
foreach ($all_students as $s) {
  array_push($full_student_array, get_full_student_array( $s ));
}

echo '<script type="text/javascript">';
echo 'var jsonstudentsall = ' . json_encode($first_last_array_full). ';';
echo 'var jsonstudentsfull = ' . json_encode($full_student_array) . ';';
echo '</script>'

?>

<script type="text/javascript">

  //autocomplete(document.getElementById("chairman-field"), jsonstudentsall, 'Inga elever hittades.');
  autocompleteFull(document.getElementById("chairman-field"), jsonstudentsfull, 'Inga elever hittades.', document.getElementById("chairman-id-field"));
  autocompleteFull(document.getElementById("student-name-field"), jsonstudentsfull, 'Inga elever hittades.', document.getElementById("student-id-field"));
</script>


<script src="<?php echo get_bloginfo('template_directory') ?>/js/datepicker.js" charset="utf-8"></script>

<?php }  ?>
