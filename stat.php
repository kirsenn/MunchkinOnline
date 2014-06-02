<?
if(isset($_SERVER["HTTP_USER_AGENT"])) $useragent = $_SERVER["HTTP_USER_AGENT"]; else $useragent = "";
$ip = $_SERVER["REMOTE_ADDR"];
$url = $_SERVER["REQUEST_URI"];
if(isset($_SERVER["HTTP_REFERER"])) $refer = $_SERVER["HTTP_REFERER"]; else $refer = "";
$now = time();
$mysql->sql_query("INSERT INTO statistics VALUES (0, '$now', '$ip', '$url', '$refer', '$useragent', '0')");

?>