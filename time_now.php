<?php 
session_start();
require_once("global.php");
require_once("my_function.php");
require_once("modules/mysql.php");

if (isset($_SESSION['id_user'])) 
{
	$json_data['time_now'] = date("H:i");

	$mysql->sql_query('UPDATE users SET active='.time().', last_ip="'.getIP().'", last_page="gmenu" WHERE (id_user='.$_SESSION['id_user'].')');
	$link_numusers = $mysql->sql_query('SELECT * FROM users WHERE ( (active>='.(time()-240).') AND (last_page="gmenu") )');

	$json_data['user_online'] = mysql_num_rows($link_numusers);
	echo json_encode($json_data);	
}
?>