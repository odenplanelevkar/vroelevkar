
let displayedOptions = [];
let currentoption = 0;
let optionlength;
let rooms = [];
let maxOptionLength = 5;

let win = false;
// All rooms
let allAreaRooms;
let pixel_font;
let load;
let grandparent;
let canvas;
let write = true; //kontrollerar om det som står i rummet ska skrivas eller inte
let leftWriteOn = false;
var gameOver;
var startSc;
var drawText;
let drawCanvas;
let hasSound = true;
let enterPause = false;

let redCircle = false;

let spriteImgSrc;

var player;
let currentRoom;
let currentArea = 'intro';

let define = true;
let clearVar = false;

let texttest;
let counter;

let textbox;
let paused = true; // DEBUG

// Minigameendings
let minigameGameOver;
let minigameWin;

//minigame-switch variables;
let current_encounter;

//ljud
let blip;
let soundEnabled;

let keypressed = false;
let timer;

// Chapter release
let completedChapters = 3;

let backgrounds = {
  beach: 'beach.gif',
  castle: 'castle.png',
  creepyHouse: 'creepyhouse.gif',
  highlands: 'scottish.gif',
  tavern: 'tavern.gif',
  bogGeneral: 'bogGeneral.gif',
  sheep: 'sheep.gif',
  swampCrossroads: 'crossroads.gif',
  mudriver: 'muddyriver2.gif',
  witchHouse: 'witchAbode.gif',
  flies: 'roomOfFlies.gif',
  throneRoom: 'frogKing.gif',
  waspKing: 'waspKing.gif',
  capitolStreets: 'streetsofoldcapitol.gif',
  generalSunset: 'generalSunset.gif',
  capLibrary: 'library.png',
  cityGeneral: 'capitol.gif',
  beegRavine: 'ravine.gif',
  survivorCamp: 'survivorCamp.gif'
}

let endscreens = [
  'chapter1finished.png',
  'chapter2finished.png',
  'chapter3finished.png'
]

let colors = {
  intro: '#416fb7',
  castle: '#A25210',
  highlands: '#3a8530',
  tavern: '#89260c',
  creepyHouse: '#4f141c',
  bog: '#26290A',
  sheep: '#38200B',
  muddyRiver: '#3C3F4A',
  crossroads: '#4E4614',
  frogKing: '#2D2608',
  roomOfFlies: '#275A52',
  witchAbode: '#5C2F62',
  capitolStreets: '#725629',
  cityGeneral: '#817663',
  library: '#5B2407',
  survivorCamp: '#626262',
  ravine: '#2A4528',
  waspKing: '#734209',
  generalSunset: '#462A32',
  default: '#000'
}

// PRODUCTION SITE MUSIC
let music = {
  highlands: 'http://vroelevkar.se/wp-content/uploads/2020/04/highlandsAmbient.mp3',
  creepyHouse: 'http://vroelevkar.se/wp-content/uploads/2020/04/creepyHouse.mp3',
  boss: 'http://vroelevkar.se/wp-content/uploads/2020/04/highlandsBoss.mp3',
  tavern: 'http://vroelevkar.se/wp-content/uploads/2020/04/tavern.mp3',
  mainThemeIntro: 'http://vroelevkar.se/wp-content/uploads/2020/04/harKommerJag.mp3',
  introBeach: 'http://vroelevkar.se/wp-content/uploads/2020/04/wakup.wav',
  gameOver: 'http://vroelevkar.se/wp-content/uploads/2020/05/sheep-calm.mp3',
  swamp: 'http://vroelevkar.se/wp-content/uploads/2020/05/swamp.wav',
  roomOfFlies: 'http://vroelevkar.se/wp-content/uploads/2020/05/roomOfFlies.wav',
  confusedSheep: 'http://vroelevkar.se/wp-content/uploads/2020/05/confusedSheep.mp3',
  witchAbode: 'http://vroelevkar.se/wp-content/uploads/2020/05/witchAbode2.mp3',
  stream: 'http://vroelevkar.se/wp-content/uploads/2020/05/streamBit.wav',
  frogeCalm: 'http://vroelevkar.se/wp-content/uploads/2020/05/froge.wav',
  frogeFight: 'http://vroelevkar.se/wp-content/uploads/2020/05/frogefight.wav',
  library: 'http://vroelevkar.se/wp-content/uploads/2020/05/library.wav',
  ravine: 'http://vroelevkar.se/wp-content/uploads/2020/05/ravine.wav',
  dance: 'http://vroelevkar.se/wp-content/uploads/2020/06/Dance2.wav',
  camp: 'http://vroelevkar.se/wp-content/uploads/2020/06/campHigher.mp3',
  streetsCapitol: 'http://vroelevkar.se/wp-content/uploads/2020/05/streetsGeneral.wav',
  throneMusic: 'http://vroelevkar.se/wp-content/uploads/2020/06/waspdrama.wav'
}

let chapterCoordinates = [
  {
    start: [0, 0],
    end: [0, 0]
  },
  {
    start: [-1, 23],
    end: [0, -17]
  },
  {
    start: [0, 22],
    end: [0, -17]
  },
  {
    start: [0, 22],
    end: []
  }
]

///////////////////////////////////////////
// ASSET FUNCTIONS
///////////////////////////////////////////

function getColorFromBackground() {
  if (player.background == backgrounds['highlands']) {
    return colors.highlands;
  }
  if (player.background == backgrounds['beach']) {
    return colors.intro
  }
  if (player.background == backgrounds['castle']) {
    return colors.castle
  }
  if (player.background == backgrounds['creepyHouse']) {
    return colors.creepyHouse
  }
  if (player.background == backgrounds['tavern']) {
    return colors.tavern
  }
  if (player.background == backgrounds['bogGeneral']) {
    return colors.bog
  }
  if (player.background == backgrounds['sheep']) {
    return colors.sheep
  }
  if (player.background == backgrounds['swampCrossroads']) {
    return colors.crossroads
  }
  if (player.background == backgrounds['mudriver']) {
    return colors.muddyRiver
  }
  if (player.background == backgrounds['witchHouse']) {
    return colors.witchAbode
  }
  if (player.background == backgrounds['flies']) {
    return colors.roomOfFlies
  }
  if (player.background == backgrounds['throneRoom']) {
    return colors.frogKing
  }
  if (player.background == backgrounds['capitolStreets']) {
    return colors.capitolStreets
  }
  if (player.background == backgrounds['cityGeneral']) {
    return colors.cityGeneral
  }
  if (player.background == backgrounds['capLibrary']) {
    return colors.library
  }
  if (player.background == backgrounds['beegRavine']) {
    return colors.ravine
  }
  if (player.background == backgrounds['survivorCamp']) {
    return colors.survivorCamp
  }
  if (player.background == backgrounds['waspKing']) {
    return colors.waspKing
  }
  if (player.background == backgrounds['generalSunset']) {
    return colors.generalSunset
  }
  else {
    return colors.default
  }
}

function changeBoxColor() {
  let classes = 'box';

  if (player.background == backgrounds['highlands']) {
    classes += ' highlands';
  }
  if (player.background == backgrounds['beach']) {
    classes += ' intro';
  }
  if (player.background == backgrounds['castle']) {
    classes += ' castle';
  }
  if (player.background == backgrounds['creepyHouse']) {
    classes += ' creepyhouse';
  }
  if (player.background == backgrounds['tavern']) {
    classes += ' tavern';
  }
  if (player.background == backgrounds['bogGeneral']) {
    classes += ' bog';
  }
  if (player.background == backgrounds['sheep']) {
    classes += ' sheep';
  }
  if (player.background == backgrounds['swampCrossroads']) {
    classes += ' crossroads';
  }
  if (player.background == backgrounds['mudriver']) {
    classes += ' river';
  }
  if (player.background == backgrounds['witchHouse']) {
    classes += ' witchAbode';
  }
  if (player.background == backgrounds['flies']) {
    classes += ' flies';
  }
  if (player.background == backgrounds['throneRoom']) {
    classes += ' throneRoom';
  }
  if (player.background == backgrounds['capitolStreets']) {
    classes += ' capitolStreets';
  }
  if (player.background == backgrounds['cityGeneral']) {
    classes += ' cityGeneral';
  }
  if (player.background == backgrounds['capLibrary']) {
    classes += ' library';
  }
  if (player.background == backgrounds['beegRavine']) {
    classes += ' ravine';
  }
  if (player.background == backgrounds['survivorCamp']) {
    classes += ' survivorCamp';
  }
  if (player.background == backgrounds['waspKing']) {
    classes += ' waspKing';
  }
  if (player.background == backgrounds['generalSunset']) {
    classes += ' generalSunset';
  }
  else {
    classes += ' default'
  }

  $('.box').attr('class', classes);
}

function getBackgroundImageFromArea( area ) {

  let imageName = backgrounds.highlands;

  switch (area) {
    case 'highlands':
      imageName = backgrounds.highlands;
      break;

    case 'intro':
      imageName = backgrounds.beach;
      break;

    case 'bog':
      imageName = backgrounds.bogGeneral;
      break;

    case 'city':
      imageName = backgrounds.cityGeneral;
      break;

    default:
      imageName = backgrounds.highlands;
  }

  return imageName;

}


function getSongFromArea( area ) {

  let song = music.highlandsMain;

  switch (area) {
    case 'intro':
      song = music.introBeach;
      break;

    case 'highlands':
      song = music.highlands;
      break;

    case 'bog':
      song = music.swamp;
      break;

    case 'city':
      song = music.streetsCapitol;
      break;

    default:
      song = music.highlands;
  }

  return song;

}


//loads sprites for games, called before setup p5 shenanigans
function preload(){

  if(!usingRoomDraw){
    assetFolderSrc = document.getElementById('game-asset-folder').innerText
    pixel_font = loadFont(assetFolderSrc+"fonts/VPPixel-Simplified.otf")
    spriteImgSrc = assetFolderSrc + 'Sprites/Png/';

    fr_preload();
    cg_preload();
    er_preload();
    pb_preload();
    i_preload();
    fk_preload();
    cyp_preload();
    tr_preload();
    wr_preload();
    wc_preload();
    ddr_preload();
    mj_preload();
    fb_preload();
  }
}

////////////////////////////////////////////
// SETUP
////////////////////////////////////////////

function setup(){
  // Get all saved rooms from the database
  loadRoomsFromDatabase(currentArea, function(returnedRooms) {

    // Set all rooms
    allAreaRooms = returnedRooms;

    // Set the current room array
    rooms = getRoomsFromArea( returnedRooms, currentArea );

    defineCanvas();
    grandparent = select('#grandparent');

    drawText = true;
    soundEnabled = false;
    optionlength = 1;
    load = false;
    counter = 0;

    timer = 0;

    // Create the player
    player = new player();

    // Update player stats from the database
    getPlayer();
    changeBackgroundImage(player.background);

    currentRoom = findRoomWithPlayer();
    currentRoom.unlockedOptions = getUnlockedOptions(currentRoom.options);

    // Set id for the displayed options
    displayedOptions.push(new option('#option-1'));
    displayedOptions.push(new option('#option-2'));
    displayedOptions.push(new option('#option-3'));
    displayedOptions.push(new option('#option-4'));
    displayedOptions.push(new option('#option-5'));

    textbox = select('#textbox');
    textbox.html("")

    updateDebug();

  });
}

////////////////////////////////////////////
// MAIN GAME LOOP
////////////////////////////////////////////

function draw(){

  if(drawText){
    drawTextbox();
  }
  if(drawCanvas){

    // TODO: Borde lagras i en "aktiv encounter"
    switch (current_encounter) {
      case 'flappy_river':
        if(define){fr_defineVar(); define= false;}
        fr_draw();
        break;
      case 'card_game':
        if(define){cg_defineVar(); define= false;}
        cg_draw();
        break;
      case 'ernst_running':
        if(define){
          ernstRun = true;
          waspRun = false;
          er_defineVar();
          define= false;
        }
        er_draw();
        break;
      case 'wasp_library_running':
        if(define){
          ernstRun = false;
          waspRun = true;
          er_defineVar();
          define= false;
        }
        er_draw();
        break;
      case 'ddr':
        if(define){ddr_defineVar(); define= false;}
        ddr_draw();
        break;
      case 'mountain_jump':
        if(define){mj_defineVar(); define = false;}
        mj_draw();
        break;
      case 'frog_king':
        if(define){fk_defineVar(); define = false}
        fk_draw();
        break;
      case 'pepes_bread':
        if(define){pb_defineVar(); define = false;}
        pb_draw();
        break;
      case 'sheep_invaders':
        if(define){
          sheep = true;
          wasp = false;
          i_defineVar();
          define = false;
        }
        i_draw();
        break;
      case 'wasp_invaders':
        if(define){
          wasp = true;
          sheep = false;
          i_defineVar();
          define = false;
        }
        i_draw();
        break;
      case 'clean_your_plate':
        if(define){cyp_defineVar(); define = false;}
        cyp_draw();
        break;
      case 'type_racer':
        if(define){tr_defineVar();define=false;}
        tr_draw();
        break;
      case 'wasp_runner':
        if(define){wr_defineVar();define=false;}
        wr_draw();
        break;
      case 'wasp_click':
        if(define){wc_defineVar();define=false;}
        wc_draw();
        break;
      case 'circle_frog_king':
        if(define){redCircle = true; fk_defineVar(); define = false}
        fk_draw();
        break;
      case 'circle_type_racer':
        if(define){redCircle = true; tr_defineVar(); define=false;}
        tr_draw();
        break;
      case 'circle_wasp_invaders':
        if(define){
          wasp = true;
          sheep = false;
          redCircle = true;
          i_defineVar();
          define = false;
        }
        i_draw();
        break;
      case 'circle_runner':
        if(define){
          ernstRun = true;
          waspRun = false;
          redCircle = true;
          er_defineVar();
          define= false;
        }
        er_draw();
        break;
      case 'final_boss':
        if(define){fb_defineVar();define=false;}
        fb_draw();
        break;
    }
  }
}

////////////////////////////////////////////
// KEY PRESSED
////////////////////////////////////////////
function keyPressed(){
  timer = 0;

  if (!paused) {

    // Stop sound
    $('#choice-holder').get(0).pause();
    $('#choice-holder').get(0).currentTime = 0;

    $('#select-holder').get(0).pause();
    $('#select-holder').get(0).currentTime = 0;

    if(!keypressed){
      // Set the current selected option by the player to no one
      // currentoption 1 is the first option, 2 is second etc.
      currentoption = 0;
      keypressed = true;
    }
    if(keyCode == UP_ARROW){
      if(currentoption > 0){
        currentoption -= 1;
      }else {
        currentoption = optionlength-1;
      }

      // Play choice sound
      $('#choice-holder').get(0).play();

    }
    if(keyCode == DOWN_ARROW){
      if(currentoption < optionlength-1){
        currentoption += 1;
      }else {
        currentoption = 0;
      }

      // Play choice sound
      $('#choice-holder').get(0).play();
    }
    if(keyCode == ENTER){
      if (enterPause == false && !drawCanvas) {
        textbox = select('#textbox');
        textbox.html("")
        counter = 0;
        displayedOptions[currentoption].runCommand();
        currentoption = 0;
        load = false;

        // Play select sound
        $('#select-holder').get(0).play();

        // SAVE PLAYER PROGRESS
        savePlayer()

        // DEBUG
        updateDebug();


      }
    }
    if(keyCode == SHIFT && !soundEnabled){
      blip = loadSound("menu_blip.wav")
      soundEnabled = true;
    }
  }
}

function keyTyped(){
  if(current_encounter == "type_racer"){
    tr_keyTyped();
  }
  if(current_encounter == "final_boss"){
    fb_keyTyped();
  }
}

////////////////////////////////////////////
// PLAYER CLASS
////////////////////////////////////////////

function player(){
  // Keep track of which room the player is in
  this.x = 0;
  this.y = 0;

  // Track stats
  this.inventory = [];
  this.beenTo = [];
  this.intellegence = 0;
  this.dexterity = 0;
  this.charisma = 0;
  this.grit = 0;
  this.kindness = 0;
  this.area = currentArea;
  this.background = backgrounds.beach;
  this.music = music.introBeach;
  this.completed = [];

}

////////////////////////////////////////////
// AREA FUNCTIONS
////////////////////////////////////////////
function getBackgroundFilePath(fileName) {
  // Get path to the game-assets/backgrounds/ folder
  let backgroundAssetFolder = document.getElementById('game-asset-folder').innerText + 'backgrounds/';

  // Get the html element which displays the image
  let backgroundElement = document.getElementById('background-image');

  let newImagePath = backgroundAssetFolder + fileName;

  return newImagePath;
}

function changeBackgroundImage( suppliedImage, isFileName = true ) {

  // Get path to the game-assets/backgrounds/ folder
  let backgroundAssetFolder = document.getElementById('game-asset-folder').innerText + 'backgrounds/';

  // Get the html element which displays the image
  let backgroundElement = document.getElementById('background-image');

  let fileName = suppliedImage;

  // Create full filepath
  if (isFileName == false) {
    fileName = backgrounds[suppliedImage];
  }
  let newImagePath = backgroundAssetFolder + fileName;

  // Update player info
  player.background = fileName;

  // Set new background image
  backgroundElement.src = newImagePath;

  // console.log('SUPPLIED FILENAME: ', fileName);
  // console.log('BACKGROUND ELEMENT SRC:', backgroundElement.src);

}

function changeArea(area) {

  currentArea = area

  // Update player
  player.area = currentArea;

  // Set rooms to new area rooms
  rooms = getRoomsFromArea( allAreaRooms, currentArea );

  // Change background image
  let newAreaImage = getBackgroundImageFromArea( currentArea );
  let newSong = getSongFromArea( currentArea )

  changeBoxColor()

  return [newAreaImage, newSong]

}

function checkOptionCritera(room) {
  let trimmedRoom = room;
  for (option of trimmedRoom.options) {
    // Check if item is supplied
    if (option.command == 'move-ifItem') {
      // Check if player has this item
      if (player.inventory.indexOf(option.values[2]) == -1) {
        // Remove the option
        trimmedRoom.options = trimmedRoom.options.slice(0, trimmedRoom.options.indexOf(option)).concat(trimmedRoom.options.slice(trimmedRoom.options.indexOf(option) + 1, trimmedRoom.options.length));
      }
    }
  }

  return trimmedRoom;
}

function checkStat(stat, value) {
  if (stat == 'intelligence') {
    if (player.intellegence >= value) {
      return true;
    }
    else {
      return false;
    }
  }
  if (stat == 'grit') {
    if (player.grit >= value) {
      return true;
    }
    else {
      return false;
    }
  }
  if (stat == 'kindness') {
    if (player.kindness >= value) {
      return true;
    }
    else {
      return false;
    }
  }
  if (stat == 'charisma') {
    if (player.charisma >= value) {
      return true;
    }
    else {
      return false;
    }
  }
  if (stat == 'dexterity') {
    if (player.dexterity >= value) {
      return true;
    }
    else {
      return false;
    }
  }
}

function getUnlockedOptions(options) {
  let unlockedOptions = []

  if (player.inventory) {
    for (const option of options) {
      if (option.command == 'move-ifItem') {
        if (player.inventory.indexOf(option.values[2]) > -1) {
          unlockedOptions.push(option);
        }
      }
      else if (option.command == 'move-ifNotItem') {
        if (player.inventory.indexOf(option.values[2]) == -1) {
          unlockedOptions.push(option);
        }
      }
      else if (option.command == 'info-ifItem') {
        if (player.inventory.indexOf(option.values[1]) > -1) {
          unlockedOptions.push(option);
        }
      }
      else if (option.command == 'move-stat-ifItem') {
        if (player.inventory.indexOf(option.values[4]) > -1) {
          unlockedOptions.push(option);
        }
      }
      else if (option.command == 'info-ifStat' || option.command == 'item-ifStat') {
        if (checkStat(option.values[1], option.values[2])) {
          unlockedOptions.push(option);
        }
      }
      else if (option.command == 'move-ifStat') {
        if (checkStat(option.values[2], option.values[3])) {
          unlockedOptions.push(option);
        }
      }
      else if (option.command == 'move-item-ifStat') {
        if (checkStat(option.values[3], option.values[4])) {
          unlockedOptions.push(option);
        }
      }
      else if (option.command == 'info-item-ifStat') {
        if (checkStat(option.values[2], option.values[3])) {
          unlockedOptions.push(option);
        }
      }
      else if (option.command == 'move-notBeenTo') {
        if (player.beenTo.indexOf(option.values[2]) == -1) {
          unlockedOptions.push(option);
        }
      }
      else if (option.command == 'move-beenTo') {
        if (player.beenTo.indexOf(option.values[2]) != -1) {
          unlockedOptions.push(option);
        }
      }
       else {
        unlockedOptions.push(option)
      }
    }
    return unlockedOptions;
  } else {
    return options;
  }

}

function checkIfRoomExists(x, y, area = false) {

  let roomsToCheck = (area == false) ? rooms : getRoomsFromArea( allAreaRooms, currentArea );

  for (room of roomsToCheck) {
    if (room.x == x && room.y == y) {
      return true;
    }
  }

  return false;
}

////////////////////////////////////////////
// OPTION CLASS
////////////////////////////////////////////

function option(ref){
  this.ref = select(ref);

  this.text = '';
  this.ref.html(this.text);

  this.command;
  this.values;

  this.highlight = function(){
      this.ref.style('background-color','#fff');
      this.ref.style('padding-color', '#fff');

      highlightColor = getColorFromBackground()
      this.ref.style('color', highlightColor);
  }
  this.unhighlight = function(){
      this.ref.style('background','none');
      this.ref.style('padding-color', 'inherit');
      this.ref.style('color','inherit');
  }

  this.addItemToInventory = function(suppliedValues) {
    // Check if there is enough values
    if (suppliedValues.length >= 1) {
      if (player.inventory.indexOf(suppliedValues[0]) == -1) {
        player.inventory.push(suppliedValues[0]);
        updateInventoryGui();
      } else {
        textbox.html('You already picked up that item!');
        write = false;
      }
    } else {
      console.log('ERROR: Not enough values supplied to item command');
    }
  }
  this.moveToNewPlace = function(suppliedValues, fadeLoad = false, toArea = false) {
    // Check that there are enough values
    if (suppliedValues.length >= 2) {
      let suppliedX = Number(suppliedValues[0]);
      let suppliedY = Number(suppliedValues[1]);

      if (checkIfRoomExists(suppliedX, suppliedY, toArea)) {
        write = true;
        player.x = Number(suppliedValues[0]);
        player.y = Number(suppliedValues[1]);
        currentRoom = findRoomWithPlayer();
        if (fadeLoad) {
          currentRoom.unlockedOptions = getUnlockedOptions(currentRoom.options);
          currentRoom.load();
          savePlayer();
        }
      } else {
        textbox.html("ERROR: ROOM DOES NOT EXIST");
        write = false;
      }


    } else {
      console.log('ERROR: Not enough values supplied to move command');
    }
  }
  this.writeInfo = function(suppliedValues) {
    // Check that there are enough values
    if (suppliedValues.length >= 1) {
      textbox.html(suppliedValues[0]);
      write = false;
    } else {
      console.log('ERROR: Not enough values supplied to info command');
    }
  }
  this.doEncounter = function(suppliedValues, song = false) {
    // Check that there are enough values
    if (suppliedValues.length >= 2) {
      current_encounter = suppliedValues[0]
      define = true;
      clearVar = false;
      startSc = true;
      gameOver = true;
      write = false;
      score = 0;
      if(suppliedValues[1]){fr_hard = true;}//slarvigt måste ändras
      if(!suppliedValues[1]){er_hard = true;}
      $('#intro-screen').css('display', 'none');
      switchToEncounter(song);
    } else {
      console.log('ERROR: Not enough values supplied to encounter command');
    }
  }
  this.giveStat = function(suppliedValues) {
    if (suppliedValues.length >= 2) {
      if (suppliedValues[0] == 'intelligence') {
        player.intellegence += Number(suppliedValues[1])
        updateStatGui('intelligence')
      }
      if (suppliedValues[0] == 'charisma') {
        player.charisma += Number(suppliedValues[1])
        updateStatGui('charisma')
      }
      if (suppliedValues[0] == 'grit') {
        player.grit += Number(suppliedValues[1])
        updateStatGui('grit')
      }
      if (suppliedValues[0] == 'kindness') {
        player.kindness += Number(suppliedValues[1])
        updateStatGui('kindness')
      }
      if (suppliedValues[0] == 'dexterity') {
        player.dexterity += Number(suppliedValues[1])
        updateStatGui('dexterity')
      }

    } else {
      console.log('ERROR: Not enough values supplied to giveIntelligence command');
    }
  }

  this.runCommand = function() {

      // Give player an item
      if(this.command == 'item'){
        this.addItemToInventory(this.values)
      }

      // Move player to new location
      if(this.command == 'move' || this.command == 'move-y' || this.command == 'move-x'){
        this.moveToNewPlace(this.values)
      }

      // Write out info
      if(this.command == 'info'){
        this.writeInfo(this.values)
      }

      // Switch to a new area
      if (this.command == 'switchArea'){
        this.switchToArea(this.values[0])
      }

      // Start a new game
      if(this.command == 'encounter'){
        if (this.values.length >= 5) {
          minigameWin = this.values.slice(0, 2);
          minigameGameOver = this.values.slice(2, 4);
          this.doEncounter(this.values.slice(4));
        } else {
          console.log('ERROR: TO FEW VALUES IN ENCOUNTER COMMAND');
        }
      }

      if(this.command == 'encounter-music'){
        if (this.values.length >= 5) {
          minigameWin = this.values.slice(0, 2);
          minigameGameOver = this.values.slice(2, 4);

          let encounterCmd = this.values.slice(4);
          let copy = encounterCmd;
          let encounterSong = copy[0].split(" ");
          // console.log({encounterSong});

          let encounterYes = [encounterSong[0], '']
          this.doEncounter(encounterYes, encounterSong[1]);

        } else {
          console.log('ERROR: TO FEW VALUES IN ENCOUNTER COMMAND');
        }
      }

      // Give stats
      if(this.command == 'giveStat'){
        this.giveIntelligence(this.values);
      }

      // ******************
      // COMPOSITE COMMANDS
      // ******************

      // (x, y), item to add to inventory
      if(this.command == 'move-item'){
        this.moveToNewPlace(this.values.slice(0, 2));
        this.addItemToInventory(this.values.slice(2));

      }
      // (z, y), item, new area
      if(this.command == 'move-item-switchArea'){
        this.moveToNewPlace(this.values.slice(0, 2));
        this.addItemToInventory(this.values.slice(2, 3));
        this.switchToArea(this.values.slice(3, 4));

      }

      // (x, y), (stat, change)
      if(this.command == 'move-stat'){
        this.moveToNewPlace(this.values.slice(0, 2));
        this.giveStat(this.values.slice(2));
      }

      // info, (stat, change)
      if(this.command == 'info-stat'){
        this.writeInfo(this.values.slice(0, 1));
        this.giveStat(this.values.slice(1));
      }

      // info, item
      if(this.command == 'info-item'){
        this.writeInfo(this.values.slice(0, 1));
        this.addItemToInventory(this.values.slice(1));
      }

      // (x, y), new area name
      if(this.command == 'move-switchArea'){
        let move = this.values.slice(0, 2);
        let newArea = this.values.slice(2,3)[0]
        let self = this;

        let newAssets = changeArea( newArea );

        fade(newAssets[1], function() {
          changeBoxColor()
          changeBackgroundImage(newAssets[0]);
          self.moveToNewPlace(move, true);
        })

      }

      // (x, y), new background name
      if(this.command == 'move-background'){
        let self = this;

        fade(false, function() {
          changeBoxColor()
          changeBackgroundImage(self.values.slice(2,3)[0], false);
          self.moveToNewPlace(self.values.slice(0,2), true);

        })

        // this.moveToNewPlace(this.values.slice(0,2));
        // changeBackgroundImage(this.values.slice(2)[0]);
      }

      // (x,y), background name, music name
      if(this.command == 'move-background-music'){
        let self = this;
        let song = music[this.values[3]];

        fade(song, function() {
          changeBoxColor()
          // self.moveToNewPlace(self.values.slice(0,2));
          changeBackgroundImage(self.values.slice(2)[0], false);
          self.moveToNewPlace(self.values.slice(0,2), true);

        })
      }

      // (x,y), background name, music name
      if(this.command == 'move-ifItem'){
        this.moveToNewPlace(this.values.slice(0,2));
      }

      if(this.command == 'move-ifNotItem'){
        this.moveToNewPlace(this.values.slice(0, 2));
      }

      if(this.command == 'move-addBeenTo'){
        this.moveToNewPlace(this.values.slice(0,2));
        player.beenTo.push(this.values.slice(2)[0]);
        updateBeenToGui();
      }

      if (this.command == 'move-ifStat') {
        this.moveToNewPlace(this.values.slice(0,2));
      }

      if (this.command == 'move-stat-ifItem') {
        this.moveToNewPlace(this.values.slice(0,2));
        this.giveStat(this.values.slice(2,4));
      }

      if (this.command == 'item-ifStat') {
        this.addItemToInventory(this.values.slice(0,1));
      }

      if (this.command == 'move-item-ifStat') {
        this.moveToNewPlace(this.values.slice(0,2));
        this.addItemToInventory(this.values.slice(2,3));
      }

      if (this.command == 'info-item-ifStat') {
        this.writeInfo(this.values.slice(0, 1));
        this.addItemToInventory(this.values.slice(1,2));
      }

      if (this.command == 'info-ifStat') {
        this.writeInfo(this.values.slice(0, 1));
      }

      if (this.command == 'info-ifItem') {
        this.writeInfo(this.values.slice(0, 1));
      }

      if(this.command == 'move-notBeenTo'){
        this.moveToNewPlace(this.values.slice(0,2));
      }

      if(this.command == 'move-beenTo'){
        this.moveToNewPlace(this.values.slice(0,2));
      }

      if(this.command == 'gameover'){
        // Show game over screen
        $('#audio-holder').attr('src', music['gameOver']);
        $('#gameover').addClass('active');
      }

      if(this.command == 'endscreen'){
        // Show end screen
        let chapterCompleted = Number(this.values[0]);
        let self = this;
        let song = music['gameOver'];
        let move = this.values.slice(1, 3);

        let completed = {
          chapter: chapterCompleted,
          inventory: player.inventory,
          beenTo: player.beenTo,
          charisma: player.charisma,
          dexterity: player.dexterity,
          grit: player.grit,
          kindness: player.kindness,
        }

        player.completed.push(completed);

        let newArea = getAreaNameFromIndex(chapterCompleted + 2);
        let newAssets = changeArea( newArea );

        paused = true;

        fade(music.gameOver, function() {
          changeBoxColor()
          changeBackgroundImage(newAssets[0]);
          self.moveToNewPlace(move, true);
          changeRoom(newArea, move[0], move[1]);

          player.music = newAssets[1];

          $('#endscreen').addClass('active');
          document.getElementById('endscreen-img').src = getBackgroundFilePath(endscreens[chapterCompleted - 1])

          // Remove continue to next chapter if it has not been released yet
          // if (player.completed.length >= completedChapters) {
          //   $('#continue-next-chapter').addClass('hidden');
          // }
        })

      }

      if(this.command == 'video'){
        // Play the video sequence
        let self = this;
        playVideo(function() {
          self.moveToNewPlace(self.values);
        });
      }
  }

}

////////////////////////////////////////////
// ROOM CLASS
////////////////////////////////////////////

function Room( x, y, mainText, options ){

  // Set default values
  this.x = x;
  this.y = y;
  this.mainText = mainText;
  this.options = options;
  this.unlockedOptions = getUnlockedOptions(this.options);

  // Go through

  this.load = function(){

    // Reset the displayed options
    for (var i = 0; i < maxOptionLength; i++) {
      displayedOptions[i].ref.html('');
    }

    // Get the number of displayedOptions
    optionlength = this.unlockedOptions.length;

    // Set the option variables for use in front ends display options
    for (var i = 0; i < optionlength; i++) {
      // Set the displayed text on option
      displayedOptions[i].ref.html(this.unlockedOptions[i].text);

      displayedOptions[i].command = this.unlockedOptions[i].command;

      displayedOptions[i].values = this.unlockedOptions[i].values;

    }

  }

}

  function typing(divId, inputtext){

    this.divId = divId;

    if (counter < inputtext.length) {
      // Play sound effect if it is not playing
      if ($('#textloop-holder').get(0).paused && !paused) {
        $('#textloop-holder').get(0).play();
      }

      document.getElementById(this.divId).innerHTML += inputtext.charAt(counter);
      counter++
    } else {
      write = false;
      // Reset sound effect
      $('#textloop-holder').get(0).pause();
    }

  }

  function switchToEncounter(song = false){
    // Switch to boss music
    if (song != false) {
      $('#audio-holder').attr('src', music[song])
    } else {
      $('#audio-holder').attr('src', music['boss'])
    }

    drawText = false;
    drawCanvas = true;
    grandparent.hide();
    canvas.show();

  }
  function switchToText(){
    drawText = true;
    drawCanvas = false;

    // Switch music
    $('#audio-holder').attr('src', player.music)

    document.getElementById('grandparent').style = '';
    document.getElementById('grandparent').style.display = 'flex';
    //grandparent.show();
    canvas.hide();

  }


function defineCanvas(){
  canvas = createCanvas(600, 600);
  // canvas.style('position: absolute')
  // canvas.style('margin: auto')
  // canvas.style('margin: auto')

  canvas.class('box');
  canvas.hide();

}


function drawTextbox(){

  // ALL OF THIS IS CALLED SEVERAL TIMES A SECOND: NEED FOR LOOPS EVERY TIME?? haah probably not //zeo
  if (!paused) {
    for (var i = 0; i < displayedOptions.length; i++) {
      displayedOptions[i].unhighlight();
    }

    if(keypressed && timer < 600){
      timer++;
      displayedOptions[currentoption].highlight();
    }

    if (write) {
      typing('textbox', currentRoom.mainText);
    }

    if(!load && currentRoom){
      currentRoom.unlockedOptions = getUnlockedOptions(currentRoom.options);
      currentRoom.load();
      load = true;
    }
  }

}

function findRoomWithPlayer(){
  for (var i = 0; i < rooms.length; i++) {
    if(player.x == rooms[i].x && player.y == rooms[i].y){
      return rooms[i];
      break;
    }
  }
}

function resetTextbox() {
  write = true;
  currentRoom = findRoomWithPlayer();
  currentRoom.load();
  textbox = select('#textbox');
  textbox.html("")
  counter = 0;
  currentoption = 0;
  load = false;
  typing('textbox', currentRoom.mainText);
  drawTextbox();
}


function changeRoom( area, x, y ) {

  // Set the properties
  currentArea = area;
  player.area = area;
  changeArea( currentArea );

  rooms = getRoomsFromArea( allAreaRooms, currentArea );

  player.x = Number(x);
  player.y = Number(y);

  // Set current room
  currentRoom = findRoomWithPlayer();
  currentRoom.unlockedOptions = getUnlockedOptions(currentRoom.options);
  currentRoom.load();

  // Update gui
  resetTextbox()
  updateDebug();
  updateInventoryGui()
  updateBeenToGui();
  updateStatGui('', true);

  // Save to database
  savePlayer();

}


function changeRoomDebug() {

  const switchAreaElement = document.getElementById('switch-area');
  const switchArea = switchAreaElement.options[switchAreaElement.selectedIndex].text;

  const switchX = document.getElementById('switch-x').value;
  const switchY = document.getElementById('switch-y').value;

  // Got all values
  if (switchArea && switchX && switchY) {
    changeRoom(switchArea, switchX, switchY);
    let newAssets = changeArea(switchArea);
    changeBackgroundImage(newAssets[0]);
    changeBoxColor()
  }

}

function clearInventory() {
  player.inventory = [];
  savePlayer();
  updateDebug();
  updateInventoryGui();
}

function addItemDebug(inputId) {
  let input = document.getElementById(inputId);

  player.inventory.push(input.value);
  savePlayer();
  updateDebug();
  updateInventoryGui();

  input.value = '';
}

function clearBeenTo() {
  player.beenTo = [];
  savePlayer();
  updateDebug();
  updateBeenToGui();
}

function addBeenToDebug(inputId) {
  let input = document.getElementById(inputId);

  player.beenTo.push(input.value);
  savePlayer();
  updateDebug();
  updateBeenToGui();

  input.value = '';
}

function resetPlayer() {

  // Keep track of which room the player is in
  player.x = 0;
  player.y = 0;

  // Track stats
  player.inventory = [];
  player.beenTo = [];
  player.intellegence = 0;
  player.charisma = 0;
  player.dexterity = 0;
  player.grit = 0;
  player.kindness = 0;
  player.area = currentArea;
  player.background = backgrounds.beach;
  player.music = music.introBeach;
  player.completed = [];

  currentArea = 'intro';
  rooms = getRoomsFromArea( allAreaRooms, currentArea );
  changeRoom( 'intro', 0, 0 );
  player.background = backgrounds.beach;

  changeBackgroundImage( player.background );
  savePlayer();

  resetTextbox();
  updateBeenToGui();

  updateDebug();

  location.reload();
  return false;

}

function updateDebug() {
  const roomInfo = document.getElementById('room-info');
  roomInfo.innerText = `${player.area}: (${player.x}, ${player.y})`;
}

function updateInventoryGui() {
  const inventoryUl = document.getElementById('inventory');

  // Remove all children
  inventoryUl.innerHTML = '';

  // Add a list item for every inventory item
  for (const item of player.inventory) {
    var li = document.createElement("li");
    li.appendChild(document.createTextNode(item));
    inventoryUl.appendChild(li);
  }
}

function updateBeenToGui() {
  const beenToUl = document.getElementById('beenTo');

  // Remove all children
  beenToUl.innerHTML = '';

  // Add a list item for every inventory item
  for (const beenTo of player.beenTo) {
    var li = document.createElement("li");
    li.appendChild(document.createTextNode(beenTo));
    beenToUl.appendChild(li);
  }
}

function updateStatGui(stat = '', all = false) {

  if (stat == 'intelligence' || all == true) {
    document.getElementById('intelligence').innerText = player.intellegence;
  }
  if (stat == 'charisma' || all == true) {
    document.getElementById('charisma').innerText = player.charisma;
  }
  if (stat == 'grit' || all == true) {
    document.getElementById('grit').innerText = player.grit;
  }
  if (stat == 'kindness' || all == true) {
    document.getElementById('kindness').innerText = player.kindness;
  }
  if (stat == 'dexterity' || all == true) {
    document.getElementById('dexterity').innerText = player.dexterity;
  }

}

function startFromChapter(chapter) {
  // If chapter one, reset player
  if (chapter == 1) {
    clearPlayer();
  }

  // Check if player has saved completed
  if (!player.completed) {
    player.completed = [];
  }

  if (player.completed.length < chapter - 1) {
    return;
  }

  // Reset player properties to that chapter
  player.inventory = player.completed[chapter - 2].inventory;
  player.beenTo = player.completed[chapter - 2].beenTo;
  player.charisma = player.completed[chapter - 2].charisma;
  player.dexterity = player.completed[chapter - 2].dexterity;
  player.grit = player.completed[chapter - 2].grit;
  player.kindness = player.completed[chapter - 2].kindness;

  // Remove completed at and after given chapter
  let newCompleted = [];
  for (let i = 0; i < chapter - 1; i++) {
    newCompleted.push(player.completed[i]);
  }

  player.completed = newCompleted;

  // Change area to chapter
  let newArea = getAreaNameFromIndex(chapter + 1);
  let startX = chapterCoordinates[chapter - 1].start[0];
  let startY = chapterCoordinates[chapter - 1].start[1];

  let newAssets = changeArea(newArea);

  // console.log({player, chapter, newCompleted, newArea, startX, startY, newAssets});

  // Change background and music
  fade(newAssets[1], function() {

    // Set player to start coordinates for that chapter
    changeRoom(newArea, startX, startY);
    changeBackgroundImage(newAssets[0]);
    resetTextbox();
    changeBoxColor()
    $('#intro-screen').addClass('hidden');
    $('#game-img').css('opacity', 1);
    $('#grandparent').css('z-index', 2);

    // SHow toggle admin
    document.getElementById('toggle-admin').style.opacity = 1;

    savePlayer();

    setTimeout(function() {
      paused = false;

    }, 2000)

  })

}

function startTimer(duration, display, callback) {
    var timer = duration, minutes, seconds;
    let intervalId = setInterval(function () {
        minutes = parseInt(timer / 60, 10);
        seconds = parseInt(timer % 60, 10);

        minutes = minutes < 10 ? "0" + minutes : minutes;
        seconds = seconds < 10 ? "0" + seconds : seconds;

        display.textContent = minutes + ":" + seconds;

        // Update local storage
        localStorage.setItem('video-time-left', timer);

        if (--timer < 0) {
            $('#video-player').addClass('hidden');
            clearInterval(intervalId);

            // Stop video
            document.getElementById('video').src = 'https://www.youtube.com/embed/D_d7zcckIwA?start=1&autoplay=0&showinfo=0&rel=0&iv_load_policy=3&controls=0&disablekb=1';

            // Start game again
            paused = false;

            // Start music
            if (hasSound) {
              $('#audio-holder').animate({
                  volume: 0.3
              }, 3000)
            }

            callback();

        }
    }, 1000);
}

function playVideo(callback) {
  paused = true;

  // Pause music
  $('#audio-holder').animate({
      volume: 0
  }, 3000)

  // Restart and play video
  document.getElementById('video').src = 'https://www.youtube.com/embed/D_d7zcckIwA?start=1&autoplay=1&showinfo=0&rel=0&iv_load_policy=3&controls=0&disablekb=1';

  // Check if there is a local storage
  let videoDuration = 60 * 38 + 29;
  //let videoDuration = 10;

  let localTimeLeft = localStorage.getItem('video-time-left')
  if (localTimeLeft != null) {
    if (localTimeLeft != '0') {
      // Set to saved value
      videoDuration = Number(localTimeLeft);
      let atTime = (60 * 38 + 29) - videoDuration;

      // Start video att that time
      document.getElementById('video').src = `https://www.youtube.com/embed/D_d7zcckIwA?start=${atTime}&autoplay=1&showinfo=0&rel=0&iv_load_policy=3&controls=0&disablekb=1`;
    }
  }

  display = document.querySelector('#timer-label');

  $('#video-player').removeClass('hidden');
  startTimer(videoDuration, display, callback);
}

function enterLockdown(time) {
  if (enterPause == false) {
    enterPause = true;
    setTimeout(function() {
      enterPause = false;
    }, time)
  }
}
