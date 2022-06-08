<?php

get_header();

require_once(get_template_directory() . "/scripts/helpful_functions.php");


$s_chairman = $wpdb->get_row('SELECT * FROM vro_styrelsen WHERE position_name = "Ordförande" ');
$vroelevkar_chairman = get_full_studentname_from_id( $s_chairman->student );

$s_vice_chairman = $wpdb->get_row('SELECT * FROM vro_styrelsen WHERE position_name = "Vice Ordförande" ');
$vroelevkar_vice_chairman = get_full_studentname_from_id( $s_vice_chairman->student );

?>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<!-- <script type="text/javascript">
  Swal.fire(
    'Förändring av hemsidan',
    'Just nu håller vi på med att förbättra hemsidan, därför kommer inte du kunna logga in och använda den som vanligt. Den väntas åter bli aktiv igen efter påsklovet!',
    'info'
  )
</script> -->

<header id="v-header">

    <div class="video-container">

      <video id="video-background" autoplay muted loop poster="<?php echo get_bloginfo('template_directory') . '/img/vrgkampenOptimized.jpg'?>"></video>

    </div>

    <div class="video-overlay"></div>

    <div class="video-content">
      <svg id="logo" width="727" height="114" viewBox="0 0 727 114" fill="none" xmlns="http://www.w3.org/2000/svg">
        <mask id="path-1-outside-1" maskUnits="userSpaceOnUse" x="0.191986" y="0.47998" width="727" height="113" fill="black">
        <rect fill="white" x="0.191986" y="0.47998" width="727" height="113"/>
        <path d="M55.592 108.008C46.28 108.008 37.784 105.848 30.104 101.528C22.424 97.112 16.328 91.016 11.816 83.24C7.39999 75.368 5.19199 66.536 5.19199 56.744C5.19199 46.952 7.39999 38.168 11.816 30.392C16.328 22.52 22.424 16.424 30.104 12.104C37.784 7.68798 46.28 5.47998 55.592 5.47998C65 5.47998 73.544 7.68798 81.224 12.104C88.904 16.424 94.952 22.472 99.368 30.248C103.784 38.024 105.992 46.856 105.992 56.744C105.992 66.632 103.784 75.464 99.368 83.24C94.952 91.016 88.904 97.112 81.224 101.528C73.544 105.848 65 108.008 55.592 108.008ZM55.592 96.632C62.6 96.632 68.888 95 74.456 91.736C80.12 88.472 84.536 83.816 87.704 77.768C90.968 71.72 92.6 64.712 92.6 56.744C92.6 48.68 90.968 41.672 87.704 35.72C84.536 29.672 80.168 25.016 74.6 21.752C69.032 18.488 62.696 16.856 55.592 16.856C48.488 16.856 42.152 18.488 36.584 21.752C31.016 25.016 26.6 29.672 23.336 35.72C20.168 41.672 18.584 48.68 18.584 56.744C18.584 64.712 20.168 71.72 23.336 77.768C26.6 83.816 31.016 88.472 36.584 91.736C42.248 95 48.584 96.632 55.592 96.632Z"/>
        <path d="M154.539 6.63198C165.483 6.63198 174.939 8.69598 182.907 12.824C190.971 16.856 197.115 22.664 201.339 30.248C205.659 37.832 207.819 46.76 207.819 57.032C207.819 67.304 205.659 76.232 201.339 83.816C197.115 91.304 190.971 97.064 182.907 101.096C174.939 105.032 165.483 107 154.539 107H123.291V6.63198H154.539ZM154.539 96.2C167.499 96.2 177.387 92.792 184.203 85.976C191.019 79.064 194.427 69.416 194.427 57.032C194.427 44.552 190.971 34.808 184.059 27.8C177.243 20.792 167.403 17.288 154.539 17.288H136.395V96.2H154.539Z"/>
        <path d="M238.208 17.288V50.84H274.784V61.64H238.208V96.2H279.104V107H225.104V6.48798H279.104V17.288H238.208Z"/>
        <path d="M377.844 107H364.74L312.036 27.08V107H298.932V6.48798H312.036L364.74 86.264V6.48798H377.844V107Z"/>
        <path d="M467.286 36.008C467.286 44.36 464.406 51.32 458.646 56.888C452.982 62.36 444.294 65.096 432.582 65.096H413.286V107H400.182V6.63198H432.582C443.91 6.63198 452.502 9.36798 458.358 14.84C464.31 20.312 467.286 27.368 467.286 36.008ZM432.582 54.296C439.878 54.296 445.254 52.712 448.71 49.544C452.166 46.376 453.894 41.864 453.894 36.008C453.894 23.624 446.79 17.432 432.582 17.432H413.286V54.296H432.582Z"/>
        <path d="M496.676 96.344H531.812V107H483.572V6.63198H496.676V96.344Z"/>
        <path d="M605.057 84.68H561.281L553.217 107H539.393L575.681 7.20798H590.801L626.945 107H613.121L605.057 84.68ZM601.313 74.024L583.169 23.336L565.025 74.024H601.313Z"/>
        <path d="M721.672 107H708.568L655.864 27.08V107H642.76V6.48798H655.864L708.568 86.264V6.48798H721.672V107Z"/>
        </mask>
        <path d="M55.592 108.008C46.28 108.008 37.784 105.848 30.104 101.528C22.424 97.112 16.328 91.016 11.816 83.24C7.39999 75.368 5.19199 66.536 5.19199 56.744C5.19199 46.952 7.39999 38.168 11.816 30.392C16.328 22.52 22.424 16.424 30.104 12.104C37.784 7.68798 46.28 5.47998 55.592 5.47998C65 5.47998 73.544 7.68798 81.224 12.104C88.904 16.424 94.952 22.472 99.368 30.248C103.784 38.024 105.992 46.856 105.992 56.744C105.992 66.632 103.784 75.464 99.368 83.24C94.952 91.016 88.904 97.112 81.224 101.528C73.544 105.848 65 108.008 55.592 108.008ZM55.592 96.632C62.6 96.632 68.888 95 74.456 91.736C80.12 88.472 84.536 83.816 87.704 77.768C90.968 71.72 92.6 64.712 92.6 56.744C92.6 48.68 90.968 41.672 87.704 35.72C84.536 29.672 80.168 25.016 74.6 21.752C69.032 18.488 62.696 16.856 55.592 16.856C48.488 16.856 42.152 18.488 36.584 21.752C31.016 25.016 26.6 29.672 23.336 35.72C20.168 41.672 18.584 48.68 18.584 56.744C18.584 64.712 20.168 71.72 23.336 77.768C26.6 83.816 31.016 88.472 36.584 91.736C42.248 95 48.584 96.632 55.592 96.632Z" stroke="white" stroke-width="10" mask="url(#path-1-outside-1)"/>
        <path d="M154.539 6.63198C165.483 6.63198 174.939 8.69598 182.907 12.824C190.971 16.856 197.115 22.664 201.339 30.248C205.659 37.832 207.819 46.76 207.819 57.032C207.819 67.304 205.659 76.232 201.339 83.816C197.115 91.304 190.971 97.064 182.907 101.096C174.939 105.032 165.483 107 154.539 107H123.291V6.63198H154.539ZM154.539 96.2C167.499 96.2 177.387 92.792 184.203 85.976C191.019 79.064 194.427 69.416 194.427 57.032C194.427 44.552 190.971 34.808 184.059 27.8C177.243 20.792 167.403 17.288 154.539 17.288H136.395V96.2H154.539Z" stroke="white" stroke-width="10" mask="url(#path-1-outside-1)"/>
        <path d="M238.208 17.288V50.84H274.784V61.64H238.208V96.2H279.104V107H225.104V6.48798H279.104V17.288H238.208Z" stroke="white" stroke-width="10" mask="url(#path-1-outside-1)"/>
        <path d="M377.844 107H364.74L312.036 27.08V107H298.932V6.48798H312.036L364.74 86.264V6.48798H377.844V107Z" stroke="white" stroke-width="10" mask="url(#path-1-outside-1)"/>
        <path d="M467.286 36.008C467.286 44.36 464.406 51.32 458.646 56.888C452.982 62.36 444.294 65.096 432.582 65.096H413.286V107H400.182V6.63198H432.582C443.91 6.63198 452.502 9.36798 458.358 14.84C464.31 20.312 467.286 27.368 467.286 36.008ZM432.582 54.296C439.878 54.296 445.254 52.712 448.71 49.544C452.166 46.376 453.894 41.864 453.894 36.008C453.894 23.624 446.79 17.432 432.582 17.432H413.286V54.296H432.582Z" stroke="white" stroke-width="10" mask="url(#path-1-outside-1)"/>
        <path d="M496.676 96.344H531.812V107H483.572V6.63198H496.676V96.344Z" stroke="white" stroke-width="10" mask="url(#path-1-outside-1)"/>
        <path d="M605.057 84.68H561.281L553.217 107H539.393L575.681 7.20798H590.801L626.945 107H613.121L605.057 84.68ZM601.313 74.024L583.169 23.336L565.025 74.024H601.313Z" stroke="white" stroke-width="10" mask="url(#path-1-outside-1)"/>
        <path d="M721.672 107H708.568L655.864 27.08V107H642.76V6.48798H655.864L708.568 86.264V6.48798H721.672V107Z" stroke="white" stroke-width="10" mask="url(#path-1-outside-1)"/>
        </svg>

        <hr id="logo-sepparator">

        <?php if (is_user_logged_in()) {
          echo '<a class="btn" href="'. wp_logout_url( '/' ) .'">Logga ut</a>';
        } else {
          echo '<a class="btn" id="front-btn" href="/register">Registrera</a>';
          echo '<a class="btn" href="/wp-login.php">Logga in</a>';
        }
        ?>

    </div>


</header>

<!-- LAUNCH -->
<section id="notice" class="green">

  <div class="news">
    <h2>Nyheter!!</h2>
    <p>Äntligen är Kommunikationsutskottets textäventyr "Curse of the Circle" här! Nu är även hela Kapitel 3 - The Bog släppt. Så vad väntar du på? Börja spela!</p>
  </div>

  <a href="/game" class="btn lg">Till Spelet</a>
</section>

<section id="about-us">

  <div class="about-us-text">
    <h2>Om Oss</h2>
    <p>Viktor Rydberg Odenplans Elevkår är Viktor Rydberg Gymnasium Odenplans största förening, till vilken nästan 100 % av skolans elever aktivt valt att ansluta sig. Elevkåren samlar och engagerar elever från alla årskurser, program och klasser för att tillsammans förgylla skoltiden för kårens medlemmar. För oss i kåren är målet enkelt: medlemmarna ska leva sin gymnasietid, inte bara överleva den.</p>
    <p>Vår vision är kort och konkret: Viktor Rydberg Odenplans Elevkår ska vara den bästa elevkåren för kårens medlemmar. Detta åstadkommer vi genom flertalet aktiviteter och arrangemang inom kategorierna service, event, bildning och lobbying.</p>
    <p>Vår elevkår startades år 2012 av Hedda Tingskog. Kårens nuvarande Ordförande är <?php echo $vroelevkar_chairman; ?><?php echo ($vroelevkar_vice_chairman != NULL) ? " och Vice Ordförande är $vroelevkar_vice_chairman" : ""; ?>.</p>
  </div>

  <a href="/om-karen" class="btn lg">Se elevkåren</a>
</section>

<section id="matsedel" class="green">

  <!-- <div class="box white lg food">
    <h3>Matsedeln</h3>
    <iframe class="matsedeln" name="matsedeln-iframe" id="matsedeln-iframe" src="https://mpi.mashie.com/public/menu/kk+vrvasastan/4465fa56?country=se" width="" height=""> </iframe>
  </div> -->

  <h2>Bussar</h2>

  <?php

  $odenplan_frescati_url = 'https://api.sl.se/api2/TravelplannerV3_1/trip.json?key=471f7b533072422587300653963192ad&originExtId=9117&destExtId=9203&products=8&lines=50';
  $curl = curl_init();
  curl_setopt($curl, CURLOPT_URL, $odenplan_frescati_url);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
  $result = curl_exec($curl);
  curl_close($curl);

  $json_odenplan_frescati = json_decode($result, true);

  $frescait_odenplan_url = 'https://api.sl.se/api2/TravelplannerV3_1/trip.json?key=471f7b533072422587300653963192ad&originExtId=9203&destExtId=9117&products=8&lines=50';
  $curl = curl_init();
  curl_setopt($curl, CURLOPT_URL, $frescait_odenplan_url);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
  $result = curl_exec($curl);
  curl_close($curl);

  $json_frescati_odenplan = json_decode($result, true);

  ?>

  <div class="bow busses">

    <div class="box white hl frescati">
      <div class="see-more">
        <h3>Nästa buss Frescati</h3>
        <button onclick="toggleClass('frescatis', 'all', 'one')">Se alla &#8594;</button>
      </div>


      <div id="frescatis" class="one">

        <?php if ($json_odenplan_frescati['Trip'] != null) { ?>

          <?php foreach ($json_odenplan_frescati['Trip'] as $key => $trip) { ?>
            <div class="frescati-time">
              <hr>
              <p><b><?php echo $trip['LegList']['Leg'][0]['Origin']['name']; ?>: </b> <?php echo substr( $trip['LegList']['Leg'][0]['Origin']['time'], 0, 5 );?></p>
              <p><b><?php echo $trip['LegList']['Leg'][0]['Destination']['name']; ?>: </b> <?php echo substr( $trip['LegList']['Leg'][0]['Destination']['time'], 0, 5); ?></p>
            </div>
          <?php } ?>

        <?php } ?>

    </div>

  </div>

    <div class="box white hl frescati">
      <div class="see-more">
        <h3>Nästa buss Odenplan</h3>
        <button onclick="toggleClass('odenplans', 'all', 'one');">Se alla &#8594;</button>
      </div>


      <div id="odenplans" class="one">

        <?php if ($json_frescati_odenplan['Trip'] != null) { ?>
        <?php foreach ($json_frescati_odenplan['Trip'] as $key => $trip) { ?>
          <div class="frescati-time">
            <hr>
            <p><b><?php echo $trip['LegList']['Leg'][0]['Origin']['name']; ?>: </b> <?php echo substr( $trip['LegList']['Leg'][0]['Origin']['time'], 0 , 5 ); ?></p>
            <p><b><?php echo $trip['LegList']['Leg'][0]['Destination']['name']; ?>: </b> <?php echo substr( $trip['LegList']['Leg'][0]['Destination']['time'], 0, 5 ); ?></p>
          </div>
        <?php } ?>
      <?php } ?>
    </div>

    </div>

  </div>



</section>

<section id="karbrev">

  <h2>Senaste Kårbrevet</h2>

  <?php

  $karbrev = true;
  display_karbrev( 1, false, false );

  ?>

</section>

<section id="latest-events">

  <h2>Events</h2>

  <div class="events">

  <?php
  global $wpdb;

  $currentMonth = date('m');
  $currentYear = date('Y');
  $currentDay = date('d');
  $currentWeek = date('W');
  if ($currentWeek[0] == '0'){
    $currentWeek = $currentWeek[1];
  }

  // DEFAULT WEEK
  // Get all events for the current day
  if (current_user_can('administrator') || current_user_can('elevkaren') ){
    $upcoming_events = $wpdb->get_results('SELECT * FROM vro_events WHERE YEAR(start) = ' . $currentYear . ' AND WEEK(start, 3) = ' . $currentWeek . ' ORDER BY start ASC LIMIT 3 ');
  } else {
    $upcoming_events = $wpdb->get_results('SELECT * FROM vro_events WHERE YEAR(start) = ' . $currentYear . ' AND WEEK(start, 3) = ' . $currentWeek . ' AND visibility="a" ORDER BY start ASC LIMIT 3');
  }

  $i = 0;
  foreach ($upcoming_events as $up_event) {

    $current_event_type = $wpdb->get_row('SELECT * FROM vro_event_types WHERE id=' . $up_event->type);
    $symbol = ($current_event_type) ? $current_event_type->symbol : '';

    if ($up_event->visibility == 'a') {
      echo '<div class="event">';
    } else {
      echo '<div class="event unpublished">';
    }
    ?>

      <div class="icon">
        <p><?php echo $symbol; ?></p>
      </div>

      <div class="info">

        <h5><?php echo $up_event->name; ?></h5>
        <?php
          // Check if the event is on one or mulitple days
          if (date('d M Y', strtotime($up_event->start)) != date('d M Y', strtotime($up_event->end)) ){
            ?>
              <p><?php echo date('H:i', strtotime($up_event->start)); ?> - <?php echo date('d M Y, l', strtotime($up_event->start)); ?></p>
              <p><?php echo date('H:i', strtotime($up_event->end)); ?> - <?php echo date('d M Y, l', strtotime($up_event->end)); ?></p>
            <?php
          } else {
              ?>
              <p><?php echo date('H:i', strtotime($up_event->start)); ?> - <?php echo date('H:i', strtotime($up_event->end)); ?></p>
              <p><?php echo date('d M Y, l', strtotime($up_event->start)); ?></p>
            <?php } ?>
      </div>


    </div>
    <?php

    $i++;
  }

  ?>

  </div>

  <a href="/front-kalender" class="btn lg">Se hela kalendern</a>

</section>

<section id="contact" class="green">

  <h2>Kontakta oss</h2>

  <form class="" action="<?php echo (get_bloginfo('template_directory') . '/scripts/send_mail.inc.php'); ?>" method="post">
    <input type="email" name="mail_from" value="" placeholder="Din mail...">
    <textarea name="message" placeholder="Fråga..."></textarea>
    <input type="text" name="subject" value="En ny fråga från hemsidan!" hidden>
    <input type="text" name="mail_to" value="odenplanselevkar@vrg.se" hidden>
    <input type="text" name="callback" value="/" hidden>
    <button type="submit" name="send_mail" class="btn lg">Skicka</button>
  </form>

</section>

<section id="social-media">

  <h2>Följ oss på sociala medier!</h2>
  <div class="social-media-list">

    <a href="https://www.snapchat.com/add/vroelevkar/" class="social-link">
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M5.829 4.533c-.6 1.344-.363 3.752-.267 5.436-.648.359-1.48-.271-1.951-.271-.49 0-1.075.322-1.167.802-.066.346.089.85 1.201 1.289.43.17 1.453.37 1.69.928.333.784-1.71 4.403-4.918 4.931-.251.041-.43.265-.416.519.056.975 2.242 1.357 3.211 1.507.099.134.179.7.306 1.131.057.193.204.424.582.424.493 0 1.312-.38 2.738-.144 1.398.233 2.712 2.215 5.235 2.215 2.345 0 3.744-1.991 5.09-2.215.779-.129 1.448-.088 2.196.058.515.101.977.157 1.124-.349.129-.437.208-.992.305-1.123.96-.149 3.156-.53 3.211-1.505.014-.254-.165-.477-.416-.519-3.154-.52-5.259-4.128-4.918-4.931.236-.557 1.252-.755 1.69-.928.814-.321 1.222-.716 1.213-1.173-.011-.585-.715-.934-1.233-.934-.527 0-1.284.624-1.897.286.096-1.698.332-4.095-.267-5.438-1.135-2.543-3.66-3.829-6.184-3.829-2.508 0-5.014 1.268-6.158 3.833z"/></svg>
      Snapchat
    </a>

    <a href="https://www.instagram.com/vroelevkar/?hl=sv" class="social-link">
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
      Instagram
    </a>

    <a href="https://sv-se.facebook.com/vroelevkar/" class="social-link">
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/></svg>
      Facebook
    </a>

    <a href="https://twitter.com/vroelevkar" class="social-link">
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
      Twitter
    </a>

    <a href="https://www.youtube.com/channel/UCdGE6zEJyZgLDrTJJICoXUw" class="social-link">
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"/></svg>
      Youtube
    </a>

  </div>

</section>

<script src="<?php echo get_bloginfo('template_directory') ?>/js/admin.js" charset="utf-8"></script>
<script type="text/javascript">
  document.getElementById('video-background').playbackRate = 0.7;

  window.onload = function() {
    if (window.innerWidth > 800) {
      document.querySelector("#video-background").src = "<?php echo get_bloginfo('template_directory') . '/img/vrgkampen.MP4'; ?>";
    }
  }

  window.addEventListener('scroll', function() {
    scrollAppear('news');
    scrollAppear('about-us-text');
    scrollAppearAll('box');
    fillNavigationBar('v-header');
  });
</script>

<?php

get_footer();

 ?>
