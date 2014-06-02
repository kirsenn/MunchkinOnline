<?php 
require_once("modules/mysql.php");
require_once("chat.php");

function show_table()
{
	//Разрешение экрана
	if(isset($_SESSION["screen_w"]) && isset($_SESSION["screen_h"]))
	{
		$screen_w = $_SESSION["screen_w"];
		$screen_h = $_SESSION["screen_h"];
	}
	else
	{
		$screen_w = 1000;
		$screen_h = 768;
	}
	
	//Ширина игрового стола
	if($screen_w < 1750)
	{
		$resolution = $screen_w - 250;
	}
	else
	{
		$resolution = 1500;
	}
	
	//Высота чата и размер шрифта в чате
	$resolutionchat = $screen_h - 650;
	if($resolutionchat>450){$resolutionchat = 450;}
	//$resolutionchat = $resolutionchat / 2;
	if($resolutionchat<150){$chatfontsize = "11px";}
	if($resolutionchat>=150 && $resolutionchat<250){$chatfontsize = "12px";}
	if($resolutionchat>=250){$chatfontsize = "13px";}
	
	$int = 1;
	$mysql = new MySQL;
	
	//Получаем параметы игрового стола
	$result = $mysql->sql_query("SELECT * FROM game_tables WHERE (id_gt=".$_SESSION['id_gt'].")");
	$row = mysql_fetch_array($result);
	$active_user = $row['active_user'];//узнаем кто в данный момент ходит
	$creator = $row['creator'];//узнаем создателя стола
	$gt_status = $row['gt_status'];
	
	//m&m
	$help_me = $row['help_me'];//Кто помогает игроку ходящему в данный момент
	
	
	$result_uhelper = $mysql->sql_query("SELECT * FROM users WHERE (id_user=".$help_me.")");
	$row_uhelper = mysql_fetch_array($result_uhelper);	
	$helper_name=$row_uhelper['login'];//Имя того кто помогает 
	//m&m

	//Если ход не текущего игрока то прячим кнопку конец хода
	if ($active_user !== $_SESSION['id_user']) $conhodavis = "visibility:hidden";
	else $conhodavis = "visibility:visible";
	
	if ($creator !== $_SESSION['login']) $next_step_vis = "visibility:hidden";
	else $next_step_vis = "visibility:visible";

	?>
	<table align="center" border="0" cellpadding="1" cellspacing="1">
		<tr>
			<td valign="top">
				<div class="idalltable" style="width:<?=$resolution ?>px;">
				<table>
					<tr>
						<td valign="top">
							<table>
								<tr>
									<td>
										<span title="Вернуться в игровое меню" id="button_menu" OnClick="location.href='gamemenu.php';"></span>
									</td>
									<td>
										<span title="Как здесь играть" id="button_help">?</span>
									</td>
								</tr>
							</table>
							<table>
								<tr>
									<td>
										<span title="Сброс" id="box_dump"></span>
									</td>
									<td>
										<span title="Очистить игровой стол" id="button_clear"></span>
									</td>
								</tr>
							</table>
							<table>
								<tr>
									<td>
										<div id="box_door" title="Карты дверей. Перетащите карту в ячейку справа">
											<img id="id_card1" width="105" class="card_batch" src="./picture/door.jpg" value="1">
										</div>
									</td>
								</tr>
								<tr>
									<td>
										<div id="box_loot" title="Карты сокровищ. Перетащите карту в ячейку справа">
											<img id="id_card2" width="105" class="card_batch" src="./picture/loot.jpg" value="2">
										</div>
									</td>
								</tr>
							</table>
							<table class="userbuttons">
								<tr>
									<td>
										<div title="Нажмите если вы не можете побить монстра или вам нечего выложить на стол" id="mess_pas">Я пас</div>
									</td>
								</tr>
								<tr>
									<td>
										<div title="Нажмите если вы собираетесь убить монстра" id="mess_boi">Я бью<br/>монстра</div>
									</td>
								</tr>
								<tr>
									<td>
										<div title="Нажмите чтобы бросить игровой кубик. В чате вы увидите число, которое вам выпало" id="mess_cube">Бросить<br/>кубик</div>
									</td>
								</tr>
								<tr>
									<td>
										<div title="Нажмите если вы завершили свой ход" id="mess_end" style="<?=$conhodavis ?>">Завершить<br/>ход</div>
									</td>
								</tr>
							</table>
						</td>
						<td>
							<table>	
								<tr>
									<?php
									for ($i=10;$i<=19;$i++)
									{
										$place = $i-9;
										?><td><div style="position:relative;"><div class="cardplacelabel1" >Игровой стол <br/> Место <?=$place ?> </div></div><?php
										$result = $mysql->sql_query("SELECT * FROM cards_of_table JOIN cards ON (cards_of_table.id_card=cards.id_card) WHERE (id_gt=".$_SESSION['id_gt']." AND place_card=".$i.")");
										if (mysql_num_rows($result)==0)
										{
											?><div title="Пустая ячейка. Перетащите сюда карты" id="id_table<?=$i ?>" class="cardplace"></div><?php
										}
										else
										{
											$row = mysql_fetch_array($result);
											?>
											<div title="Чтобы перетащить карту, удерживайте кнопку мыши на карте" id="id_table<?=$i ?>" class="cardplace" >
												<img id="id_card<?=$i ?>" width="130" height="190" class="id_card" src="./picture/<?=$row['pic'] ?>" value="<?=$i ?>">
											</div>
											<?php
										}
										?>
										</td>
										<?php
									}
									?>
								</tr>
								<tr>
								<?php
								
								for ($i=20;$i<=29;$i++)
								{
									$place = $i-19;
									?><td><div style="position:relative;"><div class="cardplacelabel2" >Рука <br/> Место <?=$place ?> </div></div><?php
									$result = $mysql->sql_query("SELECT * FROM cards_of_user JOIN cards ON (cards_of_user.id_card=cards.id_card) WHERE (id_user=".$_SESSION['id_user']." AND place_card=".$i.")");
									if (mysql_num_rows($result)==0)
									{
										?><div title="Пустая ячейка. Перетащите сюда карты" id="id_table<?=$i ?>" class="cardplace"></div><?php
									}
									else
									{
										$row = mysql_fetch_array($result);
										?>
										<div title="Чтобы перетащить карту, удерживайте кнопку мыши на карте" id="id_table<?=$i ?>" class="cardplace">
											<img id="id_card<?=$i ?>" width="130" height="190" class="id_card" src="./picture/<?=$row['pic'] ?>" value="<?=$i ?>">
										</div>
										<?php
									}
									?>
									</td>
									<?php
								}
								?>
								</tr>
							</table>
						</td>
					</tr>
				</table>
				</div>
			</td>
			<td width="200px;" valign="top" align="left" rowspan="2">
				<?php
				//Информация по cобственному игроку
				$result = $mysql->sql_query("SELECT * FROM users WHERE (id_user=".$_SESSION['id_user'].")");
				if(mysql_num_rows($result) > 0)
				{
					$row = mysql_fetch_array($result);
					//m&m
					$i_help=$row['i_help'];
					//m&m	
					//Если ход текущего игрока то рамку его окошка подъсвечиваем синим
					if ($active_user==$row['id_user'])
					{
						?><div id="player1" class="player" style="border:2px outset blue"><?php
					}
					else
					{
						?><div id="player1" class="player" style="border:2px solid green"><?php
					}   

					?>
					<div id="nick1" class="nick" title="<?=$row['login'] ?>" value="<?=$row['id_user'] ?>">
						<b><?php echo substr($row['login'],0,15); ?>(<?=$row['level'] ?>)</b>[<?=$row['sex'] ?>]<small class="hint">(Это Вы)</small><br/>
					</div>

					<span id="level1" class="level" value="<?=$row['id_user'] ?>">
						Уровень: <b><?=$row['u_level'] ?></b>
					</span>

					<span id="bonus1" class="bonus" value="<?=$row['id_user'] ?>">     
						Шмотки: <b><?=$row['u_bonus'] ?></b><br>
					</span>

					<span id="curse1" class="curse" value="<?=$row['id_user'] ?>">
						Прокл.: <b><?php echo mysql_num_rows($mysql->sql_query("SELECT * FROM carried_items JOIN cards ON (carried_items.id_card=cards.id_card)	WHERE (id_user=".$_SESSION['id_user']." AND place_card>=70 AND place_card<=75)")); ?></b>
					</span>

					<span id="u_gold1" class="u_gold" value="<?=$row['id_user'] ?>" >
						Голды: <b><?=$row['u_gold'] ?></b><br>
					</span>
					
					<span id="race1" class="race" value="<?=$row['id_user'] ?>">
						Раса: <b>
						<?php
						$result1 = $mysql->sql_query("SELECT * FROM carried_items JOIN cards ON (carried_items.id_card=cards.id_card) WHERE (id_user=".$_SESSION['id_user']." AND place_card IN (41,42)) ORDER BY place_card");
						if (mysql_num_rows($result1)!=0)
						{
							unset($str_race);
							while ($row1=mysql_fetch_array($result1))
							{
								if (isset($str_race))
								{
									$str_race=$str_race."+".$row1['c_name'];
								}
								else
								{
									$str_race=$row1['c_name']; 
								}  
							}
							print $str_race;
						}
						?>
						</b><br>
					</span>
					
					<span id="u_class1" class="u_class" value="<?=$row['id_user'] ?>">
						Класс: <b>
						<?php
						$result1 = $mysql->sql_query("SELECT * FROM carried_items JOIN cards ON (carried_items.id_card=cards.id_card)	WHERE (id_user=".$_SESSION['id_user']." AND place_card IN (31,32)) ORDER BY place_card");
						if (mysql_num_rows($result1)!=0)
						{
							unset($str_class);
							while ($row1=mysql_fetch_array($result1))
							{
								if (isset($str_class))
								{
									$str_class=$str_class."+".$row1['c_name'];  
								}
								else
								{
									$str_class=$row1['c_name']; 
								}  
							}
							print $str_class;
						}
						?>
						</b><br/>
					</span>
					
					<span id="helper_name1" class="helper_name" value="<?=$help_me?>" >
						Помощь: <b>
						<?php
							if ($active_user==$_SESSION['id_user'])
							{ 
								echo $helper_name;
							}
						?>
						</b>
					</span>
						
					</div>
					<?php
				}    

				//Информация по соперникам
				$result = $mysql->sql_query("SELECT * FROM users WHERE (id_user<>".$_SESSION['id_user']." AND id_gt=".$_SESSION['id_gt'].") ORDER BY id_user");
				if(mysql_num_rows($result) > 0)
				{
					$int=2;
					while ($row=mysql_fetch_array($result))
					{
						//Если ход текущего игрока то рамку его окошка подъсвечиваем синин, если он активен - зеленым, неактивен красным
						$expend_time=time()-$row['active'];
						
						$kick_vis = "";
						if ($creator !== $_SESSION['login'])
						{
							$kick_vis = "visibility:hidden";
						}
						
						if ($expend_time>10)
						{
							//Если игрок отошел от стола более чем на 10 секунд, то рамка его становится красной
							?><div id="player<?=$int ?>" class="player" style="border:1px solid red"><?php
						}
						else
						{
							if ($active_user==$row['id_user'])
							{
								?><div id="player<?=$int ?>" class="player" style="border:2px outset blue"><?php
							}
							else
							{
								?><div id="player<?=$int ?>" class="player" style="border:1px solid green"><?php
							}  
						}
						?>
						<table border="0">
							<tr>
								<td colspan="5">
									<span title="<?=$row['login'] ?>" id="nick<?=$int ?>" class="nick" value="<?=$row['id_user'] ?>" >
										<b><?php echo substr($row['login'],0,20); ?>(<?=$row['level'] ?>)</b>[<?=$row['sex'] ?>]
									</span>
								</td>
							</tr>
							<tr>
								<td>
									<span title="Написать игроку сообщение в чате" id="pencil<?=$int ?>" class="pencil" value="<?=$row['login'] ?>">
										<img src="./picture/pencil.png">
									</span>
								</td>
								<td>
									<span title="Информация о персонаже" class="infouser" onClick="showuser('<?=$row['id_user'] ?>');">
										<img src="./picture/information_user.png">
									</span>
								</td>
								<td>
									<?
									//Проверка на показывания значка Предложить/Принять помощь
									$help_vis = "visibility:hidden";
									if ( ($active_user==$_SESSION['id_user']) )
									{//Если ваш ход
										if ( ($row['i_help']==$_SESSION['id_user']) && ($help_me==0) )
										{//Если игрок вам предложил помощь и вы не у кого помощь еще не приняли
											$help_vis = "visibility:visible";
										}
									}else
									{//Если ход другого игрок
										if ( ($row['id_user']==$active_user) && ($help_me==0) &&  ($i_help==0) )
										{
											$help_vis = "visibility:visible";
										}
									}
									?>
									<span id="help_user_fight<?=$int ?>" class="help_user_fight" value="<?=$row['id_user'] ?>" style="<?=$help_vis ?>" title="Предложить/Принять помощь">
										<img src="./picture/shield_blue.png">
									</span>
									<?//m&m?>
								</td>
								<td>
									<span title="Выбросить игрока из-за стола" id="kick<?=$int ?>" class="kick" value="<?=$row['id_user'] ?>" style="<?=$kick_vis ?>" >
										<img src="./picture/cross.png">
									</span>
								</td>
								<td>
									<span title="Передать права управления столом игроку" id="change_creator<?=$int ?>" class="change_creator" value="<?=$row['id_user'] ?>" style="<?=$kick_vis ?>" >
										<img src="./picture/star.png">
									</span>
								</td>
							</tr>
						</table>

						<span id="level<?=$int ?>" value="<?=$row['id_user'] ?>">
							Уровень: <b><?=$row['u_level'] ?></b>
						</span>

						<span id="bonus<?=$int ?>" class="bonus" value="<?=$row['id_user'] ?>">
							Шмотки: <b><?=$row['u_bonus'] ?></b><br/>
						</span>

						<span id="curse<?=$int ?>" class="curse" value="<?=$row['id_user'] ?>">
							Прокл.: <b><?php echo mysql_num_rows($mysql->sql_query("SELECT * FROM carried_items JOIN cards ON (carried_items.id_card=cards.id_card) WHERE (id_user=".$row['id_user']." AND place_card>=70 AND place_card<=75)")); ?></b>
						</span>

						<span id="u_gold<?=$int ?>" value="<?=$row['id_user'] ?>">
							Голды: <b><?=$row['u_gold'] ?></b><br/>
						</span>
						<?php

						//Раса
						$result1 = $mysql->sql_query("SELECT * FROM carried_items JOIN cards ON (carried_items.id_card=cards.id_card)	WHERE (id_user=".$row['id_user']." AND place_card IN (41,42)) ORDER BY place_card");
						?>
						<span id="race<?=$int ?>" class="race" value="<?=$row['id_user'] ?>">
							Раса: <b>
							<?php
							if (mysql_num_rows($result1) > 0)
							{
								unset($str_race);         
								while ($row1 = mysql_fetch_array($result1))
								{
									if (isset($str_race))
									{
										$str_race=$str_race."+".$row1['c_name'];  
									}
									else
									{
										$str_race=$row1['c_name']; 
									}  
								}
								print $str_race;
							}
							?>
							</b><br>
						</span>
						<?php

						//Класс    
						$result1 = $mysql->sql_query("SELECT * FROM carried_items JOIN cards ON (carried_items.id_card=cards.id_card)	WHERE (id_user=".$row['id_user']." AND place_card IN (31,32)) ORDER BY place_card");
			  
						?>
						<span id="u_class<?=$int ?>" class="u_class" value="<?=$row['id_user'] ?>">
							Класс: <b>
							<?php
							if (mysql_num_rows($result1)!=0)
							{
								unset($str_class);          
								while ($row1=mysql_fetch_array($result1))
								{
									if (isset($str_class))
									{
									
									$str_class=$str_class."+".$row1['c_name'];  
									}
									else
									{
										$str_class=$row1['c_name']; 
									}  
								}
								print $str_class;
							}
							?>
							</b><br>
						</span>
						
						<span id="helper_name<?=$int ?>" class="helper_name" value="<?=$help_me ?>">
							Помощь: <b>
							<?php
							if ( ($active_user==$row['id_user']) )
							{ 
								echo $helper_name;
							}
							?>
							</b>
						</span>
						</div>
						
						<?php
						$int++;
					}
				}
				
				//Рисуем пустые области для пользователей которых нет но они могут присоединиться к игре  
				while ($int<=6)
				{
					?>
					<div id="player<?=$int ?>" class="player" style="visibility:hidden;">
					<table>
						<tr>
							<td>
								<span id="nick<?=$int ?>" class="nick" value="0">
								</span>
							</td>
							<td>
								<span id="pencil<?=$int ?>" class="pencil" value="0">
									<img src="./picture/pencil.png">
								</span>
							</td>
							<td>
								<span title="Информация о персонаже" class="infouser" onClick="showuser('<?=$row['id_user'] ?>');">
									<img src="./picture/information_user.png">
								</span>
							</td>
							<?php
							if ($creator==$_SESSION['login'])
							{
								?>
								<td>
									<span id="help_user_fight<?=$int ?>" class="help_user_fight" value="0" title="Предложить/Принять помощь">
										<img src="./picture/shield_blue.png">
									</span>
								</td>
								<td>
									<span id="kick<?=$int ?>" class="kick" value="0">
									<img src="./picture/cross.png">
									</span>
								</td>
								<td>
									<span id="change_creator<?=$int ?>" class="change_creator" value="0">
									<img src="./picture/star.png">
									</span>
								</td>
								<?php
							}
							?>
						</tr>
					</table>
					<span id="level<?=$int ?>" class="level" value="0">
					</span>

					<span id="bonus<?=$int ?>" class="bonus" value="0">
					</span>

					<span id="curse<?=$int ?>" class="curse" value="0">
					</span>

					<span id="u_gold<?=$int ?>" class="u_gold" value="0">
					</span>

					<span id="race<?=$int ?>" class="race" value="0">
					</span>

					<span id="u_class<?=$int ?>" class="u_class" value="0">
					</span>

					</div>
					<?php
					$int++;
				}
				?>
			</td>
		</tr>
		<tr>
			<td>
				<table width="100%" border="0">
					<tr>
						<td>
							<input title="Введите сообщение" id="mess_text" type="text" maxlength="200">
						</td>
						<td align="right" width="90px">
							<div title="Отправить сообщение в чат" id="mess_com">Отправить</div>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<div style="width: <?php echo $resolution - 100; ?>px; height:<?=$resolutionchat ?>px; font-size:<?=$chatfontsize ?>; resize:vertical;" id="mess_place"></div>
						</td>
						<td valign="top">
							<div title="История сообщений" id="mess_history"></div>
							<div id="next_step" class="next_step" style="<?=$next_step_vis ?>">
								Следующий<br/>ход
							</div>
							<div id="close_table" class="close_table" style="<?=$next_step_vis ?>">
								<?php
								if ($gt_status==1) echo "<div class=\"close_table_op\">Открыть<br/>стол</div>";
								if ($gt_status==3) echo "<div class=\"close_table_cl\">Закрыть<br/>стол</div>";
								?>
							</div>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	<table>
	
	<div id="window_card" style="background: #FFCC86;">
	</div>
	
	<div id="other_window" style="background: #FFCC86;">
	</div>
	
	<div id="vote_window">
	</div>
	
	<div id="window_message">
	</div>
	<?php
}         
?>  
