<?php
require_once('../include/utils.php');
require_once('../include/database.php');
require_once('../include/model.php');
require_once('../include/sessions.php');
require_once('../include/csrf.php');
require_once('../include/crypto.php');

include('menu.php');

if (!session_has_user()) {
  echo '<p>Please <a href="signin.php">sign in</p>';
} else if (!session_has_valid_user()) {
  echo '<p>Your account is not valid. Please wait while an administrator validates it</p>';
} else if (isset($_POST['confirmation'])) {
  if (CSRF::check($_POST['csrf'])) {
    $user = session_get_user();
    /* Remove this user's certificate */
    $pubkey = $user->get_pubkey_file();
    if (!unlink($pubkey)) {
      die('Cannot remove the public key');
    }

    /* Remove all the user's files and signature */
    $files = $user->list_owned_files();
    foreach ($files as $file) {
      if (!$user->delete_file($file)) {
        die('Cannot remove a signature');
      }
    }

    /* Remove the file shared */
    $files = $user->list_shared_files();
    foreach ($files as $file) {
      $user->unshare(User::find($file['username']), $file['filename']);
    }

    /* Create a new key pair */
    $cert = new Certificate($user);
    $cert->save($user->get_certificate_file());

    /* Display the new certificate */
    ?>
    <p>Key pair revocated. Please launch the program with <pre>java -jar signer.jar -k</pre> and paste this:</p>
    <textarea readonly="readonly" cols="70" rows="85"><?php
    echo $cert->certstr . "\n" . $cert->privkeystr;
    ?></textarea></p>
    <?php
  } else {
    echo '<p>The CSRF token is invalid</p>';
  }
} else {
  $token = CSRF::generate();
  ?>
  <p>Are you sure that you want to revocate your encryption key? All your files will be deleted. There is no way back. You are strongly advised to download all the files you have uploaded before doing so.</p>
  <form action="revocate_key.php" method="post">
  <input type="hidden" name="confirmation" value="yes"/>
  <?php echo $token->get(); ?>
  <input type="submit" value="Yes, revocate my key"/>
  </form>
  <?php
}
?>
