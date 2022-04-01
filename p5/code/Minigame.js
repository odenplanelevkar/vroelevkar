
var player;
var enemies = [];
var bullets = [];

var enemysize;
var playerdmg;
var espacing;


function setup(){
  enemysize = 50;
  playerdmg = 1;
  espacing = 10;

  canvas = createCanvas(600, 600);
  canvas.position(windowWidth/2-300, windowHeight/2-300)
  canvas.style('outline', '1px');
  canvas.style('outline-color', '#fff');
  canvas.style('outline-style', 'solid');

  player = new player(15);
  for (var i = 0; i < 4; i++) {
    for (var j = 0; j < 9; j++) {
      enemies.push(new enemy( (j*(enemysize+espacing)+enemysize/2 +espacing) , i*(enemysize+espacing)+(enemysize/2 +espacing) , 1));

    }
  }



}

function draw(){


  if(player.alive){
    background(51);
  }

  if(player.alive){
    player.show();
    player.move();
  }

  if(enemies.length == 0){
    for (var i = 0; i < 4; i++) {
      for (var j = 0; j < 9; j++) {
        enemies.push(new enemy( (j*(enemysize+espacing)+enemysize/2 +espacing) , i*(enemysize+espacing)+(enemysize/2 +espacing) , 1));

      }
    }
    for (var i = 0; i < enemies.length; i++) {
      enemies[i].dir += 0.3;
    }
  }

  //cleanup
  for (var i = 0; i < enemies.length; i++) {
    if(!enemies[i].alive){
      enemies.splice(i,1);
    }
  }
  for (var i = 0; i < bullets.length; i++) {
    if(bullets[i].used || bullets[i].y < 0){
      bullets.splice(i,1);
    }
  }


  for (var i = 0; i < enemies.length; i++) {
    if(enemies[i].alive && player.alive){
      enemies[i].show();
      enemies[i].move();
      if(enemies[i].y>height-player.size && (enemies[i].x + enemysize/2)>(player.x-player.size/2) && (enemies[i].x-enemysize/2)<(player.x+player.size/2)){
        player.alive = false;
        background(51);
        textSize(100);
        textAlign(CENTER, CENTER);
        fill(255);
        text('GAME OVER', width/2, height/2);

      }
      if(enemies[i].x > width-enemysize/2 || enemies[i].x < enemysize/2){
        for (var i = 0; i < enemies.length; i++) {
          enemies[i].shift();
        }

      }
    }
  }
  for (var i = 0; i < bullets.length; i++) {
    if(!bullets[i].used){
      bullets[i].show();
    }

    for (var j = 0; j < enemies.length; j++) {

      if(!bullets[i].used && enemies[j].alive && bullets[i].y < enemies[j].y + enemysize/2 && bullets[i].y > enemies[j].y -enemysize/2 && bullets[i].x < enemies[j].x +enemysize/2 && bullets[i].x > enemies[j].x - enemysize/2 ){
        enemies[j].hit();
        bullets[i].used = true;
      }
    }
  }

}//end function draw()

function keyPressed(){
  if(keyCode=== RIGHT_ARROW){
    player.setDir(1);
  }
  if(keyCode=== LEFT_ARROW){
    player.setDir(-1);
  }
  if(key == ' ' && bullets.length< 2 && player.alive){
    bullets.push(new bullet(player.x, player.y));
  }

}//end keypressed

function keyReleased(){

  if(key != ' '){
    if(keyCode == RIGHT_ARROW && player.dir != -1){
      player.setDir(0);
    }
    if(keyCode == LEFT_ARROW && player.dir != 1){
      player.setDir(0);

    }
  }
}//end key released,jhgljhgkhjnöyrg.,vjhtfsdj,.kfdk.jgv nkhgj






//saker som borde vara i separata dokument
function player(pspeed){
  this.pspeed = pspeed;
  this.size = 30;
  this.x = width/2
  this.y = height-this.size/2
  this.dir = 0;
  this.alive = true;

  this.show = function(){
    noStroke();
    rectMode(CENTER);
    fill(255);
    rect(this.x, this.y, this.size, this.size);
  }
  this.setDir = function(dir){
    this.dir = dir;
  }
  this.move = function(){
    newx = this.x+this.dir*this.pspeed;
    if(newx > 0 && newx < width){
    this.x = newx
    }
  }
}

function enemy(x, y, hp){
  this.x = x;
  this.y = y;
  this.hp = hp;
  this.size = enemysize;
  this.alive = true
  this.dir = 1


  this.show = function(){
    rectMode(CENTER);
    fill(255);
    noStroke();
    rect(this.x, this.y, this.size, this.size);

  }

  this.hit = function(){
    this.hp = this.hp-playerdmg
    if(this.hp <= 0){
      this.alive = false;
    }
  }

  this.move = function(){
    this.x = this.x+this.dir;
  }
  this.shift = function(){
    this.dir *= -1;
    this.y += enemysize;
  }

}

function bullet(x, y){
  this.x = x;
  this.y = y;
  this.size = 5;
  this.speed = 10;
  this.used = false;

  this.show = function() {
    rectMode(CENTER);
    fill(255);
    noStroke();
    rect(this.x, this.y,this.size,this.size)
    this.y = this.y-this.speed
  }





}
