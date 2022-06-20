<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title>Booth Submissions</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <link rel="stylesheet" type="text/css" href="style.css" />
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
  <script src="script.js"></script>
  <script>
    var timeAvailable = <?php include("date_info.php"); ?>
  </script>
</head>
<body>
  <nav class="navbar navbar-expand-lg fixed-top">
    <a class="navbar-brand" href="#">
      <img src="shield.png" height="70" />
      Booth Submissions
    </a>
  </nav>
  <div class="container-fluid">
    <div class="row">
      <div class="col-6" id="date-col">
        <h2 class="d-inline">1</h2> Select assembly date
        <hr />
        <div id="dates">
          <div id="month" class="date row">
            <div class="col-2" onclick="moveCalendar(-1)">&#x2190;</div>
            <div id="month-text" class="col-8"></div>
            <div class="col-2" onclick="moveCalendar(1)">&#x2192;</div>
          </div>
          <table id="calendar"></table>
        </div>
      </div>
      <div class="col-6" id="form-col">
        <h2 class="d-inline">2</h2> Complete submission
        <hr />
        <div id="form-box">
          <form action="#" id="form" onsubmit="submitForm(); return false">
            <p>
              <b id="date-text"></b>
              <a id="back-text" href="javascript: reopenCalendar()">Back to dates</a>
            </p>
            <div class="row">
              <div class="col-6">
                <input type="text" name="fname" required /><br />
                <p>First Name</p>
              </div>
              <div class="col-6">
                <input type="text" name="lname" required /><br />
                <p>Last Name</p>
              </div>
            </div>
            <div class="row">
              <div class="col-6">
                <input type="email" name="email" required onkeyup="updateUrgentBox(this.value)" pattern="[a-z0-9]*@nobles.edu" /><br />
                <p>Nobles Email Address</p>
              </div>
              <div class="col-6">
                <input type="email" name="advisor_email" id="advisor-email" required pattern="[a-z0-9]*0f@nobles.edu" /><br />
                <p>Advisor Email (if applicable)</p>
              </div>
            </div>

            <div id="urgent-box" style="display: none">
              <p>As a faculty member, you are allowed to mark this announcement as urgent. If you do, we will try our best to get it in on the day you request.</p>
              <p>
                This is an <u>urgent/time-sensitive announcement</u>:
                <input type="checkbox" name="urgent" />
              </p>
            </div>

            <input type="text" name="title" required /><br />
            <p>Submission Title</p>

            <p>Time requested: <input type="number" name="time" id="time-input" min="0" size="5" required /> minutes</p>

            <span>Submission Type:</span><br />
            <input type="radio" name="type" value="Performance" onchange="updateNeedsBox(this.value)" id="t1" required /> <label for="t1">Performance</label><br />
            <input type="radio" name="type" value="Announcement (no slides or video)" onchange="updateNeedsBox(this.value)" id="t2" required /> <label for="t2">Announcement (no slides or video)</label><br />
            <input type="radio" name="type" value="Slideshow or video presentation" onchange="updateNeedsBox(this.value)" id="t3" required /> <label for="t3">Slideshow or video presentation</label><br />
            <input type="radio" name="type" value="other" onchange="updateNeedsBox(this.value)" id="t4" required /> <label for="t4">Other</label> <input type="text" id="other-desc" class="no-full" /><br /><br />

            <div id="needs-box" style="display: none">
              <textarea name="needs" rows="4"></textarea>
              <p>Please specify performance needs such as instruments, microphones, lighting, etc.</p>
            </div>

            <label class="btn btn-primary">
              Upload <input type="file" name="media_file" id="media-file" onchange="updateFileName()" hidden />
            </label>
            <span id="file-name"></span>
            <p>Upload video file (optional, must be .MOV, .MP4, or .PPTX, max. file size 2GB)</p>

            <textarea name="notes" rows="4"></textarea>
            <p>Additional notes (optional)</p>

            <button type="submit" class="btn btn-primary">Submit request</button>
            <span class="red" id="error-msg"></span>
            <br /><br />
          </form>
          <p id="thanks-text">
            Thanks! Your request has been recorded and you will receive an email response shortly telling you if it was approved.<br /><br />
            <span id="desktop-thanks">If you want to make another submission, please select a date in the left panel.</span>
            <span id="mobile-thanks">If you want to make another submission, <a href="javascript: reopenCalendar()">click here to return to the calendar</a>.</span>
          </p>
          <p id="out-of-sync-text">
            Sorry, this webpage fell out-of-sync with the database, so the request could not be completed. The calendar has now been updated. Please choose another date and try again.<br /><br />
            <a id="mobile-out-of-sync" href="javascript: reopenCalendar()">Back to dates</a>
          </p>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
