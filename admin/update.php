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

if ( $_GET["type"] == "delete" ) {
  $sql = $db -> prepare("DELETE FROM submissions WHERE id=?");
  $sql -> bind_param("i",$row["id"]);
  if ( ! $sql -> execute() ) {
    die("mysql_fail" . $sql -> error);
  }

  $sql = $db -> prepare("UPDATE dates SET available=available + ?,approved = approved - ? WHERE date=?");
  $sql -> bind_param("iis",$row["time"],$row["time"],$row["requested"]);
  if ( ! $sql -> execute() ) {
    die("mysql_fail");
  }
} elseif ( $_GET["type"] == "move" ) {
  $sql = $db -> prepare("UPDATE dates SET available = available + ?,approved=approved - ? WHERE date=?");
  $sql -> bind_param("iis",$row["time"],$row["time"],$row["requested"]);
  if ( ! $sql -> execute() ) {
    die("mysql_fail" . $sql -> error);
  }

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

  if ( $_GET["type"] == "delete" ) {
    $mail -> Body = sprintf(
      "Hi %s,<br /><br />Your assembly submission \"%s\" was <b>deleted</b>.<br /><br />If you have any questions or concerns, please reply to this email.<br /><br />Thanks,<br />Booth",
      $row["fname"],$row["title"]
    );
  } elseif ( $_GET["type"] == "move" ) {
    $mail -> Body = sprintf(
      "Hi %s,<br /><br />Your assembly submission \"%s\" was <b>moved</b> to %s.<br /><br />If you have any questions or concerns, please reply to this email.<br /><br />Thanks,<br />Booth",
      $row["fname"],$row["title"],DateTime::createFromFormat("m-d-Y",$_GET["moveTo"]) -> format("l, F j")
    );
  }

  $mail -> send();
}

echo "ok";
?>
