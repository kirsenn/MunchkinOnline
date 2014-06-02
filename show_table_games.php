<?php 
require_once("global.php"); 
require_once("init_game.php");
require_once("modules/mysql.php");

function print_result($result,$button)
{
	$mysql = new MySQL;
	?>
		<tr>
			<td align="center" width="120px" valign="top">
				<b>Имя стола</b>
			</td>
			<td align="center" width="250px" valign="top">
				<b>Игроки</b>
			</td>
			<td align="center" width="40px" valign="top">
				<b>Кол-во игроков</b>
			</td>
			<td align="center" width="80px" valign="top">
				<b>Ограничение по игрокам</b>
			</td>
			<td align="center" width="150px" valign="top">
				<b>Статус игры</b>
			</td>
			<td align="center" valign="top">
				<b>Команда</b>
			</td>                                
		</tr>
	<?php
	while($row = mysql_fetch_array($result))
	{
		$rowuser = mysql_fetch_assoc($mysql->sql_query("SELECT * FROM users WHERE login='".$row['creator']."'"));
		$createuserid = $rowuser["id_user"];
		$userlevel = $rowuser["level"];

		$gamer = "<a title=\"Профиль игрока\" class=\"clickablelogin\" href=\"javascript:showuser(".$createuserid.")\" >".$row['creator']."($userlevel)</a>";
		
		//Кто присоединился к столу
		$link_usrnotcr = $mysql->sql_query('SELECT * FROM users WHERE ((id_gt='.$row['id_gt'].') AND (login<>"'.$row['creator'].'"))');
		
        if(mysql_num_rows($link_usrnotcr)!=0)
		{
			while ($row1 = mysql_fetch_array($link_usrnotcr))
			{
				$gamer = $gamer.", <a style=\"font-weight:normal;\" title=\"Профиль игрока\" class=\"clickablelogin\" href=\"javascript:showuser(".$row1['id_user'].")\" >".$row1['login'] ."(".$row1['level'].")</a>";
			}
		}
		
		if ( ($row['gt_status']==0) || ($row['gt_status']==2) )
		{
			$gt_status="Игра еще не начата!<br/>До начала игры:".($row['start_game']-time())." сек."; 
		}
		else
		{
			$lastacttime = "";
			$differencetime = time() - $row['timestamp'];
			
			if($differencetime<24*3600)
			{
				if($differencetime<60*60)
				{
					$lastacttime = ceil($differencetime / 60) . " минут назад";
				}
				else
				{
					$lastacttime = ceil($differencetime / 3600) . " часов ";
					$lastacttime .= ceil((ceil($differencetime / 3600)*3600 - $differencetime) / 60) . " минут назад ";
				}
			}
			else
			{
				if(date("d",$row['timestamp']) == date("d"))
				{
					$lastacttime = "Сегодня в ";
				}
				elseif(date("d",$row['timestamp']) == date("d")-1)
				{
					$lastacttime = "Вчера в ";
				}
				else
				{
					$lastacttime .= date("d.m",$row['timestamp'])." в ";
				}
				$lastacttime .= date("H:i:s",$row['timestamp']);
			}
			$gt_status="Игра уже началась. <br/><small>Последняя активность:<br/>$lastacttime</small>"; 
		}
		?>
		<tr>
			<td align="center" valign="top">
				<?=$row['name'] ?>
			</td>
			<td align="center" valign="top">
				<?=$gamer ?>
			</td>
			<td align="center" valign="top">
				<?=$row['num_user'] ?>
			</td>
			<td align="center" valign="top">
				<?=$row['limit_user'] ?>
			</td>
			<td align="center" valign="top">
				<?=$gt_status ?>
			</td>
			<td align="center" valign="top">
				<?php
				if ($button==1)
				{  
					if ( (($row['gt_status']==0) || ($row['gt_status']==2) || ($row['gt_status']==3)) && ($row['num_user']<$row['limit_user']) )
					{	
						?><span class="join_game" value="<?=$row['id_gt'] ?>" title="Присоединиться"><img alt="Присоединиться" width="32px" src="picture/joingame.png" /></span>
						&nbsp;&nbsp;&nbsp;
						<span  class="spectatemode" onClick="location.href='spectate.php?id=<?=$row['id_gt'] ?>'" title="Режим наблюдателя"><img alt="Режим наблюдателя" width="32px" src="picture/binoculars_big.png" /></span>
						<?php
					}
					else
					{
						?><span class="spectatemode" onClick="location.href='spectate.php?id=<?=$row['id_gt'] ?>'" title="Режим наблюдателя"><img alt="Режим наблюдателя" width="32px" src="picture/binoculars_big.png" /></span><?php
					}
				}
				elseif ($button==2)
				{
					if ($row['num_user']>=1)
					{
						//ТЕСТ может играть 1 игрок
						?><span class="start_game" value="<?=$row['id_gt'] ?>" title="Начать игру"><img alt="Начать игру" width="32px" src="picture/startgame.png" /></span><?php
					}
					?>&nbsp;&nbsp;&nbsp;<span class="destroy_table" value="<?=$row['id_gt'] ?>" title="Удалить стол"><img alt="Удалить стол" width="32px" src="picture/deletetable.png" /></span><?php
				}
				elseif ($button==3)
				{
					?><span class="spectatemode" onClick="location.href='spectate.php?id=<?=$row['id_gt'] ?>'" title="Режим наблюдателя"><img alt="Режим наблюдателя" width="32px" src="picture/binoculars_big.png" /></span><?php
				}
				elseif ($button==4)
				{
					?><span class="leave_table" value="<?=$row['id_gt'] ?>"title="Выйти из-за стола"><img alt="Выйти из-за стола" width="32px" src="picture/exitgame.png" /></span><?php
				}
				elseif ($button==5)
				{
					?><span style="width:33px;" class="return_game" value="<?=$row['id_gt'] ?>" title="Войти в игру" ><img alt="Войти в игру" width="32px" src="picture/entergame.png" /></span><?php
					?>&nbsp;&nbsp;&nbsp;<span style="width:33px;" class="destroy_table_whole" value="<?=$row['id_gt'] ?>" title="Удалить стол. Игра оборвется для всех игроков!"><img alt="Удалить стол" width="32px" src="picture/deletetable.png" /></span><?php                  
				}
				elseif ($button==6)
				{
					?><span class="return_game" value="<?=$row['id_gt'] ?>"  title="Войти в игру"><img alt="Войти в игру" width="32px" src="picture/entergame.png" /></span>
					&nbsp;&nbsp;&nbsp;<span class="leave_table" value="<?=$row['id_gt'] ?>" title="Выйти из-за стола"><img alt="Выйти из-за стола" width="32px" src="picture/exitgame.png" /></span><?php
				}
				?>
			</td>                                            
		</tr>
		<?php
	}
}

function your_table()
{
	$mysql = new MySQL;

	$link_gt = $mysql->sql_query("SELECT * FROM users JOIN game_tables ON (users.id_gt=game_tables.id_gt) WHERE users.id_user=".$_SESSION['id_user']."");
	
	//Вот с этой хренью нужно что-то сделать!
	$link_gt_double = $mysql->sql_query("SELECT * FROM users JOIN game_tables ON (users.id_gt=game_tables.id_gt) WHERE users.id_user=".$_SESSION['id_user']."");
	$row_gt = mysql_fetch_array($link_gt_double);
	
    if ($row_gt['id_gt']==0)
	{
		?>
		<script>
		function trycreate_table()
		{
			if(document.form_create_table.table_name.value=='')
			{
				alert('Вы не ввели имя стола');
				return false;
			}
			else
			{
				document.form_create_table.submit();
			}
		}
		</script>
		<h2>Создать новый игровой стол.</h2>
		<table class="newdtable" border="0" />
		<form name="form_create_table" id="form_create_table" action="create_table.php" method="post">
			<tr>
				<td>
					<b>Имя стола:</b>
				</td>
				<td>
					<input name="table_name" type="text" size="20" maxlength="15" value="" />
				</td>
			</tr>
			<tr>
				<td>
					<b>Количество игроков:</b>
				</td>
				<td>
					<select name="num_user">
						<option value="3">&nbsp;3&nbsp;</option>
						<option value="4">&nbsp;4&nbsp;</option>
						<option value="5">&nbsp;5&nbsp;</option>
						<option value="6">&nbsp;6&nbsp;</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					<b>Время ожидания игроков:</b>
				</td>
				<td>
					<select name="num_time">
						<option value="180">&nbsp;3 мин.</option>
						<option value="300">&nbsp;5 мин.</option>
						<option value="600">&nbsp;10 мин.</option>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="2" align="center">
					<input name="allow_join" type="checkbox" value="1" checked /> <b style="cursor:pointer; border-bottom:1px #656565 dotted;" disabled="true" onClick="if(document.form_create_table.allow_join.checked == true){document.form_create_table.allow_join.checked = false; } else {document.form_create_table.allow_join.checked = true; }" >Разрешить присоединяться к уже начатой игре</b>
				</td>
			</tr>
		</form>
			<tr>
				<td colspan="2" align="center">
					<button class="submit" OnClick="trycreate_table()" >Создать</button>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<small class="hint">(Игра находится в тестовом режиме. Можно играть даже с одним игроком за столом)</small>
				</td>
			</tr>
		</table>
		<?php
    }
	else
	{
		//Если стол создали вы
		if ($row_gt['creator']==$row_gt['login'])
		{
			//Если стол еще не инициализирован
			if (($row_gt['gt_status']==0) || ($row_gt['gt_status']==2))
			{
				?>
				<h2>Созданный вами игровой стол</h2>
				<table width="100%" align="center" border="1" style="border-collapse:collapse;" cellpadding="5" cellspacing="2">
					<tbody>
					<?php print_result($link_gt,2); ?>
					</tbody>
				</table>
				<?php
				
				if(($row_gt['start_game']-time()) <= 0)
				{
					init_game();
				}  
			}
			else
			//Стол уже инициализирован
			{
				?>
				<h2>Созданный вами игровой стол</h2>
				<table width="100%" align="center" border="1" style="border-collapse:collapse;" cellpadding="5" cellspacing="2">
					<tbody>
					<?php print_result($link_gt,5); ?>
					</tbody>
				</table>
				<?php
			}  
		}
		else
		{
			//Если стол создали не вы, а вы только к нему присоединились
			
			//Если стол еще не инициализирован
			if (($row_gt['gt_status']==0) || ($row_gt['gt_status']==2))
			{
				$auth = "<h2>Стол к которому вы присоединились</h2>\n";
				echo $auth;          
				echo '<table width="100%" align="center" border="1" style="border-collapse:collapse;" cellpadding="5" cellspacing="2">
				<tbody>'; 
				print_result($link_gt,4);    
				echo  '</tbody>
				</table> ';    
				//Стол уже инециализирован
			}
			else
			{
				$auth = "<h2>Стол к которому вы присоединились</h2>\n";
				echo $auth;          
				echo '<table width="100%" align="center" border="1" style="border-collapse:collapse;" cellpadding="5" cellspacing="2">
				<tbody>'; 
				print_result($link_gt,6);    
				echo  '</tbody>
				</table> ';                        
			}
		}         
    }         
}

function show_table()
{
	$mysql = new MySQL;
	?>
	<h2>Присоединиться к игровому столу.</h2>
	<table width="100%" align="center" border="1" style="border-collapse:collapse;" cellpadding="5" cellspacing="2">
	<tbody>
	<?php
	$link_users = $mysql->sql_query('SELECT * FROM users WHERE ((id_user='.$_SESSION['id_user'].'))');
	$row_user = mysql_fetch_array($link_users);

	$result = $mysql->sql_query('SELECT * FROM game_tables WHERE (id_gt<>"'.$row_user["id_gt"].'") AND (start_game > '.time().' OR (gt_status=1 OR gt_status=3)) ORDER BY timestamp DESC LIMIT 20');
	
	if (mysql_num_rows($result)==0) 
	{
		?>
		<tr>
			<td align="center" valign="top">
				Нету ни одного свободного игрового стола.<br/> Создайте сами новый игровой стол, к вам обязательно кто нибудь присоединится. 
			</td>
		</tr>
		<?php
	}
	else
	{
		if ($row_user['id_gt']==0)
		{
			print_result($result,1);
		}
		else
		{
			print_result($result,3);
		}
	}  

	?>
	</tbody>
	</table>
	<?php

}

if (isset($_REQUEST['send_com']))
{
	if(!isset($_SESSION)){session_start();}
	if (isset($_SESSION['id_user']))
	{
		if ($_REQUEST['send_com']==1)
		{        
			show_table();  
		}
		elseif($_REQUEST['send_com']==2)
		{
			your_table();  
		}
	}
}

?>