<?
$debug=false;
include $_SERVER["DOCUMENT_ROOT"]."/inc/dbHeader.php";
include $_SERVER["DOCUMENT_ROOT"]."/phplib/pbkdf2.compat.php";

//페이지 이동
function move_page($msg)
{
	global $url;
	echo "
	<script type='text/javascript'>";
		if($msg) echo "alert('{$msg}');";
		echo "location.href='/';
	</script>";
	exit;
}

if($mode=="logout")
{	
	$past=time()-3600;
	setcookie("GAC_ID","0",$past,"/");
	setcookie("GAC_NAME","0",$past,"/");
	setcookie("GAC_SSID","0",$past,"/");
	setcookie("GAC_CPN_CODE","0",$past,"/");
	setcookie("GAC_ABUSER","0",$past,"/");
	setcookie("GAC_LEVEL","0",$past,"/");
	setcookie("GAC_AUTH","0",$past,"/");
	setcookie("GAC_PTNR_PM","0",$past,"/");
	
	unset($_SESSION["GAS_ID"]);
	unset($_SESSION["GAS_NAME"]);
	unset($_SESSION["GAS_SSID"]);
	unset($_SESSION["GAS_CPN_CODE"]);
	unset($_SESSION["GAS_LEVEL"]);
	unset($_SESSION["GAS_AUTH"]);
	unset($_SESSION["GAC_PTNR_PM"]);
	
	setcookie("GAC_ID","0",$past,"/",".{$_root_domain}");	//모바일에서 넘어온 경우
}
else
{
	srand((double)microtime()*1000000);
	$GAS_SSID=uniqid(rand());
	$client_ip=getenv("REMOTE_ADDR");
	
	if($mode=="oneday")
	{
	  //일일 체험회원	
		$mobile="{$mobile1}-{$mobile2}-{$mobile3}";
		$stmt=$pdo->prepare("SELECT UNIX_TIMESTAMP(login) AS Fstamp FROM {$my_db}.tm_mcert WHERE mobile='{$mobile}' AND code='{$cert_code}' LIMIT 0,1");
		$stmt->execute();
		$rs=$stmt->fetch();
		if(!$rs){move_page("인증코드가 일치하지 않습니다.\\r\\n다시 확인해 주세요!","/member/oneday_free.php");}
		$Fstamp=$rs[Fstamp];
		if($Fstamp==0)	//최초접속
		{
			$Fstamp=time();
			$client_ip=getenv("REMOTE_ADDR");
			$stmt=$pdo->prepare("UPDATE {$my_db}.tm_mcert SET login=NOW(), ip='{$client_ip}' WHERE mobile='{$mobile}' AND code='{$cert_code}'");
			$stmt->execute();
		}
		$Tgap=time() - $Fstamp;
		if($Tgap > 86400){move_page("무료체험 기간이 만료되었습니다!","/member/agree.php");}
		
		$GAS_ID="CPN_USER";
		$GAS_NAME="쿠폰회원";
		$mType=4;
	}
	elseif($client_id=="{$_site_name}")
	{
	  //쿠폰회원
		$passwd=substr($passwd,-8);
		$stmt=$pdo->prepare("SELECT UNIX_TIMESTAMP(conn_time) AS Fstamp,edate,term FROM {$my_db}.tm_coupon WHERE passwd='{$passwd}' LIMIT 0,1");
		$stmt->execute();
		$rs=$stmt->fetch();
		if(!$rs){move_page("쿠폰의 비밀번호가 일치하지 않습니다.\\r\\n다시 확인해 주세요!");}
		
		$Fstamp=$rs[Fstamp];
		if($Fstamp==0)	//최초접속
		{
			$Fstamp=time();
			if(date("Y-m-d") > $rs[edate]){move_page("쿠폰의 등록기간이 만료되었습니다!");}
			$stmt=$pdo->prepare("UPDATE {$my_db}.tm_coupon SET conn_time=NOW() WHERE passwd='{$passwd}'");
			$stmt->execute();
		}
		$Tgap=time() - $Fstamp;
		if($Tgap > ($rs[term] * 86400)){move_page("쿠폰의 사용기간이 만료되었습니다!");}
		
		$GAS_ID="CPN_USER";
		$GAS_NAME="쿠폰회원";
		$client_id="CPN_".$passwd;
		
		setcookie("GAC_CPN_CODE",base64_encode($passwd),0,"/");
		$_SESSION["GAS_CPN_CODE"]=base64_encode($passwd);
		$mType=1;
	}
	else
	{
		$stmt=$pdo->prepare("SELECT id,name,passkey,ptnr_code,partner_pm,out_date FROM {$my_db}.tm_member WHERE id='{$client_id}' LIMIT 1");
		$stmt->execute();
		$rs=$stmt->fetch();
		if(!$rs){move_page("아이디와 비밀번호가 일치하지 않습니다.\\r\\n다시 확인해 주세요! [101]");}
		if(strlen($rs[passkey])==16)
		{
			$stmt=$pdo->prepare("SELECT id,name,ptnr_code,partner_pm FROM {$my_db}.tm_member WHERE id='{$client_id}' AND passkey=PASSWORD('{$passwd}') LIMIT 1");
			$stmt->execute();
			$rs=$stmt->fetch();
			if(!$rs){move_page("아이디와 비밀번호가 일치하지 않습니다.\\r\\n다시 확인해 주세요! [102");}
			$passkey=create_hash($passwd);
			$stmt=$pdo->prepare("UPDATE {$my_db}.tm_member SET passkey='{$passkey}' WHERE id='{$client_id}'");
			$stmt->execute();
		}
		else
		{
			if(validate_password($passwd,$rs[passkey])==false){move_page("아이디와 비밀번호가 일치하지 않습니다.\\r\\n다시 확인해 주세요! [103]");}
			if($rs[out_date]!="0000-00-00") {move_page("아이디와 비밀번호가 일치하지 않습니다.\\r\\n다시 확인해 주세요! [104]");	exit;}
		}
		
		$GAS_ID=$rs[id];
		$GAS_NAME=$rs[name];

		$usr_agnt=$_SERVER['HTTP_USER_AGENT'];
		$svrNm=$_SERVER['SERVER_NAME'];		
		$stmt=$pdo->prepare("UPDATE {$my_db}.tm_member SET login=NOW(),ip='{$client_ip}' WHERE id='{$client_id}'");
		$stmt->execute();

		## 테이블 변경 id, ip, user_agent,conn_dtm,conn_dvsn >> 일반(0),관리자(1),solar(2)
		$conn_dvsn=($conn_dvsn>0) ? $conn_dvsn : 0;
		$stmt=$pdo->prepare("INSERT INTO {$my_db}.tm_conn(id, ip, usr_agnt, conn_dtm, conn_dvsn) VALUES('{$client_id}', '{$client_ip}', '{$usr_agnt}', NOW(), '{$conn_dvsn}')");
		$stmt->execute();
		
		##INSERT는 테이블에 무조건 저장하는 것
		//REPLACE는 저장될 테이블에 데이터가 없으면 저장하고 있으면 그것을 삭제하고 저장
		$stmt=$pdo->prepare("REPLACE INTO {$my_db}.tm_live SET id='{$client_id}',ctime=UNIX_TIMESTAMP(),utime=UNIX_TIMESTAMP()");
		$stmt->execute();
		$mType=2;	
	}

	//if($_COOKIE['vType'] && $_COOKIE['vType'] != 2){visitProc(2,$mType);}
	
	//동시 접속 체크용
	$stmt=$pdo->prepare("SELECT idx FROM {$my_db}.tm_ssid WHERE id='{$client_id}' LIMIT 0,1");
	$stmt->execute();
	$rs=$stmt->fetch();
	if($rs)
	{
	  $SQL="UPDATE {$my_db}.tm_ssid SET ssid='{$GAS_SSID}',ip='{$client_ip}',mdate=NOW() WHERE idx='{$rs[idx]}'";
	}
	else
	{
	  $SQL="INSERT INTO {$my_db}.tm_ssid(id,ssid,ip,mdate) VALUES('{$client_id}','{$GAS_SSID}','{$client_ip}',NOW())";
	}
	$stmt=$pdo->prepare($SQL);
	$stmt->execute();
		
	($chk_save_id)  ? setcookie("GAC_SAVED_ID",$GAS_ID,time()+(3600*24*31),"/") : setcookie("GAC_SAVED_ID","0",(time()-3600),"/");
	($chk_save_pwd) ? setcookie("GAC_SAVED_PWD",$passwd,time()+(3600*24*31),"/") : setcookie("GAC_SAVED_PWD","0",(time()-3600),"/");
		
	setcookie("GAC_ID",base64_encode($GAS_ID),0,"/",".{$_root_domain}");
	setcookie("GAC_NAME",base64_encode($GAS_NAME),0,"/",".{$_root_domain}");
	setcookie("GAC_SSID",base64_encode($GAS_SSID),0,"/",".{$_root_domain}");
	setcookie("GAC_PTNR_PM",base64_encode($partner_pm),0,"/",".{$_root_domain}");
	setcookie("GAC_ABUSER","0",0,"/");
	
	$_SESSION["GAS_ID"]=base64_encode($GAS_ID);
	$_SESSION["GAS_NAME"]=base64_encode($GAS_NAME);
	$_SESSION["GAS_SSID"]=base64_encode($GAS_SSID);
	$_SESSION["GAC_PTNR_PM"]=base64_encode($partner_pm);
}

move_page("");
?>