<?php
define("MAINSCRIPT",true); //Для запрета редиректного обращения к скриптам

include("settings.php"); //Импорт настроек
include($path_to_mysqlclass);
include($path_to_sysinfo);

//Если скрипт подгружается в другой скрипт, то заголовки не выводим
	if(!$indesign)
	{
		header("Content-type: text/html; charset=$encoding");
		echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=$encoding\">";
		echo "<link rel=\"stylesheet\" href=\"$path_to_css\" type=\"text/css\"/>";
	}
	//Ajax
	echo "<script src=\"$path_to_ajax\"></script>";


//Очистка статистики
if(isset($_GET['clear']))
	{
		$mysql->sql_query("TRUNCATE TABLE $table_in_db");
		echo "<script>alert('Все удалил!');window.location.href='".$url_prefix."period=today';</script>";
	}

?>
<!-- Верхние заголовки и ссылки -->
<script>
function clearstat()
{
	if(confirm('Вы действительно хотите удалить статистику посещений сайта?'))
	{
		window.location.href='<?=$url_prefix?>stats&clear';
		return true;
	}
}

function getdetail(query)
{
	var records = document.detailget.querycol.value;
	var params = 'q='+query+'&limit='+records;
	postajax('<?=$path_to_detailstat?>',params,'content','wait');
}
</script>
<div id="main" align="center">
	<div class="caption"><img src="<?=$path_to_images?>eye.png" width="20px" height="20px" /> Статистика</div>
	<div><a href="javascript:clearstat();"><img src="<?=$path_to_images?>delete.png" width="12px" height="12px" /> Очистить</a></div>
	<table align="center" class="topstats" width="100%" cellspacing="0px">
		<tr>
			<td width="50%"><a href="<?=$url_prefix?>period=all" ><img <?php if($_GET['period']=="all" ){ echo "src=\"".$path_to_images."whole.png\"";}else{ echo "src=\"".$path_to_images."wholeinactive.png\"";} ?> width="16px" height="16px" /> Полная</a></td>
			<td width="50%"><a href="<?=$url_prefix?>period=date" ><img <?php if($_GET['period']=="date" ){ echo "src=\"".$path_to_images."byperiod.png\"";}else{ echo "src=\"".$path_to_images."byperiodinactive.png\"";} ?> width="16px" height="16px" /> За период</a></td>
		</tr>
	</table>



<?php
//Формируем запрос для выбора статистики из базы по периоду времени   
$query = "SELECT * FROM $table_in_db ORDER by time DESC";

//За весь период
if($_GET['period']=="all")
{
	$first = $mysql->sql_query("SELECT * FROM $table_in_db LIMIT 1");
	while($r = mysql_fetch_assoc($first))
	{$statperiod = " ".date("d.m.Y",$r['time']);}
	showstat($query,$statperiod);
}

//За определенный период
if($_GET['period']=='date')
{
	?>
	<table width="100%" align="center" border="0" class="periodsettable">
	<tr><td>
		<table align="center"  cellspacing="9" cellpadding="2">
		<form action="<?=$url_prefix?>period=date" method="post"> 
		<tr>
			<td colspan="7" align="center"><b>Укажите период</b></td>
		</tr>
		<tr>
			<td><input type="text" size="2" name="day" value="<?php if(!isset($_POST['day'])){echo date("d",time()); }else{echo $_POST['day'];} ?>" /></td>
			<td>. <input type="text" size="2" name="month" value="<?php if(!isset($_POST['month'])){echo date("m",time()); }else{echo $_POST['month'];} ?>" /></td>
			<td>. <input type="text" name="year" size="4" value="<?php if(!isset($_POST['year'])){echo date("Y",time()); }else{echo $_POST['year'];} ?>" /></td>
			<td><b>по</b></td>
			<td><input type="text" size="2" name="day_up" value="<?php if(!isset($_POST['day_up'])){echo date("d",time()); }else{echo $_POST['day_up'];} ?>" /></td>
			<td>. <input type="text" size="2" name="month_up" value="<?php if(!isset($_POST['month_up'])){echo date("m",time()); }else{echo $_POST['month_up'];} ?>" /></td>
			<td>. <input type="text" name="year_up" size="4" value="<?php if(!isset($_POST['year_up'])){echo date("Y",time()); }else{echo $_POST['year_up'];} ?>" /></td>
		</tr>
		<tr>
			<td colspan="7" align="center"><input type="submit" value="Показать" style="cursor:pointer;" /></td>
		</tr>
		</form>
		</table>
	</td></tr>
	</table>
	
	<?php
	if(isset($_POST['day']))
	{
		$day = $_POST['day'];
		$month = $_POST['month'];
		$year = $_POST['year'];
		$day_up = $_POST['day_up'];
		$month_up = $_POST['month_up'];
		$year_up = $_POST['year_up'];
		$beginofday = mktime(0,0,0,$month,$day,$year);
		$endofday = mktime(23,59,59,$month_up,$day_up,$year_up);
		//Проверка даты
		if($beginofday<$endofday)
		{
			$statperiod = date("d.m.Y",$beginofday)." - ".date("d.m.Y",$endofday);
			$query = "SELECT * FROM $table_in_db WHERE time>$beginofday AND time<$endofday ORDER by time DESC";
			showstat($query,$statperiod);
		}
		else
		{
			echo "<font color=\"red\">Указанный период недействителен (вторая дата должна быть больше)</font>";
		}
	}
	else
	{
		$statperiod = "Сегодня";
		$day = date("d",time());
		$month = date("m",time());
		$year = date("Y",time());
		$beginofday = mktime(0,0,0,$month,$day,$year);
		$query = "SELECT * FROM $table_in_db WHERE time>$beginofday ORDER by time DESC";
		showstat($query,$statperiod);
	}
}

//Собственно анализ статистики
function showstat($query,$statperiod)
{
	global $path_to_images;
	global $url_prefix;
	
	$mysql = new MySQL;
	$botcnt = 0; // Счетчик ботов
	$myipcnt = 0; //айпишники офиса
	$yandexcnt = 0; // заходов с яндеха
	$mailrucnt = 0; // заходов с майл.ру
	$googlecnt = 0; // заходов с гугл
	$ramblercnt = 0; //Rambler
	
	//Счетчики ботов и браузеров:
	$yandexbotcnt = 0;
	$yandexmetrikacnt = 0;
	$googlebotcnt = 0;
	$yahoobotcnt = 0;
	$aportbotcnt = 0;

	$operacnt = 0;
	$firefoxcnt = 0;
	$iecnt = 0;
	$chromecnt = 0;
	
	$total_ie = 0;
	$total_opera = 0;
	$total_firefox = 0;
	$total_chrome = 0;
	$total_safari = 0;
	$total_other = 0;
	
	$iparray = array(0=>""); //для подсчета уникальных IP
	
	//Запрошаем таблицу с нашими посещениями
	$readstat = $mysql->sql_query($query);
	
	//Считаем кол-во посещений
	$count = mysql_num_rows($readstat);
	
	//Процесс анализа каждой записи
	while($st = mysql_fetch_assoc($readstat))
	{
		//Для подсчета айпи адресов и уникальных айпи
		$ip = $st['ip'];
		if(!array_search($ip,$iparray)){$iparray[] = $ip; $uniqueip++;}
		//if($ip=="127.0.0.1"){$ip="<font color=\"red\">127.0.0.1</a>";$myipcnt++;}
	
		//Определяем дату запроса
		$date = date("d.m.Y", $st['time']);
		$time = date("H:i", $st['time']);
		if($date == date("d.m.Y", time())){$date = "Сегодня";}
		
		//Определяем сам запрос
		$req = $st['req'];

		//Определяем откуда пришел человек, в т.ч. выделяем поисковые системы
		if(strlen($st['ref'])>1)
		{
			$domain = preg_replace("|http://(.*?)/.+|","$1",$st['ref']);
			if(strstr($domain,"yandex")){$yandexcnt++;}
			if(strstr($domain,"mail.ru")){$mailrucnt++;}
			if(strstr($domain,"rambler")){$ramblercnt++;}
			if(strstr($domain,"google")){$googlecnt++;}
			$referer = "<a target=\"_blank\" href=\"".$st['ref']."\">$domain</a>";
		}
		else
		{
			$referer="";
		}
		
		//Подключается сисинфо, определяем браузеры и ОС
		$system = new sysinfo;
		$useragent = $st['browser'];
		$system->getbrowser($useragent); //Определяем тип браузера
		$browser = $system->browser;
		$browserver = $system->browserver;
		$system->getos($useragent); //Определяем тип ОС
		$os = $system->ostype;
		$osver =  $system->osver;
		
		//Подсчитываем ботов и браузеры
		if(strstr($browser,"Opera")){$operacnt++;}
		if(strstr($browser,"Firefox")){$firefoxcnt++;}
		if(strstr($browser,"Internet Explorer")){$iecnt++;}
		if(strstr($browser,"Chrom")){$chromecnt++;}
		if(strstr($browser,"Safari")){$safaricnt++;}
		
		if(stristr($browser,"Bot")){$botcnt++;}
		if(strstr($browser,"Yandex")){$yandexbotcnt++;}
		if(strstr($browser,"Metrika")){$yandexmetrikacnt++;}
		if(strstr($browser,"Google") && !strstr($browser,"Chrom")){$googlebotcnt++;}
		//if(strstr($browser,"Yahoo")){$yahoobotcnt++;}
		//if(strstr($browser,"Aport")){$aportbotcnt++;}
		
		//Посещений без ботов
		$people_count = $count - $botcnt;
	}
		//Далее вывод:
	?>
	<table border="0" width="100%" align="center">
		<tr>
			<td align="left">
					<table width="100%" align="center" style="border-collapse:collapse;" cellspacing="7" cellpadding="7" border="0">
						<tr>
							<td width="25%" align="center">
								<b class="stat_block">Общее</b>
							</td>
							<td width="25%" align="center">
								<b class="stat_block">Боты</b>
							</td>
							<td width="25%" align="center">
								<a href="<?=$url_prefix?>period=queries"><b class="stat_block">Рефералы</b></a>
							</td>
							<td width="25%" align="center">
								<b class="stat_block">Браузеры</b>
							</td>
						</tr>
						<tr>
							<td valign="top">
								<table align="center" cellspacing="5">
									<tr><td colspan="2"><small><?=$statperiod?>:</small></td></tr>
									<tr><td><img src="<?=$path_to_images?>pages.png" width="16px" alt="Страниц" title="Просмотров страниц" /></td><td>Страниц: <b><?=$people_count?></b></td></tr>
									<?php if($people_count<$count) { echo"<tr><td></td><td><small>С ботами: $count</small></td></tr>"; } ?>
									<tr><td><img src="<?=$path_to_images?>hosts.png" width="16px" alt="Хосты" title="Уникальных посетителей" /></td><td>Посетители: <b><?=$uniqueip?></b></td></tr>
								</table>
							</td>
							<td valign="top">
								<table align="center" cellspacing="5">
									<tr><td><img src="<?=$path_to_images?>radiolocator.png" width="16px" alt="В" title="Всего ботов заходило" /></td><td>Всего: <b><?=$botcnt?></b></td></tr>
									<tr><td><img src="<?=$path_to_images?>yandex-bot.png" width="16px" alt="Ya" title="" /></td><td>Yandex: <?php echo $yandexbotcnt-$yandexmetrikacnt; ?></td></tr>
									<?php if($yandexmetrikacnt>0) { echo"<tr><td></td><td><small>Метрика: $yandexmetrikacnt</small></td></tr>"; }?>
									<tr><td><img src="<?=$path_to_images?>google-bot.png" width="16px" alt="G" title="" /></td><td>Google: <?=$googlebotcnt?></td></tr>
								</table>
							</td>
							<td valign="top">
								<table align="center" cellspacing="5">
									<tr><td><img src="<?=$path_to_images?>yandex.png" width="16px" alt="Ya" title="" /></td><td>Yandex: <b><?=$yandexcnt?></b></td></tr>
									<tr><td><img src="<?=$path_to_images?>mailru.png" width="16px" alt="Mail" title="" /></td><td>Mail.ru: <b><?=$mailrucnt?></b></td></tr>
									<tr><td><img src="<?=$path_to_images?>google.png" width="16px" alt="G" title="" /></td><td>Google: <b><?=$googlecnt?></b></td></tr>
									<tr><td><img src="<?=$path_to_images?>rambler.png" width="16px" alt="R" title="" /></td><td>Rambler: <b><?=$ramblercnt?></b></td></tr>
								</table>
							</td>
							<td>
								
							<?php
							
							if($count-$botcnt>0)
							{
								$total_ie = round($iecnt*100/($count-$botcnt),2);
								$total_opera = round($operacnt*100/($count-$botcnt),2);
								$total_firefox = round($firefoxcnt*100/($count-$botcnt),2);
								$total_chrome = round($chromecnt*100/($count-$botcnt),2);
								$total_safari = round($safaricnt*100/($count-$botcnt),2);
								$total_other = round(($count-$botcnt-$iecnt-$operacnt-$firefoxcnt-$chromecnt-$safaricnt)*100/$count,2);
							}
							?>
							
							<table align="center" cellspacing="5">
								<tr>
									<td><img src="<?=$path_to_images?>ie.png" width="16px" alt="IE" title="IE"/></td>
									<td><?=$total_ie?>%</td>
								</tr>
								<tr>
									<td><img src="<?=$path_to_images?>opera.png" width="16px" alt="Opera" title="Opera"/></td>
									<td><?=$total_opera?>%</td>
								</tr>
								<tr>
									<td><img src="<?=$path_to_images?>firefox.png" width="16px" alt="Firefox" title="Firefox"/></td>
									<td><?=$total_firefox?>%</td>
								</tr>
								<tr>
									<td><img src="<?=$path_to_images?>chrome.png" width="16px" alt="Chrome" title="Chrome"/></td>
									<td><?=$total_chrome?>%</td>
								</tr>
								<tr>
									<td><img src="<?=$path_to_images?>safari.png" width="16px" alt="Safari" title="Safari"/></td>
									<td><?=$total_safari?>%</td>
								</tr>
								<tr>
									<td><img src="<?=$path_to_images?>browsers.png" width="16px" alt="" title=" "/></td>
									<td><?=$total_other?>%</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>

		<tr>
			<td align="center"><form name="detailget">
				<a href="javascript:getdetail('<?php echo $query; ?>');">Показать</a> 
				последние <input name="querycol" value="75" size="5" /> Записей
				</form>
			</td>
		</tr>
<?php
}
?>
		<tr>
			<td>
				<div id="wait" style="display:none;">Подождите...</div>
				<div id="content" style="display:none;"></div>
			</td>
		</tr>
</table>
<?php


//Запросы
if($_GET['period']=='queries')
{
	//Яндекс
	$yandex = $mysql->sql_query("SELECT * FROM $table_in_db WHERE locate('yandex',ref)>0 ORDER BY time");
	$queryarray = array();
	while($d = mysql_fetch_assoc($yandex))
	{
		$referer = strtolower(iconv('UTF-8','Windows-1251',urldecode($d["ref"]))); //Бывает что кодировка разная
		$text = explode("&",preg_replace("/.+text=(.*)/i","$1",$referer));
		$txt = $text[0];
		if(strlen($txt)>0)
		{
			$queryarray[]=$txt;
		}
		else
		{
			$referer = strtolower(urldecode($d["ref"]));
			$text = explode("&",preg_replace("/.+text=(.*)/i","$1",$referer));
			$txt = $text[0];
			if(strlen($txt)>0)
			{
				$queryarray[]=$txt;
			}
		}
	}
	?>
	<div class="detailscaption">Переходы с Яндекса</div>
	<table class="statstable" width="100%" align="center" border="1" cellpadding="3">
		<tr>
			<td width="15%" align="center"><b class="stat_block">Кол-во</b></td>
			<td align="center"><b class="stat_block">Текст запроса</b></td>
		</tr>
		<?php
			$queries = array_count_values($queryarray);
			arsort ($queries);
			reset ($queries);
			foreach($queries as $k=>$v)
			{
				if($v>0) echo "<tr><td align=\"center\">".$v."</td><td align=\"center\">".$k."</td></tr>";
			}
			echo "
			<tr><td colspan=\"2\" align=\"center\"><div class=\"detailscaption\">Всего переходов: ". array_sum($queries)."</div></td></tr>
			</table>";
}
?>
</div>
