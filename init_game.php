<?php 
require_once("global.php");
require_once("chat.php");

//*************************************************
function init_game(){
        if ( isset($_SESSION['id_user'])){
                if(!isset($_SESSION['init'])){
						
						if(isset($_POST["scrw"]) && is_numeric($_POST["scrw"]) && is_numeric($_POST["scrh"]))
						{
							$_SESSION["screen_w"] = $_POST["scrw"];
							$_SESSION["screen_h"] = $_POST["scrh"];
						}
						else
						{
							$_SESSION["screen_w"] = 1024;
							$_SESSION["screen_h"] = 768;
						}
						
                        $id_user=$_SESSION['id_user'];     
                     
                        $DBLink=connectdb();
                        
                        //Проверяем а вдруг до нас другой игрок инициализировал игру
                        $query='SELECT * FROM users JOIN game_tables ON (users.id_gt=game_tables.id_gt) WHERE (users.id_user='.$id_user.')';
                        $result=mysql_query($query);               
        
                        
                        if (mysql_num_rows($result)==0){
                            die("Ошибка 1 не могу инициализировать игровой стол");  
                        }                
                        
                        $row=mysql_fetch_array($result);
                        $num_user=$row['num_user'];  
                        //если игра уже кем то инициализирована значит просто к ней присоединяемся
						//если стоит 3 то к игре можно присоединиться если она уже началась
                        if (($row['gt_status']=='1') || ($row['gt_status']=='3')){
                              $_SESSION['id_gt']=$row['id_gt'];
                              $_SESSION['table_name']=$row['name'];
                              $_SESSION['init']="1";  
                                            
                        }else{
							  $gt_status=$row['gt_status'];	
                              $_SESSION['id_gt']=$row['id_gt'];
                              $_SESSION['table_name']=$row['name'];						
                              $_SESSION['init']="1";      
                               
                              //формируем колоду карт дверей
                              $query='SELECT * FROM cards WHERE card_type="door" ORDER BY RAND()';
                              $result=mysql_query($query);     
                              $int_card=1;  
                              while($row=mysql_fetch_array($result)){
                                $query='INSERT INTO cards_of_door VALUES(NULL,'.$int_card.','.$row['id_card'].','.$_SESSION['id_gt'].')';
                                mysql_query($query);
                                $int_card=$int_card+1; 
                              }
                              
                              //формируем колоду карт сокровищ
                              $query='SELECT * FROM cards WHERE card_type="loot" ORDER BY RAND()';
                              $result=mysql_query($query);
                              $int_card=1;  
                              while($row=mysql_fetch_array($result)){
                                $query='INSERT INTO cards_of_loot VALUES(NULL,'.$int_card.','.$row['id_card'].','.$_SESSION['id_gt'].')';
                                mysql_query($query);
                                $int_card=$int_card+1; 
                              }      
                              
                              //Вносим изменения в параметры игрового стола 
							  //0 - игра еще не началась, когда начнется присоединяться к игре нельзя
							  //1 - игра началась, присоединяться к игре нельзя
							  //2 - игра еще не началась, когда начнется присоединяться к игре можно
							  //3 - игра началась, присоединяться к игре можно
							  if ($gt_status==0){
								$allow_join=1;
							  }elseif ($gt_status==2){
								$allow_join=3;
							  }
							  
							  //А также определяем кто первый ходит
							  $query='SELECT * FROM users WHERE (id_gt='.$_SESSION['id_gt'].') ORDER BY RAND() LIMIT 1'; 										
                              $result=mysql_query($query);   
							  $row=mysql_fetch_array($result);
                              if (mysql_error($DBLink)!="") {
                                  die("Ошибка 1.5 не могу определить кто первый ходит");
                              }      
							  $first_step_user=$row['login'];
							  
							  $query='UPDATE game_tables SET gt_status='.$allow_join.', start_game="'.time().'", timestamp="'.time().'", active_user='.$row['id_user'].' WHERE (id_gt='.$_SESSION['id_gt'].')'; 										
                              $result=mysql_query($query);   
                              if (mysql_error($DBLink)!="") {
                                  die($query."Ошибка 2 не могу инициализировать игровой стол");
                              }       
                              
                              //Раздаем игрокам по 2 карты сокровищ и по 2 карты дверей
                              $query='SELECT * FROM users WHERE (id_gt='.$_SESSION['id_gt'].')';          
                              $result=mysql_query($query);
                              while($row=mysql_fetch_array($result)) {                       
                                    $query="SELECT * FROM cards_of_door JOIN cards ON (cards_of_door.id_card=cards.id_card) WHERE (id_gt=".$_SESSION['id_gt'].") LIMIT 2";
                                    $result1=mysql_query($query);
                                    $i=20;
                                    
                                    //Выдаем 2 карты дверей
                                    while ($row1=mysql_fetch_array($result1)) { 
                                          $query="DELETE FROM cards_of_door WHERE (id_gt=".$_SESSION['id_gt']." AND id_card=".$row1['id_card'].")";
                                          $result2=mysql_query($query);   
                                          
                                          $query="INSERT INTO cards_of_user VALUES(NULL,".$row['id_user'].",".$row1['id_card'].",".$i.")";
                                          $result2=mysql_query($query);
                                          $i=$i+1;                            
                                    } 
                                    
                                    //Выдаем 2 карты сокровищ
                                    $query="SELECT * FROM cards_of_loot JOIN cards ON (cards_of_loot.id_card=cards.id_card) WHERE (id_gt=".$_SESSION['id_gt'].") LIMIT 2";
                                    $result1=mysql_query($query);      
                                    while ($row1=mysql_fetch_array($result1)) { 
                                          $query="DELETE FROM cards_of_loot WHERE (id_gt=".$_SESSION['id_gt']." AND id_card=".$row1['id_card'].")";
                                          $result2=mysql_query($query);   
                                          
                                          $query="INSERT INTO cards_of_user VALUES(NULL,".$row['id_user'].",".$row1['id_card'].",".$i.")";
                                          $result2=mysql_query($query);
                                          $i=$i+1;                            
                                    }          
                                    
                                    //Проверяем инициализирована ли статистика ИГРЫ
                                    $query='INSERT INTO statistic_game (id_gt, name_gt, id_user, num_user, start_game, status_game) VALUES ('.$_SESSION['id_gt'].',"'.$_SESSION['table_name'].'",'.$row['id_user'].','.$num_user.','.time().',"game_init")';
                                    mysql_query($query);
                                    if (mysql_error($DBLink)!="") {
                                          die("Ошибка 2 не могу инициализировать статистику игр");
                                    }       
                                                                                                                           
                              }
                                                           
                              $per_str="Игра началась! Всем  автоматически роздано по 2 карты дверей и по 2 карты сокровищ. Мочите монстров, хапайте сокровища, подставляй друзей. Удачи всем!";
						      add_str($per_str,1); //Системное сообщение в игровой чат  
                              $per_str="[B] $first_step_user [/B] ходит первым";
							  add_str($per_str,1); //Системное сообщение в игровой чат 							  
                        }     
                                                                         
                        mysql_close ($DBLink);
                };
                echo "1";
        }else{
                echo "Зарегистрируйтесь, для того чтобы начать играть";  
        }
}

if (isset($_REQUEST['send_com_init'])){
	if($_REQUEST['send_com_init']==1)
	{
      if(session_id() == '') {
			session_start();
		}
      init_game();
	  }
}
?>