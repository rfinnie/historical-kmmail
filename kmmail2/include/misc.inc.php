<?
// @(#) $Id: misc.inc.php,v 1.1.1.1.8.2 2002/11/25 08:09:22 ryanf Exp $

function stripslashes_array(&$the_array_element, $the_array_element_key, $data) {
  $the_array_element = stripslashes($the_array_element);
}
if(get_magic_quotes_gpc() == 1) {
  while(list($key, $val) = each($_COOKIE)) {
    if(is_array($val)) {
      array_walk($val, 'stripslashes_array', '');
      $$key = $val;
    } else {
      $$key = stripslashes($val);
    }
  }
  while(list($key, $val) = each($_POST)) {
    if(is_array($val)) {
      array_walk($val, 'stripslashes_array', '');
      $$key = $val;
    } else {
      $$key = stripslashes($val);
    }
  }
  while(list($key, $val) = each($_GET)) {
    if(is_array($val)) {
      array_walk($val, 'stripslashes_array', '');
      $$key = $val;
    } else {
      $$key = stripslashes($val);
    }
  }
} else {
  while(list($key, $val) = each($_COOKIE)) {
    $$key = $val;
  }
  while(list($key, $val) = each($_POST)) {
    $$key = $val;
  }
  while(list($key, $val) = each($_GET)) {
    $$key = $val;
  }
}
ini_set("magic_quotes_gpc",0);

include_once('include/settings.inc.php');

function bc_array_search($needle, $haystack) {
  if(in_array($needle, $haystack)) {
    foreach($haystack as $key => $value) {
      if($needle == $value) {
        return $key;
      }
    }
  } else {
    return false;
  }
}

function km_human_readable_size($number, $precision) {
  $tags = array('B', 'KB', 'MB', 'GB', 'TB');
  $attag = 0;
  while($number > 1024) {
    $number = $number / 1024;
    $attag++;
  }
  return round($number, $precision) . $tags[$attag];
}
// Retrieves a full name using calls to passwd
function passwd_real_name($username) {
  global $config;

  if($config[use_passwd]) {
    $pwnam = @posix_getpwnam($username);
    $rnarray = explode(',', $pwnam[gecos]);
    $rn = $rnarray[0];
    if(!$rn) {
      return $config[passwd_map][$username];
    } else {
      return $rn;
    }
  } else {
    return $config[passwd_map][$username];
  }
}


function get_real_username($username) {
  global $config;
  $email = $username.'@'.$config['host'];
  if($config['use_virtusertable'] == 1) {
    dbmopen($config['virtusertable_file'], 'r');
    $realunix = dbmfetch($config['virtusertable_file'], $email);
    dbmclose($fn);
    if($realunix && preg_match("/^[a-z0-9]+$/", $realunix)) {
      return $realunix;
    } else {
      return $username;
    }
  } else {
    return $username;
  }
}
function km_send_udp_stat_packet() {
  global $config;
  $fp = @fsockopen("udp://stats.kmmail.org", 65520);
  @fputs($fp, $config['version'] . ' ' . $config['build']);
  @fclose($fp);
}

function get_offset() {
  global $config;
  $os = date('Z') / 60;
  if($os > 0 && $config{'west_of_utc'}) {
    $os = 0 - $os;
  }
  if($os < 0) { 
    $minus = "-";
    $os = 0 - $os;
  } else {
    $minus = "+";
  }
  $hours = intval($os / 60);
  $minutes = $os - ($hours * 60);
  return sprintf("%s%02d%02d", $minus, $hours, $minutes);
}

function get_offset_minutes() {
  global $config;
  $os = date('Z') / 60;
  if($os > 0 && $config{'west_of_utc'}) {
    $os = 0 - $os;
  }
  return $os;
}

function pre_print_r($array) {
  print "<pre>";
  print_r($array);
  print "</pre><hr>\n";
}

function splitbigwords($text, $l) {
  $a = explode(' ', $text);
  for($i = 0; $i < count($a); $i++) {
    if(strlen($a[$i]) > $l) {
      $x = $a[$i];
      $a[$i] = '';
      while(strlen($x) >= $l) {
        $a[$i] .= substr($x, 0, $l) . ' ';
        $x = substr($x, $l);
      }
      $a[$i] .= $x;
    }
  }
  return implode(' ', $a);
}

$config['is_pop3'] = (preg_match("/\/pop3$/", $config['imap_host']) ? 1 : 0);
if($config['is_pop3'] && ($config['display_folders'])) {
  $config['display_folders'] = 0;
}

if(strval($config['imap_timezone_offset']) == 'real_timezone_offset') {
  $config['imap_timezone_offset'] = get_offset_minutes();
}

session_start();







?>
