<?
// @(#) $Id: sendmail.inc,v 1.2 2001/03/06 03:03:14 ryan Exp $
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
  )
);
?>
