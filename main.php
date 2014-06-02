<?php
session_start();
require_once("global.php");
require_once("chat.php");
require_once("modules/mysql.php");
require_once("control_rule.php");

$DBLink=connectdb();


if ( (isset($_SESSION['id_user'])) && (isset($_SESSION['init'])) && (isset($_SESSION['id_gt'])) ){

	//Обновляем активность стола
	$mysql->sql_query("UPDATE game_tables SET timestamp = ".time()." WHERE id_gt=".$_SESSION['id_gt']."");

	//Карту взяли из колоды дверей
	if ($_REQUEST['from_object']==1)
	{
		//Карту пытаемся положить на игровой стол
		if (($_REQUEST['to_place']>=10)&&($_REQUEST['to_place']<=19)) {
			//1 ПРоверка правил может ли в данный момент игрок взять и положить дверь из колоды на игровой стол
			$flag_control_rule=change_phase_move(1);//Изменяем фазу хода на 1  					
			if ($flag_control_rule==1)
			{	
				//Проверяем есть свободное место на игровом столе
				$query="SELECT * FROM cards_of_table JOIN cards ON (cards_of_table.id_card=cards.id_card) WHERE (id_gt=".$_SESSION['id_gt']." AND place_card=".$_REQUEST['to_place'].")";
				$result=mysql_query($query);
				if (mysql_num_rows($result)==0){    
						
					$query="SELECT * FROM cards_of_door JOIN cards ON (cards_of_door.id_card=cards.id_card) WHERE (id_gt=".$_SESSION['id_gt'].")";
					$result=mysql_query($query);       
					if (mysql_num_rows($result)!=0){
							$row=mysql_fetch_array($result);      
							$query="DELETE FROM cards_of_door WHERE (id_gt=".$_SESSION['id_gt']." AND id_card=".$row['id_card'].")";
							$result=mysql_query($query);   
							
							$query="INSERT INTO cards_of_table VALUES(NULL,".$_SESSION['id_gt'].",".$row['id_card'].",".$_REQUEST['to_place'].")";
							$result=mysql_query($query);  
							
								
							
							echo '<img id="id_card'.$_REQUEST['to_place'].'" class="id_card" src="./picture/'.$row['pic'].'" value="'.$_REQUEST['to_place'].'">';
							
							$per_str=' открыл дверь, а там : [B]'.$row['c_name'].'.[/B] И выложил дверь на стол.';
							add_str($per_str,0);            
					}else{
						$per_str=' в колоде дверей закончились карты, перетасуйте карты из сброса(кликните на колоду дверей и нажвите кнопку "Перетасовать") и продолжайте играть!';
						add_str($per_str,0);    					
					}  
				}  
			}   
		}
		//Карту пытаемся положить в руки
		elseif (($_REQUEST['to_place']>=20)&&($_REQUEST['to_place']<=29)) 
		{
			$flag_control_rule=control_phase_move(2);//игрок берет карту двери в темную(ложит в руку), проверка может ли он это делать
			if ($flag_control_rule==1)//Если игрок уже сразился то брать карту дверерй втемную ему запрещено
			{	
				$query="SELECT * FROM cards_of_user JOIN cards ON (cards_of_user.id_card=cards.id_card) WHERE (id_user=".$_SESSION['id_user']." AND place_card=".$_REQUEST['to_place'].")";
				$result=mysql_query($query);
				if (mysql_num_rows($result)==0)
				{          
					$query="SELECT * FROM cards_of_door JOIN cards ON (cards_of_door.id_card=cards.id_card) WHERE (id_gt=".$_SESSION['id_gt'].")";
					$result=mysql_query($query);       
					if (mysql_num_rows($result)!=0)
					{
							$row=mysql_fetch_array($result);      
							$query="DELETE FROM cards_of_door WHERE (id_gt=".$_SESSION['id_gt']." AND id_card=".$row['id_card'].")";
							$result=mysql_query($query);   
							
							$query="INSERT INTO cards_of_user VALUES(NULL,".$_SESSION['id_user'].",".$row['id_card'].",".$_REQUEST['to_place'].")";
							$result=mysql_query($query);  
							  
							echo '<img id="id_card'.$_REQUEST['to_place'].'" class="id_card" src="./picture/'.$row['pic'].'" value="'.$_REQUEST['to_place'].'">';
							$per_str=' взял дверь в руки';
							add_str($per_str,0);   
					}  
				}  
			}		
		}

	//Карту взяли из колоды сокровищ			
	}elseif ($_REQUEST['from_object']==2)
	{
		$DBLink=connectdb();
		//Карту пытаемся положить на игровой стол
		if (($_REQUEST['to_place']>=10)&&($_REQUEST['to_place']<=19)) {
			$query="SELECT * FROM cards_of_table JOIN cards ON (cards_of_table.id_card=cards.id_card) WHERE (id_gt=".$_SESSION['id_gt']." AND place_card=".$_REQUEST['to_place'].")";
			$result=mysql_query($query);
			if (mysql_num_rows($result)==0){          
				$query="SELECT * FROM cards_of_loot JOIN cards ON (cards_of_loot.id_card=cards.id_card) WHERE (id_gt=".$_SESSION['id_gt'].")";
				$result=mysql_query($query);       
				if (mysql_num_rows($result)!=0){
						$row=mysql_fetch_array($result);      
						$query="DELETE FROM cards_of_loot WHERE (id_gt=".$_SESSION['id_gt']." AND id_card=".$row['id_card'].")";
						$result=mysql_query($query);   
						
						$query="INSERT INTO cards_of_table VALUES(NULL,".$_SESSION['id_gt'].",".$row['id_card'].",".$_REQUEST['to_place'].")";
						$result=mysql_query($query);  
						  
						echo '<img id="id_card'.$_REQUEST['to_place'].'" class="id_card" src="./picture/'.$row['pic'].'" value="'.$_REQUEST['to_place'].'">';
						$per_str=' открыл сокровище, а там : [B]'.$row['c_name'].'.[/B] И выложил сокровище на стол.';
						add_str($per_str,0);                 
				}else{
						$per_str=' в колоде сокровищ закончились карты, перетасуйте карты из сброса(кликните на колоду сокровищ и нажвите кнопку "Перетасовать") и продолжайте играть!';
						add_str($per_str,0);    					
				}  
			}     
		}
		//Карту пытаемся положить в руки
		elseif (($_REQUEST['to_place']>=20)&&($_REQUEST['to_place']<=29)) {
			$query="SELECT * FROM cards_of_user JOIN cards ON (cards_of_user.id_card=cards.id_card) WHERE (id_user=".$_SESSION['id_user']." AND place_card=".$_REQUEST['to_place'].")";
			$result=mysql_query($query);
			if (mysql_num_rows($result)==0){          
				$query="SELECT * FROM cards_of_loot JOIN cards ON (cards_of_loot.id_card=cards.id_card) WHERE (id_gt=".$_SESSION['id_gt'].")";
				$result=mysql_query($query);       
				if (mysql_num_rows($result)!=0){
						$row=mysql_fetch_array($result);      
						$query="DELETE FROM cards_of_loot WHERE (id_gt=".$_SESSION['id_gt']." AND id_card=".$row['id_card'].")";
						$result=mysql_query($query);   
						
						$query="INSERT INTO cards_of_user VALUES(NULL,".$_SESSION['id_user'].",".$row['id_card'].",".$_REQUEST['to_place'].")";
						$result=mysql_query($query);  
						  
						echo '<img id="id_card'.$_REQUEST['to_place'].'" class="id_card" src="./picture/'.$row['pic'].'" value="'.$_REQUEST['to_place'].'">';
						$per_str=' взял сокровище в руки.';
						add_str($per_str,0); 
				}  
			}      
		}
	//Карту взяли со стола			
	}elseif (($_REQUEST['from_object']>=10)&&($_REQUEST['from_object']<=19)) 
	{      
		$DBLink=connectdb();
		//Карту пытаемся переложить на стол на другое место
		if (($_REQUEST['to_place']>=10)&&($_REQUEST['to_place']<=19)) {
			$query="SELECT * FROM cards_of_table JOIN cards ON (cards_of_table.id_card=cards.id_card) WHERE (id_gt=".$_SESSION['id_gt']." AND place_card=".$_REQUEST['to_place'].")";
			$result=mysql_query($query);
			if (mysql_num_rows($result)==0){
			  $query="UPDATE cards_of_table SET place_card=".$_REQUEST['to_place']." WHERE (id_gt=".$_SESSION['id_gt']." AND place_card=".$_REQUEST['from_object'].")";
			  $result=mysql_query($query);                    
			}else{   
			  $row=mysql_fetch_array($result); 
			  $query="UPDATE cards_of_table SET place_card=".$_REQUEST['to_place']." WHERE (id_gt=".$_SESSION['id_gt']." AND place_card=".$_REQUEST['from_object'].")";
			  $result=mysql_query($query);  
			  $query="UPDATE cards_of_table SET place_card=".$_REQUEST['from_object']." WHERE (id_gt=".$_SESSION['id_gt']." AND place_card=".$row['place_card']." AND id_card=".$row['id_card'].")";
			  $result=mysql_query($query);                            
			}   
		//Карту пытаемся переложить в руки
		}elseif (($_REQUEST['to_place']>=20)&&($_REQUEST['to_place']<=29)) {
			  $query="SELECT * FROM cards_of_user JOIN cards ON (cards_of_user.id_card=cards.id_card) WHERE (id_user=".$_SESSION['id_user']." AND place_card=".$_REQUEST['to_place'].")";
			  $result=mysql_query($query);
			  if (mysql_num_rows($result)==0){
				  $query="SELECT * FROM cards_of_table JOIN cards ON (cards_of_table.id_card=cards.id_card) WHERE (id_gt=".$_SESSION['id_gt']." AND place_card=".$_REQUEST['from_object'].")";
				  $result=mysql_query($query);  
				  $row=mysql_fetch_array($result);
				   
				  $query="INSERT INTO cards_of_user VALUES(NULL,".$_SESSION['id_user'].",".$row["id_card"].",".$_REQUEST['to_place'].")";
				  $result=mysql_query($query);    
				  $query="DELETE FROM cards_of_table WHERE (id_gt=".$_SESSION['id_gt']." AND place_card=".$_REQUEST['from_object']." AND id_card=".$row["id_card"].")"; 
				  $result=mysql_query($query); 
				   
				  $per_str=' взял со стола карту: [B]'.$row['c_name'].'[/B] себе в руки';
				  add_str($per_str,0);                                
			  }else{   
				  //Если карта есть на руках то карту со стола нельзя менять с картой в руке
			  }             
		}         

	}
	//Карту взяли с руки
	elseif (($_REQUEST['from_object']>=20)&&($_REQUEST['from_object']<=29)) 
	{      
		$DBLink=connectdb();
		//Карту пытаемся положить на стол
		if (($_REQUEST['to_place']>=10)&&($_REQUEST['to_place']<=19)) {
			  $query="SELECT * FROM cards_of_table JOIN cards ON (cards_of_table.id_card=cards.id_card) WHERE (id_gt=".$_SESSION['id_gt']." AND place_card=".$_REQUEST['to_place'].")";
			  $result=mysql_query($query);
			  if (mysql_num_rows($result)==0){
				  $query="SELECT * FROM cards_of_user JOIN cards ON (cards_of_user.id_card=cards.id_card) WHERE (id_user=".$_SESSION['id_user']." AND place_card=".$_REQUEST['from_object'].")";
				  $result=mysql_query($query);  
				  $row=mysql_fetch_array($result);
				   
				  $query="INSERT INTO cards_of_table VALUES(NULL,".$_SESSION['id_gt'].",".$row["id_card"].",".$_REQUEST['to_place'].")";
				  $result=mysql_query($query);    
				  $query="DELETE FROM cards_of_user WHERE (id_user=".$_SESSION['id_user']." AND place_card=".$_REQUEST['from_object']." AND id_card=".$row["id_card"].")"; 
				  $result=mysql_query($query); 
				  
				  $per_str=' бросил карту  : [B]'.$row['c_name'].'[/B] с руки на стол';
				  add_str($per_str,0);                                    
			  }else{   
				  //Если карта есть на столе то карту из руки нельзя менять с картой на столе    
			  }        
		//Карту пытаемся переложить в руках на другое место
		}elseif (($_REQUEST['to_place']>=20)&&($_REQUEST['to_place']<=29)) {   
			  $query="SELECT * FROM cards_of_user JOIN cards ON (cards_of_user.id_card=cards.id_card) WHERE (id_user=".$_SESSION['id_user']." AND place_card=".$_REQUEST['to_place'].")";
			  $result=mysql_query($query);
			  if (mysql_num_rows($result)==0){
				$query="UPDATE cards_of_user SET place_card=".$_REQUEST['to_place']." WHERE (id_user=".$_SESSION['id_user']." AND place_card=".$_REQUEST['from_object'].")";
				$result=mysql_query($query);                    
			  }else{   
				$row=mysql_fetch_array($result); 
				$query="UPDATE cards_of_user SET place_card=".$_REQUEST['to_place']." WHERE (id_user=".$_SESSION['id_user']." AND place_card=".$_REQUEST['from_object'].")";
				$result=mysql_query($query);  
				$query="UPDATE cards_of_user SET place_card=".$_REQUEST['from_object']." WHERE (id_user=".$_SESSION['id_user']." AND place_card=".$row['place_card']." AND id_card=".$row['id_card'].")";
				$result=mysql_query($query);                                        
			  }  
		}    

	}
}
?>
