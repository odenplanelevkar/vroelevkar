let usingRoomDraw = true;

let roomBoxSize = 64; //helst delbar m 2
let spriteGridWidth = 3;
let spriteGridHeight = 3;

let canvasSize = {width: roomBoxSize*50, height: roomBoxSize*50 }

let topCoordinate = {x: -1, y: -1};
let activeRoom; //stores the active room Object.
let activeOptionGui;

let ableToCreateRoom;

let roomArrays;
let activeArea;
let activeRoomArray;
let activeRoomSpriteArray;

let guiBackgroundHidden = false;
let guiMouseIsOver = false;

let showGrid = true;
let showAllConnections = false;

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
  drawZeroIndicator();

}

function drawScene(){
  background(activeArea.darkColor);
  if(showGrid){
    drawGrid();
  }
  drawSprites();
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
//TODO: fungerar inte för någon anledning
function outlineZeroRoom(){
  stroke(activeArea.lightColor)
  noFill()
  rect((floor(width/2)*roomBoxSize+roomBoxSize/2),(floor(height/2)*roomBoxSize+roomBoxSize/2), roomBoxSize, roomBoxSize)
}

function highlightOverMouse(){
  if(!guiMouseIsOver){
    hx = mouseX + (32-(mouseX%64));
    hy = mouseY + (32-(mouseY%64));
    fill(color(255,255,255,50));
    noStroke();
    rectMode(CENTER);
    rect(hx,hy,roomBoxSize, roomBoxSize);
    if(showCoordinatesHighlight){
      indexCoordinates = screenToIndexCoordinates(hx,hy);
      fill(activeArea.darkColor);
      coordinateStr= "(" + indexCoordinates.x + ", " + indexCoordinates.y + ")"
      textSize(15);
      text(coordinateStr, hx-roomBoxSize/2+10,hy-roomBoxSize/2+25);
    };
  }
}

function keyPressed(){
  /* tog bort var jobbigt, kanske kan byta till annan knapp
  if(keyCode === TAB){
    window.scrollTo(floor(width/2-window.innerWidth/2),floor(height/2-window.innerHeight/2));
  }
  */
  if(keyCode === ENTER){
    showValueAmountControl();
    optionShowControl();
    drawScene()
  }

}

function mousePressed(){

  drawScene()
  if(activeRoom){ //kollar om något rum ens finns
    showValueAmountControl();
    optionShowControl();
  } //lite spray and pray måste nog inte kallas här

  onSprite = false;
  for (var i = 0; i < activeRoomSpriteArray.length; i++) { // borde göras bättre
    if(activeRoomSpriteArray[i].mouseIsOver){
      onSprite = true;
    }
  }
  if(!onSprite && !guiMouseIsOver){

    createRoom(mouseX + (roomBoxSize/2-(mouseX%roomBoxSize)), mouseY + (roomBoxSize/2-(mouseY%roomBoxSize)) ); //modulo skiten gör så att den hamnar på  sitt grid

  }
  drawScene()


}

function mouseReleased(){
  showValueAmountControl();
  optionShowControl();
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

function createRoom(x, y){

  //ser till att den aktiva guin blir gömd när ett nytt rum skapas
  if(activeRoom && activeRoom.gui){
    showValueAmountControl();//kallas för den borde det och kommer typ kalla den på en massa ställen bara yolo
    optionShowControl(); //lite spray and pray måste nog inte kallas här
    activeRoom.gui.hide();
  }

  this.x = x;
  this.y = y;

  //dubbelkollar att rummet inte finns
  ableToCreateRoom = true;
  for (var i = 0; i < activeRoomSpriteArray.length; i++) {
    if(activeRoomSpriteArray[i].indexX == (this.x-floor(width/2)-roomBoxSize/2)/roomBoxSize && activeRoomSpriteArray[i].indexY ==  (this.y-floor(height/2)-roomBoxSize/2)/roomBoxSize){
      print("error: stupid code tried to make a room where one already existed");
      ableToCreateRoom = false;

    }
  }

  if(ableToCreateRoom){
    roomSprite = createSprite(this.x, this.y, roomBoxSize, roomBoxSize);
    roomSprite.active = false;
    roomSprite.indexX = (this.x-floor(width/2)-roomBoxSize/2)/roomBoxSize;
    roomSprite.indexY = (this.y-floor(height/2)-roomBoxSize/2)/roomBoxSize;

    roomSprite.depth = 1;
    roomSprite.optionGuis = [];

    roomSprite.optionObjs = [];
    for (var i = 0; i < maxOptionAmount; i++) {
      roomSprite.optionObjs.push({option_text: '', option_command: '', values: [] })
    }


    roomSprite.coordinateString = "(" + roomSprite.indexX + ", " + roomSprite.indexY + ")";

    if (guiBackgroundHidden) {
      guiBackground.show();
      guiBackgroundHidden =false;

    }
    roomSprite.gui = QuickSettings.create(10, 10, "Room "+ roomSprite.coordinateString, guiParent);
    roomSprite.gui.setDraggable(false);
    roomSprite.gui.setWidth(250);
    roomSprite.gui.addTextArea('main_text', "");
    roomSprite.gui.addNumber('option_amount', 0,maxOptionAmount,0,1, function(value)
    {
      for (var i = 0; i < maxOptionAmount; i++) {
        roomSprite.optionGuis[i].hide()
      }
      for (var i = 0; i < value; i++) {
        roomSprite.optionGuis[i].show()
      }
    });
    roomSprite.gui.addButton('delete',function(){deleteRoom(); drawScene()});


    for (var i = 1; i <= maxOptionAmount; i++) {
      roomSprite.optionGui = QuickSettings.create( 210*i+60,10,'option_'+i, guiParent).hide();//borde parenta till en egen.
      roomSprite.optionGui.setDraggable(false);
      roomSprite.optionGui.addText('option_text', '' );
      roomSprite.optionGui.addText('option_command', ['','move', 'info']);
      roomSprite.optionGui.addHTML('command_description', 'test');

      for (var j = 0; j < maxCommandValues; j++) {
        roomSprite.optionGui.addText('command_value_'+j);
        roomSprite.optionGui.hideControl('command_value_'+j);
      }

      roomSprite.optionGuis.push(roomSprite.optionGui);
    }

    roomSprite.draw = function(){
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
      rect(0,0, roomBoxSize, roomBoxSize);
      textSize(15);
      noStroke();
      fill(strokeColor);
      text(this.coordinateString, -roomBoxSize/2+10,-roomBoxSize/2+25)

    }
    roomSprite.onMousePressed = function(){ //maybe this is retarded and should be called in mousePressed() instead
      if(!guiMouseIsOver){
        if(activeRoom){
          activeRoom.gui.hide();

        }
        // TODO: Måste kallas på fler ställen
        if (activeOptionGui!=null) {
          for (var i = 0; i < maxOptionAmount; i++) {
            activeOptionGui[i].hide();
          }
          for (var i = 0; i < this.gui.getValue('option_amount'); i++) {
            this.optionGuis[i].show();
          }
        }

        this.gui.show();
        activeRoom.active = false;
        activeRoom = this;
        activeRoom.active = true;
        activeOptionGui = this.optionGuis;
      }
    }
    roomSprite.debug = false;

    if(activeRoom){activeRoom.active = false;}
    activeRoom = roomSprite;
    activeRoom.active = true;
    activeRoomSpriteArray.push(roomSprite);
  }
}



// Hide all guis for alla areas
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

      // Do not hide rooms for test area
      if (spriteArrays.indexOf(spriteArray) != areaIndex) {
        sprite.depth = 0
        sprite.mouseActive = false
        sprite.visible = false
      }

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
            console.log("pop");
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
    print(createCoordinate);
    //creates a room and pushes it to the activeRoomSpriteArray
    createRoom(createCoordinate.x, createCoordinate.y);
    //hides all guis created
    activeRoomSpriteArray[i].gui.hide();
    for (var j = 0; j < activeRoomSpriteArray[i].optionGuis.length; j++) {
      activeRoomSpriteArray[i].optionGuis[j].hide();
    }
    //set values
    activeRoomSpriteArray[i].gui.setValue("main_text", this.inputRoomArray[i].mainText);
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
  print(activeRoom);
  print(activeRoomSpriteArray);
  for (var i = 0; i < activeRoomArray.length; i++) {
    if(activeRoomArray[i].x == activeRoom.indexX && activeRoomArray[i].y == activeRoom.indexY){
      print("presplice: "+activeRoomArray.length)
      activeRoomArray.splice(i,1);
      print("postsplice: "+activeRoomArray.length)
      break;
    }
  }
  for (var i = 0; i < activeRoomSpriteArray.length; i++) {
    if(activeRoomSpriteArray[i].indexX == activeRoom.indexX && activeRoomSpriteArray[i].indexY == activeRoom.indexY){
      activeRoom.gui.hide();
      for (var j = 0; j < activeRoom.optionGuis.length; j++) {
        activeRoom.optionGuis[j].hide();
      }
      activeRoom.remove();
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
      if(optionCommand == "move" || optionCommand == 'move-notBeenTo' || optionCommand == 'move-ifNotItem' || optionCommand == 'move-addBeenTo' || optionCommand == 'move-item' || optionCommand == 'move-background' || optionCommand == 'move-stat' || optionCommand == 'move-switchArea' || optionCommand == 'move-background-music' || optionCommand == 'move-ifItem' || optionCommand == 'move-ifStat' || optionCommand == 'move-item-ifStat' || optionCommand == 'move-stat-ifItem' || optionCommand == 'move-x' || optionCommand== 'move-y' ){
        stroke(connectionColors[z]);
        //ta koordinaterna från values delen
        let connection = indexToScreenCoordinates(
          Number(roomSprite.optionGuis[z].getValue('command_value_'+0)),
          Number(roomSprite.optionGuis[z].getValue('command_value_'+1)))

        if(connection.x!=null && connection.y != null){
          //stroke(color(255,255,255));
          line(roomSprite.position.x-10+z*4, roomSprite.position.y-10+z*4, connection.x-10+z*4, connection.y-10+z*4)
        }
      }
    }
  }
}

function createGeneralGui(){
  generalGui = QuickSettings.create( 0,0,'Settings', generalGuiParent);
  generalGui.setDraggable(false);
  generalGui.addButton('center (0,0)', function(){window.scrollTo(floor(width/2-window.innerWidth/2),floor(height/2-window.innerHeight/2));});
  generalGui.addDropDown('area' ,['test','intro', 'highlands', 'bog', 'city', 'mountain', 'core'], function(value){
      selectedArea = value.label;

      for (var i = 0; i < 7; i++) { //om det går borde man hämta antal värden i objektet istället för att hårdkoda 7
        if (i == value.index){

          for (var j= 0; j < activeRoomSpriteArray.length; j++) {
            activeRoomSpriteArray[j].depth = 0; //allt det här är dåligt gjort kommer finnas en massa sprites som är osynliga hela tiden
            activeRoomSpriteArray[j].mouseActive = false;
            activeRoomSpriteArray[j].visible = false;
          }
          activeArea = roomArrays[value.value];
          activeRoomArray = activeArea.rooms;
          activeRoomSpriteArray = activeArea.sprites;

          for (var j= 0; j < activeRoomSpriteArray.length; j++) {
            activeRoomSpriteArray[j].depth = 1;
            activeRoomSpriteArray[j].mouseActive = true;
            activeRoomSpriteArray[j].visible = true;
          }

        }
      }
  } );
  generalGui.addBoolean('show_all_connections',false, function(value){
    showAllConnections = value;
    drawScene()
  });
  generalGui.addBoolean('show_grid',true,  function(value){
    showGrid = value;
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
  })
  generalGui.addHTML('Available Commands', '<i>Type in option command to see full description.</i><br><br><b>move</b><br><b>move-y</b><br><b>move-x</b><br><b>info</b><br><b>move-item</b><br><b>encounter</b><br><b>move-item-switchArea</b><br><b>move-background</b><br><b>move-stat</b><br><b>move-switchArea</b><br><b>move-background-music</b><br><b>info-stat</b><br><b>info-item</b><br><b>move-ifItem</b><br><b>move-ifNotItem</b><br><b>move-ifStat</b><br><b>item-ifStat</b><br><b>move-item-ifStat</b><br><b>info-item-ifStat</b><br><b>info-ifStat</b><br><b>info-ifItem</b><br><b>move-notBeenTo</b><br><b>move-addBeenTo</b><br><b>gameover</b><br><b>endscreen</b>')
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

//kollar hur många values som ska visas för det aktiva rummet

function optionShowControl(){
  for (var i = 0; i < maxOptionAmount; i++) {
    activeRoom.optionGuis[i].hide();
  }
  for (var i = 0; i < activeRoom.gui.getValue('option_amount'); i++) {
    activeRoom.optionGuis[i].show();
  }
}

function showValueAmountControl(){
  //snuskigaste koden jag har skrivit på ett tag men den får va här tills jag fattar hur quicksettings divvarna går att ändra på.
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
          activeRoom.optionGuis[i].setValue('command_description', 'plays minigame with name (value 0), move to win room (value 1, value 2), move to game over room (value 3, value 4)')
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
          activeRoom.optionGuis[i].setValue('command_description', 'move to new coordinates (value 0, value 1) if player has not been to (value 2)')
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
          activeRoom.optionGuis[i].setValue('command_description', 'show endscreen for chapter int (value 0)')
          showAmount = 1;
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
