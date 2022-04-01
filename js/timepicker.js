// FUNCTIONS

// make sure that the time is always double digits. Ex. 5 -> 05
function formatTime (time) {
  if (time < 10){
    time = '0' + time;
  }

  return time;
}

// Get current date
let d = new Date();

// Define a new class or "blueprint" for the timepicker so it can be reused for multiple
class Timepicker {

  // Set up the time picker
  constructor(timepicker_element, hidden_input, hr_element = hr_element, min_element, hr_up, hr_down, min_up, min_down){
    // bind all html elements that will be used
    this.timepicker_element = timepicker_element;
    this.hidden_input = hidden_input;
    this.hr_element = hr_element;
    this.min_element = min_element;
    this.hr_up = hr_up;
    this.hr_down = hr_down;
    this.min_up = min_up;
    this.min_down = min_down;

    // If you want the current time to display initialy uncomment these lines and comment out the next two
    // let this.hour = d.getHours();
    // let this.minute = d.getMinutes();
    this.hour = 12;
    this.minute = 0;

    // Display the initial time
    this.setTime();
  }

  // Update the time
  setTime () {
    // Format the time so it become double digits and fill the proper html elements
    this.hr_element.value = formatTime(this.hour);
    this.min_element.value = formatTime(this.minute);

    // Update its dataset for use in php and database insertion
    this.timepicker_element.dataset.time = formatTime(this.hour) + ':' + formatTime(this.minute);
    this.hidden_input.value = formatTime(this.hour) + ':' + formatTime(this.minute);
  }

  // Handle the hours when user types in a value
  hour_change(e) {
    // Hour cannot be more than 23, so set a max to 23
    if (e.target.value > 23) {
      e.target.value = 23;

    // Hour cannot be less than 0 so the min is 0
    } else if (e.target.value < 0){
      e.target.value = '00';
    }

    // If it is empty, set the hour back to the last accepted value
    if (e.target.value == '' ) {
      e.target.value = formatTime(this.hour);
    }

    // Update the hour
    this.hour = e.target.value;
  }

  // Handle the minutes when user types in a value
  minute_change(e) {
    // Minute cannot be more than 59, so set a max to 59
    if (e.target.value > 59) {
      e.target.value = 59;

    // minute cannot be less than 0 so the min is 0
    } else if (e.target.value < 0){
      e.target.value = '00';
    }

    // If it is empty, set the minute back to the last accepted value
    if (e.target.value == '' ) {
      e.target.value = formatTime(this.minute);
    }

    // Update the minutes
    this.minute = e.target.value;
  }

  // Increse the hour
  hour_up() {
    this.hour++;

    // If 23 if reached, wraparound to 0
    if (this.hour > 23) {
      this.hour = 0;
    }

    // Update the time
    this.setTime();
  }

  // Decrease the hour
  hour_down() {
    this.hour--;

    // Cannot be less than 0 hours, so do a wraparound to 23
    if (this.hour < 0) {
      this.hour = 23;
    }

    // Update the time
    this.setTime();
  }

  // Increase the minute
  minute_up() {
    this.minute++;

    // If 59 minutes are reached, add an hour and go back to 0 minutes
    if (this.minute > 59) {
      this.minute = 0;
      this.hour_up();
    }

    // Update the time
    this.setTime();
  }

  // Decrease the minutes
  minute_down() {
    this.minute--;

    // If minutes go below 0, do a wraparound to 59 and decrease the hour by one
    if (this.minute < 0) {
      this.minute = 59;
      this.hour_down();
    }

    // Update the time
    this.setTime();
  }
}

// START TIMEPICKER

// Get all elements, see HTML
const start_timepicker_element = document.querySelector('#start-timepicker.timepicker');
const start_time_hidden_input = document.getElementById('start_time_hidden_input');

const start_hr_element = document.querySelector('#start-timepicker.timepicker .hour .hr');
const start_min_element = document.querySelector('#start-timepicker.timepicker .minute .min');

const start_hr_up = document.querySelector('#start-timepicker.timepicker .hour .hr-up');
const start_hr_down = document.querySelector('#start-timepicker.timepicker .hour .hr-down');

const start_min_up = document.querySelector('#start-timepicker.timepicker .minute .min-up');
const start_min_down = document.querySelector('#start-timepicker.timepicker .minute .min-down');

// Create a new Timepicker object and pass the relevant html elements
let start_timepicker = new Timepicker(start_timepicker_element, start_time_hidden_input, start_hr_element, start_min_element, start_hr_up, start_hr_down, start_min_up, start_min_down);

// EVENT LISTENERS

// Make sure the correct functions are fired when the different buttons are pressed
start_timepicker.hr_up.addEventListener('click', function(e) {
  start_timepicker.hour_up(e);
});
start_timepicker.hr_down.addEventListener('click', function(e) {
  start_timepicker.hour_down(e);
});

start_timepicker.min_up.addEventListener('click', function(e) {
  start_timepicker.minute_up(e);
});
start_timepicker.min_down.addEventListener('click', function(e) {
  start_timepicker.minute_down(e);
});

// Make sure the correct functions are fired when the user types in a value
start_timepicker.hr_element.addEventListener('change', function(e) {
  start_timepicker.hour_change(e);
});
start_timepicker.min_element.addEventListener('change', function(e) {
  start_timepicker.minute_change(e);
});

// END TIMEPICKER

// Get all elements, see HTML
const end_timepicker_element = document.querySelector('#end-timepicker.timepicker');
const end_time_hidden_input = document.getElementById('end_time_hidden_input');

const end_hr_element = document.querySelector('#end-timepicker.timepicker .hour .hr');
const end_min_element = document.querySelector('#end-timepicker.timepicker .minute .min');

const end_hr_up = document.querySelector('#end-timepicker.timepicker .hour .hr-up');
const end_hr_down = document.querySelector('#end-timepicker.timepicker .hour .hr-down');

const end_min_up = document.querySelector('#end-timepicker.timepicker .minute .min-up');
const end_min_down = document.querySelector('#end-timepicker.timepicker .minute .min-down');

// Create a new Timepicker object and pass the relevant html elements
let end_timepicker = new Timepicker(end_timepicker_element, end_time_hidden_input, end_hr_element, end_min_element, end_hr_up, end_hr_down, end_min_up, end_min_down);

// EVENT LISTENERS

// Make sure the correct functions are fired when the different buttons are pressed
end_timepicker.hr_up.addEventListener('click', function(e) {
  end_timepicker.hour_up(e);
});
end_timepicker.hr_down.addEventListener('click', function(e) {
  end_timepicker.hour_down(e);
});

end_timepicker.min_up.addEventListener('click', function(e) {
  end_timepicker.minute_up(e);
});
end_timepicker.min_down.addEventListener('click', function(e) {
  end_timepicker.minute_down(e);
});

// Make sure the correct functions are fired when the user types in a value
end_timepicker.hr_element.addEventListener('change', function(e) {
  end_timepicker.hour_change(e);
});
end_timepicker.min_element.addEventListener('change', function(e) {
  end_timepicker.minute_change(e);
});
