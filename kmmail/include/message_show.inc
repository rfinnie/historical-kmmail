<?
// @(#) $Id: message_show.inc,v 1.5 2001/04/05 22:49:17 ryan Exp $

class km_message_show {
  var $cid_list;
  var $folder;
  var $msgnum;
  var $mailbox;
  var $messages_shown = 0;

  function display_message($mailbox, $folder, $msgnum, $struct, $imap_part_base = "", $isrfc822 = "") {
    $this->mailbox = $mailbox;
    $this->folder = $folder;
    $this->msgnum = $msgnum;
    $this->isrfc822 = $isrfc822;

    $this->cid_list = $this->make_cid_list($struct->parts, $imap_part_base);
    if($struct->type == 1) {
      if($struct->subtype == "MIXED") {
        $this->process_multipart_mixed($struct->parts, $imap_part_base);
      } elseif($struct->subtype == "RELATED") {
        $this->process_multipart_related($struct->parts, $imap_part_base);
      } elseif($struct->subtype == "ALTERNATIVE") {
        $this->process_multipart_alternative($struct->parts, $imap_part_base);
      }
    } else {
      $this->process_multipart_mixed(array($struct), $imap_part_base);
    }
  }

  function make_cid_list($struct_parts, $imap_part_base) {
    $cid_list = array();
    for($i = 0; $i < count($struct_parts); $i++) {
      if($imap_part_base == "") {
        $imap_part = ($i + 1);
      } else {
        $imap_part = $imap_part_base . "." . ($i + 1);
      }
      $struct = $struct_parts[$i];
      if($struct->type == 1) {
        $get_cids = $this->make_cid_list($struct->parts, $imap_part);
        for($j = 0; $j < count($get_cids); $j++) {
          $cid_list[] = $get_cids[$j];
        }
      } else {
        if($struct->ifid == 1) {
          $id = ereg_replace("<|>","",$struct->id);
          $cid_list[] = array(
            "cid" => $id,
            "imap_part" => $imap_part,
            "struct" => $struct
          );
        }
      }
    }
    return $cid_list;
  }

  function convert_encoding($struct, $text) {
    if($struct->encoding == 3) {
      $text = base64_decode($text);
    } elseif($struct->encoding == 4) {
      $text = $this->quoted_printable_decode($text);
    }
    return $text;
  }

  function process_attachment($struct, $imap_part) {
    $types = array(
      0 => "TEXT",
      1 => "MULTIPART",
      2 => "MESSAGE",
      3 => "APPLICATION",
      4 => "AUDIO",
      5 => "IMAGE",
      6 => "VIDEO",
      7 => "OTHER"
    );
    if($struct->type) {
      $type = $types[$struct->type];
    } else {
      $type = "UNKNOWN";
    }
    if($struct->subtype) {
      $subtype = $struct->subtype;
    } else {
      $subtype = "UNKNOWN";
    }
    $mime_type = strtolower($type . "/" . $subtype);
    if($struct->ifdparameters == 1) {
      for($i = 0; $i < count($struct->dparameters); $i++) {
        if(strtolower($struct->dparameters[$i]->attribute) == "filename") {
          $filename = $struct->dparameters[$i]->value;
        }
      }
    }
    if($filename == "") {
      $filename = "unknown";
    }
    if($struct->encoding == 3) {
      $encoding = "base64";
    } elseif($struct->encoding == 4) {
      $encoding = "qprint";
    } else {
      $encoding = "other";
    }
    $size = $struct->bytes;
    $folder = $this->folder;
    $msgnum = $this->msgnum;
    $download = "get_attach.php?folder=$folder&amp;msgnum=$msgnum&amp;part_no=$imap_part&amp;type=$type&amp;subtype=$subtype&amp;name=$filename&amp;encoding=$encoding";
    print "<br /><table width=\"100%\" border=\"0\" cellpadding=\"3\" cellspacing=\"1\" class=\"backblack\"><tr><td class=\"toolbar\"><img src=\"images/img_file.gif\" width=\"11\" height=\"15\" alt=\"*\" class=\"normal\"/> <b>Attachment (<a href=\"$download\">Download</a>)</b></td></tr><tr><td class=\"light\">";
    print "<strong>Filename:</strong> $filename<br />\n";
    print "<strong>Type:</strong> $mime_type<br />\n";
    print "<strong>Size:</strong> $size bytes<br />\n";
    if($struct->description) {
      print "<strong>Description:</strong> " . $struct->description . "<br />\n";
    }
    print "</td></tr></table>\n";
  }

  function process_text_html($struct, $imap_part) {
    $mailbox = $this->mailbox;
    $msgnum = $this->msgnum;
    $folder = $this->folder;
    $text = imap_fetchbody($mailbox, $msgnum, $imap_part, FT_UID);

    $text = $this->convert_encoding($struct, $text);

    $text = preg_replace("/<(\s*)title(\s*)>(.*?)<(\s*)\/(\s*)title(\s*)>/is", "", $text);
    $text = preg_replace("/<(\/*)(\s*)(html|head|title|script|body|meta)(.*?)>/is", "", $text);
    for($i = 0; $i < count($this->cid_list); $i++) {
      if(ereg($this->cid_list[$i]["cid"], $text)) {
        $cid = $this->cid_list[$i]["cid"];
        $type = strtolower($this->cid_list[$i]["struct"]->subtype);
        $imap_part = $this->cid_list[$i]["imap_part"];
        $cid_imgsrc = "get_attach.php?folder=$folder&amp;msgnum=$msgnum&amp;part_no=$imap_part&amp;type=image&amp;subtype=$type&amp;action=inline";
        $text = ereg_replace("cid:$cid", $cid_imgsrc, $text);
      }
    }
    if($this->messages_shown > 0) {
      print "<hr size=\"1\" />";
    }

    print "$text<br />\n";
    $this->messages_shown++;
  }

  function process_text_plain($struct, $imap_part) {
    $mailbox = $this->mailbox;
    $msgnum = $this->msgnum;
    if($imap_part == "") {
      $text = imap_body($mailbox, $msgnum, FT_UID);
    } else {
      $text = imap_fetchbody($mailbox, $msgnum, $imap_part, FT_UID);
    }

    $text = $this->convert_encoding($struct, $text);

    $text = htmlentities($text);
    $text = preg_replace("/\n/", "<br />", $text);
    if($this->messages_shown > 0) {
      print "<hr />";
    }

    $text = preg_replace("'(\s+)http://(.*?)(\s+)'", "\\1<a href=\"http://\\2\" target=\"_blank\">http://\\2</a>\\3" , $text);
    $text = preg_replace("'(\s+)www.(.*?)(\s+)'", "\\1<a href=\"http://www.\\2\" target=\"_blank\">www.\\2</a>\\3" , $text);
    $text = preg_replace("'([\w|-|.]+)@([\w|-|.]+)'", "<a href=\"compose.php?to=\\0\">\\0</a>" , $text);
    print "<tt>$text</tt><br />\n";
    $this->messages_shown++;
  }

  function process_multipart_related($struct_parts, $imap_part_base) {
    for($i = 0; $i < count($struct_parts); $i++) {
      if($imap_part_base == "") {
        $imap_part = ($i + 1);
      } else {
        $imap_part = $imap_part_base . "." . ($i + 1);
      }
      $struct = $struct_parts[$i];

      if($struct->type == 1) {
        if($struct->subtype == "ALTERNATIVE") {
          $this->process_multipart_alternative($struct->parts, $imap_part);
        }
      } elseif($struct->type == 0) {
        if($struct->subtype == "HTML") {
          $this->process_text_html($struct, $imap_part);
        }
      }
    }
  }

  function process_multipart_alternative($struct_parts, $imap_part_base) {
    $types = array(
      0 => "TEXT",
      1 => "MULTIPART",
      2 => "MESSAGE",
      3 => "APPLICATION",
      4 => "AUDIO",
      5 => "IMAGE",
      6 => "VIDEO",
      7 => "OTHER"
    );
    $types_array = array();
    for($i = 0; $i < count($struct_parts); $i++) {
      $struct = $struct_parts[$i];
      $types_array[$struct->subtype] = $i;
    }
    if(isset($types_array["RELATED"])) {
      $i = $types_array["RELATED"];
      $struct = $struct_parts[$i];
      if($imap_part_base == "") {
        $imap_part = ($i + 1);
      } else {
        $imap_part = $imap_part_base . "." . ($i + 1);
      }
      $this->process_multipart_related($struct->parts, $imap_part);
    } elseif(isset($types_array["HTML"])) {
      $i = $types_array["HTML"];
      $struct = $struct_parts[$i];
      if($imap_part_base == "") {
        $imap_part = ($i + 1);
      } else {
        $imap_part = $imap_part_base . "." . ($i + 1);
      }
      $this->process_text_html($struct, $imap_part);
    } elseif(isset($types_array["PLAIN"])) {
      $i = $types_array["PLAIN"];
      $struct = $struct_parts[$i];
      if($imap_part_base == "") {
        $imap_part = ($i + 1);
      } else {
        $imap_part = $imap_part_base . "." . ($i + 1);
      }
      $this->process_text_plain($struct, $imap_part);
    }
  }

  function process_multipart_mixed($struct_parts, $imap_part_base) {
    for($i = 0; $i < count($struct_parts); $i++) {
      if($imap_part_base == "") {
        $imap_part = ($i + 1);
      } else {
        $imap_part = $imap_part_base . "." . ($i + 1);
      }
      $struct = $struct_parts[$i];

      if($struct->type == 1) {
        if($struct->subtype == "RELATED") {
          $this->process_multipart_related($struct->parts, $imap_part);
        } elseif($struct->subtype == "ALTERNATIVE") {
          $this->process_multipart_alternative($struct->parts, $imap_part);
        }
      } elseif($struct->type == 0) {
        if(strtoupper($struct->disposition) == "ATTACHMENT") {
          $this->process_attachment($struct, $imap_part);
        } else {
          if($struct->subtype == "HTML") {
            $this->process_text_html($struct, $imap_part);
          } elseif($struct->subtype == "PLAIN") {
            $this->process_text_plain($struct, $imap_part);
          } else {
            $this->process_attachment($struct, $imap_part);
          }
        }
      } elseif($struct->type == 2) {
        if($struct->subtype == "RFC822") {
          $struct = $struct->parts[0];
          $msg = imap_rfc822_parse_headers(imap_fetchbody($this->mailbox, $this->msgnum, "$imap_part.0", FT_UID));
    $to_array = array();
    for($z = 0; $z < count($msg->to); $z++) {
      $to_array[$z][name] = ($msg->to[$z]->personal ? $msg->to[$z]->personal : $msg->to[$z]->mailbox . '@' . $msg->to[$z]->host);
      $to_array[$z][address] = $msg->to[$z]->mailbox . '@' . $msg->to[$z]->host;
    }
    $cc_array = array();
    for($z = 0; $z < count($msg->cc); $z++) {
      $cc_array[$z][name] = ($msg->cc[$z]->personal ? $msg->cc[$z]->personal : $msg->cc[$z]->mailbox . '@' . $msg->cc[$z]->host);
      $cc_array[$z][address] = $msg->cc[$z]->mailbox . '@' . $msg->cc[$z]->host;
    }
    $msginfo = array(
      'date' => $msg->date,
      'udate' => $msg->udate,
      'subject' => ($msg->subject ? $msg->subject : '[No Subject]'),
      'from_name' => ($msg->from[0]->personal ? $msg->from[0]->personal : $msg->from[0]->mailbox . '@' . $msg->from[0]->host),
      'from_address' => ($msg->reply_to[0]->mailbox ? $msg->reply_to[0]->mailbox . '@' . $msg->reply_to[0]->host : $msg->from[0]->mailbox . '@' . $msg->from[0]->host),
      'to_array' => $to_array,
      'cc_array' => $cc_array
    );
          print "<br />\n";
          print "<table width=\"100%\" border=\"0\" cellpadding=\"3\" cellspacing=\"1\" class=\"backblack\">\n";
$download = "get_attach.php?folder=$this->folder&amp;msgnum=$this->msgnum&amp;part_no=&amp;type=MESSAGE&amp;subtype=RFC822&amp;name=".urlencode(time().".txt")."&amp;encoding=none";

          print "<tr><td class=\"toolbar\"><img src=\"images/img_envelope.gif\" width=\"15\" height=\"11\" alt=\"*\" class=\"normal\"/> <b>Forwarded message (<a href=\"$download\">Download</a>)</b></td></tr>\n";
          print "<tr><td class=\"light\">\n";
?>
<table border="0" cellpadding="0" cellspacing="0">
  <tr class="normal">   
    <td><b>From: </b></td>
    <td><a href="compose.php?to=<? echo $msginfo[from_address]; ?>"><? echo $msginfo[from_name]; ?></a></td>
  </tr>
<?  
if(count($msginfo[to_array]) > 0) {
  ?>
  <tr class="normal">
    <td><b>To: </b></td>
    <td>
  <?    
  for($z = 0; $z < count($msginfo[to_array]); $z++) {
    ?>
      <a href="compose.php?to=<? echo $msginfo[to_array][$z][address]; ?>"><? echo $msginfo[to_array][$z][name]; ?></a><br />
    <? 
  }
  ?>
    </td>
  </tr>
<?
}

if(count($msginfo[cc_array]) > 0) {
  ?>   
  <tr class="normal">  
    <td><b>Cc: </b></td>
    <td>
  <?
  for($z = 0; $z < count($msginfo[cc_array]); $z++) {
    ?>
      <a href="compose.php?to=<? echo $msginfo[cc_array][$z][address]; ?>"><? echo $msginfo[cc_array][$z][name]; ?></a><br />
    <?
  }
  ?>     
    </td>
  </tr>
  <?
} 
?>
  <tr class="normal">
    <td><b>Subject: </b></td>
    <td><? echo $msginfo[subject]; ?></td>
  </tr> 
  <tr>
    <td><b>Date: </b></td>
    <td><? echo $msginfo[date]; ?></td>
  </tr>
</table>
<?

          print "</td></tr>\n";
          print "<tr class=\"white\"><td>\n";
          print "<p />\n";
          $message_show = new km_message_show();
          $message_show->display_message($this->mailbox, $this->folder, $this->msgnum, $struct, $imap_part);
          print "</td></tr>\n";
          print "</table>\n";
        }
      } else {
        $this->process_attachment($struct, $imap_part);
      }
    }
  }




  function find_first_text($mailbox, $msgnum, $struct, $imap_part_base = "") {
    $text = $this->find_first_text_recursive($mailbox, $msgnum, $struct->parts, $imap_part_base);
    if($text) {
      return $text;
    } else {
      if($struct->subtype == "PLAIN") {
        $text = imap_body($mailbox, $msgnum, FT_UID);
        if($struct->encoding == 3) {
          $text = base64_decode($text);
        } elseif($struct->encoding == 4) {
          $text = $this->quoted_printable_decode($text);
        }
        return $text;
      }
    }
  }
      
  function find_first_text_recursive($mailbox, $msgnum, $struct_parts, $imap_part_base = "") {
    for($i = 0; $i < count($struct_parts); $i++) {
      if($imap_part_base == "") {
        $imap_part = ($i + 1);
      } else {
        $imap_part = $imap_part_base . "." . ($i + 1);
      }
      $struct = $struct_parts[$i];
      if($struct->type == 1) {
        $found_text = $this->find_first_text_recursive($mailbox, $msgnum, $struct->parts, $imap_part);
        if($found_text) {
          return $found_text;
        }
      } else {
        if($struct->subtype == "PLAIN") {
          $text = imap_fetchbody($mailbox, $msgnum, $imap_part, FT_UID);
          if($struct->encoding == 3) {
            $text = base64_decode($text);
          } elseif($struct->encoding == 4) {
            $text = $this->quoted_printable_decode($text);  
          }
          return $text;
        } 
      }
    }
  }


  function get_message_forward($mailbox,$action,$folder,$msgnum,&$to,&$subject,&$body) {
        $msg = imap_header($mailbox,imap_msgno($mailbox, $msgnum));
        $subject = !$msg->Subject ? "[No Subject]" : $msg->Subject;
        if($action == "reply") {
          if(!preg_match("/^re:/i", $subject)) {
            $subject = "Re: $subject";
          }
        } else {
          if(!preg_match("/\(fwd\)$/i", $subject)) {
            $subject = "$subject (fwd)";
          }
        }
  
        if($action == "reply") {
          if($msg->reply_toaddress) {
            $to = htmlentities($msg->reply_toaddress);
          } elseif($msg->fromaddress) {
            $to = $msg->fromaddress;
          } elseif($msg->senderaddress) {
            $to = $msg->fromaddress;
          }
        }
    
        $struct = imap_fetchstructure($mailbox,$msgnum, FT_UID);
        $body = $this->find_first_text($mailbox, $msgnum, $struct, "");
        if($body != "") {
          $body = preg_replace("/^/m", ">", $body);
          $fromaddress = $to;
          $body = "\n\n\nOn $msg->date, $fromaddress said:\n" . $body;
        }
  }

  function quoted_printable_decode($text) {
    $text = preg_replace("/=(\r*)\n/", "", $text);
    $text = preg_replace("/=([A-Fa-f0-9]{2})/e", "pack('H2', '\\1')", $text);
    return $text;
  }
}













?>
