var player, playersize, playerv, jumpv;
var gravity;
var spawnrate;
var score;
var currentheight;
var rockwidth;
var friction;

var rocks;
var pens, penv;
var positive;
var gameOver = false;
var difficulty;


function mountain_jump() {
  this.draw = function() {
    if(!gameOver){

      background(51);
      drawSprites();
      textSize(40);
      fill(255);


      camera.position.y = player.position.y
      text(score,  width -100, player.position.y-height/2+100)
      if(player.velocity.y < 0){
      }
      player.debug = true;

      player.velocity.y += gravity
      if(score > 2500){
        difficulty = 2;
      }
      if(score > 5000){
        difficulty = 3;
      }
      if(score > 7500){
        difficulty = 4;
      }

      if(player.overlap(ground)){
        player.velocity.y = -2*jumpv;
      }


      if(keyDown(LEFT_ARROW) && player.velocity.x >-2*playerv){
        player.velocity.x -= 0.3*playerv;
      }else if(!keyDown(LEFT_ARROW) &&player.velocity.x < 0){
        player.velocity.x +=-1*friction*player.velocity.x
      }
      if(keyDown(RIGHT_ARROW)&& player.velocity.x <2*playerv){
        player.velocity.x += 0.3*playerv;
      }else if(!keyDown(RIGHT_ARROW) && player.velocity.x > 0){
          player.velocity.x -=friction*player.velocity.x
      }
      if(player.position.x < 0){
        player.position.x = width;
      }
      if(player.position.x > width){
        player.position.x = 0;
      }
      if (frameCount % floor(400/difficulty) == 0) {
        pen = createSprite(random(0,width),player.position.y-height,10,60);
      //  pen.setCollider('c',0,0,10,60);
        pen.rotationSpeed = random(-1,2)*10;
        //pen.velocity.y = penv*difficulty;

        pens.add(pen);


      }

      for (var i = 0; i < pens.length; i++) {
        if(player.overlap(pens[i])&& player.velocity.y< 0){
          gameOver = true;
          //pens[i].remove();
          //i-=1
        }
        // } else if(player.overlap(pens[i])&& player.velocity.y> 0){
        //   player.velocity.y = -0.5*jumpv;
        //   //pens[i].remove();
        //   //i-=1
        // }
        if(pens[i].position.y > player.position.y+height){
          pens[i].remove();
        }


      }

      if(player.position.y < currentheight){
        currentheight = player.position.y - 400;
        score += 100;
        for (var i = 0; i < 5-difficulty; i++) {
          randint = floor(random(0, 20));
          if(difficulty<3){
            rock = createSprite(random(rockwidth/2+width*i/3, width*(i+1)/3), random(currentheight-width/2-50, currentheight-width/2-200), rockwidth, 40);
          }else{
            rock = createSprite(random(rockwidth/2, width-rockwidth/2), random(currentheight-width/2-50, currentheight-width/2-200), rockwidth, 40);

          }
          rock.setCollider('rectangle',0,0,rockwidth,50);

          if(randint >=0 && randint <17-difficulty){rock.type = 0;rock.shapeColor= color(150);}
          if(randint ==17-difficulty ){rock.type = 1; rock.shapeColor = color(100,100,200);}//jump
          if(randint > 17-difficulty && randint< 20-difficulty){rock.type = 2; rock.shapeColor = color(200,100,100);}//break
          if(randint >= 20-difficulty){rock.type = 3; rock.shapeColor = color(200,100,200); rock.dir = random(-difficulty,difficulty)}//move

          rocks.add(rock);


        }
      }

      for (var i = 0; i < rocks.length; i++) {
        if(rocks[i].position.y > player.position.y+height){
          rocks[i].remove();
        }
        if(rocks[i].type == 3){
          if(rocks[i].position.x > width -rockwidth/2){rocks[i].dir = -1}
          if(rocks[i].position.x < rockwidth/2){rocks[i].dir = 1}
          rocks[i].velocity.x = rocks[i].dir*0.7*difficulty;

        }
        if(player.overlap(rocks[i])&& player.velocity.y >0){
          switch (rocks[i].type) {
            case 0:
              player.velocity.y = -jumpv;
              break;
            case 1:
              player.velocity.y = -2*jumpv
              break;
            case 2:
              player.velocity.y = -jumpv;
              rocks[i].remove();
              break;
            case 3:
              player.velocity.y = -jumpv;
              break;

          }
        }
      }

   }
 }//end this.draw
 this.setup = function(){
   defineCanvas();
   defineVar();

 }
}

function newGame() {
  rocks.removeSprites();
  gameOver = false;
  updateSprites(true);
  player.position.x = width/2;
  player.position.y = height/2;
  player.velocity.y = 0;
  score = 0;
}

function defineVar(){
  friction = 0.08;
  score = 0;
  difficulty = 1; //3 = max
  oldscore = 0;
  currentheight = 0;
  penv = 2;

  rockwidth = 100;
  spawnrate = floor(random(20,40));
  playersize = 50;
  playerv = 4;
  jumpv = 20;
  gravity = 0.4;
  player = createSprite(width/2,height/2, playersize, playersize);
  player.shapeColor = color(255);
  player.velocity.x = playerv + 0.01*score;
  player.setCollider('circle', 0, 0, playersize/2)
  ground = createSprite(0,height, width*5, 1000);
  rocks = new Group();
  pens = new Group();
  positive = 1;


  camera.position.y = height/2;
  camera.position.x = width/2
}

function defineCanvas(){
  canvas = createCanvas(600, 600);
  canvas.position(windowWidth/2-300, windowHeight/2-300)
  canvas.style('outline', '1px');
  canvas.style('outline-color', '#fff');
  canvas.style('outline-style', 'solid');
}
