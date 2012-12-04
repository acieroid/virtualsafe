<?php

/**
 * Return the PDO for manipulating the database
 */
function get_pdo() {
  $www_user = 'www';
  $www_password = 'password';
  $pdo = new PDO('mysql:dbname=secu;host=localhost', $www_user, $www_password);
  /* Activate exceptions instead of silently failing */
  /* TODO: the exception have to be caught, or we need to disable when done debugging */
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  return $pdo;
}

?>
