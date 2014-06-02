<?php 
require_once("global.php");
require_once("chat.php");
?>

<?php
session_start();
//Отчищаем игровой стол от карт, все карты сбрасываем в дискард
if ( (isset($_SESSION['id_user'])) && (isset($_SESSION['init'])) && (isset($_SESSION['id_gt'])) ){
     if ($_REQUEST['send_com_clear']==1){  
            $DBLink=connectdb();

            
            $query="SELECT * FROM cards_of_table JOIN cards ON (cards_of_table.id_card=cards.id_card) WHERE (id_gt=".$_SESSION['id_gt'].")";
            $result=mysql_query($query);
            
            if (mysql_num_rows($result)!=0){
                //Определяем самую последнюю карту в дискарде именно для этого стола
                $query="SELECT MAX(num_d) AS num_d
                        FROM discards               
                        WHERE (id_gt=".$_SESSION['id_gt'].")";
                $result1=mysql_query($query);  
                
                $row1=mysql_fetch_array($result1);
                $_SESSION['num_d']=$row1['num_d']+1; 
                
                //вставляем все карты со стола в дискард
                $i=0;
                while($row=mysql_fetch_array($result)){
                        $i=$i+1;
                        $query="INSERT INTO discards VALUES(NULL,".$_SESSION['num_d'].",".$row['id_card'].",".$_SESSION['id_gt'].")";
                        $result2=mysql_query($query);  
                        $_SESSION['num_d']=$_SESSION['num_d']+1;                       
                }  
                //Удаляем все карты с игрового стола  
                $query="DELETE FROM cards_of_table WHERE (id_gt=".$_SESSION['id_gt'].")";
                $result=mysql_query($query);
                
                $per_str=' все карты([B]'.$i.' шт.[/B]) с игрового стола отправил в сброс';
                add_str($per_str,0);          
              }   
              mysql_close ($DBLink);
              

       }
}
?>     