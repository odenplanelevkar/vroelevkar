var bird;
var birdv;
var swimv;
var streamforce = 0.75;
var logs, rocks;
var logv, rockv;
var gameOver = true;
var startSc = true;
var score;
var fr_hard;

let fr_uppArrowHasBeenPressed;


function fr_preload(){
  //spriteImgSrc sätts i Main
  riverplayer_swim = loadAnimation(spriteImgSrc + 'riverplayer-falling.png', spriteImgSrc + 'riverplayer-swim.png', spriteImgSrc + 'riverplayer-up.png',spriteImgSrc + 'riverplayer-up.png',spriteImgSrc + 'riverplayer-up.png', spriteImgSrc + 'riverplayer-falling.png')
  riverplayer_swim.frameDelay = 3;
  riverplayer_swim.looping = false;
  log_anim = loadAnimation(spriteImgSrc + "log1.png",spriteImgSrc + "log2.png",spriteImgSrc + "log3.png" )
  log_anim.frameDelay = 12
}


function fr_defineVar(){
  win = false;
  fr_hard = true;
  for (var i = 0; i < player.beenTo.length; i++) {
    if(player.beenTo[i] == "tavern" || player.beenTo[i] == "old_house"){
      fr_hard = false;
    }
  }
  for (var i = 0; i < player.beenTo.length; i++) {
    if(player.beenTo[i]== 'flappy_river_hard_city'){
      fr_hard = true;
    }
  }

  if(fr_hard){
    swimv = -21
    streamforce = 2.5
  }else{
    streamforce = 0.75
    swimv = -11
  }
  shore = createSprite(8000, height/2, width, height+200 );
  shore.shapeColor = color(255)
  logv = -3
  rockv = 10
  score = 0;
  win = false;
  bird = createSprite(width/2,height/2, 50, 50);
  bird.addAnimation('swim', riverplayer_swim)

  fr_uppArrowHasBeenPressed = false;

  bird.shapeColor = color(255);
  //bird.rotateToDirection = true;
  bird.velocity.x = 3
  bird.setCollider('circle', 0, 0, 25)

  logs = new Group();

  camera.position.y = height/2;
  camera.position.x = width/2;

}

function fr_draw(){
  if(startSc){
    startScreen("River Swim")

  }
  if(!gameOver && !win){
    flappyDraw();
 }
 if(!startSc && gameOver && !win){
   camera.position.x = width/2;
   camera.position.y = height/2;
   gameOverScreen();
   if(clearVar){fr_deleteVar();}
 }
 if(win && !gameOver){
   camera.position.x = width/2;
   camera.position.y = height/2;
   winScreen();
   if(clearVar){fr_deleteVar();}
 }
}

function fr_deleteVar(){
  bird.remove();
  logs.removeSprites();
  shore.remove();

}

function flappyDraw(){

  if(keyWentDown(UP_ARROW)){fr_uppArrowHasBeenPressed = true;bird.velocity.x = 3}
  if(!fr_hard || fr_uppArrowHasBeenPressed){
    background(51);
    print(bird.position.x)
    drawSprites();

    if(bird.animation.getFrame()!=bird.animation.getLastFrame() && frameCount % 3 == 0)
      bird.animation.nextFrame();
    if(keyWentDown(UP_ARROW)){
      bird.animation.changeFrame(0)
      bird.velocity.y = swimv;
    }else {}
    bird.velocity.y += streamforce

    if(bird.overlap(logs) || bird.position.y > height+30 || (bird.position.y < -200)){
      gameOver = true;
    }
    if(bird.overlap(shore)){
      win = true;
    }

    if(frameCount%120 == 0 && (bird.position.x +2*width < shore.position.x)){
      Logt = createSprite(bird.position.x + width, random(-200,200), 140, 560)
      Logt.addAnimation('idle', log_anim)
      Logt.setCollider('rectangle',0,0,100,520);
      Logb = createSprite(Logt.position.x, Logt.position.y + 720, 140, 560)
      Logb.addAnimation('idle', log_anim)
      Logb.setCollider('rectangle',0,0,100,520);
      logs.add(Logt)
      logs.add(Logb)

    }
    for (var i = 0; i < logs.length; i++) {
      if(logs[i].position.x < bird.position.x-width/2){
        logs[i].remove();
      }
    }
    camera.position.x = bird.position.x + width/4
  }else{
    background(51);
    fill(255)
    textFont(pixel_font, 40)
    text('Press "UP" to start',3*width/4, height/2 )
    bird.velocity.x = 0;
    camera.position.x = bird.position.x + width/4
  }
}
