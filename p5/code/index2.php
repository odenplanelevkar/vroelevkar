<?php
/**
 * Template Name: Game
 */

 $str_json = file_get_contents('php://input');

 global $wbdb;

 // Create a new array that will hold all the arguments to create a new visselpipan suggestion
 $rooms = array();

 $rooms['rooms'] = $str_json;

 // Insert the new suggestion into the database
 if($wpdb->insert(
     'vroregon_testrooms',
     $rooms
 ) == false) {
   wp_die('database insertion failed');
 }

 // global $wpdb;
 //
 // $str_json = $wpdb->get_var('SELECT rooms FROM vroregon_testrooms');

?>

<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0>
    <style> body {padding: 0; margin: 0;} </style>
    <LINK REL=StyleSheet HREF="style.css" TYPE="text/css" MEDIA=screen>


      <script type="text/javascript">

        var roomsString = '<?php echo $str_json ?>';
        var roomse = JSON.parse(roomsString);

        console.log(roomse);
        console.log('test');

      </script>
    <script src="../../p5.min.js"></script>
    <script src="../../addons/p5.dom.min.js"></script>
    <script src="../../addons/p5.sound.min.js"></script>
    <script src="../../addons/p5.play.js"></script>

    <script>let usingRoomDraw = false</script>
    <script src="Main.js"></script>
    <script src="Rooms.js"></script>
    <script src="Minigame-pepe.js"></script>
    <script src="Minigame-card-game.js"></script>
    <script src="Minigame-ernst-running.js"></script>
    <script src="Minigame-flappy-river.js"></script>
    <script src="Minigame-invaders.js"></script>
    <script src="Minigame-mountain-jump.js"></script>
    <script src="Minigame-ddr.js"></script>
    <script src="Minigame-start-end.js"></script>

  </head>
  <body>





      <div id="grandparent">
        <img src="test.png" alt="test" style="width:1920px;height:1080px;position:absolute;top:0px;bottom:0px;">

        <div id="parent">

          <!--<div id="imgbox" class="box">
            img.png
          </div> -->

          <div id="textbox" class="box">
          </div>
          <div id="optionbox" class="box">
            <div id="option-1" class = "option">
            </div>
            <div id="option-2" class = "option">
            </div>
            <div id="option-3" class = "option">
            </div>
            <div id="option-4" class = "option">
            </div>
            <div id="option-5" class = "option">
            </div>

          </div>




      </div>
        <a id="mountain-jump" href="index-minigame-mountain-jump.html"></a>
      </div>

  </body>
</html>
