<?
include_once('include/misc.inc');
include_once('include/imap.inc');

function check_imap_auth($folder) {
  global $PHP_AUTH_USER, $PHP_AUTH_PW, $config;

  if(!isset($PHP_AUTH_USER) && ($config['default_login'] == 1)) {
    $PHP_AUTH_USER = $config['default_login_username'];
    $PHP_AUTH_PW = $config['default_login_password'];
  }
  
  if(!isset($PHP_AUTH_USER)) {
    $title = $config['title'];
    header("WWW-Authenticate: Basic realm=\"$title\"");
    header("HTTP/1.0 401 Unauthorized");
    include('login_error.php');
    exit;
  } else {
    $imap = new km_imap($PHP_AUTH_USER, $PHP_AUTH_PW);
    if($imap->connect($folder)) {
      return array($imap, $PHP_AUTH_USER);
    } else {
      $last_error = $imap->last_error();
      $title = $config['title'];
      header("WWW-Authenticate: Basic realm=\"$title\"");
      header("HTTP/1.0 401 Unauthorized");
      include('login_error.php');
      exit;
    }
  }
}
?>
