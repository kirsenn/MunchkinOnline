<?php 
//Алгоритм назначения очередности хода игроков
session_start();
require_once("global.php");
require_once("chat.php");
require_once("my_function.php");
require_once("control_rule.php");
?>

<?php
if ( (isset($_SESSION['id_user'])) && (isset($_SESSION['init'])) && (isset($_SESSION['id_gt'])) ){
		//Если из файла game приходит команда 
		//1 -  при нажатии кон хода
		//2- при нажатии следущий ход, только создатель стола может так делать
        if (($_REQUEST['send_com_step']==1) || ($_REQUEST['send_com_step']==2)) {
            $DBLink=connectdb(); 
			//Обнуляем переменную продажи шмоток ХАФЛИНГА
			if (isset($_SESSION['hafling'])){
				$_SESSION['hafling']=0;
			}
			if (isset($_SESSION['control_level']))
			{
				unset($_SESSION['control_level']);
			}	

			 
            //Получаем параметы игрового стола
			$query="SELECT * FROM game_tables WHERE (id_gt=".$_SESSION['id_gt'].")";
            $result=mysql_query($query); 
			$row=mysql_fetch_array($result);
			$creator=$row['creator'];//узнаем имя создателя стола
			$active_user=$row['active_user'];//узнаем кто в данный момент ходит
			$num_user=$row['num_user'];//узнаем количество игроков за столом
			  
			$flag_control_rule=1; //Устанавливаем флаг контроля правил=1 т.е. все правила соблюдены
			if ($_SESSION['id_user']==$active_user){
				$flag_control_rule=control_hand_card($place_card);//Проверяем правила сколько карт в руке control_hand_card()- control_rule.php 	
				if ($flag_control_rule==1)
				{ 	
					$flag_control_rule=control_phase_move(1);//Проверяем правила все ли фазы хода выполнены control_phase_move()- control_rule.php 
				}
			}  
			  
			if ($flag_control_rule==1)
			{
				if ( ($_SESSION['id_user']==$active_user) || (($_SESSION['login']==$creator) && ($_REQUEST['send_com_step']==2)) ) 
				{		
					
					//Делаем запрос в котором расположены последовательно все игроки, для того что узнать кто ходит следующим
					$query="SELECT * FROM users WHERE (id_gt=".$_SESSION['id_gt'].") ORDER BY id_user";
					$result=mysql_query($query);	
					$last_id=0;$fl_first=0;
					while ($row=mysql_fetch_array($result))
					{	
							//Проверяем активен ли игрок 
							$expend_time=time()-$row['active'];				  
							if ( ($fl_first==0) && ($expend_time<10) ){
								$first_id=$row['id_user'];	
								$first_login=$row['login'];
								$fl_first=1;
							}			
							//Проверяем предыдущий игрок в таблице только что ходил?		
							if ($active_user==$last_id)
							{						   
								//Проверяем активен ли игрок рассматриваемый
								if ($expend_time<10)
								{	
									$current_id=$row['id_user'];
									if ($_REQUEST['send_com_step']==2){		
										$per_str='[B]['.$_SESSION['login'].'][/B] (создатель стола) передал ход следующему игроку [B]['.$row['login'].'][/B]';
										add_str($per_str,1);
									}else{
										$per_str='[B]['.$last_login.'][/B] говорит  что он ЗАВЕРШИЛ ХОД, СЛЕДУЮЩИМ ХОДИТ [B]['.$row['login'].'][/B]';
										add_str($per_str,1);								
									}
									break;
								}else{
									$per_str='[B]['.$row['login'].'] ПРОПУСКАЕТ ХОД [/B]ТАК КАК ОН НЕ АКТИВЕН БОЛЕЕ 10 секнд';
									add_str($per_str,1);   
									$active_user=$row['id_user'];
								}
							}
							$last_id=$row['id_user'];
							$last_login=$row['login'];
					}
					  
					if (!isset($current_id))
					{
						if ($_REQUEST['send_com_step']==2){		
							$per_str='[B]['.$_SESSION['login'].'][/B] (создатель стола) передал ход следующему игроку [B]['.$first_login.'][/B]';
							add_str($per_str,1);
						}else{
							$per_str=' говорит  что он ЗАВЕРШИЛ ХОД, СЛЕДУЮЩИМ ХОДИТ [B]'.$first_login.'[/B]';
							add_str($per_str,0);								
						}
						$current_id=$first_id;
					}
						
					//Обновляем значение в таблице столов, пользователя который ходит 	
					$query='UPDATE game_tables SET active_user='.$current_id.', phase_move=0, help_me=0 
							 WHERE (id_gt='.$_SESSION['id_gt'].')';
					$result=mysql_query($query);   
					if (mysql_error($DBLink)!="") {
							die("Неудалось передать ход другому пользователю");
					}      		
					//Обнуляем значение золота у игрока
					$query="UPDATE users SET u_gold=0, i_help=0 WHERE (id_gt=".$_SESSION['id_gt'].")";
					$result=mysql_query($query);   
					if (mysql_error($DBLink)!="") {
							die("Неудалось обнулить золото");
					}      					
							
				}
			 
			}else{
				$current_id=$_SESSION['id_user'];
			} 
			
			//Если ход не текущего игрока то прячим кнопку конец хода
			if ($current_id==$_SESSION['id_user']){
				$json_data['button_mess_boi']='visible';
			}else{
				$json_data['button_mess_boi']='hidden';
			}
				  
			echo json_encode($json_data);	
			mysql_close ($DBLink);         
        }    
}
?>  