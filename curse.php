<?php 
session_start();
require_once("modules/mysql.php");
require_once("chat.php");

if ( (isset($_SESSION['id_user'])) && (isset($_SESSION['init'])) && (isset($_SESSION['id_gt'])))
{
	if ($_REQUEST['send_com']==0)
	{
		?>
		<div id="curselabel">Проклятия наложенные на игрока</div>
		<?php
		if ($_REQUEST['id_user']==$_SESSION['id_user'])
		{
			?>
			<table>
				<tr>
					<?php
					for ($i=70;$i<=75;$i++)
					{
						
						$result = $mysql->sql_query("SELECT * FROM carried_items JOIN cards ON (carried_items.id_card=cards.id_card) WHERE (id_user=".$_SESSION['id_user']." AND place_card=".$i.")");
						if (mysql_num_rows($result) < 1)
						{
							?><td><div class="curseplace" id="id_table<?=$i ?>"></div></td><?php
						}
						else
						{
							$row = mysql_fetch_array($result);
							?>
							<td>
							<div class="curseplace" id="id_table<?=$i ?>">
							<img id="id_card<?=$i ?>" class="id_card_bonus" src="./picture/<?=$row['pic'] ?>" value="<?=$i ?>">
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
			?>
			<table>
				<tr>
				<?php
				for ($i=70;$i<=75;$i++)
				{
					$result = $mysql->sql_query("SELECT * FROM carried_items JOIN cards ON (carried_items.id_card=cards.id_card) WHERE (id_user=".$_REQUEST['id_user']." AND place_card=".$i.")");
					if (mysql_num_rows($result) < 1)
					{
						?><td><div class="curseplace" id="id_table<?=$i ?>"></div></td><?php
					}
					else
					{
						$row = mysql_fetch_array($result);
						?>
						<td>
						<div class="curseplace" id="id_table<?=$i ?>">
						<img id="id_card<?=$i ?>" class="id_card_enemy" src="./picture/<?=$row['pic'] ?>" value="<?=$i ?>">';
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
	}
}
?>  