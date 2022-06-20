<?php
include("config.php");

$db = mysqli_connect("127.0.0.1",SQL_USERNAME,SQL_PWD,"booth");

$sql = $db -> prepare("SELECT available FROM dates WHERE date = ?");
$sql -> bind_param("s",$_POST["requested"]);
if ( ! $sql -> execute() ) {
  echo $sql -> error;
  die("mysql_fail");
}
$result = $sql -> get_result();
$row = $result -> fetch_assoc();

if ( $row["available"] < $_POST["time"] || strtotime($_POST["requested"]) < time() ) {
  die("out_of_sync");
}

if ( $_FILES["media_file"]["name"] ) {
  $file_ext = strtolower(end(explode(".",$_FILES["media_file"]["name"])));
  if ( in_array($file_ext,SUBMITTED_FILE_EXTS) === false ) {
    die("invalid_ext");
  }
  if ( $_FILES["media_file"]["size"] > 2e9 ) {
    die("too_large");
  }

  $fpath = "file_" . uniqid() . "." . $file_ext;
  move_uploaded_file($_FILES["media_file"]["tmp_name"],SUBMITTED_FILE_DIR . "/" . $fpath);
  $furl = "/submitted_files/" . $fpath;
} else {
  $furl = "";
}

if ( $_POST["urgent"] == "on" ) $urgent = 1;
else $urgent = 0;

$sql = $db -> prepare("INSERT INTO submissions (submitted,requested,fname,lname,email,advisor_email,urgent,time,title,type,needs,media_url,notes) VALUES (now(),?,?,?,?,?,?,?,?,?,?,?,?)");
$sql -> bind_param("sssssiisssss",$_POST["requested"],$_POST["fname"],$_POST["lname"],$_POST["email"],$_POST["advisor_email"],$urgent,$_POST["time"],$_POST["title"],$_POST["type"],$_POST["needs"],$furl,$_POST["notes"]);
if ( ! $sql -> execute() ) {
  echo $sql -> error;
  die("mysql_fail");
}

$sql = $db -> prepare("UPDATE dates SET available = available - ?,requests = requests + 1,urgent = urgent + ? WHERE date = ?");
$sql -> bind_param("iis",$_POST["time"],$urgent,$_POST["requested"]);
if ( ! $sql -> execute() ) {
  die("mysql_fail");
}

mysqli_close($db);
echo "ok";
?>
