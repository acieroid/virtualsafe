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
 * Generate a random salt
 */
function generate_salt() {
    /* generate the salted password hash */
    $size = mcrypt_get_iv_size(MCRYPT_CAST_256, MCRYPT_MODE_CFB);
    /* XXX: MCRYPT_DEV_RANDOM provides more security but is SLOW */
    return str2hex(mcrypt_create_iv($size, MCRYPT_DEV_URANDOM));
}

?>
