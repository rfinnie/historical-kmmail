<?
// @(#) $Id: misc.inc,v 1.3 2001/03/21 01:24:20 ryan Exp $

include_once('include/settings.inc');
include_once('include/imap.inc');

function km_human_readable_size($number, $precision) {
  $tags = array('B', 'KB', 'MB', 'GB', 'TB');
  $attag = 0;
  while($number > 1024) {
    $number = $number / 1024;
    $attag++;
  }
  return round($number, $precision) . $tags[$attag];
}
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

function km_session_start($username, $password) {
  global $HTTP_SESSION_VARS;
  session_start();
  global $kmauth;
  $kmauth = array(
    "username" => $username,
    "password" => $password
  );
  session_register("kmauth");
}

function km_send_udp_stat_packet() {
  @fsockopen("udp://stats.kmmail.org", 65520);
  @fputs($fp, '');
  @fclose($fp);
}
?>
