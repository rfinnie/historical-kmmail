<?
// @(#) $Id: misc.inc,v 1.2 2001/03/20 22:20:21 ryan Exp $

include_once('include/settings.inc');

// Retrieves a full name using calls to passwd
function passwd_real_name($username) {
  global $config;

  if($config[use_passwd]) {
    $pwnam = @posix_getpwnam($username);
    $rnarray = explode(',', $pwnam[gecos]);
    $rn = $rnarray[0];
    if(!$rn) {
      return $config[passwd_map][$username];
    } else {
      return $rn;
    }
  } else {
    return $config[passwd_map][$username];
  }
}

function check_cookie(&$username, &$password) {
  global $config;
  global $kmauth;
  session_start();
  session_register("kmauth");
  if(!$kmauth || !$kmauth[username]) {
    if($config[default_login]) {
      $username = $config[default_login_username];
      $password = $config[default_login_password];
    } else {
      header("Location: login.php");
      exit;
    }
  } else {
    $username = $kmauth[username];
    $password = $kmauth[password];
  }

}

function clear_cookie() {
  session_destroy();
}
?>
