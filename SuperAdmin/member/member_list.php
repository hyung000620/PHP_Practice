<?
$debug=false;
$page_code=1010;
include($_SERVER["DOCUMENT_ROOT"]."/SuperAdmin/common/header.php");

//경매 지역 구분
//$result=sql_query("SELECT state,area,use_key FROM {$my_db}.tc_price WHERE use_key=1 ORDER BY sort_num");
//while($rs=mysql_fetch_array($result))
$stmt=$pdo->prepare("SELECT state,area,use_key FROM {$my_db}.tc_price WHERE use_key=1 ORDER BY sort_num");
$stmt->execute();
while($rs=$stmt->fetch()){$state_arr[$rs[state]]=$rs[area];}

//동영상 강좌 구분
//$result=sql_query("SELECT lec_code,course FROM {$my_db}.te_lecture");
//while($rs=mysql_fetch_array($result))
$stmt=$pdo->prepare("SELECT lec_code,course,badge,price,hide FROM {$my_db}.te_lecture ORDER BY lec_code DESC");
$stmt->execute();
//while($rs=$stmt->fetch()){$lect_arr[$rs[lec_code]]=$rs[course];}
while($rs=$stmt->fetch()){ // ◐ 강좌 준비중, ★ 강좌화면 view, ♥ 유료강좌  
	$m_badge=($rs[badge]==14) ? "<font color='red'>◐</font>" : "";
	$m_hide=($rs[hide]==1) ? "" : "<font color='red'>★</font>";
	$m_price=($rs[price]==0) ? "" : "<font color='red'>♥</font>";
	$lect_arr[$rs[lec_code]]=$m_badge."·".$m_hide."·".$m_price."·".$rs[course];
}
//경매교육 구분
$stmt=$pdo->prepare("SELECT edu_code,edu_title FROM {$my_db}.tl_edu");
$stmt->execute();
while($rs=$stmt->fetch()){$edu_arr[$rs[edu_code]]=$rs[edu_title];}
//협력업체
$partner_arr=array();
//$SQL=sql_query("SELECT code,sangho FROM {$my_db}.tz_partner WHERE edate='0000-00-00' OR edate > CURDATE() ORDER BY sangho");
//while($rs=mysql_fetch_array($SQL))
$stmt=$pdo->prepare("SELECT code,sangho FROM {$my_db}.tz_partner WHERE edate='0000-00-00' OR edate > CURDATE() ORDER BY sangho");
$stmt->execute();
while($rs=$stmt->fetch()){$partner_arr[$rs[code]]=$rs[sangho];}

if($pay_his==1) $pay_db="tm_pay_history";
else $pay_db="tm_pay_result";

$usr_id=trim($usr_id);
$usr_name=trim($usr_name);
$phone=trim($phone);
$mobile=trim($mobile);
$pay_name=trim($pay_name);
$pay_memo=trim($pay_memo);

if(strchr($usr_id,",")==true) {
	$exp_usr_id=explode(",",$usr_id);
	$condiArr[]="M.id IN ('" . implode("','",$exp_usr_id) . "')";
} else {
	if($usr_id)		$condiArr[]=($srch_eq) ? "M.id='{$usr_id}'" : "M.id LIKE '%{$usr_id}%'";
}
if($usr_name)	$condiArr[]=($srch_eq) ? "name='{$usr_name}'" : "name LIKE '%{$usr_name}%'";
if($addr)	$condiArr[]=($srch_eq) ? "address1='{$addr}'" : "address1 LIKE '%{$addr}%'";

if($phone)	$condiArr[]=($srch_eq) ? "phone='{$phone}'" : "phone LIKE '%{$phone}%'";
if(strchr($mobile,",")==true) {
	$exp_mobile=explode(",",$mobile);
	$condiArr[]="M.mobile IN ('" . implode("','",$exp_mobile) . "')";
} else {
	if($mobile)	$condiArr[]=($srch_eq) ? "mobile='{$mobile}'" : "mobile LIKE '%{$mobile}%'";
}
if($pay_name)	$condiArr[]=($srch_eq) ? "payname='{$pay_name}'" : "payname LIKE '%{$pay_name}%'";
if($pay_memo)	$condiArr[]=($srch_eq) ? "memo='{$pay_memo}'" : "memo LIKE '%{$pay_memo}%'";
if($join_date_bgn)	$condiArr[]="reg_date >= '{$join_date_bgn}'";
if($join_date_cls)	$condiArr[]="reg_date <= '{$join_date_cls}'";
if($pay_date_bgn)	$condiArr[]="paydate >= '{$pay_date_bgn}'";
if($pay_date_cls)	$condiArr[]="paydate <= '{$pay_date_cls}'";
if($exp_date_bgn)	$condiArr[]="validity >= '{$exp_date_bgn}'";
if($exp_date_cls)	$condiArr[]="validity <= '{$exp_date_cls}'";
if($out)		$condiArr[]="out_date > '0000-00-00'";
if($pay_code)	$condiArr[]="pay_code='{$pay_code}'";
if($paykind)  $condiArr[]="paykind={$paykind}";
if($bank_code)	$condiArr[]="bankcode='{$bank_code}'";
if($pay_state)	$condiArr[]="state='{$pay_state}'";
if($pay_lec_code)	$condiArr[]="sector='{$pay_lec_code}'";
if($expire)		$condiArr[]=($expire=="Y") ? "validity < CURDATE()" : "validity >= CURDATE()";
if($free)		$condiArr[]=($free=="Y") ? "paykind=9" : "paykind!=9";
if($ptnr)
{
	$condiArr[]=($ptnr==1) ? "partner!=''" : "partner=''";
}
if($partner_pm) $condiArr[]="partner_pm='{$partner_pm}'";
if($ptnr_code_wb && $ptnr_code_kb) $condiArr[]="ptnr_code in(4,20)";
elseif($ptnr_code_wb) $condiArr[]="ptnr_code='{$ptnr_code_wb}'";
elseif($ptnr_code_kb) $condiArr[]="ptnr_code='{$ptnr_code_kb}'";
if($dc_apply)	$condiArr[]=($dc_apply==1) ? "dc_rate > 0 AND dc_edate >= CURDATE()" : "dc_rate > 0 AND dc_edate < CURDATE()";

if($admin_q==1) $condiArr[]="pay_code='100' AND paykind!=9 AND months='12' AND validity>='2020-03-01' AND startdate<'2020-04-01'";
if($youtuber)	$condiArr[]="M.youtuber='{$youtuber}'";
if($bid_staff)	$condiArr[]="M.bid_staff='{$bid_staff}'";
if($pay_cstm)	$condiArr[]="pay_custom=1";

if($client_level>=6) $list_scale_arr=array(30,50,100,200,300,500,1000,2000,3000,4000,5000);
else $list_scale_arr=array(30,50,100,200,300);
$order_arr=array(	"name"=>"회원명 ▲", "name DESC"=>"회원명 ▽", "M.id"=>"아이디 ▲", "M.id DESC"=>"아이디 ▽", "paydate"=>"결제일 ▲", "paydate DESC"=>"결제일 ▽",
					"reg_date"=>"가입일자 ▲", "reg_date DESC"=>"가입일자 ▽", "validity"=>"만료일자 ▲", "validity DESC"=>"만료일자 ▽", "login"=>"최근접속 ▲", "login DESC"=>"최근접속 ▽");

//$condition=($condiArr) ? implode(" AND ",$condiArr) : "reg_date > DATE_SUB(CURDATE(),INTERVAL 365 DAY)";
$condition=($condiArr) ? implode(" AND ",$condiArr) : "reg_date > DATE_SUB(CURDATE(),INTERVAL 1825 DAY)";	//5년내
//$order=($order_type) ? $order_type : " M.idx DESC ";
$order=($order_type) ? $order_type : " paydate DESC ";
//echo $condition;
$sql="SELECT COUNT(*) FROM {$my_db}.tm_member M LEFT OUTER JOIN {$my_db}.{$pay_db} P ON M.id=P.id WHERE {$condition}";
if($client_id=="yspn") echo $sql . "<br>";

$stmt=$pdo->prepare($sql);
$stmt->execute();
$rs=$stmt->fetch();
		
//$result=sql_query($sql);
//$rs=mysql_fetch_array($result);

$total_record=$rs[0];
$list_scale=($list_scale) ? $list_scale : 30;
$page_scale=10;
$start=($start) ? $start : 0;

$today=date("Y-m-d");
$SQL="SELECT M.id,name,mobile,phone,reg_date,out_date,login,partner,state,validity,P.idx,pay_code,paydate,sector,paykind,P.money,memo,M.dc_rate,dc_sdate,dc_edate FROM {$my_db}.tm_member M LEFT OUTER JOIN {$my_db}.{$pay_db} P ON P.id=M.id WHERE {$condition} ORDER BY {$order} LIMIT {$start}, {$list_scale}";
if($client_id=="yspn") echo $SQL . "<br>";
$stmt=$pdo->prepare($SQL);
$stmt->execute();

//$result=sql_query($SQL);
$arr_payBank=array("10" => "국민은행","11" => "국민은행(동)","2" => "산업","3" => "기업","7" => "수협","18" => "농협","20" => "우리","23" => "SC제일","27" => "씨티","31" => "대구","32" => "부산","34" => "광주","35" => "제주","37" => "전북","39" => "경남","45" => "새마을","48" => "신협","50" => "저축","64" => "산림","71" => "우체국","81" => "하나","88" => "신한","89" => "케이","90" => "카카오","92" => "토스");
?>
<form name="fmSrch" id="fmSrch" action="<?=$PHP_SELF?>" method="post">
<table class="tbl_grid">
	<tr>
		<th>회원명</th>
		<td><input type="text" name="usr_name" id="usr_name" value="<?=$usr_name?>" class="tx80 han"></td>
		<th>휴대폰</th>
		<td><input type="text" name="mobile" id="mobile" value="<?=$mobile?>" class="tx80"></td>
		<th>일반전화</th>
		<td><input type="text" name="phone" id="phone" value="<?=$phone?>" class="tx80"></td>
    <th>결제구분</th>
    <td>
      <select name="paykind" id="paykind">
				<option value="0">-선택-</option>
        <? foreach($pay_kind_arr as $key => $val){echo "<option value='{$key}'";if($key==$paykind) echo " selected"; echo ">{$val}</option>";} ?>
      </select>
    </td>
		<!--
		<th>입금은행</th>
		<td>
			<select name="bank_code" id="bank_code">
				<option value="0">-선택-</option>
			<?
			//foreach($bank_arr as $key => $arr){echo "<option value='{$key}'";if($key==$bank_code) echo " selected"; echo ">{$arr[name]}</option>";}
			foreach($arr_payBank as $key => $val){echo "<option value='{$key}'";if($key==$bank_code) echo " selected"; echo ">{$val}</option>";}
			?>
			</select>
		</td>
		-->
		<th>이용/만료</th>
		<td>
			<select name="expire" id="expire">
				<option value="0">-선택-</option>
				<option value="N"<? if($expire=="N") echo " selected"; ?>>이용중</option>
				<option value="Y"<? if($expire=="Y") echo " selected"; ?>>기간만료</option>
			</select>
		</td>
		<th>가입일</th>
		<td style='width:185px'>
			<input type="text" name="join_date_bgn" id="join_date_bgn" value="<?=$join_date_bgn?>" class="tx_date" autocomplete='off'> ~ <input type="text" name="join_date_cls" id="join_date_cls" value="<?=$join_date_cls?>" class="tx_date" autocomplete='off'>
		</td>
	</tr>
	<tr>
		<th>아이디</th>
		<td colspan="3">
			<input type="text" name="usr_id" id="usr_id" value="<?=$usr_id?>" class="tx80">
			<input type="checkbox" name="srch_eq" value="1"<? if($srch_eq) echo " checked"; ?>>일치
		</td>
		<th>입금자명</th>
		<td><input type="text" name="pay_name" id="pay_name" value="<?=$pay_name?>" class="tx80 han"></td>
		<th>유/무료</th>
		<td>
			<select name="free" id="free">
				<option value="0">-선택-</option>
				<option value="N"<? if($free=="N") echo " selected"; ?>>유료</option>
				<option value="Y"<? if($free=="Y") echo " selected"; ?>>무료</option>
			</select>
		</td>
		<th>상품구분</th>
		<td>
			<select name="pay_code">
				<option value="0">-선택-</option>
				<option value="100"<? if($pay_code==100) echo " selected"; ?>>경매</option>
				<option value="101"<? if($pay_code==101) echo " selected"; ?>>동영상</option>
			</select>
		</td>
		<th>결제일</th>
		<td>
			<input type="text" name="pay_date_bgn" id="pay_date_bgn" value="<?=$pay_date_bgn?>" class="tx_date" autocomplete='off'> ~ <input type="text" name="pay_date_cls" id="pay_date_cls" value="<?=$pay_date_cls?>" class="tx_date" autocomplete='off'>
		</td>
	</tr>
	<tr>
		<th>결제지역</th>
		<td>
			<select name="pay_state" id="pay_state">
				<option value="0">-선택-</option>
			<?
			foreach($state_arr as $key => $val)
			{
				echo "<option value='{$key}'";if($key==$pay_state) echo " selected"; echo ">{$val}</option>";	
			}
			?>
			</select>
		</td>
		<th>협력업체</th>
		<td>
			<select name="ptnr">
				<option value="0">-선택-</option>
				<option value="1"<? if($ptnr==1) echo " selected"; ?>>O</option>
				<option value="2"<? if($ptnr==2) echo " selected"; ?>>X</option>
			</select>
		</td>
		<th>기타</th>
		<td>
			<input type="checkbox" name="pay_cstm" value="1" <? if($pay_cstm) echo " checked" ; ?>>지정금액
		<?
		if($client_level>=5 && ($client_id=="yspn" || $client_id=="goodstaff" || $client_id=="sstog" || $client_id=="ktx")) {
			echo "<input type='checkbox' name='admin_q' value='1'"; if($admin_q) { echo " checked"; } echo ">특수";
		}
		?></td>
		<th>결제메모</th>
		<td><input type="text" name="pay_memo" id="pay_memo" value="<?=$pay_memo?>" class="tx80 han"></td>
		<th>할인적용</th>
		<td>
			<select name="dc_apply">
				<option value="0">-선택-</option>
				<option value="1"<? if($dc_apply==1) echo " selected"; ?>>적용중</option>
				<option value="2"<? if($dc_apply==2) echo " selected"; ?>>만료</option>
			</select>
		</td>
		<th>만료일</th>
		<td>
			<input type="text" name="exp_date_bgn" id="exp_date_bgn" value="<?=$exp_date_bgn?>" class="tx_date"  autocomplete='off'> ~ <input type="text" name="exp_date_cls" id="exp_date_cls" value="<?=$exp_date_cls?>" class="tx_date"  autocomplete='off'>
		</td>		
	</tr>
	<tr>
		<th>결제강좌</th>
		<td colspan="3">
			<select name="pay_lec_code" id="pay_lec_code" style='width:200px'>
				<option value="0">-선택-</option>
				<?
				foreach($lect_arr as $key => $val)
				{
					list($v_badge,$v_hide,$v_price,$v_course)=explode("·",$val);
					if($key > 125){
						if($v_hide!="") {
						 	if($v_badge!="") {$select_bg="background:#FFECDD;color:gray";}
						 	else{$select_bg="background:yellow";}	
						}else{$select_bg="background:white";}
						
					}else{
						$select_bg="background:#eeeeee;color:blue";
					}
					echo "<option value='{$key}'";if($key==$pay_lec_code) echo " selected"; echo "  style='{$select_bg}'>{$v_price}{$v_course}</option>";	
					
					//echo "<option value='{$key}'";if($key==$pay_lec_code) echo " selected"; echo "  style='{$select_bg}'>{$val}</option>";	
				}
				?>
			</select>
			<span class="tooltip blue-tooltip" style="position:relative;top:0px;"><p class="btn_whitegray radius_30 center" style='width:17px;font-size:11px'>?</p><span style="position:absolute;left:100px;width:280px;">
				<font color='red'>선택 :</font> 주황-노출(준비중), 노랑-노출, 흰색-비노출, <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;회색-옛강좌(비노출)<br><font color='red'>항목 :</font> ◐ 강좌 준비중, ★ 강좌노출, ♥ 유료강좌 
			</span></span>
		</td>
		<th>기타조건</th>
		<td colspan="3">
			<input type="checkbox" name="out" value="1" id="out"<? if($out) echo " checked"; ?>>탈퇴
			<input type="checkbox" name="youtuber" id="" value="1"<? if($youtuber==1) { echo " checked"; } ?>>유튜버
			<input type="checkbox" name="bid_staff" id="" value="1"<? if($bid_staff==1) { echo " checked"; } ?>>실시간
			<input type="checkbox" name="pay_his" id="pay_his" value="1"<? if($pay_his==1) { echo " checked"; } ?>>과거<br>
			<input type='checkbox' name='ptnr_code_wb' value='20'<? if($ptnr_code_wb=="20") { echo " checked"; } ?>>우리
			<input type='checkbox' name='ptnr_code_kb' value='4'<? if($ptnr_code_kb=="4") { echo " checked"; } ?>>국민
			<input type='checkbox' name='partner_pm' value='1'<? if($partner_pm=="1") { echo " checked"; } ?>>제휴+
		<?
		if($client_level>=5 && ($client_id=="yspn")) {
			?><input type="checkbox" name="pay_his2" id="pay_his2" value="2"<? if($pay_his2==2) { echo " checked"; } ?>>전체<?
		}
		if($client_level>=6) {
			echo "<a href='./member_list_excel.php?{$param_str}'>[엑셀]</a>";
			if($client_id=="yspn") echo "<span onclick='list_excel()' class='hand'>[excel]</span>";
		}
		?>
		</td>
		<td colspan="2">
			<select name="order_type" id="order_type">
				<option value="0">-정렬-</option>
				<?
				foreach($order_arr as $key => $val)
				{
					echo "<option value='{$key}'";if($order_type==$key) echo " selected"; echo ">{$val}</option>";	
				}
				?>
			</select> /
			<select name="list_scale" id="list_scale">
				<?
				foreach($list_scale_arr as $val)
				{
					echo "<option value='{$val}'";if($list_scale==$val) echo " selected"; echo ">{$val}</option>";	
				}
				?>
			</select>개
		</td>
		<td colspan="2" class="center">
			<? if($client_level >= 10 || $client_id == "sstog") :?>
			가입주소 <input type="text" name="addr" id="addr" value="<?=$addr?>" class="tx80"><br>
			<? endif ;?>
			<input type="submit" value="검색하기">
			&nbsp;
			<input type="button" id="btnRest" value="리셋">
			&nbsp;&nbsp;&nbsp;&nbsp;
			<input type="button" id="btnAgent" value="가입대행">
			&nbsp;
			<input type="button" value="문자발송" onclick="send_sms()">
		</td>
	</tr>
</table>
</form>

<div id="ly_agent" style="display:<? echo ($reg_more) ? "block" : "none"; ?>">
<div style="margin-top:10px">&lt;가입 대행&gt;</div>
<form name="fmAgent" id="fmAgent" action="member_db.php" method="post">
	<table class="tbl_grid">
		<tr>
			<th>이름</th>
			<td><input type="text" name="reg_name" id="reg_name" value="<?=$reg_name?>" class="tx80 han"></td>
			<th>아 이 디</th>
			<td>
				<input type="text" name="reg_id" id="reg_id" value="<?=$reg_id?>" class="tx100">
			</td>
			<th>비밀번호</th>
			<td><input type="text" name="passwd" id="passwd" value="<?=$passwd?>" class="tx80"></td>
			<th>휴 대 폰</th>
			<td><input type="text" name="mobile" id="mobile" value="<?=$mobile?>" class="tx80 eng">(- 포함)</td>
			<td colspan="2">
				<span><input type="button" value="ID 체크" id="btnIdCheck"></span>
				<span id="idCheckResult" class="red"></span>
			</td>
		</tr>
		<tr>
			<th>주소</th>
			<td colspan="5">
				<input type="hidden" name="zipcode" id="zipcode" value="<?=$zipcode?>">
				<input type="text" name="address1" id="address1" value="<?=$address1?>" class="tx250 readonly" readonly>
				<input type="button" value="찾기" id="btnSrchAddr" onclick="execDaumPostcode()">
				<input type="text" name="address2" id="address2" value="<?=$address2?>" class="tx250">
			</td>
			<th>일반전화</th>
			<td><input type="text" name="phone" value="<?=$phone?>" class="tx80 eng">(- 포함)</td>
			<td colspan="2">
				<input type="button" value="회원등록" onclick="fmAgentCheck()">
				&nbsp;
				<input type="checkbox" name="reg_more" value="1" <? if($reg_more) echo " checked"; ?>>계속
			</td>
		</tr>
	</table>
	<input type="hidden" name="mode" value="agent">
</form>
</div>
<br>
<table id="mem_list" class="tbl_grid">
	<tr>
		<th width="2%"><input type="checkbox" id="chk_all"></th>
		<th width="5%">No</th>
		<th width="8%">회원명</th>
		<th width="8%">아이디</th>
		<th width="8%">휴대폰</th>
		<th width="8%">가입일</th>
		<th width="8%">결제지역</th>
		<th width="8%">결제금액</th>
		<th width="8%">결제일</th>
		<th width="8%">만료일</th>
		<th width="8%">최근접속</th>
		<th width="12%">결제메모</th>
	</tr>
<?
$hbquery_arr=array();
foreach($_GET as $key => $val)
{
	if(!$val || $key=="id") continue;
	$hbquery_arr[$key]=$val;
}
foreach($_POST as $key => $val)
{
	if(!$val || $key=="id") continue;
	$hbquery_arr[$key]=$val;
}
$params=http_build_query($hbquery_arr);

$lineNo=$total_record-$start;
$curr_stamp=time();
$id_arr=array();
$mobile_arr=array();
//while($rs=mysql_fetch_array($result))
while($rs=$stmt->fetch())
{
	$login=($rs[login]=="0000-00-00 00:00:00") ? "-" : substr($rs[login],0,10);
	$pay_type=$pay_code_arr[$rs[pay_code]];
	$onclick="onclick=\"location.href='member_detail.php?id={$rs[id]}&{$params}'\"";
	if($rs[out_date]=="0000-00-00")
	{
		$id=$rs[id];
		$name=$rs[name];
	}
	else
	{
		$id="<strike class='gray'>".$rs[id]."</strike>";
		$name="<strike class='gray'>".$rs[name]."</strike><br>({$rs[out_date]})";
	}
	$sector="";
	//$sector=$state_arr[$rs[state]];
	if($rs[pay_code]==100) $sector=$state_arr[$rs[state]];
	elseif($rs[pay_code]==101) $sector="<span class='bg_yellow red'>[강좌]</span>".$lect_arr[$rs[sector]];
    elseif($rs[pay_code]==102) $sector="<span class='white' style='background:blue'>[경매교육]</span>".$edu_arr[$rs[sector]];
	$dc_rate="";
	if($today >= $rs[dc_sdate] && $today <= $rs[dc_edate])
	{
		$dc_rate="<span class='white f9 money' style='margin-left:5px;background:#45b417'>DC ".$rs[dc_rate]."</span>";
	}
	if($rs[paykind]==9) {$free_ment="<span class='bg_green green'>무</span>";}
	else{$free_ment="";}

	$pay_free="";
	if(($client_level>=5) && $usr_id) {
		//아이디 검색시 무료제공유무 검색
		$sql_p="SELECT * FROM {$my_db}.tm_pay_history WHERE id='$rs[id]' AND paykind='9' AND money='0' ORDER BY idx DESC";
		//$rep=sql_query($sql_p);
		$stmt_p=$pdo->prepare($sql_p);
		$stmt_p->execute();
		while($rsp=$stmt_p->fetch())
		{
			if($rsp[pay_code]==100) $pay_free.="" . str_replace(" ","",$state_arr[$rsp[state]]) . " " . str_replace("-","/",substr($rsp[startdate],2)) . "~" . str_replace("-","/",substr($rsp[validity],2)) . ", ";
			else $pay_free.="" . str_replace(" ","",$lect_arr[$rsp[sector]]) . " $rsp[startdate]~$rsp[validity], ";
		}
	}
	echo "
		<tr name='tr_members'>
			<td class='center'><input type='checkbox' name='chk_idx' id='chk_idx' value='{$rs[id]}||{$rs[idx]}'></td>
			<td class='center no' {$onclick}>{$lineNo}</td>
			<td {$onclick}>{$name} {$dc_rate}</td>
			<td class='no' {$onclick}>{$id}</td>
			<td class='no' {$onclick}>{$rs[mobile]}</td>
			<td class='center no' {$onclick}>{$rs[reg_date]}</td>
			<td class='f11' {$onclick}>{$sector}{$free_ment}</td>
			<td class='f11 money' {$onclick}>" . number_format($rs[money]) . "</td>
			<td class='center no' {$onclick}>{$rs[paydate]}</td>
			<td class='center no' {$onclick}>{$rs[validity]}</td>
			<td class='center no' {$onclick}>{$login}</td>
			<td class='f11' {$onclick}>{$rs[memo]}"; if($pay_free!="") { echo "<div class='gray'>{$pay_free}</div>"; } echo "</td>
		</tr>";
	if(strchr($usr_id,",")==true) {
		$id_arr[]=$rs[id];
	}
	if(strchr($mobile,",")==true) {
		$mobile_arr[]=$rs[mobile];
	}
	$lineNo--;
}
?>
</table>
<?
$no_id_arr=array();
if(strchr($usr_id,",")==true) {
	foreach($exp_usr_id as $eid_key=>$eid_val) {
		if(!in_array($eid_val,$id_arr)) $no_id_arr[]=$eid_val;
	}
}
if(strchr($mobile,",")==true) {
	foreach($exp_mobile as $emobile_key=>$emobile_val) {
		if(!in_array($emobile_val,$mobile_arr)) $no_mobile_arr[]=$emobile_val;
	}
}
if(count($no_id_arr)>0) echo "<div>검색안된ID : " . implode(", ",$no_id_arr) . "</div>";
if(count($no_mobile_arr)>0) echo "<div>검색안된번호 : " . implode(", ",$no_mobile_arr) . "</div>";
if(!$total_record) echo "<div class='no_result'><span>검색 결과가 없습니다.</span></div>";
if($client_level>=5) {
	echo "
	<form name='memo_fm' id='memo_fm' action='' method='post'>
	<div style='border:1px solid #ddd;margin:10px 0;padding:10px'>
		선택회원 메모일괄넣기 <input type='text' name='old_memo' id='old_memo' value='$old_memo'> => <input type='text' name='new_memo' id='new_memo' value='$new_memo'>
		<input type='button' value='메모넣기' onclick='change_memo()' class='hand'>
		<input type='hidden' name='queryType' id='queryType' value='memo_change'><input type='hidden' name='memo_id' id='memo_id' value=''><span id='url_link'></span>
	</div>
	</form>";
	echo "
	<form name='pay_fm' id='pay_fm' action='' method='post'>
	<div style='border:1px solid #ddd;margin:10px 0;padding:10px'>
		선택회원 일괄결제<br>
		경매결제 : <select name='state' id='state'>
			<option value='0'>선택</option>";
		foreach($state_arr as $state_key=>$area_val) {
			echo "<option value='$state_key'>$area_val</option>";
		}
		echo "
		</select>
		<select name='months' id='months'>
			<option value='1'>1개월</option>
			<option value='2'>2개월</option>
			<option value='3'>3개월</option>
			<option value='6'>6개월</option>
			<option value='12'>12개월</option>
			<option value='700'>7일</option>
		</select>
		, 메모 <input type='text' name='pay_memo' id='pay_memo' value='$pay_memo'><br>
		강좌결제 : <select name='pt_sector' id='pt_sector'>
			<option value='0'>선택</option>";
		foreach($lect_arr as $lec_key=>$lec_val) {
			//echo "<option value='$lec_key'>$lec_val</option>";
		
			list($vl_badge,$vl_hide,$vl_price,$vl_course)=explode("·",$lec_val);
				if($lec_key > 125){
					if($vl_hide!="") {
					 	if($vl_badge!="") {$select_bg="background:#FFECDD;color:gray";}
					 	else{$select_bg="background:yellow";}	
					}else{$select_bg="background:white";}
					
				}else{
					$select_bg="background:#eeeeee;color:blue";
				}
				echo "<option value='{$lec_key}'  style='{$select_bg}'>{$vl_price}{$vl_course}</option>";	
		}
		echo "
		</select>
		, 결제기간 <input type='text' name='pt_validity' id='pt_validity' value='$pt_validity' size='3' class='tx_date'><input type='hidden' name='pt_months' id='pt_months' value='100' size='3'>
		, 강좌메모 <input type='text' name='pt_memo' id='pt_memo' value='$pt_memo'>
		<br>협력업체 : <select name='pt_code' id='pt_code'>
			<option value='0'>선택</option>";
	if(count($partner_arr)>0) {
		foreach($partner_arr as $pt_key=>$pt_val) {
			echo "<option value='$pt_key'>$pt_val</option>";
		}
	}
		echo "</select>";
		echo "
		<input type='button' value='적용' onclick='pay()' class='hand'>
		<input type='hidden' name='queryType' id='queryType' value='pay_ok' size='2'>
		<input type='hidden' name='pay_code' id='pay_code' value='100' size='2'>
		<input type='hidden' name='paykind' id='paykind' value='9' size='2'>
		<input type='hidden' name='bankcode' id='bankcode' value='10' size='2'>
		<input type='hidden' name='pay_id' id='pay_id' value=''><span id='pay_link'></span>
	</div>
	</form>";
}
include $_SERVER["DOCUMENT_ROOT"]."/inc/PageNavi.php";
include($_SERVER["DOCUMENT_ROOT"]."/SuperAdmin/common/footer.php");
?>

<!-- <script src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script> -->
<script src="https://spi.maps.daum.net/imap/map_js_init/postcode.v2.js"></script>
<script type="text/javascript">
var id_list_json;

$(document).ready(function(){
	$(".tx_date").datepicker({
		changeMonth:true,
		changeYear:true,
		showButtonPanel:true
	});
	$("#btnRest").click(function(){
		formInit("#fmSrch");
	});
	
	$("tr[name=tr_members]").mouseover(function(){
		$(this).css({"background":"#e3eefb","cursor":"pointer"});
	}).mouseout(function(){
		$(this).css({"background":"#fff"});
	});
	
	$("#btnAgent").click(function(){
		$("#ly_agent").toggle();
	});
	
	$("#btnIdCheck").click(function(){
		id_check();
	});
	
	//선택한 검색 조건 강조
	formSrchStyle(fmSrch);
	
	//전체 선택/해제
	$("#chk_all").click(function(){
		var bool=(this.checked==true) ? true : false;
		$("input:checkbox[name=chk_idx]").each(function(){
			this.checked=bool;
		})
	});
});

function id_check()
{
	var msg="";
	$("#reg_id").val($.trim($("#reg_id").val()));	
	if($("#reg_id").val()=="")
	{
		alert("아이디와 휴대폰 번호를 입력 하세요.");
		$("#reg_id").focus();
		return;
	}
	$.ajax({
		type: "post",
		url: "/xml/id_check.php",
		data: "tmp_id="+$("#reg_id").val()+"&mobile="+$("#mobile").val(),
		dataType: "xml",
		beforeSend: function(){
			$("#idCheckResult").html("검사중...");
		},
		success: function(xml){
			if($(xml).find("result").text()=="yes") msg="등록가능";
			else msg="등록불가";
	    	$("#idCheckResult").html(msg);
		}
	});
}

//가입대행 폼 체크
function fmAgentCheck()
{
	if($("#reg_name").val()=="")
	{
		alert("회원명을 입력해 주세요~!");
		$("#reg_name").focus();
		return;
	}
	if($("#reg_id").val()=="")
	{
		alert("아이디를 입력해 주세요~!");
		$("#reg_id").focus();
		return;
	}
	if($("#reg_id").val().length < 4)
	{
		alert("아이디가 너무 짧습니다!");
		$("#reg_id").focus();
		return;
	}
	if($("#passwd").val()=="")
	{
		alert("비밀번호를 입력해 주세요~!");
		$("#passwd").focus();
		return;
	}
	$("#fmAgent").submit();
}

//Export Excel
function export_excel()
{
	$("#fmSrch").attr("action","member_excel.php");
	$("#fmSrch").submit();
	
	$("#fmSrch").attr("action","member_list.php");
}

//문자 보내기
function send_sms()
{
	var arr=[];
	$("#mem_list input:checkbox[name=chk_idx]:checked").each(function(){
		var str=$(this).val();
		var sp_uid=str.split("||");
		var uid=sp_uid[0];
		arr.push("'"+uid+"'");
	});
	if(arr.length==0)
	{
		alert("선택한 회원이 없습니다.");
		return;
	}
	var json_str="["+arr.join(",")+"]";
	id_list_json=eval("("+json_str+")");	//String To JSON
	window.open("/SuperAdmin/member/sms_write.php","send_sms","width=800,height=500,scrollbars=yes");
}

//선택회원 메모일괄넣기
function change_memo() {
	var arr=[], html="", msg="";
	if($("#new_memo")=="") {
		alert("메모내용이 없습니다.");
		return;
	}
	$("#mem_list input:checkbox[name=chk_idx]:checked").each(function(){
		var str=$(this).val();
		//var sp_uid=str.split(":");
		//var uid=sp_uid[0];
		arr.push(str);
	});
	if(arr.length==0)
	{
		alert("선택한 회원이 없습니다.");
		return;
	}
	html=arr.join(",");
	$("#memo_id").val(html);
	//$("#url_link").html($("#memo_fm").serialize());
	$.ajax({
		type: "post",
		url: "/SuperAdmin/xml/member_xml.php",
		data: $("#memo_fm").serialize(),
		dataType: "xml",
		beforeSend: function(){
			//
		},
		success: function(xml){
			if($(xml).find("msg").text()=="yes") msg="처리완료";
			else msg="처리불가";
			//alert(msg);
			window.location.reload();
		}
	});
}

//선택회원 일괄결제
function pay() {
	var arr=[], html="", msg="";
	if($("#pay_memo")=="") {
		alert("메모내용이 없습니다.");
		return;
	}
	$("#mem_list input:checkbox[name=chk_idx]:checked").each(function(){
		var str=$(this).val();
		var sp_uid=str.split("||");
		var uid=sp_uid[0];
		arr.push(uid);
	});
	if(arr.length==0)
	{
		alert("선택한 회원이 없습니다.");
		return;
	}
	html=arr.join(",");
	$("#pay_id").val(html);
	//$("#pay_link").html("https://k3.tankauction.com/SuperAdmin/xml/member_xml.php?"+$("#pay_fm").serialize());
	$.ajax({
		type: "post",
		url: "/SuperAdmin/xml/member_xml.php",
		data: $("#pay_fm").serialize(),
		dataType: "xml",
		beforeSend: function(){
			//
			//alert(html);
		},
		success: function(xml){
			var m_cnt=$(xml).find("m_cnt").text();
			var m_cnt1=$(xml).find("m_cnt1").text();
			var m_cnt2=$(xml).find("m_cnt2").text();
			alert("총 "+m_cnt+"건, 신규 "+m_cnt2+"건, 연장 "+m_cnt1+"건 처리");
			window.location.reload();
		}
	});
}

function list_excel() {
	window.open("./member_list_excel.php?"+$("#fmSrch").serialize(),"member_list_excel");
}
</script>