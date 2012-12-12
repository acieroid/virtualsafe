<?php
require_once('../include/utils.php');
require_once('../include/database.php');
require_once('../include/model.php');
require_once('../include/sessions.php');
require_once('../include/crypto.php');

if (session_has_user()) {
?>
  <p>You are already connected</p>
<?php
} else if (isset($_GET['signup'], $_POST['name'], $_POST['password'])) {
  /* The user wants to sign up */
  $user = new User();
  $user->name = $_POST['name'];
  if ($user->create($_POST['password'])) {
    /* Generate the user's certificate */
    $cert = new Certificate($user);
?>
    <p>User created. Please wait that an admin validates your account. Please copy your certificate and public/private key in the program:<br />
    <textarea readonly="readonly"> 
<?php
    echo $cert->certstr . "\n" . $cert->privkeystr;
?>
    </textarea></p>
<?php
  } else {
?>
    <p>Error when creating the user</p>
<?php
  }
} else {
?>

<form action="signup.php?signup" method="post">
  <p><label for="name">Name: </label><input type="text" name="name" id="name"/></p>
  <p><label for="password">Password: </label><input type="password" name="password" id="password"/></p>
  <p><input type="submit" value="Sign up" /></p>
</form>
<?php
}
?>