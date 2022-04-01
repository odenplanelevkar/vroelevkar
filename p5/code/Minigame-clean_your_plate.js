var cyp_player, cyp_playersize, cyp_playerspeed;
var cyp_food = 0;
var cyp_foodResistance;
var cyp_mouthCollider;
var gameOver;
var startSc;

let cyp_swallowing = false;
let cyp_bread = false;

var cyp_background_img;

function cyp_preload(){
  mouth_open = loadAnimation(
    spriteImgSrc + 'mouth_closed.png',
    spriteImgSrc + 'mouth_open_1.png',
    spriteImgSrc + 'mouth_open_2.png',
    spriteImgSrc + 'mouth_open_3.png'
  )
  mouth_open.looping = false;
  mouth_swallow = loadAnimation(
    spriteImgSrc + 'mouth_swallow.png',
    spriteImgSrc + 'mouth_closed.png'
  )
  mouth_swallow.frameDelay = 50

  spoon = loadAnimation(
    spriteImgSrc + 'spoon_empty.png',
    spriteImgSrc + 'spoon_full.png'
  )
  spoon.looping = false;
}

function cyp_setup(){
  cyp_defineVar();
}

function cyp_draw(){
  if(startSc){
    let cyp_startText;
    if(cyp_bread){
      cyp_startText = "Eat your mouldy bread!"
    }else{
      cyp_startText = "Clean your plate!"
    }
    startScreen(cyp_startText);
  }
  if(!gameOver){
    cyp_game();
  }
  if(gameOver && !startSc){
    gameOverScreen();
    if(clearVar){
      cyp_deleteVar();
    }
  }
  if(win && !gameOver){
    camera.position.x = width/2;
    camera.position.y = height/2;
    winScreen();
  }
  if(clearVar){
    cyp_deleteVar();
  }
}

function cyp_defineVar(){
  cyp_bread = false;
  for (var i = 0; i < player.beenTo.length; i++) {
    if(player.beenTo[i] == "clean_plate_bread"){
      cyp_bread = true;
    }
  }
  if(cyp_bread){
    cyp_food = 1
    cyp_foodResistance = 3.6;
  }else{
    cyp_food = 15;
    cyp_foodResistance = 1.2;
  }
  camera.position.x = width/2;
  camera.position.y = height/2;
  gameOver = true;
  startSc = true;
  cyp_playersize = 50;
  cyp_playerwidth = width/2
  cyp_playerspeed = 10;

  cyp_mouthCollider = createSprite(width/2+30, height/2, 50,50);
  cyp_mouthCollider.visible = false

  cyp_mouthVisual = createSprite(width/2, height/2, width, height)
  cyp_mouthVisual.addAnimation('swallow', mouth_swallow)
  cyp_mouthVisual.addAnimation('open', mouth_open)
  cyp_mouthVisual.changeAnimation('open')
  cyp_mouthVisual.animation.changeFrame(0)
  cyp_mouthVisual.animation.stop()

  cyp_player = createSprite(-cyp_playerwidth/3, height/2, cyp_playerwidth, cyp_playersize);
  cyp_player.addAnimation('spoon', spoon);
  cyp_player.setCollider('rectangle', 0, 0, cyp_playerwidth, cyp_playersize)
}

function cyp_deleteVar(){
  cyp_mouthCollider.remove();
  cyp_mouthVisual.remove()
  cyp_player.remove();
  gameOver = false;
  win = false;
  if(cyp_bread){
    cyp_food = 1;
  }else {
    cyp_food = 15;
  }
}

function cyp_game(){
  background(51);
  fill(255);
  drawSprites();
  textFont(pixel_font, 40)
  if(cyp_bread){
    text("Food left: "+cyp_food + " bread", 12, 50);
  }else{
    text("Food left: "+cyp_food + " spoons", 12, 50);
    text("Press 'x' to give up", 12, height-30)
  }
  cyp_mouthAnimationController()

  if(cyp_food<=0){
    win = true;
  }
  if(cyp_player.position.x > -cyp_playerwidth/3){
    cyp_player.velocity.x += -cyp_foodResistance;
  } else { cyp_player.velocity.x = 0; cyp_player.position.x = -cyp_playerwidth/3}
  if(keyWentDown(RIGHT_ARROW)){
    cyp_player.velocity.x = 10;
  }

  if(cyp_player.animation.getFrame() < 1 && frameCount % 30 == 0){
    cyp_player.animation.nextFrame();
  }

  if(cyp_player.overlap(cyp_mouthCollider)){
    cyp_player.animation.changeFrame(0);
    cyp_food-=1
    cyp_player.position.x = -cyp_playerwidth/3;
    cyp_mouthVisual.changeAnimation('swallow')
    cyp_swallowing = true
    cyp_foodResistance += 0.1
  }
  if(keyWentDown('x') && !cyp_bread){
    gameOver = true;
  }

}

function cyp_mouthAnimationController(){
  if(cyp_mouthVisual.animation.getFrame() > 0 && cyp_swallowing){
    cyp_mouthVisual.changeAnimation('open')
    cyp_mouthVisual.animation.changeFrame(0)
    cyp_swallowing = false;
  }
  if(cyp_player.position.x < -cyp_playerwidth/3+10 && frameCount % 15 == 0){
    cyp_mouthVisual.animation.changeFrame(0)
  }else if(cyp_player.position.x > -cyp_playerwidth/3+10 && cyp_player.position.x < -cyp_playerwidth/3 + 70 && frameCount % 15 == 0){
    cyp_mouthVisual.animation.changeFrame(1)
  }else if(cyp_player.position.x > -cyp_playerwidth/3 + 70 && cyp_player.position.x < -cyp_playerwidth/3 + 70*2 && frameCount % 15 == 0){
    cyp_mouthVisual.animation.changeFrame(2)
  }else if(cyp_player.position.x > -cyp_playerwidth/3 + 70*2 && frameCount % 15 == 0){
    cyp_mouthVisual.animation.changeFrame(3)
  }

}
