let usingRoomDraw = false;

var enterTextShow = true;

function startScreen(title, description){
  background(51);
  fill(255);

  textFont(pixel_font, 50)
  textAlign(CENTER, BOTTOM)
  text(title, width/2,height/2);

  blinkingText();
  if(enterTextShow){
    textFont(pixel_font, 30)
    textAlign(CENTER, TOP);
    text("press 'r' to start",width/2,height/2)
  }

  if(keyWentDown('r')){
    gameOver = false;
    startSc = false;
  }
}

function winScreen(){
  background(51);
  fill(255);
  textFont(pixel_font, 50)
  textAlign(CENTER, BOTTOM);
  text("Congratulations!", width/2,height/2);
  blinkingText();

  if(enterTextShow){
    textFont(pixel_font, 30)
    textAlign(CENTER,TOP);
    text("press 'q' to continue",width/2,height/2);
  }
  if(keyWentDown('q')){
    changeRoom(currentArea, Number(minigameWin[0]), Number(minigameWin[1]));
    switchToText();
    clearVar = true;
  }
}

function gameOverScreen(){
  background(51);
  fill(255);
  textFont(pixel_font, 50)
  textAlign(CENTER, BOTTOM);
  text("You failed!", width/2,height/2);
  blinkingText();

  if(enterTextShow){
    textFont(pixel_font, 30)
    textAlign(CENTER,TOP);
    text("press 'q' to go back",width/2,height/2)

  }
  if(keyWentDown('q')){
    changeRoom(currentArea, Number(minigameGameOver[0]), Number(minigameGameOver[1]));
    switchToText();
    clearVar= true
  }
}

function blinkingText(){
  if(frameCount % 60 == 0){
    if(enterTextShow){
      enterTextShow = false;
    }else if(!enterTextShow){
      enterTextShow = true;
    }
  }
}
