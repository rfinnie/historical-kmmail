<?
// @(#) $Id: compose.php,v 1.3 2001/03/20 06:01:19 ryan Exp $
include_once('include/misc.inc');
check_cookie(&$username, &$password);

$rn = passwd_real_name($username);

include_once('include/imap.inc');
$imap = new km_imap($username, $password);

$folder = ($folder ? $folder : $config[imap_mainbox]);
$imap->connect($folder);

if($submit) {
  include_once('include/sendmail.inc');
  if($send_rfc822 != '') {
    $rfc822[0][folder] = $folder;
    $rfc822[0][msgnum] = $msgno;
  }
  if($HTTP_POST_FILES['attach']['name'] == "") {
    $attach_array = array();
  } else {
    $attach_array = array($HTTP_POST_FILES['attach']);
  }
  if($rn) {
    $from = "\"$rn\" <".$username.'@'.$config[host].">";
  } else {
    $from = $username.'@'.$config[host];
  }
  $mail = new km_sendmail();
  $sent = $mail->build_message($body, $to, $cc, $attach_array, $from, $subject, ($send_html ? 'html' : 'plain'), $rfc822);
  $imap->append_mailbox($sent, $config[imap_sentbox]);
  header("Location: mailbox.php");
  exit;
}



  if($action != "") {
    include_once('include/message_show.inc');
    $struct = imap_fetchstructure($imap->mbox, $msgno, FT_UID);
    $message = new km_message_show();
    $message->get_message_forward($imap->mbox,$action,$folder,$msgno,&$to,&$subject,&$body);
  }
$imap->disconnect();
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
                <td bgcolor="#C0C0C0">&nbsp;<a href="mailbox.php">Mailbox</a>&nbsp;</td>
                <td bgcolor="#C0C0C0">&nbsp;<a href="folders.php">Folders</a>&nbsp;</td>
                <td bgcolor="#C0C0C0">&nbsp;Compose&nbsp;</td>
                <td bgcolor="#C0C0C0">&nbsp;Reply&nbsp;</td>
                <td bgcolor="#C0C0C0">&nbsp;Forward&nbsp;</td>
                <td bgcolor="#C0C0C0">&nbsp;<a href="logout.php">Logout</a>&nbsp;</td>
              </tr>
            </table>
            <p> 
            <table width="100%" border="0" cellspacing="1" cellpadding="3" bgcolor="#000000">
              <form enctype="multipart/form-data" method="post" action="<? echo $PHP_SELF; ?>">
<?
if($msgno) {
  ?>
                <input type="hidden" name="msgno" value="<? echo $msgno; ?>">
                <input type="hidden" name="folder" value="<? echo $folder; ?>">
  <?
}
?>
                <tr> 
                  <td bgcolor="#F0F0F0"> 
                    <table border="0" cellspacing="0" cellpadding="1">
                      <tr> 
                        <td><b>From:</b></td>
                        <td>
<?
if($rn) {
  ?>
                          "<? echo $rn; ?>" &lt;<? echo $username; ?>@<? echo $config[host]; ?>&gt;
  <?
} else {
  ?>
                          &lt;<? echo $config[host]; ?>&gt;
  <?
}
?>
                        </td>
                        <td rowspan=4 align="center" valign="middle" width="100%"> 
                          <input type="submit" name="submit" value="Send">
                        </td>
                      </tr>
                      <tr> 
                        <td><b>To:</b></td>
                        <td>
                          <input name="to" size="40" value="<? echo $to; ?>">
                        </td>
                      </tr>
                      <tr> 
                        <td><b>Cc:</b></td>
                        <td>
                          <input name="cc" size="40">
                        </td>
                      </tr>
                      <tr> 
                        <td><b>Subject:</b></td>
                        <td>
                          <input name="subject" size="40" value="<? echo $subject; ?>">
                        </td>
                      </tr>
                      <tr> 
                        <td><b>Attachment:</b></td>
                        <td> 
                          <input type="file" name="attach" size="40">
                        </td>
                      </tr>
                      <tr>
                        <td align="right"><input type="checkbox" name="send_html"></td>
                        <td>Send message in HTML</td>
                      </tr>
<?
if($msgno) {
  ?>
                      <tr>
                        <td align="right"><input type="checkbox" name="send_rfc822"></td>
                        <td>Send original message as attachment</td>
                      </tr>
  <?
}
?>
                    </table>
                  </td>
                </tr>
                <tr bgcolor="#FFFFFF"> 
                  <td> 
                    <textarea name="body" cols="80" rows="15"><? echo $body; ?></textarea>
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
