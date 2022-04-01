//--Card game--

var cg_player, cg_playersize, cg_playerspeed, cg_playerhp;
var cg_bullets, cg_bulletspeed;
var cards, cardwidth, cardheight, cardspeed, cardrate;
var cg_score = 52;

function cg_preload(){
  //spriteImgSrc sätts i Main
  crossbow_idle = loadImage(spriteImgSrc + 'crossbow-idle.png');
  crossbow_arrow = loadImage(spriteImgSrc + 'crossbow-arrow.png');
  crossbow_shoot = loadImage(spriteImgSrc + 'crossbow-shoot.png');
  crossbow_reload1 = loadImage(spriteImgSrc + 'crossbow-reload1.png');
  crossbow_reload2 = loadImage(spriteImgSrc + 'crossbow-reload2.png');
  card_images = [
    loadImage(spriteImgSrc + 'card1.png'),
    loadImage(spriteImgSrc + 'card2.png'),
    loadImage(spriteImgSrc + 'card3.png'),
    loadImage(spriteImgSrc + 'card4.png'),
    loadImage(spriteImgSrc + 'card5.png'),
    loadImage(spriteImgSrc + 'card6.png'),
    loadImage(spriteImgSrc + 'card7.png')
  ]
}

function cg_draw(){
  if(startSc){startScreen("Card Fight");}
  if(!gameOver){

    drawCardGame();
  }
  if(gameOver && !startSc){
    camera.position.x = width/2;
    camera.position.y = height/2;
    gameOverScreen();
    if(clearVar){cg_deleteVar();}
  }
  if(win){
    camera.position.x = width/2;
    camera.position.y = height/2;
    winScreen();

  }
}

function cg_defineVar(){
    win = false;
    gameOver = true;
    startSc = true;
    spawnrate = 80; //higher -> lower rate
    cardspeed = 2;
    cardwidth = 56;
    cardheight = 80;
    cards = new Group();
    cg_bullets = new Group();
    cg_bulletspeed = 5;

    cg_playerhp = 3;

    cg_playersize = 50;
    cg_playerspeed = 7;
    camera.position.x = width/2;
    camera.position.y = height/2;
    let cg_idle_animation = loadAnimation(crossbow_idle);
    let cg_reload_animation = loadAnimation(crossbow_shoot, crossbow_reload1, crossbow_reload2, crossbow_idle);

    cg_player = createSprite(width/2, height-cg_playersize, cg_playersize, cg_playersize);
    cg_player.addAnimation('reload', cg_reload_animation);
    cg_player.animation.looping = false;
    cg_player.animation.frameDelay = 12;
    cg_player.animation.stop()
    cg_player.setCollider('rectangle', 0, 0, cg_playersize, cg_playersize);
}

function cg_deleteVar(){
  cg_player.remove();
  cards.removeSprites();
  cg_bullets.removeSprites();
}


function cg_playerMove(){

  if(keyIsDown(LEFT_ARROW) && cg_player.position.x > (cg_playersize/2) ){
    cg_player.velocity.x = -cg_playerspeed;

  } else if(keyIsDown(RIGHT_ARROW) && cg_player.position.x < (width-(cg_playersize/2)) ){
    cg_player.velocity.x = cg_playerspeed;
  } else {
    cg_player.velocity.x = 0;

  }

}

function spawnCards(){
  if(frameCount % spawnrate == 0){
    card = createSprite(random(cardwidth,width-cardwidth), -20, cardwidth, cardheight)
    card.addImage(random(card_images))
    card.velocity.y = random(5, cardspeed+cg_score*0.15);
    cards.add(card);
  }

}

function newGame() {
  cards.removeSprites();
  cg_bullets.removeSprites();
  gameOver = false;
  updateSprites(true);
  cg_score = 52;
  cg_playerhp = 3;
}

function cardHit(card, bullet) {
  bullet.remove();
  card.remove();
  cg_score-=1;
}


function drawCardGame(){

    background(51);
    fill(200)
    textFont(pixel_font, 40)
    text("Cards left: " + cg_score, 20, 50);
    text("Lives left: " + cg_playerhp, 20, 100 )
    cardspeed = 2 - 0.03*cg_score;
    if(cg_score <= 0){
      win = true;
    }
    drawSprites();
    cg_playerMove();
    if(cg_player.animation.getFrame()!=cg_player.animation.getLastFrame() && frameCount % 12 == 0)
      cg_player.animation.nextFrame();

    if(keyWentDown(" ") && cg_bullets.length < 4){
      cg_player.animation.changeFrame(0)
      bullet = createSprite(cg_player.position.x, cg_player.position.y, 12, 38)
      bullet.addImage(crossbow_arrow)
      bullet.setCollider('circle',0,0,12)
      //bullet.rotation += 360
      bullet.velocity.y = -cg_bulletspeed
      //bullet.rotateToDirection = true;
      cg_bullets.add(bullet);



    }else{
    }
    spawnCards();

    cg_bullets.overlap(cards, cardHit);
    if(cg_playerhp <= 0){gameOver = true;}


    for (var i = 0; i < cg_bullets.length; i++) {
      if(cg_bullets[i].position.y < 0){
        cg_bullets[i].remove()
      }
    }
    for (var i = 0; i < cards.length; i++) {
            //cards[i].rotation += 4
      if(cards[i].position.y > height){
        cg_playerhp-=1;
        cards[i].remove();
      }else if(cards[i].overlap(cg_player)){
        cards[i].remove();
        cg_playerhp -=1;
      }

    }
  }
