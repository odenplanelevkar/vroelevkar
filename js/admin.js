
// Set the correct li in nav to be highlighted
function highlightLink(linkId) {
  // Get the root to all links
  navRoot = document.getElementById('navbar-nav');

  // Get all links
  var links = navRoot.children;

  // Loop through every link
  for (var i = 0; i < links.length; i++) {
    var link = links[i];

    // If the link is highlighted, dehighlight it
    if (link.classList.contains('active')) {
      link.classList.remove('active');
    }
  }

  // Highlight specified link
  document.getElementById(linkId).classList.add('active');
}

function showAnswerForm( formId, onStart = null, resetUrl = null ){
  answerDiv = document.getElementById(formId);

  // Check if display property is empty, if so set it a default of none
  // if (answerDiv.style.display != 'none' && answerDiv.style.display != 'block') {
  //   answerDiv.style.display = 'none';
  // }

  // Toggle visibility
  // answerDiv.style.display = (answerDiv.style.display == 'none') ? 'block' : 'none';
  answerDiv.style.opacity = (answerDiv.style.opacity == 0 ? 1 : 0);
  answerDiv.style.width = (answerDiv.style.width == 0 || answerDiv.style.width == '0px') ? '100%' : '0';
  answerDiv.style.height = (answerDiv.style.height == 0 || answerDiv.style.height == '0px') ? '100%' : '0';

  // CHECK if callback function has been defined
  if (onStart){
    // Only run when everything has loaded
    document.addEventListener('DOMContentLoaded', function() {
       onStart();
    }, false);
  }

  if (resetUrl) {
    window.history.pushState({}, document.title, "/" + resetUrl);
  }
}

function checkPlural( id ){
  var element = document.getElementById(id);

  if (element.innerText == '1'){
    element.styleList = 'singular'
  }
}

function toggleClass(id, classOn, classOff){
  var element = document.getElementById(id);
  element.classList = (element.classList.contains(classOn)) ? classOff : classOn;
}

// Fill program name from class name

function fillProgramName( classNameInputId, programNameInputId ) {

  var classInput = document.getElementById(classNameInputId);
  var programInput = document.getElementById(programNameInputId);

  var classname = classInput.value;
  var classPrefix = classname.substring(0,2).toLowerCase();

  let programName = '';

  if (classPrefix == 'na') {
    programName = 'Naturvetenskapsprogrammet';
  }
  else if (classPrefix == 'ek') {
    programName = 'Ekonomiprogrammet';
  }
  else if (classPrefix == 'sb') {
    programName = 'Samhällsvetenskapsprogrammet';
  }

  if (programName != '') {
    programInput.value = programName;
  }

}

function scrollToElement( elementId ) {
  $(document).ready(function () {
    // Handler for .ready() called.
    let scrollString = '#' + elementId

    $('html, body').animate({
        scrollTop: $(scrollString).offset().top
    }, 'slow');
  });
}
