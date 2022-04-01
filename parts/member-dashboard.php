
<!-- ***********************************
* NAVBAR
*************************************-->

<?php
require_once(get_template_directory() . "/parts/member-navigation-bar.php");

// Get access to wordpress database functions
global $wpdb;

// Get current user
$user = wp_get_current_user();

 ?>

 <!-- ***********************************
 * DASHBOARD
 *************************************-->
<section id="dashboard">

  <!-- Display header and current time -->
  <div class="top-bar">
    <h2>Dashboard</h2>
    <p><?php echo current_time('d M Y, D'); ?></p>
  </div>

  <!-- Display current name, number of visselpipan suggestions and number of kommitée applications -->
  <div class="banner">
    <h3>Välkommen tillbaka <?php echo get_user_meta($user->ID, 'nickname', true); ?>!</h3>
    <img src="<?php echo get_bloginfo('template_directory') ?>/img/chatright.png" alt="" class="chatright">
    <img src="<?php echo get_bloginfo('template_directory') ?>/img/chatleft.png" alt="" class="chatleft">
  </div>

  <?php
    require_once(get_template_directory() . "/parts/dashboard-gadgets.php");
  ?>

</section>
