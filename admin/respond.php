<?php
if ( $_GET["pwd"] != "ghijkl" ) die("invalid_pwd");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\OAuth;
use League\OAuth2\Client\Provider\Google;
date_default_timezone_set('Etc/UTC');
require '../vendor/autoload.php';

include("../config.php");

$db = mysqli_connect("127.0.0.1",SQL_USERNAME,SQL_PWD,"booth");

$result = mysqli_query($db,"SELECT * FROM submissions WHERE id=" . $_GET["id"]);
if ( ! $result ) {
  die("mysql_fail");
}
$row = $result -> fetch_assoc();

if ( $_GET["approved"] == "false" ) $sql = $db -> prepare("DELETE FROM submissions WHERE id=?");
else $sql = $db -> prepare("UPDATE submissions SET approved=true WHERE id=?");
$sql -> bind_param("i",$_GET["id"]);
if ( ! $sql -> execute() ) {
  die("mysql_fail");
}

if ( $_GET["approved"] == "false" || $_GET["moveTo"] != "" ) $sql = $db -> prepare("UPDATE dates SET requests=requests - 1,available=available + ?,urgent = urgent - ? WHERE date=?");
else $sql = $db -> prepare("UPDATE dates SET requests=requests - 1,approved=approved + ?,urgent = urgent - ? WHERE date=?");
$sql -> bind_param("iis",$row["time"],$row["urgent"],$row["requested"]);
if ( ! $sql -> execute() ) {
  die("mysql_fail");
}

if ( $_GET["moveTo"] != "" ) {
  $sql = $db -> prepare("UPDATE dates SET available = available - ?,approved=approved + ? WHERE date=STR_TO_DATE(?,'%m-%d-%Y')");
  $sql -> bind_param("iis",$row["time"],$row["time"],$_GET["moveTo"]);
  if ( ! $sql -> execute() ) {
    die("mysql_fail" . $sql -> error);
  }

  $sql = $db -> prepare("UPDATE submissions SET requested=STR_TO_DATE(?,'%m-%d-%Y') WHERE id=?");
  $sql -> bind_param("si",$_GET["moveTo"],$_GET["id"]);
  if ( ! $sql -> execute() ) {
    die("mysql_fail" . $sql -> error);
  }
}

if ( SEND_EMAILS ) {
  $mail = new PHPMailer();
  $mail -> isSMTP();
  $mail -> Host = "smtp.gmail.com";
  $mail -> SMTPAuth = true;
  $mail -> SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
  $mail -> Port = 587;
  $mail -> AuthType = "XOAUTH2";

  $provider = new Google([
    "clientId" => EMAIL_CLIENT_ID,
    "clientSecret" => EMAIL_CLIENT_SECRET
  ]);
  $mail -> setOAuth(
    new OAuth([
      "provider" => $provider,
      "clientId" => EMAIL_CLIENT_ID,
      "clientSecret" => EMAIL_CLIENT_SECRET,
      "refreshToken" => EMAIL_REFRESH_TOKEN,
      "userName" => EMAIL_FROM
    ])
  );

  $mail -> setFrom(EMAIL_FROM,"Booth Submissions");
  $mail -> addAddress($row["email"],$row["fname"] . " " . $row["lname"]);
  if ( $row["advisor_email"] != "" )$mail -> addAddress($row["advisor_email"]);
  $mail -> addReplyTo(EMAIL_REPLY_TO,"Booth Submissions");

  $mail -> IsHTML(true);
  $mail -> Subject = sprintf("Your Assembly Submission \"%s\"",$row["title"]);

  if ( $_GET["info"] != "" ) $info = sprintf("The following additional information was given:<br />%s<br /><br />",$_GET["info"]);
  else $info = "";
  if ( $_GET["moveTo"] != "" ) {
    $moveText = "Please note the change from the original date you requested.";
    $dateToUse = DateTime::createFromFormat("m-d-Y",$_GET["moveTo"]) -> format("l, F j");
  } else {
    $moveText = "";
    $dateToUse = date("l, F j",strtotime($row["requested"]));
  }

  $mail -> Body = sprintf(
    "Hi %s,<br /><br />Your assembly submission \"%s\" (%d minute%s) was <b>%s</b> for %s. %s<br /><br />%sIf you have any questions or concerns, please reply to this email.<br /><br />Thanks,<br />Booth",
    $row["fname"],$row["title"],$row["time"],$row["time"] == 1 ? "" : "s",$_GET["approved"] == "false" ? "rejected" : "approved",$dateToUse,$moveText,$info
  );

  $mail -> send();
}

echo "ok";
?>
