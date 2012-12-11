<?php
require_once('sessions.php');
require_once('utils.php');

/**
 * A token to avoid CSRF attacks
 */
class CSRFToken {
  public $token;

  /**
   * Constructor, take the token as argument
   */
  public function __construct($token) {
    $this->token = $token;
  }

  /**
   * Return the token in a hidden form field
   */
  public function get() {
    return '<input type="hidden" name="csrf" value="' . $this->token . . '" />';
  }
}

/**
 * Prevent CSRF attacks
 */
class CSRF {

  /**
   * Generate a random token
   */
  public static generate() {
    $token = sha1(str2hex(generate_salt()));
    $_SESSION['csrf'] = $token;
    return new CSRFToken($token);
  }

  /**
   * Check if a given token is correct
   */
  public static check($token) {
    return strcmp($_SESSION['csrf'], $token) == 0;
  }
}

?>