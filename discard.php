<?php require_once("global.php");
require_once("chat.php");
require_once("control_rule.php");
?>

<?php
session_start();
if ( (isset($_SESSION['id_user'])) && (isset($_SESSION['init'])) && (isset($_SESSION['id_gt'])) ){
		//При открытии СБРОСА
        if ($_REQUEST['send_com']==0) {
            $DBLink=connectdb();
            $query="SELECT * FROM discards JOIN cards ON (discards.id_card=cards.id_card) WHERE (id_gt=".$_SESSION['id_gt'].")";
            $result=mysql_query($query);
            if (mysql_num_rows($result)==0){
                print 'В "сбросе" карты отсутствуют!';  
            }else{
                $query="SELECT MAX(num_d) AS num_d
                        FROM discards               
                        WHERE (id_gt=".$_SESSION['id_gt'].")";
                $result=mysql_query($query);  
                
                $row=mysql_fetch_array($result);   
                $_SESSION['num_d']=$row['num_d']; 
                         
                $query1="SELECT * FROM discards
                       JOIN cards ON (discards.id_card=cards.id_card) 
                       WHERE (id_gt=".$_SESSION['id_gt']." AND num_d=".$_SESSION['num_d'].")";
                $result1=mysql_query($query1);
                $row1=mysql_fetch_array($result1);
				
				if ($_SESSION['num_d']>0){
						$showelement='<img id="id_card31" class="obj_dump" src="./picture/'.$row1['pic'].'" value="31">';
						$showelement=$showelement."<div align=\"center\" id=\"button_vruku\">Взять в руку</div>";
						if ($_SESSION['num_d']>1){
							$showelement=$showelement."<div align=\"center\" id=\"button_down\">Вниз</div>";
						}
				}else{
					$showelement='В сбросе карты отсутствуют';
				}
				print $showelement;			        
             } 
              
             $per_str=' открыл и просматривает сброс.';
             add_str($per_str,0);            
            
             mysql_close ($DBLink);     
        }elseif ($_REQUEST['send_com']==1) {
			//При нажатии ВВЕРХ
            $DBLink=connectdb();
            $query="SELECT * FROM discards JOIN cards ON (discards.id_card=cards.id_card) WHERE (id_gt=".$_SESSION['id_gt'].")";
            $result=mysql_query($query);
            if (mysql_num_rows($result)==0){
              
            }else{       
                $query="SELECT MAX(num_d) AS num_d
                        FROM discards               
                        WHERE (id_gt=".$_SESSION['id_gt'].")";
                $result=mysql_query($query);  
                $row=mysql_fetch_array($result);  
                        
                if ($_SESSION['num_d']<$row['num_d']){                     
                    $_SESSION['num_d']=$_SESSION['num_d']+1;
                    $query1="SELECT * FROM discards
                            JOIN cards ON (discards.id_card=cards.id_card) 
                            WHERE (id_gt=".$_SESSION['id_gt']." AND num_d=".$_SESSION['num_d'].")";
                    $result1=mysql_query($query1);
                    $row1=mysql_fetch_array($result1);  
					//Если карты еще можно листать то рисуем все кнопки
					$showelement='<img id="id_card31" class="obj_dump" src="./picture/'.$row1['pic'].'" value="31">';
					$showelement=$showelement."<div align=\"center\" id=\"button_vruku\">Взять в руку</div>";
					if ($_SESSION['num_d']<$row['num_d']){ 
						$showelement=$showelement."<div align=\"center\" id=\"button_up\">Вверх</div>";
					}
					$showelement=$showelement."<div align=\"center\" id=\"button_down\">Вниз</div>";
					print $showelement;	
                }else{
                    $query="SELECT * FROM discards
                            JOIN cards ON (discards.id_card=cards.id_card) 
                            WHERE (id_gt=".$_SESSION['id_gt']." AND num_d=".$_SESSION['num_d'].")";
                    $result=mysql_query($query);
                    $row=mysql_fetch_array($result);  
					//Если карты больше листать некуда рисуем 1 кнопку
					$showelement='<img id="id_card31" class="obj_dump" src="./picture/'.$row['pic'].'" value="31">';
					$showelement=$showelement."<div align=\"center\" id=\"button_vruku\">Взять в руку</div>";
					$showelement=$showelement."<div align=\"center\" id=\"button_down\">Вниз</div>";
					print $showelement;							
                } 
                mysql_close ($DBLink);           
            }   
			//При нажатии ВНИЗ
        }elseif ($_REQUEST['send_com']==2) {
            $DBLink=connectdb();
            $query="SELECT * FROM discards JOIN cards ON (discards.id_card=cards.id_card) WHERE (id_gt=".$_SESSION['id_gt'].")";
            $result=mysql_query($query);
            if (mysql_num_rows($result)==0){
              
            }else{       
                $query="SELECT MAX(num_d) AS num_d
                        FROM discards               
                        WHERE (id_gt=".$_SESSION['id_gt'].")";
                $result=mysql_query($query);  
                $row=mysql_fetch_array($result);  
                
                if ($_SESSION['num_d']>1){                     
                    $_SESSION['num_d']=$_SESSION['num_d']-1;
                    $query1="SELECT * FROM discards
                            JOIN cards ON (discards.id_card=cards.id_card) 
                            WHERE (id_gt=".$_SESSION['id_gt']." AND num_d=".$_SESSION['num_d'].")";
                    $result1=mysql_query($query1);
                    $row1=mysql_fetch_array($result1);       
					
					//Если карты еще можно листать то рисуем все кнопки
					$showelement='<img id="id_card31" class="obj_dump" src="./picture/'.$row1['pic'].'" value="31">';
					$showelement=$showelement."<div align=\"center\" id=\"button_vruku\">Взять в руку</div>";
					$showelement=$showelement."<div align=\"center\" id=\"button_up\">Вверх</div>";
					if ($_SESSION['num_d']>1) {
						$showelement=$showelement."<div align=\"center\" id=\"button_down\">Вниз</div>";
					}
					print $showelement;	
                }else{
                    $query="SELECT * FROM discards
                            JOIN cards ON (discards.id_card=cards.id_card) 
                            WHERE (id_gt=".$_SESSION['id_gt']." AND num_d=".$_SESSION['num_d'].")";
                    $result=mysql_query($query);
                    $row=mysql_fetch_array($result);  
					//Если карты больше листать некуда рисуем 1 кнопку
					$showelement='<img id="id_card31" class="obj_dump" src="./picture/'.$row['pic'].'" value="31">';
					$showelement=$showelement."<div align=\"center\" id=\"button_vruku\">Взять в руку</div>";
					$showelement=$showelement."<div align=\"center\" id=\"button_up\">Вверх</div>";
					$showelement=$showelement.$_SESSION['num_d'];
					print $showelement;		
                } 
                mysql_close ($DBLink);           
            }   
			//При нажатии ВЗЯТЬ В РУКУ
        }elseif ($_REQUEST['send_com']==3) {  
              $DBLink=connectdb();         
              $query="SELECT * FROM cards_of_user WHERE (id_user=".$_SESSION['id_user'].") ORDER BY place_card";
              $result=mysql_query($query);
			  unset($select_place);
              if (mysql_num_rows($result)!=0){
                      for ($i=20;$i<=29;$i++){
                          $row1=mysql_fetch_array($result);
                          if ($row1['place_card']!=$i){
                              $select_place=$i; 
                              break; 
                          }
                      }     
              }else{
                   $select_place=20;  
              }               
         
              if (isset($select_place)){
                    $query="SELECT * FROM discards JOIN cards ON (discards.id_card=cards.id_card) WHERE (id_gt=".$_SESSION['id_gt'].")";
                    $result1=mysql_query($query);
                    if (mysql_num_rows($result1)!=0){           
                          $query="SELECT * FROM discards
                                  JOIN cards ON (discards.id_card=cards.id_card) 
                                  WHERE (id_gt=".$_SESSION['id_gt']." AND num_d=".$_SESSION['num_d'].")";
                          $result=mysql_query($query);
                          $row=mysql_fetch_array($result);           
                          
                          print '<img id="id_card'.$select_place.'" class="id_card" src="./picture/'.$row['pic'].'" value="'.$select_place.'">';  
                        
                          $query="INSERT INTO cards_of_user VALUES(NULL,".$_SESSION['id_user'].",".$row['id_card'].",".$select_place.")";
                          $result=mysql_query($query);    
                          $query="DELETE FROM discards WHERE (id_gt=".$_SESSION['id_gt']." AND id_card=".$row['id_card'].")";         
                          $result=mysql_query($query);  
                          
                          $query="SELECT MAX(num_d) AS num_d
                                FROM discards               
                                WHERE (id_gt=".$_SESSION['id_gt'].")";
                          $result=mysql_query($query);  
                          $row1=mysql_fetch_array($result);   
                          
                          if (!is_numeric($row1['num_d'])){
                             $_SESSION['num_d']=0;      							 
                          }
                          else {               
						    if ($_SESSION['num_d']!=$row1['num_d']+1){
								$query="UPDATE discards SET num_d=".$_SESSION['num_d']." 
										WHERE (id_gt=".$_SESSION['id_gt']." AND num_d=".$row1['num_d'].")";
								$result=mysql_query($query);   
							}
							$per_str=' взял карту из сброса: [B]'.$row['c_name'].'[/B]';
							add_str($per_str,0);
                          }
						
						change_phase_move(2);//Изменяем фазу хода на 1  
                    } 
        
                                    
              }
              else{
                  //рука занята ничего не делаем
					$per_str='У вас нету [B]свободного места[/B] в руках,чтобы взять карту из сброса [/B]';
					add_str($per_str,1); 				  
              }
              mysql_close ($DBLink);             
        }  
}         
?>