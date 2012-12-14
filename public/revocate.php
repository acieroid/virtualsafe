<?php
require_once('../include/utils.php');
require_once('../include/database.php');
require_once('../include/model.php');
require_once('../include/sessions.php');
require_once('../include/csrf.php');
require_once('../include/crypto.php');

include('menu.php');

if (!session_has_user()) {
  ?>
  <p>Please <a href="signin.php">sign in</p>
  <?php
} else if (!session_has_valid_user()) {
  ?>
  <p>Your account is not valid. Please wait while an administrator validates it</p>
  <?php
} else if (isset($_POST['confirmation'])) {
  if (CSRF::check($_POST['csrf'])) {
    $user = session_get_user();
    /* Remove this user's certificate */
    $cert = $user->get_certificate_file();
    if (!unlink($cert)) {
      die('Cannot remove the certificate');
    }

    /* Remove all the user's signatures */
    $files = $user->list_owned_files();
    foreach ($files as $file) {
      if (!unlink($user->get_signature_path($file))) {
        die('Cannot remove a signature');
      }
    }

    /* Create a new certificate */
    $cert = new Certificate($user);
    $cert->save($user->get_certificate_file());

    /* Display the new certificate */
    ?>
    <p>Certificate revocated. Please copy the new certificate into the program:</p>
    <textarea readonly="readonly" cols="70" rows="85"><?php
    echo $cert->certstr;
    ?></textarea></p>
    <?php
  } else {
    ?>
    <p>The CSRF token is invalid</p>
    <?php
  }
} else {
  $token = CSRF::generate();
  ?>
  <p>Are you sure that you want to revocate your certificate? All your previous signatures will be deleted. To sign an existing with the new certificates, delete the file and reupload it with the new signature</p>
  <form action="revocate.php" method="post">
  <input type="hidden" name="confirmation" value="yes"/>
  <?php echo $token->get(); ?>
  <input type="submit" value="Yes, revocate my certificate"/>
  </form>
  <?php
}
?>
