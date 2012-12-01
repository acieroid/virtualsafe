<?php
$con = mysql_connect('localhost','www','password');
if (!$con) {
  die('Could not connect: ' . mysql_error());
}
echo 'Connected to the DB';
mysql_close($con);
?>
