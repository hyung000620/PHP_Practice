<?php
    include '../header/header.php';

    $user_name = $_SESSION['user_name'];
    $user_id = $_SESSION['user_id'];

    $user_pw = $_POST['user_pw'];
    $new_user_pw = $_POST['new_user_pw'];
    $new_user_pw2 = $_POST['new_user_pw2'];

    $SQL = "SELECT * FROM db_dev.tb_user WHERE user_id = '{$user_id}'";
    $stmt = $pdo->prepare($SQL);
    $stmt -> execute();
    $user = $stmt -> fetch();

    $chpw = password_verify( $user_pw , $user['user_pw']);
    $sc = "<script>";
    if($user_pw != null){
        if($chpw){
            if($new_user_pw === $new_user_pw2){
                $new_user_pw = password_hash($new_user_pw, PASSWORD_DEFAULT);
                $USQL = "UPDATE db_dev.tb_user SET user_pw = '{$new_user_pw}'";
                $stmt = $pdo ->prepare($USQL);
                $stmt -> execute();
                $sc .= "alert('정보수정이 완료되었습니다.');";
            }else{
                $sc .= "alert('비밀번호가 일치하지 않습니다.');";
            }
        }else{
            $sc .= "alert('비밀번호가 잘못되었습니다.');";
        }
    }
    

    function withdrawal(){

    }

    $sc .= "</script>";
    echo $sc;
?>
<h1>회원정보 수정</h1>
<div>이름 : <? echo $user_name?></div>
<div>아이디 : <? echo $user_id?></div>

<form method="post" action="/view/section/myPage.php">
    현재 비밀번호 : <input type="password" name="user_pw">
    <br>새 비밀번호 : <input type="password" name="new_user_pw">
    <br>새 비밀번호 확인 : <input type="password" name="new_user_pw2">
    <br><input type="submit" value="정보수정">
</form>

<input type="button" onclick="del()" value="회원탈퇴">

<script>
    function del(){
        if(confirm('정말삭제하시겠습니다')){
            document.form.submit();
        }else{
            return;
        }
    }
</script>