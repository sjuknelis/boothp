<?php
if ( $_GET["pwd"] != "ghijkl" ) die("invalid_pwd");

include("../config.php");

$begin = new DateTime($_GET["begin"]);
$end = new DateTime($_GET["end"]);
$avoid = explode(",",$_GET["avoid"]);
$interval = DateInterval::createFromDateString('1 day');
$period = new DatePeriod($begin,$interval,$end);

$values = "";
foreach ( $period as $dt ) {
  $day = $dt -> format("D");
  if ( $day == "Sat" || $day == "Sun" ) continue;
  $ymd = $dt -> format("Y-m-d");
  if ( in_array($ymd,$avoid) ) continue;

  if ( $values != "" ) $values .= ",";
  $values .= sprintf("(\"%s\",20,0,0)",$ymd);
}

$db = mysqli_connect("127.0.0.1",SQL_USERNAME,SQL_PWD,"booth");

$sql = $db -> prepare("INSERT INTO dates (date,available,approved,requests) VALUES " . $values);
if ( ! $sql -> execute() ) {
  die("mysql_fail");
}

echo "ok";
?>
