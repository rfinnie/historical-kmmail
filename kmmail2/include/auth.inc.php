<?
include_once('include/misc.inc.php');
include_once('include/imap.inc.php');


function auth_login($username, $password, $profile) {
  global $config, $profiles;

  $config = array_merge($config, $profiles[$profile]);

  $folder = $config['imap_mainbox'];
  $imap = new km_imap($username, $password);
  if($imap->connect($folder)) {
    return array(1, $imap);
  } else {
    return array(0, $imap);
  }
}


function check_imap_auth() {
  global $config, $profiles, $profiles_defaults, $mainauth, $mainlogout, $username, $password, $profile, $PHP_SELF;

  if($mainlogout) {
    session_destroy();
    session_start();
  }

  if($_SESSION['username'] && $_SESSION['password'] && $_SESSION['profile']) {
    list($auth_status, $imap) = auth_login($_SESSION['username'], $_SESSION['password'], $_SESSION['profile']);
    if($auth_status == 1) {
      if(!$_SESSION['browse_folder']) {
        $_SESSION['browse_folder'] = $config['imap_mainbox'];
      }
    } else {
      unset($_SESSION['username']);
      unset($_SESSION['password']);
      unset($_SESSION['profile']);
      $auth_error = $imap->last_error();
      include('login.php');
      exit();
    }
  } else {
    if($mainauth) {
      list($auth_status, $imap) = auth_login($username, $password, $profile);
      if($auth_status == 1) {
        $_SESSION['username'] = $username;
        $_SESSION['password'] = $password;
        $_SESSION['profile'] = $profile;
        if(!$_SESSION['browse_folder']) {
          $_SESSION['browse_folder'] = $config['imap_mainbox'];
        }
        header("Location: $HTTP_SELF");
        exit();
      } else {
        $auth_error = $imap->last_error();
        include('login.php');
        exit();
      }
    } else {
      include('login.php');
      exit();
    }
  }

  return array($imap, $_SESSION['username']);
}

?>
