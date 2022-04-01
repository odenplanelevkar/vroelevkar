////////////////////////////////////////////
// HELPER FUNCTIONS
////////////////////////////////////////////

function hasCompleted(chapter) {
  let completedChapters = []

  if (player.completed.length == 0) {
    return false
  }

  for (let completed of player.completed) {
    completedChapters.push(completed.chapter)
  }

  if (completedChapters.includes(chapter)) {
    return true;
  } else {
    return false;
  }
}

function maxChapterCompleted() {
  if (hasCompleted(5)) {
    return 5;
  }
  else if (hasCompleted(4)) {
    return 4
  }
  else if (hasCompleted(3)) {
    return 3
  }
  else if (hasCompleted(2)) {
    return 2
  }
  else if (hasCompleted(1)) {
    return 1
  } else {
    return 0
  }
}

function startPlayer(x, y, newArea) {

  player.x = x;
  player.y = y;

  let newAssets = changeArea( newArea );

  paused = true;

  changeBoxColor()
  changeBackgroundImage(newAssets[0]);
  changeRoom( newArea, x, y);

  player.background = newAssets[0];
  player.music = newAssets[1];

  // Save player
  savePlayer();
}

function jumpCompletedChapter(chapter) {
  let startX = chapterCoordinates[chapter + 1]['start'][0];
  let startY = chapterCoordinates[chapter + 1]['start'][1]

  if (player.completed && hasCompleted(chapter) && player.x == startX && player.y == startY) {

    if (chapter == 2) {
      startPlayer(startX, startY, 'city');
    }
    else if (chapter == 3) {
      startPlayer(startX, startY, 'mountain');
    }
    else if (chapter == 4) {
      startPlayer(startX, startY, 'core');
    }
  }
}

function fixChapterRelease() {

  if ((player.completed.length == 1 && player.completed[0] == 1) || (player.completed.length == 2 && player.completed[0] == 1 && player.completed[1] == 1)) {
    let self = this;

    let completed = {
      chapter: 1,
      inventory: player.inventory,
      beenTo: player.beenTo,
      charisma: player.charisma,
      dexterity: player.dexterity,
      grit: player.grit,
      kindness: player.kindness,
    }

    player.completed = [];
    player.completed.push(completed);

    startPlayer(-1, 23, 'bog');
  }

  // Jump if completed chapter 1
  jumpCompletedChapter(1)

  //  Jump if completed chapter 2
  jumpCompletedChapter(2)

  //  Jump if completed chapter 3
  // jumpCompletedChapter(3)

}

function sendAjax( parameters, callbackFunction) {

  jQuery.ajax({
      url: '/wp-admin/admin-ajax.php',
      type: 'post',
      dataType: 'json',
      data: parameters,
      success: function(response){
        callbackFunction(response);
      }
  });

}


////////////////////////////////////////////
// SAVE PLAYER OPTIONS TO DATABASE
////////////////////////////////////////////
function savePlayer() {

  var playerString = JSON.stringify(player);
  parameters = {
    action: 'save_player',
    player_string: playerString
  }

  sendAjax(parameters, function(response) {
    // DEBUG console.log(response);
    return;
  });

}

function getPlayer() {

  parameters = {
    action: 'get_saved_player'
  }

  sendAjax(parameters, function(response) {

    // Translate the recieved player and set the new player
    if ( response.player != false ) {

      player = JSON.parse(response.player);

      // ADD COMPLETED TO OLD STRUCTURE: AND FIX BACKGROUND
      fixChapterRelease();

      changeBackgroundImage(player.background);
      currentArea = player.area;
      rooms = getRoomsFromArea( allAreaRooms, currentArea );
      currentRoom.unlockedOptions = getUnlockedOptions(currentRoom.options);
      // changeBackgroundImage( player.background );

      // $('#audio-holder').attr('src', player.music)

      resetTextbox();
      changeBoxColor();

      updateDebug();
      updateInventoryGui()
      updateBeenToGui();
      updateStatGui('', true);

      // Player fix
      // if (typeof(player.completed) == 'undefined') {
      //   player.completed = [];
      // }

      return;

      // rooms = getRoomsFromArea( allAreaRooms, currentArea );
      // currentRoom = findRoomWithPlayer();
      // console.log(currentRoom);
      // console.log(currentArea);
    }

  });

}

function clearPlayer() {

  parameters = {
    action: 'clear_player'
  }

  sendAjax(parameters, function(response) {
    console.log(response);

    location.reload();
    return false;
  });

}

////////////////////////////////////////////
// SAVE ROOMS OPTIONS TO DATABASE
////////////////////////////////////////////
function saveRoomsToDatabase(roomsToSave) {

  // console.log(roomsToSave);
  var roomsString = JSON.stringify(roomsToSave);
  parameters = {
    action: 'save_rooms',
    rooms_string: roomsString
  }

  sendAjax(parameters, function(response) {
    console.log(response);
  });

}

function saveSprites(spriteArrayToSave) {

  let spriteArray = spriteArrayToSave;

  for (var i = 0; i < spriteArray.length; i++) {
    let spriteGuis = {gui: "", optionGuis: []};
    spriteGuis.gui = spriteArray[i].gui.getValuesAsJSON(true);
    spriteArray[i].gui = spriteGuis.gui;

    spriteGuis.optionGuis = [];
    for (var j = 0; j < spriteArray[i].optionGuis.length; j++) {
      spriteGuis.optionGuis.push(spriteArray[i].optionGuis[j].getValuesAsJSON(true));
      spriteArray[i].optionGuis[j] = spriteGuis.optionGuis[j];
    }
  }
  let spriteString = JSON.stringify(spriteArray);

  // parameters = {
  //   action: 'save_sprites',
  //   rooms_string: spritesString
  // }
  //
  // sendAjax(parameters, function(response) {
  //   console.log(response);
  // })
}

function loadRoomsFromDatabase(area, callback){

    // sampleRooms = [
    //
    //   new Room(0, 0, 'You wake up on a sandy beach', [
    //     {
    //       text: 'Go right',
    //       cmd: 'move',
    //       values: [0, 1]
    //     },
    //     {
    //       text: 'Go left',
    //       cmd: 'tp',
    //       values: [0, 2]
    //     }
    //   ]),
    // ]  //slut på rooms arrayn

  // Get the rooms
  parameters = {
    action: 'fetch_rooms'
  }

  sendAjax(parameters, function(response) {

    allAreaRooms = []

    parsedRooms = JSON.parse(response.rooms);
    // console.log('DATABASE RESPONSE: ', parsedRooms);

    parsedRooms.forEach(roomArray => {
      rooms = []

      roomArray.forEach(room => {
        var newRoom = new Room(room.x, room.y, room.mainText, room.options);
        rooms.push( newRoom );
      });

      allAreaRooms.push(rooms);
    });

    callback(allAreaRooms);

  })

}

////////////////////////////////////////////
// GET CORRECT AREA
////////////////////////////////////////////
function getAreaRooms(allRooms, area) {
  switch (area) {

    case 'test':
      return allRooms.test.rooms

    case 'intro':
      return allRooms.intro.rooms

    case 'highlands':
      return allRooms.highlands.rooms

    case 'bog':
      return allRooms.bog.rooms

    case 'city':
      return allRooms.city.rooms

    case 'mountain':
      return allRooms.mountain.rooms

    case 'core':
      return allRooms.core.rooms

    default:
      return Array()

  }
}

function getAreaIndex( areaName ) {
  const areas = ['test', 'intro', 'highlands', 'bog', 'city', 'mountain', 'core'];
  return areas.indexOf(areaName);
}

function getAreaNameFromIndex( areaIndex ) {
  const areas = ['test', 'intro', 'highlands', 'bog', 'city', 'mountain', 'core'];
  return areas[areaIndex];
}

function getRoomsFromArea( rooms, area ) {
  switch (area) {

    case 'test':
      return rooms[0];
      break;

    case 'intro':
      return rooms[1];
      break;

    case 'highlands':
      return rooms[2];
      break;

    case 'bog':
      return rooms[3];
      break;

    case 'city':
      return rooms[4];
      break;

    case 'mountain':
      return rooms[5];
      break;

    case 'core':
      return rooms[6];
      break;

    default:
      return Array()

  }
}
