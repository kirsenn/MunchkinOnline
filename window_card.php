<?php 
require_once("global.php");
require_once("chat.php");
require_once("modules/mysql.php");
require_once("control_rule.php");
?>

<?php
if(!isset($_SESSION)){session_start();}
if ( (isset($_SESSION['id_user'])) && (isset($_SESSION['init'])) && (isset($_SESSION['id_gt'])) ){
//****Карту отправляем в сброс****
            if ($_REQUEST['send_com']==0){  
                        if (($_REQUEST['from_object']>=20)&&($_REQUEST['from_object']<=29)) {
                            $DBLink=connectdb();
                            $query="SELECT * FROM cards_of_user JOIN cards ON (cards_of_user.id_card=cards.id_card) WHERE (id_user=".$_SESSION['id_user']." AND place_card=".$_REQUEST['from_object'].")";
                            $result=mysql_query($query);
                            if (mysql_num_rows($result)!=0){
                                $row=mysql_fetch_array($result);   
                                        
                                $query="SELECT MAX(num_d) AS num_d
                                        FROM discards               
                                        WHERE (id_gt=".$_SESSION['id_gt'].")";
                                $result=mysql_query($query);  
                                
                                $row1=mysql_fetch_array($result);
                                $_SESSION['num_d']=$row1['num_d']+1; 
                          
                                $query="INSERT INTO discards VALUES(NULL,".$_SESSION['num_d'].",".$row['id_card'].",".$_SESSION['id_gt'].")";
                                $result=mysql_query($query);  
                                  
                                $query="DELETE FROM cards_of_user WHERE (id_card=".$row['id_card']." AND id_user=".$_SESSION['id_user']." AND place_card=".$_REQUEST['from_object'].")";
                                $result=mysql_query($query);
                                
                                $per_str=' отправил карту из руки в сброс';
                                add_str($per_str,0);          
                              }   
                        mysql_close ($DBLink);     
                        }elseif (($_REQUEST['from_object']>=10)&&($_REQUEST['from_object']<=19)) {
                            $DBLink=connectdb();
                            $query="SELECT * FROM cards_of_table JOIN cards ON (cards_of_table.id_card=cards.id_card) WHERE (id_gt=".$_SESSION['id_gt']." AND place_card=".$_REQUEST['from_object'].")";
                            $result=mysql_query($query);
                            if (mysql_num_rows($result)!=0){
                                $row=mysql_fetch_array($result);   
                                        
                                $query="SELECT MAX(num_d) AS num_d
                                        FROM discards               
                                        WHERE (id_gt=".$_SESSION['id_gt'].")";
                                $result=mysql_query($query);  
                                
                                $row1=mysql_fetch_array($result);
                                $_SESSION['num_d']=$row1['num_d']+1; 
                          
                                $query="INSERT INTO discards VALUES(NULL,".$_SESSION['num_d'].",".$row['id_card'].",".$_SESSION['id_gt'].")";
                                $result=mysql_query($query);  
                                  
                                $query="DELETE FROM cards_of_table WHERE (id_card=".$row['id_card']." AND id_gt=".$_SESSION['id_gt']." AND place_card=".$_REQUEST['from_object'].")";
                                $result=mysql_query($query);
                                
                                $per_str=' отправил карту со стола в сброс';
                                add_str($per_str,0);           
                             }   
                        mysql_close ($DBLink);   
                        //Класс - отправляем в сброс  
                        }elseif (($_REQUEST['from_object']>=30)&&($_REQUEST['from_object']<=32)) {
                            $DBLink=connectdb();
                            $query="SELECT * FROM carried_items JOIN cards ON (carried_items.id_card=cards.id_card) WHERE (id_user=".$_SESSION['id_user']." AND place_card=".$_REQUEST['from_object'].")";
                            $result=mysql_query($query);
                            if (mysql_num_rows($result)!=0){
                                $row=mysql_fetch_array($result);   
                                if ($row['c_type']=="supermunch"){
                                      $query="SELECT MAX(num_d) AS num_d
                                              FROM discards               
                                              WHERE (id_gt=".$_SESSION['id_gt'].")";
                                      $result=mysql_query($query);  
                                      
                                      $row1=mysql_fetch_array($result);
                                      $_SESSION['num_d']=$row1['num_d']+1; 
                                
                                      $query="INSERT INTO discards VALUES(NULL,".$_SESSION['num_d'].",".$row['id_card'].",".$_SESSION['id_gt'].")";
                                      $result=mysql_query($query);  
                                        
                                      $query="DELETE FROM carried_items WHERE (id_card=".$row['id_card']." AND id_user=".$_SESSION['id_user']." AND place_card=".$_REQUEST['from_object'].")";
                                      $result=mysql_query($query);    
                                      
                                      $query="SELECT * FROM carried_items JOIN cards ON (carried_items.id_card=cards.id_card) WHERE (id_user=".$_SESSION['id_user']." AND place_card=32)";
                                      $result=mysql_query($query);  
                                      if (mysql_num_rows($result)!=0){
                                            $row=mysql_fetch_array($result);
                                            $_SESSION['num_d']=$_SESSION['num_d']+1; 
                                      
                                            $query="INSERT INTO discards VALUES(NULL,".$_SESSION['num_d'].",".$row['id_card'].",".$_SESSION['id_gt'].")";
                                            $result=mysql_query($query);  
                                              
                                            $query="DELETE FROM carried_items WHERE (id_card=".$row['id_card']." AND id_user=".$_SESSION['id_user']." AND place_card=32)";
                                            $result=mysql_query($query);                           
                                      }       
                                      $per_str=' отправил в сброс свой класс';
                                      add_str($per_str,0);                  
                                }else{       
                                      $query="SELECT MAX(num_d) AS num_d
                                              FROM discards               
                                              WHERE (id_gt=".$_SESSION['id_gt'].")";
                                      $result=mysql_query($query);  
                                      
                                      $row1=mysql_fetch_array($result);
                                      $_SESSION['num_d']=$row1['num_d']+1; 
                                
                                      $query="INSERT INTO discards VALUES(NULL,".$_SESSION['num_d'].",".$row['id_card'].",".$_SESSION['id_gt'].")";
                                      $result=mysql_query($query);  
                                        
                                      $query="DELETE FROM carried_items WHERE (id_card=".$row['id_card']." AND id_user=".$_SESSION['id_user']." AND place_card=".$_REQUEST['from_object'].")";
                                      $result=mysql_query($query); 
                                      $per_str=' отправил в сброс свой класс';
                                      add_str($per_str,0);                            
                                }
                             }   
                        mysql_close ($DBLink);     
                        //Расу - отправляем в сброс  
                        }elseif (($_REQUEST['from_object']>=40)&&($_REQUEST['from_object']<=42)) {
                            $DBLink=connectdb();
                            $query="SELECT * FROM carried_items JOIN cards ON (carried_items.id_card=cards.id_card) WHERE (id_user=".$_SESSION['id_user']." AND place_card=".$_REQUEST['from_object'].")";
                            $result=mysql_query($query);
                            if (mysql_num_rows($result)!=0){
                                $row=mysql_fetch_array($result);   
                                if ($row['c_type']=="halfbreed"){
                                      $query="SELECT MAX(num_d) AS num_d
                                              FROM discards               
                                              WHERE (id_gt=".$_SESSION['id_gt'].")";
                                      $result=mysql_query($query);  
                                      
                                      $row1=mysql_fetch_array($result);
                                      $_SESSION['num_d']=$row1['num_d']+1; 
                                
                                      $query="INSERT INTO discards VALUES(NULL,".$_SESSION['num_d'].",".$row['id_card'].",".$_SESSION['id_gt'].")";
                                      $result=mysql_query($query);  
                                        
                                      $query="DELETE FROM carried_items WHERE (id_card=".$row['id_card']." AND id_user=".$_SESSION['id_user']." AND place_card=".$_REQUEST['from_object'].")";
                                      $result=mysql_query($query);    
                                      
                                      $query="SELECT * FROM carried_items JOIN cards ON (carried_items.id_card=cards.id_card) WHERE (id_user=".$_SESSION['id_user']." AND place_card=42)";
                                      $result=mysql_query($query);  
                                      if (mysql_num_rows($result)!=0){
                                            $row=mysql_fetch_array($result);
                                            $_SESSION['num_d']=$_SESSION['num_d']+1; 
                                      
                                            $query="INSERT INTO discards VALUES(NULL,".$_SESSION['num_d'].",".$row['id_card'].",".$_SESSION['id_gt'].")";
                                            $result=mysql_query($query);  
                                              
                                            $query="DELETE FROM carried_items WHERE (id_card=".$row['id_card']." AND id_user=".$_SESSION['id_user']." AND place_card=42)";
                                            $result=mysql_query($query);                           
                                      }      
                                      $per_str=' отправил в сброс свою расу';
                                      add_str($per_str,0);                                            
                                }else{       
                                      $query="SELECT MAX(num_d) AS num_d
                                              FROM discards               
                                              WHERE (id_gt=".$_SESSION['id_gt'].")";
                                      $result=mysql_query($query);  
                                      
                                      $row1=mysql_fetch_array($result);
                                      $_SESSION['num_d']=$row1['num_d']+1; 
                                
                                      $query="INSERT INTO discards VALUES(NULL,".$_SESSION['num_d'].",".$row['id_card'].",".$_SESSION['id_gt'].")";
                                      $result=mysql_query($query);  
                                        
                                      $query="DELETE FROM carried_items WHERE (id_card=".$row['id_card']." AND id_user=".$_SESSION['id_user']." AND place_card=".$_REQUEST['from_object'].")";
                                      $result=mysql_query($query); 
                                      $per_str=' отправил в сброс свою расу';
                                      add_str($per_str,0);                         
                                }
                             }   
                        mysql_close ($DBLink);  
                        //Шмотку - отправляем в сброс     
                        }elseif (($_REQUEST['from_object']>=50)&&($_REQUEST['from_object']<=75)) {
                            $DBLink=connectdb();
                            $query="SELECT * FROM carried_items JOIN cards ON (carried_items.id_card=cards.id_card) WHERE (id_user=".$_SESSION['id_user']." AND place_card=".$_REQUEST['from_object'].")";
                            $result=mysql_query($query);
                            if (mysql_num_rows($result)!=0){
                                $row=mysql_fetch_array($result);        
                                $query="SELECT MAX(num_d) AS num_d
                                        FROM discards               
                                        WHERE (id_gt=".$_SESSION['id_gt'].")";
                                $result=mysql_query($query);  
                                
                                $row1=mysql_fetch_array($result);
                                $_SESSION['num_d']=$row1['num_d']+1; 
                          
                                $query="INSERT INTO discards VALUES(NULL,".$_SESSION['num_d'].",".$row['id_card'].",".$_SESSION['id_gt'].")";
                                $result=mysql_query($query);  
                                  
                                $query="DELETE FROM carried_items WHERE (id_card=".$row['id_card']." AND id_user=".$_SESSION['id_user']." AND place_card=".$_REQUEST['from_object'].")";
                                $result=mysql_query($query); 
                                
								if (($_REQUEST['from_object']>=50)&&($_REQUEST['from_object']<=69)) {
									$per_str=' отправил в сброс свою шмотку';
									add_str($per_str,0);  
								}elseif (($_REQUEST['from_object']>=70)&&($_REQUEST['from_object']<=75)) {
									$per_str=' отправил в сброс наложенное на него проклятие: [B]'.$row['c_name'].'[/B]';
									add_str($per_str,0);  								
								}
								
                             }   
                        mysql_close ($DBLink);     
                        }
//КОНЕЦ****Карту отправляем в сброс****
						
//****Карту устанавливаем как КЛАСС/РАСУ*****						
            }elseif ($_REQUEST['send_com']==1){
				apply_card($_REQUEST['from_object']);//Вызываем функцию контроля правил применения карт - распологается в control_rule.php
//КОНЕЦ ****Карту устанавливаем как КЛАСС/РАСУ*****

//****ПРОДАТЬ карту*****
	 		}elseif ($_REQUEST['send_com']==2){ 
					//Узнаем карты лежит в руке или в шмотках
					if ( (($_REQUEST['from_object']>=20)&&($_REQUEST['from_object']<=29)) || (($_REQUEST['from_object']>=50)&&($_REQUEST['from_object']<=69)) ) {
						sell_card($_REQUEST['from_object']);//Вызываем функцию контроля правил продажи карт - распологается в control_rule.php
					}
//КОНЕЦ ****ПРОДАТЬ карту*****				
			
//***Карту устанавливаем как шмотку***
            }elseif ($_REQUEST['send_com']==3){
                    if (($_REQUEST['from_object']>=20)&&($_REQUEST['from_object']<=29)) {
                        $DBLink=connectdb();
                        $query="SELECT * FROM cards_of_user JOIN cards ON (cards_of_user.id_card=cards.id_card) WHERE (id_user=".$_SESSION['id_user']." AND place_card=".$_REQUEST['from_object'].")";
                        $result=mysql_query($query);
                        if (mysql_num_rows($result)!=0){
                            $row=mysql_fetch_array($result); 
                            if (($row['c_type']=="smallitem") OR ($row['c_type']=="bigitem") OR ($row['c_type']=="chit") OR ($row['c_type']=="magic") ){                                                      
                                    $query="SELECT * FROM carried_items JOIN cards ON (carried_items.id_card=cards.id_card)
                                            WHERE (id_user=".$_SESSION['id_user']." AND place_card BETWEEN 60 AND 69) ORDER BY place_card";       
                                    $result=mysql_query($query);

									unset($select_place);
									if (mysql_num_rows($result)>0){
											  for ($i=60;$i<=69;$i++){
												  $row1=mysql_fetch_array($result);
												  if ($row1['place_card']!=$i){
													  $select_place=$i; 
													  break; 
												  }
											  }     
									}else{
										   $select_place=60;  
									}           
									
  
									if (isset($select_place)){	
										//Ложим карту в шмотки
										  $query="INSERT INTO carried_items VALUES(NULL,".$_SESSION['id_user'].",".$row['id_card'].",".$select_place.")";
										  $result=mysql_query($query);  
													  
										  $query="DELETE FROM cards_of_user WHERE (id_card=".$row['id_card']." AND id_user=".$_SESSION['id_user']." AND place_card=".$_REQUEST['from_object'].")";
										  $result=mysql_query($query); 
										  
										  $per_str=' бросил к себе в шмотки : [B]'.$row['c_name'].'[/B]';
										  add_str($per_str,0);  
										  
										  print "1";
									}else{
										//В шмотках нет свободного места
										  $per_str=' Нельзя отправить карту в неактивные шмотки там [B]НЕТ СВОБОДНОГО МЕСТА[/B]';
										  add_str($per_str,0);  
										  print "0";
                                    }									
                            }                    
                        }else{
                           print "0";
                        }   
                        
                        mysql_close ($DBLink);     
                    }elseif (($_REQUEST['from_object']>=10)&&($_REQUEST['from_object']<=19)) {
                        print "0";
                    }  
//КОНЕЦ ***Карту устанавливаем как шмотку***

//***Карту из Шмоток бросаем на стол (ну конечно если есть место)****
            }elseif ($_REQUEST['send_com']==4){
				if (($_REQUEST['from_object']>=50)&&($_REQUEST['from_object']<=69)) 
				{												
					$DBLink=connectdb();
					$query="SELECT * FROM carried_items JOIN cards ON (carried_items.id_card=cards.id_card) WHERE (id_user=".$_SESSION['id_user']." AND place_card=".$_REQUEST['from_object'].")";
					$result=mysql_query($query);
					if (mysql_num_rows($result)!=0){
						$row=mysql_fetch_array($result); 
						if (($row['c_type']=="smallitem") OR ($row['c_type']=="bigitem") OR ($row['c_type']=="chit") OR ($row['c_type']=="magic") ){                                                      
								$query="SELECT * FROM cards_of_table JOIN cards ON (cards_of_table.id_card=cards.id_card)
										WHERE (id_gt=".$_SESSION['id_gt'].") ORDER BY place_card";       
								$result=mysql_query($query);

								unset($select_place);
								if (mysql_num_rows($result)!=0){
										  for ($i=10;$i<=19;$i++){
											  $row1=mysql_fetch_array($result);
											  if ($row1['place_card']!=$i){
												  $select_place=$i; 
												  break; 
											  }
										  }     
								}else{
									   $select_place=10;  
								}           
								

								if (isset($select_place)){	
									//Ложим карту на стол
									  $query="INSERT INTO cards_of_table VALUES(NULL,".$_SESSION['id_gt'].",".$row['id_card'].",".$select_place.")";
									  $result=mysql_query($query);  
												  
									  $query="DELETE FROM carried_items WHERE (id_card=".$row['id_card']." AND id_user=".$_SESSION['id_user']." AND place_card=".$_REQUEST['from_object'].")";
									  $result=mysql_query($query); 
									  
									  $per_str=' выбросил свою одетую шмотку : [B]'.$row['c_name'].'[/B]  на стол';
									  add_str($per_str,0);  
									  
									  print "1";
								}else{
									//На столе нет свободного места
									  $per_str=' Нельзя выбросить шмотку на стол [B]НЕТ СВОБОДНОГО МЕСТА[/B]';
									  add_str($per_str,0);  
									  print "0";
								}									
						}                    
					}                        
					mysql_close ($DBLink);     
				}
//КОНЕЦ *** Карту из Шмоток бросаем на стол (ну конечно если есть место)***
			}elseif ($_REQUEST['send_com']==5)
			{
				kill_monster($_REQUEST['place_card']);			
//ОТКРЫТЬ КАРТУ           
			}elseif ($_REQUEST['send_com']==6)
			{
				open_window_card($_REQUEST['place_card']);	
            }  		  
//ОТКРЫТЬ КАРТУ 
}       
?>