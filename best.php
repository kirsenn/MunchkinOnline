<div align="center" style="margin: 0px auto;"><h2>Доска почета</h2></div>


<table width="100%" align="center" border="0">
	<!--<tr>
		<td align="center" width="50%">
			<span style="font-size:14px; font-weight:bold;">Самые опытные</span>
		</td>
		
		<td align="center" width="50%">
			<span style="font-size:14px; font-weight:bold;">Самые везучие</span>
		</td>
		
	</tr>-->
	<tr>
		<td>
			<table width="300px" align="center" border="1" style="border-collapse:collapse;">
				<tr>
					<td align="center">
						<b>Игрок</b>
					</td>
					<td>
						<b>Опыт</b>
					</td>
				</tr>
				<?php
				$link_exper = $mysql->sql_query("SELECT * FROM users ORDER BY exper DESC LIMIT 15");
				$cnt = 0;
				while($row_user = mysql_fetch_assoc($link_exper))
				{
					$cnt++;
					switch($cnt)
					{
						case 1 : $medal = "<img src=\"picture/medal_gold_2.png\" title=\"Золото\" alt=\"\" />"; break;
						case 2 : $medal = "<img src=\"picture/medal_silver_2.png\" title=\"Серебро\" alt=\"\" />"; break;
						case 3 : $medal = "<img src=\"picture/medal_bronze_2.png\" title=\"Бронза\" alt=\"\" />"; break;
						default : $medal = "";
					}
					echo "<tr><td align=\"center\">$medal <a href=\"#\" onClick=\"showuser('".$row_user["id_user"]."');\">".$row_user["login"]."(".$row_user["level"].")"."</a></td><td>".$row_user["exper"]."</td></tr>";
				}
				?>
			</table>
		</td>
		<?php
		/* 		
		?>
		<td>
			<table width="300px" align="center" border="1" style="border-collapse:collapse;">
				<tr>
					<td align="center">
						<b>Игрок</b>
					</td>
					<td>
						<b>Побед подряд</b>
					</td>
				</tr>
			<?php
				$link_vict = $mysql->sql_query("SELECT * FROM statistic_game WHERE winner!='0'  AND winner!='' ");
				while($row_st = mysql_fetch_assoc($link_vict))
				{
					$idgt = $row_st['id_gt'];
					$winner[$idgt] = $row_st['winner'];
				}
				$cnt = 0;
				$winners = array_count_values($winner);
				arsort ($winners);
				reset ($winners);
				array_splice($winners, 15);
				foreach($winners as $k => $v)
				{
					$cnt++;
					switch($cnt)
					{
						case 1 : $medal = "<img src=\"picture/medal_gold_2.png\" title=\"Золото\" alt=\"\" />"; break;
						case 2 : $medal = "<img src=\"picture/medal_silver_2.png\" title=\"Серебро\" alt=\"\" />"; break;
						case 3 : $medal = "<img src=\"picture/medal_bronze_2.png\" title=\"Бронза\" alt=\"\" />"; break;
						default : $medal = "";
					}
					echo "<tr><td align=\"center\">$medal ".$k."</td><td>".$v."</td></tr>";
				}
				?>
			</table>
		</td>
		<?php
		 */
		?>
	</tr>
</table>

