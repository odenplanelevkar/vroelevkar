let usingRoomDraw = true;

let roomBoxSize = 64; //helst delbar m 2
let spriteGridWidth = 3;
let spriteGridHeight = 3;

let canvasSize = {width: roomBoxSize*50, height: roomBoxSize*50 }

let topCoordinate = {x: -1, y: -1};
let activeRoom; //stores the active room Object.
let ableToCreateRoom;

let roomArrays;
let activeArea;
let activeRoomArray;
let activeRoomSpriteArray;

let guiMouseIsOver = false;

let showGrid = true;
let showAllConnections = false;
let showCoordinateText = true;
let selectButtonPressed = false;


let guiParent
let maxCommandValues = 6;
let maxOptionAmount = 6;

let generalGui;
let generalGuiParent;

let connectionColors;

let selectedArea = 'test';

function getAreaRoomsFromRoomArrays() {
  // Go through roomArrays and extract only the rooms from each area.
  let areaRooms = [];

  areaRooms.push(roomArrays.test.rooms);
  areaRooms.push(roomArrays.intro.rooms);
  areaRooms.push(roomArrays.highlands.rooms);
  areaRooms.push(roomArrays.bog.rooms);
  areaRooms.push(roomArrays.city.rooms);
  areaRooms.push(roomArrays.mountain.rooms);
  areaRooms.push(roomArrays.core.rooms);

  // DEBUG:
  console.log('GET AREA ROOMS: ', areaRooms);

  return areaRooms;

}

function loadRoomArraysFromAreaRooms( areaRoomsArray ) {
  // Go through areaRooms and set the rooms parameter on each area

  // Check that there are enough rooms in each
  roomArrays.test.rooms = (areaRoomsArray.length >= 0) ? areaRoomsArray[0] : [];
  roomArrays.intro.rooms = (areaRoomsArray.length >= 1) ? areaRoomsArray[1] : [];
  roomArrays.highlands.rooms = (areaRoomsArray.length >= 2) ? areaRoomsArray[2] : [];
  roomArrays.bog.rooms = (areaRoomsArray.length >= 3) ? areaRoomsArray[3] : [];
  roomArrays.city.rooms = (areaRoomsArray.length >= 4) ? areaRoomsArray[4] : [];
  roomArrays.mountain.rooms = (areaRoomsArray.length >= 5) ? areaRoomsArray[5] : [];
  roomArrays.core.rooms = (areaRoomsArray.length >= 6) ? areaRoomsArray[6] : [];

  roomArrays.test.sprites = (areaRoomsArray.length >= 0) ? createSpriteArrayFromRoomArray(areaRoomsArray[0]) : [];
  roomArrays.intro.sprites = (areaRoomsArray.length >= 1) ? createSpriteArrayFromRoomArray(areaRoomsArray[1]) : [];
  roomArrays.highlands.sprites = (areaRoomsArray.length >= 2) ? createSpriteArrayFromRoomArray(areaRoomsArray[2]) : [];
  roomArrays.bog.sprites = (areaRoomsArray.length >= 3) ? createSpriteArrayFromRoomArray(areaRoomsArray[3]) : [];
  roomArrays.city.sprites = (areaRoomsArray.length >= 4) ? createSpriteArrayFromRoomArray(areaRoomsArray[4]) : [];
  roomArrays.mountain.sprites = (areaRoomsArray.length >= 5) ? createSpriteArrayFromRoomArray(areaRoomsArray[5]) : [];
  roomArrays.core.sprites = (areaRoomsArray.length >= 6) ? createSpriteArrayFromRoomArray(areaRoomsArray[6]) : [];

  //
  activeArea = roomArrays.test;
  activeRoomArray = activeArea.rooms;
  activeRoomSpriteArray = activeArea.sprites;

  // DEBUG
  console.log('LOADED ROOM ARRAYS: ', roomArrays);
  drawScene()
}


function setup(){
  roomArrays = {//kanske konstigt att kalla det sprites
    test:      {rooms: [], sprites: [], lightColor: color(255, 255, 255), darkColor: color( 51,  51,  51)},
    intro:     {rooms: [], sprites: [], lightColor: color(214, 214, 214), darkColor: color( 51,  51,  51)},
    highlands: {rooms: [], sprites: [], lightColor: color(208, 240, 156), darkColor: color( 73, 117,  52)},
    bog:       {rooms: [], sprites: [], lightColor: color(189, 172, 157), darkColor: color( 66,  46,  33)},
    city:      {rooms: [], sprites: [], lightColor: color(212, 207, 178), darkColor: color(120, 108,  41)},
    mountain:  {rooms: [], sprites: [], lightColor: color(135, 222, 224), darkColor: color( 64, 106, 107)},
    core:      {rooms: [], sprites: [], lightColor: color(145,  42,  42), darkColor: color( 36,  35,  35)}
  }

  activeArea = roomArrays.test;
  activeRoomArray = activeArea.rooms;
  activeRoomSpriteArray = activeArea.sprites;

  connectionColors = [color(255, 0, 0), color(255, 255, 0), color(0, 255, 0), color(0, 255, 255), color(0, 0, 255)];

  createCanvas(canvasSize.width, canvasSize.height);
  guiParent = document.getElementById('guiBackground');
  guiParent.onmouseenter = function(){guiMouseIsOver = true;}
  guiParent.onmouseleave = function(){guiMouseIsOver = false;}

  generalGuiParent = document.getElementById('generalGuiBackground');
  generalGuiParent.onmouseenter = function(){guiMouseIsOver = true;}
  generalGuiParent.onmouseleave = function(){guiMouseIsOver = false;}

  createGeneralGui();

  // Get rooms array
  loadRoomsFromDatabase('all', function(returnedRooms) {
    // No rooms were found, set a default blank
    if (returnedRooms.length > 0){
      // BELOW IS FOR MULITPLE AREAS
      console.log('RETURNED ROOMS:', returnedRooms);
      // Parse all area rooms
      loadRoomArraysFromAreaRooms( returnedRooms );
      console.log('FROM DATABASE: ', activeRoomArray);
    }
  }); // End load rooms
  drawScene()
}

function drawScene(){
  background(activeArea.darkColor);
  if(showGrid){
    drawGrid();
  }
  for (var i = 0; i < activeRoomSpriteArray.length; i++) {
    activeRoomSpriteArray[i].draw();
  }
  drawConnections();
}

function drawGrid(){
  for (var y = 0; y < height; y+= roomBoxSize) {
    stroke(255,255,255,20)
    line(0,y,width,y)
  }
  for (var x = 0; x < width; x+= roomBoxSize) {
    stroke(255,255,255,20)
    line(x,0,x,height)
  }
}

function keyPressed(){

  if(keyCode === 70){ //f
    centerAt(activeRoom.x, activeRoom.y);
  }
  if(keyCode === ENTER){
    showValueAmountControl();
    optionShowControl();
    drawScene()
  }
}

function mousePressed(){
  let mouseScreenCoordinates = {x: mouseX + (roomBoxSize/2-(mouseX%roomBoxSize)), y: mouseY + (roomBoxSize/2-(mouseY%roomBoxSize)) }
  let mouseIndexCoordinates = screenToIndexCoordinates(mouseScreenCoordinates.x, mouseScreenCoordinates.y);
  let onSprite = false;
  for (var i = 0; i < activeRoomSpriteArray.length; i++) {
    if(mouseIndexCoordinates.x == activeRoomSpriteArray[i].indexX && mouseIndexCoordinates.y == activeRoomSpriteArray[i].indexY){
      activeRoomSpriteArray[i].onMousePressed();
      onSprite = true
      break;
    }
  }
  if(!onSprite && !guiMouseIsOver){
    createRoomSprite(mouseScreenCoordinates.x, mouseScreenCoordinates.y);
  }
  drawScene()
}

function drawZeroIndicator(){
  frX = floor(width/2) + (32-(floor(width/2)%64));
  frY = floor(height/2) + (32-(floor(height/2)%64));
  noFill();
  stroke(activeArea.lightColor)
  rectMode(CENTER);
  rect(frX,frY,roomBoxSize, roomBoxSize);
  indexCoordinates = screenToIndexCoordinates(frX,frY);

  fill(activeArea.lightColor);
  coordinateStr= "(" + indexCoordinates.x + ", " + indexCoordinates.y + ")"
  textSize(15);
  text(coordinateStr, frX-roomBoxSize/2+10,frY-roomBoxSize/2+25);
}

function createRoomSprite(x, y){
  if(activeRoom){deselectSpriteObj(activeRoom)}
  rs = new roomSprite(x,y);
  rs.active = true;
  activeRoom = rs;
  activeRoomSpriteArray.push(rs);
}

function roomSprite(x,y){
  this.x = x;
  this.y = y;
  this.active = true;
  this.indexX = (this.x-floor(width/2)-roomBoxSize/2)/roomBoxSize;
  this.indexY = (this.y-floor(height/2)-roomBoxSize/2)/roomBoxSize;
  this.optionGuis = [];
  this.coordinateString = "(" + this.indexX + ", " + this.indexY + ")";
  this.gui = QuickSettings.create(10, 10, "Room "+ this.coordinateString, guiParent);
  this.gui.setDraggable(false);
  this.gui.setWidth(250);
  this.gui.addTextArea('main_text', "");
  this.gui.addNumber('option_amount', 0 ,maxOptionAmount,0,1,function(){optionShowControl()});
  this.gui.addButton('delete',function(){deleteRoom(); drawScene()});

  for (var i = 1; i <= maxOptionAmount; i++) {
    let optGui = QuickSettings.create( 210*i+60,10,'option_'+i, guiParent).hide();
    optGui.setDraggable(false);
    optGui.addText('option_text', '' );
    optGui.addText('option_command', ['','move', 'info'], function(){showValueAmountControl()});
    optGui.addHTML('command_description', 'test');

    for (var j = 0; j < maxCommandValues; j++) {
      optGui.addText('command_value_'+j);
      optGui.hideControl('command_value_'+j);
    }
    this.optionGuis.push(optGui);
  }
  this.draw = function(){
    rectMode(CENTER);
    if(this.active){
      fillColor = activeArea.lightColor
      strokeColor = activeArea.darkColor
    }
    if(!this.active){
      fillColor = activeArea.darkColor
      strokeColor = activeArea.lightColor
    }
    fill(fillColor);
    stroke(strokeColor);
    rect(this.x,this.y, roomBoxSize, roomBoxSize);
    if(showCoordinateText){
      textSize(15);
      noStroke();
      fill(strokeColor);
      text(this.coordinateString, this.x-roomBoxSize/2+10, this.y-roomBoxSize/2+25)
    }
  }
  this.onMousePressed = function(){
    if(!guiMouseIsOver || selectButtonPressed){
      selectButtonPressed = false;
      //hide active room
      if(activeRoom){
        deselectSpriteObj(activeRoom);
      }
      //show this room
      selectSpriteObj(this);
    }
  }
}
// Hide all guis for all areas
function hideGuis(areaIndex = 0){
  let spriteArrays =
  [
    roomArrays.test.sprites,
    roomArrays.intro.sprites,
    roomArrays.highlands.sprites,
    roomArrays.bog.sprites,
    roomArrays.city.sprites,
    roomArrays.mountain.sprites,
    roomArrays.core.sprites
  ]

  for (const spriteArray of spriteArrays) {
    for (const sprite of spriteArray) {
      // Hide main guis
      sprite.gui.hide();
      //Hides option guis
      for (const option of sprite.optionGuis) {
        option.hide();
      }
    }
  }
}

//saves roomSprite from spriteArray into roomArray
function saveRoom(roomSpriteToSave, spriteArray, roomArray){
  // Check if there already exists a room with the coordinates

  let doesRoomExist = false;
  for (const room of roomArray){
    if (room.x == roomSpriteToSave.indexX && room.y == roomSpriteToSave.indexY) {
      doesRoomExist = true;
    }
  }

  if (!doesRoomExist) {
    let exportRoom = new Room(roomSpriteToSave.indexX, roomSpriteToSave.indexY, roomSpriteToSave.gui.getValue('main_text'), [])
    //save option object
    for (var i = 0; i < roomSpriteToSave.gui.getValue('option_amount'); i++) {
      console.log("createOption");
      let option = {
        text: roomSpriteToSave.optionGuis[i].getValue('option_text'),
        command: roomSpriteToSave.optionGuis[i].getValue('option_command'),
        values: []
      }
      for (var j = 0; j < maxCommandValues; j++) {
        option.values.push(roomSpriteToSave.optionGuis[i].getValue('command_value_'+j)) //sparar onödigt många värden men yolo

      }
      exportRoom.options.push(option);
    }
    roomArray.push(exportRoom);
    console.log('IN SAVEROOM IF');

  }else { //uppdaterar objektet om det redan finns

    console.log('IN SAVEROOM ELSE');

    //hittar vilket rum roomSpriteToSave är
    for (var i = 0; i < roomArray.length; i++) {
      if (roomArray[i].x == roomSpriteToSave.indexX && roomArray[i].y == roomSpriteToSave.indexY) {
        roomArray[i].mainText = roomSpriteToSave.gui.getValue('main_text');
        for (var j = 0; j < roomSpriteToSave.optionGuis.length; j++) {
          //kollar om det optionet finns
          if(roomArray[i].options[j]){
            roomArray[i].options[j].text = roomSpriteToSave.optionGuis[j].getValue('option_text')
            roomArray[i].options[j].command = roomSpriteToSave.optionGuis[j].getValue('option_command')
            for (var k = 0; k < maxCommandValues; k++) {
              roomArray[i].options[j].values[k] = roomSpriteToSave.optionGuis[j].getValue('command_value_'+k);
            }
          }else{
            let option = {
              text: roomSpriteToSave.optionGuis[j].getValue('option_text'),
              command: roomSpriteToSave.optionGuis[j].getValue('option_command'),
              values: []
              }
            for (var k = 0; k < maxCommandValues; k++) {
              option.values.push(roomSpriteToSave.optionGuis[j].getValue('command_value_'+k))
            }
            roomArray[i].options.push(option);
          }
          //tar bort om det finns options utöver option amount
          if(j > roomSpriteToSave.gui.getValue("option_amount")){
            roomArray[i].options.pop()
          }
        }
        // Hide everything
        hideGuis(getAreaIndex(selectedArea));

        // Show current rooms
        for (sprite of spriteArray) {
          sprite.gui.show();
        }

        // Show the active room on top
        spriteArray[i].gui.show();

        // Remove empty options from option array
        roomArray[i].options = roomArray[i].options.filter(function(value, index, arr) {
          // Show all options
          spriteArray[i].optionGuis[index].show();

          // Hide empty rooms
          if (value.text == "") {
              spriteArray[i].optionGuis[index].hide();
          }

          // Remove those options which are empty
          return value.text != "";
        })

        break;
      }
    }
  }
}

function selectRoom(indexX, indexY){
  let roomExists = false;
  for (var i = 0; i < activeRoomSpriteArray.length; i++) {
    if(indexX == activeRoomSpriteArray[i].indexX && indexY == activeRoomSpriteArray[i].indexY){
      activeRoomSpriteArray[i].onMousePressed();
      roomExists = true
      console.log(activeRoomSpriteArray[i]);
      console.log(activeRoom);
      drawScene()
      break;
    }
  }
  if(!roomExists){
    console.log("ERROR: Room you tried to select does not exist");
  }
}

function saveAllRooms(){
  let allSpriteArrays =
  [
    roomArrays.test.sprites,
    roomArrays.intro.sprites,
    roomArrays.highlands.sprites,
    roomArrays.bog.sprites,
    roomArrays.city.sprites,
    roomArrays.mountain.sprites,
    roomArrays.core.sprites
  ]
  let allRoomArrays =
  [
    roomArrays.test.rooms,
    roomArrays.intro.rooms,
    roomArrays.highlands.rooms,
    roomArrays.bog.rooms,
    roomArrays.city.rooms,
    roomArrays.mountain.rooms,
    roomArrays.core.rooms
  ]
  for (var i = 0; i < allSpriteArrays.length; i++) {
    for (var j = 0; j < allSpriteArrays[i].length; j++) {
      saveRoom(allSpriteArrays[i][j], allSpriteArrays[i], allRoomArrays[i]);
    }
  }
}

function createSpriteArrayFromRoomArray(inputRoomArray){
  this.inputRoomArray = inputRoomArray
  this.SpriteArray = [];
  activeRoomSpriteArray = [];
  for (var i = 0; i < this.inputRoomArray.length; i++) {
    createCoordinate = indexToScreenCoordinates(this.inputRoomArray[i].x, this.inputRoomArray[i].y);
    //creates a room and pushes it to the activeRoomSpriteArray
    createRoomSprite(createCoordinate.x, createCoordinate.y);
    //hides all guis created
    activeRoomSpriteArray[i].gui.hide();

    for (var j = 0; j < activeRoomSpriteArray[i].optionGuis.length; j++) {
      activeRoomSpriteArray[i].optionGuis[j].hide();
    }
    //set values
    activeRoomSpriteArray[i].gui.setValue("main_text", this.inputRoomArray[i].mainText);
    console.log(this.inputRoomArray[i].options.length);
    console.log(activeRoomSpriteArray[i].gui);
    activeRoomSpriteArray[i].gui.setValue("option_amount", this.inputRoomArray[i].options.length);

    for (var j = 0; j < this.inputRoomArray[i].options.length; j++) {
      activeRoomSpriteArray[i].optionGuis[j].setValue("option_text", this.inputRoomArray[i].options[j].text);
      activeRoomSpriteArray[i].optionGuis[j].setValue("option_command", this.inputRoomArray[i].options[j].command);
      for (var k = 0; k < this.inputRoomArray[i].options[j].values.length; k++) {
        activeRoomSpriteArray[i].optionGuis[j].setValue("command_value_"+k, String(this.inputRoomArray[i].options[j].values[k]));

      }
    }
    this.SpriteArray.push(activeRoomSpriteArray[i]);
  }
  hideGuis(getAreaIndex(selectedArea));
  return this.SpriteArray;

}

function deleteRoom(){
  for (var i = 0; i < activeRoomArray.length; i++) {
    if(activeRoomArray[i].x == activeRoom.indexX && activeRoomArray[i].y == activeRoom.indexY){
      activeRoomArray.splice(i,1);
      break;
    }
  }
  for (var i = 0; i < activeRoomSpriteArray.length; i++) {
    if(activeRoomSpriteArray[i].indexX == activeRoom.indexX && activeRoomSpriteArray[i].indexY == activeRoom.indexY){
      activeRoom.gui.hide();
      for (var j = 0; j < activeRoom.optionGuis.length; j++) {
        activeRoom.optionGuis[j].hide();
      }
      activeRoomSpriteArray.splice(i,1);
      break;
    }
  }
}

function drawConnections(){
  if(showAllConnections){
    for (var i = 0; i < activeRoomSpriteArray.length; i++) {
      drawConnectionsFromRoom(activeRoomSpriteArray[i]);
    }
  }else{
    drawConnectionsFromRoom(activeRoom);
  }
}

function drawConnectionsFromRoom(roomSprite){
  if(roomSprite){
    for (var z = 0; z < roomSprite.optionGuis.length; z++) {

      let optionCommand = roomSprite.optionGuis[z].getValue('option_command');
      if(optionCommand == "move" || optionCommand == 'move-notBeenTo' || optionCommand == 'move-beenTo' || optionCommand == 'move-ifNotItem' || optionCommand == 'move-addBeenTo' || optionCommand == 'move-item' || optionCommand == 'move-background' || optionCommand == 'move-stat' || optionCommand == 'move-switchArea' || optionCommand == 'move-background-music' || optionCommand == 'move-ifItem' || optionCommand == 'move-ifStat' || optionCommand == 'move-item-ifStat' || optionCommand == 'move-stat-ifItem' || optionCommand == 'move-x' || optionCommand== 'move-y' || optionCommand == 'video' ){
        stroke(connectionColors[z]);
        //ta koordinaterna från values delen
        let connection = indexToScreenCoordinates(
          Number(roomSprite.optionGuis[z].getValue('command_value_'+0)),
          Number(roomSprite.optionGuis[z].getValue('command_value_'+1)))

        if(connection.x!=null && connection.y != null){
          //stroke(color(255,255,255));
          line(roomSprite.x-10+z*4, roomSprite.y-10+z*4, connection.x-10+z*4, connection.y-10+z*4)
        }
      }
    }
  }
}

function deselectSpriteObj(roomSprite){
  roomSprite.gui.hide()
  for (var i = 0; i < roomSprite.optionGuis.length; i++) {
    roomSprite.optionGuis[i].hide();
  }
  roomSprite.active = false;
}

function selectSpriteObj(roomSprite){
  roomSprite.gui.show();
  for (var i = 0; i < roomSprite.gui.getValue('option_amount'); i++) {
    roomSprite.optionGuis[i].show()
  }
  roomSprite.active = true;
  activeRoom = roomSprite;
}

function createGeneralGui(){
  generalGui = QuickSettings.create( 0,0,'Settings', generalGuiParent);
  generalGui.setDraggable(false);
  //generalGui.setKey('h')
  generalGui.addButton('center (0,0)', function(){centerAt(width/2, height/2)});
  generalGui.addButton('center to active (f)', function(){centerAt(activeRoom.x,activeRoom.y);});
  generalGui.addDropDown('area' ,['test','intro', 'highlands', 'bog', 'city', 'mountain', 'core'], function(value){
      deselectSpriteObj(activeRoom);
      selectedArea = value.label;
      for (var i = 0; i < 7; i++) { //om det går borde man hämta antal värden i objektet istället för att hårdkoda 7
        if (i == value.index){
          activeArea = roomArrays[value.value];
          activeRoomArray = activeArea.rooms;
          activeRoomSpriteArray = activeArea.sprites;
        }
      }
      drawScene();
  } );
  generalGui.addBoolean('show_all_connections',false, function(value){
    showAllConnections = value;
    drawScene()
  });
  generalGui.addBoolean('show_grid',true,  function(value){
    showGrid = value;
    drawScene();
  });
  generalGui.addBoolean('show_coordinates', true, function(value){
    showCoordinateText = value;
    drawScene();
  });
  generalGui.addBoolean('show_command_descriptions', true,  function(value){
    //borde egentligen loopa alla areas
    for (var i = 0; i < activeRoomSpriteArray.length; i++) {
      for (var j = 0; j < activeRoomSpriteArray[i].optionGuis.length; j++) {
        if(value){
          activeRoomSpriteArray[i].optionGuis[j].showControl("command_description");
        }else {
          activeRoomSpriteArray[i].optionGuis[j].hideControl("command_description");
        }
      }
    }
  });
  generalGui.addButton('hide_all_guis', function(){hideGuis(getAreaIndex(selectedArea))})
  generalGui.addButton('save and upload', function(){

    saveAllRooms();
    hideGuis(getAreaIndex(selectedArea));
    // Get all areas rooms
    let areaRoomsToSave = getAreaRoomsFromRoomArrays();
    saveRoomsToDatabase(areaRoomsToSave);
  });
  generalGui.addText('select_coordinate (space between numbers)')
  generalGui.addButton('select', function(){
    let coordinateText = [generalGui.getValue('select_coordinate (space between numbers)')];
    let coordinateArray = coordinateText.join(' ').split(' ');
    selectButtonPressed = true;
    selectRoom(Number(coordinateArray[0]), Number(coordinateArray[1]))
    centerAt(activeRoom.x,activeRoom.y)

  })
  generalGui.addHTML('Available Commands', '<i>Type in option command to see full description.</i><br><br><b>move</b><br><b>move-y</b><br><b>move-x</b><br><b>info</b><br><b>move-item</b><br><b>encounter</b><br><b>encounter-music</b><br><b>move-item-switchArea</b><br><b>move-background</b><br><b>move-stat</b><br><b>move-switchArea</b><br><b>move-background-music</b><br><b>info-stat</b><br><b>info-item</b><br><b>move-ifItem</b><br><b>move-ifNotItem</b><br><b>move-ifStat</b><br><b>item-ifStat</b><br><b>move-item-ifStat</b><br><b>info-item-ifStat</b><br><b>info-ifStat</b><br><b>info-ifItem</b><br><b>move-notBeenTo</b><br><b>move-beenTo</b><br><b>move-addBeenTo</b><br><b>gameover</b><br><b>endscreen</b><br><b>video</b>')
  generalGui.addHTML('Player stats', 'intelligence<br>charisma<br>grit<br>kindness<br>dexterity' )
}

function indexToScreenCoordinates(indexX, indexY){
  x = indexX*roomBoxSize+floor(width/2)+roomBoxSize/2
  y = indexY*roomBoxSize+floor(height/2)+roomBoxSize/2
  return {x: x, y: y};
}

function screenToIndexCoordinates(screenX, screenY){
  x = (screenX-floor(width/2)-roomBoxSize/2)/roomBoxSize
  y = (screenY-floor(height/2)-roomBoxSize/2)/roomBoxSize
  return {x: x, y: y};
}

function centerAt(x,y){
  window.scrollTo(floor(x-window.innerWidth/2),floor(y-window.innerHeight/2));
}

function optionShowControl(){
  for (var i = 0; i < maxOptionAmount; i++) {
    activeRoom.optionGuis[i].hide();
  }
  for (var i = 0; i < activeRoom.gui.getValue('option_amount'); i++) {
    activeRoom.optionGuis[i].show();
  }
}

function showValueAmountControl(){
  if (activeRoom) {
    for (var i = 0; i < activeRoom.optionGuis.length; i++) {
      showAmount = 0;
      switch (activeRoom.optionGuis[i].getValue('option_command')) {
        case '':
          break;
        case 'move':
          activeRoom.optionGuis[i].setValue('command_description', 'move player to room with coordinates (value 0, value 1)')
          showAmount = 2;
          break;
        case 'move-y':
          activeRoom.optionGuis[i].setValue('command_description', 'move player to room with -1 y-coordinate')
          showAmount = 0;
          activeRoom.optionGuis[i].setValue('command_value_0', activeRoom.indexX);
          activeRoom.optionGuis[i].setValue('command_value_1', activeRoom.indexY - 1);
          break;
        case 'move-x':
          activeRoom.optionGuis[i].setValue('command_description', 'move player to room with +1 x-coordinate')
          showAmount = 0;
          activeRoom.optionGuis[i].setValue('command_value_0', activeRoom.indexX + 1);
          activeRoom.optionGuis[i].setValue('command_value_1', activeRoom.indexY);
          break;
        case 'info':
          activeRoom.optionGuis[i].setValue('command_description', 'sets new main text but does not change options or move player')
          showAmount = 1;
          break;
        case 'move-item':
          activeRoom.optionGuis[i].setValue('command_description', 'move to new coordinates (value 0, value 1) and give item (value 2)')
          showAmount = 3;
          break;
        case 'encounter':
          activeRoom.optionGuis[i].setValue('command_description', 'plays minigame with name (value 4), move to win room (value 0, value 1), move to game over room (value 2, value 3)')
          showAmount = 5;
          break;
        case 'encounter-music':
          activeRoom.optionGuis[i].setValue('command_description', 'plays minigame with name and music (SEPPARATED BY SPACE!) (value 4) , move to win room (value 0, value 1), move to game over room (value 2, value 3)')
          showAmount = 5;
          break;
        case 'move-item-switchArea':
          activeRoom.optionGuis[i].setValue('command_description', 'move to new coordinates (value 0, value 1), give item (value 2) and switch to area (value 3)')
          showAmount = 4;
          break;
        case 'move-background':
          activeRoom.optionGuis[i].setValue('command_description', 'move to new coordinates (value 0, value 1) and change background with filename (value 2)')
          showAmount = 3;
          break;
        case 'move-stat':
          activeRoom.optionGuis[i].setValue('command_description', 'move to new coordinates (value 0, value 1) and change stat (value 2) with (value 3)')
          showAmount = 4;
          break;
        case 'move-switchArea':
          activeRoom.optionGuis[i].setValue('command_description', 'move to new coordinates (value 0, value 1) and switch to area str (value 2)')
          showAmount = 3;
          break;
        case 'move-background-music':
          activeRoom.optionGuis[i].setValue('command_description', 'move to new coordinates (value 0, value 1), change background with filename (value 2), change music with filename (value 3)')
          showAmount = 4;
          break;
        case 'info-stat':
          activeRoom.optionGuis[i].setValue('command_description', 'give extra info in same room (value 0) and change stat (value 1) with (value 2)')
          showAmount = 3;
          break;
        case 'info-item':
          activeRoom.optionGuis[i].setValue('command_description', 'give extra info in same room (value 0) and give item (value 1)')
          showAmount = 2;
          break;
        case 'move-ifItem':
          activeRoom.optionGuis[i].setValue('command_description', 'move to new coordinates (value 0, value 1) if player has item (value 2)')
          showAmount = 3;
          break;
        case 'move-ifNotItem':
          activeRoom.optionGuis[i].setValue('command_description', 'move to new coordinates (value 0, value 1) if player DOES NOT have item (value 2)')
          showAmount = 3;
          break;
        case 'move-ifStat':
          activeRoom.optionGuis[i].setValue('command_description', 'move to new coordinates (value 0, value 1) if player has stat (value 2) of at least (value 3)')
          showAmount = 4;
          break;
        case 'item-ifStat':
          activeRoom.optionGuis[i].setValue('command_description', 'give item (value 0) if player has stat (value 1) of at least (value 2)')
          showAmount = 3;
          break;
        case 'move-item-ifStat':
          activeRoom.optionGuis[i].setValue('command_description', 'move to new coordinates (value 0, value 1) and give item (value 2) if player has stat (value 3) of at least (value 4)')
          showAmount = 5;
          break;
        case 'info-item-ifStat':
          activeRoom.optionGuis[i].setValue('command_description', 'write info (value 0) and give item (value 1) if has stat (value 2) of at least (value 3)')
          showAmount = 4;
          break;
        case 'info-ifStat':
          activeRoom.optionGuis[i].setValue('command_description', 'write info (value 0) if has stat (value 1) of at least (value 2)')
          showAmount = 3;
          break;
        case 'info-ifItem':
          activeRoom.optionGuis[i].setValue('command_description', 'write info (value 0) if player has item (value 1)')
          showAmount = 2;
          break;
        case 'move-stat-ifItem':
          activeRoom.optionGuis[i].setValue('command_description', 'move to new coordinates (value 0, value 1) and increment stat (value 2) with (value 3) if player has item (value 4)')
          showAmount = 5;
          break;
        case 'move-notBeenTo':
          activeRoom.optionGuis[i].setValue('command_description', 'move to new coordinates (value 0, value 1) if player HAS NOT been to (value 2)')
          showAmount = 3;
          break;
        case 'move-beenTo':
          activeRoom.optionGuis[i].setValue('command_description', 'move to new coordinates (value 0, value 1) if player HAS been to (value 2)')
          showAmount = 3;
          break;
        case 'move-addBeenTo':
          activeRoom.optionGuis[i].setValue('command_description', 'move to new coordinates (value 0, value 1) and add (value 2) to player been to ')
          showAmount = 3;
          break;

        case 'gameover':
          activeRoom.optionGuis[i].setValue('command_description', 'show gameover screen and make player reset the game')
          showAmount = 0;
          break;

        case 'endscreen':
          activeRoom.optionGuis[i].setValue('command_description', 'show endscreen for chapter int (value 0) move to startcoordinates in new area (value 1, value 2)')
          showAmount = 3;
          break;

        case 'video':
          activeRoom.optionGuis[i].setValue('command_description', 'plays the annoying video and then moves to coordinates (value 0, value1)')
          showAmount = 2;
          break;

        default:
          activeRoom.optionGuis[i].setValue('command_description', 'ERROR: command not found')
          showAmount = 0;
          break;
      }
      for (var j = 0; j < maxCommandValues; j++) {
        activeRoom.optionGuis[i].hideControl('command_value_'+j);
      }
      for (var j = 0; j < showAmount; j++) {
        activeRoom.optionGuis[i].showControl('command_value_'+j);
      }
    }
  }
}
