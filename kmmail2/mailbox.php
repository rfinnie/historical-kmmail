<?
// @(#) $Id: mailbox.php,v 1.2.8.2 2002/11/25 22:02:50 ryanf Exp $
include_once('include/misc.inc.php');
include_once('include/auth.inc.php');
include_once('include/imap.inc.php');

if($folder) {
  $_SESSION['browse_folder'] = $folder;
}
$folder = $_SESSION['browse_folder'];


list($imap, $username) = check_imap_auth();
$imap->select_folder($folder);

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
} elseif($action_jump) {
  header("Location: mailbox.php?folder=$jump_folder");
} elseif($action_move) {
  if(!is_array($delete_msg)) { $delete_msg = array(); }
  if(!is_array($undelete_msg)) { $undelete_msg = array(); }
  $move_msg = array_merge(array_keys($delete_msg), array_keys($undelete_msg));
  $imap->move_messages($folder, $move_msg, $move_folder);
  header("Location: mailbox.php");
}
$offset = ($offset ? $offset : 1);
$return = $config['mailbox_page_size'];
$count = $imap->retrieve_num_messages();
$msgs = $imap->retrieve_message_list($offset, $return);
$boxes = $imap->retrieve_mailboxes_short();
$imap->disconnect();
$today = date("m/d/Y");
  ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><? echo $config[title]; ?> - Messages (<? echo $folder; ?>)</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<link rel="stylesheet" href="css/style-xhtml-strict.css" type="text/css" />
<? if($config['use_expunge_disclaimer']) { ?>
<script language="javascript">
<!--
function expSentry() {
  var string = "<? echo $config['expunge_disclaimer_text']; ?>";
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
                <td class="titleheader"><? echo $config[title]; ?> - Messages (<? echo $folder; ?>)</td>
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
                  <? if($offset > 1) { $newoffset = (($offset - $return) < 1 ? 0 : ($offset - $return)); ?>
                  <a href="mailbox.php?folder=<? echo urlencode($folder); ?>&amp;offset=<? echo $newoffset; ?>">&lt;&lt;</a> |
                  <? } else { ?>
                  &lt;&lt; |
                  <? } ?>
                  <a href="mailbox.php?folder=<? echo urlencode($folder); ?>">Mailbox</a> |
                  <? if($config['display_folders']) { ?>
                  <a href="folders.php">Folders</a> |
                  <? } ?> 
                  <a href="compose.php">Compose</a> |
                  <a href="addressbook.php">Address Book</a> |
                  <? if(!$config['is_pop3']) { ?>
                  <a href="mailbox.php?action_expunge=1"<? echo ($config['use_expunge_disclaimer'] ? ' onclick="return expSentry();"' : ''); ?>>Expunge</a> |
                  <? } ?>
                  <a href="mailbox.php?mainlogout=true">Logout</a> |
                  <? if(($offset + $return) <= $count) { $newoffset = $offset + $return; ?>
                  <a href="mailbox.php?folder=<? echo urlencode($folder); ?>&amp;offset=<? echo $newoffset; ?>">&gt;&gt;</a> |
                  <? } else { ?>
                  &gt;&gt; |
                  <? } ?> 
                </td>
              </tr>
            </table>
            <p />
            <form name="mailboxform" method="post" action="<? echo $_SERVER['PHP_SELF']; ?>">
              <script language="JavaScript">
              <!--
              function checkAll() {
                for(i = 0; i < document.mailboxform.length; i++) {
                  if(document.mailboxform.elements[i].type == 'checkbox') {
                    if((document.mailboxform.elements[i].name.substring(0, 10) == 'delete_msg') ||
                      (document.mailboxform.elements[i].name.substring(0, 12) == 'undelete_msg')) {
                      document.mailboxform.elements[i].checked = document.mailboxform.checkall.checked;
                    }
                  }
                }
              }
              //-->
              </script>
              <input type="hidden" name="folder" value="<? echo $folder; ?>" />
              <table width="100%" border="0" cellpadding="2" cellspacing="1" class="backblack">
                <tr align="center" class="messagelist-top"> 
                  <td><b><input type="checkbox" name="checkall" onClick="checkAll();"></b></td>
                  <td><b><? echo (stristr($folder, $config['imap_sentbox']) ? 'To' : 'From'); ?></b></td>
                  <td><b>Subject</b></td>
                  <td><b>Size</b></td>
                  <td><b>Date</b></td>
                </tr>
                <?
for($i = 0; $i < count($msgs); $i++) {
  if(strlen($msgs[$i]['subject']) > 80) {
    $msgs[$i]['subject'] = substr($msgs[$i]['subject'], 0, 80) . '...';
  }
  if(strlen($msgs[$i]['from']) > 25) {
    $msgs[$i]['from'] = substr($msgs[$i]['from'], 0, 25) . '...';
  }
  if($msgs[$i][deleted]) {
    $bgclass = "messagelist-deleted";
  } elseif($config['is_pop3']) {
    $bgclass = "messagelist-read";
//  } elseif($msgs[$i][unread]) {
//    $bgclass = "messagelist-unread";
  } else {
    $bgclass = "messagelist-read";
  }
  ?> 
                <tr class="<? echo $bgclass; ?>"> 
                  <td> 
                    <input type="checkbox" name="<? echo ($msgs[$i][deleted] ? 'un' : ''); ?>delete_msg[<? echo $msgs[$i][msgno]; ?>]" />
                  </td>
                  <td><? echo ($msgs[$i][unread] ? '<b>' : ''); ?><a href="message.php?folder=<? echo urlencode($folder); ?>&amp;msgno=<? echo $msgs[$i][msgno]; ?>"><? echo htmlentities((stristr($folder, $config['imap_sentbox']) ? $msgs[$i][to] : $msgs[$i][from])); ?></a><? echo ($msgs[$i][unread] ? '</b>' : ''); ?></td>
                  <td> <? echo ($msgs[$i]['count_mime']['message/rfc822'] ? '<img src="images/img_envelope.gif" width="15" height="11" alt="*" class="normal" />' : ''); ?> 
                    <? echo ($msgs[$i]['count_disposition']['attachment'] ? '<img src="images/img_file.gif" width="11" height="15" alt="*" class="normal" />' : ''); ?> 
                    <? echo ($msgs[$i]['count_mime']['text/html'] ? '<img src="images/img_world.gif" width="13" height="13" alt="*" class="normal" />' : ''); ?> 
                    <? echo ($msgs[$i]['replied'] ? '<img src="images/img_replied.gif" width="7" height="10" alt="*" class="normal" />' : ''); ?> 
                    <? echo ($msgs[$i]['to_personal'] ? '+' : ''); ?>
                    <? echo ($msgs[$i][unread] ? ('<b>' . htmlentities($msgs[$i][subject]) . '</b>') : htmlentities($msgs[$i][subject])); ?>
                  </td>
                  <td><? echo km_human_readable_size($msgs[$i][size], 1); ?></td>
                  <?
                    $msg_date = date("m/d/Y", $msgs[$i]['udate']);
                    if ($msg_date == $today) {
                      $msg_date = date("g:ia", $msgs[$i]['udate']);
                    }
                  ?>
                  <td><nobr><? echo $msg_date; ?></nobr></td>
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
                  <td colspan="5" align="center">
Messages <? echo $offset; ?>-<? echo (($offset + $return) > $count ? $count : ($offset + $return - 1)); ?> of <? echo $count; ?> in <? echo $folder; ?>
                    <br />
                    <input type="submit" name="action_delete" value="Delete" /> checked messages
                    <?
  if($config['display_folders'] && (count($boxes) > 1)) {
    ?> 
                    or move to 
                    <select name="move_folder">
                      <?
    for($i = 0; $i < count($boxes); $i++) {
      if($boxes[$i] != $folder) {
        ?> 
                      <option value="<? echo $boxes[$i]; ?>"><? echo $boxes[$i]; ?></option>
                      <?
      }
    }
    ?> 
                    </select>
                    <input type="submit" name="action_move" value="Move" /><br>
                    Jump directly to
                    <select name="jump_folder">
                      <?
    for($i = 0; $i < count($boxes); $i++) {
      if($boxes[$i] != $folder) {
        ?> 
                      <option value="<? echo $boxes[$i]; ?>"><? echo $boxes[$i]; ?></option>
                      <?
      }
    }
    ?> 
                    </select>
                    <input type="submit" name="action_jump" value="Jump" /><br>
                    <?

  }
  ?>
 </td>
                </tr>
                <?
}
?> 
              </table>
            </form>
            <div align="right">
              kmMail version <? echo $config['version']; ?>
              <img src="images/img_envelope.gif" width="15" height="11" border="0" />
            </div>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
</body>
</html>
