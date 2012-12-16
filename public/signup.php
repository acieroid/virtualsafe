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
      Please download the following program: <a href="signer.jar">signer.jar</a><br />
      Please launch the program with <pre>java -jar signer.jar -n</pre> and paste this, along with a new line at the end:<br />
      <textarea readonly="readonly" cols="70" rows="85"><?php
      echo $cert->certstr . "\n" . $cert->privkeystr . "\n" . $key->certstr . "\n" . $key->privkeystr;
      ?></textarea></p>
<?php
    } else {
?>
      <p>Error when creating the user. Check that your password is strong enough. Maybe an user has already chosen this name, you may want to choose another one.</p>
<?php
    }
  } else {
?>
    <p>CSRF token is invalid</p>
<?php
  }
} else {
  $token = CSRF::generate();
?>

<p>Please use a strong password. If you do not know how to have a strong password, read <a href="http://xkcd.com/936/">this</a>. Passwords shorter than 10 characters or composed exclusively of alphabetic characters will be rejected</p>
<form action="signup.php?signup" method="post">
  <p><label for="name">Name: </label><input type="text" name="name" id="name"/></p>
  <p><label for="password">Password: </label><input type="password" name="password" id="password"/></p>
<?php
  echo $token->get();
?>
  <input type="submit" value="Sign up" />
</form>
<?php
}
?>