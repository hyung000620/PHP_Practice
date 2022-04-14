<?
include $_SERVER["DOCUMENT_ROOT"]."/inc/dbHeader.php";
//include $_SERVER["DOCUMENT_ROOT"]."/phplib/pbkdf2.compat.php";
include $_SERVER["DOCUMENT_ROOT"]."/member/Cipher.php";

if($phone1 && $phone2 && $phone3) $phone="{$phone1}-{$phone2}-{$phone3}";

//문자 및 메일수신 동의
$sms=($phone) ? $sms : 0;
$r_mail=($user_email) ? $r_mail : 0;

//가입가능 여부
$flag=true;

//페이지 이동
function move_page($msg)
{
	global $mode,$cfg_domain;
	$url=($mode=="new_member")? "register.php" : "modify_profile.php";
	echo "
	<script type='text/javascript'>";
		if($msg){echo "alert('{$msg}');";}
		echo "location.replace('/member/{$url}');
	</script>";
	exit;	
}

$today=date("Y-m-d");
if($flag)
{   
    switch($mode)
    {
        case 'new':
        {
            #신규회원 > ID > member 중복 체크
            $stmt=$pdo->prepare("SELECT idx FROM {$my_db}.tm_member WHERE id='{$user_id}' LIMIT 0,1");
            $stmt->execute();
            $rs=$stmt->fetch();
            if($rs){move_page("이미 등록된 ID 입니다! (106)");}
            
            //$passkey=create_hash($password);
            $passkey=$cipher->Encrypt($password);
            $SQL="INSERT INTO {$my_db}.tm_member SET ";
            $SQL.=" id='{$user_id}', passkey='{$passkey}', name='{$user_name}', reg_date='{$today}'";
            $SQL.=" ,r_mail='{$r_mail}', sms='{$sms}', email='{$user_email}', mobile='{$phone}'";
            $stmt=$pdo->prepare($SQL);
            $stmt->execute();

            $GAS_ID=strtolower($user_id);
            $GAS_NAME=$user_name;	
            setcookie("GAC_ID",base64_encode($GAS_ID),0,"/");
            setcookie("GAC_NAME",base64_encode($GAS_NAME),0,"/");
            $_SESSION["GAS_ID"]=base64_encode($GAS_ID);
            $_SESSION["GAS_NAME"]=base64_encode($GAS_NAME);
            
            echo "<script type='text/javascript'>location.href='/member/register_ok.php';</script>";
        }break;
        case 'modify':
        {   //30038ba
            $SQL="SELECT * FROM {$my_db}.tm_member WHERE id='{$user_id}'";
            $stmt=$pdo->prepare($SQL);
            $stmt->execute();
            $rs=$stmt->fetch();
            $pw_confirm=$cipher->Decrypt($rs['passkey']);
            $passkey=$cipher->Encrypt($password);
            
            if($existing_pw!=$pw_confirm){ move_page("기존 비밀번호가 일치하지 않습니다.");}

            $SQL="UPDATE {$my_db}.tm_member SET passkey='{$passkey}'";
            $SQL.=",email='{$user_email}',mobile='{$phone}',sms='{$sms}',r_mail='{$r_mail}', pf_update=NOW()";
            $SQL.=" WHERE id='{$user_id}'";
            $stmt=$pdo->prepare($SQL);
            $stmt->execute();
        
            alertBack('수정이 완료되었습니다.');            
        }break;
    }
}
?>
