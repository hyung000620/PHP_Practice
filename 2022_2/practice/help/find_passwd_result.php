<?
include($_SERVER["DOCUMENT_ROOT"]."/inc/dbHeader.php");
include($_SERVER["DOCUMENT_ROOT"]."/member/Cipher.php");

$SQL="SELECT * FROM {$my_db}.tm_member WHERE id = '{$user_id}' AND email = '{$user_email}' AND name = '{$user_name}'";
$stmt=$pdo->prepare($SQL);
$stmt->execute();
$rs=$stmt->fetch();
$ment="";
if($rs)
{
    //변경
    $tmp_pwd=mt_rand(100000,999999);
    $passkey = $cipher->Encrypt($tmp_pwd);
    $USQL="UPDATE {$my_db}.tm_member SET passkey = '{$passkey}' WHERE id = '{$user_id}' AND email = '{$user_email}' AND name = '{$user_name}'";
    $stmt=$pdo->prepare($SQL);
    $stmt->execute();
    
    //기록
    $today=date("Y-m-d H:i:s");
    $ISQL="INSERT INTO {$my_db}.tm_find_pw SET tmp_pwd={$tmp_pwd}, id='{$user_id}', email='{$user_email}', name='{$user_name}', send_date='{$today}'";
    $stmt=$pdo->prepare($ISQL);
    $stmt->execute();

    $ment.="<span>발급받은 임시비밀번호는 {$tmp_pwd} 입니다.</span></br>";
    $ment.="<span>로그인 후 비밀번호 변경을 꼭 해주세요.</span>";
}
else{$memt.="<span>등록된 사용자가 없습니다.</span>";}
?>
<div style="text-align:center;"><?=$ment?></div>
<div style="text-align:center;"><?=$ment?></div>