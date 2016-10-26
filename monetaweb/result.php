<?php
session_id($_GET['paymentid']);
session_start();
?>

<!DOCTYPE html>
<html>
  <body>
    <p>
      Result:
      <?php
        echo $_SESSION['payment-result']['result'];
      ?>
    </p>
  </body>
</html>
