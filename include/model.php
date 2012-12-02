<?php
require_once('utils.php');

/**
 * Class that should be inherited by every class of the model
 */
abstract class ModelObject {
  protected $pdo;
  protected $table;

  public function __construct(PDO $pdo) {
    $this->pdo = $pdo;
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
      $stmt = $this->pdo->prepare("select name from $this->table when id = :id");
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
   * Is the username a valid one? Check if it is already used by another user
   * XXX: this method should be static, but it needs the PDO
   */
  public function name_valid($name) {
    $stmt = $this->pdo->prepare('select * from user where name = :name');
    $stmt->bindValue(':name', $name);
    $stmt->execute();
    return count($stmt->fetchAll()) == 0;
  }

  /**
   * Check if the password given meets the security requirements
   */
  public static function password_valid($password) {
    return true; /* TODO: no security requirements yet */
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
    if (!$this->name_valid($this->name) || !self::password_valid($password)) {
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
}

?>
