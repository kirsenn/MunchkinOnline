<?php require_once("config.php");?>
<?
function connectdb()
{
  /* connect to database - and if we can't stop right there */
  global $dbhost;
  global $dbport;
  global $dbuname;
  global $dbpass;
  global $dbname;
  $DBLink=mysql_connect($dbhost . ":" .$dbport, $dbuname, $dbpass);
  @mysql_select_db("$dbname") or die ("Unable to select database.");
  mysql_query("set names utf8");
  return $DBLink;
}

?>