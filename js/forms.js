function checkForm(form, textElement, limit){
  let formText = form.value;
  let trimmedText = formText.substring(0, limit);
  form.value = trimmedText;

  if (textElement) {
    textElement.innerText = limit - trimmedText.length;
  }

}

function clickElement(id){
  document.getElementById(id).click();
}
