<?
// @(#) $Id: folders.php,v 1.1 2001/03/03 07:38:28 ryan Exp $
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
$imap->connect('INBOX');
$boxes = $imap->retrieve_mailboxes();
$imap->disconnect();


?>
<html>
<head>
<title>kmMail - Folders</title>
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
                  <div class="header1">kmMail - Folders</div>
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
                <td bgcolor="#C0C0C0">&nbsp;<a href="mailbox.php">Mailbox</a>&nbsp;</td>
                <td bgcolor="#C0C0C0">&nbsp;Folders&nbsp;</td>
                <td bgcolor="#C0C0C0">&nbsp;<a href="compose.php">Compose</a>&nbsp;</td>
                <td bgcolor="#C0C0C0">&nbsp;Reply&nbsp;</td>
                <td bgcolor="#C0C0C0">&nbsp;Forward&nbsp;</td>
                <td bgcolor="#C0C0C0">&nbsp;<a href="logout.php">Logout</a>&nbsp;</td>
              </tr>
            </table>
            <p> 
            <table width="100%" border=0 cellpadding=2 cellspacing=1 bgcolor="#000000">
              <tr align="center" bgcolor="#C0C0C0"> 
                <td><b>Mailbox</b></td>
                <td><b>Messages</b></td>
                <td><b>New</b></td>
                <td><b>Size</b></td>
              </tr>
              <?
for($i = 0; $i < count($boxes); $i++) {
  ?> 
              <tr bgcolor="#FFFFFF"> 
                <td><a href="mailbox.php?folder=<? echo urlencode($boxes[$i][name]); ?>"><? echo $boxes[$i][name]; ?></a></td>
                <td><? echo $boxes[$i][msgs]; ?></td>
                <td><? echo $boxes[$i][unread]; ?></td>
                <td><? echo $boxes[$i][size]; ?></td>
              </tr>
              <?
}
?> 
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
</body>
</html>
