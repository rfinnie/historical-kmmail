<?
// @(#) $Id$
include_once('include/misc.inc');

$folder = ($folder ? $folder : $config[imap_mainbox]);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><? echo $config[title]; ?> - Welcome</title>
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
                <td class="titleheader"><? echo $config[title]; ?> - Welcome</td>
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
                  <a href="index.php?folder=<? echo urlencode($folder); ?>">Welcome</a> |
                  <a href="mailbox.php?folder=<? echo urlencode($folder); ?>">Mailbox</a> |
                  <? if($config['display_folders']) { ?>
                  <a href="folders.php?folder=<? echo urlencode($folder); ?>">Folders</a> |
                  <? } ?> 
                  <a href="compose.php?folder=<? echo urlencode($folder); ?>">Compose</a> |
                </td>
              </tr>
            </table>
            <p /> 
            <table width="100%" border="0" cellpadding="3" cellspacing="1" class="backblack">
              <tr> 
                <td class="toolbar"><b>Welcome!</b></td>
              </tr>
              <tr> 
                <td class="light"><? include('include/welcome.inc'); ?></td>
              </tr>
            </table>
            <p />
            <div align="right">
              <a href="http://www.kmmail.org/">kmMail</a> version <? echo $config['version']; ?>, build <? echo $config['build']; ?>.
            </div>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
</body>
</html>


