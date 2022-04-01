var pb_player, pb_playersize, pb_playerspeed;
var pb_bread = 0;
var handresistance;
var pb_breadbowl;
var pepe, looktimer, pepecolors, currentcolor;
var gameOver;
var startSc;


/*
function setup(){

  defineCanvas();




} // setup()

function draw(){
  if(startSc){
    startScreen("Grab the bread");
  }
  if(!gameOver){
    drawPepe();
  }
  if(gameOver && !startSc){
    gameOverScreen();
  }
// function draw()
}
*/

function pb_preload(){
  
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
}



function pb_defineVar(){
    camera.position.x = width/2;
    camera.position.y = height/2;
    gameOver = true;
    startSc = true;
    pb_playersize = 50;
    pb_playerwidth = width/2
    pb_playerspeed = 10;
    handresistance = 1.2;
    pb_breadbowl = createSprite(width/2, height/2, 50,50)
    looktimer = floor(random(120, 60*5));
    pepe = createSprite(25,25,50,50);
    pepecolors = [color(0, 255, 0), color(255, 255, 0), color(255, 0, 0)];
    currentcolor = 0;
    pb_player = createSprite(-pb_playerwidth/3, height/2, pb_playerwidth, pb_playersize);
    pb_player.shapeColor = color(255);
    pb_player.setCollider('rectangle', 0, 0, pb_playerwidth, pb_playersize)
}


function pb_deleteVar(){

    pb_breadbowl.remove();
    pepe.remove();
    pb_player.remove();
}
/*
function defineCanvas(){

    div = createDiv();
    canvas = createCanvas(600, 600);
    canvas.position(windowWidth/2-300, windowHeight/2-300)
    canvas.style('outline', '1px');
    canvas.style('outline-color', '#fff');
    canvas.style('outline-style', 'solid');


}
*/
function drawPepe(){
  background(51);
  fill(255);
  drawSprites();

  text(pb_bread, width-100, 50);

  if(pb_player.position.x > -pb_playerwidth/3){
    pb_player.velocity.x += -handresistance;
  } else { pb_player.velocity.x = 0; pb_player.position.x = -pb_playerwidth/3}
  if(keyWentDown(RIGHT_ARROW)){
    pb_player.velocity.x = 10;
  }
  if(pb_player.overlap(pb_breadbowl)){
    pb_bread++
    pb_player.position.x = -pb_playerwidth/3;
    handresistance += 0.1
  }
  pepe.shapeColor = pepecolors[currentcolor]

  if(currentcolor == 2 && pb_player.velocity.x > 5){
    gameOver = true;
  }

  if(frameCount%looktimer == 0){

    add = 0;
    if(currentcolor == 1){

      if(floor(random(0,2)) == 0){
        add++
        if(pb_player.velocity .x > pb_playerspeed-1){
          //gameOver
        }
      }else {
        add += -1
        looktimer += floor(random(60,4*30)) //måste göra så att den inte kan bli större än innan och gå igenom igen
      }


    }else if(currentcolor == 0){
      add++
      looktimer += floor(random(30, 6*30))
    }else if(currentcolor == 2){
      add = -2
      looktimer += floor(random(30, 3*30))


    }
    currentcolor += add;


  }
}

function newGame(){
  pb_player.position.x = -pb_playerwidth/3
  pb_player.position.y = height/2;
  gameOver = false;
  handresistance = 1.5
  currentcolor = 0;
  looktimer = floor(random(120, 60*5));
  pb_bread = 0;
}
