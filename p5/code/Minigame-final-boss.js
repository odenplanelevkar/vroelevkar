let site_background_img, red_circle_loading_animation, red_circle_rotation_animation;
let url_box, close_button, other_button1, other_button2;
let fb_circleLoading;
let fb_mouseOnUrlBox;

let fb_activeCircleAnimation;
let fb_urlText;

let fb_wordTimer, fb_circleLoadingTime, fb_timeUntilFail;
let fb_standardWordArray,fb_wordToDisplay,fb_activeWordFromWordArray, fb_wordAppearFrequency, fb_typedCounter;

let fb_changeToWordArrayAfterFinished;

let fb_hasSwitched;

function fb_preload(){
  site_background_img = loadImage(spriteImgSrc+'website_background.png')
  red_circle_loading_animation = loadAnimation(spriteImgSrc+'RED_CIRCLE/loading_0001.png', spriteImgSrc+'RED_CIRCLE/loading_0012.png')
  red_circle_loading_animation.frameDelay = 20;
  red_circle_rotation_animation = loadAnimation(spriteImgSrc+'RED_CIRCLE/rotation_0001.png', spriteImgSrc+'RED_CIRCLE/rotation_0004.png')
}

function fb_defineVar(){

  //standard variables
  win = false;
  gameOver = false;

  //canvas
  resizeCanvas(800,575)
  canvas.style('border', 'none')
  canvas.style('outline', 'none')
  canvas.style('padding', '0px')
  camera.position.x = width/2;
  camera.position.y = height/2;

  //circle words
  fb_standardWordArray = ["HAH you think you can escape me?", "I am everywhere", "There is no escaping", "Soon you will be mine!"]
  fb_activeWordFromWordArray = " "
  fb_wordToDisplay = ""
  fb_wordAppearFrequency = 2000
  fb_wordArrayIndex = 0
  fb_typedCounter = 0;

  //general
  fb_wordTimer = 0;
  fb_activeWordArray = fb_standardWordArray;
  fb_timeUntilFail = 60000

  //animation variables
  fb_circleLoading = false;
  fb_activeCircleAnimation = red_circle_rotation_animation;
  fb_circleLoadingTime = 10000
  fb_hasSwitched = true;

  //url variables
  fb_urlText = ""
  fb_mouseOnUrlBox = false;
  fb_defineUrlBox()


  //top buttons
  fb_defineTopButtons()
}

function fb_deleteVar(){

}

function fb_draw(){
  if(startSc){
    startScreen("The Red Circle")

  }
  if(!gameOver && !win && !startSc){
    fb_game();
 }
 if(!startSc && gameOver && !win){
   camera.position.x = width/2;
   camera.position.y = height/2;
   gameOverScreen();
   if(clearVar){fb_deleteVar();}
 }
 if(win && !gameOver){
   camera.position.x = width/2;
   camera.position.y = height/2;
   winScreen();
   if(clearVar){fb_deleteVar();}
 }
}

function fb_game(){
  //background
  background(51)
  imageMode(CENTER)
  image(site_background_img, width/2,height/2)
  fb_activeCircleAnimation.draw(width/2-100, height/2+50)

  //time updates
  fb_wordTimer+=deltaTime
  fb_circleLoadingTime+=deltaTime
  if(!fb_circleLoading){fb_timeUntilFail -= deltaTime}
  fb_countdownText()

  //fail check
  if(fb_timeUntilFail < 0){
    gameOver = true;
  }

  //other updates
  drawSprites()
  fb_updateWordArray()

  //keyboard / inputs
  if(mouseWentDown() && !url_box.mouseIsOver){
    fb_mouseOnUrlBox = false;
  }
  if(keyWentDown(BACKSPACE)){
    fb_urlText = fb_urlText.substring(0, fb_urlText.length -1)
  }
  if(keyWentDown(ENTER)){
    if(fb_urlText == "www.canvas.com"){
      fb_urlText = ""
      let response = ["Wait hold on a sec", "Someone is trying to do something", "Hold on"]
      fb_switchWordArray(response)

      fb_circleLoading = true
      fb_activeCircleAnimation = red_circle_loading_animation;
      fb_circleLoadingTime = 0;
      fb_hasSwitched = false;
    }
  }

  if(fb_circleLoadingTime > 10000 && !fb_hasSwitched){

    fb_hasSwitched = true
    fb_switchWordArray(fb_standardWordArray)
    fb_activeCircleAnimation = red_circle_rotation_animation //borde optimeras
    fb_circleLoading = false;
  }
}

function fb_keyTyped(){
  if(fb_mouseOnUrlBox && keyCode != ENTER){
    fb_urlText += key
  }
}

function fb_updateWordArray(){
  if(fb_typedCounter <= fb_activeWordFromWordArray.length) {fb_wordToDisplay += fb_activeWordFromWordArray.charAt(fb_typedCounter-1)}

  if(fb_wordTimer > fb_wordAppearFrequency){
    if(fb_wordArrayIndex < fb_activeWordArray.length){
      fb_activeWordFromWordArray = fb_activeWordArray[fb_wordArrayIndex]
    }else{
      fb_activeWordFromWordArray = ""
      if(fb_changeToWordArrayAfterFinished){
        fb_switchWordArray(fb_standardWordArray)
        fb_changeToWrodArrayAfterFinished = false
      }
    }
    fb_wordArrayIndex++
    fb_typedCounter = 0;
    fb_wordTimer = 0;
    fb_wordToDisplay = ""
    fb_interruptWord = false;
  }

  fb_typedCounter++
  textFont(pixel_font, 40)
  textAlign(LEFT)
  fill(51)
  text(fb_wordToDisplay, 578, 120, 212, 468)
}

function fb_triggerWord(){
  fb_wordArrayIndex = 0
  fb_wordTimer = fb_wordAppearFrequency;
}

function fb_switchWordArray(array){
  fb_activeWordArray = array;
  fb_triggerWord()
}

function fb_defineUrlBox(){
  url_box = createSprite(400,79,768,52)
  url_box.mouseActive = true;
  url_box.onMousePressed = function(){
    fb_mouseOnUrlBox = true;
  }
  url_box.draw = function(){
    textFont(pixel_font, 40)
    fill(255)
    text(fb_urlText, -350,16)
  }
}

function fb_defineTopButtons(){
  close_button = createSprite(778,18,12,12)
  close_button.setCollider('circle',0,0,8)
  close_button.mouseActive = true;
  close_button.onMousePressed= function(){
    let response = ["HAH! Like I would let you do that!", "Do you think escaping will be that easy?", "I control EVERYTHING"]
    fb_changeToWordArrayAfterFinished = true;
    fb_switchWordArray(response)
    if(fb_circleLoadingTime <10000){
      win = true;
    }
  }
  close_button.visible = false;
  other_button1 = createSprite(754,18,12,12)
  other_button1.setCollider('circle',0,0,8)
  other_button1.mouseActive = true;
  other_button1.onMousePressed= function(){
    let response = ["HAH! Like I would let you do that!", "Do you think escaping will be that easy?", "I control EVERYTHING"]
    fb_changeToWordArrayAfterFinished = true;
    fb_switchWordArray(response)
  }
  other_button1.visible = false;
  other_button2 = createSprite(730,18,12,12)
  other_button2.setCollider('circle',0,0,8)
  other_button2.mouseActive = true;
  other_button2.onMousePressed= function(){
    let response = ["HAH! Like I would let you do that!", "Do you think escaping will be that easy?", "I control EVERYTHING"]
    fb_changeToWordArrayAfterFinished = true;
    fb_switchWordArray(response)
  }
  other_button2.visible = false
}

function fb_countdownText(){
  textFont(pixel_font, 40)
  fill(51)
  text(floor(fb_timeUntilFail/1000) + "s left", 578, 540 )
}
