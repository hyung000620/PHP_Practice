<?
error_reporting(E_ERROR);
ini_set("display_errors", 1);
$page_code=1110;
include($_SERVER["DOCUMENT_ROOT"]."/SuperAdmin/common/header.php");
if($client_level<6) {
?>
	<script type="text/javascript">
	<!--
	alert("권한이 없습니다.");
	location.href="/";
	//-->
	</script>
	<?
	exit;
}
$search_date=($search_date) ? $search_date : date("Y-m-d");	//조회일자
if($search_date > date("Y-m-d"))
{
	echo "<script type='text/javascript'>location.href='dt.php';</script>";
	exit;
}

//경매 지역 구분
//$result=sql_query("SELECT state,area FROM {$my_db}.tc_price ORDER BY sort_num");
//while($rs=mysql_fetch_array($result))
$stmt=$pdo->prepare("SELECT state,area FROM {$my_db}.tc_price ORDER BY sort_num");
$stmt->execute();
while($rs=$stmt->fetch()){$state_arr[$rs[state]]=$rs[area];}

//동영상 강좌 구분
//$result=sql_query("SELECT lec_code,course FROM {$my_db}.te_lecture");
//while($rs=mysql_fetch_array($result))
$stmt=$pdo->prepare("SELECT lec_code,course FROM {$my_db}.te_lecture");
$stmt->execute();
while($rs=$stmt->fetch()){$lect_arr[$rs[lec_code]]=$rs[course];}
//경매교육 구분
$stmt=$pdo->prepare("SELECT edu_code,edu_title FROM {$my_db}.tl_edu");
$stmt->execute();
while($rs=$stmt->fetch()){$edu_arr[$rs[edu_code]]=$rs[edu_title];}
//담당자
//$result=sql_query("SELECT id,name FROM {$my_db}.tz_staff WHERE 1");
//while($rs=mysql_fetch_array($result))
$stmt=$pdo->prepare("SELECT id,name FROM {$my_db}.tz_staff WHERE 1");
$stmt->execute();
while($rs=$stmt->fetch()){$staff_arr[$rs[id]]=$rs[name];}

//은행정보-관리자용
$bank_arr=array(10 => array("name"=>"국민은행",		"no"=>"361437-04-012881"),
				11 => array("name"=>"국민은행(동)",	"no"=>"361437-04-010225"));
	
$adate=explode("-",$search_date);

$prev_date=date("Y-m-d",(mktime(0,0,0,$adate[1],$adate[2],$adate[0])-86400));	//이전날
$next_date=date("Y-m-d",(mktime(0,0,0,$adate[1],$adate[2],$adate[0])+86400));	//다음날
//$prev_date=date("Y-m-d",strtotime($search_date."-1 day"));	//이전날
//$next_date=date("Y-m-d",strtotime($search_date."+1 day"));	//다음날
//setlocale (LC_TIME, "korean");

$today=strftime("%Y년 %m월 %d일 (%a)",mktime(0,0,0,$adate[1],$adate[2],$adate[0]));
$condition="M.id=P.id AND P.id=L.id AND P.order_no=L.order_no AND paydate='{$search_date}'";
if($pay_yn==1) $condition.=" AND P.paykind!='9'";
elseif($pay_yn==2) $condition.=" AND P.paykind='9'";
$SQL ="SELECT COUNT(order_no) AS cnt,order_no,pay_code,money,paykind,pay_price,bankcode,payname,point,dc_rate FROM (";
$SQL.="SELECT L.order_no,pay_code,money,pay_price,paykind,bankcode,payname,point,L.dc_rate FROM {$my_db}.tm_pay_result  P, {$my_db}.tm_pay_list L, {$my_db}.tm_member M WHERE {$condition} ";
$SQL.="UNION ALL ";
$SQL.="SELECT L.order_no,pay_code,money,pay_price,paykind,bankcode,payname,point,L.dc_rate FROM {$my_db}.tm_pay_history P, {$my_db}.tm_pay_list L, {$my_db}.tm_member M WHERE {$condition} ";
$SQL.=") T GROUP BY order_no ORDER BY order_no DESC";
if($client_id=="ice") echo $SQL . "<br>";

//$result=sql_query($SQL);
$stmt=$pdo->prepare($SQL);
$stmt->execute();
$p_arr=array();
//while($rs=mysql_fetch_array($result))
while($rs=$stmt->fetch())
{
	if($rs[paykind]==2)		$paykind=str_replace("은행","",$bank_arr[$rs[bankcode]][name])."-".$rs[payname];
	elseif($rs[paykind]==1)	$paykind="카드";
	elseif($rs[paykind]==3)	$paykind="이체";
	elseif($rs[paykind]==4)	$paykind="가상계좌";
	else					$paykind=$pay_kind_arr[$rs[paykind]];
	if($rs[pay_code]!="100") $pmt=$rs[money];
	elseif($rs[dc_rate]>0) $pmt=($rs[pay_price]*100)/(100-$rs[dc_rate]);
	else $pmt=$rs[money];
	$p_arr[$rs[order_no]]=array("rowspan" => $rs[cnt], "point" => $rs[point], "paykind" => $paykind, "amt" => $rs[pay_price], "pmt" => $pmt, "payname" => $rs[payname], "dc_rate" => $rs[dc_rate]);
	$row_no+=$rs[cnt];
	$amt_sum+=$rs[pay_price];	

	//${"sum_pay_".$rs[pay_code]."_".$rs[paykind]}=${"sum_pay_".$rs[pay_code]."_".$rs[paykind]}+$rs[pay_price];
	//${"sum_pay_p_".$rs[pay_code]."_".$rs[paykind]}=${"sum_pay_p_".$rs[pay_code]."_".$rs[paykind]}+$pmt;
}

$SQL ="SELECT M.id,name,reg_date,L.rec_id,P.order_no,P.idx,pay_code,state,sector,months,money,paykind,paytime,validity,point,L.dc_rate,pay_price,bankcode,payname,startdate,vp_edate,sp_edate,memo,staff,L.toss,1 AS tbl_key ";
$SQL.="FROM {$my_db}.tm_member M , {$my_db}.tm_pay_result  P , {$my_db}.tm_pay_list L WHERE {$condition} ";
$SQL.="UNION ALL ";
$SQL.="SELECT M.id,name,reg_date,L.rec_id,P.order_no,P.idx,pay_code,state,sector,months,money,paykind,paytime,validity,point,L.dc_rate,pay_price,bankcode,payname,startdate,vp_edate,sp_edate,memo,staff,L.toss,2 AS tbl_key ";
$SQL.="FROM {$my_db}.tm_member M , {$my_db}.tm_pay_history P , {$my_db}.tm_pay_list L WHERE {$condition} ";
$SQL.="ORDER BY order_no DESC";

//$result=sql_query($SQL);
$stmt=$pdo->prepare($SQL);
$stmt->execute();
?>
<form name="fmSrch" action="./../member/member_list.php" method="post">
<table class="tbl_grid">
	<tr>
		<th>회원명</th>
		<td><input type="text" name="usr_name" value="" class="tx80 han"></td>
		<th>아이디</th>
		<td><input type="text" name="usr_id" value="" class="tx80 ieng"></td>
		<th>휴대폰</th>
		<td><input type="text" name="mobile" value="" class="tx80"></td>
		<th>일반전화</th>
		<td><input type="text" name="phone" value="" class="tx80"></td>
		<th>입금자</th>
		<td><input type="text" name="pay_name" class="tx80"></td>
		<td><input type="submit" value=" 검 색 "></td>
		<td class="center"><a href="https://pgweb.uplus.co.kr/ms/mertpotal/retrieveMertAdminLoginPage.do" target="_blank" class="green">U+전자결제</a></td>
	</tr>
</table>
</form>
<br>
<table class="tbl_noline">
	<tr>
		<td width="32%" class="no bold">
			<a href="dt.php?search_date=<?=$prev_date?>"> &lt; </a>&nbsp;
			<a href="dt.php" class="blue"><?=$today?></a>&nbsp;&nbsp;
			<a href="dt.php?search_date=<?=$next_date?>"> &gt; </a>
		</td>
		<td width="17%" class="no bold">
			<? if($client_level >= 7) : ?>
			매출액 <span id="sum_good" class="no orange"><?=number_format($amt_sum)?></span>&nbsp;원
			<? endif; ?>
		</td>
		<td width="17%" class="no right"><span id="sum_error" class="red"></span></td>		
		<td width="17%" class="no right">
			⊙ 조회일자
			<input type="text" name="sdate" id="sdate" class="tx_date">
			<a href="javascript:location.href='dt.php?search_date='+$('#sdate').val()" class="blue">검색</a>
		</td>
		<td width="17%" class="no right"><a href="#sales_btm" name="sales_top">[아래로] ▼</a></td>
	</tr>
</table>
<br>
<div>
	<ul>
		<li onclick="location.href='<?=$PHP_SELF?>?pay_yn=0&search_date=<?=$search_date?>';" class='fleft ta100 center hand<? if($pay_yn>0) { echo " bg_darkgray white"; } ?>'>전체</li>
		<li onclick="location.href='<?=$PHP_SELF?>?pay_yn=1&search_date=<?=$search_date?>';" class='fleft ta100 center hand<? if($pay_yn!=1) { echo " bg_darkgray white"; } ?>'>유료</li>
		<li onclick="location.href='<?=$PHP_SELF?>?pay_yn=2&search_date=<?=$search_date?>';" class='fleft ta100 center hand<? if($pay_yn!=2) { echo " bg_darkgray white"; } ?>'>무료</li>
	</ul>
</div>
<br>
<table id="sales_list" class="tbl_grid">
	<tr>
		<th width="2%"><input type="checkbox" id="chk_all"></th>
		<th width="3%">No</th>
		<th width="13%">회원명(ID) &gt; <span class="f11 blue">회원정보</span></th>
		<th width="10%">결제구분</th>
		<th width="4%">기간</th>
		<th width="6%">이용료</th>
		<th width="6%">결제금액</th>
		<th width="10%">결제방법</th>
		<th width="4%">시간</th>
		<th width="8%">만료일</th>
		<th width="6%">담당자</th>
		<th width="15%">메모</th>
	</tr>
<?
$payNewMoneyCnt=0;		//신규결제(명)
$payNewMoneyTCnt=0;		//신규결제(건)
$payMoneyCnt=0;			//유료결제(명)
$payMoneyTCnt=0;		//유료결제(건)
$payFreeCnt=0;			//무료결제(명)
$payFreeTCnt=0;			//무료결제(건)
//while($rs=mysql_fetch_array($result))
while($rs=$stmt->fetch())
{
	$rowspan=$p_arr[$rs[order_no]][rowspan];
	$sector="";
	if($rs[pay_code]==100) $sector=$state_arr[$rs[state]];
	elseif($rs[pay_code]==101) $sector="<span class='bg_yellow red'>[강좌]</span>".$lect_arr[$rs[sector]];
    elseif($rs[pay_code]==102) $sector="<span class='white' style='background:blue'>[경매교육]</span>".$edu_arr[$rs[sector]];
	$expire=($rs[validity]=="0000-00-00") ? "-" : str_replace("-",".",$rs[validity]);
	$onclick="onclick=pay_edit('{$rs[tbl_key]}','{$rs[idx]}','{$rs[order_no]}','{$rs[id]}')";
	$new="";
	$new_key=(($sTime - $rTime) < (86400*3) && $rs[pay_kind] != 9) ? 1 : 0;
	if($new_key==1) $payNewMoneyTCnt++;
	if($rs['paykind']==9) $payFreeTCnt++;
	else $payMoneyTCnt++;
	
	if(strpos($rs[memo],"미확인") || strpos($rs[memo],"미입금"))
	{		
		$bgcolor=($rs[tbl_key]==1) ? "#e6ffd6" : "#ccc";
	}
	else
	{
		$bgcolor=($rs[tbl_key]==2) ? "#ccc" : "";
	}
	$sector=str_replace("서울남부/동부/중앙","서울 남/동/중",$sector);
	$sector=str_replace("서울북부/서부/중앙","서울 북/서/중",$sector);
	
	echo "
	<tr onmouseover=\"this.style.background='#e3eefb'\" onmouseout=\"this.style.background='{$bgcolor}'\" style='background:{$bgcolor};cursor:pointer'>";
	if($rs[order_no] != $prev)
	{
		echo "<td rowspan='{$rowspan}' id='td_{$rs[id]}'><input type='checkbox' id='chk_{$rs[id]}' name='chk_idx' value='{$rs[id]}' opt='{$rs[order_no]}'></td>";
	}
	echo "	
		<td class='no center f11' {$onclick}>{$row_no}</td>";
	if($rs[order_no] != $prev)
	{		
		$sdateArr=explode("-",$search_date);
		$rdateArr=explode("-",$rs[reg_date]);
		$sTime=mktime(0,0,0,$sdateArr[1],$sdateArr[2],$sdateArr[0]);
		$rTime=mktime(0,0,0,$rdateArr[1],$rdateArr[2],$rdateArr[0]);
		if($new_key==1) $new="<span class='no brown' style='padding:0 2px'><sup>N</sup></span>";
		echo "
		<td rowspan='{$rowspan}'><a href='./../member/member_detail.php?id={$rs[id]}'>{$rs[name]}<span class='no f11'>({$rs[id]})</a></span>{$new}</td>";
		if($new_key==1) $payNewMoneyCnt++;
		if($rs['paykind']==9) $payFreeCnt++;
		else $payMoneyCnt++;
	}
	echo "
		<td class='f11' {$onclick}>{$sector}</td>
		<td class='money' {$onclick}>{$rs[months]}</td>
		<td class='money' {$onclick}>";
		//echo number_format($rs[money]);
		if($rowspan>=2) {
			if($rs[pay_code]!="100") $pmt_chk=$rs[money];
			elseif($rs[dc_rate]>0) $pmt_chk=($rs[pay_price]*100)/(100-$rs[dc_rate]);
			else $pmt_chk=$rs[money];
			echo number_format($pmt_chk);
			${"sum_pay_".$rs[pay_code]."_".$rs[paykind]}=${"sum_pay_".$rs[pay_code]."_".$rs[paykind]}+$pmt_chk;
		} else {
			echo number_format($p_arr[$rs[order_no]][pmt]);
			${"sum_pay_".$rs[pay_code]."_".$rs[paykind]}=${"sum_pay_".$rs[pay_code]."_".$rs[paykind]}+$p_arr[$rs[order_no]][pmt];
		}
		echo "</td>";
	if($rs[order_no] != $prev)
	{
		$dc_flag=($p_arr[$rs[order_no]][dc_rate]) ? "<span class='white f9' style='margin-left:5px;background:#45b417'>DC ".$p_arr[$rs[order_no]][dc_rate]."</span><br>" : "";
		$ntoss_flag=($rs['toss']==0 && $rs[pay_price] != 0)? "<span class='white f9' style='position:relative;top:-5px;margin-left:5px;background:red'>구</span>" : "";
		echo "
		<td rowspan='{$rowspan}' class='money' {$onclick}>{$dc_flag}<span class='bold'>".number_format($p_arr[$rs[order_no]][amt])."</span></td>
		<td rowspan='{$rowspan}' class='right f11' {$onclick}>{$ntoss_flag}".str_replace(' ','',$p_arr[$rs[order_no]][paykind])."</td>
		<td rowspan='{$rowspan}' class='no center f11' {$onclick}>".substr($rs[paytime],0,5)."</td>";
		${"sum_pay_p_".$rs[pay_code]."_".$rs[paykind]}=${"sum_pay_p_".$rs[pay_code]."_".$rs[paykind]}+$p_arr[$rs[order_no]][amt];
	}
	echo "			
		<td class='no center' {$onclick}>{$expire}</td>
		<td class='center f11' {$onclick}>{$staff_arr[$rs[staff]]}</td>
		<td name='td_memo' opt='{$rs[id]}' class='f11' {$onclick}>{$rs[memo]}</td>
	</tr>";
	$prev=$rs[order_no];
	$row_no--;
}
?>
</table>


<div style="margin:20px 0">
	<input type="button" value="전체선택" onclick="chk_all(1)">
	<input type="button" value="선택해제" onclick="chk_all(0)">
	&nbsp;&nbsp;
	<!--
	* 메모검색 &gt;
	<input type="text" id="keyword" class="tx150 han">
	<input type="button" value="검색" onclick="srch_txt()">
	&nbsp;&nbsp;
		
	<input type="button" value="입금확인" onclick="pay_ok()">
	&nbsp;&nbsp;
	-->
	<input type="button" value="문자발송" onclick="send_sms()">
	&nbsp;&nbsp;&nbsp;&nbsp;
</div>

<?
if($client_level>=7) {
	$sum_100=$sum_pay_100_1+$sum_pay_100_2+$sum_pay_100_3+$sum_pay_100_4;
	$sum_101=$sum_pay_101_1+$sum_pay_101_2+$sum_pay_101_3+$sum_pay_101_4;
	$sum_1=$sum_pay_100_1+$sum_pay_101_1;
	$sum_2=$sum_pay_100_2+$sum_pay_101_2;
	$sum_3=$sum_pay_100_3+$sum_pay_101_3;
	$sum_4=$sum_pay_100_4+$sum_pay_101_4;
	$sum=$sum_100+$sum_101;

	$sum_p_100=$sum_pay_p_100_1+$sum_pay_p_100_2+$sum_pay_p_100_3+$sum_pay_p_100_4;
	$sum_p_101=$sum_pay_p_101_1+$sum_pay_p_101_2+$sum_pay_p_101_3+$sum_pay_p_101_4;
	$sum_p_1=$sum_pay_p_100_1+$sum_pay_p_101_1;
	$sum_p_2=$sum_pay_p_100_2+$sum_pay_p_101_2;
	$sum_p_3=$sum_pay_p_100_3+$sum_pay_p_101_3;
	$sum_p_4=$sum_pay_p_100_4+$sum_pay_p_101_4;
	$sum_p=$sum_p_100+$sum_p_101;
	?>
	<table class="tbl_grid">
		<tr>
			<th colspan="2">구분</th>
			<th width="15%">통장</th>
			<th width="15%">카드</th>
			<th width="15%">이체</th>
			<th width="15%">가상계좌</th>
			<th width="15%">소계</th>
		</tr>
		<tr>
			<td rowspan="2" class='center'>경매</td>
			<td class='center'>이용료</td>
			<td class='money'><?=number_format($sum_pay_100_2)?></td>
			<td class='money'><?=number_format($sum_pay_100_1)?></td>
			<td class='money'><?=number_format($sum_pay_100_3)?></td>
			<td class='money'><?=number_format($sum_pay_100_4)?></td>
			<td class='money'><?=number_format($sum_100)?></td>
		</tr>
		<tr>
			<td class='center'>결제금액</td>
			<td class='money'><span class='ltblue'><?=number_format($sum_pay_p_100_2)?></span></td>
			<td class='money'><span class='ltblue'><?=number_format($sum_pay_p_100_1)?></span></td>
			<td class='money'><span class='ltblue'><?=number_format($sum_pay_p_100_3)?></span></td>
			<td class='money'><span class='ltblue'><?=number_format($sum_pay_p_100_4)?></span></td>
			<td class='money'><span class='ltblue'><?=number_format($sum_p_100)?></span></td>
		</tr>
		<tr>
			<td rowspan="2" class='center'>강좌</td>
			<td class='center'>이용료</td>
			<td class='money'><?=number_format($sum_pay_101_2)?></td>
			<td class='money'><?=number_format($sum_pay_101_1)?></td>
			<td class='money'><?=number_format($sum_pay_101_3)?></td>
			<td class='money'><?=number_format($sum_pay_101_4)?></td>
			<td class='money'><?=number_format($sum_101)?></td>
		</tr>
		<tr>
			<td class='center'>결제금액</td>
			<td class='money'><span class='ltblue'><?=number_format($sum_pay_p_101_2)?></td>
			<td class='money'><span class='ltblue'><?=number_format($sum_pay_p_101_1)?></td>
			<td class='money'><span class='ltblue'><?=number_format($sum_pay_p_101_3)?></td>
			<td class='money'><span class='ltblue'><?=number_format($sum_pay_p_101_4)?></td>
			<td class='money'><span class='ltblue'><?=number_format($sum_p_101)?></td>
		</tr>
		<tr>
			<td rowspan="2" class='center'>계</td>
			<td class='center'>이용료</td>
			<td class='money'><?=number_format($sum_2)?></td>
			<td class='money'><?=number_format($sum_1)?></td>
			<td class='money'><?=number_format($sum_3)?></td>
			<td class='money'><?=number_format($sum_4)?></td>
			<td class='money'><?=number_format($sum)?></td>
		</tr>
		<tr>
			<td class='center'>결제금액</td>
			<td class='money'><span class='ltblue'><?=number_format($sum_p_2)?></span></td>
			<td class='money'><span class='ltblue'><?=number_format($sum_p_1)?></span></td>
			<td class='money'><span class='ltblue'><?=number_format($sum_p_3)?></span></td>
			<td class='money'><span class='ltblue'><?=number_format($sum_p_4)?></span></td>
			<td class='money'><span class='ltblue'><?=number_format($sum_p)?></span></td>
		</tr>
	</table>
<?
}
?>

<?
if($client_level>=7) {
	$search_dateExp=explode("-",$search_date);
	$startYDate=date("Y-m-d",mktime(0,0,0,1,1,$search_dateExp[0]));
	$startDay=date("Y-m-d",mktime(0,0,0,$search_dateExp[1],1,$search_dateExp[0]));
	$YesterDay=date("Y-m-d",mktime(0,0,0,$search_dateExp[1],$search_dateExp[2]-1,$search_dateExp[0]));
	$endDay=$search_date;
	//일일가입자수
	$DayMemCnt=0;
	$sql="select count(*) from {$my_db}.tm_member where reg_date='{$endDay}'";
	$stmt=$pdo->prepare($sql);
	$stmt->execute();
	$DayMemCnt=$stmt->fetchColumn();

	//월가입자수
	$MonthMemCnt=0;
	$sql="select count(*) from {$my_db}.tm_member where reg_date between '{$startDay}' and '{$endDay}' and out_date='0000-00-00'";
	$stmt=$pdo->prepare($sql);
	$stmt->execute();
	$MonthMemCnt=$stmt->fetchColumn();

	//전체회원
	$AllMemCnt=0;
	$sql="select count(*) from {$my_db}.tm_member where out_date='0000-00-00'";
	$stmt=$pdo->prepare($sql);
	$stmt->execute();
	$AllMemCnt=$stmt->fetchColumn();

	/*
	//일일결제건수
	$payFreeCnt=0;
	$payMoneyCnt=0;
	$payFreeTCnt=0;
	$payMoneyTCnt=0;
	$id_tmp="";
	$sql="select id,paykind from {$my_db}.tm_pay_result where paydate='{$endDay}' order by id";
	//if($client_id=="yspn") echo $sql . "<br>";
	$stmt=$pdo->prepare($sql);
	$stmt->execute();
	while($row=$stmt->fetch()) {
		if($row['paykind']==9) $payFreeTCnt++;
		else $payMoneyTCnt++;
		if($id_tmp!=$row['id']) {
			if($row['paykind']==9) $payFreeCnt++;
			else $payMoneyCnt++;
		}
		$id_tmp=$row['id'];
	}
	$payFreeCntH=0;
	$payMoneyCntH=0;
	$payFreeTCntH=0;
	$payMoneyTCntH=0;
	$id_tmp="";
	$sql="select id,paykind from {$my_db}.tm_pay_history where paydate='{$endDay}' order by id";
	//if($client_id=="yspn") echo $sql . "<br>";
	$stmt=$pdo->prepare($sql);
	$stmt->execute();
	while($row=$stmt->fetch()) {
		if($row['paykind']==9) $payFreeTCntH++;
		else $payMoneyTCntH++;
		if($id_tmp!=$row['id']) {
			if($row['paykind']==9) $payFreeCntH++;
			else $payMoneyCntH++;
		}
		$id_tmp=$row['id'];
	}
	*/

	//유료이용자
	$payAllCnt=0;
	$sql="select id from {$my_db}.tm_pay_result where paykind!=9 and validity>='{$endDay}' GROUP BY id";
	//if($client_id=="yspn") echo $sql . "<br>";
	$stmt=$pdo->prepare($sql);
	$stmt->execute();
	$payAllCnt=$stmt->rowCount();

	//유료만료건수(어제)
	$payCloseCnt=0;
	$sql="select id from {$my_db}.tm_pay_result where paykind!=9 and validity='{$YesterDay}'";
	//if($client_id=="yspn") echo $sql . "<br>";
	$stmt=$pdo->prepare($sql);
	$stmt->execute();
	$payCloseCnt=$stmt->rowCount();

	$payCloseCntH=0;
	$sql="select id from {$my_db}.tm_pay_history where paykind!=9 and validity='{$YesterDay}'";
	//if($client_id=="yspn") echo $sql . "<br>";
	$stmt=$pdo->prepare($sql);
	$stmt->execute();
	$payCloseCntH=$stmt->rowCount();

	/*
	//당일매출
	$payDayMoney=0;
	//$sql="select P.id, P.pay_code, P.money, L.pay_price, L.dc_rate from {$my_db}.tm_pay_result P, {$my_db}.tm_pay_list L where P.id=L.id AND P.order_no=L.order_no and P.paykind!=9 and P.money>0 and P.paydate='{$endDay}'";
	$sql="select L.pay_price from {$my_db}.tm_pay_result P, {$my_db}.tm_pay_list L where P.id=L.id AND P.order_no=L.order_no and P.paykind!=9 and P.money>0 and P.paydate='{$endDay}' GROUP BY L.order_no";
	//if($client_id=="yspn") echo $sql . "<br>";
	$stmt=$pdo->prepare($sql);
	$stmt->execute();
	//$row=$stmt->fetch();
	//$payDayMoney=$row[0];
	while($row=$stmt->fetch()) {
		$payDayMoney=$payDayMoney+$row[pay_price];
	}

	$payDayMoneyH=0;
	$sql="select P.id, P.pay_code, P.money, L.pay_price, L.dc_rate from {$my_db}.tm_pay_history P, {$my_db}.tm_pay_list L where P.id=L.id AND P.order_no=L.order_no and P.paykind!=9 and P.money>0 and P.paydate='{$endDay}' GROUP BY L.order_no";
	//if($client_id=="yspn") echo $sql . "<br>";
	$stmt=$pdo->prepare($sql);
	$stmt->execute();
	//$row=$stmt->fetch();
	//$payDayMoneyH=$row[0];
	while($row=$stmt->fetch()) {
		$pmt=0;
		if($row[pay_code]!="100") $pmt=$row[money];
		elseif($row[dc_rate]>0) $pmt=($row[pay_price]*100)/(100-$row[dc_rate]);
		else $pmt=$row[money];
		$payDayMoneyH=$payDayMoneyH+$row[pay_price];
	}
	*/

	//당월매출
	$payMonthMoney=0;
	$payMonthMoneyMV=0;				//동영상결제
	$sql="select P.id, P.pay_code, P.money, L.pay_price, L.dc_rate from {$my_db}.tm_pay_result P, {$my_db}.tm_pay_list L where P.id=L.id AND P.order_no=L.order_no and P.paykind!=9 and P.money>0 and P.paydate between '{$startDay}' and '{$endDay}' GROUP BY L.order_no";
	//if($client_id=="yspn") echo $sql . "<br>";
	$stmt=$pdo->prepare($sql);
	$stmt->execute();
	while($row=$stmt->fetch()) {
		if($row[pay_code]=="101") $payMonthMoneyMV=$payMonthMoneyMV+$row[pay_price];
		$payMonthMoney=$payMonthMoney+$row[pay_price];
	}

	$payMonthMoneyH=0;
	$payMonthMoneyMVH=0;			//동영상결제
	$sql="select P.id, P.pay_code, P.money, L.pay_price, L.dc_rate from {$my_db}.tm_pay_history P, {$my_db}.tm_pay_list L where P.id=L.id AND P.order_no=L.order_no and P.paykind!=9 and P.money>0 and P.paydate between '{$startDay}' and '{$endDay}' GROUP BY L.order_no";
	//if($client_id=="yspn") echo $sql . "<br>";
	$stmt=$pdo->prepare($sql);
	$stmt->execute();
	while($row=$stmt->fetch()) {
		if($row[pay_code]=="101") $payMonthMoneyMVH=$payMonthMoneyMVH+$row[pay_price];
		$payMonthMoneyH=$payMonthMoneyH+$row[pay_price];
	}

	//연매출
	$payYearMoney=0;
	$sql="select P.id, P.pay_code, P.money, L.pay_price, L.dc_rate from {$my_db}.tm_pay_result P, {$my_db}.tm_pay_list L where P.id=L.id AND P.order_no=L.order_no and P.paykind!=9 and P.money>0 and P.paydate between '{$startYDate}' and '{$endDay}' GROUP BY L.order_no";
	//if($client_id=="yspn") echo $sql . "<br>";
	$stmt=$pdo->prepare($sql);
	$stmt->execute();
	while($row=$stmt->fetch()) {
		$payYearMoney=$payYearMoney+$row[pay_price];
	}

	$payYearMoneyH=0;
	$sql="select P.id, P.pay_code, P.money, L.pay_price, L.dc_rate from {$my_db}.tm_pay_history P, {$my_db}.tm_pay_list L where P.id=L.id AND P.order_no=L.order_no and P.paykind!=9 and P.money>0 and P.paydate between '{$startYDate}' and '{$endDay}' GROUP BY L.order_no";
	//if($client_id=="yspn") echo $sql . "<br>";
	$stmt=$pdo->prepare($sql);
	$stmt->execute();
	while($row=$stmt->fetch()) {
		$payYearMoneyH=$payYearMoneyH+$row[pay_price];
	}
	?>
	<div style='padding:30px 0'>
		<div>- <b>기준일 ( <?=str_replace("-",".",$endDay)?> )</b></div>
		<table class="tbl_grid">
			<tr>
				<th width='15%'>가입자수(당일)</th>
				<td width='15%' class='money'><?=number_format($DayMemCnt)?>명</td>
				<td rowspan='4'></td>
				<th width='15%'>월가입누계</th>
				<td width='15%' class='money'><?=number_format($MonthMemCnt)?>명</td>
				<td rowspan='4'></td>
				<th width='15%'>당일매출</th>
				<td width='15%' class='money'><?=number_format($sum_p)?>원</td>
			</tr>
			<tr>
				<th>유료결제(당일)</th>
				<td class='money'><?=number_format($payMoneyCnt+$payMoneyCntH)?>명 / <?=number_format($payMoneyTCnt+$payMoneyTCntH)?>건</td>
				<th>전체회원(탈퇴제외)</th>
				<td class='money'><?=number_format($AllMemCnt)?>명</td>
				<th>월매출</th>
				<td class='money'><?=number_format($payMonthMoney+$payMonthMoneyH)?>원</td>
			</tr>
			<tr>
				<th>무료결제(당일)</th>
				<td class='money'><?=number_format($payFreeCnt+$payFreeCntH)?>명 / <?=number_format($payFreeTCnt+$payFreeTCntH)?>건</td>
				<th>유료회원( 이용중 )</th>
				<td class='money'><?=number_format($payAllCnt)?>명</td>
				<th>년매출</th>
				<td class='money'><?=number_format($payYearMoney+$payYearMoneyH)?>원</td>
			</tr>
			<tr>
				<th>신규결제(당일)</th>
				<td class='money'><?=number_format($payNewMoneyCnt)?>명 / <?=number_format($payNewMoneyTCnt)?>건</td>
				<th>(어제)만료건수(유료)</th>
				<td class='money'><?=number_format($payCloseCnt+$payCloseCntH)?>건</td>
				<th>동영상월매출</th>
				<td class='money'><?=number_format($payMonthMoneyMV+$payMonthMoneyH)?>원</td>
			</tr>
		</table>
	</div>
<?
}
?>
<form id="fm_pay_ok" action="./../member/pay_edit_db.php" method="post">
	<input type="hidden" id="pay_order_no" name="pay_order_no" value="">
	<input type="hidden" name="mode" value="pay_ok">
</form>
<div style="clear:both"></div>
<div style="text-align:right"><a href="#sales_top" name="sales_btm">[위로] ▲</a></div>
<?
include($_SERVER["DOCUMENT_ROOT"]."/SuperAdmin/common/footer.php");
?>

<script type="text/javascript">
var id_list_json;

$(document).ready(function(){
	$("#sdate").mask("9999-99-99");
	$("#sdate").datepicker({
		changeMonth:true,
		changeYear:true,
		showButtonPanel:true
	});
	$("#sales_list input:checkbox[name=chk_idx]").bind("click",function(){
		if($(this).is(":checked")==true) $("#td_"+$(this).val()).css("background","gold");
		else $("#td_"+$(this).val()).css("background","");
	});
	//전체 선택/해제
	$("#chk_all").click(function(){
		var bool=(this.checked==true) ? true : false;
		$("input:checkbox[name=chk_idx]").each(function(){
			this.checked=bool;
		})
	});
});

//결제 수정
function pay_edit(tbl,idx,order_no,user_id)
{
	var lnk="./../member/pay_edit.php?tbl="+tbl+"&idx="+idx+"&order_no="+order_no+"&user_id="+user_id;
	window.open(lnk,"pay_edit","top=0,left=0,width=720,height=300,scrollbars=yes,resizable=yes").focus();
}

//전체선택/해제
function chk_all(flag)
{
	var bool=(flag==1) ? true : false;
	var bground=(flag==1) ? "gold" : "";
	$("#sales_list input:checkbox[name=chk_idx]").each(function(){
		this.checked=bool;
		$("#td_"+$(this).val()).css("background",bground);
	})	
}

//문자 보내기
function send_sms()
{
	var arr=[];
	$("#sales_list input:checkbox[name=chk_idx]:checked").each(function(){
		var uid=$(this).val();
		arr.push("'"+uid+"'");
	});
	var json_str="["+arr.join(",")+"]";
	id_list_json=eval("("+json_str+")");	//String To JSON
	window.open("/SuperAdmin/member/sms_write.php","send_sms","width=800,height=800,scrollbars=yes");
}

//결제 메모 문자열 찾기
function srch_txt()
{
	var count=0;
	var keyword=$("#keyword").val();
	if(keyword=="")
	{
		alert("메모에서 찾을 내용을 입력 하세요^^");
		return;
	}
	
	$("#sales_list td[name=td_memo]").each(function(){
		if($(this).text().indexOf(keyword)==-1) return true;
		$("#chk_"+($(this).attr("opt"))).attr("checked",true);
		$("#td_"+($(this).attr("opt"))).css("background","gold");
		count++;
	});
	
	if(count==0) alert("검색 결과가 없습니다");
	else alert(count+" 건이 선택되었습니다.");
}

//일괄 입금확인
function pay_ok()
{
	var arr=[];
	$("#sales_list input:checkbox[name=chk_idx]:checked").each(function(){
		var order_no=$(this).attr("opt");
		arr.push("'"+order_no+"'");
	});
	
	if(arr.length==0)
	{
		alert("선택한 결제건이 없습니다.");
		return;
	}
	$("#pay_order_no").val(arr.join(","));
	$("#fm_pay_ok").submit();
}
</script>