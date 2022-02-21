<?
include($_SERVER["DOCUMENT_ROOT"]."/practice/inc/xmlHeader.php");


$SQL = "SELECT * FROM db_dev.tx_news WHERE dvsn = $dvsn AND title LIKE '%{$text}%'";
$stmt=$pdo->prepare($SQL);
$stmt->execute();
$row=$stmt->fetchColumn();

$dataArr=array();
while($rs=$stmt->fetch())
{
    $dataArr['item'][]=array(
        "cover_img"=>$rs['cover_img'],
        "title"=>$rs['title'],
        "rdt"=>$rs['rdt']
    );
}
if(!$row){echo("검색결과가없습니다");}
$result=json_encode($dataArr,JSON_UNESCAPED_UNICODE);
if($dataArr){echo($result);}
?>
