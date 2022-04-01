<?php

global $wpdb;

$classes = $wpdb->get_results('SELECT name FROM vro_classes ORDER BY points DESC');

if (count($classes) > 0){
  $first_class = $classes[0];
}

?>


<div class="bow">

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

  <div class="box green hl frescati">
    <div class="see-more">
      <h3>Nästa buss Frescati</h3>
      <button onclick="toggleClass('frescatis', 'all', 'one');">See alla &#8594;</button>
    </div>


    <div id="frescatis" class="one">

      <?php foreach ($json_odenplan_frescati['Trip'] as $key => $trip) { ?>
        <div class="frescati-time">
          <hr>
          <p><b><?php echo $trip['LegList']['Leg'][0]['Origin']['name']; ?>: </b> <?php echo substr( $trip['LegList']['Leg'][0]['Origin']['time'], 0, 5 );?></p>
          <p><b><?php echo $trip['LegList']['Leg'][0]['Destination']['name']; ?>: </b> <?php echo substr( $trip['LegList']['Leg'][0]['Destination']['time'], 0, 5); ?></p>
        </div>
      <?php } ?>
  </div>

</div>

<div class="box white hl frescati">
  <div class="see-more">
    <h3>Nästa buss Odenplan</h3>
    <button onclick="toggleClass('odenplans', 'all', 'one');">See alla &#8594;</button>
  </div>


  <div id="odenplans" class="one">

    <?php foreach ($json_frescati_odenplan['Trip'] as $key => $trip) { ?>
      <div class="frescati-time">
        <hr>
        <p><b><?php echo $trip['LegList']['Leg'][0]['Origin']['name']; ?>: </b> <?php echo substr( $trip['LegList']['Leg'][0]['Origin']['time'], 0 , 5 ); ?></p>
        <p><b><?php echo $trip['LegList']['Leg'][0]['Destination']['name']; ?>: </b> <?php echo substr( $trip['LegList']['Leg'][0]['Destination']['time'], 0, 5 ); ?></p>
      </div>
    <?php } ?>
</div>

</div>

<div class="box white sm matsedeln-dashboard">
  <h3>Matsedeln</h3>
  <iframe class="matsedeln" name="matsedeln-iframe" id="matsedeln-iframe" src="https://mpi.mashie.com/public/menu/kk+vrvasastan/4465fa56?country=se" width="" height=""> </iframe>
</div>

<div class="box white sm classpoints smaller">
<h3>Klasspokalen</h3>
<div class="first-place">
  <p><b>1</b></p>
  <p><b><?php echo $first_class->name; ?></b></p>

  <img class="trophy" src="<?php echo get_bloginfo('template_directory') ?>/img/bigtrophy.png" alt="">
  <img class="circle"src="<?php echo get_bloginfo('template_directory') ?>/img/circle.png" alt="">
</div>

</div>

</div>

<?php

display_karbrev( 1 );

?>

<a class="btn lg" href="/panel/arkiv">Till arkiven</a>
