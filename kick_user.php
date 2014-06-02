<?php 
//Выбрасить игрока за-за стола
session_start();
require_once("global.php");
require_once("chat.php");
require_once("my_function.php");
?>

<?php
if ( (isset($_SESSION['id_user'])) && (isset($_SESSION['init'])) && (isset($_SESSION['id_gt'])) ){
        if ( ($_REQUEST['send_com_kick']==1) && (isset($_REQUEST['id_user'])) ) {
		      $id_user=$_REQUEST['id_user'];
			  $id_gt=$_SESSION['id_gt'];
			  
			  if  (is_numeric($id_user)){
					  $DBLink=connectdb(); 
					  //Узнаем сидит ли игрок за игровым столом, которого мы  хотим выпнуть
					  $query='SELECT * FROM users WHERE ((id_user='.$id_user.') AND (id_gt='.$id_gt.'))';
					  $result=mysql_query($query); 
					  if (mysql_num_rows($result)!=0){	
									$row=mysql_fetch_array($result); 
									$login=$row['login'];
									$query1="UPDATE users SET id_gt=0, u_bonus=0, u_level=1, u_gold=0   
											WHERE (id_user=".$id_user.")";
									$result1=mysql_query($query1);   
									if (mysql_error($DBlink)!="") {
									   die("Неудалось ввпнуть игрока из-за стола");
									}      
									
									//Изменяем количество игроков за игровым столом
									$query2='SELECT COUNT(*) FROM users WHERE ((id_gt='.$id_gt.' ))';
									$result2=mysql_query($query2);
									$row2 = mysql_fetch_array($result2);
									$num_user=$row2[0];
									
									$query="UPDATE game_tables SET num_user=".$num_user." 
											WHERE (id_gt=".$id_gt.")"; 

									$result=mysql_query($query);   
									if (mysql_error($DBlink)!="") {
									   die("Неудалось присоединиться к столу\n");
									}    
									
									//Делаем запрос сколько карт у игрока в руке
									$query='SELECT * FROM cards_of_user WHERE (id_user='.$id_user.')';
									$result=mysql_query($query);
									if (mysql_num_rows($result)!=0){
										//Узнаем номер последней карты в дискарде на данном игровом столе
										$query1="SELECT MAX(num_d) AS num_d
												FROM discards               
												WHERE (id_gt=".$id_gt.")";
										$result1=mysql_query($query1);  				
										$row1=mysql_fetch_array($result1);
										$num_dump=$row1['num_d']+1; 
										//Перебираем по очеред все карты игрока в руке
										while($row=mysql_fetch_array($result)){
											//Добавляем карту в дискард из руки
											$query2="INSERT INTO discards VALUES(NULL,".$num_dump.",".$row['id_card'].",".$id_gt.")";
											$result2=mysql_query($query2); 
											$num_dump=$num_dump+1;
										}	
										
									}			
									//Удаляем карты из рук 
									$query="DELETE FROM cards_of_user WHERE (id_user=".$id_user.")";         
									$result=mysql_query($query); 
									if (mysql_error($DBLink)!="") {
										  die("Ошибка 2 Неудалось удалить карты игрока\n");
									}


									//Делаем запрос сколько карт у игрока в шмотках
									$query='SELECT * FROM carried_items WHERE (id_user='.$id_user.')';
									$result=mysql_query($query);
									if (mysql_num_rows($result)!=0){
										//Перебираем по очеред все карты игрока в его шмотках
										while($row=mysql_fetch_array($result)){
											//Добавляем карту в дискард из шмоток
											$query2="INSERT INTO discards VALUES(NULL,".$num_dump.",".$row['id_card'].",".$id_gt.")";
											$result2=mysql_query($query2); 
											$num_dump=$num_dump+1;
										}	
										
									}				
									//Удаляем карты из шмоток
									$query="DELETE FROM carried_items WHERE (id_user=".$id_user.")";         
									$result=mysql_query($query);      
									if (mysql_error($DBLink)!="") {
										  die("Ошибка 3 Неудалось удалить карты игрока\n");
									}       

									//Получаем параметы игрового стола
									$query="SELECT * FROM game_tables WHERE (id_gt=".$_SESSION['id_gt'].")";
									$result=mysql_query($query); 
									$row=mysql_fetch_array($result);
									$creator=$row['creator'];//узнаем имя создателя стола
									$active_user=$row['active_user'];//узнаем кто в данный момент ходит							
									//Обновляем значение в таблице столов, пользователя который ходит,если до этого ходил пользователь которого выкинули 	
									if ($active_user==$id_user) {
										$query="UPDATE game_tables SET active_user=".$_SESSION['id_user']." 
											    WHERE (id_gt=".$_SESSION['id_gt'].")";
										$result=mysql_query($query);   
										if (mysql_error($DBLink)!="") {
											die("Неудалось передать ход другому пользователю");
										}      
									}	
									
									//Делаем запрос есть ли у игрока статистика
									$query='SELECT * FROM statistic_game WHERE ( (id_gt='.$id_gt.') AND (id_user='.$id_user.'))'; 
									$result=mysql_query($query);
									if (mysql_num_rows($result)!=0){
										//Удаляем статистику игрока
										$query='DELETE FROM statistic_game WHERE ( (id_gt='.$id_gt.') AND (id_user='.$id_user.'))';        
										$result=mysql_query($query);  									
										if (mysql_error($DBLink)!="") {
											  die("Ошибка 3 Неудалось удалить карты игрока\n");
										}   
										
										//Меняем количество игроков за столом в статистике
										$query='UPDATE statistic_game SET num_user='.$num_user.' WHERE (id_gt='.$id_gt.')';
										$result=mysql_query($query);
										if (mysql_error($DBLink)!="") {
											  die("Ошибка 1: Неудалось изменить статистику игры");
										} 	
												
									}										
									
									$per_str='ВНИМАНИЕ![B]'.$_SESSION['login'].'[/B] (создатель стола) , выгнал из-за игрового стола [B]['.$login.'][/B] ход передан [B]['.$_SESSION['login'].'][/B]';
									add_str($per_str,1);  //Системное сообщение   										
					 }		  
					  mysql_close ($DBLink);   
			  }		
        }    
}
?>  