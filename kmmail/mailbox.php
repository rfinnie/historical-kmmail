<?
// @(#) $Id: mailbox.php,v 1.5 2001/03/21 00:48:37 ryan Exp $
include_once('include/misc.inc');
check_cookie(&$username, &$password);

include_once('include/imap.inc');
$imap = new km_imap($username, $password);
$folder = ($folder ? $folder : $config[imap_mainbox]);
$imap->connect($folder);
if($action_delete) {
  if(count($delete_msg) > 0) {
    $imap->delete_messages(array_keys($delete_msg));
  }
  if(count($undelete_msg) > 0) {
    $imap->undelete_messages(array_keys($undelete_msg));
  }
} elseif($action_expunge) {
  $imap->expunge_messages();
}
$msgs = $imap->retrieve_message_list();
$foo = $imap->retrieve_mailboxes();
$imap->disconnect();
$count = count($msgs);
  ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><? echo $config[title]; ?> - Messages</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<link rel="stylesheet" href="css/style-xhtml-strict.css" type="text/css" />
</head>
<body class="normal">
<table border="0" cellpadding="1" cellspacing="0" width="600" class="backblack">
  <tr>
    <td>
      <table border="0" cellpadding="5" cellspacing="0" width="598" class="main">
        <tr>
          <td class="titleheader">
            <table border="0" cellpadding="0" cellspacing="0" width="100%" class="titlebar">
              <tr>
                <td align="left"><img src="images/titleleft.gif" width="48" height="26" alt="*" class="normal" /></td>
                <td class="titleheader"><? echo $config[title]; ?> - Messages</td>
                <td align="right"><img src="images/titleright.gif" width="48" height="26" alt="*" class="normal" /></td>
              </tr>
            </table>
          </td>
        </tr>
        <tr>
          <td class="normal">
            <table width="100%" border="0" cellpadding="1" cellspacing="1" class="backblack">
              <tr align="center"> 
                <td class="toolbar">&nbsp;<a href="mailbox.php">Mailbox</a>&nbsp;</td>
                <td class="toolbar">&nbsp;<a href="folders.php">Folders</a>&nbsp;</td>
                <td class="toolbar">&nbsp;<a href="compose.php">Compose</a>&nbsp;</td>
                <td class="toolbar">&nbsp;Reply&nbsp;</td>
                <td class="toolbar">&nbsp;Forward&nbsp;</td>
                <td class="toolbar">&nbsp;<a href="logout.php">Logout</a>&nbsp;</td>
              </tr>
            </table>
            <p /> 
              <form method="post" action="<? echo $PHP_SELF; ?>">
            <table width="100%" border="0" cellpadding="2" cellspacing="1" class="backblack">
                <tr align="center" class="messagelist-top"> 
                  <td><b>&nbsp;</b></td>
                  <td><b>From</b></td>
                  <td><b>Subject</b></td>
                  <td><b>Size</b></td>
                  <td><b>Date</b></td>
                </tr>
                <?
for($i = 0; $i < $count; $i++) {
  if($msgs[$i][deleted]) {
    $bgclass = "messagelist-deleted";
  } elseif($msgs[$i][unread]) {
    $bgclass = "messagelist-unread";
  } else {
    $bgclass = "messagelist-read";
  }
  ?> 
                <tr class="<? echo $bgclass; ?>"> 
                  <td> 
                    <input type="checkbox" name="<? echo ($msgs[$i][deleted] ? 'un' : ''); ?>delete_msg[<? echo $msgs[$i][msgno]; ?>]" />
                  </td>
                  <td><a href="message.php?folder=<? echo urlencode($folder); ?>&amp;msgno=<? echo $msgs[$i][msgno]; ?>"><? echo $msgs[$i][from]; ?></a></td>
                  <td><? echo $msgs[$i][subject]; ?></td>
                  <td><? echo km_human_readable_size($msgs[$i][size], 1); ?></td>
                  <td><? echo date("m/d/Y", $msgs[$i][udate]); ?></td>
                </tr>
                <?
}
if($count == 0) {
  ?> 
                <tr class="messagelist-read"> 
                  <td colspan="5" align="center"><b>This folder is empty.</b></td>
                </tr>
                <?
}
?> 
                <tr class="messagelist-read"> 
                  <td colspan="5"> 
                    <input type="submit" name="action_delete" value="Delete" />
                    <input type="submit" name="action_expunge" value="Remove Delete Messages" />
                  </td>
                </tr>
            </table>
              </form>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
</body>
</html>

