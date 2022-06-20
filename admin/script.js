var movingID = -1;
var movingButton;
var movingApproved;
var pwd = location.search.split("=")[1];

function selectDate(date,dateText,timeAvailable) {
  if ( movingID != -1 ) {
    var originalDate = movingButton.parentElement.parentElement.dataset.date;
    if ( date == originalDate ) {
      movingButton.parentElement.parentElement.getElementsByClassName("move-text")[0].innerText = "Please select a new date.";
      return;
    }
    var timeUsed = parseInt(movingButton.parentElement.parentElement.dataset.time);
    if ( timeUsed > timeAvailable ) {
      movingButton.parentElement.parentElement.getElementsByClassName("move-text")[0].innerText = "There is not enough time on that date for this submission.";
      return;
    }
    if ( ! movingApproved ) respondToRequest(movingID,true,movingButton,date,dateText,timeAvailable);
    else sendUpdate("move",movingID,movingButton,date,dateText,timeAvailable);
    movingID = -1;
    return;
  }

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

function respondToRequest(id,approved,button,moveTo,moveToText,moveToAvailable) {
  var req = new XMLHttpRequest();
  var url = "respond.php?pwd=" + pwd + "&id=" + id + "&approved=" + approved;
  if ( moveTo ) url += "&moveTo=" + moveTo;
  if ( ! approved ) url += "&info=" + button.parentElement.getElementsByClassName("reject-info")[0].value.split("\n").join("<br />");
  var item = button.parentElement.parentElement;
  if ( approved ) {
    if ( ! moveTo ) {
      item.getElementsByClassName("approve-text")[0].innerText = "Sending approval email...";
    } else {
      var moveText = item.getElementsByClassName("move-text")[0];
      moveText.innerText = "Sending approval email...";
      moveText.style.textDecoration = "none";
    }
  } else {
    item.getElementsByClassName("reject-text")[0].innerText = "Sending rejection email...";
  }
  req.open("GET",url);
  req.onload = function() {
    var resp = this.responseText;
    console.log(resp);
    if ( resp == "ok" ) {
      if ( approved ) {
        if ( ! moveTo ) item.getElementsByClassName("approve-text")[0].innerText = "Approval email sent.";
        else moveText.innerText = "Approval email sent.";
        setTimeout(function() {
          item.firstElementChild.innerText = "Approved request";
          item.firstElementChild.classList.remove("red");
          item.firstElementChild.classList.remove("bold");
          button.parentElement.style.display = "none";
          item.getElementsByClassName("update-panel")[0].style.display = "block";
          if ( moveTo ) {
            item.getElementsByClassName("move-panel")[0].style.display = "none";
            item.dataset.date = moveTo;
            item.style.display = "none";
            selectDate(moveTo,moveToText,moveToAvailable);
          }
          rewriteDates();
        },2000);
      } else {
        item.getElementsByClassName("reject-text")[0].innerText = "Rejection email sent.";
        setTimeout(function() {
          item.parentElement.removeChild(item);
          rewriteDates();
        },2000);
      }
    } else {
      alert("Error: " + resp);
    }
  }
  req.send();
}

function sendUpdate(type,id,button,moveTo,moveToText,moveToAvailable) {
  var req = new XMLHttpRequest();
  var url = "update.php?pwd=" + pwd + "&type=" + type + "&id=" + id;
  if ( moveTo ) url += "&moveTo=" + moveTo;
  var item = button.parentElement.parentElement;
  if ( type == "delete" ) {
    item.getElementsByClassName("delete-text")[0].innerText = "Sending deletion email...";
  } else if ( type == "move" ) {
    var moveText = item.getElementsByClassName("move-text")[0];
    moveText.innerText = "Sending move email...";
    moveText.style.textDecoration = "none";
  }
  req.open("GET",url);
  req.onload = function() {
    var resp = this.responseText;
    console.log(resp);
    if ( resp == "ok" ) {
      if ( type == "delete" ) {
        item.getElementsByClassName("delete-text")[0].innerText = "Deletion email sent.";
        setTimeout(function() {
          item.parentElement.removeChild(item);
          rewriteDates();
        },2000);
      } else if ( type == "move" ) {
        moveText.innerText = "Move email sent.";
        setTimeout(function() {
          item.getElementsByClassName("update-panel")[0].style.display = "block";
          item.getElementsByClassName("move-panel")[0].style.display = "none";
          item.dataset.date = moveTo;
          item.style.display = "none";
          selectDate(moveTo,moveToText,moveToAvailable);
          rewriteDates();
        },2000);
      }
    } else {

    }
  }
  req.send();
}

function moveAndApprove(id,button,approved) {
  movingID = id;
  movingButton = button;
  movingApproved = approved;
  button.parentElement.style.display = "none";
  var item = button.parentElement.parentElement;
  var moveText = item.getElementsByClassName("move-text")[0];
  moveText.innerText = "Select a date in the left panel to move this submission to.";
  moveText.style.textDecoration = "underline";
  item.getElementsByClassName("move-panel")[0].style.display = "block";
}

function cancelMove(button) {
  movingID = -1;
  button.parentElement.style.display = "none";
  var item = button.parentElement.parentElement;
  if ( ! movingApproved ) item.getElementsByClassName("respond-panel")[0].style.display = "block";
  else item.getElementsByClassName("update-panel")[0].style.display = "block";
}

function openRejectPanel(button) {
  button.parentElement.style.display = "none";
  var item = button.parentElement.parentElement;
  item.getElementsByClassName("reject-panel")[0].style.display = "block";
}

function cancelReject(button) {
  button.parentElement.style.display = "none";
  var item = button.parentElement.parentElement;
  item.getElementsByClassName("respond-panel")[0].style.display = "block";
}

function rewriteDates() {
  var req = new XMLHttpRequest();
  req.open("GET","dates.php?pwd=" + pwd);
  req.onload = function() {
    document.getElementById("dates").innerHTML = this.responseText;
  }
  req.send();
}
