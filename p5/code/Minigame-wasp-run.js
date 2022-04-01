var wr_player, wr_playersize, wr_playerv, wr_jumpv;
var wr_gravity;
var wr_spawnrate;
var wr_waspsLeft;
var wr_ground;

let wr_slide
let wr_forward_attack
let wr_run
let wasp_dead_anim
let wr_time;
let wr_player_state; //0 = running, 1 = forward attack, 2 = slide attack


function wr_preload(){
  //spriteImgSrc sätts i Main
  wr_slide = loadAnimation(
    spriteImgSrc + 'wasp_runner_anims/wasp_runner_slide0000.png',
    spriteImgSrc + 'wasp_runner_anims/wasp_runner_slide0007.png')
  wr_forward_attack = loadAnimation(
    spriteImgSrc + 'wasp_runner_anims/wasp_runner_charge0000.png',
    spriteImgSrc + 'wasp_runner_anims/wasp_runner_charge0007.png')
  wr_run = loadAnimation(
    spriteImgSrc + 'wasp_runner_anims/wasp_runner_run0000.png',
    spriteImgSrc + 'wasp_runner_anims/wasp_runner_run0007.png')
  wasp_dead_anim = loadImage(spriteImgSrc +"wasp_dead.png")
}


function wr_draw(){
  if(startSc){startScreen("Wasp Run");}
  if(!gameOver && !win && !startSc){wr_game();}
  if(!startSc && gameOver){
    camera.position.x = width/2;
    camera.position.y = height/2;
    gameOverScreen();
    if(clearVar){wr_deleteVar();}
  }
  if(win){
    camera.position.x = width/2;
    camera.position.y = height/2;
    winScreen();
    if(clearVar){wr_deleteVar();}
  }
}

function wr_deleteVar(){
  wr_player.remove();
  wr_ground.remove();
  for(const enemy of wr_enemies){
    enemy.remove();
  }
  wr_enemies = [];
  wr_waspsLeft = 75;
  wr_time = 0;
  wr_player_state = 0;
}

function wr_defineVar(){
  //global variables
  win = false;
  gameOver = false;

  //general variables
  wr_waspsLeft = 75;
  wr_spawnrate = 750 //ms between spawn
  wr_playersize = 50;
  wr_playerv = 8;
  wr_jumpv = 15;
  wr_gravity = 1;
  wr_time = 0;
  wr_player_state = 0;

  //camera
  camera.position.y = height/2;
  camera.position.x = width/2

  //player
  wr_player = createSprite(width/2,height/2, wr_playersize, wr_playersize);
  wr_player.addAnimation('slide', wr_slide)
  wr_player.addAnimation('forward_attack', wr_forward_attack)
  wr_player.addAnimation('run', wr_run)
  wr_player.changeAnimation('run')
  wr_player.frameDelay = 6
  wr_player.velocity.x = wr_playerv + 0.01*wr_waspsLeft;
  wr_player.setCollider('rectangle', 0, 0, wr_playersize, wr_playersize)

  //ground
  wr_ground = createSprite(width/2,3*height/4+wr_playersize/2+4,width,height/2);
  wr_ground.draw = function(){
    fill(255)
    rect(0,0,width, height/2);
  }
  wr_ground.position.x = camera.position.x

  //enemies
  wr_enemies = [];
}



function wr_game(){

  background(51);
  wr_time += deltaTime
  drawSprites();
  wr_checkCollision(wr_player, wr_enemies);
  wr_scoreText();
  wr_checkWin();
  wr_updatePlayerState();
  wr_spawnEnemies(wr_spawnrate);

  camera.position.y = height/3
  camera.position.x = wr_player.position.x+width/3
  wr_ground.position.x = camera.position.x
}


function wr_createWasp(type){

  let spawnHeight;
  let waspType
  // 1 = grounded, 2 = flying
  if(type == 1){
    spawnHeight = height/2
    waspType = 1
  }else{
    spawnHeight = height/2- wr_playersize/2-10
    waspType = 2
  }

  let wasp = createSprite(wr_player.position.x + width+random(0,120), spawnHeight, wr_playersize, wr_playersize);
  wasp.addAnimation('dead', wasp_dead_anim)
  wasp.addAnimation('idle', wasp_anim)
  wasp.changeAnimation('idle')
  wasp.setCollider('circle', 0, 0, wr_playersize/2)
  wasp.type = waspType
  wasp.acceleration = 0.5
  wasp.isDead = false;

  wasp.die = function(){
    this.changeAnimation('dead')
    if(this.type == 1){
      this.setVelocity(10,-10)
    }else{
      this.setVelocity(0,-10)
    }
    this.isDead = true;
    //add gravity nice die thingie
  }
  wasp.accelerate = function(){
    this.velocity.y += this.acceleration
    if(this.position.y > height){
      if(!this.removed){wr_waspsLeft-=1}
      this.remove()
    }
  }
  wr_enemies.push(wasp);

}

function wr_spawnEnemies(rate){
  if(wr_time>rate && wr_waspsLeft > 1){
    rint = floor(random(0,2));
    if(rint == 0){
    wr_createWasp(1);
    }else if(rint == 1){
    wr_createWasp(2);
    }
    wr_time = 0;
  }
}

function wr_updatePlayerState(){
  //slide handling
  if(keyIsDown(DOWN_ARROW) && wr_player_state == 0){
    wr_player_state = 2;
    wr_player.changeAnimation("slide")
    wr_player.setCollider('rectangle', -40, 0, wr_playersize/2, wr_playersize)
  }else if(keyWentUp(DOWN_ARROW)){
    wr_player_state = 0;
    wr_player.setCollider('rectangle', 0, 0, wr_playersize, wr_playersize)
    wr_player.changeAnimation("run")
  }

  //forward attack handling
  if(keyWentDown(RIGHT_ARROW) && wr_player_state == 0){
    wr_player.changeAnimation("forward_attack")
    wr_player_state = 1;
  }else if(keyWentUp(RIGHT_ARROW)){
    wr_player.changeAnimation("run")
    wr_player_state = 0;
  }
}

function wr_checkCollision(player, enemies){
  for (var i = 0; i < enemies.length; i++) {
    if(enemies[i].overlap(player) && enemies[i].isDead == false){
      if(wr_player_state == enemies[i].type){
        enemies[i].die()
      }else{
        gameOver = true;
      }
    }
    if (wr_enemies[i].isDead) {
      wr_enemies[i].accelerate();
    }
  }
}

function wr_checkWin(){
  if(wr_waspsLeft <= 0){
    win = true;
  }
}

function wr_scoreText(){
  textFont(pixel_font,40)
  fill(255);
  text("Wasps left: " + wr_waspsLeft, wr_player.position.x- width/2 +210, -50);
  wr_player.velocity.x = wr_playerv - 0.01*wr_waspsLeft;
}
