// Create a function to get the current week
Date.prototype.getWeek = function() {
        var onejan = new Date(this.getFullYear(), 0, 1);
        var week = Math.ceil((((this - onejan) / 86400000) + onejan.getDay() + 1) / 7);

        // Do a wraparound to 1 when 53 is reached
        if (week == 53){
          return 1;
        } else {
          return week;
        }

        return week;
    }

  var getDaysArray = function(start, end) {
      for(var arr=[],dt=start; dt<=end; dt.setDate(dt.getDate()+1)){
          arr.push(new Date(dt).toISOString().slice(0,10));
      }
      return arr;
  };

// Get the curent date, month, year and week
let today = (new Date());
let currentMonth = today.getMonth();
let currentYear = today.getFullYear();
let currentWeek = today.getWeek();

// Define the swedish month names
let months = ['Januari', 'Februari', 'Mars', 'April', 'Maj', 'Juni', 'Juli', 'Augusti', 'September', 'Oktober', 'November', 'December'];

// Get the HTML element that displays the month and year
let monthAndYear = document.getElementById('monthAndYear');

// Show the calendar
showCalendar(currentMonth, currentYear);

function showCalendar(month, year){


  // Get the weekday that the month starts on
  let firstDay = new Date(year, month).getDay();

  // Get the total number of days in the month
  let daysInMonth = 32 - new Date(year, month, 32).getDate();

  // Get the table HTML element that will hold all the days
  let tbl = document.getElementById('calendar-body');

  // Always begin with a clean slate
  tbl.innerHTML = '';

  // Format the calendar header to display ex. December 2019
  monthAndYear.innerHTML = months[month] + ' ' + year;

  // Start on day 1
  let date = 1;

  // Do maximum 7 rows
  for(let i = 0; i < 7; i++){

    // Create a new row
    let row = document.createElement('tr');

    // Do the 7 weekdays
    for(let j = -1; j < 7; j++){

      if (i == 0 && j == 0){
        if (firstDay == 0) {
          date += 1;
        }

        if (firstDay == 6) {
          date += 2;
        }
      }

      // Create a new place for the day in the table
      let cell = document.createElement('td');

      // If we have overshooted the maximum days in the current month, stop creating days
      if (date > daysInMonth){
          break;
      }

      // Add week
      if (j === -1){
        // The first column is -1

        // Create a new text to hold the week numbers
        let cellText = document.createElement('p');

        // Get the week of the current row
        let rowWeek = (new Date(year, month, date)).getWeek();

        // Set the week text to the row week
        cellText.innerText = rowWeek;

        // It is the current week, give the current week text a class to style with css
        if (rowWeek === currentWeek && year == currentYear){
          cellText.classList = 'current';
        }

        // Append the week to the table
        cell.appendChild(cellText);
        row.appendChild(cell);
        continue;
      }

      // Do not display sundays and saturdays
      if (j == 5 || j == 6){
        date++;
        continue;
      }

      // Display empty boxes if the first day is not a monday
      if(i === 0 && j < firstDay - 1 && firstDay != 6 && firstDay != 0){
        let cellText = document.createElement('p');
        cellText.innerText = '';
        cell.appendChild(cellText);
        row.appendChild(cell);
        continue;

      }
      else {

        // Create a new text element to hold the day
        let cellText = document.createElement('p');

        // Set the day
        cellText.innerText = date;

        // Check if it is the current day
        if (today.getFullYear() === year && today.getMonth() == month && today.getDate() == date) {
          // Add class to enable styling in css
          cellText.classList = 'current_day';
        }

        // Add a unique id to the cell
        let idDate = (date < 10) ? '0' + date : date;

        let idMonth = month + 1;
        idMonth = (idMonth < 10) ? '0' + idMonth : idMonth;

        cell.id = year + '-' + (idMonth) + '-' + idDate;

        // Add the day to the calendar
        cell.appendChild(cellText);

        // Check if there should be any events for this day

        // Go through all events
        for (ev in allEvents) {

          add_event(ev, allEvents, cell)

        }

        // Go through all events
        for (ev in kommitteEvents) {

          add_event(ev, kommitteEvents, cell, 'kommitte-event')

        }

        row.appendChild(cell);
      }

      // Go to the next day
      date++;
    }

    // Add the whole row of days
    tbl.appendChild(row);
  }

}

// Add event
function add_event( ev, allEvents, cell, eventTypeClass = 'elevkaren-event' ){

  // Get their start date in the format year-month-day to match with the this days date
  let startDatetimeArray = allEvents[ev]['start'].split(' ');
  let startDateString = startDatetimeArray[0];
  let startTimeString = startDatetimeArray[1];
  startTimeString = startTimeString.substring(0, startTimeString.length - 3);

  let endDatetimeArray = allEvents[ev]['end'].split(' ');
  let endDateString = endDatetimeArray[0];
  let endTimeString = endDatetimeArray[1];
  endTimeString = endTimeString.substring(0, endTimeString.length - 3);

  let allDates = getDaysArray(new Date(startDateString), new Date(endDateString));

  // If they do match, this event should be shown on this day
  if (allDates.includes(cell.id)) {
    // Get the type of event
    let etId = allEvents[ev]['type'];
    // console.log(allEvents[ev]);

    // Check which type the event belongs to
    for (evType in allEventTypes){
      // If an event type is found, add the event to the calendar with the correct color codes
      if (allEventTypes[evType]['id'] == etId){
        add_event_to_calendar(cell, allEvents[ev]['name'], allEventTypes[evType]['bg_color'], allEventTypes[evType]['fg_color'], startDateString, endDateString, startTimeString, endTimeString, allEvents[ev]['place'], allEvents[ev]['host'], allEvents[ev]['description'], allEvents[ev]['visibility'], allEvents[ev]['id'], eventTypeClass);
      }
    }


  }

}

// Go back a month
function calendar_previous(){
  // If it is januari and we go back, change to december and go back one year
  currentYear = (currentMonth === 0) ? currentYear - 1 : currentYear;
  currentMonth = currentMonth === 0 ? 11: currentMonth - 1;

  // Update the calendar
  showCalendar(currentMonth, currentYear);
}

// Go forward a month
function calendar_next(){
  // If it is december and we go forward, change to januari and go forward one year
  currentYear = (currentMonth === 11) ? currentYear + 1 : currentYear;
  currentMonth = (currentMonth + 1) % 12;

  // Update the calendar
  showCalendar(currentMonth, currentYear);
}

function add_event_to_calendar(tdElement, text, bgColor, fgColor, startDate, endDate, startTime, endTime, place, host, description, visibility, id, eventTypeClass){
  // Check if there already is an event on this day, if so -> the markingsContainer has already been created, therefore do not create it again.
  let markingsContainer = (tdElement.childNodes.length == 1) ? document.createElement('div') : tdElement.childNodes[1];

  // Add the correct class for styling if it does not exist
  if (!markingsContainer.classList.contains('markings')){
    markingsContainer.classList = 'markings';
  }

  // Create a new div element to hold the text for the event
  let newEvent = document.createElement('div');
  newEvent.classList.add(eventTypeClass)
  newEvent.textContent = text;

  // Style it
  newEvent.style.backgroundColor = bgColor;
  newEvent.style.color = fgColor;

  // If the visibility is not for all, add a class
  if (visibility != 'a'){
    newEvent.style.opacity = '60%';
    newEvent.style.border = '4px dotted gray';
    // newEvent.style.textDecoration = 'line-through';
  }

  // Create a popup box if the event is clicked
  newEvent.addEventListener('click', function() {
    // Get the modal
    let modal = document.querySelector('#modal');

    // Change the modal header
    document.querySelector('#modal .modal-header .title').textContent = this.textContent;

    let modalText = '';

    if (startDate == endDate){
      modalText += '<b>Tid:</b> ' + startTime + ' - ' + endTime + '<br>';
    } else {
      modalText += '<b>Tid:</b> ' + startTime + ' <i>' + startDate.replace(/-/g, '/') + '</i> - ' + endTime + ' <i>' + endDate.replace(/-/g, '/') + '</i></br>';
    }

    if (place){
      modalText += '<b>Plats:</b> ' + place + '<br>';
    }
    if (host){
      modalText += '<b>Host:</b> ' + host + '<br>';
    }
    if (description){
      modalText += '<span class="modal-description">' + description + '</span><br>';
    }
    // Add publish/unpublish buttons depending if the event is only accesible to elevkaren or all members
    if (isAdmin){
      if (visibility != 'a'){
        modalText += '<form method="post" action="'+ actionLink +'"><div class="button-group"><button class="btn lg" type="submit" name="publish_event" value='+ id +'>Publicera event</button><button class="btn lg red" type="submit" name="remove_event" onclick="return confirm(\'Är du säker på att du vill publicera detta event?\');" value='+ id +'>-</button></div></form>';
      } else {
        modalText += '<form method="post" action="'+ actionLink +'"><div class="button-group"><button class="btn lg" type="submit" name="unpublish_event" value='+ id +'>Avpublicera event</button><button class="btn lg red" type="submit" name="remove_event" onclick="return confirm(\'Är du säker på att du vill avpublicera detta event?\');" value='+ id +'>-</button></div></form>';
      }
    }


    // Add a remove button
    modalText += '';


    // Add the body text
    document.querySelector('#modal .modal-body').innerHTML = modalText;

    // OPen the modal
    openModal(modal);
  })

  // Add event to main event container
  markingsContainer.appendChild(newEvent);

  // Add the event to the day
  tdElement.appendChild(markingsContainer);

}
