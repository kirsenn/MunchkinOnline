<?php 
session_start();
require_once("global.php");
require_once("show_table_games.php"); 
require_once("chat.php");
require_once("modules/mysql.php");

if ( (isset($_SESSION['id_user'])) && (isset($_REQUEST['id_gt'])) ) {
	 $id_user=$_SESSION['id_user'];
	 $id_gt=$_REQUEST['id_gt'];
	 
	 $DBlink=connectdb(); 
	 if (!$DBlink) {
		die("Не могу соединиться с базой данных");
	 }else {
		$query='SELECT * FROM users WHERE ((id_user='.$id_user.'))';
		$result=mysql_query($query);
		$row=mysql_fetch_array($result);
					
		if ($row['id_gt']==0) {
			 die("Вы не можете выйти из-за стола, вы за ним и не сидите");
		}

		$query1="UPDATE users SET id_gt=0, u_bonus=0, u_level=1, u_gold=0  
				WHERE (id_user=".$id_user.")";
		$result1=mysql_query($query1);   
		if (mysql_error($DBlink)!="") {
		   die("Неудалось выйти из-за стола столу\n");
		}      
		
		//Получаем параметы игрового стола
		$query="SELECT * FROM game_tables JOIN users ON (game_tables.creator=users.login) WHERE (game_tables.id_gt=".$id_gt.")";
		$result=mysql_query($query); 
		$row=mysql_fetch_array($result);
		$table_name=$row['name'];//узнаем имя стола	
		$creator=$row['id_user'];//узнаем имя создателя стола								
		
		//Изменяем количество игроков за игровым столом и передаем ход создателю стола
		$query2='SELECT COUNT(*) FROM users WHERE ((id_gt='.$id_gt.' ))';
		$result2=mysql_query($query2);
		$row2 = mysql_fetch_array($result2);
		$num_user=$row2[0];
		
		$query="UPDATE game_tables SET num_user=".$num_user.", active_user=".$creator."  
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
		//Делаем запрос есть ли у игрока статистика
		$query='SELECT * FROM statistic_game WHERE ( (id_gt='.$id_gt.') AND (id_user='.$id_user.'))';
		$result=mysql_query($query);
		if (mysql_num_rows($result)>0){
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

		$message='ВНИМАНИЕ!<B> '.$_SESSION['login'].' </B> ПОКИНУЛ ИГРУ, все его карты из рук и шмотки отправлены в сброс, а ход передан создателю стола';

		$login = $_SESSION["login"];
		$id_user = $_SESSION["id_user"];
		$time = time();
		$message = substr($message,0,320);
		$type=1;//Систсемное сообщение
		
		$mysql->sql_query("INSERT INTO gamechat VALUES ('$time', ".$id_gt.", '$id_user', '$login', '$message', $type )");
		
		//Сбрасываем все параметры игрока характерезуюшие его игровой стол
		if (isset($_SESSION['init'])){
			unset($_SESSION['init']);
		}
		if (isset($_SESSION['id_gt'])){
			unset($_SESSION['id_gt']);
		}
		if (isset($_SESSION['table_name'])){
			unset($_SESSION['table_name']);
		}
		
		if (isset($_SESSION['control_level'])){
			unset($_SESSION['control_level']);
		}
		
		mysql_close($DBlink);

   }
}else{

}
?>