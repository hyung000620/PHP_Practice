<?
include $_SERVER["DOCUMENT_ROOT"]."/inc/dbHeader.php";
include $_SERVER["DOCUMENT_ROOT"]."/member/Cipher.php";

$dataArr=array();
//페이지 이동
function move_page($msg)
{
	global $mode,$cfg_domain;
	$url="modify_profile.php";
	echo "
	<script type='text/javascript'>";
		if($msg){echo "alert('{$msg}');";}
		echo "location.replace('/member/{$url}');
	</script>";
	exit;	
}

switch((int)$mode)
{
    //개인정보 및 약관동의 창
    case 1 :
    {
        $dataArr['company']=TK_COMPANY;
        $dataArr['service']=file_get_contents($_SERVER["DOCUMENT_ROOT"]."/policy/service_guide_text.php");
        $dataArr['privacy']=file_get_contents($_SERVER["DOCUMENT_ROOT"]."/policy/privacy_guide_text.php");
    }break;
    //동의 여부 체크 후 회원가입 폼 생성
    case 2 :
    {
        if($agm1!=1 || $agm2 !=1){alertBack('이용약관 및 개인정보제공/수집에 모두 동의 해 주세요.');}
        $dataArr['item']=array('010','011','016','017','018','019');
    }break;
    //회원가입
    case 3 :
    {
        $is_success=1;
        #신규회원 > ID > member 중복 체크
        $SQL="SELECT * FROM {$my_db}.tm_member WHERE id='{$id}' LIMIT 0,1";
        $stmt=$pdo->prepare($SQL);
        $stmt->execute();
        $rs=$stmt->fetch();
        if($rs){$is_success=0;}
        
        #신규회원 > ID > member 중복체크(직원테이블)
        $SQL="SELECT * FROM {$my_db}.tz_staff WHERE id='{$id}'";
        $stmt=$pdo->prepare($SQL);
        $stmt->execute();
        $rs=$stmt->fetch();
        if($rs){$is_success=0;}
        $passkey=$cipher->Encrypt($pw);

        if($p1 && $p2 && $p3) $phone="{$p1}-{$p2}-{$p3}";
        if($is_success==1)
        {
            $SQL="INSERT INTO {$my_db}.tm_member SET ";
            $SQL.=" id='{$id}', passkey='{$passkey}', name='{$name}', reg_date='{$today}'";
            $SQL.=" ,r_mail='{$agm_mail}', sms='{$agm_sms}', email='{$email}', mobile='{$phone}'";
            $stmt=$pdo->prepare($SQL);
            $stmt->execute();
            $GAS_ID=strtolower($id);
            $GAS_NAME=$name;	
            setcookie("GAC_ID",base64_encode($GAS_ID),0,"/");
            setcookie("GAC_NAME",base64_encode($GAS_NAME),0,"/");
            $_SESSION["GAS_ID"]=base64_encode($GAS_ID);
            $_SESSION["GAS_NAME"]=base64_encode($GAS_NAME);
        }
        $dataArr['site_name']=$_site_name;
        $dataArr['success']=$is_success;
    }break;
    //회원 정보 변경
    case 4 :
    {
        $SQL="SELECT * FROM {$my_db}.tm_member WHERE id='{$user_id}'";
        $stmt=$pdo->prepare($SQL);
        $stmt->execute();
        $rs=$stmt->fetch();
        $pw_confirm=$cipher->Decrypt($rs['passkey']);
        $passkey=($password)?$cipher->Encrypt($password):$rs['passkey'];
        if($existing_pw!=$pw_confirm){ move_page("기존 비밀번호가 일치하지 않습니다.");}

        $SQL="UPDATE {$my_db}.tm_member SET passkey='{$passkey}'";
        $SQL.=",email='{$user_email}',mobile='{$phone}',sms='{$sms}',r_mail='{$r_mail}', pf_update=NOW()";
        $SQL.=" WHERE id='{$user_id}'";
        $stmt=$pdo->prepare($SQL);
        $stmt->execute();
            
        move_page('수정이 완료되었습니다.');
    }break;
    //회원 탈퇴
    case 5 :
    {
        $rand_pass=md5(uniqid());
        $passkey=$cipher->Encrypt($rand_pass);
        $SQL="UPDATE {$my_db}.tm_member SET name='탈퇴회원', passkey='{$passkey}',r_mail=0, sms=0, mobile='',email='', out_date=CURDATE() WHERE id='{$user_id}'";
        $stmt=$pdo->prepare($SQL);
        $stmt->execute();

        echo 
        "<script type='text/javascript'>
        alert('그 동안 저희 위드탱크를 이용해주셔서 감사합니다');
        location.href='/member/cert.php?mode=logout';
        </script>";
    }break;
    //결제 내역
    case 20 :
    {
        $SQL="SELECT * FROM {$my_db}.tm_pay_list WHERE id='{$user_id}'";
        $stmt=$pdo->prepare($SQL);
        $stmt->execute();
        $total_record=$stmt->rowCount();
        $dataArr['total_record']=$total_record;
        $total_price=0;
        $total_count=0;
        while($rs=$stmt->fetch())
        {
            $dataArr['item'][]=
            [
                "pay_price"=>$rs['pay_price'],
                "wdate"=>$rs['wdate'],
                "receipt"=>$rs['receipt']
            ];
            $total_price+=$rs['pay_price'];
            $total_count+=1;
        }
        $dataArr['total_price']=$total_price;
        $dataArr['total_count']=$total_count;
    }break;
    //아이디 실시간
    case 30:
    {
        if($user_id != NULL)
        {
            #회원
            $stmt=$pdo->prepare("SELECT * FROM {$my_db}.tm_member WHERE id='{$user_id}'");
            $stmt->execute();
            $row1=$stmt->fetch();
        
            #직원
            $stmt=$pdo->prepare("SELECT * FROM {$my_db}.tz_staff WHERE id='{$user_id}'");
            $stmt->execute();
            $row2=$stmt->fetch();
        }
        if($row1 || $row2){$dataArr['ment']="존재하는 ID 입니다";}
        else{$dataArr['ment']="사용가능한 ID 입니다";}
    }break;
}
$result=json_encode($dataArr,JSON_UNESCAPED_UNICODE);
echo($result);
?>