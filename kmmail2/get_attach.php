<?
// @(#) $Id: get_attach.php,v 1.1.1.1 2002/11/25 04:05:53 ryanf Exp $
include_once('include/message_show.inc.php');
include_once('include/misc.inc.php');
include_once('include/auth.inc.php');
include_once('include/imap.inc.php');

//$folder = ($folder ? $folder : $config[imap_mainbox]);
list($imap, $username) = check_imap_auth();
$imap->select_folder($folder);

header("Content-type: $type/$subtype");
if($action != "inline") {
  header("Content-Disposition: attachment; filename=$name");
}

if($part_no) {
  $body = imap_fetchbody($imap->mbox, $msgnum, $part_no, FT_UID);
  if($type == "MESSAGE" && $subtype == "RFC822") {
    $body = imap_fetchbody($imap->mbox, $msgnum, "$part_no.0", FT_UID) . $body;
  }
} else {
  $body = imap_fetchheader($imap->mbox, $msgnum, FT_UID + FT_PREFETCHTEXT);
  $body .= imap_body($imap->mbox, $msgnum, FT_UID);
}
if(($encoding == "base64") || ($action == "inline")){
  echo imap_base64($body);
} elseif($encoding == "qprint") {
  $body = preg_replace("/=(\r*)\n/", "", $body);
  $body = preg_replace("/=([A-Fa-f0-9]{2})/e", "pack('H2', '\\1')", $body);
  echo $body;
} else {
  echo $body;
}
$imap->disconnect();
?>
