<?
// @(#) $Id: mailbox.php,v 1.28 2001/09/08 20:43:04 ryanf Exp $
include_once('include/misc.inc.php');
include_once('include/auth.inc.php');
include_once('include/imap.inc.php');
  
list($imap, $username) = check_imap_auth($config['imap_mainbox']);
if(!in_array($config['addressbook_folder'], $imap->retrieve_mailboxes_short())) {
  $imap->create_mailbox($config['addressbook_folder']);
  $fromaddr = $username.'@'.$config['host'];
  $welcomea = array(
    'name' => 'Sample kmMail User',
    'email' => 'sample@kmmail.org',
    'org' => 'kmMail',
    'notes' => 'This is a sample Addressbook entry.'
  );
  $welcome = "From: $fromaddr\r\nTo: $fromaddr\r\nSubject: kmMail Addressbook Entry\r\n\r\n" . serialize($welcomea);
  //$welcome = "X-Name: kmMail User\r\nX-Email: addressbook@kmmail.org\r\nX-Organization: kmMail\r\nX-Comment: This is an automatically-generated entry.\r\n";
  //$welcome = "To: \"kmMail User\" <addressbook@kmmail.org>\r\nSubject: kmMail\r\n\r\nThis is an automatically-generated entry.\r\n";
  $imap->append_mailbox($config['addressbook_folder'], $welcome);
}
$imap->select_folder($config['addressbook_folder']);
$addrfolder = $config['addressbook_folder'];



if($action == 'delete') {
  $imap->delete_messages(array($entry));
  header("Location: addressbook.php");
  exit();
} elseif($action == 'undelete') {
  $imap->undelete_messages(array($entry));
  header("Location: addressbook.php");
  exit();
} elseif($action_expunge) {
  $imap->expunge_messages();
  header("Location: addressbook.php");
  exit();
}
$offset = ($offset ? $offset : 1);
$return = $config['mailbox_page_size'];
$count = $imap->retrieve_num_messages();
$msgs = $imap->retrieve_message_list($offset, $return);
$boxes = $imap->retrieve_mailboxes_short();
$today = date("m/d/Y");
  ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><? echo $config[title]; ?> - Address Book</title>
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
                  <? if($config['display_folders']) { ?>
                  <a href="folders.php">Folders</a> |
                  <? } ?> 
                  <a href="compose.php">Compose</a> |
                  <? if(!$config['is_pop3']) { ?>
                  <a href="addressbook.php?action_expunge=1"<? echo ($config['use_expunge_disclaimer'] ? ' onclick="return expSentry();"' : ''); ?>>Expunge</a> |
                  <? } ?>
                </td>
              </tr>
            </table>
            <p />
            <form method="post" action="<? echo $PHP_SELF; ?>">
              <input type="hidden" name="addrfolder" value="<? echo $addrfolder; ?>" />
              <table width="100%" border="0" cellpadding="2" cellspacing="1" class="backblack">
                <tr align="center" class="messagelist-top"> 
                  <td><b>&nbsp;</b></td>
                  <td><b>Name</b></td>
                  <td><b>Address</b></td>
                  <td><b>Organization</b></td>
                  <td><b>Notes</b></td>
                </tr>
                <?
//pre_print_r($msgs);
for($i = 0; $i < count($msgs); $i++) {
  $addrentry = unserialize($imap->retrieve_message_body($addrfolder, $msgs[$i][msgno]));
  if($msgs[$i][deleted]) {
    $bgclass = "messagelist-deleted";
  } else {
    $bgclass = "messagelist-read";
  }
  ?> 
                <tr class="<? echo $bgclass; ?>"> 
                  <td align="center"> 
                    <a href="addressbook.php?entry=<? echo $msgs[$i][msgno]; ?>&amp;action=edit">Edit</a> | 
                    <? if($msgs[$i][deleted]) { ?>
                    <a href="addressbook.php?entry=<? echo $msgs[$i][msgno]; ?>&amp;action=undelete">Undelete</a>
                    <? } else { ?>
                    <a href="addressbook.php?entry=<? echo $msgs[$i][msgno]; ?>&amp;action=delete">Delete</a>
                    <? } ?>
                  </td>
                  <td><? echo $addrentry['name']; ?></td>
                  <td><a href="compose.php?to=<? echo $addrentry['email']; ?>"><? echo $addrentry['email']; ?></a></td>
                  <td><? echo $addrentry['org']; ?></td>
                  <td><? echo $addrentry['notes']; ?></td>
                </tr>
                <?
}
if($count == 0) {
  ?> 
                <tr class="messagelist-read"> 
                  <td colspan="5" align="center"><b>There are no entries in the address book.</b></td>
                </tr>
                <?
}
?>
              </table>
            </form>
            <p /> 
            <table width="100%" border="0" cellpadding="1" cellspacing="1" class="backblack">
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
