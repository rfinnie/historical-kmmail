<?
// @(#) $Id: settings.inc,v 1.7 2001/03/21 00:48:44 ryan Exp $
$config = array(
  'version' => '1.0pre1',
  'build' => 'BUILDTAG',
  'host' => 'host.domain.dom',
  'imap_host' => 'localhost',
  'imap_port' => 143,
  'imap_mailpath' => 'mail/',
  'imap_mainbox' => 'INBOX',
  'imap_sentbox' => 'mail/sent-mail',
  'use_passwd' => 1,
  'passwd_map' => array(
    'juser' => 'Joe User'
  ),
  'default_login' => 0,
  'default_login_username' => 'username',
  'default_login_password' => 'password',
  'title' => 'kmMail',
  'send_udp_stat_packet' => 1
);
?>
