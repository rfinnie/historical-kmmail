<?
// @(#) $Id: message.php,v 1.14 2001/09/07 17:11:04 ryanf Exp $
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
</head>
<body class="normal">
<table border="0" cellpadding="0" cellspacing="0" width="100%" class="backblack">
  <tr> 
    <td> 
<?
if($action="show_headers") {
  ?>
                  <table width="100%" border="0" cellpadding="3" cellspacing="1" class="backblack">
                    <tr> 
                      <td class="toolbar"><b>Message Headers</b></td>
                    </tr>
                    <tr> 
                      <td class="light"><tt><? echo nl2br(htmlentities($imap->retrieve_message_headers_text($folder, $msgno))); ?></tt></td>
                    </tr>
                  </table>
  <?
}
?>
    </td>
  </tr>
</table>
</body>
</html>
<?
$imap->disconnect();
?>

