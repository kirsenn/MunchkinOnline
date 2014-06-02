<?php
$starttime = microtime(true);
/*
------------------------------------------------
Узнаем браузер и ОС посетителя
------------------------------------------------
*/
class sysinfo  //Класс информации о пользователе
{
public $browser = ""; //Тип и версия броузера
public $browserver = "";
public $ostype = "N/A"; // Тип и версия ОС
public $osver = "";

function getbrowser($regex) //Получаем тип и версию браузера
	{
		if(stristr($regex,"Opera")) // В случае если стоит Опера
		{
			$this->browser = "Opera";
			if(!strstr($regex,"Version/")){$this->browserver = preg_replace("|Opera/(.*?) .+|i","$1",$regex);} // Для версий Опера < 10
			else{$this->browserver = preg_replace("|.+ Version/(.*?)|i","$1",$regex);}
		}
		#Opera/9.20 (Windows NT 5.1; U; MRA 5.5 (build 02842); ru)
		#Opera/9.80 (Windows NT 6.1; U; ru) Presto/2.2.15 Version/10.10
		
		if(stristr($regex,"Opera Mini")) // В случае если стоит Опера Mini
		{
			$this->browser = "Opera Mini";
			$this->browserver = preg_replace("|.+ Opera Mini/(.*?)/.+|i","$1",$regex);
		}		
		#Opera/9.80 (J2ME/MIDP; Opera Mini/5.0.17443/958; U; en) Presto/2.4.15
		
		if(stristr($regex,"msie"))  //Если стоит Осел
		{
			$this->browser = "Microsoft Internet Explorer";
			$this->browserver = preg_replace("|.+msie (.*?);.+|i","$1",$regex);
		}
		#Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; Trident/4.0; MRA 5.5 (build 02842); SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.3
		
		if(stristr($regex,"firefox"))  //Если стоит Файрфокс
		{
			$this->browser = "Mozilla Firefox";
			$this->browserver = preg_replace("|.+firefox/(.*?){0,}|i","$1",$regex);
		}
		#Mozilla/5.0 (X11; U; Linux i686; ru; rv:1.9.1.9) Gecko/20100401 Ubuntu/9.10 (karmic) Firefox/3.5.9
		
		if(stristr($regex,"konqueror"))  //Если стоит Конкуерор
		{
			$this->browser = "Konqueror";
			$this->browserver = preg_replace("|.+Konqueror/(.*?);.+|i","$1",$regex);
		}
		
		if(stristr($regex,"safari"))  //Если стоит Safari
		{
			$this->browser = "Safari";
			$this->browserver = preg_replace("|.+ Version/(.*?) .+|i","$1",$regex);
		}
		#Mozilla/5.0 (Windows; U; Windows NT 6.1; ru-RU) AppleWebKit/531.9 (KHTML, like Gecko) Version/4.0.3 Safari/531.9.1
		
		if(stristr($regex,"chrome"))  //Если стоит Google Chrome
		{
			$this->browser = "Google Chrome";
			$this->browserver = preg_replace("|.+ Chrome/(.*?) .+|i","$1",$regex);
		}
		if(stristr($regex,"chromium"))  //Если стоит Google Chromium
		{
			$this->browser = "Google Chromium";
			$this->browserver = preg_replace("|.+ Chromium/(.*?) .+|i","$1",$regex);
		}
		
		if(stristr($regex,"lynx"))  //Если стоит Lynx
		{
			$this->browser = "Lynx";
			$this->browserver = preg_replace("|^lynx/(.*?) .+|i","$1",$regex);
		}
		if(stristr($regex,"yandex")) //Это бездушная скотина, поисковый робот приполз
		{
			$this->ostype = "<a target=\"_blank\" href=\"http://www.yandex.ru\">Yandex Bot</a>";
			$this->browser = "Yandex Bot";
			if(stristr($regex,"YandexMetrika"))
			{
				$this->ostype = "<a target=\"_blank\" href=\"http://metrika.yandex.ru\">Yandex Metrika</a>";
				$this->browser = "Yandex Bot Metrika";
			}
			if(stristr($regex,"Direct")) //Это бездушная скотина, поисковый робот приполз
			{
				$this->ostype = "<a target=\"_blank\" href=\"http://direct.yandex.ru\">Yandex Direct</a>";
				$this->browser = "Yandex Direct Bot";
			}
		}
		
		if(stristr($regex,"Nigma.ru")) //Это бездушная скотина, поисковый робот приполз
		{
			$this->ostype = "<a target=\"_blank\" href=\"http://www.nigma.ru\">Nigma Bot</a>";
			$this->browser = "Nigma Bot";
		}
		
		if(stristr($regex,"AportWorm")) //Это бездушная скотина, поисковый робот приполз
		{
			$this->ostype = "<a target=\"_blank\" href=\"http://www.aport.ru\">Aport Bot</a>";
			$this->browser = "Aport Bot";
		}
		if(stristr($regex,"google")) //Это бездушная скотина, поисковый робот приполз
		{
			$this->ostype = "<a target=\"_blank\" href=\"http://www.google.com\">Google Bot</a>";
			$this->browser = "Google Bot";
		}
		
		if(stristr($regex,"yahoo")) //Это бездушная скотина, поисковый робот приполз
		{
			$this->ostype = "<a target=\"_blank\" href=\"http://www.yahoo.com\">Yahoo Bot</a>";
			$this->browser = "Yahoo Bot";
		}
		if(stristr($regex,"twiceler")) //Это бездушная скотина, поисковый робот приполз
		{
			$this->ostype = "<a target=\"_blank\" href=\"http://www.cuil.com\">Cuil Bot</a>";
			$this->browser = "Cuil Bot";
		}
		if(stristr($regex,"ovalebot")) //Это бездушная скотина, поисковый робот приполз
		{
			$this->ostype = "<a target=\"_blank\" href=\"http://www.ovale.ru\">Ovale Bot</a>";
			$this->browser = "Ovale Bot";
		}
		if(stristr($regex,"DotBot")) //Это бездушная скотина, поисковый робот приполз
		{
			$this->ostype = "<a target=\"_blank\" href=\"http://www.dotnetdotcom.org\">Dot Bot</a>";
			$this->browser = "Dot Bot";
		}
		if(stristr($regex,"Nokia"))
		{
			$this->ostype = "<a target=\"_blank\" href=\"http://www.nokia.org\">Nokia</a>";
			$this->browser = "Nokia Phone";
		}
		if(stristr($regex,"NetFront")) //Sony Ericsson
		{
			$this->browser = "SE NetFront";
			$this->browserver = preg_replace("|.+NetFront/(.*?) .+|i","$1",$regex);
		}
		#SonyEricssonK810i/R8BA Browser/NetFront/3.3 Profile/MIDP-2.0 Configuration/CLDC-1.1
		
		if(stristr($regex,"alexa")) //Это бездушная скотина, поисковый робот приполз
		{
			$this->ostype = "<a target=\"_blank\" href=\"http://www.alexa.com\">Alexa Bot</a>";
			$this->browser = "Alexa Bot";
		}
		if(stristr($regex,"WebAlta")) //Это бездушная скотина, поисковый робот приполз
		{
			$this->ostype = "<a target=\"_blank\" href=\"http://www.webalta.com\">WebAlta Bot</a>";
			$this->browser = "WebAlta Bot";
		}
		if(stristr($regex,"Protocol Discovery"))
		{
			$this->ostype = "<a target=\"_blank\" href=\"http://www.microsoft.com\">Microsoft Office</a>";
			$this->browser = "Microsoft Office";
		}
		if(stristr($regex,"Tagoo"))
		{
			$this->ostype = "<a target=\"_blank\" href=\"http://www.tagoo.ru\">Tagoo Bot</a>";
			$this->browser = "Tagoo Bot";
		}
		
		if(stristr($regex,"StackRambler"))
		{
			$this->browser = "Rambler Bot";
			$this->ostype = "<a target=\"_blank\" href=\"http://www.rambler.ru\">Rambler Bot</a>";
			$this->browserver = "";	
		}
		if(stristr($regex,"Sosospider")) //Это бездушная скотина, поисковый робот приполз
		{
			$this->ostype = "<a target=\"_blank\" href=\"http://help.soso.com/webspider.htm\">Soso Bot</a>";
			$this->browser = "Soso Bot";
		}
		if(stristr($regex,"Ask")) //Это бездушная скотина, поисковый робот приполз
		{
			$this->ostype = "<a target=\"_blank\" href=\"http://about.ask.com/en/docs/about/webmasters.shtml\">Ask Bot</a>";
			$this->browser = "Ask Bot";
		}
		if(stristr($regex,"facebook")) //Это бездушная скотина, поисковый робот приполз
		{
			$this->ostype = "<a target=\"_blank\" href=\"http://facebook.com\">Facebook Bot</a>";
			$this->browser = "Facebook Bot";
		}
		
		if($this->browser == "N/A"){$this->browser = $regex;}
		if($this->browser == ""){$this->browser = $regex;}
	}
	
function getos($regex)  //Определяем тип и версию ОС (пытаемся)
	{
		if(stristr($regex,"Win")) //В случае Винды
		{
			$this->ostype = "<a target=\"_blank\" href=\"http://www.microsoft.com\">Windows</a>";
			$this->osver = preg_replace("|.+Windows (.*?);.+|","$1",$regex);
			
			switch($this->osver) //Надо отобразить вместо номера версии - имя винды
			{
				case "NT 4.0": $this->osver = "NT"; break;
				case "NT 5.0": $this->osver = "2000"; break;
				case "NT 5.1": $this->osver = "XP"; break;
				case "NT 5.2": $this->osver = "2003"; break;
				case "NT 6.0": $this->osver = "Vista"; break;
				case "NT 6.1": $this->osver = "Se7en"; break;
				default: $this->osver = ""; break;
			}
		}
		if(stristr($regex,"Linux")) //Это линуксоид пришел
		{
			$this->ostype = "Linux";
			$this->osver = "";
			if(stristr($regex,"Ubuntu")) //Ubuntu
			{
				$this->ostype = "Linux Ubuntu";
				$this->osver = preg_replace("|.+ Ubuntu/(.*?) .+|i","$1",$regex);
			}
		}
		if(stristr($regex,"freebsd")) //Это пришел с бздей
		{
			$this->ostype = "FreeBSD";
			$this->osver = "";
		}
		if(stristr($regex,"openbsd")) //То же бздя, только оупен
		{
			$this->ostype = "OpenBSD";
			$this->osver = "";
		}		
		if(stristr($regex,"mac os x")) //Яблочники
		{
			$this->ostype = "Mac OS X";
			$this->osver = "";
		}
		if(stristr($regex,"Symbian OS")) //Symbian
		{
			$this->ostype = "Symbian OS";
			$this->osver = "";
		}
		if(stristr($regex,"SonyEricsson")) //Sony Ericsson
		{
			$this->ostype = "Sony Ericsson";
			$this->osver = preg_replace("|^SonyEricsson(.*?)/\w.+|i","$1",$regex);
		}
		
		if(stristr($regex,"Android")) //Android
		{
			$this->ostype = "<a target=\"_blank\" href=\"http://www.android.com\">Android</a>";
			if($this->browser!=="Opera Mini")
			{
				$this->osver = preg_replace("|.+ Android (.*?); .+|i","$1",$regex);
				$this->browser = "Android Standart";
				$this->browserver = $this->osver;
			}
			else
			{
				$this->osver = "";
			}
		}
		#Mozilla/5.0 (Linux; U; Android 2.1-update1; ru-ru; LG-GT540 Build/ERE27) AppleWebKit/530.17 (KHTML, like Gecko) Version/4.0 Mobi Linux
		#Opera/9.80 (Android; Opera Mini/5.1.21126/20.2497; U; ru) Presto/2.5.25
	}
}


if(isset($_GET['debug']))
{
	echo "Debug mode On<br/><br/>";
	$s = new sysinfo;
	$regex = $_SERVER['HTTP_USER_AGENT'];
	if(isset($_POST['debugua']))
	{
		$regex = $_POST['debugua'];
	}
	$s->getbrowser($regex);
	$s->getos($regex);

	echo "User-agent = ".$regex."<br/><br/>";

	echo "OS type: <b>".$s->ostype."</b> ver: <b>".$s->osver."</b><br/>";
	echo "BROWSER type: <b>".$s->browser."</b> ver: <b>".$s->browserver."</b><br/>";

	?>
	<p>
	<form method="post">
		Enter your UA: <textarea name="debugua" cols="50"><?php echo $_POST['debugua'] ?></textarea><br/>
	</form>
	</p>
	<?php
	$endtime = microtime(true);
	printf("<br/>Compiling time: %.2f ms",($endtime-$starttime)*1000);
}
?>
