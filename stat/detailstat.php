<?php
include("settings.php");
#######################
## Detailstat.php
## Выводит подробную таблицу посещений со свистелками и перделками
## kirSeNN (c)
#######################
//Файл будет подгружаться через AJAX поэтому кодировка:
header("Content-type: text/html; charset=$encoding");
define("MAINSCRIPT",true); //Для запрета директного обращения к скриптам

include($path_to_mysqlclass);
include($path_to_sysinfo);

$query = $_POST['q'];
if(!preg_match("#^SELECT \* FROM $table_in_db(.*)ORDER by time desc$#i",$query)){die("Incorrect MySQL query (Query must look like \"SELECT * FROM wp_statistics ORDER BY time DESC\"). Want Injection? Go www.fsb.ru.");}
//Запросим последние N записей дабы не перегружать сервер анализом
$limit = $_POST['limit'];

?>
<div class="overflowing" align="center">
	<div class="detailscaption">Последние <?=$limit?> записей (<span style="cursor:pointer;" onClick="document.getElementById('content').style.display='none'">Убрать</span>)</div>
	<table class="statstable" width="100%" align="center" border="1" cellpadding="2" >
	<tr>
		<td width="14%"><b class="stat_block">Дата | Время</b></td>
		<td><b class="stat_block">IP</b></td>
		<td><b class="stat_block">Запрошено / Пришел</b></td>
		<td><b class="stat_block">Броузер / ОC</b></td>
	</tr>
<?php
$readstat = $mysql->sql_query("$query LIMIT $limit");
while($st = mysql_fetch_assoc($readstat))
{
	$loc_time = $st['time'] + 6*3600;
	$ip = $st['ip'];
	$date = date("d.m.Y |", $loc_time);
	$time = date("H:i:s", $loc_time);
	$req = $st['req'];
	$counter++;
	//Если хотим выделить определенный адрес
	if($ip=="127.0.0.1"){$ip="<font color=\"red\">Local</font>";$myipcnt++;}
	//По умолчанию айпи содержит ссылку на Lookup
	else
	{
		$ip="<a onMouseOver=\"postajax('$path_to_geoip','ip=$ip','geoip_$counter','geoipwait');\" onMouseOut=\"getElementById('geoip_$counter').style.display='none'\" target=\"_blank\" href=\"http://ipgeobase.ru/?address=$ip\">$ip</a>
		<br/>
		<div id=\"geoipwait\"><img src=\"images/loading.gif\"></div>
		<div class=\"geoipdiv\" id=\"geoip_$counter\"></div>";
		$myipcnt++;
	}
	
	//Если сегодня то дату не выводить
	if($date == date("d.m.Y |", time())){$date = "Сегодня";}
	
	//Определяем откуда пришел человек
	if(strlen($st['ref'])>1)
	{
		$domain = preg_replace("|http://(.*?)/.+|","$1",$st['ref']);
		$referer = "<a target=\"_blank\" href=\"".$st['ref']."\">$domain</a>";
	}
	else
	{
		$referer="";
		$refless++;
	}

	//Подключается сисинфо
	include_once("sysinfo.php");
	$system = new sysinfo;
	$useragent = $st['browser'];
	$system->getbrowser($useragent); //Определяем тип браузера
	$browser = $system->browser;
	$browserver = $system->browserver;
	$system->getos($useragent); //Определяем тип ОС
	$os = $system->ostype;
	$osver =  $system->osver;
	
	//Собственно вывод
	echo "<tr><td width=\"125px\">$date $time</td><td>$ip</a></td><td>$req <br/> $referer</td><td>$browser $browserver <br/> $os $osver</td></tr>";

}

?>
</table>
</div>
