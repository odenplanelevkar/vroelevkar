
// Get the HTML element that displays the month and year
let weekLabel = document.getElementById('week-week');
weekLabel.innerText = 'Vecka ' + currentWeek;

function getMonday(d) {
  d = new Date(d);
  var day = d.getDay(),
      diff = d.getDate() - day + (day == 0 ? -6:1); // adjust when day is sunday
  return new Date(d.setDate(diff));
}


var currentDate = new Date();

// Set date forward if it is saturday or sunday
if (currentDate.getDay() == 6) {
  // Move forward 2 days
  currentDate = new Date(currentDate.getFullYear(),currentDate.getMonth(),currentDate.getDate()+2)
}
if (currentDate.getDay() == 0) {
  // Move forward 1 day
  currentDate = new Date(currentDate.getFullYear(),currentDate.getMonth(),currentDate.getDate()+1)
}

var displayDate = getMonday(currentDate);

// Show the calendar
showWeekCalendar();

function showWeekCalendar() {

  // Get the table HTML element that will hold all the days
  let tbl = document.getElementById('week-calendar-body');
  tbl.innerHTML = '';

  // Set the week to the current week
  weekLabel.innerText = 'Vecka ' + currentWeek;

  var dayNames = ['Måndag', 'Tisdag', 'Onsdag', 'Torsdag', 'Fredag'];

  for (var i = 0; i < 5; i++) {

    let row = document.createElement('tr');
    let cell = document.createElement('td');
    let weekName = document.createElement('p');
    var weekDate = document.createElement('span');

    weekName.innerText = dayNames[i];
    cell.appendChild(weekName);

    // Format month 1 --> 01
    let idMonth = displayDate.getMonth() + 1;
    idMonth = (idMonth < 10) ? '0' + idMonth : idMonth;

    // Format the date 4 --> 04
    let idDay = (displayDate.getDate() < 10) ? '0' + displayDate.getDate() : displayDate.getDate();

    // Set the id
    cell.id = displayDate.getFullYear() + '-' + idMonth + '-' + idDay;

    //Add events
    for (ev in allEvents) {
      add_event(ev, allEvents, cell);
    }

    // Add kommittéevents
    for (ev in kommitteEvents) {
      add_event(ev, kommitteEvents, cell, 'kommitte-event')
    }

    // Set the date
    weekDate.innerText = idDay;
    weekDate.classList = 'week-calendar-date';

    // Check if the day is the current one, then add class to style it differently
    if (displayDate.setHours(0,0,0,0) == new Date().setHours(0,0,0,0)){
      weekDate.classList.add('current_day');
    }

    cell.appendChild(weekDate)

    // Add to the table
    row.appendChild(cell);
    tbl.appendChild(row);

    // Increment displayDaye
    displayDate = new Date(displayDate.getFullYear(),displayDate.getMonth(),displayDate.getDate()+1);
  }

}

// Go back a week
function week_calendar_previous(){

  // If it is januari and we go back, change to december and go back one year
  currentWeek = (currentWeek == 1) ? 52 : currentWeek - 1;

  displayDate = getMonday(displayDate);
  displayDate = new Date(displayDate.getFullYear(),displayDate.getMonth(),displayDate.getDate()-7)

  // Update the calendar
  showWeekCalendar();
}

// Go forward a week
function week_calendar_next(){

  // If it is januari and we go back, change to december and go back one year
  currentWeek = (currentWeek == 52) ? 1 : currentWeek + 1;

  displayDate = getMonday(displayDate);
  displayDate = new Date(displayDate.getFullYear(),displayDate.getMonth(),displayDate.getDate()+7)

  // Update the calendar
  showWeekCalendar();
}
