<?php
  if ( $_GET["pwd"] != "ghijkl" ) die("invalid_pwd");

  include("../config.php");

  $db = mysqli_connect("127.0.0.1",SQL_USERNAME,SQL_PWD,"booth");
  $data = mysqli_query($db,"SELECT * FROM dates WHERE date >= CURRENT_DATE()");
  while ( $row = $data -> fetch_assoc() ) {
?>
  <div class="row">
    <div class="col-6">
      <a href="javascript: selectDate('<?php echo date("n-j-y",strtotime($row["date"])); ?>','<?php echo date("l, F j",strtotime($row["date"])); ?>',<?php echo $row["available"]; ?>)">
        <?php echo date("l, F j",strtotime($row["date"])); ?>
      </a>
    </div>
    <div class="time col-6">
      <?php echo $row["available"]; ?> mins. available<br />
      <?php echo $row["approved"]; ?> mins. approved<br />
      <span class="<?php if ( $row["requests"] > 0 ) echo "red"; ?>">
        <?php echo $row["requests"]; ?> pending request<?php if ( $row["requests"] != 1 ) echo "s"; ?>
        <?php if ( $row["urgent"] > 0 ) { ?><u>(<?php echo $row["urgent"]; ?> urgent)</u><?php } ?>
      </span>
    </div>
  </div>
<?php
  }
  mysqli_close($db);
?>
