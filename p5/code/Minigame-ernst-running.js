var er_player, er_playersize, er_playerv, jumpv;
var er_gravity;
var er_spawnrate;
var er_score;
var er_ground;
var jump, slide;

let waspRun, ernstRun = false;

var gameOver = true;
var startSc = true;


function er_preload(){
  //spriteImgSrc sätts i Main
  runner_slide = loadAnimation(spriteImgSrc + 'ernstrunner-slide.png')
  runner_jump = loadAnimation(spriteImgSrc + 'ernstrunner-jump.png')
  runner_run_anim = loadAnimation(spriteImgSrc + 'ernstrunner-run1.png',spriteImgSrc + 'ernstrunner-run2.png', spriteImgSrc + 'ernstrunner-run3.png', spriteImgSrc + 'ernstrunner-run4.png')
  bookshelf_img = loadImage(spriteImgSrc + 'bookshelf.png')
  img_signs = [
    loadImage(spriteImgSrc + 'ernstrunner_sign1.png'),
    loadImage(spriteImgSrc + 'ernstrunner_sign2.png'),
    loadImage(spriteImgSrc + 'ernstrunner_sign3.png'),
    loadImage(spriteImgSrc + 'ernstrunner_sign4.png'),
    loadImage(spriteImgSrc + 'ernstrunner_sign5.png'),
    loadImage(spriteImgSrc + 'ernstrunner_sign6.png')
  ]
  circle_img_signs = []
}


function er_draw(){
  if(startSc){
    if(ernstRun){
      startScreen("Gate Run");
    }else if(waspRun){
      startScreen("Run To The Exit!");
    }
  }
  if(!gameOver){drawErnstRunning();}
  if(!startSc && gameOver){
    camera.position.x = width/2;
    camera.position.y = height/2;
    gameOverScreen();
    if(clearVar){er_deleteVar();}
  }
  if(win){
    camera.position.x = width/2;
    camera.position.y = height/2;
    winScreen();
    if(clearVar){er_deleteVar();}
  }
}

function newGame() {
  birds.removeSprites();
  er_rocks.removeSprites();
  gameOver = false;
  er_player.position.x = width/2;
  er_player.position.y = height/2;
  er_player.velocity.y = 0;
  jump = false;
  slide = false;
  er_score = 100;
}

function er_deleteVar(){
  er_player.remove();
  er_ground.remove();
  birds.removeSprites();
  er_rocks.removeSprites();
  frameCount = 0;
  er_score = 75;
}

function er_defineVar(){

  if(ernstRun){
    er_score = 75;
    er_spawnrate = 48
  }else if (waspRun){
    er_score = 50;
    er_spawnrate = 44;
  }
  if(redCircle){
    er_score = 50
    er_spawnrate = 40
    //img_signs = circle_img_signs
  }



  win = false;
  er_playersize = 50;
  er_playerv = 8;
  jumpv = 15;
  er_gravity = 1;
  er_player = createSprite(width/2,height/2, er_playersize, er_playersize);
  er_player.addAnimation('slide', runner_slide)
  er_player.addAnimation('jump', runner_jump)
  er_player.addAnimation('run', runner_run_anim)
  er_player.frameDelay = 6
  er_player.velocity.x = er_playerv + 0.01*er_score;
  er_player.setCollider('rectangle', 0, 0, er_playersize, er_playersize)
  er_ground = createSprite(width/2,3*height/4+er_playersize/2,width,height/2);
  er_ground.draw = function(){
    fill(255)
    rect(0,0,width, height/2);
  }
  birds = new Group();
  er_rocks = new Group();

  jump = false;
  slide = false;

  er_player.position.x = width/2;
  er_player.position.y = height/2;
  camera.position.y = height/2;
  camera.position.x = width/2
  er_ground.position.x = camera.position.x
}



function drawErnstRunning(){
  background(51);
  drawSprites();
  textFont(pixel_font,40)
  fill(255);
  if(ernstRun){
    text("Distance left: "+Number((420*(4/3)*floor(er_score))+195)+'m', er_player.position.x- width/2 +210, -50);

  }else{
    text("Distance left: "+Number((10*floor(er_score)))+'m', er_player.position.x- width/2 +210, -50);
  }
  er_player.velocity.x = er_playerv - 0.01*er_score;

  if(er_score <= 0){
    win = true;
  }

  if(keyIsDown(DOWN_ARROW)){
    //er_player = createSprite(width/2,height/2, er_playersize, er_playersize/2);
    slide = true;
    er_player.changeAnimation("slide")
    er_player.setCollider('rectangle', 0, er_playersize/4, er_playersize, er_playersize/2)
  }else if(keyWentUp(DOWN_ARROW)){
    er_player.setCollider('rectangle', 0, 0, er_playersize, er_playersize)
    slide = false;
    er_player.changeAnimation("run")
  }

  if(er_player.position.y < height/2 ) {
    er_player.velocity.y += er_gravity
  } else {
    er_player.velocity.y = 0;
    er_player.position.y = height/2
    jump = false;
    if(!slide) er_player.changeAnimation("run");
  }
  if(keyWentDown(UP_ARROW) && !jump && !slide){
    er_player.velocity.y = -jumpv;
    jump = true;
    er_player.changeAnimation("jump")
  }

  if(er_player.overlap(er_rocks) || er_player.overlap(birds)){
    gameOver = true;
  }

  if(frameCount%er_spawnrate == 0){
    rint = floor(random(0,2));

    if(rint == 0){
    rock = createSprite(er_player.position.x + width+random(0,120), height/2, er_playersize, er_playersize);
    if(ernstRun){
      rock.addImage(random(img_signs))
    }else{
      rock.addImage(bookshelf_img)
    }
    rock.setCollider('rectangle',0,0,er_playersize,er_playersize);
    er_rocks.add(rock);
    er_score-= 1
    }else if(rint == 1){
    bird = createSprite(er_player.position.x + width+ random(0,120), height/2- er_playersize/2-10, er_playersize, er_playersize);
    if(ernstRun){
      bird.addImage(random(img_signs))
      bird.rotation = 180;
    }else{
      bird.addAnimation('idle', wasp_anim)
    }
    birds.add(bird);
    er_score-= 1
    }
  }

  for (var i = 0; i < er_rocks.length; i++) {
    if(er_rocks[i].position.x < er_player.position.x-width/2){
      er_rocks[i].remove();
    }
  }
  for (var i = 0; i < birds.length; i++) {
    if(birds[i].position.x < er_player.position.x-width/2){
      birds[i].remove();
    }
  }
  camera.position.y = height/3
  camera.position.x = er_player.position.x+width/3

  er_ground.position.x = camera.position.x


}
