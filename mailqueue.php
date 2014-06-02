<?php
require_once('modules/smtp-func.php');
require_once("modules/mysql.php");

$log["status"] = true;
$log["filename"] = "/var/www/Munchkin/mailqueue.log";

if($log["status"])
{
	$logfile = @fopen($log["filename"],"a+") or die("can't open ".$log["filename"]);
}

function logput($str,$type)
{
	global $log;
	global $logfile;
	$time = date("d.m.Y H:i:s");

	switch($type)
	{
		case 0 : $typestr = "Info"; break;
		case 1 : $typestr = "Error"; break;
		default: $typestr = "Unknown"; break;
	}
	
	$str = $time."\t".$typestr."\t".$str."\r\n";
	
	if($log["status"])
	{
		@fwrite($logfile,$str) or die("can't write log ".$log["filename"]);
	}
}


function smtpmailqueue($mail_to, $subject, $message, $headers='') {
	global $config;
	$SEND =   "Date: ".date("D, d M Y H:i:s") . " UT\r\n";
	$SEND .=   'Subject: '.$subject."\r\n";
	if ($headers) $SEND .= $headers."\r\n\r\n";
	else
	{
		$SEND .= "Reply-To: ".$config['smtp_username']."\r\n";
		$SEND .= "MIME-Version: 1.0\r\n";
		$SEND .= "Content-Type: text/plain; charset=\"".$config['smtp_charset']."\"\r\n";
		//$SEND .= "Content-Transfer-Encoding: 8bit\r\n";
		$SEND .= "From: \"".$config['smtp_from']."\" <".$config['smtp_username'].">\r\n";
		$SEND .= "To: $mail_to <$mail_to>\r\n";
		$SEND .= "X-Priority: 3\r\n\r\n";
	}
	$SEND .=  $message."\r\n";
	if( !$socket = fsockopen($config['smtp_host'], $config['smtp_port'], $errno, $errstr, 5) ) {
	$error = $errno.": ".$errstr;
	return $error;
	}

	if (!server_parse($socket, "220", __LINE__)) return false;

	fputs($socket, "HELO " . $config['smtp_host'] . "\r\n");
	if (!server_parse($socket, "250", __LINE__)) {
	   $error = 'Cannot send HELO!';
	   fclose($socket);
	   return $error;
	}
	fputs($socket, "AUTH LOGIN\r\n");
	if (!server_parse($socket, "334", __LINE__)) {
	   $error =  'Not answer for auth.';
	   fclose($socket);
	   return $error;
	}
	fputs($socket, base64_encode($config['smtp_username']) . "\r\n");
	if (!server_parse($socket, "334", __LINE__)) {
	   $error =  'Login incorrect!';
	   fclose($socket);
	   return $error;
	}
	fputs($socket, base64_encode($config['smtp_password']) . "\r\n");
	if (!server_parse($socket, "235", __LINE__)) {
	   $error = 'Password incorrect!';
	   fclose($socket);
	   return $error;
	}
	fputs($socket, "MAIL FROM: <".$config['smtp_username'].">\r\n");
	if (!server_parse($socket, "250", __LINE__)) {
	   $error = 'Cannot send MAIL FROM:';
	   fclose($socket);
	  return $error;
	}
	fputs($socket, "RCPT TO: <" . $mail_to . ">\r\n");

	if (!server_parse($socket, "250", __LINE__)) {
	   $error = 'Cannot send RCPT TO:';
	   fclose($socket);
	   return $error;
	}
	fputs($socket, "DATA\r\n");

	if (!server_parse($socket, "354", __LINE__)) {
	   $error = 'Cannot send DATA';
	   fclose($socket);
	   return $error;
	}
	fputs($socket, $SEND."\r\n.\r\n");

	if (!server_parse($socket, "250", __LINE__)) {
	   $error = 'Cannot send message!';
	   fclose($socket);
	   return $error;
	}
	fputs($socket, "QUIT\r\n");
	fclose($socket);
	$error = 0;
	return $error;
}

//Try to read queues
$linkqueues = $mysql->sql_query("SELECT * FROM mailqueue");
if(mysql_num_rows($linkqueues)<1){logput("There is no active queues in DB or table is not available",0);}
while($qrow = mysql_fetch_assoc($linkqueues))
{
	$logstringid = "ID: ".$qrow["id"]."/ Email: ".$qrow["email"].". ";

	if(strlen($qrow["email"])<1){$logstring = "There is no email! Check me!"; logput($logstringid.$logstring,1); continue;}
	if(strlen($qrow["letter"])<1){$logstring = "There is no letter text! Check me!"; logput($logstringid.$logstring,1); continue;}
	
	//If expired
	if($qrow["timeexpire"] < time())
	{
		$logstring = "Was expired. Started at ".date("d.m.Y H:i:s",$qrow["timestarted"]).", Expired at ".date("d.m.Y H:i:s",$qrow["timeexpire"]).". ";
		if($mysql->sql_query("DELETE FROM mailqueue WHERE id='".$qrow["id"]."'"))
		{
			$logstring .= "Deleted";
			logput($logstringid.$logstring,0);
		}
		else
		{
			$logstring .= "Cannot delete";
			logput($logstringid.$logstring,1);
		}
		continue;
	}
	
	//Try to send again
	$mailfunc = smtpmailqueue($qrow["email"], $qrow["subject"], $qrow["letter"], $qrow["headers"]);
	if($mailfunc == 0)
	{
		$logstring = "Sended correctly. ";
		if($mysql->sql_query("DELETE FROM mailqueue WHERE id='".$qrow["id"]."'"))
		{
			$logstring .= "Deleted";
			logput($logstringid.$logstring,0);
		}
		else
		{
			$logstring .= "Cannot delete. Warning it may double message!";
			logput($logstringid.$logstring,1);
		}
		continue;
	}
	else
	{
		$mysql->sql_query("UPDATE mailqueue SET attempts=attempts+1, lastattempt=".time()." WHERE id='".$qrow["id"]."'");
		$logstring = "An error occured while sending. Error Text ($mailfunc). Not sended.";
		logput($logstringid.$logstring,1);
	}
	
	//If attempts exceeded
	if($qrow["attempts"] > $qrow["maxattempts"])
	{
		$logstring = "Attempts exceeded. Started at ".date("d.m.Y H:i:s",$qrow["timestarted"]).". Max attempts = ".$qrow["maxattempts"];
		if($mysql->sql_query("DELETE FROM mailqueue WHERE id='".$qrow["id"]."'"))
		{
			$logstring .= "Deleted";
			logput($logstringid.$logstring,0);
		}
		else
		{
			$logstring .= "Cannot delete";
			logput($logstringid.$logstring,1);
		}
	}
}
?>