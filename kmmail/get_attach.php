<?
// @(#) $Id: km_message_show.inc,v 2.2 2001/02/07 19:32:56 ryan Exp $
if(!$kmauth) {
  exit;
}

$kmauth_array = explode(':', $kmauth);
$username = pack("H" . strlen($kmauth_array[0]), $kmauth_array[0]);
$password = pack("H" . strlen($kmauth_array[1]), $kmauth_array[1]);
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
