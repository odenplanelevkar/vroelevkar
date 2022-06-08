<?php

/**
 * Template Name: Front-Karen
 */

get_header();

?>

<header id="single-page-header">
  <img src="<?php echo get_bloginfo('template_directory') . '/img/odengrodan.png'; ?>" alt="">
  <div class="img-overlay"></div>

  <svg class="single-page-logo" id="karen-logo" width="732" height="139" viewBox="0 0 732 139" fill="none" xmlns="http://www.w3.org/2000/svg">
    <path stroke="#fff" stroke-width="5" d="M13.6686 49.288V82.84H50.2446V93.64H13.6686V128.2H54.5646V139H0.564575V38.488H54.5646V49.288H13.6686Z"/>
    <path stroke="#fff" stroke-width="5" d="M87.4967 128.344H122.633V139H74.3927V38.632H87.4967V128.344Z" />
    <path stroke="#fff" stroke-width="5" d="M149.653 49.288V82.84H186.229V93.64H149.653V128.2H190.549V139H136.549V38.488H190.549V49.288H149.653Z" />
    <path stroke="#fff" stroke-width="5" d="M293.321 38.632L255.449 139H240.329L202.457 38.632H216.425L247.961 125.176L279.497 38.632H293.321Z" />
    <path stroke="#fff" stroke-width="5" d="M362.122 139L320.794 93.208V139H307.69V38.632H320.794V85.144L362.266 38.632H378.826L333.322 88.888L379.258 139H362.122Z" />
    <path stroke="#fff" stroke-width="5" d="M453.221 116.68H409.445L401.381 139H387.557L423.845 39.208H438.965L475.109 139H461.285L453.221 116.68ZM449.477 106.024L431.333 55.336L413.189 106.024H449.477ZM446.885 15.592C446.885 20.2 445.397 23.896 442.421 26.68C439.541 29.368 435.893 30.712 431.477 30.712C427.157 30.712 423.509 29.32 420.533 26.536C417.557 23.752 416.069 20.104 416.069 15.592C416.069 11.08 417.557 7.43199 420.533 4.64799C423.509 1.86399 427.157 0.471985 431.477 0.471985C435.893 0.471985 439.541 1.86399 442.421 4.64799C445.397 7.33599 446.885 10.984 446.885 15.592ZM439.253 15.448C439.253 13.048 438.485 11.128 436.949 9.68799C435.509 8.15199 433.685 7.38399 431.477 7.38399C429.269 7.38399 427.397 8.15199 425.861 9.68799C424.421 11.128 423.701 13.048 423.701 15.448C423.701 17.848 424.421 19.816 425.861 21.352C427.397 22.888 429.269 23.656 431.477 23.656C433.685 23.656 435.509 22.888 436.949 21.352C438.485 19.816 439.253 17.848 439.253 15.448Z" />
    <path stroke="#fff" stroke-width="5" d="M543.772 139L519.868 97.96H504.028V139H490.924V38.632H523.324C530.908 38.632 537.292 39.928 542.476 42.52C547.756 45.112 551.692 48.616 554.284 53.032C556.876 57.448 558.172 62.488 558.172 68.152C558.172 75.064 556.156 81.16 552.124 86.44C548.188 91.72 542.236 95.224 534.268 96.952L559.468 139H543.772ZM504.028 87.448H523.324C530.428 87.448 535.756 85.72 539.308 82.264C542.86 78.712 544.636 74.008 544.636 68.152C544.636 62.2 542.86 57.592 539.308 54.328C535.852 51.064 530.524 49.432 523.324 49.432H504.028V87.448Z" />
    <path stroke="#fff" stroke-width="5" d="M591.637 49.288V82.84H628.213V93.64H591.637V128.2H632.533V139H578.533V38.488H632.533V49.288H591.637Z" />
    <path stroke="#fff" stroke-width="5" d="M731.273 139H718.169L665.465 59.08V139H652.361V38.488H665.465L718.169 118.264V38.488H731.273V139Z" />
</svg>

<hr id="logo-sepparator">



</header>

<section>


  <?php display_karen(); ?>


</section>

<section class="green stadgar-reglementen">

  <h2>Stadgar och Reglementen</h2>
  <h3>Innehåll:</h3>
  <ul>
    <li><a href="#stadgar">Stadgar</a></li>
    <li><a href="#reglementen">Reglementen</a></li>
  </ul>

</section>

<section class="white stadgar-reglementen">

  <h2 class="content-header" id="stadgar">Stadgar</h2>
  <a class="pdf" href="http://vroelevkar.se/wp-content/uploads/2020/10/Stadgar-2020-2021.pdf" target="_blank">PDF-Version</a>
  <?php include_once('stadgar.php'); ?>

  <h2 class="content-header" id="reglementen">Reglementen</h2>
  <a class="pdf" href="http://vroelevkar.se/wp-content/uploads/2020/10/Reglemente-2020-2021.pdf" target="_blank">PDF-Version</a>
  <?php include_once('reglementen.php'); ?>

</section>

<script src="<?php echo get_bloginfo('template_directory') ?>/js/admin.js" charset="utf-8"></script>
<script type="text/javascript">
  window.addEventListener('scroll', function() {
    fillNavigationBar('single-page-header');
    scrollAppearAll('box');
  });
</script>

<?php

get_footer();

?>
