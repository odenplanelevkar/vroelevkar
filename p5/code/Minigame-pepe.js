var pb_player, pb_playersize, pb_playerspeed;
var pb_bread = 0;
var handresistance;
var pb_breadbowl;
var pepe, looktimer, pepecolors, pepe_state;
var gameOver;
var startSc;

var pb_background_img;

function pb_preload(){
  breadbowl_img = loadImage(spriteImgSrc +'breadbowl.png');
  pepe_idle_animation = loadAnimation(spriteImgSrc +'Pepe-idle1.png',spriteImgSrc +'Pepe-idle2.png',spriteImgSrc +'Pepe-idle3.png');
  pepe_idle_animation.frameDelay = 15;
  pepe_semi_alerted = loadAnimation(spriteImgSrc +'Pepe-semi-alerted.png');//borde bara visas som en bild
  pepe_fully_alerted = loadAnimation(spriteImgSrc +'Pepe-full-alerted.png');
  hand_animation = loadAnimation(spriteImgSrc +'pepe-hand-idle.png',spriteImgSrc +'Pepe-hand-stretch.png',spriteImgSrc +'pepe-hand-grabbed.png');
  hand_animation.playing = false;
  pb_background_img = loadImage(spriteImgSrc +'Pepe-bakgrund.png')
}

function pb_setup(){
  pb_defineVar();
}

function pb_draw(){
  if(startSc){
    startScreen("Grab the bread");
  }
  if(!gameOver){
    drawPepe();
  }
  if(gameOver && !startSc){
    gameOverScreen();
    if(clearVar){
      pb_deleteVar();
    }
  }
  if(win && !gameOver){
    camera.position.x = width/2;
    camera.position.y = height/2;
    winScreen();
  }
  if(clearVar){
    pb_deleteVar();
  }
}



function pb_defineVar(){

    pb_bread = 15;
    camera.position.x = width/2;
    camera.position.y = height/2;
    gameOver = true;
    startSc = true;
    pb_playersize = 50;
    pb_playerwidth = width/2
    pb_playerspeed = 10;
    handresistance = 1.2;
    pb_breadbowl = createSprite(width/2, height/2, 50,50);
    pb_breadbowl.addImage(breadbowl_img);
    looktimer = floor(random(120, 60*5));

    pepe = createSprite(480,380,50,50);
    pepe.addAnimation('idle',pepe_idle_animation);
    pepe.addAnimation('semi_alerted', pepe_semi_alerted);
    pepe.addAnimation('fully_alerted', pepe_fully_alerted);

  //  pepecolors = [color(0, 255, 0), color(255, 255, 0), color(255, 0, 0)];
    pepe_state = 0;
    pb_player = createSprite(-pb_playerwidth/3, height/2, pb_playerwidth, pb_playersize);
    pb_player.addAnimation('grab', hand_animation);

    pb_player.setCollider('rectangle', 0, 0, pb_playerwidth, pb_playersize)
}


function pb_deleteVar(){

    pb_breadbowl.remove();
    pepe.remove();
    pb_player.remove();
}

function drawPepe(){
  background(51);
  image(pb_background_img,0,0);
  fill(255);
  drawSprites();
  textFont(pixel_font, 40)
  text("Bread left: "+pb_bread, 12, 50);
  if(pb_bread<=0){
    win = true;
  }

  if(pb_player.position.x > -pb_playerwidth/3){
    pb_player.velocity.x += -handresistance;
  } else { pb_player.velocity.x = 0; pb_player.position.x = -pb_playerwidth/3}
  if(keyWentDown(RIGHT_ARROW)){
    pb_player.velocity.x = 10;
  }
  if(pb_player.overlap(pb_breadbowl)){
    pb_bread-=1
    pb_player.position.x = -pb_playerwidth/3;
    handresistance += 0.1
  }

  pb_player.animation.changeFrame(0);
  if(pb_player.position.x > 0){
    pb_player.animation.changeFrame(1);
  }
  if(pb_player.velocity.x < -1){
    pb_player.animation.changeFrame(2);
  }

  switch (pepe_state) {
    case 0:
      pepe.changeAnimation('idle');
      break;
    case 1:
      pepe.changeAnimation('semi_alerted');
      break;
    case 2:
      pepe.changeAnimation('fully_alerted')
      break;

  }

  if(pepe_state == 2 && pb_player.velocity.x > 5){
    gameOver = true;
  }

  if(frameCount%looktimer == 0){

    add = 0;
    if(pepe_state == 1){

      if(floor(random(0,2)) == 0){
        add++
        if(pb_player.velocity .x > pb_playerspeed-1){
          //gameOver
        }
      }else {
        add += -1
        looktimer += floor(random(60,4*30)) //måste göra så att den inte kan bli större än innan och gå igenom igen
      }


    }else if(pepe_state == 0){
      add++
      looktimer += floor(random(30, 6*30))
    }else if(pepe_state == 2){
      add = -2
      looktimer += floor(random(30, 3*30))


    }
    pepe_state += add;


  }
}

function pepe_newGame(){
  pb_player.position.x = -pb_playerwidth/3
  pb_player.position.y = height/2;
  gameOver = false;
  handresistance = 1.5
  pepe_state = 0;
  looktimer = floor(random(120, 60*5));
  pb_bread = 0;
}
