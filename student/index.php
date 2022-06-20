<?php
  if ( $_GET["pwd"] != "abcdef" ) die("invalid_pwd");
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Booth Submissions Student-Admin Portal</title>
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
      Booth Submissions Student-Admin Portal
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
            $data = mysqli_query($db,"SELECT * FROM submissions");
            while ( $row = $data -> fetch_assoc() ) {
              if ( $row["approved"] ) {
          ?>
            <div class="submission" data-date="<?php echo date("n-j-y",strtotime($row["requested"])); ?>" data-time="<?php echo $row["time"]; ?>">
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
              <hr />
            </div>
          <?php
              }
            }
            mysqli_close($db);
          ?>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
