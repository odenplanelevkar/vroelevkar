<?php
/**
 * Template Name: Game
 */


$completedChapters = 2;

?>

<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0>
    <style> body {padding: 0; margin: 0;} </style>

    <link rel="icon" href="<?php echo get_bloginfo('template_directory') ?>/img/logga.png" type="image/icon type">
    <LINK REL=StyleSheet HREF="<?php echo get_bloginfo('template_directory') ?>/p5/code/style.css" TYPE="text/css" MEDIA=screen>
    <link href="https://fonts.googleapis.com/css2?family=VT323&display=swap" rel="stylesheet">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/js/all.min.js" charset="utf-8"></script>


    <script src="<?php echo get_bloginfo('template_directory') ?>/p5/p5.min.js"></script>
    <script src="<?php echo get_bloginfo('template_directory') ?>/p5/addons/p5.dom.min.js"></script>
    <script src="<?php echo get_bloginfo('template_directory') ?>/p5/addons/p5.sound.min.js"></script>
    <script src="<?php echo get_bloginfo('template_directory') ?>/p5/addons/p5.play.js"></script>

  </head>
  <body>

  <?php




  // Only show game to logged in users
  if (! is_user_logged_in() ){
    ?>

    <div id="login">
      <div class="box">

        <h1>Du måste vara inloggad för att se spelet!</h1>

        <a href="/wp-login.php?redirect=game">Logga In</a>

        <p>Är du elev på skolan men har inget konto?</p>
        <a href="/register">Registrera dig för ett elevkonto här!</a>

        <p>Går du inte på skolan men vill fortfarande kunna spela spelet?</p>
        <a href="/register-gamer">Registrera dig för ett spelkonto här!</a>
      </div>
    </div>

    <?php
  } else {

 ?>

    <!-- ***********************************
    * ERROR HANDLING
    *************************************-->
    <?php show_error_alert(); ?>

    <?php

    global $wpdb;

    $user = wp_get_current_user();
    $has_played = (count($wpdb->get_results("SELECT * FROM vroregon_players WHERE user_id = $user->ID")) > 0) ? true : false;

    $menu_text = ($has_played == false) ? 'NEW ADVENTURE' : 'CONTINUE ADVENTURE';

    ?>

    <script type="text/javascript">

      function holdup(title, text, command) {
        event.stopPropagation();

        Swal.fire({
          title: title,
          text: text,
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Ja',
          cancelButtonText: "Avbryt",
        }).then((result) => {
          if (result.value) {
            if (command == 'restart-game') {
              clearPlayer();
            }

            if (command.includes('chapter')) {

              let chapter = Number(command.substring(8));
              startFromChapter(chapter);
            }

          }
        })
      }

    </script>

      <audio id="audio-holder" hidden autoplay loop src="http://vroelevkar.se/wp-content/uploads/2020/04/harKommerJag.mp3"></audio>
      <audio id="textloop-holder" hidden loop src="<?php echo get_bloginfo('template_directory') ?>/game-assets/soundeffects/textloop-long.wav"></audio>
      <audio id="choice-holder" hidden src="<?php echo get_bloginfo('template_directory') ?>/game-assets/soundeffects/choice.wav"></audio>
      <audio id="select-holder" hidden src="<?php echo get_bloginfo('template_directory') ?>/game-assets/soundeffects/select.wav"></audio>

      <img id="toggle-sound" src="<?php echo get_bloginfo('template_directory') ?>/game-assets/mini-assets/musicON.png" />
      <?php if (is_student_admin()) { ?>
        <button type="button" name="button" id="toggle-admin">Toggle admin view</button>
        <p id="is-admin" hidden>true</p>
      <?php } else {
        echo '<p id="is-admin" hidden>false</p>';
      }?>

      <div id="overlay"></div>

      <p id="has-played" hidden><?php echo ($has_played == true) ? 'yes' : 'no' ?></p>

      <div id="intro-screen" class="cover">
        <img id="intro-image" src="<?php echo get_bloginfo('template_directory') ?>/game-assets/backgrounds/beach.png" alt="test">
        <img id="logo" src="<?php echo get_bloginfo('template_directory') ?>/game-assets/curseofthecircle.png" alt="">

        <div class="menu">
            <button id="main-choice" class="blink">[ <?php echo $menu_text ?> ]</button>

            <button id="chapter-select-btn">[ CHAPTER SELECT ]</button>
            <div class="chapter-select hidden">
              <button id="chapter-1" onclick="holdup('Är du säker?', 'Om du startar om spelet från detta kapitel din progress efter detta kapitel att nollställas.', 'chapter-1')">[ CHAPTER 1 ]</button>
              <button id="chapter-2" class="hidden" onclick="holdup('Är du säker?', 'Om du startar om spelet från BÖRJAN av detta kapitel kommer din progress från detta och efterföljande kapitel att nollställas.', 'chapter-2')">[ CHAPTER 2 ]</button>
              <button id="chapter-3" class="hidden" onclick="holdup('Är du säker?', 'Om du startar om spelet från BÖRJAN av detta kapitel kommer din progress från detta och efterföljande kapitel att nollställas.', 'chapter-3')">[ CHAPTER 3 ]</button>
              <button id="chapter-4" class="hidden" onclick="holdup('Är du säker?', 'Om du startar om spelet från BÖRJAN av detta kapitel kommer din progress från detta och efterföljande kapitel att nollställas.', 'chapter-4')">[ CHAPTER 4 ]</button>
              <button id="chapter-5" class="hidden" onclick="holdup('Är du säker?', 'Om du startar om spelet från BÖRJAN av detta kapitel kommer din progress från detta och efterföljande kapitel att nollställas.', 'chapter-5')">[ CHAPTER 5 ]</button>
            </div>

            <?php if ($has_played): ?>
                <button id="restart-game-2" type="button" name="button" onclick="holdup('Är du säker?', 'Om du startar om spelet kommer all din progress nollställas.', 'restart-game')">[ RESTART GAME ]</button>
            <?php endif; ?>

            <button onclick="window.location.href = '/';">[ QUIT ]</button>
        </div>
      </div>

      <div id="gameover" class="cover">
        <img id="gameover-img" src="<?php echo get_bloginfo('template_directory') ?>/game-assets/backgrounds/gameover.gif" alt="">

        <div class="menu">
          <button id="restart-game">[ RESTART ADVENTURE ]</button>
        </div>

      </div>

      <!-- <div id="win" class="cover">
        <img id="gameover-img" src="<?php echo get_bloginfo('template_directory') ?>/game-assets/backgrounds/gameover.gif" alt="">

        <div class="menu">
          <button onclick="window.location.href = '/game';">[ BACK TO MAIN MENU ]</button>
        </div>

      </div> -->

      <div id="endscreen" class="cover">
        <img id="endscreen-img" src="<?php echo get_bloginfo('template_directory') ?>/game-assets/backgrounds/chapter1finished.png" alt="">

        <div class="menu">
          <!-- <button id="continue-next-chapter" type="button" name="button">[ CONTINUE TO NEXT CHAPTER ]</button> -->
          <button onclick="window.location.href = '/game';">[ BACK TO MAIN MENU ]</button>
        </div>

      </div>

      <div id="grandparent">


        <p id="game-asset-folder" hidden><?php echo get_bloginfo('template_directory') ?>/game-assets/</p>
        <img id="background-image" src="<?php echo get_bloginfo('template_directory') ?>/game-assets/backgrounds/beach.png" alt="test">

        <div id="parent">

          <div id="textbox" class="box"></div>

          <div id="optionbox" class="box">
            <div id="option-1" class = "option"></div>
            <div id="option-2" class = "option"></div>
            <div id="option-3" class = "option"></div>
            <div id="option-4" class = "option"></div>
            <div id="option-5" class = "option"></div>
          </div>

          <div id="dev-box" class="box">
            <h4 id="room-info"></h4>

            <h3>Inventory</h3>
            <ul id="inventory"></ul>

            <input type="text" name="" value="" id="item-input" placeholder="Item to add...">
            <button type="button" name="button" id="add-item" onclick="addItemDebug('item-input')">Add item</button>
            <button type="button" name="button" id="clear-inventory" onclick="clearInventory()">Clear inventory</button>

            <h3>BeenTo's</h3>
            <ul id="beenTo"></ul>
            <input type="text" name="" value="" id="beenTo-input" placeholder="BeenTo to add...">
            <button type="button" name="button" onclick="addBeenToDebug('beenTo-input')">Add beenTo</button>
            <button type="button" name="button" onclick="clearBeenTo()">Clear beenTo's</button>

            <h3>Player stats</h3>
            <div class="player-stats">
              <p>Intellligence: <span id="intelligence"></span></p>
              <p>Charisma: <span id="charisma"></span></p>
              <p>Grit: <span id="grit"></span></p>
              <p>Kindness: <span id="kindness"></span></p>
              <p>Dexterity: <span id="dexterity"></span></p>
            </div>


            <h3>Player Database Functions</h3>
            <button type="button" name="button" onclick="savePlayer()">Save player</button>
            <button type="button" name="button" onclick="getPlayer()">Get player</button>
            <button type="button" name="button" onclick="resetPlayer()">Reset Game</button>
            <button type="button" name="button" onclick="clearPlayer()">Clear Player From DB</button>

            <h3>Hoppa till koordinat</h3>

            <form>

              <select id="switch-area">
                <option value="">test</option>
                <option value="">intro</option>
                <option value="">highlands</option>
                <option value="">bog</option>
                <option value="">city</option>
                <option value="">mountain</option>
                <option value="">core</option>
              </select>
              <br/>

              <input id="switch-x" type="number" name="" value="" placeholder="x-koor" required>
              <input id="switch-y" type="number" name="" value="" placeholder="y-koor" required>
              <br/>
              <button type="button" name="button" onclick="changeRoomDebug()">Hoppa</button>

            </form>
          </div>

        </div>

      </div>

      <div id="video-player" class="hidden">
        <p>TIME REMAINING:</p>
        <p id="timer-label">39:38</p>
        <iframe id="video" src="https://www.youtube.com/embed/D_d7zcckIwA?start=1&autoplay=0&showinfo=0&rel=0&iv_load_policy=3&controls=0&disablekb=1" width="560" height="315" frameborder="0"></iframe>
      </div>

      <!-- SCRIPTS -->
      <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.2.2/gsap.min.js" charset="utf-8"></script>

      <script src="<?php echo get_bloginfo('template_directory') ?>/p5/code/helpers.js"></script>
      <script src="<?php echo get_bloginfo('template_directory') ?>/p5/code/Main.js"></script>
      <script src="<?php echo get_bloginfo('template_directory') ?>/p5/code/fader.js"></script>
      <script src="<?php echo get_bloginfo('template_directory') ?>/p5/code/introscreen.js"></script>
      <script src="<?php echo get_bloginfo('template_directory') ?>/p5/code/Minigame-pepe.js"></script>
      <script src="<?php echo get_bloginfo('template_directory') ?>/p5/code/Minigame-card-game.js"></script>
      <script src="<?php echo get_bloginfo('template_directory') ?>/p5/code/Minigame-ernst-running.js"></script>
      <script src="<?php echo get_bloginfo('template_directory') ?>/p5/code/Minigame-flappy-river.js"></script>
      <script src="<?php echo get_bloginfo('template_directory') ?>/p5/code/Minigame-invaders.js"></script>
      <script src="<?php echo get_bloginfo('template_directory') ?>/p5/code/Minigame-mountain-jump.js"></script>
      <script src="<?php echo get_bloginfo('template_directory') ?>/p5/code/ddr_level.js"></script>
      <script src="<?php echo get_bloginfo('template_directory') ?>/p5/code/Minigame-ddr.js"></script>
      <script src="<?php echo get_bloginfo('template_directory') ?>/p5/code/Minigame-frog-king.js"></script>
      <script src="<?php echo get_bloginfo('template_directory') ?>/p5/code/Minigame-clean_your_plate.js"></script>
      <script src="<?php echo get_bloginfo('template_directory') ?>/p5/code/wasp_drama_words.js"></script>
      <script src="<?php echo get_bloginfo('template_directory') ?>/p5/code/Minigame-type-racer.js"></script>
      <script src="<?php echo get_bloginfo('template_directory') ?>/p5/code/Minigame-wasp-run.js"></script>
      <script src="<?php echo get_bloginfo('template_directory') ?>/p5/code/Minigame-wasp-click.js"></script>
      <script src="<?php echo get_bloginfo('template_directory') ?>/p5/code/Minigame-final-boss.js"></script>
      <script src="<?php echo get_bloginfo('template_directory') ?>/p5/code/Minigame-start-end.js"></script>

      <script type="text/javascript">

      window.onload = function(){

        let isAdmin = document.getElementById('is-admin').innerText == 'true' ? true : false;

        // if (isAdmin == false) {
          setTimeout(function() {


            if (player.completed && hasCompleted(completedChapters)) {
              // Hide continueGame button
              $('#main-choice').remove();

              let chapterText;
              switch (maxChapterCompleted()) {
                case 1:
                  chapterText = 'Kapitel 1: Highlands';
                  break;
                case 2:
                  chapterText = 'Kapitel 2: The Bog';
                  break;
                case 3:
                  chapterText = 'Kapitel 3: City';
                  break;
              }

              // Popup with info
              Swal.fire(
                `Kapitel ${player.completed.length} Avklarat!`,
                `Du har spelat klart ${chapterText}. Vänta på nästa release för att kunna fortsätta spela!`,
                'success'
              )
            }
          }, 2500)
        // } // End isdmin
      }



      </script>

  </body>
</html>

<?php

}

 ?>
