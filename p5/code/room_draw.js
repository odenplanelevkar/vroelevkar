
roomSprites = [];
var scaleFactor;

function setup(){
  scaleFactor = 100;
  createCanvas(2000,2000);

  loadRoomsFromDatabase();

  //createRoomSprites();

}

function draw(){
  translate(floor(width/2), floor(height/2)); //lägger 0,0 i mitten

  background(51);
  drawSprites();
  vizualizeRooms();

  //fill(255);

}


function vizualizeRooms(){
  for (var i = 0; i < rooms.length; i++) {
    rooms[i].vizualize();
  }
  for (var i = 0; i < rooms.length; i++) {
    rooms[i].drawConnections();
  }
}








function room( x, y, mainText, options ){

  // Set default values
  this.x = x;
  this.y = y;
  this.mainText = mainText;
  this.options = options;

  this.load = function(){

    // Reset the displayed options
    for (var i = 0; i < maxOptionLength; i++) {
      displayedOptions[i].ref.html('');
    }

    // Get the number of displayedOptions
    optionlength = this.options.length;

    // Set the option variables for use in front ends display options
    for (var i = 0; i < optionlength; i++) {
      // Set the displayed text on option
      displayedOptions[i].ref.html(this.options[i].text);

      displayedOptions[i].cmd = this.options[i].cmd;

      displayedOptions[i].values = this.options[i].values;

    }

  }

  this.vizualize = function(){
    this.drawX = this.x*scaleFactor; //borde translatea hela koordinatsystemet istället
    this.drawY = this.y*scaleFactor;
    this.textSize = 12;


    //ritar rektangeln
    noFill(51);
    stroke(255);
    rectMode(CENTER);
    rect(this.drawX,this.drawY,scaleFactor, scaleFactor);

    noStroke();
    fill(255);
    textSize(this.textSize);
    this.displayText = "("+this.x+","+this.y+")";
    text(this.displayText, this.drawX-scaleFactor/2, this.drawY+this.textSize-scaleFactor/2);

  }

  this.drawConnections = function(){
    this.connectionColors = [color(255, 0, 0), color(255, 255, 0), color(0, 255, 0), color(0, 255, 255), color(0, 0, 255)];
    print(this.options.length);
    for (var i = 0; i < this.options.length; i++) {
      stroke(this.connectionColors[i]);
      print(i);
      this.connectionX =  this.options[i].values[0]*scaleFactor;
      this.connectionY =  this.options[i].values[1]*scaleFactor;

      line(this.drawX+4*i*floor(scaleFactor/50)-6*floor(scaleFactor/50), this.drawY+4*i*floor(scaleFactor/50)-6*floor(scaleFactor/50), this.connectionX+4*i*floor(scaleFactor/50)-6*floor(scaleFactor/50), this.connectionY+4*i*floor(scaleFactor/50)-6*floor(scaleFactor/50));

    }
  }
}
