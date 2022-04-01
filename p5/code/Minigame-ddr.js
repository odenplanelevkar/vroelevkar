var docks, spacing, docksize;
var arrows, arrowspeed, arrowspacing, bps;
var level, levelpos;
var ddr_score;
var startSc;
var gameOver;
let ddr_time;
let dock_img, arrow_dock_img;
let niklas_anim;
let elena_anim;
let frame_rate;
let ddr_musicStarted
let ddr_timerMultiplier
let ddr_musicDelayTimer;
let ddr_lives;
let ddr_missed;
let ddr_multiplier, ddr_multiplierCounter;
let ddr_textEffectProgression, ddr_textEffectType, ddr_textEffectStarted;
let ddr_hasSetScoreBeenTo, ddr_indexOfPreviousScoreBeenTo;


function ddr_preload(){
  arrow_images = [
    loadImage(spriteImgSrc +'ddr_arrows/arrow_left.png'),
    loadImage(spriteImgSrc +'ddr_arrows/arrow_down.png'),
    loadImage(spriteImgSrc +'ddr_arrows/arrow_up.png'),
    loadImage(spriteImgSrc +'ddr_arrows/arrow_right.png'),
  ]
  dock_images = [
    loadImage(spriteImgSrc +'ddr_arrows/dock_left.png'),
    loadImage(spriteImgSrc +'ddr_arrows/dock_down.png'),
    loadImage(spriteImgSrc +'ddr_arrows/dock_up.png'),
    loadImage(spriteImgSrc +'ddr_arrows/dock_right.png'),
  ]
  heart_icon = loadImage(spriteImgSrc + 'heart10px.png')
  elena_anim = loadAnimation(spriteImgSrc + 'niklas_elena_anim/separat/elena_0001.png', spriteImgSrc + 'niklas_elena_anim/separat/elena_0008.png')
  niklas_anim = loadAnimation(spriteImgSrc + 'niklas_elena_anim/separat/niklas_0001.png', spriteImgSrc + 'niklas_elena_anim/separat/niklas_0008.png')
  niklas_anim.frameDelay = 4;
  elena_anim.frameDelay = 4;

}

function ddr_draw(){
  frameRate(frame_rate)
  if(startSc){startScreen("Dj G&E's DDR")}
  if(!gameOver && !startSc){ddr_game();}
  if(!startSc && gameOver && !win){
    camera.position.x = width/2;
    camera.position.y = height/2;
    resizeCanvas(600, 600)
    frameRate(60)
    gameOverScreen();
    if(clearVar){ddr_deleteVar();}
  }
  if(!startSc && win && !gameOver){
    camera.position.x = width/2;
    camera.position.y = height/2;
    resizeCanvas(600, 600)
    frameRate(60)
    winScreen();
    if(clearVar){ddr_deleteVar();}
  }
}

function ddr_deleteVar(){
  docks = []
  arrows = [[], [], [], []]
  ddr_time = 0;
  levelpos = 0;
  ddr_score = 0
  $('#audio-holder').get(0).pause();
}


function ddr_defineVar(){

  resizeCanvas(1200, 700)
  $('#audio-holder').attr('src', music['dance'])
  $('#audio-holder').get(0).pause();
  ddr_musicStarted = false;
  win = false
  startSc = true;
  gameOver = false;
  camera.position.x = width/2;
  camera.position.y = height/2;
  niklas_anim.stop();
  elena_anim.stop();
  ddr_multiplier = 1;
  ddr_hasSetScoreBeenTo = false;
  ddr_textEffectStarted = false;
  ddr_multiplierCounter = 0;
  ddr_missed = 0;
  ddr_lives = 6;
  ddr_musicDelayTimer = 0;
  frame_rate = 48;
  let playAreaWidth = 280
  ddr_time = 0;
  ddr_timerMultiplier = 0
  docksize = 60;
  spacing = 10;
  ddr_score = 0;

  spawnrate = 40; //higher -> lower rate
  arrowspacing = 2*(docksize+spacing)
  bps = 3; // f
  arrowspeed = arrowspacing*bps //v = lambda * f

  arrows = [[], [], [], []] //0- right, 1-down, 2- up, 3-left
  docks = [];

  let dockStartXCoordinate = (width-playAreaWidth)/2
  for (var i = 0; i < 4; i++) {
    dock = new ddr_arrowDock(i, dockStartXCoordinate);
    docks.push(dock);
  }

  level = ddr_getLevel();
  levelpos = 0;
  fill(255)
}

function ddr_game(){
  ddr_time += deltaTime
  if(!ddr_musicStarted){ddr_musicDelayTimer += deltaTime}
  if(!ddr_musicStarted && ddr_musicDelayTimer > 1500){
    $('#audio-holder').get(0).play();
    ddr_musicStarted = true;
    niklas_anim.play();
    elena_anim.play();
  }
  background(51);
  fill(255)
  ddr_drawTexts()
  ddr_drawLanes()
  ddr_updateTextEffects()
  ddr_spawnAndDespawnArrows();
  ddr_updateArrowsAndDocks();
  niklas_anim.draw(niklas_anim.getWidth()/2,niklas_anim.getHeight()/2+100);
  elena_anim.draw(width-(elena_anim.getWidth()/2), elena_anim.getHeight()/2+100)
  ddr_checkWinFail()

  ddr_keyCommands();

}

function ddr_keyCommands(){
  ddr_collisionCheck(LEFT_ARROW, 0);
  ddr_collisionCheck(DOWN_ARROW, 1);
  ddr_collisionCheck(UP_ARROW, 2);
  ddr_collisionCheck(RIGHT_ARROW, 3);
}

function ddr_collisionCheck(key, index){
  if(keyWentDown(key)){
    ddr_checkScore(index);
    let hit = false;
    for (var i = 0; i < arrows[index].length; i++) {
      if(abs(docks[index].y-arrows[index][i].y)<docksize){
        arrows[index].splice(i,1);
        hit = true;
      }
    }
    if(!hit){
      ddr_missed++
      ddr_multiplier = 1
      ddr_multiplierCounter = 0
      ddr_startTextEffect(3)
    }
    ddr_checkMultiplier()
  }
}

function ddr_checkScore(index){
  for (var i = 0; i < arrows[index].length; i++) {
    if(abs(docks[0].y-arrows[index][i].y)<(docksize/4) && docks[index].x == arrows[index][i].x){
      ddr_score += 100*ddr_multiplier
      ddr_multiplierCounter ++
      ddr_startTextEffect(0)
    }else if(abs(docks[0].y-arrows[index][i].y)<(docksize/2)&& docks[index].x == arrows[index][i].x){
      ddr_score += 75*ddr_multiplier
      ddr_startTextEffect(1)
    }else if(abs(docks[0].y-arrows[index][i].y)<(docksize)&& docks[index].x == arrows[index][i].x){
      ddr_score +=25*ddr_multiplier
      ddr_startTextEffect(2)
    }
  }
}

function ddr_checkMultiplier(){
  if(ddr_multiplierCounter >= 3 && ddr_multiplier <= 3){
    ddr_multiplier++
    ddr_multiplierCounter = 0;
  }
}

function ddr_drawTexts(){
  fill(255)
  let xPos = width-400
  let yPos = 50
  let spacing = 50
  textFont(pixel_font, 40)
  text('Score: '+ddr_score, xPos, yPos);
  text('Multiplier: '+ddr_multiplier+'X', xPos, yPos+spacing)
  imageMode(CORNER)
  image(heart_icon, xPos, yPos+spacing*2-35)
  text('x'+ddr_lives, xPos+50, yPos+spacing*2)
}

function ddr_startTextEffect(type){
  ddr_textEffectProgression = 0;
  ddr_textEffectType = type
}

function ddr_updateTextEffects(){
  let textString = ""
  let yPos = height/2-150;
  let xPos = width/2-200
  textAlign(CENTER)
  switch (ddr_textEffectType) {
    case 0:
      fill(106,190,48)
      textString = "PERFECT"
      break;
    case 1:
      fill(251,242,54)
      textString = "ok!"
      break;
    case 2:
      fill(172, 50, 50)
      textString = "bad."
      break;
    case 3:
      fill(155,173,183)
      textString = "MISS!"
  }
  if(ddr_textEffectProgression < 10){
    let textSize = floor(ddr_textEffectProgression*5)
    textFont(pixel_font, textSize)
    text(textString, xPos, yPos )
  }else if (ddr_textEffectProgression >= 10 && ddr_textEffectProgression <= 50 ){
    textFont(pixel_font, 50)
    text(textString, xPos, yPos )
  }
  ddr_textEffectProgression++
}

function ddr_drawLanes(){
  noStroke();
  rectMode(CENTER)
  for (var i = 0; i < docks.length; i++) {
    switch (i) {
      case 0:
        if(keyIsDown(LEFT_ARROW)){
          fill(255,255,255,50)
        }else{
          fill(255,255,255,25)
        }
        break;
      case 1:
        if(keyIsDown(DOWN_ARROW)){
          fill(255,255,255,50)
        }else{
          fill(255,255,255,25)
        }
        break;
      case 2:
        if(keyIsDown(UP_ARROW)){
          fill(255,255,255,50)
        }else{
          fill(255,255,255,25)
        }
        break;
      case 3:
        if(keyIsDown(RIGHT_ARROW)){
          fill(255,255,255,50)
        }else{
          fill(255,255,255,25)
        }
        break;
    }
    rect(docks[i].x, height/2, docksize+4, height)
  }
}

function ddr_checkWinFail(){
  //win
  if(levelpos >= level.length && arrows[0].length == 0 && arrows[1].length == 0 && arrows[2].length == 0 && arrows[3].length == 0){
    if(!ddr_hasSetScoreBeenTo){
      win = true;
      $('#audio-holder').get(0).pause()
      let ddr_hasPlayed = false;
      player.beenTo.push("ddr_this_should_not_get_added_if_so_something_is_wrong")
      let beenToIndex = player.beenTo.length-1
      console.log(beenToIndex);
      for (var i = 0; i < player.beenTo.length; i++) {
        if(player.beenTo[i] == "ddr_perfect_score" || player.beenTo[i] == "ddr_good_score" || player.beenTo[i] == "ddr_okay_score" || player.beenTo[i] == "ddr_bad_score" || player.beenTo[i] == "ddr_missed_alot"){
          ddr_hasPlayed = true;
          beenToIndex = i
        }
      }
      console.log(beenToIndex);
      let arrowAmount = 0;

        for (var i = 0; i < level.length; i++) {
          for (var j = 0; j < 4; j++) {
            if(level[i][j]== 1){
              arrowAmount++
            }
          }
        }
      console.log(arrowAmount);;
      let perfectScore = (arrowAmount-9)*4*100+1800;
      let goodScore = 60000;
      let okayScore = 30000;
      let badScore = 15000;
      console.log(perfectScore);
      if (ddr_score >= perfectScore) {
        player.beenTo[beenToIndex] = "ddr_perfect_score"
      }else if(ddr_score >= goodScore){
        player.beenTo[beenToIndex] = "ddr_good_score"
      }else if(ddr_score >= okayScore){
        player.beenTo[beenToIndex] = "ddr_okay_score"
      }else if(ddr_score >= badScore){
        player.beenTo[beenToIndex] = "ddr_bad_score"
      }
      if(ddr_missed > 30){
        player.beenTo[beenToIndex] = "ddr_missed_alot"
      }
      ddr_hasSetScoreBeenTo = true;
    }
  }
  //fail
  if(ddr_lives <= 0){
    win = false;
    gameOver = true;
    $('#audio-holder').get(0).pause()
  }
}

function ddr_arrow(index){
  this.x = docks[index].x;
  this.y = 0;
  this.move = function(){
    this.y += deltaTime*arrowspeed/1000;
  }
  this.draw = function(){
    imageMode(CENTER)
    //ddr_rotateArrowToIndex(index)
    image(arrow_images[index], this.x, this.y)
  }
  this.update = function(){
    this.move();
    this.draw();
  }
}

function ddr_arrowDock(index, startX){
  this.x = startX+index*(spacing+docksize)+docksize/2+spacing
  this.y = 630 //arrowspeed*1.5

  this.draw = function(){
    imageMode(CENTER)
    //ddr_rotateArrowToIndex(index)
    image(dock_images[index], this.x, this.y)
  }
  this.update = function(){
    this.draw();
  }
}

function ddr_rotateArrowToIndex(index){
  angleMode(DEGREES)
  switch (index) {
    case 1:
      rotate(270)
      break;
    case 2:
      rotate(90)
      break;
    case 3:
      rotate(180)
      break;
  }
}

function ddr_spawnAndDespawnArrows(){
  if(ddr_time > (1/bps)*1000*ddr_timerMultiplier && levelpos < level.length){
    for (var i = 0; i < 4; i++) {
      if(level[levelpos][i] == 1){
        arrow = new ddr_arrow(i)
        arrows[i].push(arrow)
      }
    }
    for (var i = 0; i < arrows.length; i++) {
      for (var j = 0; j < arrows[i].length; j++) {
        if(arrows[i][j].y > height){
          arrows[i].splice(j,1)
          ddr_lives -= 1;
        }
      }
    }
    levelpos+=1
    ddr_timerMultiplier +=1
  }
}

function ddr_updateArrowsAndDocks(){
  for (var i = 0; i < 4; i++) {
    for (var j = 0; j < arrows[i].length; j++) {
      arrows[i][j].update()
    }
    docks[i].update();
  }
}
