<?php
require_once('utils.php');

/**
 * Class that should be inherited by every class of the model
 */
abstract class ModelObject {
  protected $pdo;
  protected $table;

  public function __construct() {
    $this->pdo = get_pdo();
  }
}

/**
 * Represent an identifiable object (user, admin, ...)
 */
abstract class Identifiable extends ModelObject {
  public $id;
  public $name;
  /* is the user identified? */
  private $identified = false;

  /**
   * Return the salt and the hashed password of an identifiable
   */
  private function get_salt_and_pass() {
    $stmt = $this->pdo->prepare("select salt, password from $this->table where name = :name");
    $stmt->bindValue(':name', $this->name);
    $stmt->execute();
    $res = $stmt->fetchAll();
    if (count($res) != 1) {
      /* no user or more than one user with the same name (which should not be
       * possible) */
      return null;
    } else {
      return $res[0];
    }
  }

  /**
   * If the name of the user is already filled, fill its ID.
   * If the ID of the user is already filled, fill its name.
   * Else, fails.
   */
  public function fill_fields() {
    if (isset($this->id) && isset($this->name)) {
      /* already filled */
      return true;
    } else if (isset($this->id)) {
      $stmt = $this->pdo->prepare("select name from $this->table where id = :id");
      $stmt->bindValue(':id', $this->id);
      $stmt->execute();
      $res = $stmt->fetchAll();
      if (count($res) != 1) {
        return false;
      } else {
        $this->name = $res[0][0];
        return true;
      }
    } else if (isset($this->name)) {
      $stmt = $this->pdo->prepare("select id from $this->table where name = :name");
      $stmt->bindValue(':name', $this->name);
      $stmt->execute();
      $res = $stmt->fetchAll();
      if (count($res) != 1) {
        return false;
      } else {
        $this->id = $res[0][0];
        return true;
      }
    } else {
      return false;
    }
  }

  /**
   * Identifies an user. If the name and the password are correct, fills the
   * fields of this user and returns true. Else return false. $password contains
   * the plain password
   */
  public function identify($name, $password) {
    $this->name = $name;
    $vals = $this->get_salt_and_pass();

    if ($vals == null) {
      /* no values found, the user don't exists */
      return false;
    }

    $salt = $vals[0];
    $hashed_password = $vals[1];

    $hashed = hash_secure($name . '|' . $password . '|' . $salt);
    if (strcmp($hashed, $hashed_password) != 0) {
      /* invalid password */
      return false;
    }

    $identified = true;
    return $this->fill_fields();
  }
}

/**
 * Represents an admin
 */
class Admin extends Identifiable {
  protected $table = 'admin';
}

/**
 * Represents an user
 */
class User extends Identifiable {
  protected $table = 'user';

  public $valid = false;

  /**
   * Is the username a valid one? Check if it is already used by
   * another user and if it contains valid characters.
   */
  public function name_valid($name) {
    if (!preg_match("/^[a-z0-9]+$/i", $name)) {
      /* name contains invalid characters */
      return false;
    }

    if (strlen($name) >= 100) {
      /* name is too long */
      return false;
    }

    /* name already exists? */
    $stmt = get_pdo()->prepare('select * from user where name = :name');
    $stmt->bindValue(':name', $name);
    $stmt->execute();
    return count($stmt->fetchAll()) == 0;
  }

  /**
   * Check if the password given meets the security requirements
   */
  public static function password_valid($password) {
    if (strlen($password) < 10) {
      /* password too short, rejected */
      return false;
    }

    if (preg_match("/^[a-z]*$/i", $password) != 0) {
      /* only alphabetical characters, rejected */
      return false;
    }

    return true;
  }

  /**
   * Check if a filename is valid, ie. less than 100 characters and
   * only with alpha-numeric characters, dots, dashes or spaces.
   */
  public static function filename_valid($name) {
    return strlen($name) < 100 && preg_match("/^[a-z0-9 \.-]+$/i", $name) != 0;
  }

  /**
   * Fill the fields of the user
   */
  public function fill_fields() {
    if (!parent::fill_fields()) {
      return false;
    }
    $stmt = $this->pdo->prepare('select valid from user where id = :id');
    $stmt->bindValue(':id', $this->id);
    $stmt->execute();
    $res = $stmt->fetchAll();
    if (count($res) != 1) {
      return false;
    }
    $this->valid = $res[0][0];
    return true;
  }

  /**
   * Create this user in the DB, with the password given as argument
   * Return true on succes, false on failure
   */
  public function create($password) {
    if (!self::name_valid($this->name) || !self::password_valid($password) ||
        strcmp($this->name, $password) == 0 /* same username and password, rejected */) {
      return false;
    }

    $salt = generate_salt();
    $hashed = hash_secure($this->name . '|' . $password . '|' . $salt);

    /* add the user to the db and fill its ID */
    $stmt = $this->pdo->prepare('insert into user(name, password, salt, valid) values (:name, :password, :salt, false)');
    $stmt->bindValue(':name', $this->name);
    $stmt->bindValue(':password', $hashed);
    $stmt->bindValue(':salt', $salt);
    return $stmt->execute() && $this->fill_fields();
  }

  /**
   * Find an user from its username. If no user was found, return null.
   */
  public static function find($name) {
    $user = new User();
    $user->name = $name;
    if (!$user->fill_fields()) {
      return null;
    }
    return $user;
  }

  /**
   * Deletes an user
   * Return true of success, false on failure.
   */
  public function delete() {
    $stmt = $this->pro->prepare('delete from user where id = :id');
    $stmt->bindValue(':id', $this->id);
    return $stmt->execute() == 1;
  }

  /**
   * Validate an user. When an user is valid, it can log in
   * Return true on success, false on failure.
   */
  public function validate() {
    $stmt = $this->pdo->prepare('update user set valid = true where id = :id');
    $stmt->bindValue(':id', $this->id);
    if ($stmt->execute() == 1) {
      $this->valid = true;
    }
    return $this->valid;
  }

  /**
   * Invalidate an user.
   * Return true on success, false on failure
   */
  public function invalidate() {
    $stmt = $this->pdo->prepare('update user set valid = false where id = :id');
    $stmt->bindValue(':id', $this->id);
    if ($stmt->execute() == 1) {
      $this->valid = false;
    }
    return !$this->valid;
  }

  /**
   * Return all the user not validated
   */
  static public function invalidated_users() {
    $stmt = get_pdo()->prepare('select id, name from user where valid = false');
    $stmt->execute();
    $res = $stmt->fetchAll();
    $result = array();
    foreach ($res as $u) {
      $user = new User();
      $user->id = $u['id'];
      $user->name = $u['name'];
      array_push($result, $user);
    }
    return $result;
  }

  /**
   * Return the path to the certificate file of this user
   */
  public function get_certificate_file() {
    return '../data/certificates/' . hash_secure($this->name) . '.crt';
  }

  /**
   * Extract the public key of a certificate from a file
   */
  static private function extract_key($file) {
    $cert = openssl_x509_read(file_get_contents($file));
    if ($cert === false) {
      throw new Exception('An error occured');
    }
    $key = openssl_pkey_get_public($cert);
    if ($key === false) {
      throw new Exception('An error occured');
    }
    return $key;
  }

  /**
   * Return the certificate file of this user
   */
  public function get_certificate() {
    return self::extract_key($this->get_certificate_file());
  }

  /**
   * Return the path to the public encryption key of this user
   */
  public function get_pubkey_file() {
    return '../data/pubkeys/' . hash_secure($this->name) . '.key';
  }

  /**
   * Return the public key of this user
   */
  public function get_pubkey() {
    return self::extract_key($this->get_pubkey_file());
  }

  /**
   * Return the string representing the public key of this user
   */
  public function get_pubkeystr() {
    $cert = openssl_x509_read(file_get_contents($this->get_pubkey_file()));
    if ($cert == null) {
      throw new Exception('An error occured');
    }
    openssl_x509_export($cert, $certstr);
    return $certstr;
  }

  /**
   * Return the directory where the files of this user are stored
   */
  public function get_files_directory() {
    return '../data/files/' . hash_secure($this->name);
  }

  /**
   * Return the path to a file given its name
   */
  public function get_file_path($name) {
    return $this->get_files_directory() . '/' . hash_secure($name);
  }

  /**
   * Return the path to a signature of a file given its name
   */
  public function get_signature_path($name) {
    return $this->get_file_path($name) . '.sign';
  }

  /**
   * Return the path to the key of a file given its name.  The path to
   * the key depend on the owner name (by default $this, but it can be
   * another user, when sharing), because if not, if A has a file
   * 'foo', B has a file 'foo', and B shares 'foo' with A, A cannot
   * decrypt his 'foo' anymore since the key is overwritten.
   */
  public function get_key_path($owner, $name) {
    return $this->get_files_directory() . '/' . hash_secure($owner->name . $name) . '.key';
  }

  /**
   * Change the password of an user.
   * Return true on success, false on failure.
   */
  public function change_password($old_password, $password) {
    if (!self::password_valid($password) || !$this->identify($this->name, $old_password)) {
      return false;
    }

    /* create the new password */
    $salt = generate_salt();
    $hashed = hash_secure($this->name . '|' . $password . '|' . $salt);

    $stmt = $this->pdo->prepare('update user set password = :password, salt = :salt where id = :id');
    $stmt->bindValue(':id', $this->id);
    $stmt->bindValue(':salt', $salt);
    $stmt->bindValue(':password', $hashed);

    return $stmt->execute();
  }

  /**
   * Encrypt a file with a random key. $source is the unencrypted
   * file, $filename is its original name. Save the encrypted file in
   * a user-specific directory, along with the key used for encryption
   * (itself encrypted using the user's public key).
   */
  public function encrypt_file($source, $filename) {
    if (!file_exists($source) || !self::filename_valid($filename)) {
      return false;
    }
    /* Create the destination directory */
    if (!file_exists($this->get_files_directory())) {
      mkdir($this->get_files_directory());
    }

    $dest = $this->get_file_path($filename);
    $keydest = $this->get_key_path($this, $filename);
    $key = generate_random_key();

    /* check if the destination already exists */
    if (file_exists($dest)) {
      return false;
    }

    /* encrypt the file */
    $content = file_get_contents($source);
    $encrypted = encrypt_secure($content, $key);
    /* save the encrypted file */
    /* compare with === because it might return 0 and get evaluated to false */
    if (file_put_contents($dest, $encrypted) === false) {
      return false;
    }

    /* encrypt the key */
    $encryptedKey = encrypt_asym_secure($key, $this->get_pubkey());

    /* save the encrypted key */
    if (file_put_contents($keydest, $encryptedKey) === false) {
      return false;
    }

    return true;
  }

  /**
   * Check the uploaded signature of a file.
   * Return true if the signature matches the file.
   */
  public function check_signature($file, $signature) {
    if (!file_exists($file) || !file_exists($signature)) {
      return false;
    }

    $content = file_get_contents($file);
    $signContent = hex2bin(file_get_contents($signature));

    return openssl_verify($content, $signContent, $this->get_certificate()) == 1;
  }

  /**
   * Save the signature of a file of this user. Returns true on success.
   */
  public function save_signature($signature, $filename) {
    if (!file_exists($signature)) {
      return false;
    }

    $content = file_get_contents($signature);
    if (file_put_contents($this->get_signature_path($filename),$content) === false) {
      return false;
    }

    return true;
  }

  /**
   * Add the database entry for a new file, given the filename.
   * The file should have previously been stored in
   * ../data/files/sha256(username)/sha256(filename), and its encryption key
   * in the corresponding .key file.
   * Return true on success.
   */
  public function add_file($name) {
    if (!self::filename_valid($name)) {
      return false;
    }
    /* Check if the file exists */
    $stmt = $this->pdo->prepare('select * from file where user_id = :id and filename = :name');
    $stmt->bindValue(':id', $this->id);
    $stmt->bindValue(':name', $name);
    $stmt->execute();
    $res = $stmt->fetchAll();
    if (count($res) != 0) {
      /* Already a file with that name, rejected */
      return false;
    }

    /* Add the file */
    $stmt = $this->pdo->prepare('insert into file(user_id, filename) values (:id, :name)');
    $stmt->bindValue(':id', $this->id);
    $stmt->bindValue(':name', $name);
    return $stmt->execute();
  }

  /**
   * Deletes a file of this user, given the name of the
   * file. Return true on success.
   */
  public function delete_file($filename) {
    if (!file_exists($this->get_file_path($filename))) {
      return false;
    }

    if (!unlink($this->get_file_path($filename)) ||
        !unlink($this->get_key_path($this, $filename))) {
      return false;
    }
    /* the signature might not exists, so we don't check the return value */
    unlink($this->get_signature_path($filename));


    $stmt = $this->pdo->prepare('select u.name from user u, share s, file f where s.owner_id = :id and f.user_id = u.id and s.file_id = f.id and f.filename = :filename');
    $stmt->bindValue(':id', $this->id);
    $stmt->bindValue(':filename', $filename);
    if (!$stmt->execute()) {
      return false;
    }
    $res = $stmt->fetchAll();
    foreach ($res as $username) {
      $user = User::find($username);
      if (!unlink($user->get_key_path($this, $filename))) {
        return false;
      }
    }

    /* The database schema uses the on-cascade deletion for the share
       table, so we don't have to delete those items ourselves */
    $stmt = $this->pdo->prepare('delete from file where user_id = :id and filename = :filename');
    $stmt->bindValue(':id', $this->id);
    $stmt->bindValue(':filename', $filename);
    if (!$stmt->execute()) {
      return false;
    }

    return true;
  }

  /**
   * Share a file with another user. Return true on success.
   */
  public function share_file($dest_user, $filename, $key) {
    if ($dest_user->has_access($this, $filename)) {
      /* file already shared */
      return false;
    }
    /* Find the file ID */
    $stmt = $this->pdo->prepare('select id from file where user_id = :id and filename = :filename');
    $stmt->bindValue(':id', $this->id);
    $stmt->bindValue(':filename', $filename);
    if (!$stmt->execute()) {
      return false;
    }
    $res = $stmt->fetchAll();
    if (count($res) != 1) {
      return false;
    }

    $file_id = $res[0]['id'];

    /* Add the entry to the DB */
    $stmt = $this->pdo->prepare('insert into share(file_id, owner_id, user_id) values (:file_id, :id, :dest_id)');
    $stmt->bindValue(':id', $this->id);
    $stmt->bindValue(':dest_id', $dest_user->id);
    $stmt->bindValue(':file_id', $file_id);
    if (!$stmt->execute()) {
      return false;
    }

    /* Add the key in the user directory */
    $data = file_get_contents($key);
    if (!file_exists($dest_user->get_files_directory())) {
      mkdir($dest_user->get_files_directory());
    }
    if (file_put_contents($dest_user->get_key_path($this, $filename), $data) === false) {
      return false;
    }

    return true;
  }

  /**
   * Check if a user owns a file
   */
  public function has_file($name) {
    $stmt = $this->pdo->prepare('select * from file where user_id = :id and filename = :name');
    $stmt->bindValue(':id', $this->id);
    $stmt->bindValue(':name', $name);
    $stmt->execute();
    $res = $stmt->fetchAll();
    return count($res) == 1;
  }

  /**
   * Check if a user has access to a file (ie. it is shared by the
   * owner with him)
   */
  public function has_access($owner, $name) {
    $stmt = $this->pdo->prepare('select * from share where owner_id = :owner_id and user_id = :id and file_id in (select id from file where owner_id = :owner_id and filename = :name)');
    $stmt->bindValue(':id', $this->id);
    $stmt->bindValue(':owner_id', $owner->id);
    $stmt->bindValue(':name', $name);
    $stmt->execute();
    $res = $stmt->fetchAll();
    return count($res) == 1;
  }

  /**
   * Return the list of all the files owned by this user
   */
  public function list_owned_files() {
    $stmt = $this->pdo->prepare('select filename from file where user_id = :id');
    $stmt->bindValue(':id', $this->id);
    $stmt->execute();
    $res = $stmt->fetchAll();
    $result = array();
    foreach ($res as $f) {
      array_push($result, $f['filename']);
    }
    return $result;
  }

  /**
   * Return the files shared by this user. Each entry of the list is
   * an array containing first the name of the file, and second the
   * name of the user it is shared with.
   */
  public function list_shared_files() {
    $stmt = $this->pdo->prepare('select filename, name from share s, user u, file f where s.owner_id = :id and s.user_id = u.id and s.file_id = f.id');
    $stmt->bindValue(':id', $this->id);
    $stmt->execute();
    $res = $stmt->fetchAll();
    $result = array();
    foreach ($res as $f) {
      array_push($result, array('filename' => $f['filename'],
                                'username' => $f['name']));
    }
    return $result;
  }

  /**
   * Return the list of the files shared with this user
   */
  public function list_shared_files_with() {
    $stmt = $this->pdo->prepare('select filename, name from share s, user u, file f where s.user_id = :id and s.owner_id = u.id and s.file_id = f.id');
    $stmt->bindValue(':id', $this->id);
    $stmt->execute();
    $res = $stmt->fetchAll();
    $result = array();
    foreach ($res as $f) {
      array_push($result, array('filename' => $f['filename'],
                                'username' => $f['name']));
    }
    return $result;
  }

  /**
   * Remove a shared file
   */
  public function unshare($user, $filename) {
    $stmt = $this->pdo->prepare('delete from share where owner_id = :id and file_id = (select id from file where user_id = :id and filename = :filename)');
    $stmt->bindValue(':id', $this->id);
    $stmt->bindValue(':filename', $filename);
    return $stmt->execute();
  }
}

?>
