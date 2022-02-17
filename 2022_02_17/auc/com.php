<?
include($_SERVER["DOCUMENT_ROOT". "/practice/inc/xmlHeader.php"]);

//datepicker
$SQL = "SELECT *
        FROM db_dev.ta_board
        WHERE date_format(regdate, '%Y-%m-%d') 
        BETWEEN '{$datepicker1}' AND '{$datepicker2}'";

$stmt=$pdo->prepare($SQL);
$stmt->execute();

switch($mode){
    case "json" :
        {
           $dataArr=array();
           while($rs=$stmt->fetch()){
               $dataArr["item"][]=array(
                   "title"=>$rs['title'],
                   "uname"=>$rs['user_name'] ,
                   "content"=>$rs['content'],
                   "view"=>$rs['view'],   
                   "rdate"=>$rs['regdate'] 
               );
           }
           $result= json_encode($dataArr, JSON_UNESCAPED_UNICODE);
           if($dataArr){echo($result);}
        }break;   
}
?>