<?
// @(#) $Id: km_message_show.inc,v 2.2 2001/02/07 19:32:56 ryan Exp $

class km_message_show {
  var $cid_list = "";
  var $folder = "";
  var $msgnum = "";
  var $mailbox = "";

  var $messages_shown = 0;

  function km_message_show($mailbox, $folder, $msgnum, $struct, $imap_part_base = "") {
    $this->mailbox = $mailbox;
    $this->folder = $folder;
    $this->msgnum = $msgnum;

//    print "<pre>";
//    print_r($struct);
//    print "</pre>";

//$download = "get_attach.php?folder=$this->folder&msgnum=$this->msgnum&part_no=&type=MESSAGE&subtype=RFC822&name=".urlencode(time().".txt")."&encoding=none";
//print "<a href=\"$download\">Download</a><p>\n";
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
      if($struct->subtype == "HTML") {
        $this->process_text_html($struct, $imap_part_base);
      } elseif($struct->subtype == "PLAIN") {
        $this->process_text_plain($struct, $imap_part);
      } else {
        $this->process_attachment($struct, $imap_part);
      }
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
      $text = preg_replace("/=(\r*)\n/", "", $text);
      $text = quoted_printable_decode($text);
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
    $download = "get_attach.php?folder=$folder&msgnum=$msgnum&part_no=$imap_part&type=$type&subtype=$subtype&name=$filename&encoding=$encoding";
    print "<br><table width=\"100%\" border=0 cellpadding=3 cellspacing=1 bgcolor=\"#000000\"><tr><td bgcolor=\"#C0C0C0\"><img src=\"images/img_file.gif\" width=11 height=15 border=0> <strong>Attachment (<a href=\"$download\">Download</a>)</strong></td></tr><tr><td bgcolor=\"#F0F0F0\">";
    print "<strong>Filename:</strong> $filename<br>\n";
    print "<strong>Type:</strong> $mime_type<br>\n";
    print "<strong>Size:</strong> $size bytes<br>\n";
    if($struct->description) {
      print "<strong>Description:</strong> " . $struct->description . "<br>\n";
    }
    print "</td></tr></table>\n";
  }

  function process_text_html($struct, $imap_part) {
    $mailbox = $this->mailbox;
    $msgnum = $this->msgnum;
    $folder = $this->folder;
    if($imap_part == "") {
      $text = imap_body($mailbox, $msgnum, FT_UID);
    } else {
      $text = imap_fetchbody($mailbox, $msgnum, $imap_part, FT_UID);
    }

    $text = $this->convert_encoding($struct, $text);

    $text = preg_replace("/<(\s*)title(\s*)>(.*?)<(\s*)\/(\s*)title(\s*)>/is", "", $text);
    $text = preg_replace("/<(\/*)(\s*)(html|head|title|script|body|meta)(.*?)>/is", "", $text);
    for($i = 0; $i < count($this->cid_list); $i++) {
      if(ereg($this->cid_list[$i]["cid"], $text)) {
        $cid = $this->cid_list[$i]["cid"];
        $type = strtolower($this->cid_list[$i]["struct"]->subtype);
        $imap_part = $this->cid_list[$i]["imap_part"];
        $cid_imgsrc = "get_attach.php?folder=$folder&msgnum=$msgnum&part_no=$imap_part&type=image&subtype=$type&action=inline";
        $text = ereg_replace("cid:$cid", $cid_imgsrc, $text);
      }
    }
    if($this->messages_shown > 0) {
      print "<hr size=1>";
    }

//    $text = preg_replace("'mailto:([\w|-|.]+)@([\w|-|.]+)'", "compose.php?to=\\1@\\2" , $text);
    print "$text<br>\n";
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
    $text = nl2br($text);
    if($this->messages_shown > 0) {
      print "<hr size=1>";
    }

    $text = preg_replace("'(\s+)http://(.*?)(\s+)'", "\\1<a href=\"http://\\2\" target=\"_blank\">http://\\2</a>\\3" , $text);
    $text = preg_replace("'(\s+)www.(.*?)(\s+)'", "\\1<a href=\"http://www.\\2\" target=\"_blank\">www.\\2</a>\\3" , $text);
    $text = preg_replace("'([\w|-|.]+)@([\w|-|.]+)'", "<a href=\"compose.php?to=\\0\">\\0</a>" , $text);
    print "<tt>$text</tt><br>\n";
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
        if($struct->disposition == "attachment") {
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
          print "<br>\n";
          print "<table width=\"100%\" border=0 cellpadding=3 cellspacing=1 bgcolor=\"#000000\">\n";
$download = "get_attach.php?folder=$this->folder&msgnum=$this->msgnum&part_no=&type=MESSAGE&subtype=RFC822&name=".urlencode(time().".txt")."&encoding=none";

          print "<tr><td bgcolor=\"#C0C0C0\"><img src=\"images/img_envelope.gif\" width=15 height=11 border=0> <strong>Forwarded message (<a href=\"$download\">Download</a>)</strong></td></tr>\n";
          print "<tr><td bgcolor=\"#F0F0F0\">\n";
?>
<table border=0 cellpadding=0 cellspacing=0>
  <tr>   
    <td><b>From: </b></td>
    <td><a href="compose.php?to=<? echo $msginfo[from_address]; ?>"><? echo $msginfo[from_name]; ?></a></td>
  </tr>
<?  
if(count($msginfo[to_array]) > 0) {
  ?>
  <tr>
    <td><b>To: </b></td>
    <td>
  <?    
  for($z = 0; $z < count($msginfo[to_array]); $z++) {
    ?>
      <a href="compose.php?to=<? echo $msginfo[to_array][$z][address]; ?>"><? echo $msginfo[to_array][$z][name]; ?></a><br>
    <? 
  }
  ?>
    </td>
  </tr>
<?
}

if(count($msginfo[cc_array]) > 0) {
  ?>   
  <tr>  
    <td><b>Cc: </b></td>
    <td>
  <?
  for($z = 0; $z < count($msginfo[cc_array]); $z++) {
    ?>
      <a href="compose.php?to=<? echo $msginfo[cc_array][$z][address]; ?>"><? echo $msginfo[cc_array][$z][name]; ?></a><br>
    <?
  }
  ?>     
    </td>
  </tr>
  <?
} 
?>
  <tr>
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
          print "<tr><td bgcolor=\"#FFFFFF\">\n";
          print "<p>\n";
          $message_show = new km_message_show($this->mailbox, $this->folder, $this->msgnum, $struct, $imap_part);
          print "</td></tr>\n";
          print "</table>\n";
        }
      } else {
        $this->process_attachment($struct, $imap_part);
      }
    }
  }



}
















?>
