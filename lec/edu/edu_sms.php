<?
include($_SERVER["DOCUMENT_ROOT"]."/inc/dbHeader.php");

$today=date("Y-m-d H:i:s");

$dataArr=array();

$SQL="SELECT rdate FROM {$my_db}.tl_edu WHERE edu_code={$edu_code}";
$stmt=$pdo->prepare($SQL);
$stmt->execute();
$rs=$stmt->fetch();
$rdate=$rs['rdate'];

##모집시간 10분전
$rdate10=date("Y-m-d H:i:s", strtotime($rdate."-10 minutes")); 
if($today>$rdate10) 
{
    $dataArr['result']=2;
}
else
{
    $SQL="SELECT mobile FROM {$my_db}.tm_member WHERE id='{$id}'";
    $stmt=$pdo->prepare($SQL);
    $stmt->execute();
    $rs=$stmt->fetch();
    $mobile=str_replace("-", "", $rs['mobile']);
    
    $SQL="SELECT * FROM {$my_db}.tl_edu_sms WHERE id='{$id}' AND edu_code='{$edu_code}'";
    $stmt=$pdo->prepare($SQL);
    $stmt->execute();
    $rs=$stmt->fetch();
    if($rs){$dataArr['result']=1;}
    else
    {
        $SQL="INSERT INTO {$my_db}.tl_edu_sms SET id='{$id}', edu_code={$edu_code}, wdtm=NOW()";
        $stmt=$pdo->prepare($SQL);
        $stmt->execute();

        $msg="신청하신 강의 모집시간 10분전입니다.";
        $rdate10=date("Y-m-d H:i:s", strtotime($rdate."-10 minutes"));
        send_sms($mobile,$msg,$id,$rdate10);
        $dataArr['result']=0;
    }
}


$result=json_encode($dataArr,JSON_UNESCAPED_UNICODE);
echo($result);
?>
