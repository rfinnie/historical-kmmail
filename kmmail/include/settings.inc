<?
// @(#) $Id: settings.inc,v 1.17 2001/09/07 22:18:55 ryanf Exp $
$config = array(

  // Version number.
  'version' => 'VERSIONTAG',

  // Build date/time.
  'build' => 'BUILDTAG',

  // This is what will be placed after the username when sending email.
  'host' => 'host.domain.dom',

  // IMAP host to connect to.
  'imap_host' => 'localhost',

  // IMAP port to connect to.
  'imap_port' => 143,

  // Path relative to the user's home directory that mail folders are in.
  'imap_mailpath' => 'mail/',

  // Main IMAP folder.
  'imap_mainbox' => 'INBOX',

  // This option controls whether folders should be displayed.
  // If this is a POP3 connection, 0 is assumed.
  'display_folders' => 1,

  // This options controls whether copies of sent messages should be
  // deposited in 'imap_sentbox' (see below).
  // If 'display_folders' is 0, 0 is assumed here as well.
  'use_sentbox' => 1,

  // Path relative to 'imap_mailpath' where copies of sent mail go.
  'imap_sentbox' => 'sent-mail',

  // Set to 1 if kmMail should check for the real name of the user.
  'use_passwd' => 1,

  // If use_passwd is disabled or a real name can't be found, this array will
  // be used for username to real name mapping.
  'passwd_map' => array(
    'juser' => 'Joe User'
  ),

  // Title of the program as seen by the end users.  This is for display
  // purposes only; you may use whatever you want here.
  'title' => 'kmMail',

  // How many messages should be displayed in the mailbox per page?
  'mailbox_page_size' => 30,

  // Set to 1 if you want to help by sending statistical data to the kmMail
  // servers.  This will send an empty UDP packet to stats.kmmail.org
  // whenever someone logs in.  No user information is sent whatsoever; this
  // just gives us an idea of where and how often kmMail is being used.  If
  // you have any reservations about this, feel free to turn it off.
  'send_udp_stat_packet' => 1,

  // Set to 1 if you want sendmail virtual user table to username mapping.
  // If you have a virtusetable entry "joe@domain.com juser" and 'host'
  // above is "domain.com" and the end user types in "joe" and a username
  // and the correct password for "juser", kmmail will log in as "juser".
  // Email sent will appear from "joe@domain.com" instead of
  // "juser@domain.com".  This is extremely useful if you run an ISP with
  // multiple mail domains handled.  This option requires PHP to be compiled
  // with "--with-db".
  'use_virtusertable' => 0,

  // If use_virtusertable is enabled, this is the location of the
  // virtusertable db file.  In the example below, the path/filename is
  // really '/etc/mail/virtusertable.db'; PHP will add the extension.
  'virtusertable_file' => '/etc/mail/virtusertable',

  // This option allows you to specify a download disclaimer for attachments.
  'use_download_disclaimer' => 1,

  // Text to use for disclaimer.
  'download_disclaimer_text' => 'Warning! If you do not know who sent you this email, do not download the attached file. It could contain a computer virus that may compromise your system or contain questionable graphics.\n\nDo you wish to download this file?',

  // The number of minutes your timezone is offset, west of GMT is negative.
  // For example:
  //   New York:    -300 (GMT-5:00)
  //   Los Angeles: -480 (GMT-8:00)
  //   London:         0 (GMT)
  //   Sydney:       600 (GMT+10:00)
  'timezone_offset' => -480,

  // That's it!
  'the' => 'end'
);
?>
