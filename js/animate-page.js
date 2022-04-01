function scrollAppear(elementClass) {
  var elementToChange = '.' + elementClass
  var introText = document.querySelector(elementToChange);
  var introPosition = introText.getBoundingClientRect().top;
  var screenPosition = window.innerHeight / 1.3;

  if (introPosition < screenPosition) {
    introText.classList.add('appear');
  }
}

function scrollAppearAll(elementClass) {
  var elementsToChange = '.' + elementClass
  var elements = document.querySelectorAll(elementsToChange);
  var screenPosition = window.innerHeight / 1.2;

  elements.forEach(element => {
    var elementPosition = element.getBoundingClientRect().top;

    if (elementPosition < screenPosition) {
      element.classList.add('appear');
    }
  })

}

function fillNavigationBar(headerId) {
  var navbar = document.getElementById('front-navbar');
  var header = document.getElementById(headerId);
  var headerPosition = header.getBoundingClientRect().bottom;

  if (headerPosition <= 30){
    if (!navbar.classList.contains('filled')){
      navbar.classList.add('filled');
    }
  } else {
    if (navbar.classList.contains('filled')){
      navbar.classList.remove('filled');
    }
  }

}
