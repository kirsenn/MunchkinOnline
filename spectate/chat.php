<?php
if(!isset($_SESSION)){session_start();}

require_once("../global.php");
require_once("../my_function.php");
require_once("../modules/mysql.php");

$str_all = "";

$getlaststr = $mysql->sql_query("SELECT * FROM gamechat WHERE id_gt=".$_SESSION['id_gt_spec']." ORDER BY time DESC LIMIT 30");
while($chatdata = mysql_fetch_assoc($getlaststr))
{
	if(strlen($chatdata["text"])>0)
	{
		$text = str_replace("\n","<br/>",$chatdata["text"]);
		$time = date("d/m H:i:s",$chatdata["time"]);
		if($chatdata["type"]==0)
		{
			$str_all .= "[".$time."] [<b><a title=\"Профиль пользователя\" target=\"_blank\" href=\"profile_".$chatdata["id_user"].".htm\">".$chatdata["login"]."</a></b>] ".$text."<br/>";
		}
		else
		{
			$str_all .= "[".$time."] ".$text."<br/>";
		}
	}
}
$json_data['chat_text'] = $str_all;
echo json_encode($json_data);

?>
