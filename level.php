<?php 
session_start();
require_once("global.php");
require_once("chat.php");
require_once("my_function.php");
?>

<?php
if ( (isset($_SESSION['id_user'])) && (isset($_SESSION['init'])) && (isset($_SESSION['id_gt'])) ){
        if ($_REQUEST['send_com']==0) {
            $DBLink=connectdb(); 
            if ($_REQUEST['id_user']==$_SESSION['id_user']){  
				//За 1 ход игрок может поднять не более 2 уровней. Делаем проверку сколько уроней поднял игрок
				if (!isset($_SESSION['control_level']))
				{
					$_SESSION['control_level']=0;
				}
				
                $query="SELECT * FROM users 
                        WHERE (id_user=".$_REQUEST['id_user'].")";       
                $result=mysql_query($query);
                $row=mysql_fetch_array($result);
				//Выводим элементы формы
				$str_content='<table width="100%" align="center"><tr><td align="center" colspan="2"><div id="blok_info_level">Ваш уровень:<b> '.$row['u_level'].'</b></div></td></tr>';

				//Выводим кнопки
				$visible_plus="visible";
				$visible_minus="visible";
				if ($row['u_level']==10){$visible_plus="hidden";}
				if ($row['u_level']==1) {$visible_minus="hidden";} 
				$str_content.='<tr><td align="right"><span id="u_level_plus" style="visibility:$visible_plus"></span></td>' ;
				$str_content.='<td><span align="left" id="u_level_minus" style="visibility:$visible_minus"></span></td></tr></table>' ;
            }else{
                $query="SELECT * FROM users 
                        WHERE (id_user=".$_REQUEST['id_user'].")";       
                $result=mysql_query($query);
                $row=mysql_fetch_array($result);
				//Выводим элементы формы
				$str_content='<div id="blok_info_level">';
                $str_content.='<center>Имя: '.$row['login'].'<br>';
                $str_content.='Уровень: '.$row['u_level'].'</center>';
				$str_content.='</div>';
            } 
			
			$json_data['content_window']=$str_content;
			echo json_encode($json_data);				
            mysql_close ($DBLink); 
			
        }elseif ($_REQUEST['send_com']==1) 
		{//УМЕНЬШАЕМ уровень на 1
            $DBLink=connectdb(); 
            if ($_REQUEST['id_user']==$_SESSION['id_user']){  
                $query="SELECT * FROM users 
                        WHERE (id_user=".$_SESSION['id_user'].")";       
                $result=mysql_query($query);
                $row=mysql_fetch_array($result);
				
				//Выводим элементы формы
                if ($row['u_level']>1){
                    $u_level=$row['u_level']-1;
                    $query="UPDATE users SET u_level=".$u_level." 
                            WHERE (id_user=".$_SESSION['id_user'].")";
                    $result=mysql_query($query);   
                    $per_str=' изменил уровень  [B]'.($u_level+1).'[/B] на [B]'.$u_level.'[/B] ';
                    add_str($per_str,0);   
					//Выводим элементы формы
					$str_content.='Ваш уровень:<b> '.$u_level.'</b>';		
					$_SESSION['control_level']=$_SESSION['control_level']-1;
                }else{
                    $u_level=1; 
					//Выводим элементы формы
					$str_content.='Ваш уровень:<b> '.$row['u_level'].'</b>';	
                }   
				
				//Выводим кнопки
				$visible_plus="visible";
				$visible_minus="visible";
				if ($u_level==10){$visible_plus="hidden";}
				if ($u_level==1) {$visible_minus="hidden";} 

            }	
			$json_data['content_window']=$str_content;
			$json_data['visible_plus']=$visible_plus;
			$json_data['visible_minus']=$visible_minus;
			echo json_encode($json_data);	
			
            mysql_close ($DBLink);          
        }elseif ($_REQUEST['send_com']==2) {
            $DBLink=connectdb(); 
            if ($_REQUEST['id_user']==$_SESSION['id_user']){  
                $query="SELECT * FROM users 
                        WHERE (id_user=".$_SESSION['id_user'].")";       
                $result=mysql_query($query);
                $row=mysql_fetch_array($result);
				
				if ( ($_SESSION['control_level'])<3 )//НУЖНО ПОСТАВИТЬ 2
				{//если игрок не поднял свой уровень вручную более чем на два
					//Выводим элементы формы
					if ($row['u_level']<10){
						$u_level=$row['u_level']+1;
						$query="UPDATE users SET u_level=".$u_level." 
								WHERE (id_user=".$_SESSION['id_user'].")";
						$result=mysql_query($query);  
						$per_str=' изменил уровень  [B]'.($u_level-1).'[/B] на [B]'.$u_level.'[/B] ';
						add_str($per_str,0);
						//Выводим элементы формы
						$str_content.='Ваш уровень:<b> '.$u_level.'</b>';
						//Устанавливаем в переменной количество уровней на которые игрок поднялся
						$_SESSION['control_level']=$_SESSION['control_level']+1;
					}else{
						$u_level=10;
						//Выводим элементы формы
						$str_content.='Ваш уровень:<b> '.$row['u_level'].'</b>';
					}
					//Выводим кнопки
					$visible_plus="visible";
				}else
				{
					$str_content.='Ваш уровень:<b> '.$row['u_level'].'</b>';
					$str_content.='<center><font color=red>Вы не можете в ручном режиме подняться за один ход выше чем на 3 уровня</font></center>';
					$visible_plus="hidden";
				}

				//Выводим кнопки			
				$visible_minus="visible";
				if ($u_level==10){$visible_plus="hidden";}
				if ($u_level==1) {$visible_minus="hidden";} 
            }	
			$json_data['content_window']=$str_content;
			$json_data['visible_plus']=$visible_plus;
			$json_data['visible_minus']=$visible_minus;
			echo json_encode($json_data);
            mysql_close ($DBLink);
        }  
}                  
?>