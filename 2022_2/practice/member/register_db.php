<?
include $_SERVER["DOCUMENT_ROOT"]."/inc/dbHeader.php";
include $_SERVER["DOCUMENT_ROOT"]."/phplib/pbkdf2.compat.php";

if(!$client_id && !$user_id)
{
	echo "
	<script type='text/javascript'>
		alert('정식 경로를 이용해 주세요~!');
		history.back();
	</script>";
	exit;
}

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


if($phone1 && $phone2 && $phone3) $phone="{$phone1}-{$phone2}-{$phone3}";
if($mobile1 && $mobile2 && $mobile3) $mobile="{$mobile1}-{$mobile2}-{$mobile3}";

if($email_id)
{
	$mail_domain=($selMailComp)	? $selMailComp : $txtMailComp;
	$email=($mail_domain) ? $email_id."@".$mail_domain : "";
}

//문자 및 메일수신 동의
$sms=($mobile) ? $sms : 0;
$r_mail=($email) ? $r_mail : 0;

switch ($mode)
{
  case "new_member" :
  {
  	## NAME, ID, PW 검증 ##
  	#신규회원 > NAME
  	if(!preg_match("/^[가-힣\xA1-\xFEa-zA-Z0-9_]{2,20}$/", $user_name)){move_page("한글,영문대.소,숫자(2~20)만 가능합니다! (101)");}
    #신규회원 > ID > 기본규칙
  	if(!preg_match("/^[a-zA-Z0-9_]{6,30}$/", $user_id)){move_page("영문대.소, 숫자(6~30)만 가능합니다! (102)");}
  	#신규회원 > ID > 단어필터링 (function.php)
  	$idchk_=txt_filtering($user_id);
    if($idchk_){move_page("ID에 사용할 수 없는 단어가 포함되어 있습니다! (103)"); }
    #신규회원 > ID > 검정체크
    if($id_chk!=1){move_page("ID를 확인해 주세요! (104)"); }
    #신규회원 > ID > 중복체크
    if($id_exist!=1){move_page("ID중복을 확인해 주세요! (105)"); }
  	#신규회원 > ID > member 중복 체크
  	$stmt=$pdo->prepare("SELECT idx FROM {$my_db}.tm_member WHERE id='{$user_id}' LIMIT 0,1");
  	$stmt->execute();
  	$rs=$stmt->fetch();
  	if($rs){move_page("이미 등록된 ID 입니다! (106)");}
  	#신규회원 > ID > staff 중복체크
  	$stmt=$pdo->prepare("SELECT id FROM {$my_db}.tz_staff WHERE id='{$user_id}' || mobile='{$mobile}' LIMIT 0,1");
  	$stmt->execute();
  	$rs=$stmt->fetch();
  	if($rs)
  	{
  		if($mobile==$rs[mobile]){	move_page("이미 등록된 휴대폰 번호 입니다! (107)");}
  		else{move_page("이미 등록된 ID 입니다! (108)");}
  	}
    #신규회원 > PW >  비밀번호 확인
    if($ps_chk!=1 || $psv_chk!=1 || !$passwd || !$passwd_verify){move_page("비밀번호를 확인해 주세요! (109)");}
  	#신규회원 > PW > 비번검증 (function.php)
  	$pw_chk_=regular_pw_chk($passwd);
  	if($pw_chk_==1){move_page("비밀번호를 확인해 주세요! (110)");}
  	#신규회원 > PW == ID
  	if($user_id==$passwd){move_page("아이디와 비밀번호가 같습니다! (111)");}
    ## ID, PW 검증 ##	
  	#추천인 ID		
  	$rec_id=trim($rec_id);
  	if($user_id==$rec_id) $rec_id="";
  	
  	//$referer=($_COOKIE[REFERER_PAGE]) ? $_COOKIE[REFERER_PAGE] : "";
  	$passkey=create_hash($passwd);
  	$SQL ="INSERT INTO {$my_db}.tm_member (id,name,passkey,mobile,zipcode,address1,address2,email,r_mail,sms,rec_id,reg_date) ";
  	$SQL.="VALUES ('{$user_id}','{$user_name}','{$passkey}','{$mobile}','{$zipcode}','{$address1}','{$address2}','{$email}','{$r_mail}','{$sms}','{$rec_id}',CURDATE())";
  	$stmt=$pdo->prepare($SQL);
  	$stmt->execute();
  	
  	#신규회원 > 로그인 처리 > 결제완료 페이지
  	$GAS_ID=strtolower($user_id);
  	$GAS_NAME=$user_name;	
  	setcookie("GAC_ID",base64_encode($GAS_ID),0,"/");
  	setcookie("GAC_NAME",base64_encode($GAS_NAME),0,"/");
  	$_SESSION["GAS_ID"]=base64_encode($GAS_ID);
  	$_SESSION["GAS_NAME"]=base64_encode($GAS_NAME);
  	
  	echo "<script type='text/javascript'>	location.href='/member/register_ok.php?mode={$mode}';	</script>";
  	exit;
  } break;
  case "profile_edit" :
  {
    if(!$client_id){exit;}
  	#회원정보수정
  	if($new_passwd)
  	{
  		# PW > 기본검증
  		if($ps_chk!=1 || $psv_chk!=1 || !$passwd_verify){move_page("비밀번호를 확인해 주세요!(112)");}
  		# PW > 비번규칙 검증 (function.php)
  		$pw_chk_=regular_pw_chk($new_passwd);
  		if($pw_chk_==1){move_page("비밀번호를 확인해 주세요!(113)");}
  	}
  	$stmt=$pdo->prepare("SELECT id,name,passkey FROM {$my_db}.tm_member WHERE id='{$client_id}' LIMIT 1");
  	$stmt->execute();
  	$rs=$stmt->fetch();
  	if(!$rs){move_page("아이디와 비밀번호가 일치하지 않습니다!(114)");}
  	else{if(validate_password($new_passwd,$rs[passkey])==true){move_page("사용중인 비밀번호와 동일합니다!(115)");}}
  	//기존 old_password > sha256 암호화처리
  	if(strlen($rs[passkey])==16)
  	{
  		$stmt=$pdo->prepare("SELECT id,name FROM {$my_db}.tm_member WHERE id='{$client_id}' AND passkey=PASSWORD('{$passwd}') LIMIT 1");
  		$stmt->execute();
  		$rs=$stmt->fetch();
  		if(!$rs){move_page("아이디와 비밀번호가 일치하지 않습니다.\\r\\n다시 확인해 주세요!(116)");}
  		
  		#oldpasswd > sha256
  		if(!$new_passwd && $passwd)
  		{
  			$passkey=create_hash($passwd);
  			$stmt=$pdo->prepare("UPDATE {$my_db}.tm_member SET passkey='{$passkey}' WHERE id='{$client_id}'");
  		  $stmt->execute();
  		}
  	}
  	else
  	{
  		if(validate_password($passwd,$rs[passkey])==false){move_page("현재 비밀번호를 확인하세요!(117)");}
  	}
  	
  	$SQL ="UPDATE {$my_db}.tm_member SET address1='{$address1}',address2='{$address2}',zipcode='{$zipcode}',mobile='{$mobile}',phone='{$phone}',email='{$email}',r_mail='{$r_mail}',sms='{$sms}',pf_update=CURDATE() ";
  	if($new_passwd)
  	{
  		$new_passkey=create_hash($new_passwd);
  		$SQL.=",passkey='{$new_passkey}' ";
  	}
  	
  	##주의##
  	$SQL.="WHERE id='{$client_id}'";
  	$stmt=$pdo->prepare($SQL);
  	$stmt->execute();
  	##주의##
  	
  	echo "
  	<script type='text/javascript'>
  		alert('수정 되었습니다!');
  		location.href='/member/modify_profile.php';
  	</script>";
  	exit;
  } break;
}
?>
