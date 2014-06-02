<?php 
require_once("global.php");
require_once("chat.php");
require_once("destroy_table.php");
require_once("my_function.php");
require_once("modules/mysql.php");
require_once("modules/functions.php");
?>

<?php
//*************************************************

function victory($par){//Если параметр 1 то значит открыто окно голосовалки, и мы делаем более детальную проверку
	$mysql = new MySQL;//Создаем объект 
	$time_delay=60;//Время отведеное на голосование
	if ( isset($_SESSION['id_user']) ) 
	{
		$id_user=$_SESSION['id_user'];  
		$id_gt=$_SESSION['id_gt'];     

		//Проверяем дорос ли кто из игроков до 10 уровня и существует ли игровой стол
		$result_ulevel=$mysql->sql_query('SELECT * FROM users JOIN game_tables ON (users.id_gt=game_tables.id_gt) WHERE ((users.id_gt='.$id_gt.') AND (users.u_level=10))');		

		//если недорос или стол не существует то просто прерываем функцию        
		if ((mysql_num_rows($result_ulevel)==0) && ($par==0) )
		{
			$json_data['flag_victory']="0";
			$json_data['text_window']="Большинство проголосовало Против.Игра продолжается";
			echo json_encode($json_data);	
			return;
		}else
		{//Если окно голосовалки открыто это par=1				
			if (mysql_num_rows($result_ulevel)>0)
			{//имеется игрок заявивший что он 10 уровень
				//Получаем параметры игрока и игрового стола 
				$row = mysql_fetch_array($result_ulevel);
				$num_user=$row['num_user'];//количество игроков за столом
				$id_user_win=$row['id_user'];//id игрока который заявил что он выиграл
				$login_user_win=$row['login'];//Логин игрока который заявил что он выиграл
				
				//Считываем значение статистики
				$result_statgame=$mysql->sql_query('SELECT * FROM statistic_game WHERE ((id_gt='.$id_gt.') AND (id_user='.$id_user.'))');
				$row1 = mysql_fetch_array($result_statgame); 
				
				//Если игра закончена уже то пропускаем этот кусок кода и выводим результаты голосования
				if ($row1['end_game']==0){
					$time_limit=$row1['time_limit'];//Время до окончания голосования
					$status_vote=$row1['status_vote'];//Статус голосовалки(уже запущена она или нет)
					//Проверяем Голосовалка уже запущена или нет
					if (($status_vote==0) && ($id_user_win>0))
					{//Устанавливаем начальные параметры голосовалки: Время голования, Заявленного победителя, статус голосовалки
						$mysql->sql_query('UPDATE statistic_game SET time_limit='.(time()+$time_delay).', winner="'.$row['login'].'", status_vote=1 WHERE (id_gt='.$id_gt.')');
					}
								
					//Проверяем время для голосования окончено или нет 
					if ( ($time_limit>0) && ($time_limit-time()<=0) ){
						//если время окончено, приводим в порядок все значения таблицы статистики игр
						//Голос у тех кто не проголосовал = СОГЛАСЕН
						$mysql->sql_query("UPDATE statistic_game SET vote=1 WHERE ( (vote=0) AND (id_gt=".$id_gt.") )");
						
						//Узнаем количество записей кто проголосовал СОГЛАСЕН
						$result_numPRO=$mysql->sql_query("SELECT count(*) AS count FROM statistic_game WHERE ( (id_gt=".$id_gt.") AND (vote=1))");
						$row2=mysql_fetch_array($result_numPRO);
						
						//Указываем у всех пользователей стола сколько игроков проголосовало ЗА и ПРОТИВ 
						$mysql->sql_query("UPDATE statistic_game SET pro=".$row2['count'].", con=".($num_user-$row2['count']).", time_limit=0 WHERE (id_gt=".$id_gt.")");
						
						//Считываем обновленное значение статистики
						$result_statgame=$mysql->sql_query('SELECT * FROM statistic_game WHERE ((id_gt='.$id_gt.') AND (id_user='.$id_user.'))');
						
						$row1 = mysql_fetch_array($result_statgame); 
					} 
				}
			  
				//***********Считаем голоса
				$remain=$num_user%2;
				if ($remain==0){
					$major_pro=round($num_user/2)+1;//за
					$major_con=round($num_user/2);//против
				}else{
					$major_pro=round($num_user/2)+1;//за
					$major_con=round($num_user/2)-1;//против
				}
							
				if ($row1['pro']>=$major_pro) 
				{//Если большинство проголосовали ЗА победу
					$text_window='<center><h2>Игрок <B> '.$login_user_win.'</B> стал победителем</h2><br><br>
								Всего игроков: '.$row['num_user'].'<br>
								Проголосовало ЗА: '.$row1['pro'].' ('.$major_pro.')<br>
								Проголосовало ПРОТИВ: '.$row1['con'].' ('.$major_con.')<br>	<br>
								<a href="statgame.htm">Перейти на страницу статистики</a></center>';
					$json_data['text_window']=$text_window;
					$json_data['flag_victory']="2";
					
					//Все игра окончена теперь необходимо написать игра прошла в зачет или нет им подсчитать полученный опыт	
					//Проверяем сколько по времени длилась игра
					$duration_game=time()-$row1['start_game'];
					$exper=0;//Заработанный опыт по умолчанию равен 0
					//ПРоверяем сколько за игровым столом активных игроков
					$expend_time=time()-60;
					$result_uactive=$mysql->sql_query('SELECT * FROM users WHERE ( (id_gt='.$id_gt.') AND (active>'.$expend_time.') )');
					//$json_data['text_window'].='-duration_game='.$duration_game.'-expend_time='.$expend_time.'mysql_rows='.mysql_num_rows($result_uactive);
					if ( (mysql_num_rows($result_uactive)>=3)&&($duration_game>600) )
					{//если  игроков >3 и время игры>10 минут
						//получаем сумму уровней всех игроков за столом
						$json_data['zapr'].='SELECT SUM(level) AS sumlevel FROM users WHERE ((id_gt='.$id_gt.') AND (active>'.$expend_time.'))';
						$result_sumlevel=$mysql->sql_query('SELECT SUM(level) AS sumlevel FROM users WHERE ((id_gt='.$id_gt.') AND (active>'.$expend_time.'))');			
						$row_sumlevel = mysql_fetch_array($result_sumlevel);
						$sumlevel=$row_sumlevel['sumlevel'];
						//$json_data['text_window'].='<br>-sumlevel='.$sumlevel;
						
						//Получаем всех игроков сидящих за игровым столом
						$result_users=$mysql->sql_query('SELECT * FROM users WHERE (id_gt='.$id_gt.')');					
						//Начисляем опыт игрокам за игру
						while ($row_users = mysql_fetch_array($result_users))
						{
							if ($row_users['active']>$expend_time)
							{
								if ($id_user_win==$row_users['id_user'])
								{
									$kvic=5;//Коэффициент победы
									$exper=($kvic*10)+($kvic*$sumlevel); 	
								}else{
									$kvic=1;//Коэффициент победы
									$exper=round( (($kvic*10)+($kvic*$sumlevel))*($row_users['u_level']/10) );
								}
							}else{
								$exper=0;
							}
							$exper_user_all=$exper+$row_users['exper'];
							$mysql->sql_query('UPDATE statistic_game SET take_exper='.$exper.' WHERE ((id_gt='.$id_gt.') AND (id_user='.$row_users['id_user'].'))');
							//Проверяем игрок дорос до следующего уровня
							$next_exper_level=get_exper_level($row_users['level']);	
							if ($exper_user_all>=$next_exper_level){
								$next_level=$row_users['level']+1;

								$mysql->sql_query('UPDATE users SET exper='.$exper_user_all.', level='.$next_level.' WHERE (id_user='.$row_users['id_user'].')');
							}else{
								$mysql->sql_query('UPDATE users SET exper='.$exper_user_all.' WHERE (id_user='.$row_users['id_user'].')');
							}														
						}						
						destroy_table($id_gt,1);//Игра пошла в зачет
						$_SESSION['id_gt']=$id_gt;
					}else
					{//Если игра не пошла в зачет
						$mysql->sql_query('UPDATE statistic_game SET exper='.$exper.' WHERE (id_gt='.$id_gt.')');
						destroy_table($id_gt,5);//Игра пошла в не зачет
						$_SESSION['id_gt']=$id_gt;
					}	
					echo json_encode($json_data);
					return;
					
				} elseif ($row1['con']>=$major_con)
				{//Если большинство проголосовали против победы игрока
					$json_data['text_window']="Игра продолжается";
					$json_data['flag_victory']="2";
					//ПРоверяем кто нибудь уже сбросил все характеристики на 0
					if ($id_user_win>0)
					{
						//Сбрасываем все начальные установки статистики,отвечающие за голосование на 0
						$mysql->sql_query('UPDATE statistic_game SET pro=0, con=0,vote=0, time_limit=0, winner="0", status_vote=0 WHERE (id_gt='.$id_gt.')');
						//Уменьшаем уровень игрока, заявившего что он победитель на 1
						$mysql->sql_query('UPDATE users SET u_level=9 WHERE (id_user='.$id_user_win.')');
					}
					echo json_encode($json_data);		
					return;	
				}  
				//***********Закончили Считать голоса
			  
				//Подготавливаем текст который выведем в окно
				$text_window='<center>Игрок <B>'.$row['login'].'</B> заявил что он поднялся до 10-го уровня,следовательно стал ПОБЕДИТЕЛЕМ<br>
						  Вы согласны что '.$row['login'].' стал победителем?<br><br>
						  Всего игроков: '.$row['num_user'].'<br>
						  Проголосовало ЗА: '.$row1['pro'].' ('.$major_pro.')<br>
						  Проголосовало ПРОТИВ: '.$row1['con'].' ('.$major_con.')<br>';
				//Проверяем время для голосование окончено или нет 
				if ($row1['time_limit']-time()>=0){                 
				   $text_window=$text_window.'До конца голосования осталось: '.($row1['time_limit']-time()).'</center>';
				}else{
				   $text_window=$text_window.'Голосование  окончено!</center>';
				}                 
			  
				if ($row1['vote']==0){
				   $text_window=$text_window.'<center><br><div id="vote_pro">Согласен</div><br> 
								<div id="vote_con">Не согласен</div> </center>';
				}			
					
				$json_data['text_window']=$text_window;
				$json_data['flag_victory']="1";
				echo json_encode($json_data);
			}else
			{//Голосование окончено всем игрокам выводим сообщение о результате голосования,может быть как положительнгое так и отрицательное
				//Так как уже нету игрока объявившего что он > 10 уровня, или вобще игрового стола нету то проставляем параметры из табл статистики				
				//Считываем значение статистики
				$result_statgame=$mysql->sql_query('SELECT * FROM statistic_game WHERE ((id_gt='.$id_gt.') AND (id_user='.$id_user.'))');
				$row1 = mysql_fetch_array($result_statgame); 					  
				$num_user=$row1['num_user'];//количество игроков за столом
				$login_user_win=$row1['winner'];//Логин игрока который заявил что он выиграл
			  
				if ($row1['end_game']>0)
				{//Если голосование окончено и есть параметр окончания игры
					//***********Считаем голоса
					$remain=$num_user%2;
					if ($remain==0){
						$major_pro=round($num_user/2)+1;//за
						$major_con=round($num_user/2);//против
					}else{
						$major_pro=round($num_user/2)+1;//за
						$major_con=round($num_user/2)-1;//против
					}
								
					$text_window='<center><h2>Игрок <B> '.$login_user_win.'</B> стал победителем</h2><br><br>
								Всего игроков: '.$row['num_user'].'<br>
								Проголосовало ЗА: '.$row1['pro'].' ('.$major_pro.')<br>
								Проголосовало ПРОТИВ: '.$row1['con'].' ('.$major_con.')<br>	<br>
								<a href="statgame.htm">Перейти на страницу статистики</a></center>';
					$json_data['text_window']=$text_window;
					$json_data['flag_victory']="2";
					echo json_encode($json_data);
					return;		
				}else
				{//Если голосование окончено и но параметра конца игры нету
					$json_data['text_window']="Игра продолжается";
					$json_data['flag_victory']="2";
					echo json_encode($json_data);		
					return;					
				}
			}
						
			
		}                 		  
	}else{
		echo "Зарегестрируйтесь, для того чтобы начать играть";  
	}
}


function vote($your_vote){//Если вы нажади кнопку голосовать
          $id_user=$_SESSION['id_user'];  
          $id_gt=$_SESSION['id_gt'];  
          
          $DBLink=connectdb();
		  //Получаем значение статистики
		  $query='SELECT * FROM statistic_game WHERE ((id_gt='.$id_gt.') AND (id_user='.$id_user.'))';
		  $result=mysql_query($query);                  
		  $row_stgame = mysql_fetch_array($result);  
		  
		  //Устанавливаем ваш голос в вашей статистике
		  if ($row_stgame['vote']==0){
			  $query="UPDATE statistic_game SET vote=".$your_vote." 
					   WHERE ( (id_user=".$id_user.") AND (id_gt=".$id_gt.") )";
			  $result=mysql_query($query); 	
			  //Обновляем количество голосов у всех за игровым столом		  
			  if ($your_vote==1){
			  	  $per_str='Игрок [B]['.$_SESSION['login'].'][/B] проголосовал ЗА';
				  add_str($per_str,1); 
				  
				  $pro=$row_stgame['pro']+1;
				  $query2="UPDATE statistic_game SET pro=".$pro." 
						   WHERE ( (id_gt=".$id_gt.") )";
			  }elseif ($your_vote==-1){
				  $per_str='Игрок [B]['.$_SESSION['login'].'][/B] проголосовал ПРОТИВ';
				  add_str($per_str,1); 
				  
				  $con=$row_stgame['con']+1;
				  $query2="UPDATE statistic_game SET con=".$con." 
						   WHERE ( (id_gt=".$id_gt.") )";                      
			  }
			  $result2=mysql_query($query2);   
			  if (mysql_error($DBLink)!="") {
				  die("Неудалось создать стол\n");
			  } 		
		  }	
		  mysql_close($DBLink);		
		  victory(1);	
} 

if ($_REQUEST['send_com_victory']==0){
      if(!isset($_SESSION)){session_start();}
      victory(0);
}elseif($_REQUEST['send_com_victory']==1){
      if(!isset($_SESSION)){session_start();}
      victory(1);
}elseif ($_REQUEST['send_com_victory']==2){
      if(!isset($_SESSION)){session_start();}
      vote(1);
}elseif ($_REQUEST['send_com_victory']==3){
      if(!isset($_SESSION)){session_start();}
      vote(-1);
}
?>