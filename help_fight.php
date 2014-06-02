<?php 
//Выбрасить игрока за-за стола
session_start();
require_once("global.php");
require_once("chat.php");
require_once("my_function.php");
require_once("modules/mysql.php");
?>

<?php
if ( (isset($_SESSION['id_user'])) && (isset($_SESSION['init'])) && (isset($_SESSION['id_gt'])) ){
	if ( ($_REQUEST['send_com_help_fight']==1) && (isset($_REQUEST['id_user'])) ) {
		$id_user_help=$_REQUEST['id_user'];//Игрок которому предлагаем помощь или игрок у которого мы принимаем помощь 
	    $id_gt=$_SESSION['id_gt'];
		$user_suggest=$_REQUEST['user_suggest'];//Предложение за которое игрок поможет в бою
		//Обрабатываем текстовую строку что бы она была без багов
		if ($user_suggest!==0){
			$user_suggest = substr($user_suggest,0,150);
		
			$user_suggest = htmlspecialchars($user_suggest, ENT_QUOTES);

			//С помощью этих строк мы выделяем из реплики адреса сайтов и e-mail’ы.
			$user_suggest = preg_replace("|(http://.[-a-zA-Z0-9@:%_+.~#?&//=]+?)|i","<a href=\"$1\" target=\"_blank\">$1</a>",$user_suggest);
			$user_suggest = preg_replace("|(www\.[-a-zA-Z0-9@:%_+.~#?&//=]\.[a-z]{2,6})|i","<a href=\"$1\" target=\"_blank\">$1</a>",$user_suggest);
			$user_suggest = preg_replace("|([-._a-z0-9]+@(?:[a-z0-9][-a-z0-9]+\.)+[a-z]{2,6})|i","<a href=\"mailto:$1\">$1</a>", $user_suggest); 
			$user_suggest = str_replace("[B]","<b>",$user_suggest);
			$user_suggest = str_replace("[/B]","</b>",$user_suggest);
		}
		if  (is_numeric($id_user_help))
		{
			//Получаем данные по игровому столу
			$result_gt=$mysql->sql_query('SELECT * FROM game_tables WHERE (id_gt='.$_SESSION['id_gt'].')');
			$row_gt=mysql_fetch_array($result_gt);	
			
			if ($_SESSION['id_user']==$row_gt['active_user'])
			{//Проверяем сейчас ваш ход? Я ПРИНИМАЮ ПОМОЩЬ
				//Принимаем помощь от другого игрока
				if ($row_gt['help_me']==0){
					$mysql->sql_query('UPDATE game_tables SET help_me='.$id_user_help.' WHERE id_gt='.$_SESSION['id_gt']);	
					//Получаем информацию по игроку который предложил помощь
					$result_uhelper=$mysql->sql_query('SELECT * FROM users WHERE (id_user='.$id_user_help.')');
					$row_uhelper=mysql_fetch_array($result_uhelper);					
					
					$per_str=' принимает помощь от [B]'.$row_uhelper['login'].'[/B] и соглашается на поставленные условия оказания помощи';
					add_str($per_str,0);  //Системное сообщение  				
				}
			}else
			{//Проверяем сейчас не ваш ход? Я ПРЕДЛАГАЮ ПОМОЩЬ
				//Устанавливаем поле I_help т.е. вы предлагаете другому игроку свою помощь
				$mysql->sql_query('UPDATE users SET i_help='.$row_gt['active_user'].' WHERE id_user='.$_SESSION['id_user']);
				$per_str=' предлагает оказать ПОМОЩЬ в бою с монстром, за это просит: "'.$user_suggest.'"';
				add_str($per_str,0);  //Системное сообщение  
			}
		}		
	}    
}
?>  