<?
// @(#) $Id: login.php,v 1.1.1.1.8.1 2002/11/25 07:49:17 ryanf Exp $

// WARNING: This file is included from within a function in auth.inc.php
// and therefor does not have global status.

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Login</title>
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
                <td class="titleheader">Login</td>
                <td align="right"><img src="images/titleright.gif" width="48" height="26" alt="*" class="normal" /></td>
              </tr>
            </table>
          </td>
        </tr>
        <tr> 
          <td class="normal" align="center">
            <?
            if($auth_error) {
              ?>
              <i>Login Error: <? echo $auth_error; ?></i>
              <?
            }
            ?>
            <form method="post" action="<? echo $_SERVER['PHP_SELF']; ?>">
              <input type="hidden" name="mainauth" value="true" />
              <table border="0">
                <tr>
                  <td><b>Username:</b></td>
                  <td><input size="12" name="username" /></td>
                </tr>
                <tr>
                  <td><b>Password:</b></td>
                  <td><input size="12" type="password" name="password" /></td>
                </tr>
                <?
                $akeys = array_keys($profiles);
                if(count($akeys) > 1) {
                  ?>
                  <tr>
                    <td><b>Profile:</b></td>
                    <td>
                      <select name="profile">
                      <?
                      for($i = 0; $i < count($akeys); $i++) {
                        ?>
                        <option value="<? echo $akeys[$i]; ?>"><? echo $akeys[$i]; ?></option>
                        <?
                      }
                      ?>
                      </select>
                    </td>
                  </tr>
                  <?
                } else {
                  ?>
                  <input type="hidden" name="profile" value="<? echo $akeys[0]; ?>" />
                  <?
                }
                ?>
                <tr>
                  <td align="center" colspan="2"><input type="submit" value="Login"></td>
                </tr>
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
