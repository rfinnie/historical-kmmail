<?
// @(#) $Id: settings.inc,v 1.10 2001/04/02 06:06:51 ryan Exp $
$config = array(

  // Version number.
  'version' => '1.0pre2',

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

  // Path relative to 'imap_mailpath' where copies of sent mail go.
  'imap_sentbox' => 'sent-mail',

  // Set to 1 if kmMail should check for the real name of the user.
  'use_passwd' => 1,

  // If use_passwd is disabled or a real name can't be found, this array will
  // be used for username to real name mapping.
  'passwd_map' => array(
    'juser' => 'Joe User'
  ),

  // Set to 1 and specify username and password if you want a default
  // username and password to be used if the session cookie cannot be found.
  // This should only be used in a testing environment.
  'default_login' => 0,

  // Default username to use if default_login is 1.
  'default_login_username' => 'username',

  // Default password to use if default_login is 1.
  'default_login_password' => 'password',

  // Title of the program as seen by the end users.  This is for display
  // purposes only; you may use whatever you want here.
  'title' => 'kmMail',

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
  'virtusertable_file' => '/etc/mail/virtusertable'
);
?>
