<?
// @(#) $Id: get_attach.php,v 1.4 2001/04/19 06:20:33 ryan Exp $
include_once('include/message_show.inc');
include_once('include/misc.inc');
include_once('include/auth.inc');
include_once('include/imap.inc');

$folder = ($folder ? $folder : $config[imap_mainbox]);
list($imap, $username) = check_imap_auth($folder);

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
