function fade(music = false, continueGame = false) {

  // ENTER LOCKDOWN
  enterLockdown(5000);

  if (music != false) {

    if (hasSound == false) {
      $('#audio-holder').prop('volume', 0);
      player.music = music
      $('#audio-holder').attr('src', music)
    } else {
      $('#audio-holder').animate({
            volume: 0
        }, 3000, function() {
            // Change music
            player.music = music
            $('#audio-holder').attr('src', music)
            $('#audio-holder').animate({
                volume: 0.3
            }, 3000)
        })
    }

  }

  $('#overlay').animate({
    opacity: 1
  }, 3000, function() {

    if (continueGame) {
      $('#audio-holder').attr('src', player.music)
      continueGame()
    }

    changeBoxColor()

    $('#overlay').animate({
      opacity: 0
    }, 3000)
  })

}

$(window).ready(function() {

    $('#audio-holder').get(0).pause();
    $('#audio-holder').prop('volume', 0.3);

    setTimeout(function() {
      $('#audio-holder').get(0).play();
    }, 2800);
    // $('#audio-holder').attr('src', player.music)
    // $('#audio-holder').prop('volume', 0);
    //
    // $('#audio-holder').animate({
    //     volume: 0.3
    // }, 4000)

    // $('.dim').css('opacity', 1);
    //
    // setTimeout(function() {
    //   paused = false;
    // }, 2000)
    //
    // $('.dim').animate({
    //   opacity: 0
    // }, 6000)
})
