<?php

// Show this page to all logged in users and is admin
if (! is_user_logged_in() ){
  wp_redirect( '/panel' );
} else {

// Check if this page has been called
if (!isset($_GET['p_id'])) {
  wp_redirect('/panel/projektgrupper');
  exit();
}

// Check if an id has been supplied
if (!is_numeric($_GET['p_id'])){
  header('Location: /panel/projektgrupper?status=idnan');
  exit();
}

require_once(get_template_directory() . "/scripts/helpful_functions.php");

// Get access to the wordpress database
global $wpdb;

$p_id = (int)$_GET['p_id'];

$current_projektgrupp = $wpdb->get_row('SELECT * FROM vro_projektgrupper WHERE id=' . $p_id);

if ($current_projektgrupp->visibility == 'e' && (!current_user_can('administrator') && !current_user_can('elevkaren') ) ) {
  wp_redirect('/panel/projektgrupper');
}

$current_student_id = (int)get_studentshell_by_wpuser_id( get_current_user_id() )->id;

// get all kommitee members
$all_members = $wpdb->get_results('SELECT * FROM vro_projektgrupper_members WHERE projektgrupp_id=' . $p_id . ' AND status="y"');

$is_waiting = count($wpdb->get_results('SELECT * FROM vro_projektgrupper_members WHERE user_id='. $current_student_id .' AND projektgrupp_id='. $p_id .' AND status="w"'));
$waiting_members = $wpdb->get_results('SELECT * FROM vro_projektgrupper_members where projektgrupp_id = ' . $p_id . ' AND status = "w"' );

// Check if the $current student is in this projektgrupp
if (count($wpdb->get_results('SELECT * FROM vro_projektgrupper_members WHERE user_id='. $current_student_id .' AND projektgrupp_id='. $p_id)) > 0) {
  $in_projektgrupp = true;
} else {
  $in_projektgrupp = false;
}

if (is_student_admin()) {
  $visibility_string = ($current_projektgrupp->visibility == 'e') ? '(Endast synlig för admins)' : '(Öppen för alla)';
} else {
  $visibility_string = '';
}


?>

<!-- **************************
  BANNER
**************************  -->
<script src="<?php echo get_bloginfo('template_directory') ?>/js/autocomplete.js" charset="utf-8"></script>

<div class="top-bar">
  <h2><?php echo $current_projektgrupp->name; ?> <?php echo $visibility_string; ?></h2>
  <p><?php echo current_time('d M Y, D'); ?></p>
</div>

<?php if (is_student_admin()) { ?>
<div class="banner">

  <?php
    if (count($waiting_members) == 1){
      echo "<h3>" . count($waiting_members) . " ny medlemsförfrågan!</h3>";
    } else {
      echo "<h3>" . count($waiting_members) . "nya medlemsförfrågningar!</h3>";
    }
  ?>

  <img src="<?php echo get_bloginfo('template_directory') ?>/img/chatright.png" alt="" class="chatright">
  <img src="<?php echo get_bloginfo('template_directory') ?>/img/chatleft.png" alt="" class="chatleft">
</div>
<?php } else {?>

  <div class="banner">

    <h3><?php echo $current_projektgrupp->name; ?></h3>

    <img src="<?php echo get_bloginfo('template_directory') ?>/img/chatright.png" alt="" class="chatright">
    <img src="<?php echo get_bloginfo('template_directory') ?>/img/chatleft.png" alt="" class="chatleft">
  </div>

<?php } // End is student admin  ?>

<?php

if (is_student_admin()) {
foreach ($waiting_members as $wait_member)
{
  $wm = $wpdb->get_row('SELECT * FROM vro_users WHERE id = ' . $wait_member->user_id);

  ?>
  <div class="row">

    <div class="box white lg">
      <div class="see-more">
        <h4><?php echo get_full_studentname( $wm ); ?> - <?php echo get_classname_by_id( $wm->class_id ); ?></h4>
          <div>
          <button onclick="showAnswerForm(<?php echo $wm->id ?>)">Svara &#8594;</button>
        </div>
      </div>

      <p><i>Motivering: </i><?php echo (($wait_member->motivation != NULL) ? $wait_member->motivation : 'Ingen motivering gavs.') ?></p>
      <p><i>Skolmail: </i><?php echo $wm->email; ?></p>

      <div class="answer" id="<?php echo $wm->id; ?>">

        <hr>

        <h4>Svar</h4>

        <form autocomplete="off"  autocomplete="off"  action="<?php echo (get_bloginfo('template_directory') . '/scripts/handle_projektgrupper.inc.php'); ?>" method="POST">
          <textarea name="kommitee_member_answer" placeholder="Svar..." required></textarea>
          <input name="kid" value="<?php echo $p_id; ?>" hidden>

          <button name="accept_kommitee_member" value="<?php echo $wm->id ?>" class="btn" type="submit">Godkänn</button>
          <button name="deny_kommitee_member" value="<?php echo $wm->id ?>" class="btn red" type="submit">Avböj</button>
        </form>

      </div>

    </div>

  </div>

<?php
} // ENd foreach
} // END IS admin
?>

<!-- **************************
  BASIC INFORMATION
**************************  -->
<div class="row">

  <div class="box white lg">
    <?php if (current_user_can('administrator') || current_user_can('elevkaren') ){?>

        <div class="see-more">
            <h4>Beskrivning</h4>
            <div>
              <button onclick="showAnswerForm('change_description')">Ändra info &#8594;</button>
            </div>
        </div>

        <p><?php echo $current_projektgrupp->description; ?></p>

        <div class="answer" id="change_description">

          <hr>

          <form autocomplete="off"  action="<?php echo (get_bloginfo('template_directory') . '/scripts/handle_kommiteer.inc.php'); ?>" method="POST">
            <p class="form-label">Namn på projektgruppen</p>
            <input type="text" name="projektgrupp_name" value="<?php echo $current_projektgrupp->name ?>">

            <p class="form-label">Beskrivning av projektgruppen</p>
            <div class="text-limited-root">
              <textarea name="projektgrupp_description" placeholder="Ny beskrivning..." required onkeyup="checkForm(this, event_description_char_count, 300)"></textarea>
              <p id="event_description_char_count">300</p>
            </div>
            <input name="p_id" value="<?php echo $p_id; ?>" hidden>

            <button name="change_projektgrupp_information" class="btn" type="submit">Ändra info</button>
          </form>

        </div>

    <?php } else { ?>
      <h4>Beskrivning</h4>
      <p><?php echo $current_projektgrupp->description; ?></p>
    <?php } ?>


  </div>

</div>


<?php if ( (!current_user_can( 'administrator' ) && !current_user_can( 'elevkaren' )) && !$in_projektgrupp ): ?>
  <div class="row">

    <div class="box green lg">
      <h4>Skicka en intresseförfrågan</h4>

      <form class="" action="<?php echo (get_bloginfo('template_directory') . '/scripts/handle_projektgrupper.inc.php'); ?>" method="post">
        <input type="text" name="p_id" value="<?php echo $p_id; ?>" hidden>
        <input type="text" name="student_id" value="<?php echo $current_student_id; ?>" hidden>

        <div class="text-limited-root">
          <textarea name="motivation" placeholder="Varför ska just DU få vara med i denna projektgrupp?" required onkeyup="checkForm(this, motivation_char_count, 300)"></textarea>
          <p id="motivation_char_count">300</p>
        </div>

        <button class="btn lg" type="submit" name="apply_for_projektgrupp">Skicka en intresseförfrågan!</button>
      </form>
    </div>

  </div>
<?php endif; ?>

<?php if ( $is_waiting ): ?>
  <div class="row">

    <div class="box green lg">
      <h4>En förfrågan till denna projekgrupp har skickats!</h4>

      <form class="" action="<?php echo (get_bloginfo('template_directory') . '/scripts/handle_projektgrupper.inc.php'); ?>" method="post">
        <input type="text" name="projektgrupp_id" value="<?php echo $p_id; ?>" hidden>
        <input type="text" name="student_id" value="<?php echo $current_student_id; ?>" hidden>

        <button class="btn lg" type="submit" name="leave_projektgrupp">Dra tillbaka förfrågan</button>
      </form>
    </div>

  </div>
<?php endif; ?>


<!-- **************************
  ALL MEMBERS
**************************  -->
<div class="row">

  <div class="box white lg">
    <h4>Elever i projektgruppen</h4>
    <input type="search" placeholder="Elev...">

    <div class="kommitee_members">

      <?php

      foreach($all_members as $m)
      {

        $member = $wpdb->get_row('SELECT * FROM vro_users WHERE id = ' . $m->user_id );
      ?>

      <div class="kommitee_member">

        <div>
          <p><b><?php echo get_full_studentname( $member ); ?></b></p>
          <p><?php echo ($wpdb->get_row('SELECT * FROM vro_classes WHERE id=' . $member->class_id ))->name; ?></p>

          <?php if (current_user_can('administrator') || current_user_can('elevkaren')){ ?>
            <form autocomplete="off"  class="" action="<?php echo (get_bloginfo('template_directory') . '/scripts/handle_projektgrupper.inc.php'); ?>" method="post">
              <input type="text" name="projektgrupp_id" value="<?php echo $p_id; ?>" hidden>
              <input type="text" name="student_id" value="<?php echo $member->id; ?>" hidden>

              <button type="submit" name="leave_projektgrupp" class="add-btn extra-btn deny" onclick="return confirm('Är du säker på att du vill ta bort denna medlem?');">-</button>
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

<?php if (is_student_admin()): ?>
<div class="row">
  <div class="box green lg allow-overflow" id="addNewMember">

    <h4>Lägg till elev i projektgruppen</h4>

    <form autocomplete="off"  class="" action="<?php echo (get_bloginfo('template_directory') . '/scripts/handle_projektgrupper.inc.php'); ?>" method="post">

      <div class="autocomplete">
        <input type="text" name="student_name" value="" placeholder="Elevens namn..." id="student-name-field">
        <input type="text"  name="student_id" id="student-id-field" hidden>
      </div>
      <input type="text" name="projektgrupp_id" value="<?php echo $p_id; ?>" hidden>

      <button type="submit" class="btn lg" name="add_student">Lägg till</button>

    </form>

  </div>
</div>

<div class="row">
  <form class="expand" action="<?php echo (get_bloginfo('template_directory') . '/scripts/handle_projektgrupper.inc.php'); ?>" method="post">
    <input type="text" name="p_id" value=<?php echo $p_id; ?> hidden>
    <?php if ($current_projektgrupp->visibility == 'e'){  ?>
      <button class="btn lg" type="submit" name="toggle_projektgrupp">Öppna projektgrupp för alla</button>
    <?php } else { ?>
      <button class="btn lg" type="submit" name="toggle_projektgrupp">Lås projektgrupp för icke-admins</button>
    <?php } ?>
  </form>
</div>

<div class="row">
  <form class="expand" action="<?php echo (get_bloginfo('template_directory') . '/scripts/handle_projektgrupper.inc.php'); ?>" method="post">
    <input type="text" name="p_id" value=<?php echo $p_id; ?> hidden>
    <button class="btn lg red" type="submit" name="remove_projektgrupp" onclick="event.stopPropagation(); return confirm('Är du säker på att du vill ta bort denna projektgrupp?');">Ta bort denna projektgrupp</button>
  </form>
</div>
<?php endif; ?>

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

  autocompleteFull(document.getElementById("student-name-field"), jsonstudentsfull, 'Inga elever hittades.', document.getElementById("student-id-field"));
</script>

<?php } // End check admin ?>
