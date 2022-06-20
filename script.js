var selected = null;
var calendarMonth;
var haveSubmitted = false;
var DAY = 24 * 60 * 60 * 1000;
var MONTHS = ["January","February","March","April","May","June","July","August","September","October","November","December"];

function selectDate(date) {
  selected = date;
  var available = timeAvailable[date];
  document.getElementById("time-input").max = available;
  if ( haveSubmitted ) {
    document.getElementById("form").reset();
    document.getElementById("needs-box").style.display = "none";
    var otherDesc = document.getElementById("other-desc");
    otherDesc.style.display = "none";
    otherDesc.required = false;
  }
  haveSubmitted = false;
  document.getElementById("form").style.display = "block";
  document.getElementById("thanks-text").style.display = "none";
  document.getElementById("out-of-sync-text").style.display = "none";
  document.getElementById("date-text").innerText = "For " + date;
  document.getElementById("form-col").style.display = "block";
  document.getElementById("form-col").classList.add("visible");
}

function reopenCalendar() {
  createCalendar();
  document.getElementById("form-col").classList.remove("visible");
}

function updateFileName() {
  document.getElementById("file-name").innerText = document.getElementById("media-file").files[0].name;
}

function submitForm() {
  var req = new XMLHttpRequest();
  req.open("POST","submit.php");
  req.onload = function() {
    var resp = this.responseText;
    console.log(resp);
    if ( resp == "ok" ) {
      var form = document.getElementById("form");
      form.style.display = "none";
      document.getElementById("thanks-text").style.display = "block";
      document.getElementById("urgent-box").style.display = "none";
      timeAvailable[selected] -= form.time.value;
      createCalendar();
      document.getElementById("error-msg").innerText = "";
      haveSubmitted = true;
    } else {
      if ( resp == "invalid_ext" ) document.getElementById("error-msg").innerText = "Error: Invalid file extension";
      else if ( resp == "too_large" ) document.getElementById("error-msg").innerText = "Error: File too large";
      else if ( resp == "out_of_sync" ) handleOutOfSync();
      else alert("Unexpected system error: " + resp + ". Sorry for any inconvenience.");
    }
  }
  var data = new FormData(form);
  data.append("requested",sqlFormatDate(selected));
  if ( data.get("type") == "other" ) data.set("type",document.getElementById("other-desc").value);
  req.send(data);
}

function handleOutOfSync() {
  var req = new XMLHttpRequest();
  req.open("GET","date_info.php");
  req.onload = function() {
    var resp = this.responseText;
    console.log(resp);
    timeAvailable = JSON.parse(resp);
    createCalendar();
    document.getElementById("form").style.display = "none";
    document.getElementById("out-of-sync-text").style.display = "block";
  }
  req.send();
}

function createCalendar() {
  var weekday = calendarMonthToDate().getDay();
  var date = calendarMonthToDate().getTime() - DAY * weekday;

  var enabled = false;
  var table = document.getElementById("calendar");
  while ( table.lastChild ) table.removeChild(table.lastChild);
  for ( var i = 0; i < 5; i++ ) {
    var row = document.createElement("tr");
    for ( var j = 0; j < 7; j++ ) {
      var col = document.createElement("td");
      var button = document.createElement("div");
      var str = new Date(date).getDate();
      if ( str == 1 ) {
        str = new Date(date).toLocaleString("default",{month: "short"}) + " " + str;
        enabled = ! enabled;
      }
      button.classList.add("date");
      var available = timeAvailable[formatDate(date)];
      if ( available && date >= new Date().getTime() ) {
        button.innerText = str + "\n" + available + " min.";
      } else {
        button.innerText = str + "\n–";
        button.classList.add("disabled");
      }
      if ( ! enabled ) button.classList.add("disabled");
      if ( ! button.classList.contains("disabled") ) button.classList.add("active");
      button.dataset.date = formatDate(date);
      button.onclick = function() {
        if ( ! this.classList.contains("active") ) return;
        selectDate(this.dataset.date);
        var buttons = document.querySelectorAll(".date.active");
        for ( var i = 0; i < buttons.length; i++ ) buttons[i].classList.remove("selected");
        this.classList.add("selected");
      }
      col.appendChild(button);
      row.appendChild(col);
      date += DAY;
    }
    table.appendChild(row);
  }
}

function moveCalendar(move) {
  var diff = 0;
  if ( move == 1 ) diff = DAY * 31;
  else if ( move == -1 ) diff = -DAY;
  var date = new Date(calendarMonthToDate().getTime() + diff);
  console.log(calendarMonth)
  calendarMonth = date.toLocaleString("default",{month: "long"}) + " " + date.getFullYear();
  document.getElementById("month-text").innerText = calendarMonth;
  createCalendar();
}

function formatDate(val) {
  var date = new Date(val);
  return date.toLocaleString("default",{weekday: "long",year: "numeric",month: "long",day: "numeric"})
}

function sqlFormatDate(val) {
  var date = new Date(val);
  return date.getFullYear() + "-" + (date.getMonth() + 1) + "-" + date.getDate();
}

function calendarMonthToDate() {
  var month = MONTHS.indexOf(calendarMonth.split(" ")[0]);
  var year = parseInt(calendarMonth.split(" ")[1]);
  return new Date(year,month,1);
}

function updateUrgentBox(email) {
  var urgentBox = document.getElementById("urgent-box");
  var advisorEmail = document.getElementById("advisor-email");
  if ( email.endsWith("0f@nobles.edu") ) {
    urgentBox.style.display = "block";
    advisorEmail.disabled = true;
    advisorEmail.value = "N/A";
  } else {
    urgentBox.style.display = "none";
    if ( advisorEmail.disabled ) {
      advisorEmail.disabled = false;
      advisorEmail.value = "";
    }
  }
}

function updateNeedsBox(type) {
  var needsBox = document.getElementById("needs-box");
  if ( type == "Performance" ) needsBox.style.display = "block";
  else needsBox.style.display = "none";
  var otherDesc = document.getElementById("other-desc");
  if ( type == "other" ) {
    otherDesc.style.display = "inline";
    otherDesc.required = true;
  } else {
    otherDesc.style.display = "none";
    otherDesc.required = false;
  }
}

window.onload = function() {
  var date = new Date();
  calendarMonth = date.toLocaleString("default",{month: "long"}) + " " + date.getFullYear();
  moveCalendar(0);
  createCalendar();
  console.log(window.innerWidth);
}
