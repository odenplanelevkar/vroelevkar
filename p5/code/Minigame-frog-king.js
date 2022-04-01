let fk_player;
let fk_playerValues = {};
let fk_toungeActive = false
let fk_hasShotTounge = false;
let fk_startedToungeRetraction = false;
let fk_mouseVector;

let fk_flies;
let fk_flySpeed;
let fk_fliesOnTounge;
let fk_timeBetweenEnemySpawn;

let fk_time;
let fk_score;
let flyAmount;
let flySpawnCount;

function fk_preload(){
  fly_animation = loadAnimation(spriteImgSrc + 'fly1.png',spriteImgSrc + 'fly2.png')
  frog_animation = loadAnimation(spriteImgSrc + 'frog_look_left.png', spriteImgSrc + 'frog_look_up.png', spriteImgSrc + 'frog_look_right.png')
  //circle_fly_animation = loadAnimation()
}
function fk_defineVar(){


  //general setup
  fk_time = 0;
  fk_score = 0;
  mouseVector = createVector(mouseX, mouseY)
  win = false
  gameOver = false
  startSc = true

  //difficulty parameters
  if(!redCircle){
    fk_timeBetweenEnemySpawn = 800;//ms
    fk_playerValues = {x:width/2 , y:(height-30) , width: 152, height: 108, toungeSpeed: 15, toungeColliderRadius: 20};
    fk_flySpeed = 2;
    flyAmount = 100;
    flySpawnCount = 0;
  }else if(redCircle){
    fk_timeBetweenEnemySpawn = 200;//ms
    fk_playerValues = {x:width/2 , y:(height-30) , width: 152, height: 108, toungeSpeed: 15, toungeColliderRadius: 40};
    fk_flySpeed = 1.7;
    flyAmount = 200;
    flySpawnCount = 0;
  }



  //camera
  camera.position.x = width/2;
  camera.position.y = height/2;

  //player setup
  fk_player = createSprite(fk_playerValues.x, fk_playerValues.y, fk_playerValues.width, fk_playerValues.height);
  fk_player.addAnimation('look_direction', frog_animation)
  fk_player.animation.looping = false;
  fk_player.tounge = new tounge(fk_player.position.x, fk_player.position.y-fk_playerValues.height/2);

  //enemy setup
  fk_flies = [];
  fk_fliesOnTounge = [];
}
function fk_draw(){
  if(startSc){
    startScreen("Frog King")
  }
  if(!gameOver && !win && !startSc){
    fk_game();
  }
  if(!startSc && gameOver && !win){
   camera.position.x = width/2;
   camera.position.y = height/2;
   gameOverScreen();
   if(clearVar){fk_resetVar();}
  }
  if(win && !gameOver && !startSc){
   camera.position.x = width/2;
   camera.position.y = height/2;
   winScreen();
  if(clearVar){fk_resetVar();}
  }
}
function fk_resetVar(){
  //reset general variables
  fk_time = 0
  fk_flySpeed = 2
  fk_score = 0;
  camera.position.x = width/2
  camera.position.y = height/2
  flySpawnCount = 0;

  //delete player
  fk_player.tounge.endPoint.remove()
  fk_player.remove()

  //delete all enemies
  for(const fly of fk_flies){fly.sprite.remove()}
  for(const fly of fk_fliesOnTounge){fly.sprite.remove()}
  fk_flies = [];
  fk_fliesOnTounge = [];
}
function fk_game(){

  //general updates
  background(51);
  fk_time += deltaTime
  if(fk_score < 20 && !redCircle){
    spawnFlies(1500)
  }else{
    spawnFlies(fk_timeBetweenEnemySpawn-fk_score*2)
  }
  drawSprites();
  checkFrogAnimationState();
  fk_player.tounge.draw();

  if(fk_score>=flyAmount){
    win = true
  }

  noStroke()
  //fill(51, 51, 51, 50)
  //rect(0,0, 300, 80)
  fill(255)
  textFont(pixel_font, 40)
  let fliesLeftText = flyAmount-fk_score
  text("Flies left: "+fliesLeftText, 12, 50);

  for (var i = 0; i < fk_flies.length; i++) {
    fk_flies[i].draw();
    if(fk_flies[i].sprite.position.y > height+100){
      fk_flies[i].sprite.remove()
      fk_flies.splice(i,1)
      gameOver = true
    }
  }
  for (var i = 0; i < fk_fliesOnTounge.length; i++) {
    fk_fliesOnTounge[i].draw();
  }
  //collision checks
  checkEnemyCollisionWithTounge();

  //tounge movement
  updateToungeMovement();
}

//update checks
function checkEnemyCollisionWithTounge(){
  for (var i = 0; i < fk_flies.length; i++) {
    if(fk_flies[i].sprite.overlap(fk_player.tounge.endPoint)){
      addFlyToTounge(fk_flies[i])
      fk_flies.splice(i,1)
    }
  }
}
function checkFrogAnimationState(){
  if(mouseX < width/4){
    fk_player.animation.goToFrame(0)
    //move tounge
    fk_player.tounge.startPoint.x = fk_player.position.x-fk_playerValues.width/2+4
    fk_player.tounge.startPoint.y = fk_player.position.y-fk_playerValues.height/2+16
    if(!mouseIsPressed && !fk_hasShotTounge){
      fk_player.tounge.endPoint.position.x = fk_player.tounge.startPoint.x
      fk_player.tounge.endPoint.position.y = fk_player.tounge.startPoint.y
    }
  }else if(mouseX >width/4 && mouseX < 3*width/4){
    fk_player.animation.goToFrame(1)
    //move tounge
    fk_player.tounge.startPoint.x = fk_player.position.x
    fk_player.tounge.startPoint.y = fk_player.position.y-fk_playerValues.height/2
    if(!mouseIsPressed && !fk_hasShotTounge){
      fk_player.tounge.endPoint.position.x = fk_player.tounge.startPoint.x
      fk_player.tounge.endPoint.position.y = fk_player.tounge.startPoint.y
    }
  }else if(mouseX > 3*width/4){
    fk_player.animation.goToFrame(2)
    //move tounge
    fk_player.tounge.startPoint.x = fk_player.position.x+fk_playerValues.width/2-4
    fk_player.tounge.startPoint.y = fk_player.position.y-fk_playerValues.height/2+16
    if(!mouseIsPressed && !fk_hasShotTounge){
      fk_player.tounge.endPoint.position.x = fk_player.tounge.startPoint.x
      fk_player.tounge.endPoint.position.y = fk_player.tounge.startPoint.y
    }
  }
}
function updateToungeMovement(){
  let mPressed = true
  if(mPressed){
    updateMouseVector();
  }
  if(mouseIsPressed && !fk_toungeActive && fk_player.tounge.endPoint.velocity.y == 0){
    fk_toungeActive = true;
    fk_startedToungeRetraction = false;

  }else if(!mouseIsPressed){
    fk_toungeActive = false;
  }

  if(fk_toungeActive && !fk_hasShotTounge ){
    fk_player.tounge.endPoint.moveTowards(mouseVector, fk_playerValues.toungeSpeed)

    fk_hasShotTounge = true;
  }else if(!fk_toungeActive){
    if(!fk_startedToungeRetraction){
      fk_player.tounge.endPoint.moveTowards(fk_player.tounge.startPoint, fk_playerValues.toungeSpeed*1.5)
      fk_startedToungeRetraction = true;
    }
    if(fk_player.tounge.endPoint.position.dist(fk_player.tounge.startPoint) < 60 || fk_player.tounge.endPoint.position.y > height){
      fk_player.tounge.endPoint.position.x = fk_player.tounge.startPoint.x;
      fk_player.tounge.endPoint.position.y = fk_player.tounge.startPoint.y;
      for (var i = 0; i < fk_fliesOnTounge.length; i++) {
        fk_fliesOnTounge[i].sprite.remove()
        fk_score++
      }
      fk_fliesOnTounge = [];
      fk_player.tounge.endPoint.setVelocity(0,0)
      fk_hasShotTounge = false
    }
  }
}

//classes
function fly(x, y, speed){
  this.size = 60;
  this.gitter = true;
  this.onTounge = false;
  this.downSpeed = -speed;
  this.sprite = createSprite(x,y,this.size,this.size);
  this.sprite.setCollider("circle", 0, 0, this.size/2);
  this.sprite.setVelocity(0,-this.downSpeed);
  this.sprite.addAnimation('flying', fly_animation)

  this.draw = function(){
    if(this.gitter){
      this.sprite.position.x = this.sprite.position.x + random(-2,2);
      this.sprite.position.y = this.sprite.position.y + random(-2,2);
    }
    if(this.onTounge){
      this.sprite.position.x = fk_player.tounge.endPoint.position.x;
      this.sprite.position.y = fk_player.tounge.endPoint.position.y;
    }
  }
}
function tounge(startX, startY){
  this.startPoint = createVector(startX, startY)
  this.endPoint = createSprite(startX, startY, this.colliderRadius, this.colliderRadius);
  this.colliderRadius = fk_playerValues.toungeColliderRadius;
  this.endPoint.setCollider('circle', 0, 0, this.colliderRadius);
  this.endPoint.draw = function(){};
  this.endPoint.moveTowards = function(vector2,mag){
    let spriteVector = createVector(this.position.x, this.position.y)
    let newVector = p5.Vector.sub(vector2, spriteVector)
    newVector.setMag(mag)
    this.setVelocity(newVector.x, newVector.y)
  }

  this.draw = function(){
    stroke(255);
    strokeWeight(6);
    strokeCap(SQUARE)
    line(this.startPoint.x, this.startPoint.y, this.endPoint.position.x, this.endPoint.position.y);

  }
}

//other
function spawnFlies(rate){
  if(floor(fk_time) > rate && flySpawnCount < flyAmount){
    let f = new fly(random(60,width-60), -100, random(fk_flySpeed-1, fk_flySpeed+1))
    fk_flies.push(f)
    fk_time = 0
    flySpawnCount++
  }
}
function addFlyToTounge(fly){
  fly.sprite.setVelocity(0,0)
  fly.gitter = false;
  fly.onTounge = true;
  fk_fliesOnTounge.push(fly)
}
function updateMouseVector(){
  mouseVector.x = mouseX
  mouseVector.y = mouseY
}
