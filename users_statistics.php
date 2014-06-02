<html>
<head>
	<meta http-equiv="content-type" content="text/html;  charset=utf-8">
	<link href="style.css" rel="stylesheet" type="text/css" />
</head>
<body>

<h3>Статистика по пользователям</h3>
<?php
include("modules/mysql.php");

$get_all = $mysql->sql_query("SELECT * FROM users ORDER BY id_user DESC");
$allcnt = mysql_num_rows($get_all);

$first = mysql_result($mysql->sql_query("SELECT timeactive FROM users WHERE id_user=11"),0);
$now = time();
$periodsec = $now - $first;
$periodday = $periodsec / (3600*24);
$periodhour = $periodsec / (3600);

echo "<br/><b>За все время:</b><br/>";
echo "С ".date("d.m.Y",$first)." по ".date("d.m.Y")." зарегистрировалось ".$allcnt." человек<br/>";
printf("В среднем в сутки регистрируется  %.2f человек<br/>",$allcnt/$periodday);
printf("В среднем в час регистрируется %.2f человек<br/>",$allcnt/$periodhour);


$weekago = time() - (7*24*3600);
$weekcount = mysql_num_rows($mysql->sql_query("SELECT * FROM users WHERE timeactive>$weekago"));

echo "<br/><b>За последнюю неделю:</b><br/>";
echo "С ".date("d.m.Y",$weekago)." по ".date("d.m.Y")." зарегистрировалось ".$weekcount." человек<br/>";
printf("В среднем в сутки регистрируется  %.2f человек<br/>",$weekcount/7);
printf("В среднем в час регистрируется %.2f человек<br/>",$weekcount/(7*24));


$threedayago = time() - (3*24*3600);
$threecount = mysql_num_rows($mysql->sql_query("SELECT * FROM users WHERE timeactive>$threedayago"));

echo "<br/><b>За последние три дня:</b><br/>";
echo "С ".date("H:i d.m.Y",$threedayago)." по ".date("H:i d.m.Y")." зарегистрировалось ".$threecount." человек<br/>";
printf("В среднем в сутки регистрируется  %.2f человек<br/>",$threecount/3);
printf("В среднем в час регистрируется %.2f человек<br/>",$threecount/(3*24));


$dayago = time() - (24*3600);
$daycount = mysql_num_rows($mysql->sql_query("SELECT * FROM users WHERE timeactive>$dayago"));

echo "<br/><b>За последние сутки:</b><br/>";
echo "С ".date("H:i d.m.Y",$dayago)." по ".date("H:i d.m.Y")." зарегистрировалось ".$daycount." человек<br/>";
printf("В среднем в сутки регистрируется  %.2f человек<br/>",$daycount);
printf("В среднем в час регистрируется %.2f человек<br/>",$daycount/(24));

$grandsum = ($allcnt/$periodday + $weekcount/7 + $threecount/3 + $daycount)/4;
//printf("<br/>Среднее арифметическое -  %.2f человек в сутки<br/>",$grandsum);
echo "<br/><b>Прогноз:</b><br/>";
printf("Завтра - %.2f человек<br/>",$grandsum+$allcnt);
printf("Через три дня - %.2f человек<br/>",$grandsum*3+$allcnt);
printf("Через неделю - %.2f человек<br/>",$grandsum*7+$allcnt);


//Last id
$lastid = mysql_result($mysql->sql_query("SELECT id_user FROM users ORDER BY id_user DESC LIMIT 1"),0);
echo "<br/>Последний id = $lastid<br/>";
$userids[0]="";
while($udata = mysql_fetch_assoc($get_all))
{
	$userids[] = $udata["id_user"];
}

for($i=1;$i<$lastid;$i++)
{
	if(!array_search($i,$userids))
	{
		echo "There is no userid ".$i."<br/>";
	}
}

//Очень давно не были
echo "<br/><b>Частота посещений:</b><br/>";
$getbadusers = $mysql->sql_query("SELECT * FROM users WHERE active=0");
echo "Не заходили на сайт никогда ".mysql_num_rows($getbadusers)." человек<br/>";

$week2ago = time() - (14*24*3600);
$gethbadusers = $mysql->sql_query("SELECT * FROM users WHERE active<$week2ago");
echo "Не заходили уже в течение 2 недель ".mysql_num_rows($gethbadusers)." человек<br/>";

$getdayusers = $mysql->sql_query("SELECT * FROM users WHERE active>$dayago");
echo "За последние сутки заходили ".mysql_num_rows($getdayusers)." человек<br/>";

$get3dusers = $mysql->sql_query("SELECT * FROM users WHERE active>$threedayago");
echo "За последние 3 дня заходили ".mysql_num_rows($get3dusers)." человек<br/>";

$getweekusers = $mysql->sql_query("SELECT * FROM users WHERE active>$weekago");
echo "За последнюю неделю заходили ".mysql_num_rows($getweekusers)." человек<br/>";

?>
</body>
</html>