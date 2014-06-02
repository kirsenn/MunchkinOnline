<?php
require_once("modules/functions.php");
require_once("global.php");
session_start();

if (isset($_SESSION['id_user'])) {
  if ((isset($_REQUEST['table_name'])) && (isset($_REQUEST['num_user'])) && (isset($_REQUEST['num_time']))   ) {
			 $id_user = safform($_SESSION['id_user']);
			 $login = safform($_SESSION['login']);
			 $table_name = safform($_REQUEST['table_name']);
			 $num_user = safform($_REQUEST['num_user']);
			 $num_time = safform($_REQUEST['num_time']);
			 
			 if (!isset($_POST['allow_join'])){
				$allow_join=0;
			 }else{
				$allow_join=2;
			 }
			 
	
			 if ($table_name == '') {
				die("Поле 'Имя стола:' не заполнено<br />\n");
			 }
			 
			 //Проверяемкорректность пришедших данных - количество пользователей
			 switch($num_user) 
			 {
			   case 3: 
				  $num_user=3;
			   break;
			   case 4: 
				  $num_user=4;
			   break;
			   case 5: 
				  $num_user=5;
			   break;
			   default:		 
				  $num_user=6;
			 }
			 
			 //Проверяемкорректность пришедших данных - количество пользователей
			 switch($num_time) 
			 {
			   case 180: 
				  $num_time=180;
			   break;
			   case 300: 
				  $num_time=300;
			   break;
			   default:		 
				  $num_time=600;
			 }
	
			 $DBLink=connectdb(); 
			 if (!$DBLink) {
				die("Не могу соединиться с базой данных");
			 }else {
				$query='SELECT * FROM game_tables WHERE (creator="'.$login.'")';
				$result=mysql_query($query);  
				if (mysql_num_rows($result)!=0) {
					 die("Вы уже создали один ирговой стол, более одного стола запрещается создавать");
				}
				
				$query='SELECT * FROM game_tables WHERE (name="'.$table_name.'")';
				$result=mysql_query($query);  
				if (mysql_num_rows($result)!=0) {
					 die("<a href=\"gamemenu.php\">назад</a><br/> Стол с таким именем уже существует");
				}
			
				$query='INSERT INTO game_tables (name, creator, gt_status, timestamp, start_game, num_user, limit_user) VALUES("'.$table_name.'","'.$login.'",'.$allow_join.','.time().','.(time()+$num_time).',1,'.$num_user.')';
				$result=mysql_query($query);
				if (mysql_error($DBLink)!="") {
				   die("Неудалось создать игровой стол!Вернитесь назад повторите попытку\n");
				}
				
				$query2='SELECT id_gt FROM game_tables WHERE ((creator="'.$login.'" ))';
				$result2=mysql_query($query2);
				$row2 = mysql_fetch_array($result2); 
				$id_gt=$row2[0];
				
				$query1="UPDATE users SET id_gt=".$id_gt." 
						 WHERE (id_user=".$id_user.")";
				$result1=mysql_query($query1);   
				if (mysql_error($DBLink)!="") {
				   die("Неудалось создать стол\n");
				}      
				
				$query='SELECT COUNT(*) FROM users WHERE ((id_gt='.$id_gt.' ))';
				$result=mysql_query($query);
				$row = mysql_fetch_array($result);
				
				$query="UPDATE game_tables SET num_user=".$row[0]." 
						WHERE (id_gt=".$id_gt.")"; 
				$result=mysql_query($query);   
				if (mysql_error($DBLink)!="") {
				   die("Неудалось присоединиться к столу\n");
				}          
		
				mysql_close($DBLink);
				
				if (isset($_SERVER['HTTP_REFERER'])) {
				   header ("location: ".$_SERVER['HTTP_REFERER']);
				}else {
				   header ("location: gamemenu.php");
				}          
		   }
   }
}else{

}
?>