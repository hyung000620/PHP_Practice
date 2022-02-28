<?
$page_code=901600;
$member_only=true;
$cpn_deny=true;
$today=date('Y-m-d');
$to_day=date('Ymd');
include($_SERVER["DOCUMENT_ROOT"]."/inc/header.php");

//주문번호
srand((double)microtime()*1000000);
$order_no=date("YmdHis").rand(1000,9999);

//이용료 할인
$allow_pay_custom=false;
//$result=sql_query("SELECT * FROM {$my_db}.tm_member WHERE id='{$client_id}' LIMIT 0,1");
//$rs=mysql_fetch_array($result);
$stmt=$pdo->prepare("SELECT * FROM {$my_db}.tm_member WHERE id='{$client_id}' LIMIT 0,1");
$stmt->execute();
$rs=$stmt->fetch();
if($rs[dc_rate])
{
	if($today >= $rs[dc_sdate] && $today <= $rs[dc_edate])
	{
		$dc_rate=$rs[dc_rate];
		$dc_sdate=$rs[dc_sdate];
		$dc_edate=$rs[dc_edate];
	}
}
if($rs[pay_custom]==1)
{
	$allow_card=true;
	$allow_pay_custom=true;
}
?>
<div class="lh28">
	- 서비스 이용 후 환불이 안되오니 경매정보 <span class="bold">샘플과 사이트를 충분히 검토 후 결제</span> 하시기 바랍니다.&nbsp;<a href="/auction/ca_sample.php"><span class="btn_box_ssss btn_white radius_5" style="line-height:16px">샘플보기</span></a><br>
	- 당사는 면책조건으로 정보를 제공 되오니, <span class="bold">참고용 자료로만 활용</span> 하시기 바랍니다.<br>
	- 동시접속은 사이트 이용장애나 보안상의 문제로 제한됩니다.<br>
	- 세금계산서 발급을 원하시는 경우, 대표자명 또는 회사명으로 입금해 주셔야 합니다.<br>
	- 고객문의 및 개통 가능시간 안내 : 월 ~ 금 / 오전09:00 ~ 오후06:00 ( 점심시간 오후 1시00분 ~ 오후 2시00분 )<br>
	- 한글 아이디로 회원가입 시, 동영상 플레이가 안되니 영문 아이디로 재가입해 주시기 바랍니다.<br>
	- 결제수단(카드, 입금) 변경은 결제 후 1개월 이내에 가능하고, 추가 결제는 동일지역 남은 기간이 100일 이내에만 가능합니다.<br>
	<div><a href="/board/bo_view.php?board_id=notice&ref_idx=1572"><img src='/img/event/event_20220201.png' alt="event공지" width="100%"></a></div>
	<? if($to_day < 20200301) :?>
		<span class='blue bold'>* 탱크옥션 3주년 + 홈페이지 리뉴얼 기념할인</span> <span class="bold">기간 01.20 ~ 02.29까지 (전국 12개월 50만원 &gt; 30만원)</span><br>
		<? endif ;?>
</div>
<br><br><br>
<div class="bold f14">
	＊ 경매정보/동영상강좌 이용요금
</div>
<? if($client_id=="hans2") : ?>
	<form id="fm_pay" name="fm_pay" action="XPay_test.php" method="post">
<? else : ?>
	<form id="fm_pay" name="fm_pay" action="XPay_order.php" method="post">
<? endif; ?>
<? if(($allow_card && $allow_pay_custom) || $client_level >= 5) : ?>
	<div class="right"><a href="pay_custom.php" class=" bold red">+ 지정금액 결제 &gt;</a></div>
<? endif; ?>
<table class="tbl_grid chack_line inputWrap">
	<tr  height='40'>
		<th width="130px" class="center">결제종류</th>
		<td>
		<? 
			foreach($pay_code_arr as $k => $v) : 
				if($k > 101)	break;
		?>
			<label for="pay_code_<?=$k?>" class="fleft hand" style='width:120px'><input type="radio" id="pay_code_<?=$k?>" name="pay_code" value="<?=$k?>"<? if($k==100) echo " checked"; ?> onclick="payform_ctrl(<?=$k?>)" class='rdo'> <span class='rdo_ment'><?=$v?></span></label> &nbsp;
		<? endforeach; ?>
		</td>
	</tr>
</table>
<div class="clear" style="height:10px"></div>
<!-- 요금테이블(경매) -->
<table class="tbl_grid inputWrap f14" id="tbl_price_auct">
	<tr height='50'>
		<th width="130px">지역/법원</th>
		<th>해당지역</th>
		<th width="90px">1개월</th>
		<th width="90px">3개월</th>
		<th width="90px">6개월</th>
		<th width="90px">12개월</th>
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
		<tr height='40' id='row_{$rs[state]}' name='row' class='{$bg_grp}'>
			<td class='{$tx_grp}'>{$rs[area]}</td>
			<td>".str_replace("<br>","",$rs[service_area])."</td>
			<td><label for='chk_{$rs[state]}_1' class='hand'><input type='checkbox' id='chk_{$rs[state]}_1' name='' value='{$rs[state]}' class='chk'><span class='chk_ment'>{$price_01}</span></label></td>
			<td><label for='chk_{$rs[state]}_3' class='hand'><input type='checkbox' id='chk_{$rs[state]}_3' name='' value='{$rs[state]}' class='chk'><span class='chk_ment'>{$price_03}</span></label></td>
			<td><label for='chk_{$rs[state]}_6' class='hand'><input type='checkbox' id='chk_{$rs[state]}_6' name='' value='{$rs[state]}' class='chk'><span class='chk_ment'>{$price_06}</span></label></td>
			<td><label for='chk_{$rs[state]}_12' class='hand'><span class='gray'><strike>{$bp_12}</strike></span><br><input type='checkbox' id='chk_{$rs[state]}_12' name='' value='{$rs[state]}' class='chk'><span class='chk_ment bold'>{$price_12}</span></label></td>
		</tr>";
	}
	else
	{
		echo "
		<tr height='40' id='row_{$rs[state]}' name='row' class='{$bg_grp}'>
			<td class='{$tx_grp}'>{$rs[area]}</td>
			<td>".str_replace("<br>","",$rs[service_area])."</td>
			<td><label for='chk_{$rs[state]}_1' class='hand'><input type='checkbox' id='chk_{$rs[state]}_1' name='' value='{$rs[state]}' class='chk'><span class='chk_ment'>{$price_01}</span></label></td>
			<td><label for='chk_{$rs[state]}_3' class='hand'><input type='checkbox' id='chk_{$rs[state]}_3' name='' value='{$rs[state]}' class='chk'><span class='chk_ment'>{$price_03}</span></label></td>
			<td><label for='chk_{$rs[state]}_6' class='hand'><input type='checkbox' id='chk_{$rs[state]}_6' name='' value='{$rs[state]}' class='chk'><span class='chk_ment'>{$price_06}</span></label></td>
			<td><label for='chk_{$rs[state]}_12' class='hand'><input type='checkbox' id='chk_{$rs[state]}_12' name='' value='{$rs[state]}' class='chk'><span class='chk_ment'>{$price_12}</span></label></td>
		</tr>";	
	}
}
?>
</table>
<!-- //요금테이블(경매) -->
<br>
<!-- 요금테이블(강좌) -->
<table class="tbl_grid" id="tbl_price_lect" style="display:none">
	<tr  height='40'>
		<th width="130px">선택</th>
		<th>강좌명</th>
		<th>강사</th>
		<th>강좌수</th>		
		<th>수강일수</th>
		<th>금액</th>
	</tr>
<?
//$result=sql_query("SELECT * FROM {$my_db}.te_lecture WHERE hide=0 AND (lec_code!=103 AND lec_code!=104 AND lec_code!=117) AND price > 0 AND ctgr BETWEEN 20 AND 22");

//cg_sector : 103~110 까지는 동영상 기사단 
//$result=sql_query("SELECT * FROM {$my_db}.te_lecture WHERE hide=0 AND cg_kind_mv =0 AND price > 0 AND ctgr BETWEEN 20 AND 22");
//while($rs=mysql_fetch_array($result))
//$stmt=$pdo->prepare("SELECT * FROM {$my_db}.te_lecture WHERE hide=0 AND cg_kind_mv =0 AND price > 0 AND ctgr BETWEEN 20 AND 22");
$stmt=$pdo->prepare("SELECT * FROM {$my_db}.te_lecture WHERE hide=0 AND price > 0 and pay_on=1");
$stmt->execute();
while($rs=$stmt->fetch())
{
	$price_lec_arr[$rs[lec_code]]=array("course"=>urlencode($rs[course]), "days"=>$rs[days], "price"=>$rs[price]);
	echo "
	<tr  height='40'>
		<td class='center'><input type='checkbox' id='chk_{$rs[lec_code]}_{$rs[days]}' name='' value='{$rs[lec_code]}'></td>
		<td>{$rs[course]}</td>
		<td class='center'>{$rs[teacher]}</td>
		<td class='center'>{$rs[lec_cnt]}</td>
		<td class='center'>{$rs[days]}</td>
		<td class='right'>".number_format($rs[price])."</td>
	</tr>";
}
?>
</table>
<!-- //요금테이블(강좌) -->
<br>
<table class="tbl_grid inputWrap">
	<tr  height='40'>
		<th width="130px">선택지역</th>
		<td id="area_info" class="bold blue">선택전</td>
	</tr>
	<tr  height='40'>
		<th>이 용 료</th>
		<td id="amt_info" class="bold orange no">0 원</td>
	</tr>
	<tr  height='40'>
		<th>결제방법</th>
		<td>
			<label for="pay_opt2" class="hand fleft" style="width:100px"><input type="radio" id="pay_opt2" name="pay_opt" value="2" checked class='rdo'> <span class="rdo_ment">가상계좌</span></label> &nbsp;
			<label for="pay_opt1" class="hand fleft" style="width:100px"><input type="radio" id="pay_opt1" name="pay_opt" value="1" class='rdo'> <span class="rdo_ment">카드결제</span></label> &nbsp;
			<? if(!$mobile_agent) : ?>
			<label for="pay_opt3" class="hand fleft"><input type="radio" id="pay_opt3" name="pay_opt" value="3"  class='rdo'> <span class="rdo_ment">실시간 계좌이체</span></label>(공인인증서 필수-법인계좌 사용불가)
			<? endif; ?>
			<!--<br>
			<span class="red">※ 현재 전자결제(카드결제/실시간 계좌이체)연동 테스트중 이므로, 통장입금 부탁드립니다.</span>-->
		</td>
	</tr>
	<tr   height='40' name="row_bank">
		<th>입금은행</th>
		<td>
		<? foreach($bank_arr as $k => $arr) : ?>
			<label for="bank<?=$k?>" class="hand fleft" style="width:100px"><input type="radio" id="bank<?=$k?>" name="bank_code" value="<?=$k?>"<? if($k==10) echo " checked"; ?> class="rdo"> <span class="rdo_ment"><?=$arr[name]?></span></label> &nbsp;
		<? endforeach; ?>
		</td>
	</tr>
	<tr  height='40' name="row_bank">
		<th>입금자명</th>
		<td><input type="text" id="" name="pay_name" value="<?=$client_name?>" class="tx150"> (입금자 확인으로 시간이 지연 되니, 전화 주시면 바로 사용 가능합니다.) </td>
	</tr>
</table>
<br>
<div class="center"><a href="javascript:pay2()"><span class="btn_box_ss btn_tank radius_10" style="width:130px">신청 및 결제하기(준형)</span></a></div>
<div class="center"><a href="javascript:pay1()"><span class="btn_box_ss btn_tank radius_10" style="width:130px">신청 및 결제하기(성헌)</span></a></div>

	<input type="hidden" id="amt" name="amt" value="0">
	<input type="hidden" id="smp" name="smp" value="">
	<input type="hidden" id="order_no" name="order_no" value="<?=$order_no?>">
	<input type="hidden" id="epoint" name="epoint" value="<?=$eff_point?>">
	<input type="hidden" id="dc_rate" name="dc_rate" value="<?=$dc_rate?>">
</form>
<div id="bank_info">
<?
//계좌 안내
bank_info();
?>
</div>
<div id="bank_info_mv" style="display:none">
<?
//동영상 계좌 안내
bank_info_mv();
?>
</div>

<?
$price_json=urldecode(json_encode($price_arr));
$price_lec_json=urldecode(json_encode($price_lec_arr));
include($_SERVER["DOCUMENT_ROOT"]."/inc/footer.php");
?>

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
	
	$("input:radio[name=pay_opt]").click(function(){
		(this.value==2) ? $("tr[name=row_bank]").show() : $("tr[name=row_bank]").hide();
	});
	
	$("tr[name=row]").mouseover(function(){
		$(this).css({"background":"#fff6d7"});
	}).mouseout(function(){
		$(this).css({"background":""});
	});
});

function payform_ctrl(pay_code)
{
	if(pay_code==100)
	{
		$("#tbl_price_lect").hide();
		$("#tbl_price_auct").show();
		//$("#bank_info").show();
		//$("#bank_info_mv").hide();	
	}
	else if(pay_code==101)
	{		
		$("#tbl_price_auct").hide();
		$("#tbl_price_lect").show();
		//$("#bank_info").hide();
		//$("#bank_info_mv").show();
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

function pay()
{
	//결제창에서 뒤로가기시
	if($("#area_info").text()=="선택전" && $("#amt").val() > 0)
	{
		location.reload();
	}
	
	if($("#amt").val()==0)
	{
		alert("선택한 항목이 없습니다.");
		return;
	}	
	$("#fm_pay").submit();
    
}

function pay1() //성헌
{
    $("#fm_pay").attr("action", "YPay_order.php");
	//결제창에서 뒤로가기시
	if($("#area_info").text()=="선택전" && $("#amt").val() > 0)
	{
		location.reload();
	}
	
	if($("#amt").val()==0)
	{
		alert("선택한 항목이 없습니다.");
		return;
	}	
	$("#fm_pay").submit();
}

function pay2() //준형
{
    $("#fm_pay").attr("action", "ZPay_order.php");
	//결제창에서 뒤로가기시
	if($("#area_info").text()=="선택전" && $("#amt").val() > 0)
	{
		location.reload();
	}
	
	if($("#amt").val()==0)
	{
		alert("선택한 항목이 없습니다.");
		return;
	}	
	$("#fm_pay").submit();
}


<? if($ref=="aply_lect") : ?>
	$("#pay_code_101").trigger("click");
	$("#tbl_price_lect input:checkbox").each(function(){
		if(this.value==<?=$lec_cd?>)
		{
			var chkLec=this.id;
			setTimeout(function(){
				$("#"+chkLec).trigger("click");
			},1000);
		}
	});
<? endif; ?>

</script>