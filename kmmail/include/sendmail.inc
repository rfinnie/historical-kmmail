<?php
// @(#) $Id: sendmail.inc,v 1.3 2001/03/06 05:49:28 ryan Exp $

class km_sendmail {

  // The messae the user sees if his client cannot support multipart/mixed
  var $non_mime = "If you see this text, your email client is not MIME compliant.";
  // MIME type of body -- text/html or text/plain
  var $email_type = "text/html";
  // Cc value
  var $cc;
  // From value
  var $sender;
  // To value
  var $recipient;
  // Subject value
  var $subject;
  // Finished message to eventually go to sendmail
  var $message;
  // Finished headers to eventually go to sendmail
  var $email_headers;
  // User's body
  var $body;
  // Array of attachments 
  var $attachments;
  // Array of folders and uids to be included at message/rfc822 attachments
  var $rfc822_messages;

  function get_unique_separator() {
    return "--=_" . md5(microtime());
  }

  function encode($data) {
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

  function add_attachment($separator, $num) {
    // Get the attachment info from the property
    // $this->attachments[0]["data"] -> changed to "tmp_name"
    // $this->attachments[0]["name"]
    // $this->attachments[0]["type"]
    // $this->attachments[0]["size"]
    $uploaded_file = $this->attachments[$num]["tmp_name"];
    $attach_name = $this->attachments[$num]["name"];
    $attach_type = $this->attachments[$num]["type"];
    $binary_data = $this->get_object_data($uploaded_file);
    $this->message .= "--$separator\n";
    if(strtolower($attach_type) != "message/rfc822") {
      $this->message .= "Content-Type: $attach_type; NAME=\"$attach_name\"\n";
      $this->message .= "Content-Transfer-Encoding: BASE64\n";
      $this->message .= "Content-Disposition: attachment; filename=\"$attach_name\"\n\n";
      $this->message .= $this->encode($binary_data);
    } else {
      $this->message .= "Content-Type: message/rfc822\n\n";
      $this->message .= $binary_data;
    }
    $this->message .= "\n";

    return "";
  }

  function add_rfc822_message($separator, $num) {
    $folder = $this->rfc822_messages[$num]["folder"];
    $msgnum = $this->rfc822_messages[$num]["msgnum"];
// //print "<pre>";
// //print_r($this->rfc822_messages);
// //print "</pre>";
    $body = $this->imap->retrieve_message_body($folder, $msgnum);
    $headers = $this->imap->retrieve_message_headers_text($folder, $msgnum);

    $headers = preg_replace("/\r/", "", $headers);
    $headers = preg_replace("/^From /", "X-Original: ", $headers);
    $headers = preg_replace("/\n([ |\t]+)/", " ", $headers);
    $headers = preg_replace("/^Received: (.*?)$/m", "", $headers);
    while(preg_match("/\n\n/", $headers)) {
      $headers = preg_replace("/\n\n/", "\n", $headers);
    }
    $headers .= "\n";
    $body = preg_replace("/\r/", "", $body);

    $this->message .= "--$separator\n";
    $this->message .= "Content-Type: message/rfc822\n";
//    $this->message .= "Content-Disposition: attachment\n";
    $this->message .= "\n";
    $this->message .= $headers . $body;
    $this->message .= "\n\n";
    return "";
  }


  function build_msg_mixed() {

    // Set the top level header
    $this->email_headers = "MIME-Version: 1.0\n";
    $unique_separator = $this->get_unique_separator();
    $this->email_headers .= "Content-Type: multipart/mixed; boundary=\"$unique_separator\"";

    // Add the apropriate header
    $this->message = $this->non_mime . "\n\n";
    $this->message .= "--$unique_separator\n";
    $this->message .= "Content-Type: ".$this->email_type."; charset=\"iso-8859-1\"\n";
//    $this->message .= "Content-Transfer-Encoding: base64\n";
    $this->message .= "\n";
//    $this->message .= $this->encode($this->body . "\n");
    $this->message .= stripslashes($this->body) . "\n";
    $this->message .= "\n\n";
    for ($i = 0; $i < count($this->attachments); $i++)
      $this->add_attachment($unique_separator, $i);
    for ($i = 0; $i < count($this->rfc822_messages); $i++)
      $this->add_rfc822_message($unique_separator, $i);

    $this->message .= "--$unique_separator--";

    return "";
  }

  function check_mime() {
    $num = count($this->attachments) + count($this->rfc822_messages);

    $this->build_msg_mixed();
/*
      if (($num == 0) && ($this->email_type == "text"))
        $this->build_plain_text();
      elseif (($num > 0) && ($this->email_type == "text"))
        $this->build_plain_mixed();
      elseif (($num == 0) && ($this->email_type == "html"))
        $this->build_alternative();
      elseif (($num > 0) && ($this->email_type == "html"))
        $this->build_mixed();
*/

    return "";
  }

  /*-------------------------------------------------------------------------------------------------
   * Function to send the E-mail
   *
   * parameters : IN : 	$email_body  : E-mail message body
   *                    $recipient   : Array with the information of the recipient
   *                    $ad_id       : Array with all the Advertisements
   *                    $attach      : Array with all the filenames to attach
   *                    $sender      : Information of the Sender
   *                    $subject     : Subject of the E-mail, if any
   *                    $email_type  : options -> "text" or "html"
   *
   * returned value :	"" if okay
   *-----------------------------------------------------------------------------------------------*/
  function build_message($email_body, $recipient, $cc, $attach, $sender, $subject, $email_type = '', $rfc822_messages = "") {
    $this->body = $email_body;
    $this->subject = $subject;
    if($email_type == "html") {
      $this->email_type = "text/html";
    } else {
      $this->email_type = "text/plain";
    }
    if (is_array($attach))
      $this->attachments = $attach;
    if (is_array($rfc822_messages))
      $this->rfc822_messages = $rfc822_messages;
    $this->recipient = $recipient;
    $this->cc = $cc;
    $this->sender = $sender;

    $this->check_mime();

    return $this->send();

//    return "";
  }

  function send() {
    $extra = "From: ".$this->sender."\n";
    if($this->cc) {
      $extra .= "Cc: ".stripslashes($this->cc)."\n";
    }
    $extra .= "Sender: ".$this->sender."\n";
    $this->email_headers = $extra . $this->email_headers;

// //print "<pre>u ".$this->subject." u" . $this->email_headers . " yyy " . $this->message . "</pre>";
    mail(stripslashes($this->recipient), stripslashes($this->subject), $this->message, $this->email_headers);

  }

  function add_sent_mail() {
    global $config;
    $sent = "To: ".stripslashes($this->recipient)."\nSubject: ".stripslashes($this->subject)."\nDate: ".gmdate("D, j M Y h:i:s -0000")."\n" . $this->email_headers . "\n" . $this->message;
    
    if(!in_array($config[imap_sentbox], $this->imap->retrieve_mailboxes_short())) {
      $this->imap->create_mailbox($config[imap_sentbox]);
    }
    $this->imap->append_mailbox($config[imap_sentbox], $sent);
  }
}
?>
