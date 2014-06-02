<?php 
session_start();

require_once("../global.php");
require_once("../my_function.php");


function reload_user() {
		    $DBLink=connectdb(); 				  
		    //*****Проверка на условие окночания игры для игрока перебрасываем на другую страницу**** 	
		    if ( (isset($_SESSION['id_gt_spec'])) ){
			      $id_gt=$_SESSION['id_gt_spec'];	 
				  //Получаем параметы игрового стола
				  $query="SELECT * FROM game_tables WHERE (id_gt=".$id_gt.")";
				  $result=mysql_query($query); 
				  //Если игровой стол отсутствует то игра тоже заканчивается 
				  if (mysql_num_rows($result)==0) {
							//Отсылаем 1 если стол отсутствует, перебрасывает на страницу статистики и удаляет стол
							$json_data['flag_game']="1";
							echo json_encode($json_data);	
							return;
				  }		
				  $row=mysql_fetch_array($result);
				  $num_user=$row['num_user'];//Количество игроков за столом;
				  $active_user=$row['active_user'];//узнаем кто в данный момент ходит
				  $creator=$row['creator'];//узнаем кто создатель стола
				  		  				 
				  //Проверяем сколько игроков осталось за столом
				  if ($num_user<1){
						//Отсылаем 1 если игроков мало, перебрасывает на страницу статистики и удаляет стол
						$json_data['flag_game']="1";
						echo json_encode($json_data);	
						return;
				  }	 					  				  
			//Если пользователя выкинули(отсутствует у него $_SESSION['id_gt']) из-за стола то перебрасываем его на gamemenu		  
			}else{
			    //Отсылаем 2 если игрока выкинули из игры, перебрасывает на страницу gamemenu
				$json_data['flag_game']="2";
				echo json_encode($json_data);
				return;
			}
			//*****Проверка ОКОНЧЕНА**** 	
			//Все нормально продолжается игра, отправляем флаг что все нормально
			$json_data['flag_game']="0";	
			  
			
			//Снимаем все показаетли пользователей
            //YOU ARE
            $query="SELECT * FROM users WHERE (id_gt=".$_SESSION['id_gt_spec'].")";
            $result=mysql_query($query);   
            if (mysql_num_rows($result)>0){
                    $row=mysql_fetch_array($result);                    
                    
                    $expend_time=time()-$row['active'];
                    if ($expend_time>10){
						//Если игрок отошел от стола более чем на 10 секунд, то рамка его становится красной
						$json_data['active1']='1px solid red';						
                    }else{
						//Если игрок активен, то рамка его становится Зеленой
						$json_data['active1']='1px solid green';
						//Если ход игрока то рамка становится синей
						if ($active_user==$row['id_user']){
							$json_data['active1']='2px solid blue';
							$json_data['button_mess_boi']='visible';
						}else {
							$json_data['button_mess_boi']='hidden';
						}
                    }
					
                    $json_data['win_user1']='visible';//Отображение рамок игроков, если игрок есть то visible
					
                    $json_data['nick1']='<b>'.substr($row['login'],0,15).'</b>['.$row['sex'].']<small class="hint">(Это Вы)</small><br/>';
                    $json_data['level1']=' Уровень: <b>'.$row['u_level'].'</b>';
					$json_data['bonus1']=' Шмотки: <b>'.$row['u_bonus'].'</b><br>';
					
					//ПРоверяем сколько проклятий
					$query="SELECT * FROM carried_items JOIN cards ON (carried_items.id_card=cards.id_card)
							WHERE (id_user=".$row['id_user']." AND place_card>=70 AND place_card<=75)";
					$result1=mysql_query($query);   					
					$json_data['curse1']=' Прокл.: <b>'.mysql_num_rows($result1).'</b>';
					$json_data['u_gold1']=' Голды: <b>'.$row['u_gold'].'</b><br>';
					
                    //Раса    
                    $query="SELECT * FROM carried_items JOIN cards ON (carried_items.id_card=cards.id_card)
                           WHERE (id_user=".$row['id_user']." AND place_card IN (41,42)) ORDER BY place_card";
                    $result1=mysql_query($query);       
                    $str=' Раса: <b>'; 
                    if (mysql_num_rows($result1)!=0){
                        unset($str_race);         
                        while ($row1=mysql_fetch_array($result1)){
                            if (isset($str_race)){
                                $str_race=$str_race."+".$row1['c_name'];  
                            }else{
                                $str_race=$row1['c_name']; 
                            }  
                        }
                        $str=$str.$str_race;
                    }
                    $str=$str.'</b><br>'; 
                    
					$json_data['race1']=$str;
					 

                    //Класс    
                    $query="SELECT * FROM carried_items JOIN cards ON (carried_items.id_card=cards.id_card)
                            WHERE (id_user=".$row['id_user']." AND place_card IN (31,32)) ORDER BY place_card";
                    $result1=mysql_query($query);           
                    $str=' Класс: <b>'; 
                    if (mysql_num_rows($result1)!=0){
                        unset($str_class);          
                        while ($row1=mysql_fetch_array($result1)){
                            if (isset($str_class)){
                                $str_class=$str_class."+".$row1['c_name'];  
                            }else{
                                $str_class=$row1['c_name']; 
                            }  
                        }
                        $str=$str.$str_class;
                    }
                    $str=$str.'</b><br>'; 

					$json_data['u_class1']=$str;
					$json_data['next_step']="hidden";
					$json_data['close_table']="hidden";	
            }  
        
//********************************OTHER USERS****************************
              
			  $int=1;

              $query="SELECT * FROM users WHERE (id_gt=".$_SESSION['id_gt_spec'].") ORDER BY id_user";
              $result=mysql_query($query);   
              if (mysql_num_rows($result)==0){
	
              }else{
                while ($row=mysql_fetch_array($result)){
                      
                      $expend_time=time()-$row['active'];
                      if ($expend_time>10){
						  $json_data['active'.$int]='1px solid red';
                      }else{
					      $json_data['active'.$int]='1px solid green';
						  //Если ход игрока то рамка становится синей
						  if ($active_user==$row['id_user']){
								$json_data['active'.$int]='2px solid blue';
						  }
                      }
						//Делаем окно пользователя активным
					    $json_data['win_user'.$int]='visible';
						//Устанавливаем все параметры полльзователя
						$json_data['nick'.$int]='<b>'.substr($row['login'],0,15).'</b>['.$row['sex'].']';						
						$json_data['level'.$int]=' Уровень: <b>'.$row['u_level'].'</b>';
						$json_data['bonus'.$int]=' Шмотки: <b>'.$row['u_bonus'].'</b><br>';
						
						$json_data['pencil'.$int]=$row['login'];
						
						$json_data['kick'.$int]="0";
						$json_data['change_creator'.$int]="0";
						$json_data['change_creator_vis'.$int]="hidden";
						$json_data['kick_vis'.$int]="hidden";								

						
						//ПРоверяем сколько проклятий
						$query="SELECT * FROM carried_items JOIN cards ON (carried_items.id_card=cards.id_card)
								WHERE (id_user=".$row['id_user']." AND place_card>=70 AND place_card<=75)";
						$result1=mysql_query($query);   					
						$json_data['curse'.$int]=' Прокл.: <b>'.mysql_num_rows($result1).'</b>';	
						$json_data['u_gold'.$int]=' Голды: <b>'.$row['u_gold'].'</b><br>';
						
						//Раса 
						$query="SELECT * FROM carried_items JOIN cards ON (carried_items.id_card=cards.id_card)
							 WHERE (id_user=".$row['id_user']." AND place_card IN (41,42)) ORDER BY place_card";
						$result1=mysql_query($query);       
						$str=' Раса: <b>'; 
						if (mysql_num_rows($result1)!=0){
							unset($str_race);         
							while ($row1=mysql_fetch_array($result1)){
								if (isset($str_race)){
									$str_race=$str_race."+".$row1['c_name'];  
								}else{
									$str_race=$row1['c_name']; 
								}  
							}
							$str=$str.$str_race;
						}
						$str=$str.'</b><br>'; 
						  
						$json_data['race'.$int]=$str;
			
						//Класс    
						$query="SELECT * FROM carried_items JOIN cards ON (carried_items.id_card=cards.id_card)
								WHERE (id_user=".$row['id_user']." AND place_card IN (31,32)) ORDER BY place_card";
						$result1=mysql_query($query);           
						$str=' Класс: <b>'; 
						if (mysql_num_rows($result1)!=0){
							unset($str_class);          
							while ($row1=mysql_fetch_array($result1)){
								if (isset($str_class)){
									$str_class=$str_class."+".$row1['c_name'];  
								}else{
									$str_class=$row1['c_name']; 
								}  
							}
							$str=$str.$str_class;
						}
						$str=$str.'</b><br>'; 
						
						$json_data['u_class'.$int]=$str;  
							   
						$int=$int+1;
                }
              }   
			  //Оставшиеся окна для пользователей делаем невидемыми
			  while ($int<=6){
						$json_data['win_user'.$int]='hidden';
						$json_data['change_creator_vis'.$int]="hidden";
						$json_data['kick_vis'.$int]="hidden";							
						$int=$int+1;
			  }				  
              mysql_close ($DBLink); 
			  			 				
              echo json_encode($json_data);	
}   

reload_user();

?>