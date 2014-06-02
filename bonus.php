<?php 
session_start();
require_once("modules/mysql.php");
require_once("chat.php");

if ( (isset($_SESSION['id_user'])) && (isset($_SESSION['init'])) && (isset($_SESSION['id_gt'])) )
{
	//Открыть окно со шмотками игрока
	if ($_REQUEST['send_com']==0) 
	{
		if ($_REQUEST['id_user']==$_SESSION['id_user'])
		{
			$u_bonus = mysql_result($mysql->sql_query("SELECT u_bonus FROM users WHERE (id_user=".$_SESSION['id_user'].")"),0);
			?>
			<table>
				<tr>
					<td valign="top">
						<div id="act_items_label">Активные шмотки</div>
					</td>
					<td>
						<div id="u_bonus_count">Бонус: <b><?=$u_bonus ?></b></div>
					</td>
					<td>
						<div id="u_bonus_minus">&nbsp;</div>
					</td>
					<td>
						<div id="u_bonus_plus">&nbsp;</div>
					</td>
				</tr>
			</table>
			<table>
				<tr>
					<?php
					for ($i=50;$i<=69;$i++)
					{
						$result=$mysql->sql_query("SELECT * FROM carried_items JOIN cards ON (carried_items.id_card=cards.id_card) WHERE (id_user=".$_SESSION['id_user']." AND place_card=".$i.")"); 
						if($i==60) 
						{
							?>
							</tr>
							<tr><td colspan="6"><div id="inact_items_label">Неактивные  шмотки</div></td></tr>
							<tr>
							<?php
						}
						if (mysql_num_rows($result) < 1)
						{
							?><td><div class="activeitem" id="id_table<?=$i ?>"></div></td><?php
						}
						else
						{
							$row = mysql_fetch_array($result);
							?>
							<td>
								<div class="activeitem" id="id_table<?=$i ?>">
								<img width="130" height="190" id="id_card<?=$i ?>" class="id_card_bonus" src="./picture/<?=$row['pic'] ?>" value="<?=$i ?>">
								</div>
							</td>
							<?php
						}
					}
					?>
				</tr>
			</table>
			<?php
		}
		else
		{
			$u_bonus = mysql_result($mysql->sql_query("SELECT u_bonus FROM users WHERE (id_user=".$_REQUEST['id_user'].")"),0);
			?>
			<table>
				<tr>
					<td valign="top">
						<div id="act_items_label">Активные шмотки</div>
					</td>
					<td>
						<div id="u_bonus_count">Бонус: <b><?=$u_bonus ?></b></div>
					</td>
				</tr>
			</table>
			
			<table>
				<tr>
					<?php
					for ($i=50;$i<=59;$i++)
					{
						$result = $mysql->sql_query("SELECT * FROM carried_items JOIN cards ON (carried_items.id_card=cards.id_card) WHERE (id_user=".$_REQUEST['id_user']." AND place_card=".$i.")");
						if (mysql_num_rows($result) < 1)
						{
							?><td><div class="activeitem" id="id_table<?=$i ?>"></div></td><?php
						}
						else
						{
							$row = mysql_fetch_array($result);
							?>
							<td>
								<div class="activeitem" id="id_table<?=$i ?>">
								<img width="130" height="190" id="id_card<?=$i ?>" class="id_card_enemy" src="./picture/<?=$row['pic'] ?>" value="<?=$i ?>">
								</div>
							</td>
							<?php
						}
					}
					?>
				</tr>
			</table>

			<table>
				<tr>
					<td>
						<div id="inact_items_label">Неактивные  шмотки</div>
					</td>
				</tr>
			</table>
			
			<table>
				<tr>
					<?php
					for ($i=60;$i<=69;$i++)
					{
						$result = $mysql->sql_query("SELECT * FROM carried_items JOIN cards ON (carried_items.id_card=cards.id_card) WHERE (id_user=".$_REQUEST['id_user']." AND place_card=".$i.")");
						if (mysql_num_rows($result) < 1)
						{
							?><td><div class="inactiveitem" id="id_table<?=$i ?>"></div></td><?php
						}
						else
						{
							$row = mysql_fetch_array($result);
							?>
							<td>
							<div class="inactiveitem" id="id_table<?=$i ?>">
							<img width="130" height="190" id="id_card<?=$i ?>" class="id_card_enemy" src="./picture/<?=$row['pic'] ?>" value="<?=$i ?>">
							</div>
							</td>
							<?php
						}
					}

					?>
				</tr>
			</table>
			<?php
		}

		// Игрок перетащил шмотку из одной ячейки в другую
	}
	elseif($_REQUEST['send_com']==1)
	{
		if (($_REQUEST['from_object']>=50)&&($_REQUEST['from_object']<=75)) 
		{     
			if (($_REQUEST['to_place']>=50)&&($_REQUEST['to_place']<=75))
			{
				$result = $mysql->sql_query("SELECT * FROM carried_items JOIN cards ON (carried_items.id_card=cards.id_card) WHERE (id_user=".$_SESSION['id_user']." AND place_card=".$_REQUEST['to_place'].")");
				
				if (mysql_num_rows($result) < 1)
				{
					$mysql->sql_query("UPDATE carried_items SET place_card=".$_REQUEST['to_place']." WHERE (id_user=".$_SESSION['id_user']." AND place_card=".$_REQUEST['from_object'].")");
				}
				else
				{   
					$row = mysql_fetch_array($result); 
					$mysql->sql_query("UPDATE carried_items SET place_card=".$_REQUEST['to_place']." WHERE (id_user=".$_SESSION['id_user']." AND place_card=".$_REQUEST['from_object'].")");
					$mysql->sql_query("UPDATE carried_items SET place_card=".$_REQUEST['from_object']." WHERE (id_user=".$_SESSION['id_user']." AND place_card=".$row['place_card']." AND id_card=".$row['id_card'].")");
				}
			}    
		}
	}
	//Игрок уменьшил бонус от шмотки
	elseif ($_REQUEST['send_com']==2)
	{
		$result = $mysql->sql_query("SELECT * FROM users WHERE (id_user=".$_SESSION['id_user'].")");       
		$row = mysql_fetch_array($result);
		if ($row['u_bonus']>-20)
		{
			$u_bonus=$row['u_bonus']-1;
			$mysql->sql_query("UPDATE users SET u_bonus=".$u_bonus." WHERE (id_user=".$_SESSION['id_user'].")");
			$per_str=' изменил бонус с [B]'.($u_bonus+1).'[/B] на [B]'.$u_bonus.'[/B] ';
			add_str($per_str,0);
		}
		else
		{
			$u_bonus=-20; 
		}
		print "Бонус: <b>".$u_bonus."</b>";
	}
	//Игрок увеличил бонус от шмотки
	elseif($_REQUEST['send_com']==3)
	{
		$result = $mysql->sql_query("SELECT * FROM users WHERE (id_user=".$_SESSION['id_user'].")");
		$row = mysql_fetch_array($result);
		if ($row['u_bonus']<50)
		{
			$u_bonus=$row['u_bonus']+1;
			$mysql->sql_query("UPDATE users SET u_bonus=".$u_bonus." WHERE (id_user=".$_SESSION['id_user'].")");
			$per_str=' изменил бонус с [B]'.($u_bonus-1).'[/B] на [B]'.$u_bonus.'[/B] ';
			add_str($per_str,0);           
		}
		else
		{
			$u_bonus=50; 
		}
		print "Бонус: <b>".$u_bonus."</b>";
	}
}
?>  