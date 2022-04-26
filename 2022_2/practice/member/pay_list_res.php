<?
include_once("$_SERVER[DOCUMENT_ROOT]/inc/xmlHeader.php");

$dataArr=array();
$mode=(int)$mode;

switch($mode)
{
    case 20 :
    {
        $SQL="SELECT * FROM {$my_db}.tm_pay_list WHERE id='{$user_id}'";
        $stmt=$pdo->prepare($SQL);
        $stmt->execute();
        $total_record=$stmt->rowCount();
        $dataArr['total_record']=$total_record;
        $total_price=0;
        while($rs=$stmt->fetch())
        {
            $dataArr['item'][]=
            [
                "pay_price"=>$rs['pay_price'],
                "wdate"=>$rs['wdate']
            ];
            $total_price+=$rs['pay_price'];
        }
        $dataArr['total_price']=$total_price;
    }
}
$result=json_encode($dataArr,JSON_UNESCAPED_UNICODE);
echo($result);
?>
