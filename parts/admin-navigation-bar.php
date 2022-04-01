<!--
* Admin Navbar
--------------------------------------->
<?php

  global $wpdb;

  // Get the number of current suggestions
  $visselpipan_suggestions = count($wpdb->get_results('SELECT * FROM vro_visselpipan WHERE status = "w"'));

  // Get the number of current kommitee aplications
  $kommitee_applications = count($wpdb->get_results('SELECT * FROM vro_kommiteer WHERE status = "w"'));

  // Get all users that are waiting to become members of kåren
  $member_applications = count($wpdb->get_results('SELECT * FROM vro_users WHERE status = "w"'));

  // Projektgrupper applications
  $projektgrupp_applications = count($wpdb->get_results('SELECT * FROM vro_projektgrupper_members WHERE status = "w"'));

 ?>

<section id="navigation-bar" class="closed">

  <div class="nav-header">
    <a href="/"><img src="<?php echo get_bloginfo('template_directory') ?>/img/vitfluga.png" alt=""></a>
    <a href="/panel/dashboard"><h2>Admin</h2></a>

    <button class="icon" onclick="toggleNavbar('navigation-bar')">
      <img src="<?php echo get_bloginfo('template_directory') ?>/img/menu.png" alt="">
    </button>

  </div>

  <nav id="navbar-nav">

    <a href="/panel/dashboard/" class="nav-item active" id="link-dashboard">
      <img src="<?php echo get_bloginfo('template_directory') ?>/img/hemsida.png" alt="" class="nav-icon">
      <p>Dashboard</p>
    </a>

    <a href="/panel/visselpipan/" class="nav-item" id="link-visselpipan">

      <!-- Check if there are any new visselpipan suggestions, if so -> add a notification circle -->
      <?php if ($visselpipan_suggestions > 0) { ?>
      <div class="notification">
        <img src="<?php echo get_bloginfo('template_directory') ?>/img/chat.png" alt="" class="nav-icon ">
        <span><?php echo $visselpipan_suggestions; ?></span>
      </div>
      <?php } else { ?>
        <img src="<?php echo get_bloginfo('template_directory') ?>/img/chat.png" alt="" class="nav-icon ">
      <?php } ?>

      <p>Visselpipan</p>
    </a>

    <a href="/panel/kommiteer/" class="nav-item" id="link-kommiteer">

      <!-- Check if there are any new kommitée applications, if so -> add a notification circle -->
      <?php if ($kommitee_applications > 0) { ?>
      <div class="notification">
        <img src="<?php echo get_bloginfo('template_directory') ?>/img/folder.png" alt="" class="nav-icon">
        <span><?php echo $kommitee_applications; ?></span>
      </div>
      <?php } else { ?>
        <img src="<?php echo get_bloginfo('template_directory') ?>/img/folder.png" alt="" class="nav-icon">
      <?php } ?>

      <p>Kommittéer</p>
    </a>

    <a href="/panel/kalender/" class="nav-item" id="link-kalender">
      <img src="<?php echo get_bloginfo('template_directory') ?>/img/calendar.png" alt="" class="nav-icon">
      <p>Kalender</p>
    </a>

    <a href="/panel/klasspokalen/" class="nav-item" id="link-klasspokalen">
      <img src="<?php echo get_bloginfo('template_directory') ?>/img/trophy.png" alt="" class="nav-icon">
      <p>Klasspokalen</p>
    </a>

    <a href="/panel/karen/" class="nav-item" id="link-karen">
      <img src="<?php echo get_bloginfo('template_directory') ?>/img/bowtie.png" alt="" class="nav-icon">
      <p>Kåren</p>
    </a>

    <a href="/panel/medlemmar/" class="nav-item" id="link-medlemmar">
      <!-- Check if there are any new memebr suggestions, if so -> add a notification circle -->
      <?php if ($member_applications > 0) { ?>
      <div class="notification">
        <img src="<?php echo get_bloginfo('template_directory') ?>/img/members.png" alt="" class="nav-icon">
        <span><?php echo $member_applications; ?></span>
      </div>
      <?php } else { ?>
        <img src="<?php echo get_bloginfo('template_directory') ?>/img/members.png" alt="" class="nav-icon">
      <?php } ?>

        <p>Medlemmar</p>
    </a>

    <a href="/panel/projektgrupper/" class="nav-item" id="link-projektgrupper">

      <?php if ($projektgrupp_applications > 0) { ?>
      <div class="notification">
        <img src="<?php echo get_bloginfo('template_directory') ?>/img/edit.png" alt="" class="nav-icon">
        <span><?php echo $projektgrupp_applications; ?></span>
      </div>
      <?php } else { ?>
        <img src="<?php echo get_bloginfo('template_directory') ?>/img/edit.png" alt="" class="nav-icon">
      <?php } ?>

      <p>Projektgrupper</p>
    </a>

    <a href="/panel/karkortet" class="nav-item" id="link-karkortet">
      <img src="<?php echo get_bloginfo('template_directory') ?>/img/karkortet-icon.png" alt="" class="nav-icon">
      <p>Kårkortet</p>
    </a>

    <a href="game" class="nav-item" id="link-game">
      <img src="<?php echo get_bloginfo('template_directory') ?>/img/cursecircle.png" alt="" class="nav-icon">
      <p>Textäventyr</p>
    </a>

    <a href="/panel/arkiv" class="nav-item" id="link-arkiv">
      <img src="<?php echo get_bloginfo('template_directory') ?>/img/folder.png" alt="" class="nav-icon">
      <p>Arkiv</p>
    </a>

    <a href="/panel/installningar/" class="nav-item" id="link-installningar">
      <img src="<?php echo get_bloginfo('template_directory') ?>/img/cog.png" alt="" class="nav-icon">
      <p>Inställningar</p>
    </a>

    <a href="<?php echo wp_logout_url( '/' ); ?>" class="nav-item" id="link-logout">
      <img src="<?php echo get_bloginfo('template_directory') ?>/img/logout-icon.png" alt="" class="nav-icon">
      <p>Logga ut</p>
    </a>

  </nav>

  <div class="drive">

    <img src="<?php echo get_bloginfo('template_directory') ?>/img/protocolfolder.png" alt="">
    <p>Öppna <strong>DRIVE</strong> för att se de senaste protokollen!</p>

    <a href="https://drive.google.com/drive/folders/0B-UL6CSzT9I0bXUzUXVCRFNVZVk?usp=sharing" target="_blank" class="btn sm">Drive</a>

  </div>

</section>

<script>
  function toggleNavbar(id) {
    var navbar = document.getElementById(id);

    navbar.classList = (navbar.classList == 'closed') ? 'open' : 'closed';
  }
</script>
