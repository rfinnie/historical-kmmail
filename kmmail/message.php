<?
// @(#) $Id: message.php,v 1.17 2001/09/08 01:06:28 ryanf Exp $
include_once('include/message_show.inc');
include_once('include/misc.inc');
include_once('include/auth.inc');
include_once('include/imap.inc');

$folder = ($folder ? $folder : $config[imap_mainbox]);
list($imap, $username) = check_imap_auth($folder);

list($next, $prev, $cur_pos, $num_msgs) = $imap->find_next_prev_uid($msgno);

if($action == 'delete') {
  $imap->delete_messages(array($msgno));
  if($next > -1) {
    header("Location: message.php?folder=$folder&msgno=$next");
  } else {
    header("Location: mailbox.php?folder=$folder");
  }
}

$msginfo = $imap->retrieve_message_info($msgno);


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><? echo $config[title]; ?> - Read Message</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<link rel="stylesheet" href="css/style-xhtml-strict.css" type="text/css" />
<script language="javascript">
<!--
function pwin(url) {
  w = window.open(url, 'popupwin', 'width=600,height=400,scrollbars,resizable');
  if(w.focus) { w.focus(); }
  return false;
}
// -->
</script>
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
                  <? if($prev > -1) { ?>
                  <a href="message.php?folder=<? echo urlencode($folder); ?>&amp;msgno=<? echo $prev; ?>">&lt;&lt;</a> |
                  <? } ?>
                  <a href="index.php?folder=<? echo urlencode($folder); ?>">Welcome</a> |
                  <a href="mailbox.php?folder=<? echo urlencode($folder); ?>">Mailbox</a> |
                  <? if($config['display_folders']) { ?>
                  <a href="folders.php?folder=<? echo urlencode($folder); ?>">Folders</a> |
                  <? } ?> 
                  <a href="compose.php?folder=<? echo urlencode($folder); ?>">Compose</a> |
                  <a href="compose.php?action=reply&amp;folder=<? echo urlencode($folder); ?>&amp;msgno=<? echo $msgno; ?>">Reply</a> |
                  <a href="compose.php?action=forward&amp;folder=<? echo urlencode($folder); ?>&amp;msgno=<? echo $msgno; ?>">Forward</a> |
                  <a href="message.php?action=delete&amp;folder=<? echo urlencode($folder); ?>&amp;msgno=<? echo $msgno; ?>">Delete</a> |
                  <? if($next > -1) { ?>
                  <a href="message.php?folder=<? echo urlencode($folder); ?>&amp;msgno=<? echo $next; ?>">&gt;&gt;</a> |
                  <? } ?>
                </td>
              </tr>
            </table>
            <p /> 
            <table width="100%" border="0" cellpadding="3" cellspacing="1" class="backblack">
              <tr> 
                <td class="light"> 
<table border="0" cellpadding="0" cellspacing="0" width="100%">
  <tr valign="top">
    <td align="left">
                  <table border="0" cellpadding="0" cellspacing="0">
                    <tr class="normal" valign="top"> 
                      <td><b>From: </b></td>
                      <td><a href="compose.php?to=<? echo $msginfo[from_address]; ?>"><? echo $msginfo[from_name]; ?></a></td>
                    </tr>
                    <?
if(count($msginfo[to_array]) > 0) {
  ?> 
                    <tr class="normal" valign="top"> 
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
                    <tr class="normal" valign="top"> 
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
                    <tr class="normal" valign="top"> 
                      <td><b>Subject: </b></td>
                      <td><? echo $msginfo[subject]; ?></td>
                    </tr>
                    <tr class="normal" valign="top"> 
                      <td><b>Date: </b></td>
                      <td><? echo $msginfo[date]; ?></td>
                    </tr>
                  </table>
    </td>
    <td align="right">
                  <table border="0" cellpadding="0" cellspacing="0">
                    <tr class="normal" align="right"> 
                      <td><nobr>Message <? echo $cur_pos; ?> of <? echo $num_msgs; ?></nobr></td>
                    </tr>
                    <tr class="normal" align="right"> 
                      <td><nobr>Folder: <? echo $folder; ?></nobr></td>
                    </tr>
                    <tr class="normal" align="right"> 
                      <td><nobr><a href="#" onClick="return pwin('popup.php?action=show_headers&folder=<? echo urlencode($folder); ?>&msgno=<? echo $msgno; ?>');">Show Headers</a></nobr></td>
                    </tr>
                    <tr class="normal" align="right"> 
                      <td><nobr><a href="get_attach.php?folder=<? echo $folder; ?>&amp;type=MESSAGE&amp;subtype=RFC822&amp;msgnum=<? echo $msgno; ?>&amp;name=foo.txt">Download Message</a></nobr></td>
                    </tr>
                  </table>


    </td>
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

