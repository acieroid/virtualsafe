<?php

/**
 * Convert a string to its hexadecimal representation
 * TODO: it drops the 0 (eg. '12' . '0A' -> '12A' thanks to PHP)
 */
function str2hex($string) {
  $hex='';
  for ($i=0; $i < strlen($string); $i++) {
    $hex .= dechex(ord($string[$i]));
  }
  return $hex;
}

/**
 * Hash a string using a secure hash algorithm
 */
function hash_secure($str) {
  return hash('sha256', $str);
}

/**
 * Encrypt a string using a secure symmetric algorithm given a
 * key. Return the encrypted result. Note that it uses
 * MCRYPT_RIJNDAEL_128, which means 128-bit block size, and does not
 * mean nothing about the key (which can be set to 256-bit). AES256 is
 * Rijndael-128 with 256-bit key
 */
function encrypt_secure($str, $key) {
  $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
  $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
  $output = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $str, MCRYPT_MODE_CBC, $iv);
  return $iv . $output;
}

/**
 * Encrypt a string using an assymetric algorithm, given the public
 * key. Return the encrypted result, or null on failure.
 */
function encrypt_asym_secure($str, $key) {
  if (openssl_public_encrypt($str, $output, $key) === false) {
    throw new Exception('Cannot encrypt data: ' . openssl_error_string());
  }
  return $output;
}

/**
 * Generate a random salt
 */
function generate_salt() {
    /* generate the salted password hash */
    $size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
    /* XXX: MCRYPT_DEV_RANDOM provides more security but is SLOW */
    return str2hex(mcrypt_create_iv($size, MCRYPT_DEV_URANDOM));
}

/**
 * Generate 256-bit a random key
 */
function generate_random_key() {
  $size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
  return mcrypt_create_iv($size, MCRYPT_DEV_URANDOM);
}

?>
