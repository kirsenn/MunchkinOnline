<?	 
session_start();
 If(!isset($_SESSION['regcode']) OR !isset($_SESSION['formula']))
 {
	srand();
	$fid = mt_rand(1,20);
	switch($fid)
	{
		case 1: $formula="2+2=?"; $text = "4"; break;
		case 2: $formula="3+3=?"; $text = "6"; break;
		case 3: $formula="3+2=?"; $text = "5"; break;
		case 4: $formula="1+2=?"; $text = "3"; break;
		case 5: $formula="5+5=?"; $text = "10"; break;
		case 6: $formula="8-3=?"; $text = "5"; break;
		case 7: $formula="1+4=?"; $text = "5"; break;
		case 8: $formula="9-1=?"; $text = "8"; break;
		case 9: $formula="4-1=?"; $text = "3"; break;
		case 10: $formula="3-2=?"; $text = "1"; break;
		case 11: $formula="7+7=?"; $text = "14"; break;
		case 12: $formula="7+1=?"; $text = "8"; break;
		case 13: $formula="6+1=?"; $text = "7"; break;
		case 14: $formula="5-2=?"; $text = "3"; break;
		case 15: $formula="3*3=?"; $text = "9"; break;
		case 16: $formula="5*5=?"; $text = "25"; break;
		case 17: $formula="4*4=?"; $text = "16"; break;
		case 18: $formula="8-4=?"; $text = "4"; break;
		case 19: $formula="5+2=?"; $text = "7"; break;
		case 20: $formula="1+7=?"; $text = "8"; break;
	}
	$_SESSION['regcode']=$text;
	$_SESSION['formula']=$formula;
}
 
 $font = "./font/TESLDOC.TTF";
 
 $img = imagecreate(80,20);
 $white = imagecolorallocate($img,0xFF,0xCC,0x99);
 $color = imagecolorallocate($img,0x5F,0x4C,0x39);
 imagecolortransparent($img,$white);
 
 imagettftext($img, 14, 0, 4, 17, $color, $font,$_SESSION['formula']);
 header("Content-Type: image/png");
 imagejpeg($img,'',90);
?>