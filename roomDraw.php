<?php

/**
 * Template Name: Room Draw
 */

 // Only show game to logged in users
 if (! is_user_logged_in() || !(current_user_can('administrator') || current_user_can('elevkaren') ) ){
   wp_redirect( '/' );
 }else {

?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <LINK REL=StyleSheet HREF="<?php echo get_bloginfo('template_directory') ?>/p5/addons/quicksettings.css" TYPE="text/css" MEDIA=screen>
      <link rel="icon" href="<?php echo get_bloginfo('template_directory') ?>/img/logga.png" type="image/icon type">

  </head>
  <body>
        <style> body {padding: 0; margin: 0;} </style>

        <script src="<?php echo get_bloginfo('template_directory') ?>/p5/p5.min.js"></script>
        <script src="<?php echo get_bloginfo('template_directory') ?>/p5/addons/p5.dom.min.js"></script>
        <script src="<?php echo get_bloginfo('template_directory') ?>/p5/addons/p5.sound.min.js"></script>
        <script src="<?php echo get_bloginfo('template_directory') ?>/p5/addons/p5.play_room_draw.js"></script>

        <script src="<?php echo get_bloginfo('template_directory') ?>/p5/addons/quicksettings.js"></script>
        <script src="<?php echo get_bloginfo('template_directory') ?>/p5/addons/p5.gui.js"></script>
        <script src="<?php echo get_bloginfo('template_directory') ?>/p5/addons/dat.gui.js"></script>

        <script src="<?php echo get_bloginfo('template_directory') ?>/p5/code/helpers.js"></script>
        <script src="<?php echo get_bloginfo('template_directory') ?>/p5/code/Main.js"></script>
        <script src="<?php echo get_bloginfo('template_directory') ?>/p5/code/room_class.js"></script>
        <script src="<?php echo get_bloginfo('template_directory') ?>/p5/code/room_draw_2.js"></script>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

        <style media="screen">
          .guiBackground{
            position: fixed;
            left: 0px;
            top: 0px;
            width: auto;
            height: auto;
            background-color: #969696
          }
          .generalGuiBackground{
            position: fixed;
            right: 210px;
            top: 10px;
            width: auto;
            height: auto;
            background-color: #969696
          }
        </style>
        <div class="guiBackground" id="guiBackground">

        </div>
        <div class="generalGuiBackground" id="generalGuiBackground">

  </body>
</html>

<?php } // End if logged in ?>
