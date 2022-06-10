<?
//$debug=true;
$page_code=1010;
include $_SERVER["DOCUMENT_ROOT"]."/SuperAdmin/common/db_header.php";

//주문번호
srand((double)microtime()*1000000);
$order_no=date("YmdHis").rand(1000,9999);
$today=date("Y-m-d");

if($paykind == 9) $amt=0;

$dc_flag=false;
$piArr=explode(",",$smp);
foreach($piArr as $val)
{
	$apmArr=explode(":",$val);
	$stateArr[]=$apmArr[0];	
	$monthArr[]=$apmArr[1];
	$smoneyArr[]=($paykind==9) ? 0 : $apmArr[2];
	if($apmArr[0]==99 && $apmArr[1] >= 12) $dc_flag=true;
}

//결제 요약 처리
$pay_price=($paykind==9) ? 0 : $amt;
if(!$dc_flag) $dc_rate=0;
//sql_query("INSERT INTO {$my_db}.tm_pay_list(order_no,id,point,pay_price,dc_rate,rec_id,wdate) VALUES('{$order_no}','{$user_id}','{$use_point}','{$pay_price}','{$dc_rate}','{$rec_id}',CURDATE())");
$stmt=$pdo->prepare("INSERT INTO {$my_db}.tm_pay_list(order_no,id,point,pay_price,dc_rate,rec_id,wdate) VALUES('{$order_no}','{$user_id}','{$use_point}','{$pay_price}','{$dc_rate}','{$rec_id}',CURDATE())");
$stmt->execute();

if(!$pay_code) $pay_code=100;

if($paykind==9 && $free_month)
{
	setcookie("GAC_PAY_MEMO",$memo,0,"/");		//무료 N개월 단체 가입시 메모
	$bankcode=0;
}

switch ($pay_code)
{
	case 100 :	//경매정보
		foreach($stateArr as $key => $val)
		{	
			//$result=sql_query("SELECT * FROM {$my_db}.tm_pay_result WHERE id='{$user_id}' AND pay_code='{$pay_code}' AND state='{$stateArr[$key]}' LIMIT 0,1");
			//$rs=mysql_fetch_array($result);
			$stmt=$pdo->prepare("SELECT * FROM {$my_db}.tm_pay_result WHERE id='{$user_id}' AND pay_code='{$pay_code}' AND state='{$stateArr[$key]}' LIMIT 0,1");
			$stmt->execute();
			$rs=$stmt->fetch();
			if($rs)
			{
				$SQL ="INSERT INTO {$my_db}.tm_pay_history(order_no,id,pay_code,paykind,months,paydate,paytime,payname,bankcode,money,state,sector,validity,tempdate,startdate,staff,memo) ";
				$SQL.="VALUES('{$rs[order_no]}','{$rs[id]}','{$rs[pay_code]}','{$rs[paykind]}','{$rs[months]}','{$rs[paydate]}','{$rs[paytime]}','{$rs[payname]}','{$rs[bankcode]}','{$rs[money]}','{$rs[state]}',";
				$SQL.="'{$rs[sector]}','{$rs[validity]}','{$rs[tempdate]}','{$rs[startdate]}','{$rs[staff]}','{$rs[memo]}')";
				//sql_query($SQL);
				$stmt=$pdo->prepare($SQL);
				$stmt->execute();
					
				$expire=($rs[validity] > $today) ? "DATE_ADD(validity,INTERVAL {$monthArr[$key]} MONTH)" : "DATE_ADD(CURDATE(),INTERVAL {$monthArr[$key]} MONTH)";
				$SQL ="UPDATE {$my_db}.tm_pay_result SET order_no='{$order_no}',id='{$user_id}',paykind='{$paykind}',months='{$monthArr[$key]}',paydate=CURDATE(),paytime=CURTIME(),payname='{$payname}',";
				$SQL.="bankcode='{$bankcode}',money='{$smoneyArr[$key]}',state='{$stateArr[$key]}',sector='',validity={$expire},tempdate='',startdate=CURDATE(),";
				$SQL.="staff='{$client_id}',memo='{$memo}' WHERE idx='{$rs[idx]}' AND id='{$user_id}'";
				//sql_query($SQL);
				$stmt=$pdo->prepare($SQL);
				$stmt->execute();
			}
			else	//신규결제
			{
				$SQL ="INSERT INTO {$my_db}.tm_pay_result(order_no,id,pay_code,paykind,months,paydate,paytime,bankcode,payname,money,state,validity,startdate,memo,staff) ";
				$SQL.="VALUES('{$order_no}','{$user_id}','{$pay_code}','{$paykind}','{$monthArr[$key]}',CURDATE(),CURTIME(),'{$bankcode}','{$payname}','{$smoneyArr[$key]}','{$stateArr[$key]}',";
				$SQL.="DATE_ADD(CURDATE(),INTERVAL {$monthArr[$key]} MONTH),CURDATE(),'{$memo}','{$client_id}')";
				//sql_query($SQL);
				$stmt=$pdo->prepare($SQL);
				$stmt->execute();
			}
		}
		break;
	
	case 101 : //동영상 강좌
		foreach($stateArr as $key => $val)
		{	
			//$result=sql_query("SELECT * FROM {$my_db}.tm_pay_result WHERE id='{$user_id}' AND pay_code='{$pay_code}' AND sector='{$stateArr[$key]}' LIMIT 0,1");
			//$rs=mysql_fetch_array($result);
			$stmt=$pdo->prepare("SELECT * FROM {$my_db}.tm_pay_result WHERE id='{$user_id}' AND pay_code='{$pay_code}' AND sector='{$stateArr[$key]}' LIMIT 0,1");
			$stmt->execute();
			$rs=$stmt->fetch();
			if($rs)
			{
				$SQL ="INSERT INTO {$my_db}.tm_pay_history(order_no,id,pay_code,paykind,months,paydate,paytime,payname,bankcode,money,state,sector,validity,tempdate,startdate,vp_sdate,vp_edate,sp_sdate,sp_edate,staff,memo) ";
				$SQL.="VALUES('{$rs[order_no]}','{$rs[id]}','{$rs[pay_code]}','{$rs[paykind]}','{$rs[months]}','{$rs[paydate]}','{$rs[paytime]}','{$rs[payname]}','{$rs[bankcode]}','{$rs[money]}','{$rs[state]}',";
				$SQL.="'{$rs[sector]}','{$rs[validity]}','{$rs[tempdate]}','{$rs[startdate]}','{$rs[vp_sdate]}','{$rs[vp_edate]}','{$rs[sp_sdate]}','{$rs[sp_edate]}','{$rs[staff]}','{$rs[memo]}')";
				//sql_query($SQL);
				$stmt=$pdo->prepare($SQL);
				$stmt->execute();
				
				$expire=($rs[validity] > $today) ? "DATE_ADD(validity,INTERVAL {$monthArr[$key]} DAY)" : "DATE_ADD(CURDATE(),INTERVAL {$monthArr[$key]} DAY)";
				$SQL ="UPDATE {$my_db}.tm_pay_result SET order_no='{$order_no}',id='{$user_id}',paykind='{$paykind}',months='{$monthArr[$key]}',paydate=CURDATE(),paytime=CURTIME(),payname='{$payname}',";
				$SQL.="bankcode='{$bankcode}',money='{$smoneyArr[$key]}',state='',sector='{$stateArr[$key]}',validity={$expire},tempdate='',startdate=CURDATE(),";
				$SQL.="staff='{$client_id}',memo='{$memo}' WHERE idx='{$rs[idx]}' AND id='{$user_id}'";
				//sql_query($SQL);
				$stmt=$pdo->prepare($SQL);
				$stmt->execute();
			}
			else	//신규결제
			{
				$expire="DATE_ADD(CURDATE(),INTERVAL {$monthArr[$key]} DAY)";
				$SQL ="INSERT INTO {$my_db}.tm_pay_result(order_no,id,pay_code,paykind,months,paydate,paytime,bankcode,payname,money,sector,validity,startdate,memo,staff) ";
				$SQL.="VALUES('{$order_no}','{$user_id}','{$pay_code}','{$paykind}','{$monthArr[$key]}',CURDATE(),CURTIME(),'{$bankcode}','{$payname}','{$smoneyArr[$key]}','{$stateArr[$key]}',";
				$SQL.="{$expire}, CURDATE(),'{$memo}','{$client_id}')";
				//sql_query($SQL);
				$stmt=$pdo->prepare($SQL);
				$stmt->execute();
			}
		}
		break;
    case 102 : //경매교육
        #회원정보
        $stmt=$pdo->prepare("SELECT * FROM {$my_db}.tm_member WHERE id='{$user_id}' LIMIT 0,1");
        $stmt->execute();
        $rs=$stmt->fetch();
        $username=$rs['name'];
        $partner=$rs['partner'];
        if($partner)
        {
            $stmt=$pdo->prepare("SELECT sangho FROM {$my_db}.tz_partner WHERE code='{$partner}'");
            $stmt->execute();
            $rs=$stmt->fetch();
            $partner=$rs['sangho'];
        }

        $stmt=$pdo->prepare("SELECT * FROM {$my_db}.tl_edu WHERE 1");
  	    $stmt->execute();
        while($rs=$stmt->fetch()){$pi[$rs['edu_code']]=array("area" => $rs['edu_title'], "srv_area" => $rs['edu_teacher'], "price"=>$rs['edu_pay'],"place"=>$rs['edu_addr']);}
        
        $srv_price=0;
        $smp_arr=explode(",",$smp);
        $arr_=array();
        foreach($smp_arr as $v)
        {
        list($state,$month,$price)=explode(":",$v);
        $month_str=($month==0)?"오프라인":"온라인";
        array_push($arr_,$pi[$state]['area'].">". $month_str." ");
        $srv_price=$srv_price+$pi[$state]['price'][$month];
        }
        $stateArea=implode(",",$arr_);

        $ISQL="INSERT INTO {$my_db}.tm_pay_log SET id='{$user_id}',status='DONE', order_no='{$order_no}', pay_opt='{$pay_opt}', pay_code='{$pay_code}', goods='{$stateArea}', name='{$username}', partner='{$partner}', smp='{$smp}', bank='{$bankCode}', srv_price='{$srv_price}', pay_price='{$amt}', dc_rate='{$dc_rate}', wdate=NOW(), order_ip=''";
        $stmt=$pdo->prepare($ISQL);
        $stmt->execute();

        foreach($stateArr as $key => $val)
		{	
            $expire="DATE_ADD(CURDATE(),INTERVAL {$monthArr[$key]} DAY)";
            $SQL ="INSERT INTO {$my_db}.tm_pay_result(order_no,id,pay_code,paykind,months,paydate,paytime,bankcode,payname,money,sector,validity,startdate,memo,staff) ";
            $SQL.="VALUES('{$order_no}','{$user_id}','{$pay_code}','{$paykind}','{$monthArr[$key]}',CURDATE(),CURTIME(),'{$bankcode}','{$payname}','{$smoneyArr[$key]}','{$stateArr[$key]}',";
            $SQL.="{$expire}, CURDATE(),'{$memo}','{$client_id}')";
            $stmt=$pdo->prepare($SQL);
            $stmt->execute();
            
            #선착순 카운팅
            if($monthArr[$key]==0)
            {
                $SQL="UPDATE {$my_db}.tl_edu SET pay_people=(pay_people+1) WHERE edu_code = '{$stateArr[$key]}'";
                $stmt=$pdo->prepare($SQL);
                $stmt->execute();
            }
		}
		break;
}

if($paykind != 9)
{
	//$result=sql_query("select mobile from {$my_db}.tm_member where id='{$user_id}' limit 1");
	//$rs=mysql_fetch_array($result);
	$stmt=$pdo->prepare("select mobile from {$my_db}.tm_member where id='{$user_id}' limit 1");
	$stmt->execute();
	$rs=$stmt->fetch();
	if($rs)
	{
		$msg="▣탱크옥션▣\\r\\n연결되었습니다.\\r\\n결제,상담 등은 카톡 https://bit.ly/38LJNqZ";
		$mobile=str_replace("-", "", $rs[mobile]);
		send_sms($mobile,$msg,$user_id);	
	}	
}

echo "
<script type='text/javascript'>
	location.href='member_detail.php?id={$user_id}';
</script>";
?>