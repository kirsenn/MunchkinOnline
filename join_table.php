<?php
if(!isset($_SESSION)){session_start();}

require_once("global.php");
require_once("show_table_games.php"); 
require_once("init_game.php");
require_once("chat.php");
require_once("modules/mysql.php");

//Игрок присоединяется к столу
if ((isset($_SESSION['id_user'])) && (isset($_REQUEST['id_gt'])))
{
	$id_user = $_SESSION['id_user'];
	$login = $_SESSION['login'];
	
	//Проверяем на правильность данные пришедшие от пользователя
	if (is_numeric($_REQUEST['id_gt']))
	{
		$id_gt=$_REQUEST['id_gt'];
		
		//Может уже создал стол
		$linkcheckcreator = $mysql->sql_query("SELECT * FROM game_tables WHERE creator='$login' AND id_gt='$id_gt' ");
		if (mysql_num_rows($linkcheckcreator)!==0)
		{
			die("<a href=\"gamemenu.php\">назад</a><br/>Вы не можете присоединиться к игровому столу, так как вы уже создали свой стол");
		}
		
		//Who are you?
		$getuserinfo = $mysql->sql_query("SELECT * FROM users WHERE id_user=$id_user");
		$row_user = mysql_fetch_array($getuserinfo);
		
		if ($row['id_gt']==0)
		{
			$link_gt = $mysql->sql_query("SELECT * FROM game_tables WHERE id_gt='$id_gt'");
			$row_gt = mysql_fetch_array($link_gt);

			//К игре можно присоединиться только в том случае если игра еще не началась gt_status=0 либо 2 и если игра началась но стояла галочка разрешить присоединяться к начавтой игре gt_status=3	
			if ((($row_gt['gt_status']==0) || ($row_gt['gt_status']==2) || ($row_gt['gt_status']==3)) && ($row_gt['num_user']<$row_gt['limit_user']) )
			{
				$mysql->sql_query("UPDATE users SET id_gt=$id_gt WHERE id_user=$id_user");
				
				$link_numusers = $mysql->sql_query("SELECT COUNT(*) FROM users WHERE id_gt=$id_gt");
				$row = mysql_fetch_array($link_numusers);
				$mysql->sql_query("UPDATE game_tables SET num_user=".$row[0]." WHERE id_gt=$id_gt");

				//Если игра уже началась, а игрок к ней присоединился
				if($row_gt['gt_status']==3)
				{
					//Проверяем инициализирована ли статистика ИГРЫ
					$link_statistic = $mysql->sql_query("SELECT * FROM statistic_game WHERE id_user=$id_user AND id_gt=$id_gt");
					if (mysql_num_rows($link_statistic)<1)
					{
						$num_useradd = $row_gt['num_user']+1;
						//Если статистики нет то добавляем ее	
						$mysql->sql_query("INSERT INTO statistic_game (id_gt, name_gt, id_user, num_user, start_game, status_game) VALUES ('$id_gt', '".$row_gt['name']."', $id_user, $num_useradd, ".time().",'game_init')");
						
						//Меняем количество игроков за столом в статистике
						$mysql->sql_query("UPDATE statistic_game SET num_user=$num_useradd WHERE id_gt='$id_gt'");
					}
					else
					{
						$mysql->sql_query("UPDATE statistic_game SET pro=0, con=0,vote=0, time_limit=0, winner='0' WHERE id_gt=".$id_gt."");
					}
					//Инициализируем игру
					init_game();
					//Выводим сообщение в игровой чат
					$per_str = "ВНИМАНИЕ! К ИГРЕ ПРИСОЕДИНИЛСЯ [B]".$_SESSION['login']."[/B].";
					add_str($per_str,1);   //Системное сообщение
					
					$per_str = " вы зашли на игровой стол, поприветствуйте игроков в чате и присоединяйтесь играть, взяв 2 карты дверей и 2 карты сокровищ(перетащив их к себе в руку)";
					add_str($per_str,0);   //Системное сообщение
				}
				else
				{
					if ($row[0]>=$row_gt['limit_user'])
					{
						init_game();
					}
				}
			}
			else
			{
				die("<a href=\"gamemenu.php\">назад</a><br/> Вы не можете присоединиться к игровому столу игра уже началась,либо игроков достаточно<br />\n");   
			}

		}
		else
		{
			die("Вы уже итак сидите за игровым столом");   
		}

	}
	else 
	{
		//если данные пришедшие от пользователя не корректны
		die("Вы послали на сервер запрещенные данные"); 
	}
}
?>