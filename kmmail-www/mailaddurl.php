<?
// Yes, it took 5 minutes to write.  Who cares?

$ip = $HTTP_SERVER_VARS['REMOTE_ADDR'];
$browser = $HTTP_SERVER_VARS['HTTP_USER_AGENT'];

$to = 'ryanf@users.sourceforge.net';
$subject = 'kmMail Users URL Submission';
$body = <<<EOM
Here is the information:

Name: $f_name
Email: $f_email
Site: $f_site
URL: $f_url

IP: $ip
Browser: $browser

EOM;

mail($to, $subject, $body);
header('Location: index.php?confirm=1');
?>

Nothing to see here