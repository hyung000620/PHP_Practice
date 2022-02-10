<?
session_start();

include $_SERVER["DOCUMENT_ROOT"] . "/inc/cfg.php";
include $_SERVER["DOCUMENT_ROOT"] . "/inc/pdo.php";

$user_id = $_POST[ 'user_id' ];
$user_pw = $_POST['user_pw'];
$SQL = "SELECT * FROM db_dev.tb_user WHERE user_id = '{$user_id}'";
$stmt = $pdo->prepare($SQL);
$stmt->execute();
$id = $stmt-> rowCount();
$user = $stmt -> fetch();

$chpw = password_verify( $user_pw, $user['user_pw']);
$sc = "<script>";
if($id == 1){
    if($chpw){
        $_SESSION[ 'user_id' ] = $user['user_id'];
        $_SESSION[ 'user_name'] = $user['user_name'];
        $sc .= "location.href='/view/section/list.php';";
    }else{
        $sc .= "window.alert('아이디 또는 비밀번호가 잘못되었습니다'); history.back();";
    }
}else{
    $sc .= "window.alert('아이디 또는 비밀번호가 잘못되었습니다'); history.back();";
}
$sc .= "</script>";
echo($sc);
?>