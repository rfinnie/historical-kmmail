<?
// @(#) $Id: logout.php,v 1.1 2001/03/03 07:38:28 ryan Exp $
setcookie('kmauth');
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
                  <div class="header1">kmMail - Logout</div>
                </td>
                <td align="right"><img src="images/titleright.gif" width="48" height="26" border="0"></td>
              </tr>
            </table>
          </td>
        </tr>
        <tr> 
          <td> 
            <p>Thank you for using kmMail. You are now logged off. To log in again, 
              <a href="login.php">click here</a>.</p>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
</body>
</html>
