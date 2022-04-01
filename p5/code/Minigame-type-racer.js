let tr_typedWord;
let tr_enemies;
let tr_enemySpeed;
let tr_wordArray;
let tr_currentWordIndex;

let tr_timeBetweenEnemySpawn
let tr_time

let showUnderline;
let wasp_anim;

function tr_preload(){
  wasp_anim = loadAnimation(spriteImgSrc + 'wasp_anim/wasp0000.png',spriteImgSrc + 'wasp_anim/wasp0010.png' )
}

function tr_defineVar(){
  camera.position.x = width/2;
  camera.position.y = height/2;
  tr_currentWordIndex = 0;
  tr_time = 0;
  tr_enemySpeed = 100;
  tr_timeBetweenEnemySpawn = 1500;
  tr_wordArray = getWaspDramaWordArray();
  tr_enemies = []
  tr_typedWord = ""
  showUnderline = false
  tr_spawnWord()
  if(redCircle){
    tr_spawnWord()
  }
  // tr_spawnWord()
  // tr_enemies[1].y = -300

  startSc = true;
  gameOver = false;
  win = false;

}

function tr_resetVar(){
  camera.position.x = width/2;
  camera.position.y = height/2;
  tr_currentWordIndex = 0;
  tr_time = 0;
  tr_enemySpeed = 100;
  tr_enemies = []
  tr_typedWord = ""
  showUnderline = false

}

function tr_draw(){
  if(startSc){
    startScreen("The Wasp King");
    tr_typedWord = ""
  }
  if(!gameOver && !win && !startSc){
    tr_game();
  }
  if(!startSc && gameOver && !win){
    gameOverScreen();
    if(clearVar){
      tr_resetVar();
    }
  }
  if(win && !gameOver){
    camera.position.x = width/2;
    camera.position.y = height/2;
    winScreen();
  }
  if(clearVar){
    tr_resetVar();
  }
}

function tr_game(){
  background(51)
  tr_time += deltaTime;

  //tr_spawnEnemies(tr_timeBetweenEnemySpawn)
  for (var i = 0; i < tr_enemies.length; i++) {
    tr_enemies[i].update()
    if(tr_enemies[i].y > height+100){
      gameOver=true;
    }
  }
  if(keyWentDown(BACKSPACE)){
    tr_typedWord = tr_typedWord.substring(0, tr_typedWord.length -1)
  }
  tr_checkWordCollision()
  fill(255)
  textSize(40)
  textAlign(LEFT)
  if(frameCount % 30 == 0){
    if(showUnderline){
      showUnderline= false;
    }else{
      showUnderline = true;
    }
  }
  if(showUnderline){
    text(tr_typedWord+"_",width/2-100, 550)
  }else{
    text(tr_typedWord, width/2-100, 550)
  }
}

function tr_keyTyped(){
  if(key != ' '){
    tr_typedWord += key
  }
}

function tr_checkWordCollision(){
  for (var i = 0; i < tr_enemies.length; i++) {
    if(tr_typedWord == tr_enemies[i].text){
      tr_enemies.splice(i,1)
      tr_typedWord = ""
      tr_spawnWord()
    }
  }
}

function wordWasp(x, y, speed, displayText){
  this.text = displayText;
  this.downSpeed = speed;
  this.x = x;
  this.y = y;
  this.animation = wasp_anim
  this.display = function(){
    fill(255)
    textSize(40)
    textAlign(CENTER)
    textFont(pixel_font)
    text(this.text, this.x, this.y)
    this.animation.draw(this.x, this.y-70);
  }
  this.move = function(){
    this.x = this.x + random(-2,2);
    this.y = this.y + random(-2,2);
    this.y += Number(this.downSpeed*floor(deltaTime)/1000)
  }
  this.update = function(){
    this.display();
    this.move();
  }
}

function tr_spawnWord(){
  if(tr_currentWordIndex < tr_wordArray.length){
    let f = new wordWasp(random(100,width-100), 0, tr_enemySpeed, tr_wordArray[tr_currentWordIndex])
    tr_enemies.push(f)
    tr_time = 0
    tr_currentWordIndex++
  }else{
    win = true;
  }
}

function tr_spawnEnemies(rate){
  if(tr_time > rate){
    tr_spawnWord()
  }
}
