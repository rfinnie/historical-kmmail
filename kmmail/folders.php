<?
// @(#) $Id: folders.php,v 1.9 2001/04/19 06:20:33 ryan Exp $
include_once('include/misc.inc');
check_cookie($username, $password);

include_once('include/imap.inc');
$imap = new km_imap($username, $password);
$imap->connect($config[imap_mainbox]);
if($action == "delete") {
  if($folder != "INBOX") {
    $imap->delete_mailbox($folder);
  }
  header("Location: $PHP_SELF");
} elseif($action == "create") {
  if($folder != "INBOX") {
    $imap->create_mailbox($folder);
  }
  header("Location: $PHP_SELF");
} elseif($action == "rename") {
  if($oldfolder != "INBOX") {
    if($newfolder != "INBOX") {
      $imap->rename_mailbox($oldfolder, $newfolder);
    }
  }
  header("Location: $PHP_SELF");
}
$boxes = $imap->retrieve_mailboxes();

$imap->disconnect();


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><? echo $config[title]; ?> - Folders</title>
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
                <td class="titleheader"><? echo $config[title]; ?> - Folders</td>
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
            <table width="100%" border="0" cellpadding="2" cellspacing="1" class="backblack">
              <tr align="center" class="messagelist-top"> 
                <td><b>Mailbox</b></td>
                <td><b>Messages</b></td>
                <td><b>New</b></td>
                <td><b>Size</b></td>
                <td>&nbsp;</td>
              </tr>
              <?
for($i = 0; $i < count($boxes); $i++) {
  ?> 
              <tr class="messagelist-read"> 
                <td><a href="mailbox.php?folder=<? echo urlencode($boxes[$i][name]); ?>"><? echo $boxes[$i][name]; ?></a></td>
                <td><? echo $boxes[$i][msgs]; ?></td>
                <td><? echo $boxes[$i][unread]; ?></td>
                <td><? echo km_human_readable_size($boxes[$i][size], 1); ?></td>
                <td><? if(!(($boxes[$i]['name'] == 'INBOX') || ($boxes[$i]['msgs'] > 0))) { ?><a href="<? echo $PHP_SELF; ?>?action=delete&amp;folder=<? echo urlencode($boxes[$i][name]); ?>">Delete</a><? } else { ?>&nbsp;<? } ?></td>
              </tr>
              <?
}
?> 
            </table>
            <form method="post" action="<? echo $PHP_SELF; ?>">
              <input type="hidden" name="action" value="create" />
              <b>Create New Folder</b><br>
              <input name="folder" size="20" />
              <input type="submit" value="Create" />
            </form>
            <?
$noninbox = 0;
for($i = 0; $i < count($boxes); $i++) {
  if($boxes[$i]['name'] != "INBOX") {
    $noninbox++;
  }
}
if($noninbox > 0) {
  ?> 
            <form method="post" action="<? echo $PHP_SELF; ?>">
              <input type="hidden" name="action" value="rename" />
              <b>Rename Folder</b><br>
              From 
              <select name="oldfolder">
                <?
  for($i = 0; $i < count($boxes); $i++) {
    if($boxes[$i]['name'] != "INBOX") {
      ?> 
                <option value="<? echo $boxes[$i]['name']; ?>"><? echo $boxes[$i]['name']; ?></option>
                <?
    }
  }
  ?> 
              </select>
              to 
              <input name="newfolder" size="20" />
              <input type="submit" value="Rename" />
            </form>
            <?
}
?> </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
</body>
</html>
