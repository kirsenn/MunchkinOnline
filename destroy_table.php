<?php require_once("global.php"); ?>
<?php require_once("show_table_games.php"); ?>

<?php 
//Функция  удаления  стола, если part=0 то удаляем и статистику, если =1 то статитстика остается
function destroy_table($id_gt,$part){ 
         $id_user=$_SESSION['id_user'];
         $login=$_SESSION['login'];

         
         $DBLink=connectdb(); 
         if (!$DBLink) {
            die("Не могу соединиться с базой данных");
         }else {
            $query='SELECT * FROM game_tables WHERE (id_gt='.$id_gt.')';
            $result=mysql_query($query);
            if (mysql_num_rows($result)!=0) {
						//Удаляем игровой стол
						$query='DELETE FROM game_tables WHERE (id_gt='.$id_gt.')';         
						$result=mysql_query($query);  
						if (mysql_error($DBLink)!="") {
						   die("Ошибка 1 Неудалось удалить игровой стол\n");
						}
						
						//Удаляем чат
						$query='DELETE FROM gamechat WHERE (id_gt='.$id_gt.')';         
						$result=mysql_query($query);  
						if (mysql_error($DBLink)!="") {
						   die("Ошибка 1 Неудалось удалить игровой стол\n");
						}						

						//Отчищаем все справочники игроков,принадлежащие удаляемому столу , от карт 
						$query1='SELECT * FROM users WHERE (id_gt='.$id_gt.')';
						$result1=mysql_query($query1);
						while($row=mysql_fetch_array($result1)) {
							$query="DELETE FROM cards_of_user WHERE (id_user=".$row['id_user'].")";         
							$result=mysql_query($query); 
							if (mysql_error($DBLink)!="") {
								  die("Ошибка 2 Неудалось удалить карты игрока\n");
							}
							$query="DELETE FROM carried_items WHERE (id_user=".$row['id_user'].")";         
							$result=mysql_query($query);      
							if (mysql_error($DBLink)!="") {
								  die("Ошибка 3 Неудалось удалить карты игрока\n");
							}                                
						}
						
						//Приводим все пераметры игроков в начальное положение
						$query1="UPDATE users SET id_gt=0, u_bonus=0, u_level=1, u_gold=0, i_help=0
								 WHERE (id_gt=".$id_gt.")";
						$result1=mysql_query($query1);                 
						if (mysql_error($DBLink)!="") {
						   die("Ошибка 2 Неудалось удалить игровой стол\n");
						}                                
						
						//Отчищаем все справочники карт принадлежащие удаляемому столу
						$query="DELETE FROM cards_of_door WHERE (id_gt=".$id_gt.")";         
						$result=mysql_query($query);  
						if (mysql_error($DBLink)!="") {
						   die("Ошибка 3 Неудалось удалить игровой стол\n");
						}
						
						$query="DELETE FROM cards_of_loot WHERE (id_gt=".$id_gt.")";         
						$result=mysql_query($query);  
						if (mysql_error($DBLink)!="") {
						   die("Ошибка 4 Неудалось удалить игровой стол\n");
						}

						$query="DELETE FROM cards_of_table WHERE (id_gt=".$id_gt.")";         
						$result=mysql_query($query);  
						if (mysql_error($DBLink)!="") {
						   die("Ошибка 5 Неудалось удалить игровой стол\n");
						}

						$query="DELETE FROM discards WHERE (id_gt=".$id_gt.")";         
						$result=mysql_query($query);  
						if (mysql_error($DBLink)!="") {
						   die("Ошибка 6 Неудалось удалить игровой стол\n");
						}
						  
						if ($part==0)
						{//Удалили игровой стол	
							$query='UPDATE statistic_game SET status_game="delete_game_hole", end_game='.time().'  
									   WHERE (id_gt='.$id_gt.')';
							//$query="DELETE FROM statistic_game WHERE (id_gt=".$id_gt.")";         
							$result=mysql_query($query);  
							if (mysql_error($DBLink)!="") {
								 die("Ошибка 7 Неудалось удалить статистику игр\n");
							}
						}elseif ($part==1)
						{//Игра окончена имеется победитель
							  $query='UPDATE statistic_game SET status_game="end_game_victory", end_game='.time().'   
									   WHERE (id_gt='.$id_gt.')';
							  //$query="DELETE FROM statistic_game WHERE (id_gt=".$id_gt.")";         
							  $result=mysql_query($query);  
							  if (mysql_error($DBLink)!="") {
								 die("Ошибка 7 Неудалось удалить статистику игр\n");
							  }
					   }elseif ($part==2)
					   {//Игра удалена так как число пользователей за игровым столом слишком мало
							  $query='UPDATE statistic_game SET status_game="delete_game_user<1", end_game='.time().'   
									   WHERE (id_gt='.$id_gt.')';
							  //$query="DELETE FROM statistic_game WHERE (id_gt=".$id_gt.")";         
							  $result=mysql_query($query);  
							  if (mysql_error($DBLink)!="") {
								 die("Ошибка 7 Неудалось удалить статистику игр\n");
							  }
					   }elseif ($part==3)
					   {
							  $query='UPDATE statistic_game SET status_game="table_empty", end_game='.time().'   
									   WHERE (id_gt='.$id_gt.')';
							  //$query="DELETE FROM statistic_game WHERE (id_gt=".$id_gt.")";         
							  $result=mysql_query($query);  
							  if (mysql_error($DBLink)!="") {
								 die("Ошибка 7 Неудалось удалить статистику игр\n");
							  }
					   }elseif ($part==4)
					   {//Игра удалена так как она весит уже более 1 суток
							  $query='UPDATE statistic_game SET status_game="old_game", end_game='.time().'   
									   WHERE (id_gt='.$id_gt.')';
							  //$query="DELETE FROM statistic_game WHERE (id_gt=".$id_gt.")";         
							  $result=mysql_query($query);  
							  if (mysql_error($DBLink)!="") {
								 die("Ошибка 8 Неудалось удалить статистику игр\n");
							  }
					   }elseif ($part==5)
					   {//Игра закончена но не идет в зачет так как не выполнены условия
							  $query='UPDATE statistic_game SET status_game="incalculate", end_game='.time().'   
									   WHERE (id_gt='.$id_gt.')';
							  //$query="DELETE FROM statistic_game WHERE (id_gt=".$id_gt.")";         
							  $result=mysql_query($query);  
							  if (mysql_error($DBLink)!="") {
								 die("Ошибка 8 Неудалось удалить статистику игр\n");
							  }
					   }
            }
			if ($part!=4){
				unset($_SESSION['init']);
				unset($_SESSION['id_gt']);
				unset($_SESSION['table_name']); 
			}
            mysql_close($DBLink);
       }
}

if ( (isset($_REQUEST['send_com_destroy'])) && (isset($_REQUEST['id_gt'])) ){
    if(!isset($_SESSION)){session_start();}
    if (isset($_SESSION['id_user'])){  
        if ($_REQUEST['send_com_destroy']==0){           
            destroy_table($_REQUEST['id_gt'],0);    

			//ПРоверяем существуют ли игровые столы которые простаивают более 2 суток, если существуют тогда удаляем их
			$DBLink=connectdb(); 
			$query='SELECT * FROM game_tables WHERE (timestamp<"'.(time()-86400).'" )';
			$result=mysql_query($query);
			mysql_close($DBLink);

			while ($row = mysql_fetch_array($result)){
				destroy_table($row['id_gt'],4); 
			}	
						
        }
    }
}
?>