<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>kmMail</title>
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
                <td class="titleheader">kmMail</td>
                <td align="right"><img src="images/titleright.gif" width="48" height="26" alt="*" class="normal" /></td>
              </tr>
            </table>
          </td>
        </tr>
        <tr> 
          <td class="normal"> 
            <table width="100%" border="0" cellpadding="1" cellspacing="1" class="backblack">
              <tr align="center"> 
                <td class="toolbar"> | <a href="http://www.sourceforge.net/">SourceForge</a> 
                  | <a href="https://sourceforge.net/projects/kmmail/">Project</a> 
                  | <a href="#sshots">Screen Shots</a> | <a href="#announce">Announcements</a> 
                  | <a href="#users">Users</a> |</td>
              </tr>
            </table>
            <table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr valign="top"> 
                <td width="200"> 
                  <table width="100%" border="0" cellpadding="3" cellspacing="1" class="backblack">
                    <tr> 
                      <td class="toolbar"><b>SourceForge Stats</b></td>
                    </tr>
                    <tr> 
                      <td class="light"> 
                        <? include('http://sourceforge.net/export/projhtml.php?group_id=32721&mode=full&no_table=1'); ?>
                      </td>
                    </tr>
                  </table>
                </td>
                <td> 
                  <table width="100%" border="0" cellpadding="3" cellspacing="1" class="backblack">
                    <tr> 
                      <td class="toolbar"><b>News</b></td>
                    </tr>
                    <tr> 
                      <td class="light"> 
                        <? include('http://sourceforge.net/export/projnews.php?group_id=32721&limit=5&flat=1&show_summaries=1'); ?>
                      </td>
                    </tr>
                  </table>
                  <br>
                  <table width="100%" border="0" cellpadding="3" cellspacing="1" class="backblack">
                    <tr> 
                      <td class="toolbar"><a name="sshots"></a><b>Screen Shots</b></td>
                    </tr>
                    <tr> 
                      <td class="light"> 
                        <p>Unfortunately, I am unable to provide a demo environment 
                          to test kmMail. I used to provide this service, but 
                          it was abused too often. I encourage you to download 
                          kmMail, but hopefully these screen shots will also help.</p>
                        <ul>
                          <li><a href="sshots/welcome.gif">The welcome screen, 
                            which of course can be easily customized.</a></li>
                          <li><a href="sshots/messages.gif">The mailbox. This 
                            shot demonstrates three mail states (read, unread 
                            and deleted) and four mail attributes (HTML parts, 
                            regular attachments, message attachments, and replied 
                            to).</a></li>
                          <li><a href="sshots/read_message.gif">The message reading 
                            window.</a></li>
                          <li><a href="sshots/folders.gif">The folders window.</a></li>
                          <li><a href="sshots/compose.gif">The compose window. 
                            Note my poor attempt at humor.</a></li>
                          <li><a href="sshots/multipart-related.gif">A demonstration 
                            of embedded images in messages.</a></li>
                          <li><a href="sshots/attachments.gif">A warning popup 
                            can be activated to warn users before downloading 
                            attachments.</a></li>
                        </ul>
                      </td>
                    </tr>
                  </table>
                  <br>
                  <table width="100%" border="0" cellpadding="3" cellspacing="1" class="backblack">
                    <tr> 
                      <td class="toolbar"><a name="announce"></a><b>Announcements</b></td>
                    </tr>
                    <tr> 
                      <td class="light"> 
                        <p>I provide an announcement list for new versions of 
                          kmMail. This list is extremely low volume, and is encouraged 
                          for anybody who uses kmMail.</p>
                        <form method=POST action="http://lists.sourceforge.net/lists/subscribe/kmmail-announce">
                          <table border="0" cellspacing="2" cellpadding="2" width="100%">
                            <tr> 
                              <td align="right" width="55%"><b>Your email address:</b></td>
                              <td width="33%"> 
                                <input type="Text" name="email" size="30">
                              </td>
                              <td width="12%">&nbsp;</td>
                            </tr>
                            <tr> 
                              <td colspan="3">You must enter a privacy password. 
                                This provides only mild security, but should prevent 
                                others from messing with your subscription. <b>Do 
                                not use a valuable password</b> as it will occasionally 
                                be emailed back to you in cleartext. Once a month, 
                                your password will be emailed to you as a reminder. 
                              </td>
                            </tr>
                            <tr> 
                              <td align="right"><b>Pick a password:</b></td>
                              <td> 
                                <input type="Password" name="pw" size="15">
                              </td>
                              <td rowspan="2"> 
                                <input type="Submit" name="email-button" value="Subscribe">
                              </td>
                            </tr>
                            <tr> 
                              <td align="right"><b>Reenter password to confirm:</b></td>
                              <td> 
                                <input type="Password" name="pw-conf" size="15">
                                <input type=hidden name="digest" value="0">
                              </td>
                            </tr>
                          </table>
                        </form>
                        <p>You can administer this page by using the "Mailing 
                          Lists" link at the left.</p>
                      </td>
                    </tr>
                  </table>
                  <br>
                  <table width="100%" border="0" cellpadding="3" cellspacing="1" class="backblack">
                    <tr> 
                      <td class="toolbar"><a name="users"></a><b>kmMail Users 
                        Around The World</b></td>
                    </tr>
                    <tr> 
                      <td class="light"> I am still working on gathering a kmMail 
                        users list. If you use kmMail on your site and would like 
                        to be listed here, please follow <a href="http://sourceforge.net/survey/survey.php?group_id=32721&survey_id=12198">this 
                        link</a> to a survey for kmMail installers. There is a 
                        text field to leave your site name and address. I also 
                        encourage you to sign up for the newsletter above.</td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
</body>
</html>



