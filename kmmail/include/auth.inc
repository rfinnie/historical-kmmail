<?
include_once('include/misc.inc');
include_once('include/imap.inc');

function check_imap_auth($folder) {
  global $PHP_AUTH_USER, $PHP_AUTH_PW;
  if(!isset($PHP_AUTH_USER)) {
    header("WWW-Authenticate: Basic realm=\"My Realm\"");
    header("HTTP/1.0 401 Unauthorized");
    include('login_error.php');
    exit;
  } else {
    $imap = new km_imap($PHP_AUTH_USER, $PHP_AUTH_PW);
    if($imap->connect($folder)) {
      return array($imap, $PHP_AUTH_USER);
    } else {
      header("WWW-Authenticate: Basic realm=\"My Realm\"");
      header("HTTP/1.0 401 Unauthorized");
      include('login_error.php');
      exit;
    }
  }
}
?>
