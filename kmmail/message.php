<?
// @(#) $Id: message.php,v 1.3 2001/03/05 15:17:32 ryan Exp $
include_once('include/misc.inc');
check_cookie(&$username, &$password);

include_once('include/imap.inc');
include_once('include/message_show.inc');
$imap = new km_imap($username, $password);
$folder = ($folder ? $folder : $config[imap_mainbox]);
$imap->connect($folder);
$msginfo = $imap->retrieve_message_info($msgno);
#$msgbody = $imap->retrieve_message_body($msgno);


?>
<html>
<head>
<title>kmMail - Read Message</title>
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
                  <div class="header1">kmMail - Read Message</div>
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
                <td bgcolor="#C0C0C0">&nbsp;<a href="folders.php">Folders</a>&nbsp;</td>
                <td bgcolor="#C0C0C0">&nbsp;<a href="compose.php">Compose</a>&nbsp;</td>
                <td bgcolor="#C0C0C0">&nbsp;<a href="compose.php?action=reply&folder=<? echo urlencode($folder); ?>&msgno=<? echo $msgno; ?>">Reply</a>&nbsp;</td>
                <td bgcolor="#C0C0C0">&nbsp;<a href="compose.php?action=forward&folder=<? echo urlencode($folder); ?>&msgno=<? echo $msgno; ?>">Forward</a>&nbsp;</td>
                <td bgcolor="#C0C0C0">&nbsp;<a href="logout.php">Logout</a>&nbsp;</td>
              </tr>
            </table>
            <p> 
            <table width="100%" border=0 cellpadding=3 cellspacing=1 bgcolor="#000000">
              <tr> 
                <td bgcolor="#F0F0F0"> 
                  <table border=0 cellpadding=0 cellspacing=0>
                    <tr> 
                      <td><b>From: </b></td>
                      <td><a href="compose.php?to=<? echo $msginfo[from_address]; ?>"><? echo $msginfo[from_name]; ?></a></td>
                    </tr>
                    <?
if(count($msginfo[to_array]) > 0) {
  ?> 
                    <tr> 
                      <td><b>To: </b></td>
                      <td> <?
  for($i = 0; $i < count($msginfo[to_array]); $i++) {
    ?> <a href="compose.php?to=<? echo $msginfo[to_array][$i][address]; ?>"><? echo $msginfo[to_array][$i][name]; ?></a><br>
                        <?
  }
  ?> </td>
                    </tr>
                    <?
}

if(count($msginfo[cc_array]) > 0) {
  ?> 
                    <tr> 
                      <td><b>Cc: </b></td>
                      <td> <?
  for($i = 0; $i < count($msginfo[cc_array]); $i++) {
    ?> <a href="compose.php?to=<? echo $msginfo[cc_array][$i][address]; ?>"><? echo $msginfo[cc_array][$i][name]; ?></a><br>
                        <?
  }
  ?> </td>
                    </tr>
                    <?
}
?> 
                    <tr> 
                      <td><b>Subject: </b></td>
                      <td><? echo $msginfo[subject]; ?></td>
                    </tr>
                    <tr> 
                      <td><b>Date: </b></td>
                      <td><? echo $msginfo[date]; ?></td>
                    </tr>
                  </table>
                </td>
              </tr>
              <tr> 
                <td bgcolor="#FFFFFF"><?
$struct = imap_fetchstructure($imap->mbox, $msgno, FT_UID);
$message_show = new km_message_show();
$message_show->display_message($imap->mbox, $folder, $msgno, $struct);
?></td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
</body>
</html>
<?
$imap->disconnect();
?>

