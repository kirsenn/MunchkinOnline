<?php 
require_once("global.php");
require_once("chat.php");
?>
<?php
function show_table()
{
	  //Окно для информации по картам
	  print '<div id="window_card" style="background: #FFCC86;">';
	  print '</div>';  
	  //Осталльные окна
	  print '<div id="other_window" style="background: #FFCC86;">';
	  print '</div>'; 

	  //Окно для сообщений
	  print '<div id="window_message">';
	  print '</div>'; 

	  print '<div id="mess_place"></div>';
	  
	  $DBLink=connectdb(); 
	   //Получаем параметы игрового стола
	  $query="SELECT * FROM game_tables WHERE (id_gt=".$_SESSION['id_gt_spec'].")";
	  $result=mysql_query($query); 
	  $row=mysql_fetch_array($result);
	  $active_user=$row['active_user'];//узнаем кто в данный момент ходит
	  $creator=$row['creator'];//узнаем создателя стола
	  $gt_status=$row['gt_status'];
	  
	  //Информация по соперникам
	  $query="SELECT * FROM users WHERE (id_gt=".$_SESSION['id_gt_spec'].") ORDER BY id_user";
	  $result=mysql_query($query);   
	  if (mysql_num_rows($result)==0){
		 
	  }
	  else{
		$int=1;
		while ($row=mysql_fetch_array($result)){
				//Если ход текущего игрока то рамку его окошка подъсвечиваем синин, если он активен - зеленым, неактивен красным
				 $expend_time=time()-$row['active'];
				if ($expend_time>10){
					//Если игрок отошел от стола более чем на 10 секунд, то рамка его становится красной
					print '<div id="player'.$int.'" class="player" style="border:1px solid red">'; 					
				}else{
					if ($active_user==$row['id_user']){
						print '<div id="player'.$int.'" class="player" style="border:2px solid blue">'; 
					}else{
						print '<div id="player'.$int.'" class="player" style="border:1px solid green">';
					}  
				}			
				print '<span id="nick'.$int.'" class="nick" value="'.$row['id_user'].'" >';
				print '<b>'.substr($row['login'],0,15).'</b>['.$row['sex'].']';
				print '</span>';
				
				print '<br/><span id="level'.$int.'" class="level" value="'.$row['id_user'].'">';
				print ' Уровень: <b>'.$row['u_level'].'</b>';
				print '</span>';
				 
				print '<span id="bonus'.$int.'" class="bonus" value="'.$row['id_user'].'">';              
				print ' Шмотки: <b>'.$row['u_bonus'].'</b><br>';
				print '</span>';

				//ПРоверяем сколько проклятий
				$query="SELECT * FROM carried_items JOIN cards ON (carried_items.id_card=cards.id_card)
						WHERE (id_user=".$row['id_user']." AND place_card>=70 AND place_card<=75)";
				$result1=mysql_query($query);    					
				print '<span id="curse'.$int.'" class="curse" value="'.$row['id_user'].'">';      
				print ' Прокл.: <b>'.mysql_num_rows($result1).'</b>';
				print '</span>';  			
	 
				//Количество Голдов
				print '<span id="u_gold'.$int.'" class="u_gold" value="'.$row['id_user'].'">';      
				print ' Голды: <b>'.$row['u_gold'].'</b><br>';
				print '</span>';   
				
				//Раса
				$query="SELECT * FROM carried_items JOIN cards ON (carried_items.id_card=cards.id_card)
						WHERE (id_user=".$row['id_user']." AND place_card IN (41,42)) ORDER BY place_card";
				$result1=mysql_query($query);     
				print '<span id="race'.$int.'" class="race" value="'.$row['id_user'].'">';        
				print ' Раса: <b>'; 
				if (mysql_num_rows($result1)!=0){
					unset($str_race);         
					while ($row1=mysql_fetch_array($result1)){
						if (isset($str_race)){
							$str_race=$str_race."+".$row1['c_name'];  
						}else{
							$str_race=$row1['c_name']; 
						}  
					}
					print $str_race;
				}
				print '</b><br>'; 
				print '</span>';
				
				//Класс    
				$query="SELECT * FROM carried_items JOIN cards ON (carried_items.id_card=cards.id_card)
						WHERE (id_user=".$row['id_user']." AND place_card IN (31,32)) ORDER BY place_card";
				$result1=mysql_query($query);     
				print '<span id="u_class'.$int.'" class="u_class" value="'.$row['id_user'].'">';       
				print ' Класс: <b>'; 
				if (mysql_num_rows($result1)!=0){
					unset($str_class);          
					while ($row1=mysql_fetch_array($result1)){
						if (isset($str_class)){
							$str_class=$str_class."+".$row1['c_name'];  
						}else{
							$str_class=$row1['c_name']; 
						}  
					}
					print $str_class;
				}
				print '</b><br>'; 
				print '</span>';         
			print '</div>';
			$int=$int+1;
		}
	  }
	//Рисуем пустые области для пользователей которых нет но они могут присоединиться к игре  
	while ($int<=6){
			print '<div id="player'.$int.'" class="player" style="visibility:hidden;">';
				print '<span id="nick'.$int.'" class="nick" value="0">';
				print '</span>';
				echo "<br/>";
	 
				print '<span id="level'.$int.'" class="level" value="0">';
				print '</span>';
							
				print '<span id="bonus'.$int.'" class="bonus" value="0">';              
				print '</span>';
				
				print '<span id="curse'.$int.'" class="curse" value="0">';      
				print '</span>';  	
				
				print '<span id="u_gold'.$int.'" class="u_gold" value="0">';
				print '</span>';			
				
				print '<span id="race'.$int.'" class="race" value="0">';  			
				print '</span>';	

				print '<span id="u_class'.$int.'" class="u_class" value="0">';  
				print '</span>';	
				
			print '</div>';
			$int=$int+1;
	}

		print '<div id="id_all_table1">';
		print '<div id="id_all_table2">';

 
	  for ($i=10;$i<=19;$i++) {
		  $query="SELECT * FROM cards_of_table JOIN cards ON (cards_of_table.id_card=cards.id_card) WHERE (id_gt=".$_SESSION['id_gt_spec']." AND place_card=".$i.")";
		  $result=mysql_query($query);
		  $place = $i-9;
		  if (mysql_num_rows($result)==0){
			print '<div id="id_table'.$i.'"></div>';
		  }
		  else{
			$row=mysql_fetch_array($result);
			print '<div id="id_table'.$i.'">';
			print '<img id="id_card'.$i.'" class="id_card" src="./picture/'.$row['pic'].'" value="'.$i.'">';
			print '</div>' ;
		  }
		?><div class="id_table<?=$i ?>" style="border:none; color:#777; z-index:1; height: 50px; text-align:center; font-size:18px; text-shadow: 0px 1px 0px #e5e5ee; box-shadow:none;" >Игровой стол <br/> Место <?=$place ?> </div><?php
	  }  

	 
	 print '</div>' ;
	 print '</div>' ;
}         
?>  
