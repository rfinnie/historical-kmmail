<?
// @(#) $Id: imap.inc,v 1.8 2001/04/02 06:06:51 ryan Exp $

include_once('include/settings.inc');

class km_imap {
  var $host;
  var $port;
  var $username;
  var $password;
  var $mbox;
  var $config;

  function km_imap($username, $password) {
    global $config;
    $this->config = $config;
    $username = get_real_username($username);
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

  function create_imap_string($folder = '') {
    if($folder == '') {
      return "{" . $this->config['imap_host'] .":" . $this->config['imap_port'] . "}";
    } elseif($folder == $this->config[imap_mainbox]) {
      return "{" . $this->config['imap_host'] .":" . $this->config['imap_port'] . "}" . $folder;
    } else {
      return "{" . $this->config['imap_host'] .":" . $this->config['imap_port'] . "}" . $this->config[imap_mailpath] . $folder;
    }
  }

  function create_imap_string_short($folder) {
    if($folder == $this->config[imap_mainbox]) {
      return $folder;
    } else {
      return $this->config[imap_mailpath] . $folder;
    }
  }

  function connect($folder) {
    $this->mbox = imap_open($this->create_imap_string($folder), $this->username, $this->password);
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

  function append_mailbox($folder, $text) {
    return imap_append($this->mbox, $this->create_imap_string($folder), $text);
  }

  function retrieve_mailboxes_short() {
    $imap_string = $this->create_imap_string();
    $list = imap_listmailbox($this->mbox, $imap_string . $this->config[imap_mailpath], '*');
    $list[] = $imap_string . "INBOX";
    sort($list);
    $out = array();
    for($i = 0; $i < count($list); $i++) {
      $name = str_replace($imap_string, "", $list[$i]);
      $name = str_replace($this->config[imap_mailpath], "", $name);
      $out[] = $name;
    }
    return $out;
  }

  function retrieve_mailboxes() {
    $imap_string = $this->create_imap_string();
    $list = imap_listmailbox($this->mbox, $imap_string . $this->config[imap_mailpath], '*');
    $list[] = $imap_string . "INBOX";
    sort($list);
    $out = array();
    for($i = 0; $i < count($list); $i++) {
      $name = str_replace($imap_string, "", $list[$i]);
      $name = str_replace($this->config[imap_mailpath], "", $name);
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

  function retrieve_message_info($msgnum) {
    $msg = imap_header($this->mbox, imap_msgno($this->mbox, $msgnum));
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

  function delete_messages($msgs) {
    for($i = 0; $i < count($msgs); $i++) {
      imap_delete($this->mbox, $msgs[$i], FT_UID);
    }
  }

  function move_messages($old_folder, $msgs, $new_folder) {
    if($old_folder != $new_folder) {
      $this->select_folder($old_folder);
      $msgss = implode(',', $msgs);
      imap_mail_move($this->mbox, $msgss, $this->create_imap_string_short($new_folder), CP_UID);
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
  
  function select_folder($folder) {
    imap_reopen($this->mbox, $this->create_imap_string($folder));
  }

  function retrieve_message_headers_text($folder, $msgnum) {
    $this->select_folder($folder);
    return imap_fetchheader($this->mbox, $msgnum, FT_UID);
  }

  function retrieve_message_body($folder, $msgnum) {
    $this->select_folder($folder);
    return imap_body($this->mbox, $msgnum, FT_UID);
  }

  function create_mailbox($folder) {
    imap_createmailbox($this->mbox, $this->create_imap_string($folder));
  }

  function delete_mailbox($folder) {
    imap_deletemailbox($this->mbox, $this->create_imap_string($folder));
  }

  function rename_mailbox($oldfolder, $newfolder) {
    imap_renamemailbox($this->mbox, $this->create_imap_string($oldfolder), $this->create_imap_string($newfolder));
  }

 function retrieve_all_headers_array($folder, $msgnum) {
    $this->select_folder($folder);
    $headers_text = imap_fetchheader($this->mbox, $msgnum, FT_UID);
    $headers_text = preg_replace("/\r/", "\n", $headers_text);
    $headers_text = preg_replace("/\n+/", "\n", $headers_text);
    $headers_array = preg_split("/\n/", $headers_text);
    $headers = array();
    for($i = 0; $i < count($headers_array); $i++) {
      if($headers_array[$i]) {
        if(preg_match("/^\s+/", $headers_array[$i])) {
          $headers[strtolower($lastname)] .= " " . preg_replace("/^\s+/", "", $headers_array[$i]);
        } else {
          list($name, $value) = preg_split("/:\s+/", $headers_array[$i], 2);
          $headers[strtolower($name)] = $value;
          $lastname = $name;
        }
      }
    }
    return $headers;
  }
}

function pre_print_r($array) {
  print "<pre>";
  print_r($array);
  print "</pre>\n";
}
?>
