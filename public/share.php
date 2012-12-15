<?php
require_once('../include/utils.php');
require_once('../include/database.php');
require_once('../include/model.php');
require_once('../include/sessions.php');

include('menu.php');

if (!session_has_user()) {
  echo '<p>Please <a href="signin.php">sign in</a></p>';
} else if (isset($_GET['filename'], $_GET['user'], $_FILES['key'])) {
  /* Share the file with the user */
  if (CSRF::check($_POST['csrf'])) {
    if ($_FILES['key']['error'] == 0 || $_FILES['key']['error'] == UPLOAD_ERR_OK) {
      $filename = urldecode($_GET['filename']);
      $dest_user = User::find(urldecode($_GET['user']));
      $user = session_get_user();

      if ($user->has_file($filename)) {
        if ($dest_user != null) {
          if ($user->share_file($dest_user, $filename, $_FILES['key']['tmp_name'])) {
            echo '<p>The file is now shared with the user ' . $dest_user->name . '</p>';
          } else {
            echo '<p>Error when sharing the file</p>';
          }
        } else {
          echo '<p>The user you want to share the file with do not exists</p>';
        }
      } else {
        echo '<p>You do not own this file</p>';
      }
    } else {
      echo '<p>Error when uploading the file</p>';
    }
  } else {
    echo '<p>The CSRF token is invalid</p>';
  }
} else if (isset($_GET['filename'], $_POST['user'])) {
  /* Show the user's encryption key and display the form to upload the
     encrypted key, if the user is found */
  $user = session_get_user();
  $dest_user = User::find($_POST['user']);
  if ($dest_user != null) {
    /* The destination user is found */
    
    /* Add a link to download the key */
    echo '<p>Download the <a href="download.php?name=' . $_GET['filename'] . '&key">key</a></p>';
    /* Add the public key of the other user */
    echo '<p>Copy the public key of ' . $_POST['user'] . '</p>';
    echo '<textarea readonly="readonly" cols="70" rows="50">' . $dest_user->get_certstr() . '</textarea>';
    /* Add the form to upload the result */
    echo '<p>And upload the resulting file:</p>';
    $token = CSRF::generate();
    ?>
    <form method="post" action="share.php?filename=<?php echo $_GET['filename']; ?>&user=<?php echo urlencode($_POST['user']); ?>" enctype="multipart/form-data">
    <p><label for="key">Result: </label><input type="file" name="key" id="key"/></p>
    <?php echo $token->get(); ?>
    <p><input type="submit" value="Share"/></p>
    </form>
    <?php
  } else {
    echo '<p>This user do not exist. <a href="share.php?filename=' . $_GET['filename'] . '">Go back</a></p>';
  }
} else if (isset($_GET['filename'])) {
  ?>
  <form method="post" action="share.php?filename=<?php echo $_GET['filename']; ?>">
  <p><label for="user">User to share with: </label><input type="text" name="user" id="user"/></p>
  <p><input type="submit" value="Share"/>
  </form>
  <?php
} else {
    echo '<p>Invalid request</p>';
}
?>
