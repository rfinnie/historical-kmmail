<?
// @(#) $Id: login.php,v 1.7 2001/03/22 07:36:35 ryan Exp $
include_once('include/misc.inc');

if($username) {
  $imap = new km_imap($username, $password, $config[imap_mainbox]);
  if($imap->check_login()) {
    km_session_start($username, $password);

    if($config[send_udp_stat_packet]) {
      km_send_udp_stat_packet();
    }
    header("Location: mailbox.php");
    exit;
  } else {
    $login_failed = 1;
  }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><? echo $config[title]; ?> - Login</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<link rel="stylesheet" href="css/style-xhtml-strict.css" type="text/css" />
</head>
<body class="normal">
<table border="0" cellpadding="1" cellspacing="0" width="300" class="backblack">
  <tr> 
    <td> 
      <table border="0" cellpadding="5" cellspacing="0" width="298" class="main">
        <tr> 
          <td class="titleheader"> 
            <table border="0" cellpadding="0" cellspacing="0" width="100%" class="titlebar">
              <tr> 
                <td align="left"><img src="images/titleleft.gif" width="48" height="26" alt="*" class="normal" /></td>
                <td class="titleheader"><? echo $config[title]; ?> - Login</td>
                <td align="right"><img src="images/titleright.gif" width="48" height="26" alt="*" class="normal" /></td>
              </tr>
            </table>
          </td>
        </tr>
        <tr> 
          <td class="normal"> 
            <p>Welcome to <? echo $config[title]; ?>! Please enter your username 
              and password to continue.</p>
            <form method="post" action="<? echo $PHP_SELF; ?>">
              <div class="center"> 
                <table border="0">
                  <tr class="normal"> 
                    <td><b>Username</b></td>
                    <td> 
                      <input type="text" name="username" size="12" />
                    </td>
                  </tr>
                  <tr class="normal"> 
                    <td><b>Password</b></td>
                    <td> 
                      <input type="password" name="password" size="12" />
                    </td>
                  </tr>
                  <tr class="normal"> 
                    <td></td>
                    <td> 
                      <input type="submit" value="Submit" />
                    </td>
                  </tr>
                </table>
              </div>
            </form>
            <p /> 
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<table border="0" cellpadding="5" cellspacing="0" width="300">
  <tr> 
    <td> 
      <div class="kmblurb"><a class="kmblurb" href="http://www.kmmail.org/">kmMail</a> 
        version <? echo $config[version]; ?>, build <? echo $config[build]; ?><br />
        <a href="http://validator.w3.org/check/referer"><img src="images/valid-xhtml10.gif" alt="Valid XHTML 1.0!" height="31" width="88" class="normal"/></a> 
        <a href="http://jigsaw.w3.org/css-validator/check/referer"><img src="images/vcss.gif" alt="Valid CSS!" height="31" width="88" class="normal"/></a></div>
    </td>
  </tr>
</table>
</body>
</html>
