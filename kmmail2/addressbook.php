<?
// @(#) $Id: addressbook.php,v 1.1.1.1 2002/11/25 04:05:53 ryanf Exp $
include_once('include/misc.inc.php');
include_once('include/auth.inc.php');
include_once('include/imap.inc.php');
  
list($imap, $username) = check_imap_auth();

if(!in_array($config['addressbook_folder'], $imap->retrieve_mailboxes_short())) {
  $imap->create_mailbox($config['addressbook_folder']);
  $fromaddr = $username.'@'.$config['host'];
  $welcomea = array(
    'name' => 'Sample kmMail User',
    'email' => 'sample@kmmail.org',
    'org' => 'kmMail',
    'notes' => 'This is a sample address book entry.'
  );
  $welcome = "From: $fromaddr\r\nTo: $fromaddr\r\nSubject: kmMail address book Entry\r\n\r\n" . serialize($welcomea);
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
$count = $imap->retrieve_num_messages();
$msgs = $imap->retrieve_message_list(1, 10000);
  ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><? echo $config[title]; ?> - Address Book</title>
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
                <td class="titleheader"><? echo $config[title]; ?> - Address Book</td>
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
                  <a href="addressbook.php">Address Book</a> |
                  <a href="mailbox.php?mainlogout=true">Logout</a> |
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
