<?
$debug=true;
$page_code=1010;
include($_SERVER["DOCUMENT_ROOT"]."/SuperAdmin/common/header.php");

$hbquery_arr=array();
foreach($_GET as $key => $val)
{
	if(!$val) continue;
	$hbquery_arr[$key]=$val;
}
foreach($_POST as $key => $val)
{
	if(!$val) continue;
	$hbquery_arr[$key]=$val;
}
$params=http_build_query($hbquery_arr);

$today=date("Y-m-d");
$dtm=date("Y-m-d H:i:s");
//경매 지역 구분
//$result=sql_query("SELECT state,area FROM {$my_db}.tc_price ORDER BY sort_num");
//while($rs=mysql_fetch_array($result))
$stmt=$pdo->prepare("SELECT state,area FROM {$my_db}.tc_price ORDER BY sort_num");
$stmt->execute();
while($rs=$stmt->fetch()){$state_arr[$rs[state]]=$rs[area];}

//협력업체
////$SQL=sql_query("SELECT sangho,code FROM {$my_db}.tz_partner WHERE sdate <= CURDATE() AND (edate='0000-00-00' OR edate >= CURDATE())");
//$SQL=sql_query("SELECT sangho,code,edate FROM {$my_db}.tz_partner WHERE 1");

$stmt=$pdo->prepare("SELECT sangho,code,edate FROM {$my_db}.tz_partner WHERE 1");
$stmt->execute();
$curdate=date("Y-m-d");
//while($rs=mysql_fetch_array($SQL))
while($rs=$stmt->fetch())
{
	if($rs[edate] > "0000-00-00" && $rs[edate] < $curdate) $partner_arr[$rs[code]]="<span class='gray' style='text-decoration:line-through'>".$rs[sangho]."</span>";
	else $partner_arr[$rs[code]]=$rs[sangho];
}

//동영상 강좌 구분
//$result=sql_query("SELECT lec_code,course FROM {$my_db}.te_lecture WHERE ctgr BETWEEN 20 AND 33");
//while($rs=mysql_fetch_array($result))
$stmt=$pdo->prepare("SELECT lec_code,course FROM {$my_db}.te_lecture WHERE ctgr BETWEEN 20 AND 33");
$stmt->execute();
while($rs=$stmt->fetch()){$lect_arr[$rs[lec_code]]=$rs[course];}

// 경매교육 구분
$stmt=$pdo->prepare("SELECT edu_code,edu_title FROM {$my_db}.tl_edu WHERE 1");
$stmt->execute();
while($rs=$stmt->fetch()){$edu_arr[$rs[edu_code]]=$rs[edu_title];}

//관심물건 현황
$icount_arr=array(); $icount_arr[1]=0; $icount_arr[2]=0;
//$result=sql_query("SELECT COUNT(*),itype FROM {$my_db}.tm_interest WHERE id='{$id}' GROUP BY itype");
//while($rs=mysql_fetch_array($result))
$stmt=$pdo->prepare("SELECT COUNT(*),itype FROM {$my_db}.tm_interest WHERE id='{$id}' GROUP BY itype");
$stmt->execute();
while($rs=$stmt->fetch()){$icount_arr[$rs[itype]]=number_format($rs[0]);}
//관심물건 휴면상태
//$result=sql_query("SELECT COUNT(*) FROM {$my_db}.tm_interest_sleep WHERE id='{$id}'");
//$rs=mysql_fetch_row($result);
$stmt=$pdo->prepare("SELECT COUNT(*) FROM {$my_db}.tm_interest_sleep WHERE id='{$id}'");
$stmt->execute();
$rs=$stmt->fetch();
$icount_sleep=$rs[0];

//회원 기본정보
//$result=sql_query("SELECT * FROM {$my_db}.tm_member WHERE id='{$id}' LIMIT 0,1");
//$rs=mysql_fetch_array($result);
$stmt=$pdo->prepare("SELECT * FROM {$my_db}.tm_member WHERE id='{$id}' LIMIT 0,1");
$stmt->execute();
$rs=$stmt->fetch();
$last_login=($rs[login]=="0000-00-00 00:00:00") ? "-" : $rs[login];
$user_name=$rs[name];
$partner_info="없음";
if($rs[partner])
{
	$tmp_part_arr=explode("|",$rs[partner]);
	foreach($tmp_part_arr as $key)
	{
		if(!array_key_exists($key,$partner_arr)) continue;
		$pi_arr[]=$partner_arr[$key];
	}
	$partner_info=implode(",",$pi_arr);
}

//이용료 할인
if($rs[dc_rate])
{
	if($today >= $rs[dc_sdate] && $today <= $rs[dc_edate])
	{
		$dc_rate=$rs[dc_rate];
		$dc_sdate=$rs[dc_sdate];
		$dc_edate=$rs[dc_edate];
	}
}

//은행정보-관리자용
$bank_arr=array(10 => array("name"=>"국민은행",		"no"=>"361437-04-012881"));
				//11 => array("name"=>"국민은행(동)",	"no"=>"361437-04-010225"));
?>

<!-- 회원기본정보 -->
<div>
<span class="bold">회원 기본정보</span>
<div style="margin:10px 0">
<table class="tbl_noline">
	<tr>
		<th width="60%"></th>
		<th>
			* <a href="javascript:open_inter_list('<?=$id?>','<?=$user_name?>')" class="blue">관심물건</a> &gt;
			경매 <span class="bold no"><?=$icount_arr[1]?></span>, 공매 <span class="bold no"><?=$icount_arr[2]?></span>
			<? if($icount_sleep > 0) : ?>
			<a href="javascript:sleep_recover('<?=$id?>')" class="green">(휴:<?=number_format($icount_sleep)?>)</a>
			<? endif; ?>
		</th>
		<th>* <a href="javascript:open_conn_history('<?=$id?>','<?=$user_name?>')" class="blue">최근접속</a></th>
		<th>* <a href="javascript:open_pdview_history('<?=$id?>','<?=$user_name?>')" class="blue">최근열람</a></th>
		<td>* <a href="javascript:open_sms_history('<?=$id?>','<?=$user_name?>')" class="blue">문자내역</a></td>
	</tr>
</table>
</div>
<form name="fm_user_info" id="fm_user_info" action="member_db.php" method="post" enctype="multipart/form-data">
<table class="tbl_grid">
	<tr>
		<th>회원명</th>
		<td><input type="text" name="user_name" id="user_name" value="<?=$rs[name]?>" class="tx100"></td>
		<th>아이디</th>
		<td><input type="text" name="user_id" id="user_id" value="<?=$id?>" class="tx80 readonly"></td>
		<th>휴대폰</th>
		<td>
			<input type="text" name="mobile" id="mobile" value="<?=$rs[mobile]?>" class="tx80">
			<a href="javascript:send_sms('<?=$id?>')" class="blue">[문자발송]</a>
		</td>
		<th>일반전화</th>
		<td><input type="text" name="phone" id="phone" value="<?=$rs[phone]?>" class="tx80"></td>
		<th>팩스번호</th>
		<td><input type="text" name="fax" id="fax" value="<?=$rs[fax]?>" class="tx80"></td>
	</tr>
	<tr>
		<th>정보수신</th>
		<td>
			<input type="checkbox" name="sms" value="1"<? if($rs[sms]) echo " checked"; ?>>문자
			&nbsp;
			<input type="checkbox" name="r_mail" value="1"<? if($rs[r_mail]) echo " checked"; ?>>E-Mail
		</td>
		<th>비밀번호</th>
		<td><input type="text" name="passwd" id="passwd" value="" class="tx80">
			<input type="checkbox" name="chk_new_pwd" value="y">변경
		</td>
		<th>E-메일</th>
		<td><input type="text" name="email" id="email" value="<?=$rs[email]?>" class="tx150"></td>
		<th>가입일</th>
		<td><?=$rs[reg_date]?></td>
		<th>최근접속</th>
		<td><?=$last_login?></td>
	</tr>
	<tr>
		<th>가입주소</th>
		<td colspan="5">
			<input type="hidden" name="zipcode" id="zipcode" value="<?=$rs[zipcode]?>">
			<input type="hidden" name="bmng_no" id="bmng_no" value="<?=$rs[bmng_no]?>">
			<input type="text" name="address1" id="address1" value="<?=$rs[address1]?>" class="tx250 readonly" readonly>
			<input type="button" id="btnSrchAddr" value="찾기" onclick="execDaumPostcode()">
			<input type="text" name="address2" id="address2" value="<?=$rs[address2]?>" class="tx250">
		</td>
		<th>메모</th>
		<td>
			<input type="text" name="m_memo" id="m_memo" value="<?=$rs[m_memo]?>" class="tx80">
		</td>
		<th>지정금액</th>
		<td>
			<input type="checkbox" name="pay_custom" value="1"<? if($rs[pay_custom]) echo " checked"; ?>>결제허용
		</td>
		<!--
		<th>추천 ID</th>
		<td colspan="3"><input type="text" name="rec_id" id="rec_id" value="<?=$rs[rec_id]?>" class="tx80"></td>
		-->
	</tr>
	<tr>
		<th<? if($dc_rate) echo " class='orange bold'"?>>할인적용</th>
		<td colspan="3">
			<select name="dc_rate">
				<option value="0">없음</option>
			<?
			for($i=10;$i<=50;$i+=10)
			{
				echo "<option value='{$i}'";if($rs[dc_rate]==$i) echo " selected"; echo ">{$i} %</option>";
			}
			?>
			</select>
			&nbsp;
			<input type="text" name="dc_sdate" id="dc_sdate" value="<?=$rs[dc_sdate]?>" class="tx_date">
			~
			<input type="text" name="dc_edate" id="dc_edate" value="<?=$rs[dc_edate]?>" class="tx_date">
		</td>
	<? if($client_level>=5) { ?>
		<th>협력업체</th>
		<td colspan="3">
			<span id="partner_info"><?=$partner_info?></span>
			&gt; <a href="javascript:open_partner()" class="blue">업체선택</a>
		</td>
	<? } ?>
		<th>기타</th>
		<td>
			<input type="checkbox" name="youtuber" value="1"<? if($rs[youtuber]) echo " checked"; ?>>유튜버
			<input type="checkbox" name="bid_staff" value="1"<? if($rs[bid_staff]) echo " checked"; ?>>실시간
			<input type='radio' name='ptnr_code' id='ptnr_code' value='0'<? if($rs[ptnr_code]<=0) { echo " checked"; } ?>>X
			<!--<input type='radio' name='ptnr_code' id='ptnr_code' value='20'<? if($rs[ptnr_code]==20) { echo " checked"; } ?>>우리
			<input type='radio' name='ptnr_code' id='ptnr_code' value='4'<? if($rs[ptnr_code]==4) { echo " checked"; } ?>>국민-->
			<input type='checkbox' name='partner_pm' id='partner_pm' value='1'<? if($rs[partner_pm]==1) { echo " checked"; } ?>>제휴+
		</td>
	</tr>
</table>
</div>
<!-- //회원기본정보 -->
<div style="margin:10px">
	<table class="tbl_noline">
		<tr>
			<td width="33%"></td>
			<td width="34%" class="center"><input type="button" id="btn_save_info" value="저장하기"></td>
			<td width="33%" class="right"><input type="button" value="목록으로" onclick="location.href='member_list.php?<?=$params?>'"></td>
		</tr>
	</table>
</div>
<input type="hidden" name="mode" value="profile_edit">
<input type="hidden" name="params" value="<?=$params?>">
<input type="hidden" name="partner" id="partner" value="<?=$rs[partner]?>">
</form>
<br>
<!-- 회원관련 기록 -->
<div>
	<span class="bold">회원 관리정보</span>
	<form name="fm_history" id="fm_history">
	<div style="float:left;width:60%;height:130px">	
		<table class="tbl_nlist">
			<thead>
				<tr>
					<th style="width:7%">No</th>
					<th style="width:13%">등록일</th>
					<th style="width:60%">내용</th>
					<th style="width:8%">삭제</th>
					<th style="width:12%">담당자</th>
				</tr>
			</thead>
			<tbody id="list_body"></tbody>
		</table>
		<div id="page_navi"></div>
		<input type="hidden" name="history_mode" id="history_mode" value="">
		<input type="hidden" name="user_id" value="<?=$id?>">
		<input type="hidden" name="list_scale" id="list_scale" value="3">
		<input type="hidden" name="page_scale" id="page_scale" value="10">
		<input type="hidden" name="start" id="start" value="0">
		<input type="hidden" name="total_record" id="total_record" value="0">
		<input type="hidden" name="idx_arr" id="idx_arr" value="">
		<input type="hidden" name="ref_start" id="ref_start" value="">	
	</div>
	<div style="float:right">
		<textarea rows="5" name="history_memo" id="history_memo" class="ta350 han"></textarea>
		<div class="center" style="margin-top:5px">
			등록일 <input type="text" id="his_wdate" name="wdate" value="<?=date("Y-m-d")?>" class="tx_date">
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<input type="button" id="btn_add_history" value="등 록">
		</div>
	</div>	
	</form>
</div>
<br>
<!-- //회원관련 기록 -->
<div class="clear"></div>

<div>
<span class="bold">결제 처리</span>
<form name="fmAGS_pay" id="fmAGS_pay" action="pay_db.php" method="post">
<table class="tbl_grid">
	<tr>
		<th width="130px" class="center">결제종류</th>
		<td>
		<? foreach($pay_code_arr as $k => $v) : ?>
			<label for="pay_code_<?=$k?>" class="hand"><input type="radio" id="pay_code_<?=$k?>" name="pay_code" value="<?=$k?>"<? if($k==100) echo " checked"; ?> onclick="payform_ctrl(<?=$k?>)"> <?=$v?></label> &nbsp;
		<? endforeach; ?>
		</td>
	</tr>
</table>
<div class="clear" style="height:10px"></div>
<!-- 요금테이블(경매) -->
<table class="tbl_grid" id="tbl_price_auct">
	<tr>
		<th width="130px">지역/법원</th>
		<th>해당지역</th>
		<th width="100px">1개월</th>
		<th width="100px">3개월</th>
		<th width="100px">6개월</th>
		<th width="100px">12개월</th>
	</tr>
<?
//$result=sql_query("SELECT * FROM {$my_db}.tc_price WHERE use_key=1 ORDER BY sort_num");
//while($rs=mysql_fetch_array($result))
$stmt=$pdo->prepare("SELECT * FROM {$my_db}.tc_price WHERE use_key=1 ORDER BY sort_num");
$stmt->execute();
while($rs=$stmt->fetch())
{	
	if($dc_rate && $rs[state]==99)
	{
		//$ap_01=round($rs[price_01]*(1-$dc_rate*0.01)/1000)*1000;
		//$ap_03=round($rs[price_03]*(1-$dc_rate*0.01)/1000)*1000;
		//$ap_06=round($rs[price_06]*(1-$dc_rate*0.01)/1000)*1000;
		$ap_01=$rs[price_01];
		$ap_03=$rs[price_03];
		$ap_06=$rs[price_06];
		$ap_12=round($rs[price_12]*(1-$dc_rate*0.01)/1000)*1000;
		
		$price_arr[$rs[state]]=array("area" => urlencode($rs[area]), "grp" => $rs[group_key], "par" => $rs[par_state], "sub" => $rs[sub_state], 1 => $ap_01, 3 => $ap_03, 6 => $ap_06, 12 => $ap_12);
		$price_01=str_replace("_","&nbsp;",str_pad(number_format($ap_01),8,"_",STR_PAD_LEFT));
		$price_03=str_replace("_","&nbsp;",str_pad(number_format($ap_03),8,"_",STR_PAD_LEFT));
		$price_06=str_replace("_","&nbsp;",str_pad(number_format($ap_06),8,"_",STR_PAD_LEFT));
		$price_12=str_replace("_","&nbsp;",str_pad(number_format($ap_12),8,"_",STR_PAD_LEFT));
		
		$bp_01=str_replace("_","&nbsp;",str_pad(number_format($rs[price_01]),10,"_",STR_PAD_LEFT));
		$bp_03=str_replace("_","&nbsp;",str_pad(number_format($rs[price_03]),10,"_",STR_PAD_LEFT));
		$bp_06=str_replace("_","&nbsp;",str_pad(number_format($rs[price_06]),10,"_",STR_PAD_LEFT));
		$bp_12=str_replace("_","&nbsp;",str_pad(number_format($rs[price_12]),10,"_",STR_PAD_LEFT));
	}
	else
	{
		$price_arr[$rs[state]]=array("area" => urlencode($rs[area]), "grp" => $rs[group_key], "par" => $rs[par_state], "sub" => $rs[sub_state], 1 => $rs[price_01], 3 => $rs[price_03], 6 => $rs[price_06], 12 => $rs[price_12]);
		$price_01=str_replace("_","&nbsp;",str_pad(number_format($rs[price_01]),8,"_",STR_PAD_LEFT));
		$price_03=str_replace("_","&nbsp;",str_pad(number_format($rs[price_03]),8,"_",STR_PAD_LEFT));
		$price_06=str_replace("_","&nbsp;",str_pad(number_format($rs[price_06]),8,"_",STR_PAD_LEFT));
		$price_12=str_replace("_","&nbsp;",str_pad(number_format($rs[price_12]),8,"_",STR_PAD_LEFT));	
	}
	
	$bg_grp=($rs[state]==99) ? "bg_red" : "";
	$tx_grp=($rs[group_key]==1 || $rs[state]==99) ? " bold center" : " center";
	//if($rs[state]==99) $bg_grp="bg_red";
	if($dc_rate && $rs[state]==99)
	{
		/*
		echo "
		<tr id='row_{$rs[state]}' name='row' class='{$bg_grp}'>
			<td class='{$tx_grp}'>{$rs[area]}</td>
			<td>".str_replace("<br>","",$rs[service_area])."</td>
			<td class='center' style='font-family:돋움체'><label for='chk_{$rs[state]}_1' class='hand'><span class='gray'><strike>{$bp_01}</strike></span><br><input type='checkbox' id='chk_{$rs[state]}_1' name='' value='{$rs[state]}'>{$price_01}</label></td>
			<td class='center' style='font-family:돋움체'><label for='chk_{$rs[state]}_3' class='hand'><span class='gray'><strike>{$bp_03}</strike></span><br><input type='checkbox' id='chk_{$rs[state]}_3' name='' value='{$rs[state]}'>{$price_03}</label></td>
			<td class='center' style='font-family:돋움체'><label for='chk_{$rs[state]}_6' class='hand'><span class='gray'><strike>{$bp_06}</strike></span><br><input type='checkbox' id='chk_{$rs[state]}_6' name='' value='{$rs[state]}'>{$price_06}</label></td>
			<td class='center' style='font-family:돋움체'><label for='chk_{$rs[state]}_12' class='hand'><span class='gray'><strike>{$bp_12}</strike></span><br><input type='checkbox' id='chk_{$rs[state]}_12' name='' value='{$rs[state]}'>{$price_12}</label></td>
		</tr>";
		*/
		echo "
		<tr id='row_{$rs[state]}' name='row' class='{$bg_grp}'>
			<td class='{$tx_grp}'>{$rs[area]}</td>
			<td>".str_replace("<br>","",$rs[service_area])."</td>
			<td class='center' style='font-family:돋움체'><label for='chk_{$rs[state]}_1' class='hand'><input type='checkbox' id='chk_{$rs[state]}_1' name='' value='{$rs[state]}'>{$price_01}</label></td>
			<td class='center' style='font-family:돋움체'><label for='chk_{$rs[state]}_3' class='hand'><input type='checkbox' id='chk_{$rs[state]}_3' name='' value='{$rs[state]}'>{$price_03}</label></td>
			<td class='center' style='font-family:돋움체'><label for='chk_{$rs[state]}_6' class='hand'><input type='checkbox' id='chk_{$rs[state]}_6' name='' value='{$rs[state]}'>{$price_06}</label></td>
			<td class='center bold' style='font-family:돋움체'><label for='chk_{$rs[state]}_12' class='hand'><span class='gray'><strike>{$bp_12}</strike></span><br><input type='checkbox' id='chk_{$rs[state]}_12' name='' value='{$rs[state]}'>{$price_12}</label></td>
		</tr>";
	}
	else
	{
		echo "
		<tr id='row_{$rs[state]}' name='row' class='{$bg_grp}'>
			<td class='{$tx_grp}'>{$rs[area]}</td>
			<td>".str_replace("<br>","",$rs[service_area])."</td>
			<td class='center' style='font-family:돋움체'><label for='chk_{$rs[state]}_1' class='hand'><input type='checkbox' id='chk_{$rs[state]}_1' name='' value='{$rs[state]}'>{$price_01}</label></td>
			<td class='center' style='font-family:돋움체'><label for='chk_{$rs[state]}_3' class='hand'><input type='checkbox' id='chk_{$rs[state]}_3' name='' value='{$rs[state]}'>{$price_03}</label></td>
			<td class='center' style='font-family:돋움체'><label for='chk_{$rs[state]}_6' class='hand'><input type='checkbox' id='chk_{$rs[state]}_6' name='' value='{$rs[state]}'>{$price_06}</label></td>
			<td class='center' style='font-family:돋움체'><label for='chk_{$rs[state]}_12' class='hand'><input type='checkbox' id='chk_{$rs[state]}_12' name='' value='{$rs[state]}'>{$price_12}</label></td>
		</tr>";	
	}
}
?>
</table>
<!-- //요금테이블(경매) -->


<!-- 요금테이블(강좌) -->
<table class="tbl_grid" id="tbl_price_lect" style="display:none">
	<tr>
		<th width="130px">선택</th>
		<th>구분</th>
		<th>동영상</th>
		<th>강좌명</th>
		<th>강사</th>
		<th>강좌수</th>		
		<th>수강일수</th>
		<th>금액</th>
	</tr>
<?
//$result=sql_query("SELECT * FROM {$my_db}.te_lecture WHERE hide=0 AND price > 0 AND ctgr BETWEEN 20 AND 33");
//while($rs=mysql_fetch_array($result))
$stmt=$pdo->prepare("SELECT * FROM {$my_db}.te_lecture WHERE hide=0 AND price > 0 AND ctgr BETWEEN 20 AND 33");
$stmt->execute();
while($rs=$stmt->fetch())
{
	// 기사단 동영상 표기
	if($rs[cg_kind_mv] ==1) {
		$code_ment="<span class='red'>기사단 동영상</span>";
		//$rs[price]="0";
	}else{
		$code_ment="";
		//$rs[price]="$rs[price]";
	}
	
	$price_lec_arr[$rs[lec_code]]=array("course"=>urlencode($rs[course]), "days"=>$rs[days], "price"=>$rs[price]);
	
	echo "
	<tr>
		<td class='center'><input type='checkbox' id='chk_{$rs[lec_code]}_{$rs[days]}' name='' value='{$rs[lec_code]}'></td>
		<td class='center orange'>{$code_ment}</td>
		<td class='center orange'>{$lec_ctgr_arr[$rs[ctgr]]}</td>
		<td>{$rs[course]}</td>
		<td class='center'>{$rs[teacher]}</td>
		<td class='center'>{$rs[lec_cnt]}</td>
		<td class='center'>{$rs[days]}</td>
		<td class='right'>".number_format($rs[price])."</td>
	</tr>";
}
?>
</table>
<!-- 요금테이블(오프라인강좌) -->

<table class="tbl_grid" id="tbl_price_off" style="display:none">
	<tr>
		<th>강좌명</th>
		<th>강사</th>
        <th>시작일자</th>
        <th>종료일자</th>
		<th>금액</th>
        <th>(오프라인)모집정원</th>
        <th>온라인</th>
        <th>오프라인</th>
	</tr>
<?
$stmt=$pdo->prepare("SELECT * FROM {$my_db}.tl_edu WHERE rdate<='{$dtm}'");
$stmt->execute();
$html=array();
while($rs=$stmt->fetch())
{
    $tr="<tr id='row_{$rs[edu_code]}'>";
    $tr.="<td>{$rs['edu_title']} {$on_off_ment}</td>";
    $tr.="<td class='center'>{$rs['edu_teacher']}</td>";
    $tr.="<td class='center'>".substr($rs['sdate'],5,5)."</td>";
    $tr.="<td class='center'>".substr($rs['edate'],5,5)."</td>";
    $tr.="<td class='right'>".number_format($rs['edu_pay'])."</td>";
    $tr.="<td class='center'>{$rs['pay_people']}/{$rs['edu_people']}</td>";

    $off_o="<td class='center'><input type='radio' id='chk_{$rs[edu_code]}_0' name=\"edu\" value='{$rs['edu_pay']}' data-code='{$rs['edu_title']}'></td>"; //오프라인 오픈
    $off_x="<td class='center'><input type='radio' id='chk_{$rs[edu_code]}_0' name=\"edu\" value='{$rs['edu_pay']}' data-code='{$rs['edu_title']}' disabled></td>"; //오프라인 오프
    $on_o="<td class='center'><input type='radio' id='chk_{$rs[edu_code]}_1' name=\"edu\" value='{$rs['edu_pay']}' data-code='{$rs['edu_title']}'></td>"; //온라인 오픈
    $on_x="<td class='center'><input type='radio' id='chk_{$rs[edu_code]}_1' name=\"edu\" value='{$rs['edu_pay']}' data-code='{$rs['edu_title']}'  disabled></td>"; //오프라인 오프
    
    if($rs['state']==0 && $rs['sdate']<$today){$tr="";}

    $html[]=$tr;
    switch($rs['state'])
    {
        ##자동
        case 0 :
        {
            if($rs['sdate']>=$today)
            {
                if($rs['on_off']==0){$html[]=$on_x;$html[]=($rs['pay_people']>=$rs['edu_people'])?$off_x:$off_o;}
                elseif($rs['on_off']==1){$html[]=$on_o;$html[]=$off_x;}
                else{$html[]=$on_o;$html[]=($rs['pay_people']>=$rs['edu_people'])?$off_x:$off_o;}
            }
        }break;
        ##전체 마감(수동)
        case 1 : 
        {
            $html[]=$on_x;
            $html[]=$off_x;
        }break;
        ##오프라인 마감(수동)
        case 2:
        {
            if($rs['on_off']==0){$html[]=$on_x;$html[]=$off_x;}
            else{$html[]=$on_o;$html[]=$off_x;}
        }break;
        ##결제창 활성화(수동)
        case 3:
        {
            if($rs['on_off']==0){$html[]=$on_x;$html[]=($rs['pay_people']>=$rs['edu_people'])?$off_x:$off_o;}
            elseif($rs['on_off']==1){$html[]=$on_o;$html[]=$off_x;}
            else{$html[]=$on_o;$html[]=($rs['pay_people']>=$rs['edu_people'])?$off_x:$off_o;}
        }break;
    }
    $html[]="</tr>";
}
$html[]="</table>";
$html=implode("",$html);
echo $html;
?>
<!-- //요금테이블(강좌) -->

<div class="clear" style="height:20px"></div>
<table class="tbl_grid">
	<tr>
		<th width="12%" class="right">선택지역 &gt;</th>
		<td width="31%" id="area_info" class="bold blue">선택전</td>
		<th width="12%" class="right">결제 방법 &gt;</th>
		<td width="45%">
			<label for="paykind2"><input type="radio" name="paykind" value="2" id="paykind2" class="input_rdo" checked>통장</label>&nbsp;
			<label for="paykind1"><input type="radio" name="paykind" value="1" id="paykind1" class="input_rdo">카드</label>&nbsp;
			<label for="paykind3"><input type="radio" name="paykind" value="3" id="paykind3" class="input_rdo">계좌</label>&nbsp;
			<label for="paykind4"><input type="radio" name="paykind" value="3" id="paykind4" class="input_rdo">가상계좌</label>&nbsp;
			<label for="paykind9"><input type="radio" name="paykind" value="9" id="paykind9" class="input_rdo">무료</label>
			&nbsp;&nbsp;
			☞ 단체(전국)
			<label for="rdo_free0"><input type="radio" name="free_month" value="0" id="rdo_free0" onclick="fnFree(this)">X</label>
			<label for="rdo_free1"><input type="radio" name="free_month" value="1" id="rdo_free1" onclick="fnFree(this)">1개월</label>
			<label for="rdo_free2"><input type="radio" name="free_month" value="2" id="rdo_free2" onclick="fnFree(this)">2개월</label>
		</td>
	</tr>
	<tr>
		<th class="right">이 용 료 &gt;</th>
		<td id="amt_info" class="bold orange no">0 원</td>
		<!--
		<th class="right">입금 은행 &gt;</th>
		<td>
	  <select name="bank_code" id="bank_code">
		    <option value="0">-선택-</option>
		  <? foreach($arr_payBank as $key => $val){echo "<option value='{$key}'";if($key==10) echo " selected"; echo ">{$val}</option>";}  ?>
		</select>
		
		<?
		//foreach($bank_arr as $key => $arr){echo "<label for='bankcode_{$key}'><input type='radio' name='bankcode' id='bankcode_{$key}' value='{$key}'"; if($key==10) echo " checked"; echo ">{$arr[name]}</label>&nbsp;";}
		?>
		</td>
		-->
		<th class="right">입금자/결제일 &gt;</th>
		<td>
			<input type="text" name="payname" id="payname" value="<?=$user_name?>" class="tx100"> /
			<input type="text" name="paydate" id="paydate" value="<?=date('Y-m-d')?>" class="tx_date">
		</td>
	</tr>
	<tr>
		<th class="right">메 모 &gt;</th>
		<td colspan="3">
			<input type="text" name="memo" id="memo" value="입금미확인" class="tx200">
			<input type="button" value=" 결제처리 " onclick="pay()">
		</td>
	</tr>
</table>
<input type="hidden" name="srv_price" id="srv_price" value="0">
<input type="hidden" name="pay_info" id="pay_info" value="">
<input type="hidden" name="user_id" id="id" value="<?=$id?>">
<input type="hidden" name="user_name" id="uname" value="<?=$user_name?>">
<input type="hidden" id="amt" name="amt" value="0">
<input type="hidden" id="smp" name="smp" value="0">
<input type="hidden" id="dc_rate" name="dc_rate" value="<?=$dc_rate?>">
<input type="hidden" name="free_memo" value="<?=$_COOKIE['GAC_PAY_MEMO']?>">
</form>
</div>
<br>
<!-- 결제 내역 -->
<?
$pi_arr=array();
$order_arr=array();
$pay_sum=0;

$condition="P.order_no=L.order_no AND P.id=L.id AND P.id='{$id}'";
$SQL ="SELECT COUNT(order_no) AS cnt,order_no,pay_code,money,state,paydate,paykind,pay_price,months,bankcode,payname,point,dc_rate FROM (";
$SQL.="SELECT L.order_no,pay_code,money,state,paydate,pay_price,paykind,months,bankcode,payname,point,L.dc_rate FROM {$my_db}.tm_pay_result P, {$my_db}.tm_pay_list L WHERE {$condition} ";
$SQL.="UNION ALL ";
$SQL.="SELECT L.order_no,pay_code,money,state,paydate,pay_price,paykind,months,bankcode,payname,point,L.dc_rate FROM {$my_db}.tm_pay_history P, {$my_db}.tm_pay_list L WHERE {$condition} ";
$SQL.=") T GROUP BY order_no ORDER BY order_no DESC";
if($client_id=="ice") echo $SQL;
//exit;

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
	$p_arr[$rs[order_no]]=array("rowspan" => $rs[cnt], "point" => $rs[point], "paykind" => $paykind, "paydate" => $rs[paydate], "amt" => $rs[pay_price], "pmt" => $pmt, "bankcode" => $rs[bankcode], "payname" => $rs[payname], "dc_rate" => $rs[dc_rate]);
}
//print_r($row_arr);
//exit;
?>
<div>
<span class="bold">결제 내역</span>
<table class="tbl_grid">
	<tr>
		<th width="3%">No</th>
		<th width="12%">결제구분</th>
		<th width="4%">기간</th>
		<th width="7%">이용료</th>
		<!--<th width="6%">포인트</th>-->
		<th width="7%">결제금액</th>
		<th width="13%">결제방법</th>
		<th width="8%">결제일</th>
		<th width="8%">시작일</th>
		<th width="8%">만료일</th>
		<th width="5%">잔여일</th>
		<th width="19%">메모</th>
	</tr>
<?
$SQL ="(SELECT P.order_no,P.idx,P.id,pay_code,state,sector,months,money,paykind,paydate,validity,point,L.dc_rate,pay_price,bankcode,payname,startdate,vp_sdate,vp_edate,sp_sdate,sp_edate,memo,1 AS tbl_key FROM {$my_db}.tm_pay_result  P , {$my_db}.tm_pay_list L WHERE {$condition}) ";
$SQL.="UNION ALL ";
$SQL.="(SELECT P.order_no,P.idx,P.id,pay_code,state,sector,months,money,paykind,paydate,validity,point,L.dc_rate,pay_price,bankcode,payname,startdate,vp_sdate,vp_edate,sp_sdate,sp_edate,memo,2 AS tbl_key FROM {$my_db}.tm_pay_history P , {$my_db}.tm_pay_list L WHERE {$condition}) ";
$SQL.="ORDER BY order_no DESC";
//$result=sql_query($SQL);
$stmt=$pdo->prepare($SQL);
$stmt->execute();
		
//while($rs=mysql_fetch_array($result))
while($rs=$stmt->fetch())
{
	if(!$p_arr[$rs[order_no]]) continue;
	$sector="";
	if($rs[pay_code]==100) $sector=$state_arr[$rs[state]];
	elseif($rs[pay_code]==101) $sector="<span class='bg_yellow red'>[강좌]</span>".$lect_arr[$rs[sector]];
    elseif($rs[pay_code]==102) $sector="<span class='bg_yellow blue'>[경매교육]</span>".$edu_arr[$rs[sector]];

	$remain="";
	if($rs[validity] >= $today)
	{
		$expire_date=explode("-",$rs[validity]);
		$expire_time=mktime(0,0,0,$expire_date[1],$expire_date[2],$expire_date[0]);
		$remain=floor(($expire_time - time() + 86400) / 86400);
		if($remain < 0) $remain="";
	}
	
	$no++;
	$rowspan=$p_arr[$rs[order_no]][rowspan];
	if($rs[tbl_key]==1)
	{
		$bg_color=($remain) ? "#e6ffd6" : "#fff";	
	}
	else
	{
		$bg_color="#ccc";
	}	
	$pay_recover=($rs[tbl_key]==2 && $remain > 0) ? " <a href='javascript:pay_recover({$rs[idx]})' class='bold ltblue'>R</a>" : "";
	echo "
	<tr style='background:{$bg_color}'>
		<td class='center'>{$no}</td>
		<td class='f11'>
			<a href=\"javascript:pay_edit({$rs[tbl_key]},{$rs[idx]},'{$rs[order_no]}','{$id}')\">{$sector}</a>
			{$pay_recover}
		</td>
		<td class='center'>{$rs[months]}</td>
		<td class='money'>";
		//echo number_format($rs[money]);
		if($rowspan>=2) {
			if($rs[pay_code]!="100") $pmt_chk=$rs[money];
			elseif($rs[dc_rate]>0) $pmt_chk=($rs[pay_price]*100)/(100-$rs[dc_rate]);
			else $pmt_chk=$rs[money];
			echo number_format($pmt_chk);
		} else {
			echo number_format($p_arr[$rs[order_no]][pmt]);
		}
		echo "</td>";
	if($rs[order_no] != $prev)
	{
		$dc_flag=($p_arr[$rs[order_no]][dc_rate]) ? "<span class='white f9' style='margin-left:5px;background:#45b417'>DC ".$p_arr[$rs[order_no]][dc_rate]."</span><br>" : "";
		echo "
		<!--<td rowspan='{$rowspan}' class='money'>".number_format($p_arr[$rs[order_no]][point])."</td>-->
		<td rowspan='{$rowspan}' class='money'>{$dc_flag}<span class='bold'>".number_format($p_arr[$rs[order_no]][amt])."</span></td>
		<td rowspan='{$rowspan}' class='f11 right'>".str_replace(' ','',$p_arr[$rs[order_no]][paykind])."</td>
		<td rowspan='{$rowspan}' class='center'>".str_replace("-",".",$p_arr[$rs[order_no]][paydate])."</td>";
	}
		echo "
		<td class='center'>".str_replace("-",".",$rs[startdate])."</td>
		<td class='center'>".str_replace("-",".",$rs[validity])."</td>
		<td class='money bold'>{$remain}</td>
		<td class='f11'>{$rs[memo]}</td>
	</tr>";
	$prev=$rs[order_no];
}
?>
</table>
<? if($no==0) : ?>
	<div class="no_result_s"><span>결제 내역이 없습니다.</span></div>
<? endif; ?>
</div>
<!-- //결제 내역 -->
<?
#경매결제
$stmt=$pdo->prepare("SELECT * FROM {$my_db}.tc_price WHERE use_key=1 ORDER BY sort_num");
$stmt->execute();
while($rs=$stmt->fetch()){$pi[$rs['state']]=array("area" => $rs['area'], "srv_area" => $rs['service_area']);}	 
    
#강의결제
$stmt=$pdo->prepare("SELECT * FROM {$my_db}.te_lecture WHERE 1");
$stmt->execute();
while($rs=$stmt->fetch()){$pi[$rs['lec_code']]=array("area" => $rs['course'], "srv_area" => $rs['teacher']);}
    
$stmt=$pdo->prepare("SELECT COUNT(*) FROM {$my_db}.tm_pay_wait WHERE id='{$id}' AND wtime > DATE_SUB(CURRENT_DATE(),INTERVAL 10 DAY) GROUP BY apm");
$stmt->execute();        
$tot=$stmt->fetchColumn();

if($tot>0)
{
  echo "
    <br><hr><br>
    <div class='bold left'> 최근 신청/접수 내역 (무통장)</div>
    <table class='tbl_grid'>
    	<tr>
    		<th>신청지역</th>
    		<th>이용료</th>
    		<th>결제(예정)금액</th>
    		<th>입금정보</th>
    		<th>접수시간</th>
    	</tr>";
  	
  $stmt=$pdo->prepare("SELECT * FROM {$my_db}.tm_pay_wait WHERE id='{$id}' AND wtime > DATE_SUB(CURRENT_DATE(),INTERVAL 10 DAY) GROUP BY apm");
  $stmt->execute();
  while($rs=$stmt->fetch())
  {
    /*
  	$bank_info=$bank_arr[$rs['bankcode']]['name']."-{$rs['payname']}";
  	$apmArr=[];
  	$apm="";
  	if($rs['pay_code']==100)
  	{
  		$tmpArr=explode(",",$rs['apm']);
  		if($tmpArr)
  		{
  			foreach($tmpArr as $eaPay)
  			{
  				list($s,$m,$p)=explode(":",$eaPay);
  				$sector=$state_arr[$s];
  				$apmArr[]="{$sector}-{$m}개월";
  			}
  			$apm=implode("<br>",$apmArr);
  		}
  	}
  	*/
  	$bank_info="{$arr_payBank[$rs['bankcode']]}-{$rs['payname']}";
  	$smp_arr=explode(",",$rs['apm']);
    $arr=array();
    foreach($smp_arr as $v)
    {
       list($state,$month,$price)=explode(":",$v);
       $month=($rs['pay_code']==100)? "{$month}개월" : "{$month}일";
       array_push($arr,$pi[$state]['area']." > ". $month." ");
    }
    $apm_str=implode(",",$arr);
  	
  	echo "
  	<tr>
  		<td class='center'>{$apm_str}</td>		
  		<td class='money'>".number_format($rs['srv_price'])."</td>
  		<td class='money orange'>".number_format($rs['pay_price'])."</td>
  		<td>{$bank_info}</td>
  		<td class='center'>".substr($rs['wtime'],5,11)."</td>
  	</tr>";
  }
}
?>
</table>
<!-- //가상계좌 신청 내역 -->
<?
$stmt=$pdo->prepare("SELECT COUNT(*) FROM {$my_db}.tm_pay_log WHERE id='{$id}' AND pay_opt=4 AND status='WAITING_FOR_DEPOSIT' AND dueDate>=NOW()");
$stmt->execute();        
$tot=$stmt->fetchColumn();

if($tot>0)
{
  echo "
  <br><hr><br>
  <div class='bold left'> 가상계좌신청 내역</div>
  <table class='tbl_grid'>
  	<tr>
  		<th>결제지역</th>
  		<th>결제예정 금액(원)</th>
  		<th>은행</th>
  		<th>계좌번호</th>
  		<th>입금자명</th>
  		<th>입금기간</th>
  	</tr>";

  $VSQL="SELECT * FROM {$my_db}.tm_pay_log WHERE id='{$id}' AND status='WAITING_FOR_DEPOSIT' AND dueDate>=NOW()";	
  $stmt=$pdo->prepare($VSQL);
  $stmt->execute();
  while($rs=$stmt->fetch())
  {
    $goods=$rs['goods'];
    $accountBank=$rs['accountBank'];
    $accountNumber=$rs['accountNumber'];
    $customerName=$rs['customerName'];
    $dueDate=date("Y.m.d H:i:s", strtotime($rs['dueDate'])); 
  	echo "
  	<tr>
  		<td>{$goods}</td>		
  		<td class='money orange'>".number_format($rs['pay_price'])."</td>
  		<td>{$accountBank}</td>
  		<td class='center'>{$accountNumber}</td>
  		<td class='center'>{$customerName}</td>
  		<td class='center'>{$dueDate}</td>
  	</tr>";
  }
}
?>
</table>
<?
$price_json=urldecode(json_encode($price_arr));
$price_lec_json=urldecode(json_encode($price_lec_arr));
include($_SERVER["DOCUMENT_ROOT"]."/SuperAdmin/common/footer.php");
?>

<!-- <script src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script> -->
<script src="https://spi.maps.daum.net/imap/map_js_init/postcode.v2.js"></script>


<script type="text/javascript">
var pj=<?=$price_json?>;
var pj_lec=<?=$price_lec_json?>;

$(document).ready(function(){
	$("#tbl_price_auct input:checkbox").click(function(){
		calc(this);
	});
	$("#tbl_price_lect input:checkbox").click(function(){
		calc_lect();
	});
    $("#tbl_price_off input:radio").click(function(){calc_off(this);});

	$("#his_wdate,#paydate,#dc_sdate,#dc_edate").datepicker({
		changeMonth:true,
		changeYear:true,
		showButtonPanel:true
	});
	//$.history.init(historyLoad);	//ajax history	
	$("#pay_date").mask("9999-99-99");
	//$("#his_wdate").mask("9999-99-99");
	$("#btn_save_info").click(function(){
		$("#fm_user_info").submit();
	});
	$("#btn_add_history").click(function(){
		add_history();
	});
	$("tr[name=row]").mouseover(function(){
		$(this).css({"background":"#fff6d7"});
	}).mouseout(function(){
		$(this).css({"background":""});
	});
	load_member_history();
});

function payform_ctrl(pay_code)
{
	if(pay_code==100)
	{
		$("#tbl_price_lect").hide();
        $("#tbl_price_off").hide();
		$("#tbl_price_auct").show();
	}
	else if(pay_code==101)
	{		
		$("#tbl_price_auct").hide();
        $("#tbl_price_off").hide();
		$("#tbl_price_lect").show();
	}
    else if(pay_code==102)
    {    
        $("#tbl_price_auct").hide();
        $("#tbl_price_lect").hide();
        $("#tbl_price_off").show();

    }
	else if(pay_code==200)
	{		
		$("#tbl_price_lect").hide();
        $("#tbl_price_off").hide();
		$("#tbl_price_auct").show();
	}
	else
	{
		$("#tbl_price_auct").hide();
        $("#tbl_price_off").hide();
		$("#tbl_price_lect").hide();
	}
	
	$("#area_info").text("선택전");
	$("#amt_info").text("0 원");
	
	$("#smp").val("");
	$("#amt").val(0);
}

function calc(obj)
{
	var state=0, month=0, grp=0, st_arr="", sm_arr="", area_arr=[], smp_arr=[];
	var area_info="", amt=0;
	
	sm_arr=obj.id.split("_");
	state=sm_arr[1];
	month=sm_arr[2];
		
	if(obj.checked)
	{
		$("#row_"+state+" input:checkbox").each(function(){
			if(this.id==obj.id) return true;
			this.checked=false;
		});
		
		//전국 선택시
		if(state==99)
		{
			$("#tbl_price_auct input:checkbox:checked").each(function(){
				if(this.value==99) return true;
				this.checked=false;
			});
		}
		else
		{
			$("#row_99 input:checkbox:checked").attr("checked",false);
		}
		
		//권역선택시 제주 해제
		if(pj[state]['grp'] == 1 && state != 83)
		{
			$("#row_91 input:checkbox:checked").attr("checked",false);
		}
		
		//상위 선택시 하위 해제
		if(pj[state]['sub'] != "")
		{
			st_arr=(pj[state]['sub']).split(",");
			for(var i in st_arr)
			{					
				$("#row_"+st_arr[i]+" input:checkbox:checked").attr("checked",false);
			}
		}
		
		//하위 선택시 상위 해제
		if(pj[state]['par'] != "")
		{
			st_arr=(pj[state]['par']).split(",");
			for(var i in st_arr)
			{					
				$("#row_"+st_arr[i]+" input:checkbox:checked").attr("checked",false);
			}
		}
	}
		
	//$("#tbl_price input:checkbox:checked").each(function(){
	$("#tbl_price_auct input:checkbox").each(function(){
		if(this.checked)
		{
			sm_arr=this.id.split("_");
			state=sm_arr[1];
			month=sm_arr[2];
			smp_arr.push(state+":"+month+":"+pj[state][month]);
			area_arr.push(pj[state]['area']+"> "+month+"개월");
			amt+=parseInt(pj[state][month]);
			$(this).parent().addClass('orange bold');
		}
		else
		{
			$(this).parent().removeClass('orange bold');
		}
	});
	
	if(area_arr.length > 0)
	{
		$("#area_info").text(area_arr.join(", "));
		$("#amt_info").text($.formatNumber(amt)+" 원");
		
		$("#smp").val(smp_arr.join(","));
		$("#amt").val(amt);
	}
	else
	{
		$("#area_info").text("선택전");
		$("#amt_info").text("0 원");
		
		$("#smp").val("");
		$("#amt").val(0);
	}
}

function calc_lect()
{
	var state=0, month=0, grp=0, st_arr="", sm_arr="", area_arr=[], smp_arr=[];
	var area_info="", amt=0;
	
	$("#tbl_price_lect input:checkbox").each(function(){
		if(this.checked)
		{
			sm_arr=this.id.split("_");
			state=sm_arr[1];
			month=sm_arr[2];
			smp_arr.push(state+":"+month+":"+pj_lec[state]['price']);
			area_arr.push(pj_lec[state]['course']);
			amt+=parseInt(pj_lec[state]['price']);
			//$(this).parent().addClass('orange bold');
		}
		else
		{
			//$(this).parent().removeClass('orange bold');
		}
	});
	
	if(area_arr.length > 0)
	{
		$("#area_info").text(area_arr.join(", "));
		$("#amt_info").text($.formatNumber(amt)+" 원");
		
		$("#smp").val(smp_arr.join(","));
		$("#amt").val(amt);
	}
	else
	{
		$("#area_info").text("선택전");
		$("#amt_info").text("0 원");
		
		$("#smp").val("");
		$("#amt").val(0);
	}
}
//온/오프라인
function calc_off(obj)
{

    var state=0, on_off=0, grp=0, st_arr="", sm_arr="", area_arr=[], smp_arr=[];
    var area_info="", amt = 0;
    
    sm_arr=obj.id.split("_");
	state=sm_arr[1];
	month=sm_arr[2];
		
	if(obj.checked)
	{
		$("#row_"+state+" input:radio").each(function(){
			if(this.id==obj.id) return true;
			this.checked=false;
		});
    }
    $("#tbl_price_off input:radio").each(function(){
        if(this.checked)
        {
            sm_arr=this.id.split("_");
            state=sm_arr[1];
            on_off=sm_arr[2];
            
            let price = this.value;
            let title = this.dataset.code;
            smp_arr.push(state+":"+on_off+":"+price);
            if(on_off == 0){area_arr.push(title+"(오프라인)");}
            else{area_arr.push(title+"(온라인)");}
            amt+=parseInt(price);
        }
    });

    if(area_arr.length > 0)
    {
        $("#area_info").text(area_arr.join(", "));
        $("#amt_info").text($.formatNumber(amt)+" 원");
        
        $("#smp").val(smp_arr.join(","));
        $("#amt").val(amt);
    }
    else
    {
        $("#area_info").text("선택전");
        $("#amt_info").text("0 원");
        
        $("#smp").val("");
        $("#amt").val(0);
    }
    
}
//히스토리 등록
function add_history()
{
	if($.trim($("#history_memo").val())=="")
	{
		alert("내용을 입력 해 주세요");
		$("#history_memo").focus();
		return;
	}
	$("#history_mode").val("add_history");
	load_member_history();
}

//히스토리 삭제
function del_history(idx)
{
	$("#tr_"+idx).css({"background":"#ddd"});
	var is_del=confirm("선택하신 내용을 삭제 하시겠습니까?");
	if(is_del==true)
	{
		$("#history_mode").val("del_history");
		$("#idx_arr").val(idx);
		load_member_history();
	}
	else
	{
		$("#tr_"+idx).css({"background":"#fff"});
	}
}

//히스토리 내용보기
function view_history(memo_enc)
{
	var memo=decodeURIComponent(memo_enc);
	$("#history_memo").val(memo);
}

//히스토리 목록
function load_member_history()
{
	var arr=[], html="", total=0, start=0;
	$.ajax({
		type: "post",
		url: "/SuperAdmin/xml/member_history.php",
		data: $("#fm_history").serialize(),
		dataType: "xml",
		beforeSend: function(){
			//
		},
		success: function(xml){
			pageClear();
			$(xml).find("item").each(function(){
	    		var $entry=$(this);
	    		var line_no=$entry.find("line_no").text();
	    		var idx=$entry.find("idx").text();
	    		var memo=$entry.find("memo").text();
				var wdate=$entry.find("wdate").text();
				var rdate=$entry.find("rdate").text();
				var staff=$entry.find("staff").text();
				var admin=$entry.find("admin").text();
				arr.push("<tr id='tr_"+idx+"'>");
				arr.push("<td class='center'>"+line_no+"</td>");
				arr.push("<td class='center'>"+wdate+"</td>");
				rdate=(rdate=="0000-00-00") ? "" : " <span style='background:#ff6000;color:#fff'>예약일</span> <span class='bold no blue'>"+rdate+"</span>";
				memo+=rdate;
				var memo_enc=escape(encodeURIComponent(memo));
				arr.push("<td><div class='ellipsis' style='width:350px' title='"+memo+"'><a href=\"javascript:view_history('"+memo_enc+"')\">"+memo+"</a></div></td>");
				arr.push("<td class='center'><a href=\"javascript:del_history('"+idx+"')\" class='red'>X</a></td>");
				arr.push("<td class='center'>"+staff+"</td>");
				arr.push("</tr>");
	    	});
	    	html=arr.join("");
	    	$("#list_body").html(html);
	    	start=$(xml).find("start").text();
	    	total=$(xml).find("total_record").text();
	    	if(total==0)
	    	{
	    		$("#page_navi").html("<div class='no_result_s'><span>관리정보가 없습니다.</span></div>");	
	    	}
	    	else
	    	{
	    		pageNavi(total,start,"load_member_history",0);	
	    	}	    	
	    	
	    	$("#history_memo").val("");
	    	$("#idx_arr").val("");
	    	$("#history_mode").val("");
			$("#ref_start").val(start);
			
			$("#chk_reserve").attr("checked",false);
			$("#rdate").val("");
		},
		error: function(xml,status,err){
			$("#list_body").html("<td colspan='4'><div class='center' style='padding:50px'>서버와의 통신이 실패했습니다. <a href=\"javascript:load_member_history()\" class='blue'>[새로 고침]</a></div></td>");
		},
		complete: function(){
			$("#loading").hide();
		}
	});
}

//결제폼 체크
function pay()
{
	var rd1=$("#rdo_free1").is(":checked");
	var rd2=$("#rdo_free2").is(":checked");
	var pk9=$("#paykind9").is(":checked");
	//if($("#rdo_free1").is(":checked")==false && $("#rdo_free2").is(":checked")==false)
	if((rd1==false && rd2==false) || (pk9==false && (rd1==true || rd2==true)))
	{
		if($("#amt").val()==0)
		{
			alert("선택한 지역이 없습니다.");
			return;
		}	
	}	
	$("#fmAGS_pay").submit();
}

//결제 수정
function pay_edit(tbl,idx,order_no,user_id)
{
	var lnk="pay_edit.php?tbl="+tbl+"&idx="+idx+"&order_no="+order_no+"&user_id="+user_id;
	window.open(lnk,"pay_edit","top=0,left=0,width=720,height=600,scrollbars=yes,resizable=yes").focus();
}

//휴면 관심물건 복원
function sleep_recover(uid)
{
	if(confirm("관심물건을 복원 하시겠습니까?"))
	{
		window.open("sleep_recover.php?uid="+uid,"sleep_recover","width=500,height=200,left=10,top=10,resizable=yes");
	}
}

//결제내역 구테이블-현테이블 전환
function pay_recover(idx)
{
	if(confirm("결제내역을 복원 하시겠습니까?")==true)
	{
		location.href="pay_edit_db.php?mode=pay_recover&idx="+idx;
	}
}

//관심 물건목록
function open_inter_list(id,name)
{
	window.open("/member/inter_list.php?user_id="+id, "win_inter_list", "top=0,left=0,width=1300,height="+(screen.availHeight-50)+",scrollbars=yes,resizable=yes");
}

//최근 접속내역
function open_conn_history(id, name)
{
	window.open("conn_history.php?user_id="+id+"&user_name="+name, "conn","top=0,left=0,width=1000,height="+(screen.availHeight-50)+",scrollbars=yes,resizable=yes").focus();
}

//최근 물건 열람내역
function open_pdview_history(uid,uname)
{
	window.open("/member/rsntList.php?user_id="+uid,"point","top=0,left=0,width=1300,height="+(screen.availHeight-50)+",scrollbars=yes,resizable=yes").focus();
}

//최근 문자 발송내역
function open_sms_history(uid,uname)
{
	window.open("sms_history.php?id="+uid+"&name="+uname,"point","top=0,left=0,width=900,height="+(screen.availHeight-50)+",scrollbars=yes,resizable=yes").focus();
}

//문자 보내기
function send_sms(uid)
{
	window.open("/SuperAdmin/member/sms_write.php?id="+uid,"send_sms","width=800,height=500,scrollbars=yes");
}

//협력업체 선택
function open_partner()
{
	window.open("partner_sel.php?partner="+$("#partner").val(),"partner","top=0,left=0,width=800,height="+(screen.availHeight-50)+",scrollbars=yes,resizable=yes").focus();
}

//무료 N개월 체크시 결제방법 무료 Select
function fnFree(obj)
{
	var fm=document.fmAGS_pay;
	var objId=obj.id;
	
	if(objId=="rdo_free1" || objId=="rdo_free2")
	{
		fm.paykind[3].checked=true;
		fm.payname.value="";
		fm.memo.value=fm.free_memo.value;
		if(objId=="rdo_free1") fm.smp.value="99:1:0";
		else fm.smp.value="99:2:0";
	}
	else
	{
		fm.paykind[0].checked=true;
		fm.payname.value=fm.user_name.value;
		fm.memo.value="입금미확인";
		fm.smp.value="";
	}
}
</script>