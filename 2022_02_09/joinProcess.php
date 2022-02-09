<?
session_start();

include $_SERVER["DOCUMENT_ROOT"] . "/inc/cfg.php";
include $_SERVER["DOCUMENT_ROOT"] . "/inc/pdo.php";

$user_name =$_POST[ 'user_name' ];
$user_id = $_POST[ 'user_id' ];
$user_pw = password_hash($_POST['user_pw'], PASSWORD_DEFAULT);
$user_email = $_POST[ 'user_email' ];
$user_mobile = $_POST[ 'user_mobile' ];
$user_address = $_POST[ 'user_address' ];

if(){

    echo("<script> window.alert('작성화지 않은 항목이 있습니다.')</script>");
}else{
$ISQL="INSERT INTO db_dev.tb_user SET user_name = '{$user_name}' , user_pw = '{$user_pw}', user_id = '{$user_id}', user_email = '{$user_email}', user_mobile = '{$user_mobile}', user_address = '{$user_address}' ";
$stmt=$pdo->prepare($ISQL);
$stmt->execute();
}
echo ("<script>window.alert('회원가입이 되었습니다'); location.href='/login.php'</script>");
?>
