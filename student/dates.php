<?php
  if ( $_GET["pwd"] != "abcdef" ) die("invalid_pwd");

  include("../config.php");

  $db = mysqli_connect("127.0.0.1",SQL_USERNAME,SQL_PWD,"booth");
  $data = mysqli_query($db,"SELECT date,available,approved,requests FROM dates WHERE date >= CURRENT_DATE()");
  while ( $row = $data -> fetch_assoc() ) {
?>
  <div class="row">
    <div class="col-6">
      <a href="javascript: selectDate('<?php echo date("n-j-y",strtotime($row["date"])); ?>','<?php echo date("l, F j",strtotime($row["date"])); ?>',<?php echo $row["available"]; ?>)">
        <?php echo date("l, F j",strtotime($row["date"])); ?>
      </a>
    </div>
    <div class="time col-6">
      <?php echo $row["approved"]; ?> mins. approved<br />
    </div>
  </div>
<?php
  }
  mysqli_close($db);
?>
