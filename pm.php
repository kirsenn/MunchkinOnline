<?php
session_start();

require_once("modules/mysql.php");

if(!isset($_SESSION["login"]))die("I'm really sorry but you have to log in!");

if($_GET['act']!=='check' && $_GET['act']!=='send')
{
	?>
	<table width="100%" style="background:#C19A72; margin-bottom:5px;">
		<tr>
			<td align="center" style="color:#fff; text-shadow:none;">
				<b><u>Мои сообщения</u></b>
			</td>
			<td width="20px" valign="top" align="right">
				<span title="Закрыть окно сообщений" style="cursor:pointer;" onClick="mymessageshide()"><img src="picture/cross.png"></span>
			</td>
		</tr>
		<tr>
			<td align="center" colspan="2" style="text-shadow:none;">
				<a href="#" <?php if($_GET["act"]=="in"){echo " style=\"color:#2C231A\" ";}else{echo " style=\"color:#7D644A\" ";} ?> onClick="mymessagesshow('in')"><b>Входящие</b></a> | 
				<a href="#" <?php if($_GET["act"]=="out"){echo " style=\"color:#2C231A\" ";}else{echo " style=\"color:#7D644A\" ";} ?> onClick="mymessagesshow('out')"><b>Исходящие</b></a>
			</td>
		</tr>
	</table>
	<?php
}

if($_GET['act']=='in')
{
	$linkmsgs = $mysql->sql_query("SELECT * FROM messages WHERE `to`='".$_SESSION["id_user"]."' ORDER BY id DESC LIMIT 25");
	$cnt = mysql_num_rows($linkmsgs);

	while($msgdata = mysql_fetch_assoc($linkmsgs))
	{
		$get_user_info = $mysql->sql_query("SELECT * FROM users WHERE id_user='".$msgdata["from"]."'");
		$udata = mysql_fetch_assoc($get_user_info);
		?>
		<table class="msgbox" width="100%" cellpadding="0" cellspacing="0">
			<tr bgcolor="#F6C492">
				<td  align="justify">
					<b><a href="#" onClick="showuser('<?=$msgdata["from"]?>')"><?=$udata["login"] ?></a></b>: <b><?=$msgdata["subject"] ?></b><br/>
					<?php if($msgdata["isread"]==0){ ?><font color="red">Новое.</font><?php } ?> 
					<a href="#" onClick="showonemessage('<?=$msgdata["id"]?>');"><?php echo substr($msgdata["text"],0,124); if(strlen($msgdata["text"])>124){echo "...";} ?></a>
					<br/><small class="hint"><?php echo date("d.m.Y H:i",$msgdata["date"]); ?></small>
				</td>
				<td align="right">
					<?php
					if(strlen($udata["image"])>1)
					{
						?><img src="picture/users/<?=$udata["image"] ?>" width="35" border="0" alt="Avatar"/><?php
					}
					else
					{
						?><img src="picture/users/default.png" width="35" border="0" alt="Avatar"/><?php
					}
					?>
				</td>
			</tr>
		</table>
		<hr/>
		<?php
	}
	if($cnt==25)
	{
		?><a href="inmsgs.htm">Все сообщения</a><?php
	}
}

if($_GET['act']=='out')
{
	$linkmsgs = $mysql->sql_query("SELECT * FROM messages WHERE `from`='".$_SESSION["id_user"]."' ORDER BY id DESC LIMIT 25");
	$cnt = mysql_num_rows($linkmsgs);

	while($msgdata = mysql_fetch_assoc($linkmsgs))
	{
		$get_user_info = $mysql->sql_query("SELECT * FROM users WHERE id_user='".$msgdata["to"]."'");
		$udata = mysql_fetch_assoc($get_user_info);
		?>
		<table class="msgbox" width="100%" cellpadding="0" cellspacing="0">
			<tr bgcolor="#F6C492">
				<td align="justify">
					Кому: <b><a href="#" onClick="showuser('<?=$msgdata["to"]?>')"><?=$udata["login"] ?></a></b><br/>
					<b><?=$msgdata["subject"] ?></b><br/>
					<?=$msgdata["text"] ?>
					<br/><small class="hint"><?php echo date("d.m.Y H:i",$msgdata["date"]); ?></small>
				</td>
				<td align="right">
				<?php
					if(strlen($udata["image"])>1)
					{
						?><img src="picture/users/<?=$udata["image"] ?>" width="35" border="0" alt="Avatar"/><?php
					}
					else
					{
						?><img src="picture/users/default.png" width="35" border="0" alt="Avatar"/><?php
					}
					?>
				</td>
			</tr>
		</table>
		<hr/>
		<?php
	}
	if($cnt==25)
	{
		?><a href="outmsgs.htm">Все сообщения</a><?php
	}
}

if($_GET['act']=='show' && isset($_GET["id"]))
{
	$id = $_GET["id"];
	if(!is_numeric($_GET["id"])){die("Enter a number");}
	
	$get_msg = $mysql->sql_query("SELECT * FROM messages WHERE id='$id'");
	$msgdata = mysql_fetch_assoc($get_msg);
	$get_user_info = $mysql->sql_query("SELECT * FROM users WHERE id_user='".$msgdata["from"]."'");
	$udata = mysql_fetch_assoc($get_user_info);
	
	if($_SESSION["id_user"]!==$msgdata["to"]){die("Вы не можете просматривать чужие сообщения");}
	
	?>
	<table class="msgbox" width="100%" cellpadding="0" cellspacing="0">
		<tr bgcolor="#EABA8B">
			<td align="left">
				От: <a href="profile_<?=$msgdata["from"]?>.htm"><?=$udata["login"] ?></a>
			</td>
			<td align="right">
				<?php echo date("d.m.Y H:i",$msgdata["date"]); ?>
			</td>
		</tr>
		<tr bgcolor="#F6C492">
			<td align="justify">
				<?php if($msgdata["isread"]==0){ $mysql->sql_query("UPDATE messages SET isread=1 WHERE id='$id'"); } ?>
				<b><?=$msgdata["subject"] ?></b><br/>
				<?=$msgdata["text"]?>
			</td>
			<td align="right">
			<?php
			if(strlen($udata["image"])>1)
			{
				?><img src="picture/users/<?=$udata["image"] ?>" width="35" border="0" alt="Avatar"/><?php
			}
			else
			{
				?><img src="picture/users/default.png" width="35" border="0" alt="Avatar"/><?php
			}
			?>
			</td>
		</tr>
	</table>
	<button OnClick="document.getElementById('messageuserarea').style.display='inline';">Ответить</button>
	<button OnClick="mymessagesshow('in')">Назад</button>
	
	<div id="messageuserarea" style="display:none; margin-left:5px;">
		<span class="hint" style="margin:0px auto;">Отправить сообщение</span><br/>
		<textarea id="usermessagetext" style="width:100%; height:39px;"></textarea><br/>
		<button OnClick="sendusermessage('<?=$msgdata["from"] ?>')">Отправить</button>
		<button OnClick="document.getElementById('messageuserarea').style.display='none';">Отмена</button>
	</div>
	<?php
}

if($_GET['act']=='check')
{
	$new_msg_cnt = mysql_num_rows($mysql->sql_query("SELECT * FROM messages WHERE `to`='".$_SESSION['id_user']."' AND isread=0"));
	if($new_msg_cnt<1)
	{
		?><img width="32" src="picture/mail_generic.png"><?php
	}
	else
	{
		?>
		<img width="32" src="picture/newmessage.gif">
		<span style="position:absolute; background:red; color:#ffffff; display:block; font-size:9px; width:12px; height:12px; padding:2px; text-align:center"><?=$new_msg_cnt ?></span>
		<?php
	}
}


if($_GET['act']=='send' && isset($_GET["user"]))
{
	$to_user = $_GET["user"];
	if(!is_numeric($to_user)){die("Hack stop");}
	if($to_user == $_SESSION["id_user"]){die("Вы не можете послать сообщение себе");}
	
	$getuinfo = $mysql->sql_query("SELECT * FROM users WHERE id_user=".$to_user."");
	if(mysql_num_rows($getuinfo)<1){die("Такого пользователя не существует");}
	$udata = mysql_fetch_assoc($getuinfo);
	
	if(isset($_POST["text"]))
	{
		require_once("modules/functions.php");
		$subject = safform($_POST["subject"]);
		$text = safform($_POST["text"]);
		$date = time();
		$cansend = true;
		
		if(strlen($subject)<1){$cansend = false; echo "Вы не ввели тему сообщения";}
		if(strlen($text)<1){$cansend = false; echo "Вы не ввели текст сообщения";}
		
		if($cansend)
		{
			$mysql->sql_query("INSERT INTO messages VALUES (0,'".$udata["id_user"]."','".$_SESSION["id_user"]."','$subject','$text','$date',0)");
			echo "Ваше сообщение отправлено";
		}
	}
}

?>