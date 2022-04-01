// Have all swedish month names declared
const dp_months = ['Januari', 'Februari', 'Mars', 'April', 'Maj', 'Juni', 'Juli', 'Augusti', 'September', 'Oktober', 'November', 'December'];

// HELPER FUNCTIONS

// Check if the clicked element is contained in some element that has the specified selector
function checkEventPathForClass (path, selector) {
  for (let i = 0; i < path.length; i++){
    if (path[i].classList && path[i].classList.contains(selector)) {
      return true;
    }
  }
  return false;
}

// Always make sure the date is double digits. Ex. 5 -> 05
function formatDate(d, sql){
  let day = d.getDate();
  if (day < 10){
    day = '0' + day;
  }

  let month = d.getMonth() + 1;
  if (month < 10){
    month = '0' + month;
  }

  let year = d.getFullYear();

  // Return a formatted date such as 28 / 12 / 2019 for the 28th december 2019 (easter-egg: this file was created this date, hello future!)
  if (!sql) {
    return day + ' / ' + month + ' / ' + year;
  } else {
    return year + '-' + month + '-' + day;
  }
}

// Create a new DAtepicker class so multiple timepickers can be created with the same functionalitity
class Datepicker {

  // Setup the datepicker
  constructor(date_picker_element, hidden_input, selected_date_element, dates_element, mth_element, next_mth_element, prev_mth_element, days_element) {
    // Get all relevant html elements
    this.date_picker_element = date_picker_element;
    this.hidden_input = hidden_input;
    this.selected_date_element = selected_date_element;
    this.dates_element = dates_element;
    this.mth_element = mth_element;
    this.next_mth_element = next_mth_element;
    this.prev_mth_element = prev_mth_element;
    this.days_element = days_element;

    // Get the current date, day, month and year
    this.date = new Date();
    this.day = this.date.getDate();
    this.month = this.date.getMonth();
    this.year = this.date.getFullYear();

    // Selecteddate is the date shown in the box, start by being the current date as initialized above
    this.selectedDate = this.date;
    this.selectedDay = this.day;
    this.selectedMonth = this.month;
    this.selectedYear = this.year;

    // Display the current date above the datepicker expandable calendar formatted ex. December 2019
    this.mth_element.textContent = dp_months[this.month] + ' ' + this.year;

    // Display the formatted current date in the datepicker box
    this.selected_date_element.textContent = formatDate(this.date, false);

    // Set the dataset value to the current value to be used in php and database insertion
    this.selected_date_element.dataset.value = this.selectedDate;
    this.hidden_input.value = formatDate(this.selectedDate, true);

    // Create the dropdown calendar
    this.populateDates();
  }

  // Toggle the dropdown calendar
  toggleDatePicker(e) {
    if (!checkEventPathForClass(e.path, 'dates')){
      this.dates_element.classList.toggle('active');
    }
  }

  // Create the drowdown calendar
  populateDates(){
    // Start with a fresh calendar every time the month is changed
    this.days_element.innerHTML = '';

    // Get the amount of days in the month
    let days_in_month = 32 - new Date(this.year, this.month, 32).getDate();

    // Create a new day element for every day in the month
    for (let i = 0; i < days_in_month; i++){
      // Create a new element
      const day_element = document.createElement('div');

      // Add class for styling in css
      day_element.classList.add('day');

      // Set the day text, remember i starts at 0 so 1 must be added
      day_element.textContent = i + 1;

      // Check if this day is the current selected date, if so -> apply a class to this day for styling in css
      if (this.selectedDay == (i + 1 )&& this.selectedYear == this.year && this.selectedMonth == this.month){
        day_element.classList.add('selected')
      }

      // _this refers to the object of this class because in the event listener below, this will refer to the current day_element instead of the whole class.
      var _this = this;
      day_element.addEventListener('click', function(e) {
        // Update the selected information with _this to refer to the whole class instead of the current day
        _this.selectedDate = new Date(_this.year + '-' + (_this.month + 1) + '-' + (i + 1));
        _this.selectedDay = (i + 1);
        _this.selectedMonth = _this.month;
        _this.selectedYear = _this.year;

        // Update the text
        _this.selected_date_element.textContent = formatDate(_this.selectedDate, false);

        // Update the values of elements to be used in database insertion
        _this.selected_date_element.dataset.value = _this.selectedDate;
        _this.hidden_input.value = formatDate(_this.selectedDate, true);

        // Call this function again to uodate what day is selected
        _this.populateDates();
      });

      // Add the day to the HTML
      this.days_element.appendChild(day_element);
    }
  }

  // Go to the next month
  goToNextMonth(e) {
    // Add one to the mont
    this.month++;

    // If last month was december, change to januari next year
    if (this.month > 11){
      this.month = 0;
      this.year++;
    }

    // Format the month and year as ex. Januari 2020
    this.mth_element.textContent = dp_months[this.month] + ' ' + this.year;

    // Update the dropdown calendar
    this.populateDates();
  }

  goToPrevMonth(e) {
    // Decrease the month by one
    this.month--;

    // If last month was januari, change to december last year
    if (this.month < 0){
      this.month = 11;
      this.year--;
    }

    // Format the month and year as ex. Januari 2020
    this.mth_element.textContent = dp_months[this.month] + ' ' + this.year;

    // Update the dropdown calendar
    this.populateDates();
  }

}

// START DATEPICKER

// Get all elements, see HTML
const start_date_picker_element = document.querySelector('#start-datepicker.date-picker');
const start_hidden_input = document.getElementById('start_hidden_input');
const start_selected_date_element = document.querySelector('#start-datepicker.date-picker .selected-date');
const start_dates_element = document.querySelector('#start-datepicker.date-picker .dates');
const start_mth_element = document.querySelector('#start-datepicker.date-picker .dates .month .mth');
const start_next_mth_element = document.querySelector('#start-datepicker.date-picker .dates .month .next-mth');
const start_prev_mth_element = document.querySelector('#start-datepicker.date-picker .dates .month .prev-mth');
const start_days_element = document.querySelector('#start-datepicker.date-picker .dates .days');

// Create a new Datepicker object and pass the relevant html elements
let start_datepicker = new Datepicker(start_date_picker_element, start_hidden_input, start_selected_date_element, start_dates_element, start_mth_element, start_next_mth_element, start_prev_mth_element, start_days_element);

// EVENT LISTENERS

// Make sure the correct functions are fired when the different buttons are pressed

// The startdate box clicked to toggle the dropdown calendar
start_datepicker.date_picker_element.addEventListener('click', function(e) {
  start_datepicker.toggleDatePicker(e);
})

// Right arrow clicked
start_next_mth_element.addEventListener('click', function(e) {
  start_datepicker.goToNextMonth(e);
});

// Left arrow clicked
start_prev_mth_element.addEventListener('click', function(e){
  start_datepicker.goToPrevMonth(e);
});


// END DATEPICKER

// Get all elements, see HTML
const end_date_picker_element = document.querySelector('#end-datepicker.date-picker');
const end_hidden_input = document.getElementById('end_hidden_input');
const end_selected_date_element = document.querySelector('#end-datepicker.date-picker .selected-date');
const end_dates_element = document.querySelector('#end-datepicker.date-picker .dates');
const end_mth_element = document.querySelector('#end-datepicker.date-picker .dates .month .mth');
const end_next_mth_element = document.querySelector('#end-datepicker.date-picker .dates .month .next-mth');
const end_prev_mth_element = document.querySelector('#end-datepicker.date-picker .dates .month .prev-mth');
const end_days_element = document.querySelector('#end-datepicker.date-picker .dates .days');

// Create a new Datepicker object and pass the relevant html elements
let end_datepicker = new Datepicker(end_date_picker_element, end_hidden_input, end_selected_date_element, end_dates_element, end_mth_element, end_next_mth_element, end_prev_mth_element, end_days_element);

// EVENT LISTENERS

// Make sure the correct functions are fired when the different buttons are pressed

// The startdate box clicked to toggle the dropdown calendar
end_datepicker.date_picker_element.addEventListener('click', function(e) {
  end_datepicker.toggleDatePicker(e);
})

// Right arrow clicked
end_next_mth_element.addEventListener('click', function(e) {
  end_datepicker.goToNextMonth(e);
});

// Left arrow clicked
end_prev_mth_element.addEventListener('click', function(e){
  end_datepicker.goToPrevMonth(e);
});
