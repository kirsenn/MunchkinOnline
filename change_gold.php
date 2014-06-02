<?php 
session_start();
require_once("global.php");
require_once("chat.php");
require_once("modules/mysql.php");
require_once("my_function.php");
?>

<?php
if ( (isset($_SESSION['id_user'])) && (isset($_SESSION['init'])) && (isset($_SESSION['id_gt'])) ){
		if ($_REQUEST['send_com_gold']==0) {
			$id_user=$_REQUEST['id_user'];			  
			if  (is_numeric($id_user)){
				$result_user=$mysql->sql_query('SELECT * FROM users WHERE (id_user='.$id_user.')');
				$row_user=mysql_fetch_array($result_user);
				
				if ($id_user==$_SESSION['id_user']){
					$content_window="<center>Ваши сбережения: ".$row_user['u_gold'].' голдов<br>';				
					$content_window=$content_window."1 уровень=1000 голдов</center><br>";
					if ( ($row_user['u_gold']>1000) && ($row_user['u_level']<9) ){
						$content_window=$content_window.'<div align="center" id="button_change_gold" style="border:#555 1px solid;border-radius:5px; -moz-border-radius: 5px;">Купить уровень</div>';
					}else{
						if ($row_user['u_gold']<1000){
							$content_window=$content_window."<center><font color=red>У вас не хватает голдов для приобретения уровня</font></center>";
						}else{
							$content_window=$content_window."<center><font color=red>Вы 9 уровень, вам нельзя покупать уровни</font></center>";
						}
					}
					$json_data['content_window']=$content_window;
				}else{
					$json_data['content_window']="<center>Cбережения ".$row_user['login'].": ".$row_user['u_gold'].'</center>';
				}
				echo json_encode($json_data);
			}	
		}elseif($_REQUEST['send_com_gold']==1){	
				$result_user=$mysql->sql_query('SELECT * FROM users WHERE (id_user='.$_SESSION['id_user'].')');
				$row_user=mysql_fetch_array($result_user);		
				if ($row_user['u_level']<9){
					$u_gold=$row_user['u_gold'];//Узнаем сколько голдов у игрока
					$get_level=floor($u_gold/1000);//Узнаем сколько игрок получит уровней после продажи карты
					$remain=0;
					//Проверка можно ли игроку дать столько уровней сколько он золота заработал
					if($get_level==1){//Если он получит 1 уровнь
						$u_gold=$u_gold%1000;//
						$u_level=$row_user['u_level']+$get_level;
					}else{//Если он получит больше 1 уровня
						$sum_level=$row_user['u_level']+$get_level;//узнаем сколько у игрока будет уровней если обменяем все голды
						if ($sum_level>9){//Так как игрок не может подняться более 9 уровня за продажу шмоток, то просто их оставляем в виде голдов
							$remain=$sum_level-9;
							$u_level=9;
							$u_gold=$remain*1000+$u_gold%1000;
						}else{//Все нормльно просто даем игроку нужное количествоуровней
							$u_gold=$u_gold%1000;
							$u_level=$row_user['u_level']+$get_level;
						}
					}
					
					$result_last_card=$mysql->sql_query( "SELECT MAX(num_d) AS num_d FROM discards WHERE (id_gt=".$_SESSION['id_gt'].")" );
					$row_last_card=mysql_fetch_array($result_last_card);				
					$num_card=$row_last_card['num_d']+1;			
					
					$mysql->sql_query("UPDATE users SET u_gold=".$u_gold.", u_level=".$u_level." WHERE (id_user=".$_SESSION['id_user'].")");		
						
					$per_str=' обменял '.($get_level*1000-$remain*1000).' голдов на '.($get_level-$remain).' уровень и поднялся с '.$row_user['u_level'].' на '.$u_level.' уровень';   
					add_str($per_str,0); 								

					$content_window="<center>Ваши сбережения: ".$u_gold.' голдов<br>';							
					$content_window=$content_window."1 уровень=1000 голдов</center><br>";
					if ( ($u_gold>1000) && ($u_level<9) ){
						$content_window=$content_window.'<div align="center" id="button_change_gold" style="border:#555 1px solid;border-radius:5px; -moz-border-radius: 5px;">Купить уровень</div>';
					}else{
						if ($u_gold<1000){
							$content_window=$content_window."<center><font color=red>У вас не хватает голдов для приобретения уровня</font></center>";
						}else{
							$content_window=$content_window."<center><font color=red>Вы 9 уровень, вам нельзя покупать уровни</font></center>";
						}
					}
					$json_data['content_window']=$content_window;
					echo json_encode($json_data);
					
				}
		}  
}                  
?>