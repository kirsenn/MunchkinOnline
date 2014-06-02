<?php 
require_once("global.php");
require_once("chat.php");
require_once("modules/mysql.php");
require_once("my_function.php");
?>

<?php
//****ПРОДАТЬ карту*****
function sell_card($place_card)
{	
	$mysql = new MySQL;//Создаем объект 
	//Узнаем карты лежит в руке или в шмотках
	$result_user=$mysql->sql_query('SELECT * FROM users WHERE (id_user='.$_SESSION['id_user'].')');
	$row_user=mysql_fetch_array($result_user);
	//Если игрок ниже 9 уровня
	if ($row_user['u_level']<9)
	{
		//Если карта лежит в руке	
		if ( (($place_card>=20)&&($place_card<=29)) ) 
		{	
			$result_card=$mysql->sql_query("SELECT * FROM cards_of_user JOIN cards ON (cards_of_user.id_card=cards.id_card) WHERE (id_user=".$_SESSION['id_user']." AND place_card=".$place_card.")");
		}else
		{//Если карта лежит в шмотках
			$result_card=$mysql->sql_query("SELECT * FROM carried_items JOIN cards ON (carried_items.id_card=cards.id_card) WHERE (id_user=".$_SESSION['id_user']." AND place_card=".$place_card.")");
		}
		
		$row_card=mysql_fetch_array($result_card);	
		
		//Если карта имеет цену
		if ($row_card['c_cost']!==NULL)
		{
			//Проверка на ХАФЛИНГА
			$result_hafling=$mysql->sql_query('SELECT * FROM carried_items JOIN cards ON (carried_items.id_card=cards.id_card) WHERE (id_user='.$_SESSION['id_user'].' AND cards.c_name="Хафлинг")');								
			if (mysql_num_rows($result_hafling)>0){
				//Если раса игрока Хафлинг то шмотки по двойной цене
				if (isset($_SESSION['hafling'])){
					if ($_SESSION['hafling']==0){//Но сначала проверяем он уже продал хоть одну шмотку по двойной цене
						$c_cost=$row_card['c_cost']*2;//Узнаем сколько голдов у игрока получится после продажи карты
						$_SESSION['hafling']=1;
					}else{
						$c_cost=$row_card['c_cost'];//Узнаем сколько голдов у игрока получится после продажи карты	
					}
				}else{
					$c_cost=$row_card['c_cost']*2;
					$_SESSION['hafling']=1;
				}
				
			}else{
				$c_cost=$row_card['c_cost'];//Узнаем сколько голдов у игрока получится после продажи карты
			}
			$u_gold=$row_user['u_gold']+$c_cost;//$c_cost-цена карты, если ХАФЛИНГ то  цена карты двойная
			$get_level=floor($u_gold/1000);//Узнаем сколько игрок получит уровней после продажи карты
			$remain=0;
			//Проверка можно ли игроку дать столько уровней сколько он золота заработал
			if ($get_level==0){//Если он получит 0 уровней
				$u_gold=$row_user['u_gold']+$c_cost;
				$u_level=$row_user['u_level'];
			}elseif($get_level==1){//Если он получит 1 уровнь
				$u_gold=$u_gold%1000;
				$u_level=$row_user['u_level']+$get_level;
			}else{//Если он получит больше 1 уровня
				$sum_level=$row_user['u_level']+$get_level;//узнаем сколько у игрока будет уровней если обменяем все голды
				if ($sum_level>9){//Так как игрок не может подняться более 9 уровня за продажу шмоток, то просто их оставляем в виде голдов
					$remain=$sum_level-9;
					$u_level=9;
					$u_gold=$remain*1000+$u_gold%1000;
				}else{//Все нормльно просто даем игроку нужное количествоуровней
					$u_gold=$u_gold%1000;
					$u_level=$row_user['u_level']+$get_level;
				}
			}
			
			$result_last_card=$mysql->sql_query( "SELECT MAX(num_d) AS num_d FROM discards WHERE (id_gt=".$_SESSION['id_gt'].")" );
			$row_last_card=mysql_fetch_array($result_last_card);				
			$num_card=$row_last_card['num_d']+1;
			
			$mysql->sql_query("INSERT INTO discards VALUES(NULL,".$num_card.",".$row_card['id_card'].",".$_SESSION['id_gt'].")");
			
			//Если карта лежит в руке	
			if ( (($place_card>=20)&&($place_card<=29)) ) {				
				$mysql->sql_query("DELETE FROM cards_of_user WHERE (id_card=".$row_card['id_card']." AND id_user=".$_SESSION['id_user']." AND place_card=".$place_card.")");
			}else{
				$mysql->sql_query("DELETE FROM carried_items WHERE (id_card=".$row_card['id_card']." AND id_user=".$_SESSION['id_user']." AND place_card=".$place_card.")");
			}									
			
			$mysql->sql_query("UPDATE users SET u_gold=".$u_gold.", u_level=".$u_level." WHERE (id_user=".$_SESSION['id_user'].")");		
			
			if (mysql_num_rows($result_hafling)>0){//Если ХАФЛИНГ выводим этот текст	
				if ($c_cost==$row_card['c_cost']){
					//ХАФЛИНГ продает уже не первую шмотку поэтому цена обычная
					$per_str=' продал [B]'.$row_card['c_name'].'[/B] (по обычной цене так как ХАФЛИНГ может продать по двойной цене 1 шмотку за ход) за что получил '.$c_cost.' голдов';														
				}else{
					//ХАФЛИНГ продает первую шмотку  цена двойная
					$per_str=' продал [B]'.$row_card['c_name'].'[/B] по двойной цене(так как он ХАФЛИНГ) за что получил '.$c_cost.' голдов';														
				}
			}else{//Если НЕТ тогда этот
				$per_str=' продал [B]'.$row_card['c_name'].'[/B] за что получил '.$c_cost.' голдов';
			}
			if ($get_level>0){
				$per_str=$per_str.', а также обменял '.($get_level*1000-$remain*1000).' голдов на '.($get_level-$remain).' уровнь и поднялся с '.$row_user['u_level'].' на '.$u_level.' уровень';
			}     
			add_str($per_str,0); 								
			echo 1;
		}
	}else{
		$per_str=' вы не можете продавать шмотки так вы 9 уровень!';
		add_str($per_str,0);  
		echo 0;		
	}
}  
//КОНЕЦ ПРОДАТЬ карту*****

//****ОТКРЫТЬ КАРТУ *****
function open_window_card($place_card)
{	
	$mysql = new MySQL;//Создаем объект 
	//ПОлучем информацию по игровому столу
	$result_gt=$mysql->sql_query('SELECT * FROM game_tables WHERE (id_gt='.$_SESSION['id_gt'].')');
	$row_gt=mysql_fetch_array($result_gt);	
	
	if ( ($place_card>=20) && ($place_card<=29) )
	{		
		$result_ucard=$mysql->sql_query('SELECT * FROM cards_of_user JOIN cards ON (cards_of_user.id_card=cards.id_card) WHERE ( (id_user='.$_SESSION['id_user'].') AND (place_card='.$place_card.') )');
		$row_ucard=mysql_fetch_array($result_ucard);
		
		$content_window='<img id="obj_card" src="./picture/'.$row_ucard['pic'].'">';
		
		$button_shmotki='<div align="center" id="button_shmotki">В шмотки</div>';
		$button_sell='<div align="center" id="button_sell">Продать</div>';
		$button_apply='<div align="center" id="button_apply">Применить</div>';
		$button_sbros='<div align="center" id="button_sbros">В сброс</div>';
		
		switch ($row_ucard['c_type']) {
			case "u_class":
			$type_of_card="Класс";
			$apply_card="Установить как класс";
			$description_card="Применив карту вы станете принадлежать к данному классу, получив все его преимущества и недостатки";
			$buton_card=$button_apply.$button_sbros;
			break;
			case "supermunch":
			$type_of_card="Суперманчкин";
			$apply_card="Позволяет принадлежать к 2 классам";
			$description_card="Применив карту вы сможете принадлежать к двум разным класссам, получая все их преимущества и недостатки";
			$buton_card=$button_apply.$button_sbros;
			break;						
			case "race":
			$type_of_card="Раса";
			$apply_card="Установить как расу";
			$description_card="Применив карту вы станете принадлежать к данной расе, получив все ее преимущества и недостатки";
			$buton_card=$button_apply.$button_sbros;
			break;
			case "halfbreed":
			$type_of_card="Расовый коктейль";
			$apply_card="Позволяет принадлежать к 2 расам";
			$description_card="Применив карту вы сможете принадлежать к двум разным расам, получая все их преимущества и недостатки";
			$buton_card=$button_apply.$button_sbros;
			break;				
			case "getlevel":
			$type_of_card="Получи уровень";
			$apply_card="Получить уровень";
			$description_card="Применив карту вы поднимитесь на один уровень(если вы уже 9 уровень, применять карту запрещено)";
			$buton_card=$button_apply.$button_sbros;
			break;		
			case "smallitem":
			$type_of_card="Маленькая шмотка";
			$apply_card="Не работает";
			$description_card="Вы можете ее бросить в свои неактивные шмотки(бонусы)";
			$buton_card=$button_shmotki.$button_sbros;	
			break;				
			case "bigitem":
			$type_of_card="Большая шмотка";
			$apply_card="Не работает";
			$description_card="Вы можете ее бросить в свои неактивные шмотки(бонусы)";
			$buton_card=$button_shmotki.$button_sbros;
			break;			
			case "chit":
			$type_of_card="Чит";
			$apply_card="Не работает";
			$description_card="Вы можете его бросить в свои неактивные шмотки(бонусы)";
			$buton_card=$button_shmotki.$button_sbros;
			break;	
			case "magic":
			$type_of_card="Зелье";
			$apply_card="Не работает";
			$description_card="Вы можете его бросить в свои неактивные шмотки(бонусы)";
			$buton_card=$button_shmotki.$button_sbros;
			break;
			case "curse":
			$type_of_card="Проклятие";
			$apply_card="Не работает";
			$description_card="Бросьте карту на стол и в чате напишите на кого это проклятие насылаете, игрок должен будет его поместить к себе в проклятия";
			$buton_card=$button_sbros;
			break;
			case "monster":
			$type_of_card="Монстр";
			$apply_card="Не работает";
			$description_card="Вы можете его сыграть против себя, либо против другого игрока, если у вас имеется «Бродячая тварь»";
			$buton_card=$button_sbros;
			break;
			case "tvar":
			$type_of_card="Бродячая тварь";
			$apply_card="Не работает";
			$description_card="Вы можете ее сыграть против другого игрока, но только в парочке с любым монстром";
			$buton_card=$button_sbros;
			break;	
			case "pumping":
			$type_of_card="Модификатор";
			$apply_card="Не работает";
			$description_card="Вы можете его сыграть для ослабления или усиления монстра";
			$buton_card=$button_sbros;
			break;			
			default:
			$type_of_card="Неустановлен";
			$apply_card="Не работает";
			$description_card="Отсутствует";			
			$buton_card=$button_sbros;
		}
		
		if ($row_ucard['c_cost']!==NULL){
			$buton_card.=$button_sell;
		}
		
		$content_window.="<div id='info_card'><b>Тип карты:</b> $type_of_card<br/>
						  <b>Применить:</b> $apply_card<br/>
						  <b>Описание:</b> $description_card
						  </div>";
		$content_window.=$buton_card;
		
		$json_data['content_window']=$content_window;
		echo json_encode($json_data);													
	}elseif ( ($place_card>=10) && ($place_card<=19) )
	{
		$result_gtcard=$mysql->sql_query('SELECT * FROM cards_of_table JOIN cards ON (cards_of_table.id_card=cards.id_card) WHERE ( (id_gt='.$_SESSION['id_gt'].') AND (place_card='.$place_card.') )');
		$row_gtcard=mysql_fetch_array($result_gtcard);
		
		$content_window='<img id="obj_card" src="./picture/'.$row_gtcard['pic'].'">';

		$button_apply='<div align="center" id="button_apply">Применить</div>';
		$button_sbros='<div align="center" id="button_sbros">В сброс</div>';
		$button_kill='<div align="center" id="button_kill">Убить монстра/монстров</div>';
		
		switch ($row_gtcard['c_type']) {
			case "u_class":
			$type_of_card="Класс";
			$apply_card="Установить как класс";
			$description_card="Применив карту вы станете принадлежать к данному классу, получив все его преимущества и недостатки";
			$buton_card=$button_apply.$button_sbros;
			break;
			case "supermunch":
			$type_of_card="Суперманчкин";
			$apply_card="Позволяет принадлежать к 2 классам";
			$description_card="Применив карту вы сможете принадлежать к двум разным класссам, получая все их преимущества и недостатки";
			$buton_card=$button_apply.$button_sbros;
			break;						
			case "race":
			$type_of_card="Раса";
			$apply_card="Установить как расу";
			$description_card="Применив карту вы станете принадлежать к данной расе, получив все ее преимущества и недостатки";
			$buton_card=$button_apply.$button_sbros;
			break;
			case "halfbreed":
			$type_of_card="Расовый коктейль";
			$apply_card="Позволяет принадлежать к 2 расам";
			$description_card="Применив карту вы сможете принадлежать к двум разным расам, получая все их преимущества и недостатки";
			$buton_card=$button_apply.$button_sbros;
			break;				
			case "getlevel":
			$type_of_card="Получи уровень";
			$apply_card="Получить уровень";
			$description_card="Применив карту вы поднимитесь на один уровень(если вы уже 9 уровень, применять карту запрещено)";
			$buton_card=$button_apply.$button_sbros;
			break;		
			case "smallitem":
			$type_of_card="Маленькая шмотка";
			$apply_card="Не работает";
			$description_card="Вы можете ее бросить в свои неактивные шмотки(бонусы)";
			$buton_card=$button_shmotki.$button_sbros;	
			break;				
			case "bigitem":
			$type_of_card="Большая шмотка";
			$apply_card="Не работает";
			$description_card="Вы можете ее бросить в свои неактивные шмотки(бонусы)";
			$buton_card=$button_shmotki.$button_sbros;
			break;			
			case "chit":
			$type_of_card="Чит";
			$apply_card="Не работает";
			$description_card="Вы можете его бросить в свои неактивные шмотки(бонусы)";
			$buton_card=$button_shmotki.$button_sbros;
			break;	
			case "magic":
			$type_of_card="Зелье";
			$apply_card="Не работает";
			$description_card="Вы можете его бросить в свои неактивные шмотки(бонусы)";
			$buton_card=$button_shmotki.$button_sbros;
			break;
			case "curse":
			$type_of_card="Проклятие";
			$apply_card="Поместить карту к себе в проклятия";
			$description_card="Применив карту вы наложите на себе проклятие";
			$buton_card=$button_sbros.$button_apply;
			break;
			case "monster":
			$type_of_card="Монстр";
			$apply_card="Не работает";
			$description_card="С ним надо сражаться или убегать от него, коли победить не сможете";
 			if ($row_gt['active_user']==$_SESSION['id_user']){
				$buton_card=$button_sbros.$button_kill;
			}else{
				$buton_card=$button_sbros;
			}
			break;
			case "tvar":
			$type_of_card="Бродячая тварь";
			$apply_card="Не работает";
			$description_card="Она добавляет в бой дополнительного мостра";
			$buton_card=$button_sbros;
			break;	
			case "pumping":
			$type_of_card="Модификатор";
			$apply_card="Не работает";
			$description_card="Модификатор - усиливает или ослабляет монстра";
			$buton_card=$button_sbros;
			break;			
			default:
			$type_of_card="Неустановлен";
			$apply_card="Не работает";
			$description_card="Отсутствует";			
			$buton_card=$button_sbros;
		}		
		
		
		
		if ($row_ucard['c_cost']!==NULL){
			$buton_card.=$button_sell;
		}		
		
		$content_window.="<div id='info_card'><b>Тип карты:</b> $type_of_card<br/>
						  <b>Применить:</b> $apply_card<br/>
						  <b>Описание:</b> $description_card
						  </div>";
		$content_window.=$buton_card;
		
		$json_data['content_window']=$content_window;
		echo json_encode($json_data);	
	}
}  
//КОНЕЦ ОТКРЫТЬ КАРТУ *****

//Применить если карта КЛАСС
function apply_class($place_card,$row)
{			   
	$query="SELECT * FROM carried_items JOIN cards ON (carried_items.id_card=cards.id_card)
			WHERE (id_user=".$_SESSION['id_user']." AND place_card IN (30,31,32)) ORDER BY place_card";       
	$result=mysql_query($query);
	while ($row1=mysql_fetch_array($result))
	{
		  if ($row1['place_card']==30){
			  $per1=$row1;
		  }elseif ($row1['place_card']==31){
			  $per2=$row1;
		  }elseif ($row1['place_card']==32){
			  $per3=$row1;
		  }        
	}     
	//Дурацкий код, здесь вручную проверяется  сколько классов
	// можно иметь одновременно и имею ли я в данный момент класс     
	if ($row['c_type']=="supermunch")
	{ // если выбранная карта суперманчкин
		if (!isset($per1))
		{//если на 30 месте (место суперманчкина) нет карты
			$query="INSERT INTO carried_items VALUES(NULL,".$_SESSION['id_user'].",".$row['id_card'].",30)";
			$result=mysql_query($query);  
			  
			if (($place_card>=10)&&($place_card<=19)){		
				$query="DELETE FROM cards_of_table WHERE (id_card=".$row['id_card']." AND id_gt=".$_SESSION['id_gt']." AND place_card=".$place_card.")";
				$per_str=' применил карту со стола, и теперь стал [B]суперманчкином[/B]';
			}elseif (($place_card>=20)&&($place_card<=29)){	
				$query="DELETE FROM cards_of_user WHERE (id_card=".$row['id_card']." AND id_user=".$_SESSION['id_user']." AND place_card=".$place_card.")";
				$per_str=' применил карту с руки, и теперь стал [B]суперманчкином[/B]';
			}
			
			$result=mysql_query($query);

			add_str($per_str,0);      
			
			echo 1;
		}else{
			$per_str=' вы не можете применить карту [B]'.$row['c_name'].'[/B] так как вы уже используетет точно такую же!';
			add_str($per_str,0);   
			echo 0;
		}                    
	}elseif ($row['c_type']=="u_class")
	{
		if (isset($per1))
		{//Если игрок Суперманчкин
			if (!isset($per2))
			{
				$query="INSERT INTO carried_items VALUES(NULL,".$_SESSION['id_user'].",".$row['id_card'].",31)";
				$result=mysql_query($query);  
					  
				if (($place_card>=10)&&($place_card<=19)){		
					$query="DELETE FROM cards_of_table WHERE (id_card=".$row['id_card']." AND id_gt=".$_SESSION['id_gt']." AND place_card=".$place_card.")";
					$per_str=' применил карту со стола, и теперь стал принадлежать к классу: [B]'.$row['c_name'].'[/B]';
				}elseif (($place_card>=20)&&($place_card<=29)){	
					$query="DELETE FROM cards_of_user WHERE (id_card=".$row['id_card']." AND id_user=".$_SESSION['id_user']." AND place_card=".$place_card.")";
					$per_str=' применил карту с руки, и теперь стал принадлежать к классу: [B]'.$row['c_name'].'[/B]';
				}
				$result=mysql_query($query);
				
				
				add_str($per_str,0);    
		 
				echo 1;
			}elseif(!isset($per3)){
				$query="INSERT INTO carried_items VALUES(NULL,".$_SESSION['id_user'].",".$row['id_card'].",32)";
				$result=mysql_query($query);  
					  
				if (($place_card>=10)&&($place_card<=19)){		
					$query="DELETE FROM cards_of_table WHERE (id_card=".$row['id_card']." AND id_gt=".$_SESSION['id_gt']." AND place_card=".$place_card.")";
					$per_str=' применил карту со стола, и теперь стал принадлежать к классу: [B]'.$row['c_name'].'[/B]';
				}elseif (($place_card>=20)&&($place_card<=29)){	
					$query="DELETE FROM cards_of_user WHERE (id_card=".$row['id_card']." AND id_user=".$_SESSION['id_user']." AND place_card=".$place_card.")";
					$per_str=' применил карту с руки, и теперь стал принадлежать к классу: [B]'.$row['c_name'].'[/B]';
				}
				$result=mysql_query($query); 
					
				add_str($per_str,0);   
													  
				echo 1;                            
			} else{
				$per_str=' вы не можете принадлежать к классу [B]'.$row['c_name'].'[/B] так как вы уже принадлежите к двум классам. Избавьтесь от одного из класса, и тогда сможете принадлежать к  классу [B]'.$row['c_name'].'[/B]';
				add_str($per_str,0); 
				echo 0;
			}
		}else{
			if ((!isset($per2))&&(!isset($per3)))
			{          
				$query="INSERT INTO carried_items VALUES(NULL,".$_SESSION['id_user'].",".$row['id_card'].",31)";
				$result=mysql_query($query);  
					
				$query="DELETE FROM cards_of_user WHERE (id_card=".$row['id_card']." AND id_user=".$_SESSION['id_user']." AND place_card=".$place_card.")";
				if (($place_card>=10)&&($place_card<=19)){		
					$query="DELETE FROM cards_of_table WHERE (id_card=".$row['id_card']." AND id_gt=".$_SESSION['id_gt']." AND place_card=".$place_card.")";
					$per_str=' применил карту со стола, и теперь стал принадлежать к классу: [B]'.$row['c_name'].'[/B]';
				}elseif (($place_card>=20)&&($place_card<=29)){	
					$query="DELETE FROM cards_of_user WHERE (id_card=".$row['id_card']." AND id_user=".$_SESSION['id_user']." AND place_card=".$place_card.")";
					$per_str=' применил карту с руки, и теперь стал принадлежать к классу: [B]'.$row['c_name'].'[/B]';
				}  
				$result=mysql_query($query); 
				  
				add_str($per_str,0);   
				  
				echo 1;  
			}else{			
				$per_str=' вы не можете принадлежать к двум классам одновременно (если вы не Суперманчкин). Избавьтесь от своего класса, и тогда сможете принадлежать к классу [B]'.$row['c_name'].'[/B]';
				add_str($per_str,0); 
				echo 0;
			}                          
		}         
	}   
}
//Применить если карта РАСА
function apply_race($place_card,$row)
{
	$query="SELECT * FROM carried_items JOIN cards ON (carried_items.id_card=cards.id_card)
		    WHERE (id_user=".$_SESSION['id_user']." AND place_card IN (40,41,42)) ORDER BY place_card";       
	$result=mysql_query($query);
	while ($row1=mysql_fetch_array($result)){
		if ($row1['place_card']==40){
			$per1=$row1;
		}elseif ($row1['place_card']==41){
			$per2=$row1;
		}elseif ($row1['place_card']==42){
			$per3=$row1;
		}        
	}     
	//Дурацкий код, здесь вручную проверяется  сколько рас
	// можно иметь одновременно и имею ли я в данный момент расу    
	if ($row['c_type']=="halfbreed")
	{ // если выбранная карта полукровки
		if (!isset($per1))
		{//если на 30 месте (место полукровки) нет карты
			$query="INSERT INTO carried_items VALUES(NULL,".$_SESSION['id_user'].",".$row['id_card'].",40)";
			$result=mysql_query($query);  
			
			if (($place_card>=10)&&($place_card<=19)){		
				$query="DELETE FROM cards_of_table WHERE (id_card=".$row['id_card']." AND id_gt=".$_SESSION['id_gt']." AND place_card=".$place_card.")";
				$per_str=' применил карту со стола, и теперь обладает [B]рассовым коктейлем[/B]';
			}elseif (($place_card>=20)&&($place_card<=29)){	
				$query="DELETE FROM cards_of_user WHERE (id_card=".$row['id_card']." AND id_user=".$_SESSION['id_user']." AND place_card=".$place_card.")";
				$per_str=' применил карту с руки, и теперь обладает [B]рассовым коктейлем[/B]';
			}

			$result=mysql_query($query); 
			  			
			add_str($per_str,0);   
			  
			echo 1;
		}else{
			$per_str=' вы не можете применить карту [B]'.$row['c_name'].'[/B] так как вы уже используетет точно такую же!';
			add_str($per_str,0); 
			echo 0;
		}                    
	}elseif ($row['c_type']=="race")
	{
	    if (isset($per1))
		{
		    if (!isset($per2))
			{
				$query="INSERT INTO carried_items VALUES(NULL,".$_SESSION['id_user'].",".$row['id_card'].",41)";
				$result=mysql_query($query);  
					
				if (($place_card>=10)&&($place_card<=19)){		
					$query="DELETE FROM cards_of_table WHERE (id_card=".$row['id_card']." AND id_gt=".$_SESSION['id_gt']." AND place_card=".$place_card.")";
					$per_str=' применил карту со стола, и теперь стал принадлежать к расе: [B]'.$row['c_name'].'[/B]';
				}elseif (($place_card>=20)&&($place_card<=29)){	
					$query="DELETE FROM cards_of_user WHERE (id_card=".$row['id_card']." AND id_user=".$_SESSION['id_user']." AND place_card=".$place_card.")";
					$per_str=' применил карту с руки, и теперь стал принадлежать к расе: [B]'.$row['c_name'].'[/B]';
				}
				$result=mysql_query($query); 
								
				add_str($per_str,0);   
				  
				echo 1;
			}elseif(!isset($per3))
			{
			    $query="INSERT INTO carried_items VALUES(NULL,".$_SESSION['id_user'].",".$row['id_card'].",42)";
				$result=mysql_query($query);  
					
				if (($place_card>=10)&&($place_card<=19)){		
					$query="DELETE FROM cards_of_table WHERE (id_card=".$row['id_card']." AND id_gt=".$_SESSION['id_gt']." AND place_card=".$place_card.")";
					$per_str=' применил карту со стола, и теперь стал принадлежать к расе: [B]'.$row['c_name'].'[/B]';
				}elseif (($place_card>=20)&&($place_card<=29)){	
					$query="DELETE FROM cards_of_user WHERE (id_card=".$row['id_card']." AND id_user=".$_SESSION['id_user']." AND place_card=".$place_card.")";
					$per_str=' применил карту с руки, и теперь стал принадлежать к расе: [B]'.$row['c_name'].'[/B]';
				}
				$result=mysql_query($query); 
				
				add_str($per_str,0);  
					
				echo 1;                          
			}else{
				$per_str=' вы не можете принадлежать к расе [B]'.$row['c_name'].'[/B] так как вы уже принадлежите к двум расам. Избавьтесь от одной из рас, и тогда сможете принадлежать к  расе [B]'.$row['c_name'].'[/B]';
				add_str($per_str,0); 
				echo 0;
			} 
		}else{
			if ((!isset($per2))&&(!isset($per3))){          
				$query="INSERT INTO carried_items VALUES(NULL,".$_SESSION['id_user'].",".$row['id_card'].",41)";
				$result=mysql_query($query); 
					
				if (($place_card>=10)&&($place_card<=19)){		
					$query="DELETE FROM cards_of_table WHERE (id_card=".$row['id_card']." AND id_gt=".$_SESSION['id_gt']." AND place_card=".$place_card.")";
					$per_str=' применил карту со стола, и теперь стал принадлежать к расе: [B]'.$row['c_name'].'[/B]';
				}elseif (($place_card>=20)&&($place_card<=29)){	
					$query="DELETE FROM cards_of_user WHERE (id_card=".$row['id_card']." AND id_user=".$_SESSION['id_user']." AND place_card=".$place_card.")";
					$per_str=' применил карту с руки, и теперь стал принадлежать к расе: [B]'.$row['c_name'].'[/B]';
				}
				$result=mysql_query($query); 
				
				add_str($per_str,0);  
					 
				echo 1;
			}else{			
				$per_str=' вы не можете принадлежать к двум расам одновременно (если вы не полукровка). Избавьтесь от своей расы, и тогда сможете принадлежать к расе [B]'.$row['c_name'].'[/B]';
				add_str($per_str,0); 
				echo 0;
			}                          
		}         
	}else{
		echo 0;
	}    
}
//Применить если карта ПОЛУЧИТЬ УРОВЕНЬ
function apply_getlevel($place_card,$row_card)
{
	$mysql = new MySQL;//Создаем объект 
	//Получаем данные по игроку
	$result_user=$mysql->sql_query('SELECT * FROM users WHERE (id_user='.$_SESSION['id_user'].')');
	$row_user=mysql_fetch_array($result_user);
	//Если игрок ниже 9 уровня
	if ($row_user['u_level']<9){
		$result_last_card=$mysql->sql_query( "SELECT MAX(num_d) AS num_d FROM discards WHERE (id_gt=".$_SESSION['id_gt'].")" );
		$row_last_card=mysql_fetch_array($result_last_card);				
		$num_card=$row_last_card['num_d']+1;
		
		$mysql->sql_query("INSERT INTO discards VALUES(NULL,".$num_card.",".$row_card['id_card'].",".$_SESSION['id_gt'].")");
		
		$u_level=$row_user['u_level']+1;
		
		if (($place_card>=10)&&($place_card<=19)){		
			$mysql->sql_query("DELETE FROM cards_of_table WHERE (id_card=".$row_card['id_card']." AND id_gt=".$_SESSION['id_gt']." AND place_card=".$place_card.")");
			$per_str=' применил карту со стола - [B]'.$row_card['c_name'].'[/B] в следствии чего поднялся с '.$row_user['u_level'].' на '.$u_level.' уровень';
		}elseif (($place_card>=20)&&($place_card<=29)){	
			$mysql->sql_query("DELETE FROM cards_of_user WHERE (id_card=".$row_card['id_card']." AND id_user=".$_SESSION['id_user']." AND place_card=".$place_card.")");
			$per_str=' применил карту с руки - [B]'.$row_card['c_name'].'[/B] в следствии чего поднялся с '.$row_user['u_level'].' на '.$u_level.' уровень';
		}
							
		$mysql->sql_query("UPDATE users SET u_level=".$u_level." WHERE (id_user=".$_SESSION['id_user'].")");	
		
		
		add_str($per_str,0); 								
		echo 1;
	}else{
		$per_str=' вы не можете применить карту [B]'.$row_card['c_name'].'[/B] так вы 9 уровень!';
		add_str($per_str,0);  
		echo 0;			
	}
}
//Применить если карта ПРОКЛЯТИЕ
function apply_curse($place_card,$row)
{
	$DBLink=connectdb();
                                                    
	$query="SELECT * FROM carried_items JOIN cards ON (carried_items.id_card=cards.id_card)
			WHERE (id_user=".$_SESSION['id_user']." AND place_card BETWEEN 70 AND 75) ORDER BY place_card";       
	$result=mysql_query($query);

	unset($select_place);
	if (mysql_num_rows($result)!=0){
			  for ($i=70;$i<=75;$i++){
				  $row1=mysql_fetch_array($result);
				  if ($row1['place_card']!=$i){
					  $select_place=$i; 
					  break; 
				  }
			  }     
	}else{
		   $select_place=70;  
	}          
	
	if (isset($select_place)){	
		//Ложим карту в шмотки
		  $query="INSERT INTO carried_items VALUES(NULL,".$_SESSION['id_user'].",".$row['id_card'].",".$select_place.")";
		  $result=mysql_query($query);  
					  
		  $query="DELETE FROM cards_of_table WHERE (id_card=".$row['id_card']." AND id_gt=".$_SESSION['id_gt']." AND place_card=".$_REQUEST['from_object'].")";
		  $result=mysql_query($query); 
		  
		  $per_str=' бросил карту со стола : [B]'.$row['c_name'].'[/B] к себе в проклятия';
		  add_str($per_str,0);  
		  
		  print "1";
	}else{
		//В шмотках нет свободного места
		  $per_str=' Нельзя положить карту себе в проклятия [B]НЕТ СВОБОДНОГО МЕСТА[/B]';
		  add_str($per_str,0);  
		  print "0";
	}										                              
	mysql_close ($DBLink);     	
}

//****КНОПКА ПРИМЕНИТЬ*****
function apply_card($place_card)
{
	$mysql = new MySQL;//Создаем объект 
	if (($place_card>=10)&&($place_card<=19)) 
	{//Если карта лежит на СТОЛЕ то запрос такой				
		$result_card=$mysql->sql_query('SELECT * FROM cards_of_table JOIN cards ON (cards_of_table.id_card=cards.id_card) WHERE (id_gt='.$_SESSION['id_gt'].' AND place_card='.$place_card.')');				
	}elseif (($place_card>=20)&&($place_card<=29)) 
	{//Если карта лежит в РУКЕ то запрос такой		
		$result_card=$mysql->sql_query("SELECT * FROM cards_of_user JOIN cards ON (cards_of_user.id_card=cards.id_card) WHERE (id_user=".$_SESSION['id_user']." AND place_card=".$place_card.")");			
	}
	if (mysql_num_rows($result_card)!=0)
	{
		$row_card=mysql_fetch_array($result_card);
		if ( ($row_card['c_type']=="supermunch") || ($row_card['c_type']=="u_class")){ 
			apply_class($place_card,$row_card);	 // -------если карта КЛАСС-------
		}elseif ( ($row_card['c_type']=="halfbreed") || ($row_card['c_type']=="race") ){		
			apply_race($place_card,$row_card);	 // -------если карта РАСА-------
		}elseif ($row_card['c_type']=="getlevel"){		
			apply_getlevel($place_card,$row_card);// -------если карта ПОЛУЧИТЬ УРОВЕНЬ-------			
		}elseif ($row_card['c_type']=="curse"){		
			apply_curse($place_card,$row_card);// -------если карта ПРОКЛЯТИЕ-------			
		}else{
		  echo 0;
		}   			
	}		
}  
//КОНЕЦ КНОПКА ПРИМЕНИТЬ*****

//****КОНТРОЛЬ КОЛИЧЕСТВА КАРТ В РУКЕ*****
function control_hand_card($place_card)
{	
	$mysql = new MySQL;//Создаем объект 
	//Узнаем количество карт у игрока в руке
	$result_ucard=$mysql->sql_query('SELECT * FROM cards_of_user WHERE (id_user='.$_SESSION['id_user'].')');
	$num_cards=mysql_num_rows($result_ucard);
	$limit_cards=5;
	
	//Проверка на Дварфа, можете вы 6 карт иметь или нет
	$result_dvarf=$mysql->sql_query('SELECT * FROM carried_items JOIN cards ON (carried_items.id_card=cards.id_card) WHERE (id_user='.$_SESSION['id_user'].' AND cards.c_name="Дварф")');
	if (mysql_num_rows($result_dvarf)>0)
	{
		$limit_cards=6;
	}
	
	if ($num_cards>$limit_cards)
	{
		$per_str=' вы не можете закончить ход! На руках должно остаться не более [B]'.$limit_cards.' карт[/B],а у вас их [B]'.$num_cards.'[/B]. Избавьтесь от лишних (примените их или отдайте игрокам с наинизшим уровнем,если у вас наинизший уровень то отправьте в сброс)!';
		add_str($per_str,0); 
		return -1;//Нарушены правила, отправляем -1	
	}else{
		return 1;//Все правила в норме отправляем 1
	}
}  
//КОНЕЦ КОНТРОЛЬ КОЛИЧЕСТВА КАРТ В РУКЕ*****

//****КОНТРОЛЬ ФАЗЫ ХОДА*****
function control_phase_move($param)
{	//phase_move=1 одна карта дверей открыта, т.е. выполнена первая фаза хода
	//phase_move=10 игрок сразился с монстром
	
	//param=1 - Игрок нажал конец хода - проверяем фазу хода
	//param=2 - игрок берет карту двери в темную(ложит в руку)
	//param=3 - игрок пытается убить монстра
	
	$mysql = new MySQL;//Создаем объект 
	//Получаем данные по столу
	$result_gt=$mysql->sql_query('SELECT * FROM game_tables WHERE (id_gt='.$_SESSION['id_gt'].')');
	$row_gt=mysql_fetch_array($result_gt);
		
	
	if ($param==1)
	{//param=1 - Игрок нажал конец хода - проверяем фазу хода
		if ($row_gt['phase_move']==0)
		{//Игрок за свой ход вообще ничего не сделал
			$per_str=' вы не можете закончить ход! Вы должны открыть одну дверь (перетянув карту из колоды дверей на игровой стол) или если вы клирик - взять карту из сброса!';
			add_str($per_str,0); 	
			return -1;//Нарушены правила, отправляем -1	
		}elseif ($row_gt['phase_move']==1)
		{//Игрок открыл дверь
			return 1;//Все правила в норме отправляем 1
		}elseif ($row_gt['phase_move']==10)
		{//игрок сразился с монстром
			return 1;//Все правила в норме отправляем 1
		}		
	}elseif($param==2)
	{//param=2 - игрок берет карту двери в темную(ложит в руку)
		if ($row_gt['phase_move']==10)
		{//игрок уже сразился с монстром
			$per_str=' вы не можете взять карту из колоды дверей и положить ее в руку, вы уже сразились с монстром!';
			add_str($per_str,0); 		
			return -1;//игрок не может взять карту двери в темную(ложит в руку),так как он уже сразился с монстром
		}else{
			return 1;
		}		
	}elseif($param==3)
	{//param=3 - игрок пытается убить монстра
		if ($row_gt['phase_move']==10)
		{//игрок уже сразился с монстром
			$per_str=' вы не можете дважды за ход сражаться с монстром!';
			add_str($per_str,0); 		
			return -1;//игрок не может дважды за ход сражаться с монстрами
		}else{ 
			return 1;
		}		
	}
}
//КОНЕЦ КОНТРОЛЬ ФАЗЫ ХОДА*****

//****Изменяем фазу хода*****
function change_phase_move($param)
{	//Если $param=1 - игрок достает карту из колоды дверей и ложит ее на игровой стол
	//Если $param=2 - игрок берет карту из дискарда 
	//Если $param=3 - игрок сражается с монстром
	$mysql = new MySQL;//Создаем объект 
	//Получаем данные по столу
	$result_gt=$mysql->sql_query('SELECT * FROM game_tables WHERE (id_gt='.$_SESSION['id_gt'].')');
	$row_gt=mysql_fetch_array($result_gt);	
	if ($param==1)
	{//Если $param=1 - игрок достает карту из колоды дверей и ложит ее на игровой стол	
		if ($_SESSION['id_user']==$row_gt['active_user'])
		{//Если игрок ходит в данный момент то РАЗРЕШЕНО
			if ($row_gt['phase_move']==0){
				$mysql->sql_query('UPDATE game_tables SET phase_move="1" WHERE (id_gt='.$_SESSION['id_gt'].')');
			} 
			return 1;//Все правила в норме отправляем 1
		}else{//Если игрок не ходит в данный момент то ЗАПРЕЩЕНО
			$per_str=' вы не можете взять карту из колоды дверей и положить ее на игровой стол, сейчас не ваш ход!';
			add_str($per_str,0); 
			return -1;//Нарушены правила, отправляем -1	
		}
	}elseif($param==2)
	{//Если $param=2 - игрок берет карту из дискарда 
		if ($_SESSION['id_user']==$row_gt['active_user'])
		{//Если игрок ходит в данный момент то РАЗРЕШЕНО
			if ($row_gt['phase_move']==0)
			{//Проверяем фаза хода 0, т.е. игрок только что начал ходить
				//Проверяем на КЛИРИКА
				$result_kliric=$mysql->sql_query('SELECT * FROM carried_items JOIN cards ON (carried_items.id_card=cards.id_card) WHERE (id_user='.$_SESSION['id_user'].' AND cards.c_name="Клирик")');								
				if (mysql_num_rows($result_kliric)>0)
				{//Если игрок КЛИРИК то ему можно не открывать дверь а взять 1 верхнюю карту из сброса					
					$mysql->sql_query('UPDATE game_tables SET phase_move=1 WHERE (id_gt='.$_SESSION['id_gt'].')');					
					return 1;//Все правила в норме отправляем 1
				}
			} 
		}
	}elseif($param==3)
	{//Если $param=3 -  игрок сражается с монстром
	if ($_SESSION['id_user']==$row_gt['active_user'])
		{//Если игрок ходит в данный момент то РАЗРЕШЕНО
			if ($row_gt['phase_move']>=1)
			{//Проверяем фаза хода 1, т.е. игрок открыл дверь или если он клирик воскресил карту из дискарда				
				$mysql->sql_query('UPDATE game_tables SET phase_move=10 WHERE (id_gt='.$_SESSION['id_gt'].')');					
				return 1;//Все правила в норме отправляем 1
			}else{
				$per_str=' вы не можете сражаться с монстром! По правилам игры вы сначала должны открыть одну дверь (перетянув карту из колоды дверей на игровой стол) или если вы клирик - взять карту из сброса!';
				add_str($per_str,0); 	
				return -1;//Нарушены правила, отправляем -1	
			} 
		}
		return 1;
	}
}
//КОНЕЦ *Изменяем фазу хода*****

//****УБИТЬ МОНСТРА*****
function kill_monster($place_card)
{	//Карта МОНСТЕР обладает параметрами:
	//param1 - количество уровней которые можно получить   
    //param2 - количество сокровищ которые можно получить 
	$mysql = new MySQL;//Создаем объект 
	if (($place_card>=10)&&($place_card<=19)) 
	{	
		$flag_control_kill=control_phase_move(3);//проверка может ли игрок сражаться с монстром. Игрок за ход может только 1 раз сразиться с монстром
		if ($flag_control_kill==1)
		{ 
			$flag_control_rule=change_phase_move(3);//Изменяем фазу хода на 10,т.е. игрок убивате монстра/монстров		
			if ($flag_control_rule==1)
			{	
				$fl_elf_level=0;//Флаг проверки дали уровень эльфу или нет
				//Получаем данные по картам на столе с типом монстер
				$result_card=$mysql->sql_query('SELECT * FROM cards_of_table JOIN cards ON (cards_of_table.id_card=cards.id_card) WHERE (id_gt='.$_SESSION['id_gt'].' AND cards.c_type="monster")');				
				while ($row_card=mysql_fetch_array($result_card))
				{//Получаем по очереди все карты монстров лежащие на игровом столе и убиваем их		
					$place_card=$row_card['place_card'];			
					$result_last_card=$mysql->sql_query( "SELECT MAX(num_d) AS num_d FROM discards WHERE (id_gt=".$_SESSION['id_gt'].")" );
					$row_last_card=mysql_fetch_array($result_last_card);				
					$num_card=$row_last_card['num_d']+1;
					
					$mysql->sql_query("INSERT INTO discards VALUES(NULL,".$num_card.",".$row_card['id_card'].",".$_SESSION['id_gt'].")");
					
					$mysql->sql_query("DELETE FROM cards_of_table WHERE (id_card=".$row_card['id_card']." AND id_gt=".$_SESSION['id_gt']." AND place_card=".$place_card.")");
					
					//Получаем данные по игроку
					$result_user=$mysql->sql_query('SELECT * FROM users WHERE (id_user='.$_SESSION['id_user'].')');
					$row_user=mysql_fetch_array($result_user);
					
					//Получаем данные по игровому столу
					$result_gt=$mysql->sql_query('SELECT * FROM game_tables WHERE (id_gt='.$_SESSION['id_gt'].')');
					$row_tg=mysql_fetch_array($result_gt);
					$help_me = $row_tg['help_me'];//Кто помогает игроку ходящему в данный момент
					//Если игроку помогает другой игрок, получаем данные по помощнику
					if ($help_me>0)
					{
						$result_uhelper = $mysql->sql_query("SELECT * FROM users WHERE (id_user=".$help_me.")");
						$row_uhelper = mysql_fetch_array($result_uhelper);
						$helper_id=$row_uhelper['id_user'];//id того кто помогает 					
						$helper_name=$row_uhelper['login'];//Имя того кто помогает 
						
						$uhelper_level=0;
						//Проверка на Эльфа помощника, получает 1 уровень 
						$result_elf=$mysql->sql_query('SELECT * FROM carried_items JOIN cards ON (carried_items.id_card=cards.id_card) WHERE (id_user='.$helper_id.' AND cards.c_name="Эльф")');
						if ((mysql_num_rows($result_elf)>0) && ($fl_elf_level==0))
						{
							$uhelper_level=$row_uhelper['u_level']+1;
							$mysql->sql_query("UPDATE users SET u_level=".$uhelper_level." WHERE (id_user=".$helper_id.")");
							$fl_elf_level=1;
						}					
					}
					
					
					//Сколько уровней дать игроку
					$take_level=$row_card['param1'];
					//Сколько сокровищ дать игроку
					$take_loot=$row_card['param2'];
					//Такой уровень установим игроку
					$u_level=$row_user['u_level']+$take_level;
						
					if ($u_level>10){					
						$u_level=10;									
					}
									
					$mysql->sql_query("UPDATE users SET u_level=".$u_level." WHERE (id_user=".$_SESSION['id_user'].")");	
					$occupy_place="";//Места на которые ложаться сокровища
					
					if ($help_me==0)
					{//Если у игрока НЕТ ПОМОЩНИКА, то выдаем все сокровища ему на руку
						//Выдаем сокровища за убитого монстра
						//1 Определяем сколько и каких карт лежит у игрока в руках
						$result_ucard=$mysql->sql_query("SELECT * FROM cards_of_user WHERE (id_user=".$_SESSION['id_user'].") ORDER BY place_card");       
						if (mysql_num_rows($result_ucard)!==0)
						{					
							$row_ucard=mysql_fetch_array($result_ucard);
						}
						//Первое место в руке куда ложим карты
						$i=20;
						$num_card=0;
						$result_loot=$mysql->sql_query("SELECT * FROM cards_of_loot JOIN cards ON (cards_of_loot.id_card=cards.id_card) WHERE (id_gt=".$_SESSION['id_gt'].") LIMIT ".$take_loot);   
						while($row_loot=mysql_fetch_array($result_loot)) 
						{ 	//Есть ли у игрока вообще карты в руке				
							if (isset($row_ucard['place_card']))
							{			
								while ($i<=29)
								{
									if ($row_ucard['place_card']==$i){
										$row_ucard=mysql_fetch_array($result_ucard);
										$i=$i+1; 
									}else{
										$mysql->sql_query("DELETE FROM cards_of_loot WHERE (id_gt=".$_SESSION['id_gt']." AND id_card=".$row_loot['id_card'].")");					  
										$mysql->sql_query("INSERT INTO cards_of_user VALUES(NULL,".$_SESSION['id_user'].",".$row_loot['id_card'].",".$i.")");								
										if ($occupy_place==""){
											$occupy_place=($i-19);
										}else{
											$occupy_place=$occupy_place.",".($i-19);
										}									
										$num_card=$num_card+1;								
										$json_data['id_card'.$i]='<img id="id_card'.$i.'" class="id_card" src="./picture/'.$row_loot['pic'].'" value="'.$i.'">';
										$i=$i+1; 
										break;															
									}	
								}							
							}else
							{						
								$mysql->sql_query("DELETE FROM cards_of_loot WHERE (id_gt=".$_SESSION['id_gt']." AND id_card=".$row_loot['id_card'].")");
								$mysql->sql_query("INSERT INTO cards_of_user VALUES(NULL,".$_SESSION['id_user'].",".$row_loot['id_card'].",".$i.")");						     
								if ($occupy_place==""){
									$occupy_place=($i-19);
								}else{
									$occupy_place=$occupy_place.",".($i-19);
								}									
								$num_card=$num_card+1;
								$json_data['id_card'.$i]='<img id="id_card'.$i.'" class="id_card" src="./picture/'.$row_loot['pic'].'" value="'.$i.'">';
								$i=$i+1; 
							}    																				
							//Если не все карты поместились в руку	
							if ($i>29){
								break;
							}					
						}         
							
						if ($num_card==$take_loot){
							$per_str=' убил монстра [B]'.$row_card['c_name'].'[/B] и поднялся с '.$row_user['u_level'].' на '.$u_level.' уровень! А также получил '.$take_loot.' сокровища на руки. Номера мест куда упали сокровища:'.$occupy_place;	
						}else{
							$per_str=' убил монстра [B]'.$row_card['c_name'].'[/B] и поднялся с '.$row_user['u_level'].' на '.$u_level.' уровень! А также получил '.$num_card.' сокровища на руки, а должен был получить '.$take_loot.' но у него на руках место кончилось. Освободи руки и возьми еще '.($take_loot-$num_card).' сокровища. Номера мест куда упали сокровища:'.$occupy_place;
							
						}	
										
						add_str($per_str,0); 								
						$json_data['result_com']=1;	
					}else
					{//Если у игрока ЕСТЬ помощник, то выдаем все сокровища в светлую на игровой стол
						//1 Определяем сколько и каких карт лежит на игровом столе
						$result_ucard=$mysql->sql_query("SELECT * FROM cards_of_table WHERE (id_gt=".$_SESSION['id_gt'].") ORDER BY place_card");       
						if (mysql_num_rows($result_ucard)!==0)
						{					
							$row_ucard=mysql_fetch_array($result_ucard);
						}
						//Первое место на столе куда ложим карты
						$i=10;
						$num_card=0;//Количество реально полученных сокровищ на руки
						$result_loot=$mysql->sql_query("SELECT * FROM cards_of_loot JOIN cards ON (cards_of_loot.id_card=cards.id_card) WHERE (id_gt=".$_SESSION['id_gt'].") LIMIT ".$take_loot);   
						while($row_loot=mysql_fetch_array($result_loot)) 
						{ 	//Есть ли на столе карты вообще			
							if (isset($row_ucard['place_card']))
							{			
								while ($i<=19)
								{
									if ($row_ucard['place_card']==$i){
										$row_ucard=mysql_fetch_array($result_ucard);
										$i=$i+1; 
									}else{
										$mysql->sql_query("DELETE FROM cards_of_loot WHERE (id_gt=".$_SESSION['id_gt']." AND id_card=".$row_loot['id_card'].")");					  
										$mysql->sql_query("INSERT INTO cards_of_table VALUES(NULL,".$_SESSION['id_gt'].",".$row_loot['id_card'].",".$i.")");								
										if ($occupy_place==""){
											$occupy_place=($i-9);
										}else{
											$occupy_place=$occupy_place.",".($i-9);
										}			
										$num_card=$num_card+1;								
										$json_data['id_card'.$i]='<img id="id_card'.$i.'" class="id_card" src="./picture/'.$row_loot['pic'].'" value="'.$i.'">';
										$i=$i+1; 
										break;															
									}	
								}							
							}else
							{						
								$mysql->sql_query("DELETE FROM cards_of_loot WHERE (id_gt=".$_SESSION['id_gt']." AND id_card=".$row_loot['id_card'].")");
								$mysql->sql_query("INSERT INTO cards_of_table VALUES(NULL,".$_SESSION['id_gt'].",".$row_loot['id_card'].",".$i.")");						     
								if ($occupy_place==""){
									$occupy_place=($i-9);
								}else{
									$occupy_place=$occupy_place.",".($i-9);
								}			
								$num_card=$num_card+1;
								$json_data['id_card'.$i]='<img id="id_card'.$i.'" class="id_card" src="./picture/'.$row_loot['pic'].'" value="'.$i.'">';
								$i=$i+1; 
							}    																				
							//Если не все карты поместились в руку	
							if ($i>19){
								break;
							}					
						}         
						
						//Если помощник не Эльф то ему не даем уровней нужно сообщить об этом
						if ($uhelper_level==0)
						{
							if ($num_card==$take_loot)
							{//место  хватило на столе
								$per_str=' при участии [B]'.$helper_name.'[/B] убили монстра [B]'.$row_card['c_name'].'[/B]. [B]'.$_SESSION['login'].'[/B] поднялся с '.$row_user['u_level'].' на '.$u_level.' уровень! А также за убийство монстра они получили '.$num_card.' сокровища в открытую. Номера мест куда упали сокровища:'.$occupy_place;
							}else{//место не хватило на столе
								$per_str=' при участии [B]'.$helper_name.'[/B] убили монстра [B]'.$row_card['c_name'].'[/B]. [B]'.$_SESSION['login'].'[/B] поднялся с '.$row_user['u_level'].' на '.$u_level.' уровень! А также за убийство монстра они получили '.$num_card.' сокровища в открытую, а должны были получить '.$take_loot.' но на столе место кончилось. Отчистите стол и возьмите еще '.($take_loot-$num_card).' сокровища. Номера мест куда упали сокровища:'.$occupy_place;
							}
						}else
						{//если ЭЛЬФ то даем уровень
							if ($num_card==$take_loot)
							{//место  хватило на столе
								$per_str=' при участии [B]'.$helper_name.'[/B] убили монстра [B]'.$row_card['c_name'].'[/B]. [B]'.$_SESSION['login'].'[/B] поднялся с '.$row_user['u_level'].' на '.$u_level.' уровень и [B]'.$helper_name.'[/B] поднялся с '.$row_uhelper['u_level'].' на '.$uhelper_level.' уровень - так как он ЭЛЬФ ! А также они получили '.$take_loot.' сокровища в открытую';	
							}else{//место не хватило на столе
								$per_str=' при участии [B]'.$helper_name.'[/B] убили монстра [B]'.$row_card['c_name'].'[/B]. [B]'.$_SESSION['login'].'[/B] поднялся с '.$row_user['u_level'].' на '.$u_level.' уровень и [B]'.$helper_name.'[/B] поднялся с '.$row_uhelper['u_level'].' на '.$uhelper_level.' уровень - так как он ЭЛЬФ ! А также они получили '.$num_card.' сокровища в открытую,а должны были получить '.$take_loot.' но на столе место кончилось. Отчистите стол и возьмите еще '.($take_loot-$num_card).' сокровищ. Номера мест куда упали сокровища:'.$occupy_place;
							}						
						}
										
						add_str($per_str,0); 								
						$json_data['result_com']=1;						
					}			
				}
			}else{
				$json_data['result_com']=0;
			}	
		}else{//Игрок уже сражался 1 раз за ход с монстром, поэтому ему 2-й раз нельзя
			$json_data['result_com']=0;
		}			
	}else{
		$json_data['result_com']=0;
	}
	echo json_encode($json_data);
}  
//КОНЕЦ УБИТЬ МОНСТРА*****
?>