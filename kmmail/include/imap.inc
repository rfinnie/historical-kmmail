<?
// @(#) $Id: km_message_show.inc,v 2.2 2001/02/07 19:32:56 ryan Exp $

class km_imap {
  var $host = 'localhost';
  var $port = 143;
  var $username = '';
  var $password = '';
  var $mbox = '';

  function km_imap($username, $password) {
    $this->username = $username;
    $this->password = $password;
  }

  function check_login() {
    if($this->connect("INBOX")) {
      $this->disconnect();
      return true;
    } else {
      return false;
    }
  }

  function connect($folder) {
    $imap_string =  "{" . $this->host .":" . $this->port . "}" . $folder;
    $this->mbox = imap_open($imap_string, $this->username, $this->password);
    if($this->mbox) {
      return true;
    } else {
      return false;
    }
  }

  function disconnect() {
    imap_close($this->mbox);
    return true;
  }

  function retrieve_num_messages() {
    return imap_num_msg($this->mbox);
  }

  function retrieve_mailboxes() {
    $imap_string =  "{" . $this->host .":" . $this->port . "}";
    $list = imap_listmailbox($this->mbox, $imap_string . "mail", '*');
    $list[] = $imap_string . "INBOX";
    sort($list);
    $out = array();
    for($i = 0; $i < count($list); $i++) {
      $name = ereg_replace("{.*}", "", $list[$i]);
      imap_reopen($this->mbox, $list[$i]);
      $info=imap_mailboxmsginfo($this->mbox);
      $out[$i] = array(
        'name' => $name,
        'unread' => $info->Unread,
        'size' => $info->Size,
        'msgs' => $info->Nmsgs
      );
    }
    return $out;
  }

  function retrieve_message_info($msgno) {
    $msg = imap_header($this->mbox, imap_msgno($this->mbox, $msgno));
    $to_array = array();
    for($i = 0; $i < count($msg->to); $i++) {
      $to_array[$i][name] = ($msg->to[$i]->personal ? $msg->to[$i]->personal : $msg->to[$i]->mailbox . '@' . $msg->to[$i]->host);
      $to_array[$i][address] = $msg->to[$i]->mailbox . '@' . $msg->to[$i]->host;
    }
    $cc_array = array();
    for($i = 0; $i < count($msg->cc); $i++) {
      $cc_array[$i][name] = ($msg->cc[$i]->personal ? $msg->cc[$i]->personal : $msg->cc[$i]->mailbox . '@' . $msg->cc[$i]->host);
      $cc_array[$i][address] = $msg->cc[$i]->mailbox . '@' . $msg->cc[$i]->host;
    }
    $out = array(
      'date' => $msg->date,
      'udate' => $msg->udate,
      'subject' => ($msg->subject ? $msg->subject : '[No Subject]'),
      'from_name' => ($msg->from[0]->personal ? $msg->from[0]->personal : $msg->from[0]->mailbox . '@' . $msg->from[0]->host),
      'from_address' => ($msg->reply_to[0]->mailbox ? $msg->reply_to[0]->mailbox . '@' . $msg->reply_to[0]->host : $msg->from[0]->mailbox . '@' . $msg->from[0]->host),
      'to_array' => $to_array,
      'cc_array' => $cc_array
    );
    return $out;
  }

  function retrieve_message_body($msgno) {
    $body = imap_body($this->mbox, $msgno, FT_UID + FT_PEEK);
    return $body;
  }

  function delete_messages($msgs) {
    for($i = 0; $i < count($msgs); $i++) {
      imap_delete($this->mbox, $msgs[$i], FT_UID);
    }
  }

  function undelete_messages($msgs) {
    for($i = 0; $i < count($msgs); $i++) {
      imap_undelete($this->mbox, $msgs[$i], FT_UID);
    }
  }

  function expunge_messages() {
    imap_expunge($this->mbox);
  }

  function retrieve_message_list() {
    $out = array();
    $msg_array = imap_sort($this->mbox, "SORTARRIVAL", 1, SE_UID);
    for($i=0; $i < count($msg_array); $i++) {
      $msg = imap_header($this->mbox, imap_msgno($this->mbox, $msg_array[$i]));
      $out[] = array(
        'msgno' => $msg_array[$i],
        'date' => $msg->date,
        'subject' => ($msg->subject ? $msg->subject : '[No Subject]'),
        'from' => ($msg->from[0]->personal ? $msg->from[0]->personal : $msg->from[0]->mailbox . '@' . $msg->from[0]->host),
        'size' => $msg->Size,
        'udate' => $msg->udate,
        'unread' => ($msg->Unseen == 'U' || $msg->Recent == 'N'),
        'deleted' => ($msg->Deleted == 'D')
      );
    }
    return $out;
  }
  
}

function pre_print_r($array) {
  print "<pre>";
  print_r($array);
  print "</pre>\n";
}
?>
