<?
// @(#) $Id: compose.php,v 1.10 2001/04/19 06:20:33 ryan Exp $
include_once('include/misc.inc');
check_cookie($username, $password);

$rn = passwd_real_name($username);

include_once('include/imap.inc');
$imap = new km_imap($username, $password);

$folder = ($folder ? $folder : $config[imap_mainbox]);
$imap->connect($folder);

if($submit) {
  include_once('include/sendmail.inc');
  $texts_array = array();
  $texts_array[] = array(
    'content_type' => ($send_html ? 'text/html' : 'text/plain'),
    'message' => $body
  );

  $rfc822_array = array();
  if($send_rfc822 != '') {
    $rfc822_array[] = array(
      'folder' => $folder,
      'msgnum' => $msgno
    );
  }
  if($HTTP_POST_FILES['attach']['name'] == "") {
    $attach_array = array();
  } else {
    $attach_array = array($HTTP_POST_FILES['attach']);
  }
  if($rn) {
    $from = "\"$rn\" <$fromaddr>";
  } else {
    $from = $fromaddr;
  }
  $mail = new km_sendmail();
  $mail->imap = $imap;
  $mail->recipient = $to;
  $mail->sender = $from;
  $mail->cc = $cc;
  $mail->subject = $subject;
  $mail->texts = $texts_array;
  $mail->attachments = $attach_array;
  $mail->rfc822_messages = $rfc822_array;

  if($msgno) {
    $mail->reply_referrer = array(
      'folder' => $folder,
      'msgnum' => $msgno
    );
  }
  $mail->build_message();
  $mail->send();
  if(!$config['is_pop3']) {
    $mail->add_sent_mail();
  }
  header("Location: mailbox.php");
  exit;
}



  if($action != "") {
    include_once('include/message_show.inc');
    $struct = imap_fetchstructure($imap->mbox, $msgno, FT_UID);
    $message = new km_message_show();
    $message->get_message_forward($imap->mbox,$action,$folder,$msgno,$to,$subject,$body);
  }
$imap->disconnect();
  ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><? echo $config[title]; ?> - Compose</title>
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
                <td class="titleheader"><? echo $config[title]; ?> - Compose</td>
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
                <? if(!$config['is_pop3']) { ?><td class="toolbar">&nbsp;<a href="folders.php">Folders</a>&nbsp;</td><? } ?>
                <td class="toolbar">&nbsp;<a href="compose.php">Compose</a>&nbsp;</td>
                <td class="toolbar">&nbsp;Reply&nbsp;</td>
                <td class="toolbar">&nbsp;Forward&nbsp;</td>
                <td class="toolbar">&nbsp;<a href="logout.php">Logout</a>&nbsp;</td>
              </tr>
            </table>
            <p /> 
              <form enctype="multipart/form-data" method="post" action="<? echo $PHP_SELF; ?>">
            <table width="100%" border="0" cellspacing="1" cellpadding="3" class="backblack">
<?
if($msgno) {
  ?>
                <input type="hidden" name="msgno" value="<? echo $msgno; ?>" />
                <input type="hidden" name="folder" value="<? echo $folder; ?>" />
  <?
}
?>
                <tr> 
                  <td class="light"> 
                    <table border="0" cellspacing="0" cellpadding="1">
                      <tr class="normal"> 
                        <td><b>From:</b></td>
                        <td>
<?
if($rn) {
  ?>
                          "<? echo $rn; ?>" &lt;<? echo $fromaddr; ?>&gt;
  <?
} else {
  ?>
                          &lt;<? echo $fromaddr; ?>&gt;
  <?
}
?>
                        </td>
                        <td rowspan="4" align="center" valign="middle"> 
                          <input type="submit" name="submit" value="Send" />
                        </td>
                      </tr>
                      <tr class="normal"> 
                        <td><b>To:</b></td>
                        <td>
                          <input name="to" size="40" value="<? echo $to; ?>" />
                        </td>
                      </tr>
                      <tr class="normal"> 
                        <td><b>Cc:</b></td>
                        <td>
                          <input name="cc" size="40" />
                        </td>
                      </tr>
                      <tr class="normal"> 
                        <td><b>Subject:</b></td>
                        <td>
                          <input name="subject" size="40" value="<? echo $subject; ?>" />
                        </td>
                      </tr>
                      <tr class="normal"> 
                        <td><b>Attachment:</b></td>
                        <td> 
                          <input type="file" name="attach" size="40" />
                        </td>
                      </tr>
                      <tr class="normal">
                        <td align="right"><input type="checkbox" name="send_html" /></td>
                        <td>Send message in HTML</td>
                      </tr>
<?
if($msgno) {
  ?>
                      <tr class="normal">
                        <td align="right"><input type="checkbox" name="send_rfc822" /></td>
                        <td>Send original message as attachment</td>
                      </tr>
  <?
}
?>
                    </table>
                  </td>
                </tr>
                <tr class="compose"> 
                  <td> 
                    <textarea name="body" cols="69" rows="15"><? echo $body; ?></textarea>
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
