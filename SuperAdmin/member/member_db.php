<?
//$debug=true;
$page_code=1010;
include $_SERVER["DOCUMENT_ROOT"]."/SuperAdmin/common/db_header.php";
include $_SERVER["DOCUMENT_ROOT"]."/phplib/pbkdf2.compat.php";

set_time_limit(300);	//5분
$path1=$_SERVER["DOCUMENT_ROOT"]."/Photo/GBC/Common/fc/";
$path2=$_SERVER["DOCUMENT_ROOT"]."/Photo62/GBC/Common/fc/";
$allow_ext=array("jpg","gif","png");
$max_size=1024*1024*3;	//용량 제한(3M);

switch($mode)
{
	case "agent" :
		$book=6;		//기본으로 해당없음 처리
		$jisa_code=100;
		if($phone3)		$phone="{$phone1}-{$phone2}-{$phone3}";
		if($mobile3)	$mobile="{$mobile1}-{$mobile2}-{$mobile3}";
		//$result=sql_query("SELECT id FROM {$my_db}.tm_member WHERE id='{$reg_id}' LIMIT 0,1");
		//$rs=mysql_fetch_array($result);
		$stmt=$pdo->prepare("SELECT id FROM {$my_db}.tm_member WHERE id='{$reg_id}' LIMIT 0,1");
		$stmt->execute();
		$rs=$stmt->fetch();
		if($rs)
		{
			echo "<script type='text/javascript'>alert('이미 사용중인 id 입니다.'); history.back();</script>";
			exit;
		}
		$passkey=create_hash($passwd);
		$SQL ="INSERT INTO {$my_db}.tm_member(id,passkey,name,phone,mobile,zipcode,address1,address2,m_memo,reg_date) ";
		$SQL.="VALUES('{$reg_id}','{$passkey}','{$reg_name}','{$phone}','{$mobile}','{$zipcode}','{$address1}','{$address2}','{$m_memo}',CURDATE())";
		//sql_query($SQL);
		$stmt=$pdo->prepare($SQL);
		$stmt->execute();
		if($reg_more)
		{
			echo "<script type='text/javascript'>location.href='member_list.php?reg_more={$reg_more}'</script>";
			exit;
		}
		else
		{			
			$next_url="member_detail.php?id={$reg_id}";
		}
		break;
		
	case "profile_edit" :
		//$result=sql_query("SELECT * FROM {$my_db}.tm_member WHERE id='{$user_id}'");
		//$rs=mysql_fetch_array($result);
		$stmt=$pdo->prepare("SELECT * FROM {$my_db}.tm_member WHERE id='{$user_id}'");
		$stmt->execute();
		$rs=$stmt->fetch();
		$uidx=$rs[idx];
		if($chk_new_pwd=="y")
		{
			$new_passkey=create_hash($passwd);
			$pwd_condition=",passkey='{$new_passkey}'";
		}
		$SQL ="UPDATE {$my_db}.tm_member SET name='{$user_name}',edu_dc='{$edu_dc}',mobile='{$mobile}',phone='{$phone}',zipcode='{$zipcode}',address1='{$address1}',address2='{$address2}',m_memo='{$m_memo}',";
		$SQL.="fax='{$fax}',email='{$email}',sms='{$sms}',r_mail='{$r_mail}',partner='{$partner}', partner_pm='{$partner_pm}',ptnr_code='{$ptnr_code}',youtuber='{$youtuber}',bid_staff='{$bid_staff}',dc_rate='{$dc_rate}',dc_sdate='{$dc_sdate}',dc_edate='{$dc_edate}',pay_custom='{$pay_custom}' ";
		$SQL.="{$pwd_condition} {$file_condition} WHERE id='{$user_id}'";
		//sql_query($SQL);		
		$stmt=$pdo->prepare($SQL);
		$stmt->execute();
		$next_url="member_detail.php?id={$user_id}&params={$params}";
		break;
		
	case "point" :
		if($point_type=="del")
		{
			$SQL="DELETE FROM {$my_db}.tm_point WHERE idx='{$idx}' AND id='{$user_id}'";
		}
		else
		{
			$point=($point_type=="plus") ? $point : $point * -1;
			$SQL="INSERT INTO {$my_db}.tm_point(id,point,pdate,rec_id,staff,memo) values('{$user_id}','{$point}',CURDATE(),'{$rec_id}','{$staff}','{$memo}')";
		}
		//sql_query($SQL);
		$stmt=$pdo->prepare($SQL);
		$stmt->execute();
			
		$url="point.php";
		break;
		
	case "log_del" :
		//sql_query("UPDATE {$my_db}.tm_member SET member_log='' WHERE id='{$user_id}'");
		$stmt=$pdo->prepare("UPDATE {$my_db}.tm_member SET member_log='' WHERE id='{$user_id}'");
		$stmt->execute();
		$next_url="member_detail.php?id={$user_id}&params={$params}";
		break;
		
	case "partner" :	//일매출에서 소속단체 일괄저장
		$id_list=str_replace("\\","",$id_list);
		$id_arr=explode(",",$id_list);
		//print_r($id_arr);
		foreach($id_arr as $id)
		{
			$ptnr_arr=array();
			$partner="";
			//$result=sql_query("SELECT partner FROM {$my_db}.tm_member WHERE id='{$id}' LIMIT 0,1");
			//$rs=mysql_fetch_array($result);
			$stmt=$pdo->prepare("SELECT partner FROM {$my_db}.tm_member WHERE id='{$id}' LIMIT 0,1");
			$stmt->execute();
			$rs=$stmt->fetch();
			if($rs[partner])
			{
				$ptnr_arr=explode("|",$rs[partner]);
				if(in_array($pt_code,$ptnr_arr)) continue;
				else
				{
					array_push($ptnr_arr,$pt_code);
				}	
			}
			else
			{
				array_push($ptnr_arr,$pt_code);
			}
			$partner=implode("|",$ptnr_arr);
			//echo $partner."<br>";
			//sql_query("UPDATE {$my_db}.tm_member SET partner='{$partner}' WHERE id='{$id}'");
			$stmt=$pdo->prepare("UPDATE {$my_db}.tm_member SET partner='{$partner}' WHERE id='{$id}'");
		$stmt->execute();
		}
		$next_url="./../stats/sales_day.php";
		break;
}

function File_Upload($fileArr)
{
	global $path1,$path2,$allow_ext,$max_size,$user_id,$db_good,$my_db,$uidx,$_FILES;
	
	foreach($fileArr as $file)
	{
		$file_size=$_FILES[$file][size];
		$real_name=$_FILES[$file][name];
		$temp_name=$_FILES[$file][tmp_name];
		if(!$file_size) continue;	//첨부파일이 없다면 pass
		if($file_size > $max_size)	MessageBox("파일크기가 허용치를 초과하였습니다!");
		
		$name_ext=explode(".",$real_name);
		$ext=strtolower($name_ext[1]);	//확장자
		$file_name=strtolower("{$file}_{$uidx}.{$ext}");

		if(!in_array($ext,$allow_ext))	MessageBox("허용되지 않는 파일형식 입니다!");
		if(!copy($temp_name,$path1.$file_name))	MessageBox("파일업로드가 제대로 되지 않았습니다!");
		if(!move_uploaded_file($temp_name,$path2.$file_name))	MessageBox("파일업로드가 제대로 되지 않았습니다!");
		$upfileArr[$file]=$file_name;
	}
	if($upfileArr)
	{
		foreach($upfileArr as $key => $val)
		{
			$condiArr[]="{$key}='{$val}'";
		}
		$condition=implode("," , $condiArr);
		$SQL="UPDATE {$my_db}.tm_member SET {$condition} WHERE id='{$user_id}'";
		sql_query($SQL);
	}
}

function File_Delete($file_name)
{
	global $path1,$path2;
		
	if(file_exists($path1.$file_name))
	{
		if(!unlink($path1.$file_name)) MessageBox("파일삭제 에러-{$file_name}");
	}
	if(file_exists($path2.$file_name))
	{
		if(!unlink($path2.$file_name)) MessageBox("파일삭제 에러-{$file_name}");
	}
}

function MessageBox($msg)
{
	echo "
	<script language='javascript'>
		alert('{$msg}');
		history.back();
	</script>";
	exit;
}
?>

<script type="text/javascript">
	alert("처리 되었습니다 ^^");
	location.href="<?=$next_url?>";
</script>