<?
// @(#) $Id: get_attach.php,v 1.3 2001/03/20 22:20:08 ryan Exp $
include_once('include/misc.inc');
check_cookie($username, $password);

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
