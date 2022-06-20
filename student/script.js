function selectDate(date,dateText,timeAvailable) {
  var items = document.getElementsByClassName("submission");
  var first = true;
  for ( var i = 0; i < items.length; i++ ) {
    if ( items[i].dataset.date == date ) {
      items[i].style.display = "block";
      //if ( first ) items[i].firstElementChild.style.display = "none";
      //else items[i].firstElementChild.style.display = "block";
      first = false;
    } else {
      items[i].style.display = "none";
    }
  }
  document.getElementById("dateField").innerText = "For " + dateText + ":";
}
