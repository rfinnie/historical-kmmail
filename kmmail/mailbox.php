<?
// @(#) $Id: mailbox.php,v 1.2 2001/03/03 08:36:56 ryan Exp $
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
<html>
<head>
<title>kmMail - Mailbox</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Content-Style-Type" content="text/css">
<link rel="stylesheet" href="css/style.css" type="text/css">
</head>
<body bgcolor="#FFFFFF" text="#000000" background="images/bg.gif">
<table border=0 cellpadding=1 cellspacing=0 bgcolor="#000000" width=600 align="center">
  <tr> 
    <td> 
      <table border=0 cellpadding=5 cellspacing=0 bgcolor="#DEDFD6" width=598>
        <tr> 
          <td align="center"> 
            <table border=0 cellpadding=0 cellspacing=0 width="100%" background="images/titlebg.gif">
              <tr> 
                <td align="left"><img src="images/titleleft.gif" width="48" height="26" border="0"></td>
                <td align="center"> 
                  <div class="header1">kmMail - <? echo ($folder ? $folder : "Inbox"); ?> 
                    (<? echo $count; ?> messages)</div>
                </td>
                <td align="right"><img src="images/titleright.gif" width="48" height="26" border="0"></td>
              </tr>
            </table>
          </td>
        </tr>
        <tr> 
          <td> 
            <table width="100%" border=0 cellpadding=1 cellspacing=1 bgcolor="#000000">
              <tr align="center"> 
                <td bgcolor="#C0C0C0">&nbsp;Mailbox&nbsp;</td>
                <td bgcolor="#C0C0C0">&nbsp;<a href="folders.php">Folders</a>&nbsp;</td>
                <td bgcolor="#C0C0C0">&nbsp;<a href="compose.php">Compose</a>&nbsp;</td>
                <td bgcolor="#C0C0C0">&nbsp;Reply&nbsp;</td>
                <td bgcolor="#C0C0C0">&nbsp;Forward&nbsp;</td>
                <td bgcolor="#C0C0C0">&nbsp;<a href="logout.php">Logout</a>&nbsp;</td>
              </tr>
            </table>
            <p> 
            <table width="100%" border=0 cellpadding=2 cellspacing=1 bgcolor="#000000">
              <form>
                <tr align="center" bgcolor="#C0C0C0"> 
                  <td><b>&nbsp;</b></td>
                  <td><b>From</b></td>
                  <td><b>Subject</b></td>
                  <td><b>Size</b></td>
                  <td><b>Date</b></td>
                </tr>
                <?
for($i = 0; $i < $count; $i++) {
  if($msgs[$i][deleted]) {
    $bgcolor = "#808080";
  } elseif($msgs[$i][unread]) {
    $bgcolor = "#F0F0FF";
  } else {
    $bgcolor = "#FFFFFF";
  }
  ?> 
                <tr bgcolor="<? echo $bgcolor; ?>"> 
                  <td> 
                    <input type="checkbox" name="<? echo ($msgs[$i][deleted] ? 'un' : ''); ?>delete_msg[<? echo $msgs[$i][msgno]; ?>]">
                  </td>
                  <td><a href="message.php?folder=<? echo urlencode($folder); ?>&msgno=<? echo $msgs[$i][msgno]; ?>"><? echo $msgs[$i][from]; ?></a></td>
                  <td><? echo $msgs[$i][subject]; ?></td>
                  <td><? echo $msgs[$i][size]; ?></td>
                  <td><? echo date("m/d/Y", $msgs[$i][udate]); ?></td>
                </tr>
                <?
}
if($count == 0) {
  ?> 
                <tr bgcolor="#FFFFFF"> 
                  <td colspan=5 align="center"><b>This folder is empty.</b></td>
                </tr>
                <?
}
?> 
                <tr bgcolor="#FFFFFF"> 
                  <td colspan=5> 
                    <input type="submit" name="action_delete" value="Delete">
                    <input type="submit" name="action_expunge" value="Remove Delete Messages">
                  </td>
                </tr>
              </form>
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
</body>
</html>
