var mj_player, mj_playersize, mj_playerv, mj_jumpv;
let mj_ground;
var mj_gravity;
var spawnrate;
var mj_score;
var currentheight;
var rockwidth;
var friction;

var rocks;
var pens, penv;

var startSc = true;
var gameOver = true;

var difficulty;
var rockheight;
var mj_jumpedOnBoost = false;
let warning_img;
let mj_diff_increase1, mj_diff_increase2, mj_diff_increase3, mj_win_score;
let mj_rod_spawn_rate;
let mj_startedFalling = false;

let mj_short_path = false;
let mj_long_path = false;

function mj_preload(){
  mj_pen_images = [
    loadImage(spriteImgSrc +'mountain_jump/pen_red.png'),
    loadImage(spriteImgSrc +'mountain_jump/pen_blue.png'),
    loadImage(spriteImgSrc +'mountain_jump/pen_green.png'),
    loadImage(spriteImgSrc +'mountain_jump/pen_black.png')
  ]

  mj_platforms = [
    [loadImage(spriteImgSrc +'mountain_jump/regular_platform.png'),loadImage(spriteImgSrc +'mountain_jump/regular_platform_2.png'),loadImage(spriteImgSrc +'mountain_jump/regular_platform_3.png')],
    loadImage(spriteImgSrc +'mountain_jump/bouncy_platform.png'),
    loadImage(spriteImgSrc +'mountain_jump/cracky_platform.png')
  ]

  warning_img = loadImage(spriteImgSrc +'mountain_jump/warning.png')
  mj_jump_anim = loadAnimation(spriteImgSrc + 'mountain_jump/player_jump_0002.png',spriteImgSrc + 'mountain_jump/player_jump_0004.png')
  mj_jump_anim.looping = false;
  mj_falling_anim = loadAnimation(spriteImgSrc + 'mountain_jump/player_falling.png')
  mj_falling_anim.looping = false;
}

function mj_draw(){
  if(startSc && !gameOver && !win){
    startScreen("Mountain Jump");
  }

  if(!gameOver && !startSc && !win){
    background(51);
    mountainJumpDraw();

 }
  if(gameOver && !startSc && !win){
    gameOverScreen();
    camera.position.x = width/2;
    camera.position.y = height/2;

    if(clearVar){
      mj_deleteVar();
      resizeCanvas(600,600);}

  }if(win && !startSc && !gameOver){
    winScreen();
    camera.position.x = width/2
    camera.position.y = height/2
    if(clearVar){
      mj_deleteVar();
      resizeCanvas(600,600)
    }
  }
}

function mj_defineVar(){
  for (var i = 0; i < player.beenTo.length; i++) {
    if(player.beenTo[i]== "mj_short_path"){
      mj_short_path = true;
    }
    if(player.beenTo[i]== "mj_long_path"){
      mj_long_path = true;
    }
  }
  for (var i = 0; i < player.beenTo.length; i++) {
    if(player.beenTo[i] == "mj_final_ascent"){
      mj_long_path = false;
      mj_short_path = false;
    }
  }

  if(mj_long_path){
    mj_diff_increase1 = 5000;
    mj_win_score = 15000;
    mj_diff_increase2 = mj_win_score +1
    mj_diff_increase3 = mj_win_score +1
    mj_rod_spawn_rate = 800
  }else if(mj_short_path){
    mj_diff_increase1 = 0;
    mj_diff_increase2 = 1000
    mj_diff_increase3 = 3000;
    mj_win_score = 5000;
    mj_rod_spawn_rate = 1200
  }else{
    mj_diff_increase1 = 2500;
    mj_diff_increase2 = 5000;
    mj_diff_increase3 = 6500;
    mj_win_score = 8000;
    mj_rod_spawn_rate = 300
  }
  mj_time = 0;
  friction = 0.08;
  mj_score = 0;
  difficulty = 1; //3 = max
  oldscore = 0;
  currentheight = 0;
  penv = 2;
  mj_jumpedOnBoost = false;
  rockwidth = 74;
  rockheight = 32
  spawnrate = floor(random(20,40));
  mj_playersize = 40;
  mj_playerv = 4;
  mj_jumpv = 20;
  mj_gravity = 0.4;
  mj_player = createSprite(width/2,height/2, mj_playersize, mj_playersize);
  mj_player.addAnimation('falling', mj_falling_anim);
  mj_player.addAnimation('jump', mj_jump_anim);
  mj_player.velocity.x = mj_playerv + 0.01*mj_score;
  mj_player.setCollider('circle', 0, 0, mj_playersize/2)
  mj_ground = createSprite(0,height, width*5, 1000);
  mj_ground.shapeColor = color(255)
  rocks = new Group();
  pens = new Group();

  startSc = true;
  win = false;
  gameOver = false;
  resizeCanvas(400,720)
  camera.position.y = height/2;
  camera.position.x = width/2
}
function mj_deleteVar(){
  rocks.removeSprites();
  pens.removeSprites();
  mj_player.remove();
  mj_ground.remove();
}

function mountainJumpDraw(){
  drawSprites();

  fill(255);
  for (var i = 0; i < pens.length; i++) {
    if(pens[i].position.y < camera.position.y -360){
      imageMode(CENTER)
      image(warning_img, pens[i].position.x, camera.position.y-300)
    }
  }

  if(mj_player.velocity.y < 0 && mj_player.position.y <= camera.position.y){
    camera.position.y = mj_player.position.y
  }
  if(mj_player.position.y > camera.position.y+width){
    gameOver = true;
  }
  textFont(pixel_font, 40)
  textAlign(RIGHT)
  text(mj_score + "m",  width -20, camera.position.y-height/2+100)
  if(mj_player.velocity.y > 0){
    if(!mj_startedFalling){
      mj_player.changeAnimation('falling')
      mj_startedFalling = true;
    }
  }

  mj_player.velocity.y += mj_gravity
  if(mj_score > mj_diff_increase1){
    difficulty = 2;
  }
  if(mj_score > mj_diff_increase2){
    difficulty = 3;
  }
  if(mj_score > mj_diff_increase3){
    difficulty = 4;
  }
  if(mj_player.overlap(mj_ground)){
    mj_player.velocity.y = -2*mj_jumpv;
  }
  if(mj_score > mj_win_score){
    win = true;
  }

  if(keyDown(LEFT_ARROW) && mj_player.velocity.x >-2*mj_playerv){
    mj_player.velocity.x -= 0.3*mj_playerv;
  }else if(!keyDown(LEFT_ARROW) &&mj_player.velocity.x < 0){
    mj_player.velocity.x +=-1*friction*mj_player.velocity.x
  }
  if(keyDown(RIGHT_ARROW)&& mj_player.velocity.x <2*mj_playerv){
    mj_player.velocity.x += 0.3*mj_playerv;
  }else if(!keyDown(RIGHT_ARROW) && mj_player.velocity.x > 0){
      mj_player.velocity.x -=friction*mj_player.velocity.x
  }
  if(mj_player.position.x < 0){
    mj_player.position.x = width;
  }
  if(mj_player.position.x > width){
    mj_player.position.x = 0;
  }
  if (frameCount % floor(mj_rod_spawn_rate/difficulty) == 0 && mj_player.velocity.y >-10) {

    pen = createSprite(random(10,width-10),camera.position.y-height-200,12,64);
    pen.addImage(random(mj_pen_images))
  //  pen.setCollider('c',0,0,10,60);
    pen.rotationSpeed = random(-1,2)*10;
    pen.velocity.y = penv*difficulty;

    pens.add(pen);
  }

  for (var i = 0; i < pens.length; i++) {
    if(mj_player.overlap(pens[i]) && !mj_jumpedOnBoost){
      gameOver = true;
      //pens[i].remove();
      //i-=1
    }
    // } else if(mj_player.overlap(pens[i])&& mj_player.velocity.y> 0){
    //   mj_player.velocity.y = -0.5*mj_jumpv;
    //   //pens[i].remove();
    //   //i-=1
    // }
    if(pens[i].position.y > mj_player.position.y+height){
      pens[i].remove();
    }
  }

  if(mj_player.position.y < currentheight){
    currentheight = mj_player.position.y - 400;
    mj_score += 100;
    for (var i = 0; i < 5-difficulty; i++) {
      randint = floor(random(0, 20));
      if(difficulty<3){
        rock = createSprite(random(rockwidth/2+width*i/3, width*(i+1)/3), random(currentheight-width/2-50, currentheight-width/2-200), rockwidth, rockheight);
      }else{
        rock = createSprite(random(rockwidth/2, width-rockwidth/2), random(currentheight-width/2-50, currentheight-width/2-200), rockwidth, rockheight);

      }
      rock.setCollider('rectangle',0,0,rockwidth,rockheight);

      if(randint >=0 && randint <17-difficulty){rock.type = 0;rock.addImage(random(mj_platforms[0]));}
      if(randint ==17-difficulty ){rock.type = 1; rock.addImage(mj_platforms[1]);}//jump
      if(randint > 17-difficulty && randint< 20-difficulty){rock.type = 2; rock.addImage(mj_platforms[2]);}//break
      if(randint >= 20-difficulty){rock.type = 3; rock.addImage(random(mj_platforms[0])); rock.dir = random(-difficulty,difficulty)}//move

      rocks.add(rock);
    }
  }

  for (var i = 0; i < rocks.length; i++) {
    if(rocks[i].position.y > mj_player.position.y+height){
      rocks[i].remove();
    }
    if(rocks[i].type == 3){
      if(rocks[i].position.x > width -rockwidth/2){rocks[i].dir = -1}
      if(rocks[i].position.x < rockwidth/2){rocks[i].dir = 1}
      rocks[i].velocity.x = rocks[i].dir*0.7*difficulty;

    }
    if(mj_player.overlap(rocks[i])&& mj_player.velocity.y >0){
      mj_player.changeAnimation('jump');
      mj_player.animation.changeFrame(0);
      mj_startedFalling = false;
      switch (rocks[i].type) {
        case 0:
          mj_player.velocity.y = -mj_jumpv;
          mj_jumpedOnBoost = false;
          break;
        case 1:
          mj_player.velocity.y = -2*mj_jumpv
          mj_jumpedOnBoost = true;
          rock = createSprite(mj_player.position.x, mj_player.position.y-1900, rockwidth, rockheight); //skapar en där man hamnar
          rock.type = 0
          rock.addImage(random(mj_platforms[0]));
          rocks.add(rock)
          break;
        case 2:
          mj_player.velocity.y = -mj_jumpv;
          mj_jumpedOnBoost = false;
          rocks[i].remove();
          break;
        case 3:
          mj_player.velocity.y = -mj_jumpv;
          mj_jumpedOnBoost = false;
          break;
      }
    }
  }
}
