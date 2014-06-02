<?php 
if(!isset($_SESSION)){session_start();}
//Для всех файлов
require_once("global.php");
require_once("my_function.php");
require_once("modules/mysql.php");


function show_chat()
{
	global $mysql;
	$getlaststr = $mysql->sql_query("SELECT * FROM minichat ORDER BY time DESC LIMIT 60");
	while($chatarr[] = mysql_fetch_assoc($getlaststr))
	{
	}
	krsort($chatarr);
	foreach($chatarr as $chatdata)
	{
		if(strlen($chatdata["text"])>0)
		{
			$time = date("d/m H:i:s",$chatdata["time"]);
			echo "[".$time."] [<a title=\"Профиль игрока\" class=\"clickablelogin\" href=\"javascript:showuser(".$chatdata["id_user"].")\" >".$chatdata["login"]."</a>] => : ".$chatdata["text"]."<br/>";
		}
	}
}

function add_str()
{
	global $mysql;
	$message = $_REQUEST["send_text"];
	$login = $_SESSION["login"];
	$login .= "(".$_SESSION["level"].")";
	$id_user = $_SESSION["id_user"];
	$time = time();
	$message=substr($message,0,256);
	$message=htmlspecialchars($message, ENT_QUOTES);

	//С помощью этих строк мы выделяем из реплики адреса сайтов и e-mail’ы.
	$message = preg_replace("|(http://.[-a-zA-Z0-9@:%_+.~#?&//=]+?)|i","<a href=\"$1\" target=\"_blank\">$1</a>",$message);
	$message = preg_replace("|(www\.[-a-zA-Z0-9@:%_+.~#?&//=]\.[a-z]{2,6})|i","<a href=\"$1\" target=\"_blank\">$1</a>",$message);
	$message = preg_replace("|([-._a-z0-9]+@(?:[a-z0-9][-a-z0-9]+\.)+[a-z]{2,6})|i","<a href=\"mailto:$1\">$1</a>", $message); 
	$message = str_replace("[B]","<b>",$message);
	$message = str_replace("[/B]","</b>",$message);
	
	if(strlen($message)>0)
	{
		$mysql->sql_query("INSERT INTO minichat VALUES ('$time', '$id_user', '$login', '$message')");
	}
}

if (isset($_REQUEST['send_com_forum'])){
      if (isset($_SESSION['id_user'])){				
			  //Выводи сообщение от пльзователя к пользователю
			  if (strcmp('1',$_REQUEST['send_com_forum'])==0) {
				  if (isset($_REQUEST['send_text'])){                  
					  if (!empty($_REQUEST['send_text'])){                        
							add_str();
							unset($_REQUEST['send_text']);  
					  }
				  }
				  show_chat();
			  //Обновляем сообщения
			  }elseif (strcmp('2',$_REQUEST['send_com_forum'])==0){
					  show_chat();  
					  unset($_REQUEST['send_text']);    
			  }  
        } 
}
?>
