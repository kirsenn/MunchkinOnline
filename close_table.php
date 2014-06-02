<?php 
//Выбрасить игрока за-за стола
session_start();
require_once("global.php");
require_once("chat.php");
require_once("my_function.php");
?>

<?php
if ( (isset($_SESSION['id_user'])) && (isset($_SESSION['init'])) && (isset($_SESSION['id_gt'])) )
{
	//Узнаем какая команда пришла
	if ($_REQUEST['send_com_close']==1) 
	{
		$id_gt=$_SESSION['id_gt'];		  
		$DBLink=connectdb(); 
		//Узнаем существует ли игровой стол,если существует получаем пераметры его
		$query='SELECT * FROM game_tables WHERE (id_gt='.$id_gt.')';
		$result=mysql_query($query); 
		if (mysql_num_rows($result)!=0)
		{	
			$row=mysql_fetch_array($result);	
			$gt_status=$row['gt_status'];					
			//Узнаем является ли игрок пославший команду создателем стола
			if ($row['creator']==$_SESSION['login'])
			{								
					if ($gt_status==1){//Если к игровому столу запрещено присоединяться то мы разрешаем
						$query1="UPDATE game_tables SET gt_status=3
								WHERE (id_gt=".$id_gt.")";
						$result1=mysql_query($query1); 
						$json_data['text_frame']="<div class=\"close_table_cl\">Закрыть<br/>стол</div>";
						$per_str='ВНИМАНИЕ! [B]'.$_SESSION['login'].'[/B] Создатель стола , РАЗРЕШИЛ другим игрокам присоединяться к игре';								
					}elseif($gt_status==3){//Если к игровому столу разрешено присоединяться то мы запрещаем
						$query1="UPDATE game_tables SET gt_status=1
								WHERE (id_gt=".$id_gt.")";
						$result1=mysql_query($query1); 
						$json_data['text_frame']="<div class=\"close_table_op\">Открыть<br/>стол</div>";
						$per_str='ВНИМАНИЕ! [B]'.$_SESSION['login'].'[/B] Создатель стола , ЗАПРЕТИЛ другим игрокам присоединяться к игре';								
					}
														  
					if (mysql_error($DBlink)!="") {
					   die("Неудалось ввпнуть игрока из-за стола");
					}      										
					add_str($per_str,1);//Системное сообщение
					echo json_encode($json_data);								
			}				
		} 			
		mysql_close ($DBLink);   
	}		
}    
?>  