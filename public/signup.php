<?php
require_once('../include/utils.php');
require_once('../include/database.php');
require_once('../include/model.php');
require_once('../include/sessions.php');
require_once('../include/csrf.php');
require_once('../include/crypto.php');

include('menu.php');

if (session_has_user()) {
?>
  <p>You are already connected</p>
<?php
} else if (isset($_GET['signup'], $_POST['name'], $_POST['password'])) {
  if (CSRF::check($_POST['csrf'])) {
    /* The user wants to sign up */
    $user = new User();
    $user->name = $_POST['name'];
    if ($user->create($_POST['password'])) {
      /* Generate the user's certificate and save the public part */
      $cert = new Certificate($user);
      $cert->save($user->get_certificate_file());
      /* Generate the user encryption/decryption key (represented by a
         certificate) and save the public part */
      $key = new Certificate($user);
      $key->save($user->get_pubkey_file());
?>
      <p>User created. Please wait that an admin validates your account.<br />
      Please copy the following into the program:<br />
      <textarea readonly="readonly" cols="70" rows="85"><?php
      echo $cert->certstr . "\n" . $cert->privkeystr . "\n" . $key->certstr . "\n" . $key->privkeystr;
      ?></textarea></p>
<?php
    } else {
?>
      <p>CSRF token is invalid</p>
<?php
    }
  } else {
?>
    <p>Error when creating the user</p>
<?php
  }
} else {
  $token = CSRF::generate();
?>

<form action="signup.php?signup" method="post">
  <label for="name">Name: </label><input type="text" name="name" id="name"/>
  <label for="password">Password: </label><input type="password" name="password" id="password"/>
<?php
  echo $token->get();
?>
  <input type="submit" value="Sign up" />
</form>
<?php
}
?>