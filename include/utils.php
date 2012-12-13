<?php

/**
 * Convert a string to its hexadecimal representation
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
 * key. Return the encrypted result.
 * TODO: use CBC mode
 */
function encrypt_secure($str, $key) {
  //$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
  //$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
  /* TODO: pass the IV as last argument, and save it somewhere for decryption */
  return mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $str, MCRYPT_MODE_ECB);
}

/**
 * Decrypt a string using the same secure symmetric algorithm as
 * encrypt_secure. Return the decrypted result.
 */
function decrypt_secure($str, $key) {
  return mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $str, MCRYPT_MODE_ECB);
}

/**
 * Generate a random salt
 */
function generate_salt() {
    /* generate the salted password hash */
    $size = mcrypt_get_iv_size(MCRYPT_CAST_256, MCRYPT_MODE_CFB);
    /* XXX: MCRYPT_DEV_RANDOM provides more security but is SLOW */
    return str2hex(mcrypt_create_iv($size, MCRYPT_DEV_URANDOM));
}

?>
