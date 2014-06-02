<?php 
//Выбрасить игрока за-за стола
session_start();
require_once("global.php");
require_once("chat.php");
require_once("my_function.php");
?>

<?php
if ( (isset($_SESSION['id_user'])) && (isset($_SESSION['init'])) && (isset($_SESSION['id_gt'])) ){
		//Узнаем какая команда пришла
        if ($_REQUEST['send_com_change']==1) {
		        $id_user=$_REQUEST['id_user'];
			    $id_gt=$_SESSION['id_gt'];
			    if  (is_numeric($id_user)){
					    $DBLink=connectdb(); 
					    //получаем данные по игровому столу
					    $query='SELECT * FROM game_tables WHERE (id_gt='.$id_gt.')';
					    $result=mysql_query($query); 
					    if (mysql_num_rows($result)!=0){	
							$row=mysql_fetch_array($result);								
							//Узнаем является ли игрок пославший команду создателем стола
							if ($row['creator']==$_SESSION['login']){							
								//Получаем данные по игрокук которого хотят назначитьь создателем стола
								$query='SELECT * FROM users WHERE ( (id_user='.$id_user.') AND (id_gt='.$id_gt.') )';
								$result=mysql_query($query); 										
								if (mysql_num_rows($result)!=0){//Если данные нашлись то назначаем его создателем стола	
								    $row=mysql_fetch_array($result);	
									$change_creator=$row['login'];
									
									$query1='UPDATE game_tables SET creator="'.$change_creator.'"
											WHERE (id_gt='.$id_gt.')';
									$result1=mysql_query($query1); 
							
									$per_str=' ВНИМАНИЕ! '.$_SESSION['id_user'].' (cоздатель стола), отдал все права создателя стола [B]['.$change_creator.'][/B]';								
									add_str($per_str,1);//Системное сообщение в игровой чат   
									
									$json_data['next_step']="hidden";
									$json_data['close_table']="hidden";									
									$json_data['change_creator']="hidden";
									$json_data['kick']="hidden";
	
									
									echo json_encode($json_data);	
									
								}																	  							
							}				
					    } 			
					    mysql_close ($DBLink);   
				}
		}		
}    
?>  