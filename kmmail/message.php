<?
// @(#) $Id: message.php,v 1.12 2001/09/06 23:12:04 ryanf Exp $
include_once('include/message_show.inc');
include_once('include/misc.inc');
include_once('include/auth.inc');
include_once('include/imap.inc');

$folder = ($folder ? $folder : $config[imap_mainbox]);
list($imap, $username) = check_imap_auth($folder);

$msginfo = $imap->retrieve_message_info($msgno);


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><? echo $config[title]; ?> - Read Message</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<link rel="stylesheet" href="css/style-xhtml-strict.css" type="text/css" />
<? if($config['use_download_disclaimer']) { ?>
<script language="javascript">
<!--
function dlSentry() {
  var string = "<? echo $config['download_disclaimer_text']; ?>";
  return confirm(string);
}
// -->
</script>
<? } ?>
</head>
<body class="normal">
<table border="0" cellpadding="1" cellspacing="0" width="100%" class="backblack">
  <tr> 
    <td> 
      <table border="0" cellpadding="5" cellspacing="0" width="100%" class="main">
        <tr> 
          <td class="titleheader"> 
            <table border="0" cellpadding="0" cellspacing="0" width="100%" class="titlebar">
              <tr> 
                <td align="left"><img src="images/titleleft.gif" width="48" height="26" alt="*" class="normal" /></td>
                <td class="titleheader"><? echo $config[title]; ?> - Read Message</td>
                <td align="right"><img src="images/titleright.gif" width="48" height="26" alt="*" class="normal" /></td>
              </tr>
            </table>
          </td>
        </tr>
        <tr> 
          <td class="normal"> 
            <table width = "100%" border="0" cellpadding="1" cellspacing="1" class="backblack">
              <tr align="center"> 
                <td class="toolbar"> |
                  <a href="mailbox.php?folder=<? echo urlencode($folder); ?>">Mailbox</a> |
                  <? if(!$config['is_pop3']) { ?>
                  <a href="folders.php?folder=<? echo urlencode($folder); ?>">Folders</a> |
                  <? } ?> 
                  <a href="compose.php?folder=<? echo urlencode($folder); ?>">Compose</a> |
                  <a href="compose.php?action=reply&amp;folder=<? echo urlencode($folder); ?>&amp;msgno=<? echo $msgno; ?>">Reply</a> |
                  <a href="compose.php?action=forward&amp;folder=<? echo urlencode($folder); ?>&amp;msgno=<? echo $msgno; ?>">Forward</a> |
                </td>
              </tr>
            </table>
            <p /> 
            <table width="100%" border="0" cellpadding="3" cellspacing="1" class="backblack">
              <tr> 
                <td class="light"> 
                  <table border="0" cellpadding="0" cellspacing="0">
                    <tr class="normal"> 
                      <td><b>From: </b></td>
                      <td><a href="compose.php?to=<? echo $msginfo[from_address]; ?>"><? echo $msginfo[from_name]; ?></a></td>
                    </tr>
                    <?
if(count($msginfo[to_array]) > 0) {
  ?> 
                    <tr class="normal"> 
                      <td><b>To: </b></td>
                      <td> <?
  for($i = 0; $i < count($msginfo[to_array]); $i++) {
    ?> <a href="compose.php?to=<? echo $msginfo[to_array][$i][address]; ?>"><? echo $msginfo[to_array][$i][name]; ?></a><br />
                        <?
  }
  ?> </td>
                    </tr>
                    <?
}

if(count($msginfo[cc_array]) > 0) {
  ?> 
                    <tr class="normal"> 
                      <td><b>Cc: </b></td>
                      <td> <?
  for($i = 0; $i < count($msginfo[cc_array]); $i++) {
    ?> <a href="compose.php?to=<? echo $msginfo[cc_array][$i][address]; ?>"><? echo $msginfo[cc_array][$i][name]; ?></a><br />
                        <?
  }
  ?> </td>
                    </tr>
                    <?
}
?> 
                    <tr class="normal"> 
                      <td><b>Subject: </b></td>
                      <td><? echo $msginfo[subject]; ?></td>
                    </tr>
                    <tr class="normal"> 
                      <td><b>Date: </b></td>
                      <td><? echo $msginfo[date]; ?></td>
                    </tr>
                  </table>
                </td>
              </tr>
              <tr class="white"> 
                <td><?
$struct = imap_fetchstructure($imap->mbox, $msgno, FT_UID);
$message_show = new km_message_show();
$message_show->display_message($imap->mbox, $folder, $msgno, $struct);
?></td>
              </tr>
              <tr class="white"> 
                <td> <? if($show_headers != "on") { ?><a href="<? echo $PHP_SELF; ?>?folder=<? echo $folder; ?>&amp;msgno=<? echo $msgno; ?>&amp;show_headers=on">Show 
                  Headers</a><br />
                  <? } ?> <?
if($show_headers == "on") {
  $link = "$PHP_SELF?folder=$folder&amp;msgno=$msgno&amp;show_headers=off";
  ?> 
                  <table width="100%" border="0" cellpadding="3" cellspacing="1" class="backblack">
                    <tr> 
                      <td class="toolbar"><b>Message Headers (<a href="<? echo $link; ?>">Hide</a>)</b></td>
                    </tr>
                    <tr> 
                      <td class="light"><tt><? echo nl2br(htmlentities($imap->retrieve_message_headers_text($folder, $msgno))); ?></tt></td>
                    </tr>
                  </table>
                  <?
}
?> </td>
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

