<?php

/**
 * Represente a certificate with its private key
 */
class Certificate {
  public $cert, $privkey, $csr;
  public $certstr, $privkeystr;

  /**
   * Create and sign the certificate for the user given
   */
  public function __construct($user) {
    $config = array('config' => '/etc/ssl/openssl.cnf',
                    'digest_alg' => 'sha1',
                    'private_key_bits' => 2048,
                    'private_key_type' => OPENSSL_KEYTYPE_RSA,
                    'encrypt_key' => false /* TODO: Not sure if useful/secure? */);
    /* Fill the certificate informations */
    $dn = array('countryName' => 'BE',
                'stateOrProvinceName' => 'Brussels',
                'localityName' => 'Brussels',
                'organizationName' => 'ULB',
                'organizationalUnitName' => 'INFO-F-405',
                'commonName' => $user->name,
                /* TODO: add email addresses for users? */
                'emailAddress' => 'admin@cours.awesom.eu');

    /* Generate the certificate signing request and the private key*/
    $this->privkey = null; /* will be set by openssl_csr_new */
    $this->csr = openssl_csr_new($dn, $this->privkey, $config);

    /* Sign the certificate */
    /* TODO: $this->cert = self::server_sign($this->csr); */
    $this->cert = openssl_csr_sign($this->csr, null, $this->privkey, 365, $config);

    /* Convert the certificate and key to readable values */
    /* TODO: also save them to files */
    /* TODO: drop the certificate part until the ----- BEGIN ... */
    openssl_x509_export($this->cert, $this->certstr, false);
    openssl_pkey_export($this->privkey, $this->privkeystr, false);
  }

  /**
   * Sign a certificate with the server's certificate for one year
   */
  public static function server_sign() {
    /* TODO: read the certificate from the file were its saved, see http://forums.phpfreaks.com/topic/78186-openssl-x509-certificate-problems/ */
    /* $cert = openssl_csr_new($this->csr, $servcert->cert, $servcert->privkey, 365); */
  }
}

?>