<?php
// @(#) $Id: sendmail.inc.php,v 1.1.1.1 2002/11/25 04:05:53 ryanf Exp $

class km_sendmail {

  // The messae the user sees if his client cannot support multipart/mixed
  var $non_mime = "  This message is in MIME format.  The first part should be readable text,\n  while the remaining parts are likely unreadable without MIME-aware tools.";
  // Cc value
  var $cc;
  // From value
  var $sender;
  // From value, email address only
  var $senderaddr;
  // To value
  var $recipient;
  // Subject value
  var $subject;
  // Array of text from user
  var $texts;
  // Array of attachments 
  var $attachments;
  // Array of folders and uids to be included at message/rfc822 attachments
  var $rfc822_messages;
  // imap.inc.php class object
  var $imap;
  // Config array
  var $config;
  // Associative array of folder and uid that this message referrences
  var $reply_referrer;
  // Final headers
  var $msgheaders;
  // Final body
  var $msgbody;

  function km_sendmail() {
    global $config;
    $this->config = $config;
  }

  function get_unique_separator() {
    return "--=_" . md5(microtime());
  }

  function encode_base64($data) {
    return chunk_split(base64_encode($data));
  }

  function get_object_data($file) {
    // Open the file
    $fp = fopen($file, "r");
    // Read all the contents
    $contents = fread($fp, filesize($file));
    fclose($fp);
  
    // Return the contents
    return $contents;
  }

  function add_attachment($num) {
    // $this->attachments[0]["tmp_name"]
    // $this->attachments[0]["name"]
    // $this->attachments[0]["type"]
    // $this->attachments[0]["size"]
    $uploaded_file = $this->attachments[$num]["tmp_name"];
    $attach_name = $this->attachments[$num]["name"];
    $attach_type = $this->attachments[$num]["type"];
    $binary_data = $this->get_object_data($uploaded_file);

    if(strtolower($attach_type) != "message/rfc822") {
      $headersout = "Content-Type: $attach_type; NAME=\"$attach_name\"\n";
      $headersout .= "Content-Transfer-Encoding: BASE64\n";
      $headersout .= "Content-Disposition: attachment; filename=\"$attach_name\"\n";
      $bodyout = $this->encode_base64($binary_data);
    } else {
      $headersout = "Content-Type: message/rfc822\n";
      $bodyout = $binary_data;
    }
    return array($headersout, $bodyout);
  }

  function add_rfc822_message($num) {
    $folder = $this->rfc822_messages[$num]["folder"];
    $msgnum = $this->rfc822_messages[$num]["msgnum"];
    $body = $this->imap->retrieve_message_body($folder, $msgnum);
    $headers = $this->imap->retrieve_message_headers_text($folder, $msgnum);

    $headers = preg_replace("/\r/", "", $headers);
    $headers = preg_replace("/^From /", "X-Original-From: ", $headers);
    $headers = preg_replace("/^Received: /m", "X-Original-Received: ", $headers);
    $headers = preg_replace("/^\n+/", "", $headers);
    while(preg_match("/\n\n/", $headers)) {
      $headers = preg_replace("/\n\n/", "\n", $headers);
    }
    $headers .= "\n";
    $body = preg_replace("/\r/", "", $body);

    $headersout = "Content-Type: message/rfc822\n";
    $bodyout = $headers . $body;
    return array($headersout, $bodyout);
  }


  function add_text($i) {
    $content_type = $this->texts[$i]['content_type'];
    $headersout = "Content-Type: $content_type; charset=\"iso-8859-1\"\n";
    $bodyout = $this->texts[$i]['message'];
    return array($headersout, $bodyout);
  }

  function build_msg_mixed() {
    $separator = $this->get_unique_separator();
    $headersout = "Content-Type: multipart/mixed; boundary=\"$separator\"\n";
    $bodyout = $this->non_mime . "\n\n";
    for ($i = 0; $i < count($this->texts); $i++) {
      list($headers, $body) = $this->add_text($i);
      $bodyout .= "--$separator\n" . $headers . "\n" . $body . "\n";
    }
    for ($i = 0; $i < count($this->attachments); $i++) {
      list($headers, $body) = $this->add_attachment($i);
      $bodyout .= "--$separator\n" . $headers . "\n" . $body . "\n";
    }
    for ($i = 0; $i < count($this->rfc822_messages); $i++) {
      list($headers, $body) = $this->add_rfc822_message($i);
      $bodyout .= "--$separator\n" . $headers . "\n" . $body . "\n";
    }
    $bodyout .= "--$separator--\n";
    return array($headersout, $bodyout);
  }

  function build_msg_single() {
    for ($i = 0; $i < count($this->texts); $i++) {
      list($headersout, $bodyout) = $this->add_text($i);
    }
    for ($i = 0; $i < count($this->attachments); $i++) {
      list($headersout, $bodyout) = $this->add_attachment($i);
    }
    for ($i = 0; $i < count($this->rfc822_messages); $i++) {
      list($headersout, $bodyout) = $this->add_rfc822_message($i);
    }
    return array($headersout, $bodyout);
  }

  function build_message() {
    global $REMOTE_ADDR;

    $num = count($this->attachments) + count($this->rfc822_messages) + count($this->texts);
    if($num == 1) {
      list($headers, $body) = $this->build_msg_single();
    } else {
      list($headers, $body) = $this->build_msg_mixed();
    }
    $topheaders = "From: ".$this->sender."\n";
    $topheaders .= "Sender: ".$this->sender."\n";
    $offset = get_offset();
    $topheaders .= "Date: ".date('D, j M Y H:i:s ')."$offset\n";
    if($this->cc) {
      $topheaders .= "Cc: ".$this->cc."\n";
    }
    if($this->reply_referrer['msgnum']) {
      $headers_array = $this->imap->retrieve_all_headers_array($this->reply_referrer['folder'], $this->reply_referrer['msgnum']);
      $topheaders .= "In-Reply-To: ".$headers_array['message-id']."\n";
    }
    $topheaders .= "X-Sender: ".$this->config['title']." via kMmail version ".$this->config['version'].", build ".$this->config['build']."\n";
    $topheaders .= "X-Originating-IP: ".$REMOTE_ADDR."\n";
    $topheaders .= "MIME-Version: 1.0\n";
    $headers = $topheaders . $headers;

    $this->msgheaders = $headers;
    $this->msgbody = $body;
  }

  function send() {
    if(ini_get('safe_mode')) {
      mail($this->recipient, $this->subject, $this->msgbody, $this->msgheaders);
    } else {
      mail($this->recipient, $this->subject, $this->msgbody, $this->msgheaders, '-f' . $this->senderaddr);
    }
  }

  function add_sent_mail() {
    $topheaders = "To: ".$this->recipient."\n";
    $topheaders .= "Subject: ".$this->subject."\n";
    $topheaders .= "Date: ".gmdate("D, j M Y h:i:s -0000")."\n";
    $this->msgheaders = $topheaders . $this->msgheaders;

    if(!in_array($this->config[imap_sentbox], $this->imap->retrieve_mailboxes_short())) {
      $this->imap->create_mailbox($this->config[imap_sentbox]);
    }
    $this->imap->append_mailbox($this->config[imap_sentbox], $this->msgheaders . "\n" . $this->msgbody);
  }
}
?>
