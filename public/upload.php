<?php
require_once('../include/utils.php');
require_once('../include/database.php');
require_once('../include/model.php');
require_once('../include/sessions.php');
require_once('../include/csrf.php');

include('menu.php');

if (!session_has_user()) {
  ?>
  <p>Please <a href="signin.php">sign in</a></p>
  <?php
} else if (isset($_FILES['file'], $_FILES['signature'])) {
  if (CSRF::check($_POST['csrf'])) {
    if (($_FILES['file']['error'] == 0 || $_FILES['file']['error'] == UPLOAD_ERR_OK) &&
        ($_FILES['signature']['error'] == 0 || $_FILES['signature']['error'] == UPLOAD_ERR_OK)) {
      $user = session_get_user();
      $fileName = $_FILES['file']['name'];
      if (User::filename_valid($fileName)) {
        if ($user->check_signature($_FILES['file']['tmp_name'], $_FILES['signature']['tmp_name'])) {
          if ($user->add_file($fileName) &&
              $user->encrypt_file($_FILES['file']['tmp_name'], $fileName) &&
              $user->save_signature($_FILES['signature']['tmp_name'], $fileName)) {
            echo '<p>File has been added</p>';
          } else {
            echo '<p>Error when saving the file</p>';
          }
        } else {
          echo '<p>The signature is invalid</p>';
        }
      } else {
        echo '<p>The filename is not valid. Please use only alphanumeric characters, dashes, spaces and dots</p>';
      }
    } else {
      echo '<p>Error when uploading the files</p>';
    }
  } else {
    echo '<p>The CSRF token is invalid</p>';
  }
}
else {
  $token = CSRF::generate();
  ?>
  <p>To generate the signature, run <pre>java -jar signer.jar -s file file.sign</pre>, and <pre>file.sign</pre> will contain the signature of <pre>file</pre>.</p>
  <form method="post" action="upload.php" enctype="multipart/form-data">
  <p><label for="file">File to upload: </label><input type="file" name="file" id="file"/></p>
  <p><label for="signature">Signature: </label><input type="file" name="signature" id="signature"/></p>
  <?php echo $token->get(); ?>
  <p><input type="submit" value="Send"/></p>
  </form>
  <?php
}
?>
