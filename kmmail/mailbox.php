<?
// @(#) $Id: mailbox.php,v 1.16 2001/08/24 04:30:59 ryanf Exp $
include_once('include/misc.inc');
include_once('include/auth.inc');
include_once('include/imap.inc');
  
$folder = ($folder ? $folder : $config[imap_mainbox]);   
list($imap, $username) = check_imap_auth($folder);

if($action_delete) {
  if(count($delete_msg) > 0) {
    $imap->delete_messages(array_keys($delete_msg));
  }
  if(count($undelete_msg) > 0) {
    $imap->undelete_messages(array_keys($undelete_msg));
  }
  header("Location: mailbox.php?folder=$folder");
} elseif($action_expunge) {
  $imap->expunge_messages();
  header("Location: mailbox.php?folder=$folder");
} elseif($action_move) {
  if(!is_array($delete_msg)) { $delete_msg = array(); }
  if(!is_array($undelete_msg)) { $undelete_msg = array(); }
  $move_msg = array_merge(array_keys($delete_msg), array_keys($undelete_msg));
  $imap->move_messages($folder, $move_msg, $move_folder);
  header("Location: mailbox.php?folder=$folder");
}
$offset = ($offset ? $offset : 1);
$return = 20;
$count = $imap->retrieve_num_messages();
$msgs = $imap->retrieve_message_list($offset, $return);
$boxes = $imap->retrieve_mailboxes_short();
$imap->disconnect();
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
<table border="0" cellpadding="1" cellspacing="0" width="100%" class="backblack">
  <tr> 
    <td> 
      <table border="0" cellpadding="5" cellspacing="0" width="100%" class="main">
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
                <td class="toolbar"> |
                  <a href="mailbox.php">Mailbox</a> |
                  <? if(!$config['is_pop3']) { ?>
                  <a href="folders.php">Folders</a> |
                  <? } ?> 
                  <a href="compose.php">Compose</a> |
                </td>
              </tr>
            </table>
            <p /> 
            <form method="post" action="<? echo $PHP_SELF; ?>">
              <input type="hidden" name="folder" value="<? echo $folder; ?>" />
              <table width="100%" border="0" cellpadding="2" cellspacing="1" class="backblack">
                <tr align="center" class="messagelist-top"> 
                  <td><b>&nbsp;</b></td>
                  <td><b>From</b></td>
                  <td><b>Subject</b></td>
                  <td><b>Size</b></td>
                  <td><b>Date</b></td>
                </tr>
                <?
for($i = 0; $i < count($msgs); $i++) {
  if($msgs[$i][deleted]) {
    $bgclass = "messagelist-deleted";
  } elseif($config['is_pop3']) {
    $bgclass = "messagelist-read";
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
                  <td> <? echo ($msgs[$i]['count_mime']['message/rfc822'] ? '<img src="images/img_envelope.gif" width="15" height="11" alt="*" class="normal" />' : ''); ?> 
                    <? echo ($msgs[$i]['count_disposition']['attachment'] ? '<img src="images/img_file.gif" width="11" height="15" alt="*" class="normal" />' : ''); ?> 
                    <? echo ($msgs[$i]['count_mime']['text/html'] ? '<img src="images/img_world.gif" width="13" height="13" alt="*" class="normal" />' : ''); ?> 
                    <? echo ($msgs[$i]['replied'] ? '<img src="images/img_replied.gif" width="7" height="10" alt="*" class="normal" />' : ''); ?> 
                    <? echo $msgs[$i][subject]; ?> </td>
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
} else {
  ?> 
                <tr class="messagelist-read">
                  <td colspan="5">
<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr>
<td align="left">
<?
if($offset > 1) {
  $newoffset = (($offset - $return) < 1 ? 0 : ($offset - $return));
  ?>
<a href="mailbox.php?folder=<? echo $folder; ?>&amp;offset=<? echo $newoffset; ?>">&lt;&lt;</a> 
  <?
}
?>
</td>
<td align="center">
Displaying messages <? echo $offset; ?>-<? echo (($offset + $return) > $count ? $count : ($offset + $return - 1)); ?> of <? echo $count; ?>
</td>
<td align="right">
<?
if(($offset + $return) < $count) {
  $newoffset = $offset + $return;
  ?>
<a href="mailbox.php?folder=<? echo $folder; ?>&amp;offset=<? echo $newoffset; ?>">&gt;&gt;</a> 
  <?
}
?>
</td>
</tr>
</table>
</td>
                </tr>
                <tr class="messagelist-read"> 
                  <td colspan="5"> 
                    <input type="submit" name="action_delete" value="Delete" />
                    <?
  if(!$config['is_pop3']) {
    ?> 
                    <input type="submit" name="action_expunge" value="Remove Deleted Messages" />
                    <br />
                    Move to 
                    <select name="move_folder">
                      <?
    for($i = 0; $i < count($boxes); $i++) {
      ?> 
                      <option value="<? echo $boxes[$i]; ?>"><? echo $boxes[$i]; ?></option>
                      <?
    }
    ?> 
                    </select>
                    <input type="submit" name="action_move" value="Move" />
                    <?
  }
  ?> </td>
                </tr>
                <?
}
?> 
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

