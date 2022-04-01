let wc_time;
let wc_enemies;
let wc_resizeSpeed;
let wc_timeBetweenEnemySpawn;
let crosshair
let wc_waspsLeft;
let wc_waspAmount;
let wc_waspsSpawned;

let wc_mouseHasBeenPressed

function wc_preload(){
  crosshair = loadImage(spriteImgSrc + 'crosshair.png')
}
function wc_draw(){
  if(startSc){
    startScreen("Wasp Attack!");
  }
  if(!gameOver && !win && !startSc){
    wc_game();
  }
  if(!startSc && gameOver && !win){
    gameOverScreen();
    if(clearVar){
      wc_resetVar();
    }
  }
  if(win && !gameOver){
    camera.position.x = width/2;
    camera.position.y = height/2;
    winScreen();
  }
  if(clearVar){
    wc_resetVar();
  }
}

function wc_defineVar(){
  wc_time = 0;
  wc_timeBetweenEnemySpawn = 1000;
  wc_resizeSpeed = 0.005
  wc_enemies = new Group;
  wc_waspAmount = 60;
  wc_waspsSpawned = 0;
  wc_mouseHasBeenPressed = false;

  wc_waspsLeft = wc_waspAmount;

  camera.position.x = width/2;
  camera.position.y = height/2;

  startSc = true;
  gameOver = false;
  win = false;
}

function wc_resetVar(){
  wc_time = 0;
  wc_enemies.removeSprites();
  wc_waspsLeft = wc_waspAmount;
  wc_waspsSpawned = 0;
  wc_mouseHasBeenPressed = false;

  camera.position.x = width/2;
  camera.position.y = height/2;

  startSc = true;
  gameOver = false;
  win = false;
}

function wc_game(){
  background(51);
  drawSprites();
  imageMode(CENTER)
  image(crosshair, mouseX, mouseY)

  fill(255)
  textFont(pixel_font, 40)
  text("Wasps left: "+wc_waspsLeft, 12, 50);

  if(wc_waspsLeft <= 0){
    win = true;
  }
  if(mouseWentUp(LEFT)){wc_mouseHasBeenPressed = false;}
  wc_time += deltaTime;
  wc_spawnEnemies(wc_timeBetweenEnemySpawn-((wc_waspAmount-wc_waspsLeft)*15))
  for (var i = 0; i < wc_enemies.length; i++) {
    if(wc_enemies[i].scale > 2.5){
      gameOver = true;
    }
    if(!wc_enemies[i].isDead){
      wc_enemies[i].updateSize();
      wc_enemies[i].mouseUpdate();
    }else{
      wc_enemies[i].updateGravity();
    }
  }
}

function wc_createWasp(x,y){
  this.x = x;
  this.y = y;
  this.size = 60
  this.sprite = createSprite(this.x,this.y,this.size,this.size)
  this.sprite.addAnimation("idle", wasp_anim)
  this.sprite.addAnimation('dead', wasp_dead_anim)
  this.sprite.animation.looping = true;
  this.sprite.animation.play();
  this.sprite.scale = 0.01
  this.sprite.mouseActive = true;
  this.sprite.isDead = false;
  this.sprite.acceleration = 0.5
  this.sprite.onMousePressed = function(){
    if(!wc_mouseHasBeenPressed){
      this.isDead = true
      this.changeAnimation('dead')
      this.setVelocity(random(-5*this.scale,5*this.scale), -10*this.scale)
      wc_mouseHasBeenPressed = true
    }
  }
  this.sprite.updateSize = function(){
    this.scale += wc_resizeSpeed
    this.setCollider('circle', 0,0,30*this.scale)
    if(frameCount % 6 == 0){
      this.animation.nextFrame()
    }
  }
  this.sprite.updateGravity = function(){
    this.velocity.y += this.acceleration*this.scale
    if(this.position.y > height){
      if(!this.removed){wc_waspsLeft-=1}
      wc_enemies.remove(this);
      this.remove();
    }
  }
  wc_enemies.add(this.sprite)
}

function wc_spawnEnemies(rate){
  if(wc_time > rate && wc_waspsSpawned<=wc_waspAmount){
    wc_createWasp(random(60,width-60), random(60,height-60));
    wc_time = 0;
    wc_waspsSpawned += 1
  }
}
