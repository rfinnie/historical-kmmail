<?
// @(#) $Id: get_attach.php,v 1.1 2001/03/03 07:38:28 ryan Exp $
session_start();
session_register("kmauth");
if(!$kmauth) {
  exit;
}
$username = $kmauth[username];
$password = $kmauth[password];
if(!$username) {
  exit;
}

include_once('include/imap.inc');
$imap = new km_imap($username, $password);
$imap->connect($folder);

header("Content-type: $type/$subtype");
if($action != "inline") {
  header("Content-Disposition: attachment; filename=$name");
}

$body = imap_fetchbody($imap->mbox, $msgnum, $part_no, FT_UID);
if($type == "MESSAGE" && $subtype == "RFC822") {
  $body = imap_fetchbody($imap->mbox, $msgnum, "$part_no.0", FT_UID) . $body;
}
if(($encoding == "base64") || ($action == "inline")){
  echo imap_base64($body);
} else {
  echo $body;
}
$imap->disconnect();
?>
