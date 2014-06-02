<?php
if(!isset($_SESSION)){session_start();}

require_once("global.php");
require_once("my_function.php");
require_once("modules/mysql.php");

//error_reporting(0);

//Весь чат
function show_chat_all()
{
	global $mysql;
	$str_all = "";
	$getlaststr = $mysql->sql_query("SELECT * FROM gamechat WHERE id_gt=".$_SESSION['id_gt']." ORDER BY time DESC LIMIT 300");
	while($chatdata = mysql_fetch_assoc($getlaststr))
	{
		if(strlen($chatdata["text"])>0)
		{
			$text = str_replace("\n","<br/>",$chatdata["text"]);
			$time = date("d/m H:i:s",$chatdata["time"]);
			if($chatdata["type"]==0)
			{
				$str_all .= "[".$time."] [<a title=\"Профиль игрока\" class=\"clickablelogin\" href=\"javascript:showuser(".$chatdata["id_user"].")\" >".$chatdata["login"]."</a>] ".$text."<br/>";
			}
			else
			{
				$str_all .= "[".$time."] ".$text."<br/>";
			}
		}
	}
	$json_data['chat_text_all'] = $str_all;
	echo json_encode($json_data);
}

//Последние 20 строк
function show_chat()
{
	global $mysql;
	$str_all = "";
	$getlaststr = $mysql->sql_query("SELECT * FROM gamechat WHERE id_gt=".$_SESSION['id_gt']." ORDER BY time DESC LIMIT 30");
	while($chatdata = mysql_fetch_assoc($getlaststr))
	{
		if(strlen($chatdata["text"])>0)
		{
			$text = str_replace("\n","<br/>",$chatdata["text"]);
			if(stristr($text,$_SESSION['login'])){$text = "<span class=\"yourmess\">".$text."</span>";}
			
			$time = date("H:i:s",$chatdata["time"]);
			if($chatdata["type"]==0)
			{
				$str_all .= "[".$time."] [<a title=\"Профиль игрока\" class=\"clickablelogin\" href=\"javascript:showuser(".$chatdata["id_user"].")\">".$chatdata["login"]."</a>] ".$text."<br/>";
			}
			else
			{
				$str_all .= "[".$time."] ".$text."<br/>";
			}
		}
	}
	$json_data['chat_text'] = $str_all;
	echo json_encode($json_data);
}

function add_str($message,$type)
{
	$mysql = new MySQL;
	$login = $_SESSION["login"];
	$login .= "(".$_SESSION["level"].")";
	$id_user = $_SESSION["id_user"];
	
	if(strlen($_SESSION["login"])<1 || $_SESSION["login"]=="guest")
	{
		die();
	}
	
	$time = time();
	$message = substr($message,0,600);
	
	$message = htmlspecialchars($message, ENT_QUOTES);

	//С помощью этих строк мы выделяем из реплики адреса сайтов и e-mail’ы.
	$message = preg_replace("|(http://.[-a-zA-Z0-9@:%_+.~#?&//=]+?)|i","<a href=\"$1\" target=\"_blank\">$1</a>",$message);
	$message = preg_replace("|(www\.[-a-zA-Z0-9@:%_+.~#?&//=]\.[a-z]{2,6})|i","<a href=\"$1\" target=\"_blank\">$1</a>",$message);
	$message = preg_replace("|([-._a-z0-9]+@(?:[a-z0-9][-a-z0-9]+\.)+[a-z]{2,6})|i","<a href=\"mailto:$1\">$1</a>", $message); 
	$message = str_replace("[B]","<b>",$message);
	$message = str_replace("[/B]","</b>",$message);
	
	if(strlen($message)>0 && strlen($_SESSION['table_name'])>0)
	{
		if(strlen($type)>0)
		{
			$mysql->sql_query("INSERT INTO gamechat VALUES ('$time', ".$_SESSION['id_gt'].", '$id_user', '$login', '$message', $type )");
		}
		else
		{
			echo "Error while add_str. Need 2nd argument";
		}
	}
}

if (isset($_REQUEST['send_com_chat']))
{
	if((isset($_SESSION['id_user'])) && (isset($_SESSION['init'])) && (isset($_SESSION['id_gt'])) )
	{
		//Выводи сообщение от пльзователя к пользователю
		if (strcmp('1',$_REQUEST['send_com_chat'])==0) 
		{
			if(isset($_REQUEST['send_text']) && !empty($_REQUEST['send_text']))
			{
				add_str("[B]".$_REQUEST['send_user']."[/B] ".$_REQUEST['send_text'],0);
				unset($_REQUEST['send_text']);
			}
			show_chat();
		}
		//Обновляем сообщения
		elseif (strcmp('2',$_REQUEST['send_com_chat'])==0)
		{
			show_chat();  
			unset($_REQUEST['send_text']);    
		}
		//При нажатии на кнопку ПАС
		elseif (strcmp('mess_pas',$_REQUEST['send_com_chat'])==0)
		{ 
			$per_str='заявил что он [B]ПАС[/B]';
			add_str($per_str,0);
			show_chat();    
			unset($_REQUEST['send_text']);  
		//При нажатии на кнопку  бью
		}
		elseif (strcmp('mess_boi',$_REQUEST['send_com_chat'])==0)
		{  
			$per_str='заявил что он [B]БЬЕТ[/B] монстра';
			add_str($per_str,0);
			show_chat();    
			unset($_REQUEST['send_text']);  
		}
		//При нажатии на кнопку конец хода
		elseif (strcmp('mess_end',$_REQUEST['send_com_chat'])==0)
		{
			$per_str='говорит  что он [B]ЗАВЕРШИЛ ХОД[/B]';
			add_str($per_str,0);
			show_chat();
			unset($_REQUEST['send_text']); 
		}
		//При нажатии на кнопку  кубик
		elseif (strcmp('mess_cube',$_REQUEST['send_com_chat'])==0)
		{
			$per_str='бросил кубик и выпало  [B]'.rand(1,6).'[/B]';
			add_str($per_str,0);
			show_chat();
			unset($_REQUEST['send_text']);
		}
		elseif (strcmp('3',$_REQUEST['send_com_chat'])==0)
		{
			show_chat_all();
		}
	} 
}
?>
