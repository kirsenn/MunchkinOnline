<?php
if(!isset($_SESSION)){session_start();}
  
if (isset($_SESSION['id_user'])) 
{
	$id_user=$_SESSION['id_user'];
	$login=$_SESSION['login'];
	
	//Просмотр своего профиля
	if($_GET["profile"]=="my")
	{
		$getuinfo = $mysql->sql_query("SELECT * FROM users WHERE id_user=".$id_user."");
		$udata = mysql_fetch_array($getuinfo);
		
		$get_stats = $mysql->sql_query("SELECT * FROM statistic_game WHERE id_user=".$id_user."");
		$countgames = mysql_num_rows($get_stats);
		
		$get_visctories = $mysql->sql_query("SELECT * FROM statistic_game WHERE id_user=".$id_user." AND winner='".$login."'");
		$victories = mysql_num_rows($get_visctories);
		
		$get_new_msg = $mysql->sql_query("SELECT * FROM messages WHERE `to`='$id_user' AND isread=0");
		$new_msg_cnt = mysql_num_rows($get_new_msg);
		if($new_msg_cnt<1){$new_msg_cnt="нет новых";}

		?>
		<div align="center" style="margin: 0px auto;"><h2>Мой профиль</h2></div>

		<table class="profile" width="100%" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td colspan="3" class="tdhead">
					<table width="100%">
						<tr>
							<td align="left">
								<b><?=$login?>, id=<?=$udata["id_user"] ?></b>
							</td>
							<td align="right">
								Зарегистрирован: <?php echo date("d.m.Y",$udata["timeactive"]); ?>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td width="150">
					<table width="100%">
						<tr>
							<td>
								<?php
								if(strlen($udata["image"])>1)
								{?><img class="useravatar" src="picture/users/<?=$udata["image"] ?>" width="150" border="0" alt="Avatar"/><?php }
								else
								{?><img class="useravatar" src="picture/users/default.png" width="150" height="150" border="0" alt="Avatar"/><?php }
								?>
							</td>
						</tr>
					</table>
				</td>
				<td valign="top" align="left">
					<table width="100%">
						<tr>
							<td>
								<span class="userinfostr">Имя: <b><?php if(strlen($udata["name"])>0){echo $udata["name"];}else{echo "не указано";}?></b></span>
								<span class="userinfostr">Фамилия: <b><?php if(strlen($udata["sname"])>0){echo $udata["sname"];}else{echo "не указана";}?></b></span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="userinfostr">E-mail: <b><?=$udata["email"] ?></b></span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="userinfostr">Дата рождения: <b><?php if(strlen($udata["birth"])>1){echo date("d.m.Y",$udata["birth"]);}else{echo "Не указана";} ?></b></span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="userinfostr">Пол: <b><?=$udata["sex"] ?></b>
							</td>
						</tr>
						<tr>
							<td>
								<span class="userinfostr">Сайт: <b>
								<?php 
								if(strlen($udata["www"])>0)
								{
									$www = $udata["www"];
									if(stristr($www,"http://")){$www = preg_replace("|(http://.+\.[a-z]{2,6})|i","<a href=\"$1\" target=\"_blank\">$1</a>",$www);}
									elseif(stristr($www,"www") && !stristr($www,"http://")){$www = preg_replace("|(www\..+\.[a-z]{2,6})|i","<a href=\"http://$1\" target=\"_blank\">$1</a>",$www);}
									echo $www;
								}
								else
								{
									echo "Не указан";
								}
								?>
								</b></span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="userinfostr">О себе: <b><?php if(strlen($udata["about"])>0){echo $udata["about"];}else{echo "Нет информации";}?></b></span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="userinfostr">Город: <b><?php if(strlen($udata["city"])>0){echo $udata["city"];}else{echo "Не указан";}?></b></span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="userinfostr">ICQ: <b><?php if(strlen($udata["icq"])>0){echo $udata["icq"];}else{echo "Не указан";}?></b></span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="userinfostr">Skype: <b><?php if(strlen($udata["skype"])>0){echo $udata["skype"];}else{echo "Не указан";}?></b></span>
							</td>
						</tr>
					</table>
				</td>
				<td valign="top" align="left">
					<table width="100%">
						<tr>
							<td>
								<span class="userinfostr">Уровень: <b><?=$udata["level"] ?></b></span>
							</td>
						</tr>
						<tr>
							<td>
								<span title="Текущий опыт / Опыт до следующего уровня" class="userinfostr">Опыт: <b><?=$udata["exper"] ?> / <?php echo get_exper_level($udata["level"]); ?></b></span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="userinfostr">Игр сыграно: <b><?=$countgames?></b></span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="userinfostr">Побед: <b><?=$victories?></b></span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="userinfostr"><a href="statgame.htm">Последняя игра</a></span>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<br/>

		<table width="100%" border="0" cellpadding="3" cellspacing="3">
			<tr>
				<td width="33%" class="tdhead">
					<b><a href="inmsgs.htm">Сообщения:</a></b> <?=$new_msg_cnt?>
				</td>
				<td width="33%" class="tdhead">
					<b><a href="changeprofile.htm">Изменить профиль</a></b>
				</td>
				<td width="33%" class="tdhead">
					<b><a href="mysettings.htm">Настройки</a></b>
				</td>
			</tr>
		</table>
		<br/>

		<table class="profile" width="100%" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td colspan="3" class="tdhead">
					<table width="100%">
						<tr>
							<td>
								<?php
								$getnewfriends = $mysql->sql_query("SELECT * FROM friends WHERE user2='$id_user' AND status=0  ORDER BY id DESC");
								$countfriends = mysql_num_rows($getnewfriends);
								?>
								<b><a href="friends.htm">Мои друзья <?php if($countfriends>0){echo "(<span style=\"color:red\">$countfriends</span>)";} ?></a></b>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<br/>

		<table class="profile" width="100%" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td colspan="3" class="tdhead">
					<table width="100%">
						<tr>
							<td>
								<a href="searchusers.htm"><b>Поиск пользователей</b></a>
							</td>
						</tr>
						<tr>
							<td>
								<form action="searchusers.htm" method="post">
								<table align="center">
									<tr>
										<td>
											ID:
										</td>
										<td>
											<input type="text" name="searchid" value="<?=$_POST["searchid"]?>" size="2" />
										</td>
										<td>
											Логин:
										</td>
										<td>
											<input type="text" name="ulogin" value="<?=$_POST["ulogin"]?>" size="9" />
										</td>
										<td>
											Имя:
										</td>
										<td>
											<input type="text" name="name" value="<?=$_POST["name"]?>"  size="9" />
										</td>
										<td>
											Фамилия:
										</td>
										<td>
											<input type="text" name="sname" value="<?=$_POST["sname"]?>"  size="9" />
										</td>
										<td colspan="2">
											<input type="checkbox" name="onlineonly" value="Только онлайн" <?php if(isset($_POST["onlineonly"])){echo "checked";} ?> /> Онлайн
										</td>
										<td>
										</td>
										<td>
											<input type="submit" value="Искать" />
										</td>
									</tr>
								</table>
								</form>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>

		<?php
	}
	
	//Просмотр чужого профиля
	if($_GET["profile"]=="other" && isset($_GET["id"]))
	{
		$id = safform($_GET["id"]);
		if(!is_numeric($id)){die("Hack stop");}
		if($id == $id_user){die("<script>location.href='profile.htm';</script>");}
		
		$getuinfo = $mysql->sql_query("SELECT * FROM users WHERE id_user=".$id."");
		if(mysql_num_rows($getuinfo)<1){die("Такого пользователя не существует");}
		$udata = mysql_fetch_assoc($getuinfo);
		
		$get_stats = $mysql->sql_query("SELECT * FROM statistic_game WHERE id_user=".$id."");
		$countgames = mysql_num_rows($get_stats);
		
		$get_visctories = $mysql->sql_query("SELECT * FROM statistic_game WHERE id_user=".$id." AND winner='".$udata["login"]."'");
		$victories = mysql_num_rows($get_visctories);
		
		$get_allow_email = $mysql->sql_query("SELECT showemail FROM users_settings WHERE id_user='$id'");
		if(mysql_num_rows($get_allow_email)>0){$allow_email = mysql_result($get_allow_email,0);}else{$allow_email = 0;}
		
		//Places
		$lastpage = $udata["last_page"];
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
		
		?>
		<script src="modules/functions.js"></script>
		
		<div align="center" style="margin: 0px auto;"><h2>Профиль пользователя <?=$udata["login"]?></h2></div>
		
		<div id="writemsgform" style="display:none; opacity:0.1;">
			<form method="post">
			<table>
				<tr>
					<td colspan="2">
						Сообщение пользователю <b><?=$udata["login"]?></b>
					</td>
				</tr>
				<tr>
					<td>
						Тема:
					</td>
					<td>
						<input type="text" name="subject" />
					</td>
				</tr>
				<tr>
					<td valign="top">
						Текст:
					</td>
					<td>
						<textarea name="text" cols="19" rows="10"></textarea>
					</td>
				</tr>
				<tr>
					<td>
						
					</td>
					<td>
						<input type="submit" value="Отправить" />
						<a href="javascript:hidean();">Отмена</a>
					</td>
				</tr>
			</table>
			</form>
		</div>
		
		<?php
		if(isset($_POST["text"]))
		{
			$subject = safform($_POST["subject"]);
			$text = safform($_POST["text"]);
			$date = time();
			$cansend = true;
			
			if(strlen($subject)<1){$cansend = false; echo "<script>alert('Вы не ввели тему сообщения'); window.history.back(-1);</script>";}
			if(strlen($text)<1){$cansend = false; echo "<script>alert('Вы не ввели текст сообщения'); window.history.back(-1);</script>";}
			
			if($cansend)
			{
				$mysql->sql_query("INSERT INTO messages VALUES (0,'".$udata["id_user"]."','".$id_user."','$subject','$text','$date',0)");
				echo "<script>alert('Ваше сообщение отправлено'); location.href='profile_".$udata["id_user"].".htm';</script>";
			}
		}
		?>
		
		<table class="profile" width="100%" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td colspan="3" class="tdhead">
					<table width="100%">
						<tr>
							<td align="left">
								<b>id=<?=$udata["id_user"] ?></b><br/>
								<?php
								$threeminleft = time() - (3*60);
								if($udata["active"]>$threeminleft){echo "<span style=\"color:green\">Онлайн</span>";}
								else
								{echo "<span style=\"color:red\">Не в сети</span>";}
								?>
							</td>
							<td align="right">
								Зарегистрирован: <?php echo date("d.m.Y",$udata["timeactive"]); ?><br/>
								Последнее посещение: <?php if(strlen($udata["active"])>1){echo date("d.m.Y",$udata["active"]);}else{echo "нет";} ?>
								<br/>Где был последний раз: <?=$lastpage ?>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td width="150">
					<table width="100%">
						<tr>
							<td>
								<a style="font-size:12px;" class="msgbtn" href="javascript:showan()">Сообщение</a><br/>
								<?php
								//Может уже друзья?
								$checkfriends = $mysql->sql_query("SELECT * FROM friends WHERE (user1='$id_user' AND user2='".$udata["id_user"]."') OR (user2='$id_user' AND user1='".$udata["id_user"]."')");
								//Ссылка на добавление в друзья
								if(mysql_num_rows($checkfriends)<1 && !isset($_GET["request"]))
								{
									?>
									<a style="font-size:12px;" class="msgbtn" href="reqprofile_<?=$udata["id_user"] ?>.htm">Добавить в друзья</a><br/>
									<?php
								}
								//Хотим быть друзьями
								elseif(mysql_num_rows($checkfriends)<1 && isset($_GET["request"]))
								{
									if($id_user == $udata["id_user"])
									{
										echo "<script>alert('Вы не можете предложить дружбу себе');</script>";
									}
									else
									{
										$addrequset = $mysql->sql_query("INSERT INTO friends VALUES (0,'$id_user','".$udata["id_user"]."',0)");
										echo "Отправлен запрос на добавление в друзья<br/><br/>";
									}
								}
								
								if(strlen($udata["image"])>1)
								{
									?><img src="picture/users/<?=$udata["image"] ?>" width="150" border="0" alt="Avatar"/><?php
								}
								else
								{
									?><img src="picture/users/default.png" width="150" border="0" alt="Avatar"/><?php
								}
								?>
							</td>
						</tr>
					</table>
				</td>
				<td valign="top" align="left">
					<table width="100%">
						<tr>
							<td>
								<span class="userinfostr">Имя: <b><?php if(strlen($udata["name"])>0){echo $udata["name"];}else{echo "не указано";}?></b></span>
								<span class="userinfostr">Фамилия: <b><?php if(strlen($udata["sname"])>0){echo $udata["sname"];}else{echo "не указана";}?></b></span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="userinfostr">E-mail: <b><?php if($allow_email){echo $udata["email"];}else{echo "<адрес скрыт>";} ?></b></span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="userinfostr">Дата рождения: <b><?php if(strlen($udata["birth"])>1){echo date("d.m.Y",$udata["birth"]);}else{echo "Не указана";} ?></b></span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="userinfostr">Пол: <b><?=$udata["sex"] ?></b>
							</td>
						</tr>
						<tr>
							<td>
								<span class="userinfostr">Сайт: <b>
								<?php 
								if(strlen($udata["www"])>0)
								{
									$www = $udata["www"];
									if(stristr($www,"http://")){$www = preg_replace("|(http://.+\.[a-z]{2,6})|i","<a href=\"$1\" target=\"_blank\">$1</a>",$www);}
									elseif(stristr($www,"www") && !stristr($www,"http://")){$www = preg_replace("|(www\..+\.[a-z]{2,6})|i","<a href=\"http://$1\" target=\"_blank\">$1</a>",$www);}
									echo $www;
								}
								else
								{
									echo "Не указан";
								}
								?>
								</b></span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="userinfostr">О себе: <b><?php if(strlen($udata["about"])>0){echo $udata["about"];}else{echo "Нет информации";}?></b></span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="userinfostr">Город: <b><?php if(strlen($udata["city"])>0){echo $udata["city"];}else{echo "Не указан";}?></b></span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="userinfostr">ICQ: <b><?php if(strlen($udata["icq"])>0){echo $udata["icq"];}else{echo "Не указан";}?></b></span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="userinfostr">Skype: <b><?php if(strlen($udata["skype"])>0){echo $udata["skype"];}else{echo "Не указан";}?></b></span>
							</td>
						</tr>
					</table>
				</td>
				<td valign="top" align="left">
					<table width="100%">
						<tr>
							<td>
								<span class="userinfostr">Уровень: <b><?=$udata["level"] ?></b></span>
							</td>
						</tr>
						<tr>
							<td>
								<span title="Текущий опыт / Опыт до следующего уровня" class="userinfostr">Опыт: <b><?=$udata["exper"] ?> / <?php echo get_exper_level($udata["level"]); ?></b></span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="userinfostr">Игр сыграно: <b><?=$countgames?></b></span>
							</td>
						</tr>
						<tr>
							<td>
								<span class="userinfostr">Побед: <b><?=$victories?></b></span>
							</td>
						</tr>
						<tr>
							<td>
								<?php
								$lastidgt = mysql_result($mysql->sql_query("SELECT id_gt FROM statistic_game WHERE id_user='$id' ORDER BY end_game DESC LIMIT 1"),0);
								?>
								<span class="userinfostr"><a href="index.php?profile=statgame&idgt=<?=$lastidgt ?>">Последняя игра</a></span>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<br/>

		<table class="profile" width="100%" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td colspan="3" class="tdhead">
					<table width="100%">
						<tr>
							<td>
								<?php
								$getfriends = $mysql->sql_query("SELECT * FROM friends WHERE (user1='".$udata["id_user"]."' OR user2='".$udata["id_user"]."') AND status=1 ORDER BY id DESC");
								$countfriends = mysql_num_rows($getfriends);
								?>
								<b><a href="friendsof_<?=$udata["id_user"] ?>.htm">Друзья (<?=$countfriends?>)</a></b>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<br/>
		<div style="text-align:center; width:100%;"><a href="profile.htm">&laquo;&laquo; На страницу профиля</a></div>
		<?php
	}
	
	//Изменение своего профиля
	if($_GET["profile"]=="change")
	{
		$getuinfo = $mysql->sql_query("SELECT * FROM users WHERE id_user=".$id_user."");
		$udata = mysql_fetch_array($getuinfo);
		?>
		<form enctype="multipart/form-data" method="post">
		<table class="profile" width="100%" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td colspan="3" class="tdhead">
					<table width="100%">
						<tr>
							<td>
								<b>Редактировать профиль</b>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td valign="top" width="170">
					<table width="100%">
						<tr>
							<td valign="top">
								<?php
								if(strlen($udata["image"])>1)
								{
									?><img src="picture/users/<?=$udata["image"] ?>" width="150" border="0" alt="Avatar"/><?php
								}
								else
								{
									?><img src="picture/users/default.png" width="150" border="0" alt="Avatar"/><?php
								}
								?>
								<br/>
								Изменить (JPG, 3Мб)<br/>
								<input type="file" name="avatar" size="10"/>
							</td>
						</tr>
					</table>
				</td>
				<td valign="top" align="left">
					<table width="100%">
						<tr>
							<td>
								Имя:
							</td>
							<td>
								<input type="text" name="name" value="<?=$udata["name"] ?>" />
							</td>
						</tr>
						<tr>
							<td>
								Фамилия:
							</td>
							<td>
								<input type="text" name="sname" value="<?=$udata["sname"] ?>" />
							</td>
						</tr>
						<?php /*
						<tr>
							<td>
								E-mail: 
							</td>
							<td>
								<input type="text" name="email" value="<?=$udata["email"] ?>" />
							</td>
						</tr>
						*/ ?>
						<tr>
							<td>
								Дата рождения: <?php if(strlen($udata["birth"])>1){$d=date("d",$udata["birth"]);$m=date("m",$udata["birth"]);$y=date("Y",$udata["birth"]);}else{$d="дд";$m="мм";$y="гггг";} ?>
							</td>
							<td>
								<input type="text" name="bday" value="<?=$d ?>" size="3" />
								<input type="text" name="bmth" value="<?=$m ?>" size="3" />
								<input type="text" name="byar" value="<?=$y ?>" size="3" />
							</td>
						</tr>
						<tr>
							<td>
								Пол: 
							</td>
							<td>
								<select name="sex"><option value="м">м</option><option <?php if($udata["sex"]=="ж"){echo "selected";} ?> value="ж">ж</option></select>
							</td>
						</tr>
						<tr>
							<td>
								Сайт:
							</td>
							<td>
								<input type="text" name="www" value="<?=$udata["www"] ?>" />
							</td>
						</tr>
						<tr>
							<td>
								О себе:
							</td>
							<td>
								<textarea name="about" cols="40" rows="4"><?=$udata["about"] ?></textarea>
							</td>
						</tr>
						<tr>
							<td>
								Город:
							</td>
							<td>
								<input type="text" name="city" value="<?=$udata["city"] ?>" />
							</td>
						</tr>
						<tr>
							<td>
								ICQ:
							</td>
							<td>
								<input type="text" name="icq" value="<?=$udata["icq"] ?>" />
							</td>
						</tr>
						<tr>
							<td>
								Skype:
							</td>
							<td>
								<input type="text" name="skype" value="<?=$udata["skype"] ?>" />
							</td>
						</tr>
						<tr>
							<td><input type="submit" value="Сохранить" />
								<button OnClick="location.href='profile.htm'">Отмена</button>
							</td>
							<td>
								
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<div style="text-align:center; width:100%;"><a href="profile.htm">&laquo;&laquo; На страницу профиля</a></div>
		<?php
		//Сохранение профиля
		if(isset($_POST["name"]))
		{
			$name = safform($_POST["name"]);
			$sname = safform($_POST["sname"]);
			//$email = safform($_POST["email"]);
			$sex = safform($_POST["sex"]);
			$www = safform($_POST["www"]);
			$about = safform($_POST["about"]);
			$city = safform($_POST["city"]);
			$icq = safform($_POST["icq"]);
			$skype = safform($_POST["skype"]);
			
			
			//if(strlen($email)<1 || !preg_match("|^[-.\w]+@(?:[a-z\d][-a-z\d]+\.)+[a-z]{2,6}$|",$email)){die("<br/><b>Адрес электронной почты не введен или введен неправильно</b>");}
			
			//Если нам нужна смена аватара
			If(isset($_FILES['avatar']) && strlen($_FILES['avatar']['name'])>1)
			{
				include("modules/imageclass.php");
				$img = new Image;
				$img->max_w = 2048;
				$img->max_h = 2048;
				$img->max_s = 3048576;
				$img->allowed_types = array(2=>"image/jpeg",3=>"image/pjpeg");
				$ext = $img->getext($_FILES['avatar']['type']);
				$newname = $id_user.$ext;
				
				if($img->upload($_FILES['avatar'],"picture/users/",false,$newname))
				{
					$img->mk_preview("picture/users/",$newname);
					$chimage = ", image='$newname' ";
				}
			}
			
			//Дата рождения
			if(is_numeric($_POST["bday"]) && is_numeric($_POST["bmth"]) && is_numeric($_POST["byar"]))
			{
				$birth=mktime(0,0,0,$_POST["bmth"],$_POST["bday"],$_POST["byar"]);
				$chbirth = " , birth='$birth' ";
			}
			else
			{
				$chbirth = "";
			}
			
			//Сохраняем
			$mysql->sql_query("UPDATE users SET name='$name', sname='$sname', sex='$sex', www='$www', about='$about', city='$city', icq='$icq', skype='$skype' $chimage $chbirth WHERE id_user='$id_user'");
			echo "<script>location.href='profile.htm'</script>";
		}
	}
	
	
	//Просмотр личных сообщений
	if($_GET["profile"]=="inmsgs" || $_GET["profile"]=="outmsgs")
	{
		?>
		<div align="center" style="margin: 0px auto;"><h2>Личные сообщения</h2></div>
		<table width="100%">
			<tr>
				<td align="center"><a <?php if($_GET["profile"]=="inmsgs"){echo " style=\"color:#2C231A\" ";} ?> class="msgbtn" href="inmsgs.htm">Входящие</a></td>
				<td align="center"><a <?php if($_GET["profile"]=="outmsgs"){echo " style=\"color:#2C231A\" ";} ?> class="msgbtn" href="outmsgs.htm">Исходящие</a></td>
			</tr>
		</table>
		
		<?php 
		//Входящие сообщения
		if($_GET["profile"]=="inmsgs") 
		{
			$page = "inmsgs";
			$query = "SELECT * FROM messages WHERE `to`='$id_user' ORDER BY id DESC";
			$p_col = 20;
			$query  = link_pages($p_col,$query,$page);
			$get_msgs = $mysql->sql_query($query);
			$cnt = mysql_num_rows($get_msgs);
			if($cnt>0)
			{
				while($msgdata = mysql_fetch_assoc($get_msgs))
				{
					$get_user_info = $mysql->sql_query("SELECT * FROM users WHERE id_user='".$msgdata["from"]."'");
					$udata = mysql_fetch_assoc($get_user_info);
					?>
					<table class="msgbox" width="100%" cellpadding="0" cellspacing="0">
						<tr bgcolor="#EABA8B">
							<td align="left">
								От: <b><a href="profile_<?=$msgdata["from"]?>.htm"><?=$udata["login"] ?></a></b>
							</td>
							<td align="right">
								<?php echo date("d.m.Y H:i",$msgdata["date"]); ?>
							</td>
						</tr>
						<tr bgcolor="#F6C492">
							<td  align="justify">
								<b><?=$msgdata["subject"] ?></b><br/>
								<?php if($msgdata["isread"]==0){ ?><font color="red">Новое.</font><?php } ?> <a href="message_<?=$msgdata["id"] ?>.htm"><?php echo substr($msgdata["text"],0,124); if(strlen($msgdata["text"])>124){echo "...";} ?></a>
							</td>
							<td align="right">
								<?php
								if(strlen($udata["image"])>1)
								{
									?><img src="picture/users/<?=$udata["image"] ?>" width="35" border="0" alt="Avatar"/><?php
								}
								else
								{
									?><img src="picture/users/default.png" width="35" border="0" alt="Avatar"/><?php
								}
								?>
							</td>
						</tr>
					</table>
					<br/>
					<?php
				}
			}
			else
			{
				echo "Нет сообщений<br/>";
			}
			?>
			<div style="text-align:center; width:100%;"><a href="profile.htm">&laquo;&laquo; На страницу профиля</a></div>
			<?php
		}
		
		
		//ИСходящие сообщения
		if($_GET["profile"]=="outmsgs")
		{
			$page = "outmsgs";
			$query = "SELECT * FROM messages WHERE `from`='$id_user' ORDER BY id DESC";
			$p_col = 20;
			$query  = link_pages($p_col,$query,$page);
			
			$get_msgs = $mysql->sql_query($query);
			$cnt = mysql_num_rows($get_msgs);
			if($cnt>0)
			{
				while($msgdata = mysql_fetch_assoc($get_msgs))
				{
					$get_user_info = $mysql->sql_query("SELECT * FROM users WHERE id_user='".$msgdata["to"]."'");
					$udata = mysql_fetch_assoc($get_user_info);
					?>
					<table class="msgbox" width="100%" cellpadding="0" cellspacing="0">
						<tr bgcolor="#EABA8B">
							<td align="left">
								Кому: <b><a href="profile_<?=$msgdata["to"]?>.htm"><?=$udata["login"] ?></a></b>
							</td>
							<td align="right">
								<?php echo date("d.m.Y H:i",$msgdata["date"]); ?>
							</td>
						</tr>
						<tr bgcolor="#F6C492">
							<td align="justify">
								<b><?=$msgdata["subject"] ?></b><br/>
								<?=$msgdata["text"] ?>
							</td>
							<td align="right">
							<?php
								if(strlen($udata["image"])>1)
								{
									?><img src="picture/users/<?=$udata["image"] ?>" width="35" border="0" alt="Avatar"/><?php
								}
								else
								{
									?><img src="picture/users/default.png" width="35" border="0" alt="Avatar"/><?php
								}
								?>
							</td>
						</tr>
					</table>
					<br/>
					<?php
				}
			}
			else
			{
				echo "Нет сообщений<br/>";
			}
			?>
			<div style="text-align:center; width:100%;"><a href="profile.htm">&laquo;&laquo; На страницу профиля</a></div>
			<?php
		}
		
	}
	
	//Режим просмотра одного сообщения и формы ответа
	if($_GET["profile"]=="showmessage" && isset($_GET["id"]))
	{
		$id = safform($_GET["id"]);
		$get_msg = $mysql->sql_query("SELECT * FROM messages WHERE id='$id'");
		$msgdata = mysql_fetch_assoc($get_msg);
		$get_user_info = $mysql->sql_query("SELECT * FROM users WHERE id_user='".$msgdata["from"]."'");
		$udata = mysql_fetch_assoc($get_user_info);
		
		if($id_user!==$msgdata["to"]){die("Вы не можете просматривать чужие сообщения");}
		
		//Удаление сообщения ОТКЛЮЧЕНО
		/*
		if(isset($_GET["delete"]))
		{
			$mysql->sql_query("DELETE FROM messages WHERE id='$id'");
			echo "<script>alert('Ваше сообщение удалено'); location.href='inmsgs.htm';</script>";
		}
		*/
		
		?>
		<table class="msgbox" width="100%" cellpadding="0" cellspacing="0">
			<tr bgcolor="#EABA8B">
				<td align="left">
					От: <a href="profile_<?=$msgdata["from"]?>.htm"><?=$udata["login"] ?></a>
				</td>
				<td align="right">
					<?php echo date("d.m.Y H:i",$msgdata["date"]); ?>
				</td>
			</tr>
			<tr bgcolor="#F6C492">
				<td align="justify">
					<?php if($msgdata["isread"]==0){ $mysql->sql_query("UPDATE messages SET isread=1 WHERE id='$id'"); } ?>
					<b><?=$msgdata["subject"] ?></b><br/>
					<?=$msgdata["text"]?>
				</td>
				<td align="right">
				<?php
				if(strlen($udata["image"])>1)
				{
					?><img src="picture/users/<?=$udata["image"] ?>" width="35" border="0" alt="Avatar"/><?php
				}
				else
				{
					?><img src="picture/users/default.png" width="35" border="0" alt="Avatar"/><?php
				}
				?>
				</td>
			</tr>
			<!--
			<tr bgcolor="#F6C492">
				<td align="left">
					
				</td>
				<td align="right">
					<a href="deletemsg_<?=$msgdata["id"]?>.htm">Удалить</a>
				</td>
			</tr>
			-->
		</table>
		
		<form method="post">
		<table>
			<tr>
				<td>
					<b>Ответить</b>
				</td>
			</tr>
			<tr>
				<td>
					Кому:
				</td>
				<td>
					<b><?=$udata["login"] ?></b>
				</td>
			</tr>
			<tr>
				<td>
					Тема:
				</td>
				<td>
					<input type="text" name="subject" value="<?php echo "Re: ".$msgdata["subject"]; ?>" />
				</td>
			</tr>
			<tr>
				<td valign="top">
					Сообщение:
				</td>
				<td>
					<textarea name="text" cols="40" rows="10"></textarea>
				</td>
			</tr>
			<tr>
				<td valign="top">
				</td>
				<td>
					<input type="submit" value="Отправить" />
				</td>
			</tr>
		</table>
		</form>
		<div style="text-align:center; width:100%;"><a href="profile.htm">&laquo;&laquo; На страницу профиля</a></div>
		<?php
		//Ответ на сообщение
		if(isset($_POST["text"]))
		{
			$subject = safform($_POST["subject"]);
			$text = safform($_POST["text"]);
			$date = time();
			$cansend = true;
			
			if(strlen($text)<1){$cansend = false; echo "Вы не ввели текст сообщения";}
			if(strlen($subject)<1){$cansend = false; echo "Вы не ввели тему сообщения";}
			
			if($cansend)
			{
				$mysql->sql_query("INSERT INTO messages VALUES (0,'".$msgdata["from"]."','".$id_user."','$subject','$text','$date',0)");
				echo "<script>alert('Ваше сообщение отправлено'); location.href='inmsgs.htm';</script>";
			}
		}
	}
	
	//НАСТРОЙКИ
	if($_GET["profile"]=="settings")
	{
		?>
		<div align="center" style="margin: 0px auto;"><h2>Настройки</h2></div>
		<?php
		//Save settings
		if(isset($_POST["savesettings"]))
		{
			if(isset($_POST["showemail"])){$showemail=1;}else{$showemail=0;}
			if(isset($_POST["allownews"])){$allownews=1;}else{$allownews=0;}
			if(isset($_POST["soundon"])){$soundon=1;}else{$soundon=0;}
			$mysql->sql_query("UPDATE users_settings SET showemail='$showemail', allownews='$allownews', soundon='$soundon' WHERE id_user='$id_user'");
			echo "Настройки сохранены";
		}
		
		//Получение настроек
		$get_settings = $mysql->sql_query("SELECT * FROM users_settings WHERE id_user='$id_user'");
		//Если не существует настроек, то мы их создаем
		if(mysql_num_rows($get_settings)<1)
		{
			$mysql->sql_query("INSERT INTO users_settings (id_user) VALUES ('$id_user');");
			$get_settings = $mysql->sql_query("SELECT * FROM users_settings WHERE id_user='$id_user'");
		}
		$settings = mysql_fetch_assoc($get_settings);
		
		?>
		<form method="post">
			<table>
				<tr>
					<td>
						<input type="checkbox" name="showemail" <?php if($settings["showemail"]==1){echo "checked";} ?>/>
					</td>
					<td>
						Показывать мой email публично
					</td>
				</tr>
				<tr>
					<td>
						<input type="checkbox" name="allownews" <?php if($settings["allownews"]==1){echo "checked";} ?>/>
					</td>
					<td>
						Присылать мне новости игры на email
					</td>
				</tr>
				<tr>
					<td>
						<input type="checkbox" name="soundon" <?php if($settings["soundon"]==1){echo "checked";} ?>/>
					</td>
					<td>
						Включить звук в чате
					</td>
				</tr>
				<tr>
					<td>
					</td>
					<td><input type="submit" name="savesettings" value="Сохранить"/>
					</td>
				</tr>
			</table>
		</form>
		<div style="text-align:center; width:100%;"><a href="profile.htm">&laquo;&laquo; На страницу профиля</a></div>
		<?php
	}
	
	//Подтверждаем чтомы друзья или нет
	if($_GET["profile"]=="confirmfriends" || $_GET["profile"]=="rejectfriends")
	{
		$id=safform($_GET["id"]);
		
		//Существует ли такая дружба
		$checkfriendship = $mysql->sql_query("SELECT * FROM friends WHERE id='$id'");
		if(mysql_num_rows($checkfriendship)<1)
		{
			die("Ошибка. Такой записи не существует<br/><a href=\"javascript:window.history.back(-1)\">&laquo;&laquo; Назад</a>");
		}
		
		$frdata = mysql_fetch_assoc($checkfriendship);
		
		//А относится ли она к пользователю?
		if($frdata["user2"]!==$id_user)
		{
			die("Ошибка. Вы не можете подтверждать или отменять чужую дружбу<br/><a href=\"javascript:window.history.back(-1)\">&laquo;&laquo; Назад</a>");
		}
		
		//А может они уже друзья?
		if($frdata["status"]==1)
		{
			die("Ошибка. Дружба уже подтверждена<br/><a href=\"javascript:window.history.back(-1)\">&laquo;&laquo; Назад</a>");
		}
		
		if($_GET["profile"]=="confirmfriends")
		{
			$mysql->sql_query("UPDATE friends SET status=1 WHERE id='$id'");
			echo "<script>alert('Вы подтвердили что вы являетесь друзьями'); location.href='friends.htm';</script>";
		}
		elseif($_GET["profile"]=="rejectfriends")
		{
			$mysql->sql_query("DELETE FROM friends WHERE id='$id'");
			echo "<script>alert('Вы отказали в запросе на дружбу'); location.href='profile.htm';</script>";
		}
	}
	
	//Мои друзья
	if($_GET["profile"]=="friends")
	{
		?>
		<div align="center" style="margin: 0px auto;"><h2>Мои друзья</h2></div>
		
		<script src="modules/functions.js"></script>
		
		<div id="writemsgform" style="display:none; opacity:0.1;">
			<form method="post" name="msgform">
			<table>
				<tr>
					<td colspan="2">
						Сообщение пользователю <b><div id="touser"></div></b>
					</td>
				</tr>
				<tr>
					<td>
						Тема:
					</td>
					<td>
						<input type="text" name="subject" />
					</td>
				</tr>
				<tr>
					<td valign="top">
						Текст:
					</td>
					<td>
						<textarea name="text" cols="19" rows="10"></textarea>
					</td>
				</tr>
				<tr>
					<td>
						
					</td>
					<td>
						<input type="hidden" name="touser" value=""/>
						<input type="submit" value="Отправить" />
						<a href="javascript:hidean();">Отмена</a>
					</td>
				</tr>
			</table>
			</form>
		</div>
		
		<?php
		//Отправка сообщения
		if(isset($_POST["text"]))
		{
			$touser = safform($_POST["touser"]);
			$subject = safform($_POST["subject"]);
			$text = safform($_POST["text"]);
			$date = time();
			$cansend = true;
			
			if(mysql_num_rows($mysql->sql_query("SELECT * FROM users WHERE id_user='$touser'"))<1){$cansend = false; echo "<script>alert('Такого пользователя не существует'); window.history.back(-1);</script>";}
			if(strlen($subject)<1){$cansend = false; echo "<script>alert('Вы не ввели тему сообщения'); window.history.back(-1);</script>";}
			if(strlen($text)<1){$cansend = false; echo "<script>alert('Вы не ввели текст сообщения'); window.history.back(-1);</script>";}
			
			if($cansend)
			{
				$mysql->sql_query("INSERT INTO messages VALUES (0,'$touser','$id_user','$subject','$text','$date',0)");
				echo "<script>alert('Ваше сообщение отправлено'); location.href='friends.htm';</script>";
			}
		}
		
		//Удаление из списка друзей
		if(isset($_POST["delfriend"]))
		{
			$delfriend = safform($_POST["delfriend"]);
			//Проверка на существование дружбы
			$checkfriendship = $mysql->sql_query("SELECT * FROM friends WHERE (user1='$id_user' OR user2='$id_user') AND id='$delfriend' ");
			if(mysql_num_rows($checkfriendship)<1)
			{
				echo "У вас не существует этой дружбы";
			}
			else
			{
				$frienddata = mysql_fetch_assoc($checkfriendship);
				if($frienddata["user1"]==$id_user){$friendid = $frienddata["user2"];}else{$friendid = $frienddata["user1"];}
				//Отправить сообщение второму пользователю
				if($frienddata["status"]==1)
				{
					$date = time();
					$mysql->sql_query("INSERT INTO messages VALUES (0,'$friendid','$id_user','Удаление из списка друзей','Пользователь $login удалил вас из списка друзей','$date',0)");
				}
				//Удаляем из БД
				$mysql->sql_query("DELETE FROM friends WHERE id='$delfriend'");
			}
		}
		
		//Реакция на предложение дружбы
		if(isset($_POST["actionwithfr"]) && isset($_POST["friendshipid"]))
		{	
			//Проверка существования приглашения
			$friendshipid = safform($_POST["friendshipid"]);
			$checkfriendship = $mysql->sql_query("SELECT * FROM friends WHERE (user1='$id_user' OR user2='$id_user') AND id='$friendshipid' ");
			if(mysql_num_rows($checkfriendship)!==1)
			{
				echo "У вас не существует этой дружбы";
			}
			else
			{
				$frienddata = mysql_fetch_assoc($checkfriendship);
				if($frienddata["user1"]==$id_user){$friendid = $frienddata["user2"];}else{$friendid = $frienddata["user1"];}
				$date = time();
				
				if($_POST["actionwithfr"]=="Принять")
				{
					$mysql->sql_query("UPDATE friends SET status=1 WHERE id='$friendshipid'");
					$mysql->sql_query("INSERT INTO messages VALUES (0,'$friendid','$id_user','Новый друг','Пользователь $login успешно добавлен в список друзей','$date',0)");
				}
				elseif($_POST["actionwithfr"]=="Отказать")
				{
					$mysql->sql_query("DELETE FROM friends WHERE id='$friendshipid'");
					$mysql->sql_query("INSERT INTO messages VALUES (0,'$friendid','$id_user','Отказ в дружбе','Пользователь $login отказался добавить вас в список друзей','$date',0)");
				}
			}
		}
		
		//Вывод списка друзей
		$page = "friends";
		$query = "SELECT * FROM friends WHERE (user1='$id_user' OR user2='$id_user') ORDER BY id DESC";
		$p_col = 20;
		$query  = link_pages($p_col,$query,$page);
		
		$get_friends = $mysql->sql_query($query);
		$count = mysql_num_rows($get_friends);
		if($count<1)
		{
			echo "Вас еще никто не добавил в друзья<br/>";
		}
		else
		{
			while($frdata = mysql_fetch_assoc($get_friends))
			{
				if($frdata["user1"]==$id_user){$friendid = $frdata["user2"];}else{$friendid = $frdata["user1"];}
				$getuserinfo = $mysql->sql_query("SELECT * FROM users WHERE id_user='$friendid'");
				while($udata = mysql_fetch_assoc($getuserinfo))
				{
					?>
						<table class="friendtable" border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td valign="top" width="75">
									<?php
									if(strlen($udata["image"])>1)
									{
										?><img src="picture/users/<?=$udata["image"] ?>" width="75" border="0" alt="Avatar"/><?php
									}
									else
									{
										?><img src="picture/users/default.png" width="75" border="0" alt="Avatar"/><?php
									}
									?>
								</td>
								<td valign="top" align="left">
									<b><a href="profile_<?=$udata["id_user"]?>.htm"><?=$udata["login"]?></a></b> 
									<?php 
										if($frdata["status"]==0 && $frdata["user2"]==$id_user)
										{
											echo "<i>хочет добавить вас в друзья</i>";
										}
										elseif($frdata["status"]==0 && $frdata["user2"]!==$id_user)
										{
											echo "<i>Вы отправили предложение дружбы.</i>";
										}
									?>
									<br/>
									Имя: <b><?php if(strlen($udata["name"])>0){echo $udata["name"];}else{echo "не указано";}?></b>, Фамилия: <b><?php if(strlen($udata["sname"])>0){echo $udata["sname"];}else{echo "не указана";}?></b>
									<br/>
									<?php
									$threeminleft = time() - (3*60);
									if($udata["active"]>$threeminleft){echo "<span style=\"color:green\">Онлайн</span>";}
									else
									{echo "<span style=\"color:red\">Не в сети</span>";}
									?>
									<br/><span style="width:70px; padding:5px; font-size:12px;" class="msgbtn" OnClick="showan(); document.getElementById('touser').innerHTML='<?=$udata["login"] ?>'; document.msgform.touser.value='<?=$udata["id_user"] ?>';">Сообщение</span>
								</td>
								<td valign="top" align="right">
									<?php 
										if($frdata["status"]==0 && $frdata["user2"]==$id_user)
										{
											?>
											<form method="post">
											<input class="msgbtn" name="actionwithfr" style="margin-bottom:3px; width:65px; font-size:13px; padding:3px; border:none;" type="submit" value="Принять"/>
											<input class="msgbtn" name="actionwithfr" style="width:65px; font-size:13px; padding:3px; border:none;" type="submit" value="Отказать"/>
											<input type="hidden" name="friendshipid" value="<?=$frdata["id"]?>" />
											</form>
											<?php
										}
										elseif($frdata["status"]==1)
										{
											?>
											<form method="post"><input class="msgbtn" style="font-size:12px; padding:2px; border:none;" type="submit" value="Удалить из друзей"/><input type="hidden" name="delfriend" value="<?=$frdata["id"]?>" /></form>
											<?php 
										}
										elseif($frdata["status"]==0 && $frdata["user2"]!==$id_user)
										{
											?>
											<form method="post"><input class="msgbtn" style="font-size:12px; padding:2px; border:none;" type="submit" value="Отменить предложение"/><input type="hidden" name="delfriend" value="<?=$frdata["id"]?>" /></form>
											<?php 
										}
									?>
								</td>
							</tr>
						</table><br/>
					<?php
				}
			}
		}
		?>
		<div style="text-align:center; width:100%;"><a href="profile.htm">&laquo;&laquo; Назад на страницу профиля</a></div>
		<?php
	}
	
	
	
	//ЧУЖИЕ друзья
	if($_GET["profile"]=="friendsof" && isset($_GET["id"]))
	{
		$userslogin = mysql_result($mysql->sql_query("SELECT login FROM users WHERE id_user='".$_GET["id"]."'"),0);
		?>
		<div align="center" style="margin: 0px auto;"><h2>Друзья пользователя <?=$userslogin?></h2></div>
		<?php
		$page = "friends";
		$query = "SELECT * FROM friends WHERE (user1='".$_GET["id"]."' OR user2='".$_GET["id"]."') AND status=1 ORDER BY id DESC";
		$p_col = 20;
		$query  = link_pages($p_col,$query,$page);
		
		$get_friends = $mysql->sql_query($query);
		$count = mysql_num_rows($get_friends);
		if($count<1)
		{
			echo "У этого пользователя нет друзей<br/>";
		}
		else
		{
			while($frdata = mysql_fetch_assoc($get_friends))
			{
				if($frdata["user1"]==$_GET["id"]){$friendid = $frdata["user2"];}else{$friendid = $frdata["user1"];}
				$getuserinfo = $mysql->sql_query("SELECT * FROM users WHERE id_user='$friendid'");
				while($udata = mysql_fetch_assoc($getuserinfo))
				{
					?>
						<table class="friendtable" border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td valign="top" width="75">
									<?php
									if(strlen($udata["image"])>1)
									{
										?><img src="picture/users/<?=$udata["image"] ?>" width="75" border="0" alt="Avatar"/><?php
									}
									else
									{
										?><img src="picture/users/default.png" width="75" border="0" alt="Avatar"/><?php
									}
									?>
								</td>
								<td valign="top" align="left">
									<b><a href="profile_<?=$udata["id_user"]?>.htm"><?=$udata["login"]?></a></b><br/>
									Имя: <b><?php if(strlen($udata["name"])>0){echo $udata["name"];}else{echo "не указано";}?></b>, Фамилия: <b><?php if(strlen($udata["sname"])>0){echo $udata["sname"];}else{echo "не указана";}?></b>
									<br/>
									<?php
									$threeminleft = time() - (3*60);
									if($udata["active"]>$threeminleft){echo "<span style=\"color:green\">Онлайн</span>";}
									else
									{echo "<span style=\"color:red\">Не в сети</span>";}
									?>
								</td>
							</tr>
						</table><br/>
					<?php
				}
			}
		}
		?>
		<div style="text-align:center; width:100%;"><a href="profile_<?=$_GET["id"] ?>.htm">&laquo;&laquo; Назад на страницу профиля</a></div>
		<?php
	}
	
	//Поиск пользователей
	if($_GET["profile"]=="searchusers")
	{
		//Получаем самый большой ID пользователя
		$lastid = mysql_result($mysql->sql_query("SELECT id_user FROM users ORDER BY id_user DESC LIMIT 1"),0);
		?>
		<div align="center" style="margin: 0px auto;"><h2>Поиск пользователей</h2></div>
		<form method="post">
		<table class="searchuserstable" align="center" align="center" cellpadding="1" cellspacing="1">
			<tr>
				<td>
					ID
				</td>
				<td>
					<input type="text" name="searchid" value="<?=$_POST["searchid"]?>" size="5" /> <small class="hint">(1 - <?=$lastid?>)</small>
				</td>
				<td>
					Логин
				</td>
				<td>
					<input type="text" name="ulogin" value="<?=$_POST["ulogin"]?>" />
				</td>
			</tr>
			<tr>
				<td>
					Имя
				</td>
				<td>
					<input type="text" name="name" value="<?=$_POST["name"]?>" />
				</td>
				<td>
					Фамилия
				</td>
				<td>
					<input type="text" name="sname" value="<?=$_POST["sname"]?>" />
				</td>
			</tr>
			<tr>
				<td colspan="4">
					<input type="checkbox" name="onlineonly" value="Только онлайн" <?php if(isset($_POST["onlineonly"])){echo "checked";} ?> /> Только онлайн пользователи
				</td>
			</tr>
			<tr>
				<td colspan="4" align="center">
					<input type="submit" value="Поиск" />
				</td>
			</tr>
		</table>
		</form>
		<br/>
		<?php
		//Собсно сам поиск
		if(isset($_POST["searchid"]))
		{
			$searchid = safform($_POST["searchid"]);
			$ulogin = safform($_POST["ulogin"]);
			$name = safform($_POST["name"]);
			$sname = safform($_POST["sname"]);
			$threeminleft = time() - (3*60);
			$cansearch = true;
			
			if(strlen($searchid)<1 && strlen($ulogin)<1 && strlen($name)<1 && strlen($sname)<1)
			{
				$cansearch = false;
				echo "Задан пустой поисковый запрос";
			}
			
			if($cansearch)
			{
				//Если поиск по ID то можно сделать просто
				if(strlen($searchid)>0)
				{
					$query = "SELECT * FROM users WHERE id_user='$searchid'";
				}
				//Если поиск по другим параметрам
				else
				{
					$query = "SELECT * FROM users WHERE 1=1 ";
					if(strlen($ulogin)>0)$query.=" AND login='$ulogin' ";
					if(strlen($name)>0)$query.=" AND name='$name' ";
					if(strlen($sname)>0)$query.=" AND sname='$sname' ";
					if(isset($_POST["onlineonly"])){$query.=" AND active>$threeminleft ";}
				}
				$getsearch = $mysql->sql_query($query);
				while($udata = mysql_fetch_assoc($getsearch))
				{
					?>
						<table class="friendtable" border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td valign="top" width="75">
									<?php
									if(strlen($udata["image"])>1)
									{
										?><img src="picture/users/<?=$udata["image"] ?>" width="75" border="0" alt="Avatar"/><?php
									}
									else
									{
										?><img src="picture/users/default.png" width="75" border="0" alt="Avatar"/><?php
									}
									?>
								</td>
								<td valign="top" align="left">
									<b><a href="profile_<?=$udata["id_user"]?>.htm"><?=$udata["login"]?></a></b><br/>
									Имя: <b><?php if(strlen($udata["name"])>0){echo $udata["name"];}else{echo "не указано";}?></b>, Фамилия: <b><?php if(strlen($udata["sname"])>0){echo $udata["sname"];}else{echo "не указана";}?></b>
									<br/>
									<?php
									if($udata["active"]>$threeminleft){echo "<span style=\"color:green\">Онлайн</span>";}
									else
									{echo "<span style=\"color:red\">Не в сети</span>";}
									?>
								</td>
							</tr>
						</table><br/>
					<?php
				}
				if(mysql_num_rows($getsearch)<1){?><div style="text-align:center; width:100%;"><b>По вашему запросу ничего не найдено</b></div><?php }
				
			}
		}
		?>
		<div style="text-align:center; width:100%;"><a href="profile.htm">&laquo;&laquo; На страницу профиля</a></div>
		<?php
	}
}
elseif($_GET["profile"]!=="whoonline" && $_GET["profile"]!=="statgame")
{
	echo "Вы не авторизованы для этого действия";
}

//Вывод пользователей онлайн
if($_GET["profile"]=="whoonline")
{
	?>
	<div align="center" style="margin: 0px auto;"><h2>Кто онлайн</h2></div>
	<?php
	$onlinecount = 0;
	$page = "whoonline";
	$query = "SELECT * FROM users WHERE (active>=".(time()-180).") ORDER BY active DESC";
	$p_col = 20;
	$query  = link_pages($p_col,$query,$page);
	$get_online = $mysql->sql_query($query);
	if(mysql_num_rows($get_online)<1)
	{
		?><div align="center" style="margin: 0px auto;">Нет пользователей онлайн</div><?php
	}
	else
	{
		while($udata = mysql_fetch_assoc($get_online))
		{
			if($udata["id_user"]==$id_user){continue;}
			$onlinecount++;
			?>
				<table class="friendtable" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td valign="top" width="75">
							<?php
							if(strlen($udata["image"])>1)
							{
								?><img src="picture/users/<?=$udata["image"] ?>" width="75" border="0" alt="Avatar"/><?php
							}
							else
							{
								?><img src="picture/users/default.png" width="75" border="0" alt="Avatar"/><?php
							}
							?>
						</td>
						<td valign="top" align="left">
							<b><a href="profile_<?=$udata["id_user"]?>.htm"><?=$udata["login"]?></a></b><br/>
							Имя: <b><?php if(strlen($udata["name"])>0){echo $udata["name"];}else{echo "не указано";}?></b>, Фамилия: <b><?php if(strlen($udata["sname"])>0){echo $udata["sname"];}else{echo "не указана";}?></b>
							<br/>
							<?php
							$threeminleft = time() - (3*60);
							if($udata["active"]>$threeminleft){echo "<span style=\"color:green\">Онлайн</span>";}
							else
							{echo "<span style=\"color:red\">Не в сети</span>";}
							?>
						</td>
					</tr>
				</table><br/>
			<?php
		}
	}
	if($onlinecount==0){ ?><div align="center" style="margin: 0px auto;">Никого кроме вас нет онлайн</div><?php }
	else{ ?><div align="center" style="margin: 0px auto;">Вы не отображаетесь в этом списке</div><?php }
}


//Статистика игры
if($_GET["profile"]=="statgame")
{
	require_once("statistic_game.php");
}