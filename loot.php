<?php 
session_start();
require_once("global.php");
require_once("chat.php");
?>

<?php

function show_info_loot(){
    $query="SELECT count(*) AS count FROM cards_of_loot WHERE (id_gt=".$_SESSION['id_gt'].")";
    $result=mysql_query($query);
    $row=mysql_fetch_array($result);
    print '<b>Количество карт(сокровищ) в колоде: <b>'.$row['count'].'<br>';
    
    $query='SELECT count(*) AS count FROM discards JOIN cards ON (discards.id_card=cards.id_card) WHERE (id_gt='.$_SESSION['id_gt'].' AND cards.card_type="loot")';
    $result=mysql_query($query);
    $row=mysql_fetch_array($result);    
    print '<b>Количество карт(сокровищ) в "сбросе": <b>'.$row['count'].'<br><br>';  
    print '<b>Нажмите "Перетасовать" что бы перетасовать карты(сокровищ) из "сброса"<b><br>';  
}

if ( (isset($_SESSION['id_user'])) && (isset($_SESSION['init'])) && (isset($_SESSION['id_gt'])) ){
          if ($_REQUEST['send_com']==0) {
              $DBLink=connectdb();
              show_info_loot();
              mysql_close ($DBLink);     
          }elseif ($_REQUEST['send_com']==1) {
              $DBLink=connectdb();
              $query='SELECT * FROM discards JOIN cards ON (discards.id_card=cards.id_card) WHERE (id_gt='.$_SESSION['id_gt'].' AND cards.card_type="loot") ORDER BY RAND()';
              $result=mysql_query($query);
              if (mysql_num_rows($result)!=0){  
                  $query="SELECT MAX(num_loot) AS num_loot
                          FROM cards_of_loot               
                          WHERE (id_gt=".$_SESSION['id_gt'].")";
                  $result1=mysql_query($query);  
                  $row1=mysql_fetch_array($result1);        
                  if (!is_numeric($row1['num_loot'])){
                       $int_card=0;                        
                  }else{    
                       $int_card=$row1['num_loot'];
                  }
                  
                  while($row=mysql_fetch_array($result)){
                    $int_card=$int_card+1; 
                    $query='INSERT INTO cards_of_loot VALUES(NULL,'.$int_card.','.$row['id_card'].','.$_SESSION['id_gt'].')';
                    mysql_query($query);
                    $query="DELETE FROM discards WHERE (id_gt=".$_SESSION['id_gt']." AND id_card=".$row['id_card'].")";         
                    mysql_query($query);  
                  }         
                  
                  $query='SELECT * FROM discards WHERE (id_gt='.$_SESSION['id_gt'].')';
                  $result=mysql_query($query); 
                  if (mysql_num_rows($result)!=0){   
                        $int_card=0;
                        while($row=mysql_fetch_array($result)){
                            $int_card=$int_card+1; 
                            $query="UPDATE discards SET num_d=".$int_card." WHERE (id_gt=".$_SESSION['id_gt']." AND id_card=".$row['id_card'].")"; 
                            mysql_query($query);
                        }  
                  }  
                  $per_str=' перетасовал карты сокровищ из сброса.';
                  add_str($per_str,0);    
                  show_info_loot();                    
              }else{
                  show_info_loot();   
              }
              mysql_close ($DBLink);     
          }  
}        
?>