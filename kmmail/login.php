<?
// @(#) $Id: login.php,v 1.2 2001/03/03 08:36:56 ryan Exp $
include_once('include/settings.inc');

if($username) {
  include_once('include/imap.inc');
  $imap = new km_imap($username, $password, $config[imap_mainbox]);
  if($imap->check_login()) {
    session_start();
    $kmauth = array(
      "username" => $username,
      "password" => $password
    );
    session_register("kmauth");
    header("Location: mailbox.php");
    exit;
  } else {
    $login_failed = 1;
  }
}
?>
<html>
<head>
<title>kmMail - Login</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Content-Style-Type" content="text/css">
<link rel="stylesheet" href="css/style.css" type="text/css">
</head>
<body bgcolor="#FFFFFF" text="#000000" background="images/bg.gif">
<table border=0 cellpadding=1 cellspacing=0 bgcolor="#000000" width=300 align="center">
  <tr> 
    <td> 
      <table border=0 cellpadding=5 cellspacing=0 bgcolor="#DEDFD6" width=298>
        <tr> 
          <td align="center"> 
            <table border=0 cellpadding=0 cellspacing=0 width="100%" background="images/titlebg.gif">
              <tr> 
                <td align="left"><img src="images/titleleft.gif" width="48" height="26" border="0"></td>
                <td align="center"> 
                  <div class="header1">kmMail - Login</div>
                </td>
                <td align="right"><img src="images/titleright.gif" width="48" height="26" border="0"></td>
              </tr>
            </table>
          </td>
        </tr>
        <tr> 
          <td> 
            <p>Welcome to kmMail! Please enter your username and password to continue.</p>
            <table border="0" align="center">
              <form method="post" action="<? echo $PHP_SELF; ?>">
                <tr> 
                  <td>Username</td>
                  <td> 
                    <input type="text" name="username" size="12">
                  </td>
                </tr>
                <tr> 
                  <td>Password</td>
                  <td> 
                    <input type="password" name="password" size="12">
                  </td>
                </tr>
                <tr> 
                  <td></td>
                  <td> 
                    <input type="submit" value="Submit">
                  </td>
                </tr>
              </form>
            </table>
            <p>
            <div align="right"><font size="1">kmMail version <? echo $config[version]; ?>, build <? echo $config[build]; ?></font></div>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
</body>
</html>
