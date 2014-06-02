<?php
class Image
{
var $width;
var $height;

var $max_w = 4000;
var $max_h = 4000;
var $max_s = 4194304;
var $allowed_types = array(0=>"image/png",1=>"image/gif",2=>"image/jpeg",3=>"image/pjpeg");

	//Загрузка картины на сервер
	function upload($img_descriptor,$uploaddir,$chname,$reqname)
	{
		$canupload = true;
		$errormsg = "";		
				
		$name = $img_descriptor['name'];
		$tmpname = $img_descriptor['tmp_name'];
		if($chname){$newname = $this->imagesetname($name,$img_descriptor['type']);}else{$newname=$reqname;}
		if(!is_dir($uploaddir)){mkdir($uploaddir);}
		
		if(strlen($name)<1)
		{
			$errormsg.="Не задан рисунок!\\n";
			$canupload = false;
		}

		if($img_descriptor['size']>$this->max_s)
		{
			$errormsg.="Превышен максимальный размер ".$this->max_s." байт\\n";
			$canupload = false;
		}
		
		
		if(!in_array($img_descriptor['type'],$this->allowed_types))
		{
			$errormsg.="Разрешается загрузка только ";
			foreach($this->allowed_types as $v)
			{
				$errormsg.= $this->getimgtype($v)." ";
			}
			$errormsg.="\\nВы пытаетесь загрузить ".$img_descriptor['type'];
			$canupload = false;
		}

		if($canupload)
		{
			$resolution = getimagesize($tmpname);
			If($resolution[0]>$this->max_w or $resolution[1]>$this->max_h)
			{
				$canupload = false; 
				$errormsg.= "Максимальное разрешение картинки - $this->max_w х $this->max_h\\n";
			}
		}

		if($canupload)
		{			
			if(move_uploaded_file($tmpname,$uploaddir.$newname))
			{
				return true;
			}
			else
			{
				$canupload = false;
				$errormsg.= "Произошла неизвестная ошибка, файлы не загружены!\\n";
			}			
		}
		if(!$canupload)
		{
			echo "<script>alert('$errormsg\\n')</script>";
			return false;
		}	
	}
	
	function imagesetname($name,$type)
	{
		switch($type)
    		{
				case 'image/gif': $ext = '.gif'; break;
				case 'image/jpeg': $ext = '.jpg'; break;
				case 'image/pjpeg': $ext = '.jpg'; break;
				case 'image/png': $ext = '.png'; break;
			}
		$name = md5($name);
		return $name.$ext;
	}
	
	function getext($type)
	{
		switch($type)
    		{
				case 'image/gif': $ext = '.gif'; break;
				case 'image/jpeg': $ext = '.jpg'; break;
				case 'image/pjpeg': $ext = '.jpg'; break;
				case 'image/png': $ext = '.png'; break;
				case 'image/bmp': $ext = '.bmp'; break;
			}
		if(!$ext)
		{
			return false;
		}
		else
		{
			return $ext;
		}
	}
	
	function getimgtype($type)
	{
		sscanf($type,"image/%s",$ntype);
		return $ntype;
	}

	//Сохранение превьюшки
	function mk_preview($imgpath,$imgname)
	{
		$img = imagecreatefromjpeg($imgpath.$imgname);
		if(!$img)
		{
			return false;
		}
		$newsize = $this->getnewsize($img);
		$newimg = imagecreateTrueColor($newsize["nX"],$newsize["nY"]);
		
		ImageCopyResampled($newimg,$img,0,0,0,0,$newsize["nX"],$newsize["nY"],$newsize["fX"],$newsize["fY"]);
		$chkopn = @opendir($imgpath);
		if(!$chkopn){mkdir($imgpath);}
		imagejpeg($newimg,$imgpath.$imgname);
	}
	
	//Определение размеров превьюшки чтобы сохранились пропорции
	function getnewsize($img)
	{
		//Получим размер исходного
		$X = imageSX($img);
		$Y = imageSY($img);
		
		//Максимальный размер по одной из сторон
		$nX = 150;
		$nY = 150;
		
		//Определим какая сторона больше
		if($X>$Y)
		{
			$kX = $X/$nX; //Коэффициент уменьшения
			$rX = $nX; //Здесь принимаем размер по значению $nX
			$rY = $Y/$kX; //Заделим другую сторону на коэффициент и получим её размер =))
		}
		else //здесь  то же самое только с Y
		{
			$kY = $Y/$nY;
			$rY = $nY;
			$rX = $X/$kY;
		}
		//Возвращаем массив исходных и новых размеров
		return array("fX" => $X, "fY" => $Y, "nX" => $rX, "nY" => $rY);
	}
}
