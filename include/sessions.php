<?php
require_once('database.php');

/* start the session */
session_start();

/**
 * Add an user to the session. We do not need any prevention for
 * session hijacking, because the website is delivered in HTTPS
 */
function session_store_user($user) {
  $_SESSION['uid'] = $user->id;
}

/**
 * Retrieve the user from the session. If there is no user in the
 * session or if the user is invalid, return null. To check if there
 * is a user, you should use session_has_user() first
 */
function session_get_user() {
  if (!session_has_user()) {
    return null;
  }
  $user = new User(get_pdo());
  $user->id = $_SESSION['uid'];
  $user->fill_fields();
}

/**
 * Check if the session contains a user and if it is valid. Returns
 * true if it is the case, false otherwise
 */
function session_has_user() {
  return isset($_SESSION['uid']) && is_numeric($_SESSION['uid']);
}

/**
 * Remove the user from the session
 */
function session_remove_user() {
  unset($_SESSION['uid']);
}

/**
 * Similar to session_store_user
 */
function session_store_admin($admin) {
  $_SESSION['aid'] = $admin->id;
}

/**
 * Similar to session_get_user
 */
function session_get_admin() {
  if (!session_has_admin()) {
    return null;
  }
  $admin = new Admin(get_pdo());
  $admin->id = $_SESSION['aid'];
  $admin->fill_fields();
}

/**
 * Similar to session_has_user
 */
function session_has_admin() {
  return isset($_SESSION['aid']) && is_numeric($_SESSION['aid']);
}

/**
 * Similar to session_remove_user
 */
function session_remove_admin() {
  unset($_SESSION['aid']);
}

?>