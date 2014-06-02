<?php
session_start();

require_once("modules/mysql.php");

if(!isset($_SESSION["login"]))die("Вы не авторизированы для этого действия<span style=\"cursor:pointer;\" onClick=\"hideuser()\"><img src=\"picture/cross.png\"></span>");

if(isset($_GET["id"]) && is_numeric($_GET["id"]))
{
	$id = $_GET["id"];

	$row_user = mysql_fetch_assoc($mysql->sql_query("SELECT * FROM users WHERE id_user='".$_GET["id"]."'"));
	
	$countgames = mysql_num_rows($mysql->sql_query("SELECT * FROM statistic_game WHERE id_user=".$id.""));
	
	$victories = mysql_num_rows($mysql->sql_query("SELECT * FROM statistic_game WHERE id_user=".$id." AND winner='".$row_user["login"]."' AND status_game='end_game_victory'"));
	
	//Places
	$lastpage = $row_user["last_page"];
	if(strlen($lastpage)>0)
	{
		if(stristr($lastpage,"profile.htm") || stristr($lastpage,"kabin")){$lastpage = "Свой профиль";}
		elseif(stristr($lastpage,"help.htm")){$lastpage = "Справка";}
		elseif(stristr($lastpage,"gamerule.htm")){$lastpage = "Правила игры";}
		elseif(stristr($lastpage,"rights.htm")){$lastpage = "Права";}
		elseif(stristr($lastpage,"watch.htm")){$lastpage = "Просмотр активных столов";}
		elseif(stristr($lastpage,"changeprofile.htm")){$lastpage = "Редактирование профиля";}
		elseif(stristr($lastpage,"profile_")){$lastpage = "Профиль другого пользователя";}
		elseif(stristr($lastpage,"mysettings")){$lastpage = "Настройки";}
		elseif(stristr($lastpage,"friends")){$lastpage = "Свои друзья";}
		elseif(stristr($lastpage,"friendsof")){$lastpage = "Друзя другого пользователя";}
		elseif(stristr($lastpage,"searchusers")){$lastpage = "Поиск пользователей";}
		elseif(stristr($lastpage,"whoonline")){$lastpage = "Кто онлайн";}
		elseif(stristr($lastpage,"msgs") || stristr($lastpage,"message")){$lastpage = "Сообщения";}
		elseif(stristr($lastpage,"home") || stristr($lastpage,"index.htm")){$lastpage = "Главная страница";}
		elseif(stristr($lastpage,"gmenu")){$lastpage = "Игровое меню";}
		elseif(stristr($lastpage,"stat")){$lastpage = "Статистика";}
		elseif(stristr($lastpage,"login")){$lastpage = "Авторизация";}
		elseif(stristr($lastpage,"game")){$lastpage = "В игре";}
		//book
	}
	
	$threeminleft = time() - (3*60);
	if($row_user["active"]>$threeminleft){$online = true;}else{$online = false;}
	
	?>
	<table cellpadding="3" cellspacing="3" border="0">
		<tr>
			<td width="400px" colspan="3" align="center" style="background:#FFD0A0;">
				Манчкиновец <a style="<?php if($online){echo "color:green;";} ?>" title="Перейти к профилю пользователя" target="_blank" href="profile_<?=$row_user["id_user"] ?>.htm"><b><?=$row_user["login"] ?></b></a>
			</td>
		</tr>
		<tr>
			<td width="102px">
				<table>
					<tr>
						<td>
							<?php
							if(strlen($row_user["image"])>1)
							{?><img src="picture/users/<?=$row_user["image"] ?>" width="100" border="0" alt="Avatar"/><?php }
							else
							{?><img src="picture/users/default.png" width="100" height="100" border="0" alt="Avatar"/><?php }
							?>
						</td>
					</tr>
					<tr>
						<td align="center">
							<?php 
							if($_SESSION["id_user"]!==$row_user["id_user"])
							{
							?>
							<table align="center">
								<tr>
									<td>
										<span title="Отправить игроку сообщение" class="playerinfomes" onClick="document.getElementById('messageuserarea').style.display='inline';">&nbsp;</span>
									</td>
									<td>
										<?php
										//Может уже друзья?
										$checkfriends = $mysql->sql_query("SELECT * FROM friends WHERE (user1='".$_SESSION["id_user"]."' AND user2='".$row_user["id_user"]."') OR (user2='".$_SESSION["id_user"]."' AND user1='".$row_user["id_user"]."')");
										//Ссылка на добавление в друзья
										if(mysql_num_rows($checkfriends)<1)
										{
											?>
											<span title="Добавить в друзья" class="playerinfofrnd" onClick="frienduser(<?=$row_user["id_user"] ?>)"></span>
											<?php
										}
										?>
									</td>
								</tr>
							</table>
							<?php
							}
							else echo "Это Вы";
							?>
						</td>
					</tr>
				</table>
			</td>
			<td align="left" valign="top" >
				Уровень: <u><?=$row_user["level"] ?></u><br/>
				Опыт: <u><?=$row_user["exper"] ?></u><br/>
				
				<div style="color:#5C4F3C; font-size:12px; line-height:17px; ">
				Сыграно игр: <?=$countgames ?><br/>
				Победы: <?=$victories ?><br/>
				Зарегистрирован<?php if($row_user["sex"]=="ж") echo "а"; ?>: <?php echo date("d.m.Y",$row_user["timeactive"]); ?><br/>
				Был<?php if($row_user["sex"]=="ж") echo "а"; ?> здесь: <?php if(strlen($row_user["active"])>1){echo date("d.m.Y H:i",$row_user["active"]);}else{echo "нет";} ?><br/>
				Откуда: <?php if(strlen($row_user["city"])>0){echo $row_user["city"];}else{echo "-";}?><br/>
				</div>
			</td>
			<td width="20px" valign="top" align="right">
				<span title="Закрыть окно информации" style="cursor:pointer;" onClick="hideuser()"><img src="picture/cross.png"></span>
			</td>
		</tr>
	</table>
	<div id="messageuserarea" style="display:none; margin-left:5px;">
		<span class="hint" style="margin:0px auto;">Отправить сообщение</span><br/>
		<textarea id="usermessagetext" style="width:100%; height:39px;"></textarea><br/>
		<button OnClick="sendusermessage('<?=$row_user["id_user"] ?>')">Отправить</button>
		<button OnClick="document.getElementById('messageuserarea').style.display='none';">Отмена</button>
	</div>
	<?php
}
?>