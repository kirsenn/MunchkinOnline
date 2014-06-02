<?php
//Если статистика по конкретному столу
if(isset($_GET["idgt"]))
{
	$idgt = $_GET["idgt"];
	if($idgt=="spectate" && isset($_SESSION['id_gt_spec']))
	{
		echo "<script>location.href='index.php?profile=statgame&idgt=".$_SESSION['id_gt_spec']."';</script>"; 
		unset($_SESSION['id_gt_spec']);
		die(); 
	}
	elseif(!is_numeric($_GET["idgt"])){die("Idgt is NAN");}
}

//Если ничего не задано
if (!isset($_SESSION['id_user']) && !isset($idgt)){die ("You have not any game");}
$id_user=$_SESSION['id_user'];

//Статистика по юзеру или по столу
if(isset($idgt))
{
	$query = 'SELECT * FROM statistic_game WHERE (id_gt='.$idgt.') ORDER BY end_game DESC LIMIT 1';
}
else
{
	$query = 'SELECT * FROM statistic_game WHERE (id_user='.$id_user.') ORDER BY end_game DESC LIMIT 1';
}

$result_suser=$mysql->sql_query($query);
if (mysql_num_rows($result_suser)>0)
{
	$row = mysql_fetch_array($result_suser);

	if ($row['status_game']=='end_game_victory')
	{
		$winnerid = mysql_result($mysql->sql_query("SELECT id_user FROM users WHERE login ='".$row['winner']."'"),0);
		
		$status_game='Игра доиграна до конца!<br/>Победил: <a href="profile_'.$winnerid.'.htm"><b>'.$row['winner'].'</b></a>';
	}
	elseif ($row['status_game']=='delete_game_user<2')
	{
		$status_game='Игра прервана, так как игроков осталось  меньше 2 человек<br>(скорее всего остальные просто вышли из-за игрового стола)';
	}
	elseif($row['status_game']=='delete_game_hole')
	{
		$status_game='Стол был  удален, игроком который его создал. Все остальные игроки были выгнаны со стола';
	}
	elseif ($row['status_game']=='old_game')
	{
		$status_game='Стол был  удален, так как игра была создана более 2 суток назад';
	}
	elseif ($row['status_game']=='incalculate')
	{
		$winnerid = mysql_result($mysql->sql_query("SELECT id_user FROM users WHERE login ='".$row['winner']."'"),0);
		
		$status_game='Игровой стол '.$row['name_gt'].'Игра доиграна до конца!<br/>Победил: <a href="profile_'.$winnerid.'.htm"><b> '.$row['winner'].'</b></a> но игра не идет в зачет, так как за игровым столом было менее 3 активных игроков, либо игра длилась менее 10 минут';
	}
	else
	{
		$status_game='';
	}

	?>
	<div align="center" style="margin: 0px auto;"><h2>Статистика игры <?php if(isset($idgt)){echo ". Стол id = ".$idgt;} ?></h2></div>
	<span class="statstr"><?=$status_game ?></span>
	<span class="statstr">Игроков: <?=$row['num_user'] ?></span>
	<span class="statstr">Голосов "За" / "Против": <?=$row['pro'] ?> /  <?=$row['con'] ?></span>
	<span class="statstr">
		<b>Игроки:</b><br/>
		<?php
		$result_salluser=$mysql->sql_query('SELECT * FROM statistic_game JOIN users ON (statistic_game.id_user=users.id_user) WHERE (statistic_game.id_gt='.$row['id_gt'].')');
		while ($row_salluser = mysql_fetch_array($result_salluser)) 
		{
			$login = $row_salluser['login'];
			$id_user = $row_salluser['id_user'];
			$exper = $row_salluser['take_exper'];
			$level = $row_salluser['level'];
			echo "<a href=\"profile_$id_user.htm\">$login</a> ( +$exper exp)<br/>";
		}
		?>
	</span>
	<span class="statstr">Начало / Конец <br/> <?php echo date("d.m.y H:i",$row['start_game']) ?> / <?php echo date("d.m.y H:i",$row['end_game']) ?></span>
	
	<?php
	$linkname = "На главную";
	$linkurl = "index.htm";
	if(isset($_SERVER["HTTP_REFERER"]) && strlen($_SERVER["HTTP_REFERER"])>0 && !stristr($_SERVER["HTTP_REFERER"],"spectate") && !stristr($_SERVER["HTTP_REFERER"],"statgame"))
	{
		$linkname = "Назад";
		$linkurl = $_SERVER["HTTP_REFERER"];
	}
	?>
	<div style="text-align:center; width:100%;"><a href="<?=$linkurl ?>"><?=$linkname ?></a></div>
	<?php
}
else
{
	echo "Такого стола не существует";
}
?>