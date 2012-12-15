<?php

/**
 * Hash a string using a secure hash algorithm
 */
function hash_secure($str) {
  return hash('sha256', $str);
}

/**
 * Encrypt a string using a secure symmetric algorithm given a
 * key. Return the encrypted result.
 */
function encrypt_secure($str, $key) {
  /* create the IV */
  $iv_size = openssl_cipher_iv_length('aes-256-cbc');
  $iv = openssl_random_pseudo_bytes($iv_size);
  /* encrypt with mcrypt instead of openssl, because openssl seems to
     do weird things with the padding. However, since mcrypt does not
     support AES, but does support rijndael, we have to do a bit of
     manipulation: add the PKCS#5 padding ourselve, and specify the
     block size (128-bit) */
  $block_size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
  $padding = $block_size - (strlen($str) % $block_size);
  $str .= str_repeat(chr($padding), $padding);
  $output = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $str, MCRYPT_MODE_CBC, $iv);
  /* $output = openssl_encrypt($str, 'aes-256-cbc', $key, true, $iv); */ 
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
 * TODO: use openssl_random_pseudo_bytes
 */
function generate_salt() {
  $size = 16;
  return bin2hex(openssl_random_pseudo_bytes($size));
}

/**
 * Generate 256-bit a random key
 * TODO: use openssl_random_pseudo_bytes
 */
function generate_random_key() {
  $size = openssl_cipher_iv_length('aes-256-cbc');
  return openssl_random_pseudo_bytes($size);
}

?>
