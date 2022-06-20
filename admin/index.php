<?php
  if ( $_GET["pwd"] != "ghijkl" ) die("invalid_pwd");
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Booth Submissions Admin Portal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <link rel="stylesheet" type="text/css" href="/style.css" />
  <link rel="stylesheet" type="text/css" href="style.css" />
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
  <script src="script.js"></script>
</head>
<body>
  <nav class="navbar navbar-expand-lg fixed-top">
    <a class="navbar-brand" href="#">
      <img src="/shield.png" height="70" />
      Booth Submissions Admin Portal
    </a>
  </nav>
  <div class="container-fluid">
    <div class="row">
      <div class="col-6" id="date-col">
        <b>Select a date</b>
        <hr />
        <div id="dates">
          <?php include("dates.php"); ?>
        </div>
      </div>
      <div class="col-6" id="submission-col">
        <b id="dateField">&nbsp;</b>
        <hr />
        <div id="submissions">
          <?php
            include("../config.php");

            $db = mysqli_connect("127.0.0.1",SQL_USERNAME,SQL_PWD,"booth");
            $data = mysqli_query($db,"SELECT * FROM submissions ORDER BY approved ASC,urgent DESC");
            while ( $row = $data -> fetch_assoc() ) {
          ?>
            <div class="submission" data-date="<?php echo date("n-j-y",strtotime($row["requested"])); ?>" data-time="<?php echo $row["time"]; ?>">
          <?php
              if ( $row["approved"] ) {
          ?>
              <b class="block">Approved request</b>
          <?php
              } else {
                if ( ! $row["urgent"] ) {
          ?>
              <b class="red block">Pending request</b>
          <?php
                } else {
          ?>
              <b class="red bold block">Urgent pending request</b>
          <?php
                }
              }
          ?>
              <p>Title: <?php echo $row["title"]; ?></p>
              <p>Type: <?php echo $row["type"]; ?></p>
              <p>
                Submitted by <?php echo $row["fname"] . " " . $row["lname"] ?>
                (<a href="mailto:<?php echo $row["email"]; ?>"><?php echo $row["email"]; ?></a>)
              </p>
              <p>
                <?php
                  if ( $row["advisor_email"] != "" ) {
                    ?>
                    Advisor: <a href="mailto:<?php echo $row["advisor_email"]; ?>"><?php echo $row["advisor_email"]; ?></a>
                    <?php
                  }
                ?>
              </p>
              <p>Time requested: <b><?php echo $row["time"]; ?> mins.</b></p>
              <?php
                if ( $row["needs"] != "" ) {
              ?>
                <p>Performance needs: <?php echo $row["needs"]; ?></p>
              <?php
                }
              ?>
              <p>
                <?php
                  if ( $row["media_url"] == "" ) echo "No media attached.";
                  else echo "<a target=\"_blank\" href=" . $row["media_url"] . ">View attached media</a>";
                ?>
              </p>
              <?php
                if ( $row["notes"] != "" ) {
              ?>
                <p>Additional notes: <?php echo $row["notes"]; ?></p>
              <?php
                }
              ?>
          <?php
              if ( ! $row["approved"] ) {
          ?>
              <div class="respond-panel">
                <button type="button" class="btn btn-success" onclick="respondToRequest(<?php echo $row["id"]; ?>,true,this)">Approve</button>
                <button type="button" class="btn btn-danger" onclick="openRejectPanel(this)">Reject</button>
                <button type="button" class="btn btn-secondary" onclick="moveAndApprove(<?php echo $row["id"]; ?>,this,false)">Move & Approve</button>
                <span class="approve-text"></span>
              </div>
              <div class="reject-panel" style="display: none">
                <textarea class="reject-info" rows="4"></textarea>
                <p>Rejection info (optional)</p>
                <button type="button" class="btn btn-danger" onclick="respondToRequest(<?php echo $row["id"]; ?>,false,this)">Send rejection email</button>
                <button type="button" class="btn btn-secondary" onclick="cancelReject(this)">Cancel</button>
                <span class="red reject-text"></span>
              </div>
          <?php
              }
          ?>
              <div class="move-panel" style="display: none">
                <u class="move-text">Select a date in the left panel to move this submission to.</u>
                <button type="button" class="btn btn-secondary" onclick="cancelMove(this)">Cancel</button>
              </div>
              <div class="update-panel"
                <?php if ( ! $row["approved"] ) { ?>
                  style="display: none"
                <?php } ?>
              >
                <button type="button" class="btn btn-danger" onclick="sendUpdate('delete',<?php echo $row["id"]; ?>,this)">Delete</button>
                <button type="button" class="btn btn-secondary" onclick="moveAndApprove(<?php echo $row["id"]; ?>,this,true)">Move</button>
                <span class="red delete-text"></span>
              </div>
              <hr />
            </div>
          <?php
            }
            mysqli_close($db);
          ?>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
