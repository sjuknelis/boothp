{
  <?php
    include("config.php");

    $db = mysqli_connect("127.0.0.1",SQL_USERNAME,SQL_PWD,"booth");
    $data = mysqli_query($db,"SELECT date,available FROM dates");
    while ( $row = $data -> fetch_assoc() ) {
      if ( $row["available"] > 0 ) {
  ?>
  "<?php echo date("l, F j, Y",strtotime($row["date"])); ?>": <?php echo $row["available"]; ?>,
  <?php
      }
    }
  ?>
  "x": "x"
  <?php
    mysqli_close($db);
  ?>
}
