<?
$page_code=9030;
$new_page_code=9030;
$member_only=true;
$cpn_deny=true;
$today=date('Y-m-d');
$to_day=date('Ymd');
include($_SERVER["DOCUMENT_ROOT"]."/lec/inc/header.php");

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
<div class='wrap'>
<div class="lh20">
	- 서비스 이용 후 환불이 안되오니충분히 검토 후 결제</span> 하세요.<br>
	- 동시접속은 사이트 이용장애나 보안상의 문제로 제한됩니다.<br>
	- 세금계산서 발급을 원하시는 경우, 대표자명 또는 회사명으로 입금.<br>
	- 고객문의 및 개통 가능시간 안내 : 월 ~ 금 / 오전09:00 ~ 오후06:00<br>
</div>
<br><br><br>
<div class="bold f14">
	동영상강좌 이용요금
</div>
<? if($client_id=="hans2") : ?>
	<form id="fm_pay" name="fm_pay" action="XPay_test.php" method="post">
<? else : ?>
	<form id="fm_pay" name="fm_pay" action="XPay_order_lec.php" method="post">
<? endif; ?>
<table class="tbl_grid chack_line inputWrap">
	<tr  height='40'>
		<th width="80px" class="center">결제종류</th>
		<td>
		<label for="pay_code_101" class="fleft hand" style='width:120px'><input type="radio" id="pay_code_101" name="pay_code" value="101" checked class='rdo' onclick="payform_ctrl(101)"> <span class='rdo_ment'>동영상강좌</span></label> &nbsp;
        <label for="pay_code_102" class="fleft hand" style='width:120px'><input type="radio" id="pay_code_102" name="pay_code" value="102" class='rdo' onclick="payform_ctrl(102)"> <span class='rdo_ment'>경매교육</span></label> &nbsp;
    </td>
	</tr>
</table>
<div class="clear" style="height:10px"></div>
<!-- 요금테이블(경매) -->

<!-- 요금테이블(동영상강좌) -->
<table class="tbl_grid" id="tbl_price_lect">
	<tr  height='40'>
		<th width="">선택</th>
		<th>강좌명</th>
		<th>강좌수</th>		
		<th>수강일수</th>
		<th>금액</th>
	</tr>
<?
//$result=sql_query("SELECT * FROM {$my_db}.te_lecture WHERE hide=0 AND (lec_code!=103 AND lec_code!=104 AND lec_code!=117) AND price > 0 AND ctgr BETWEEN 20 AND 22");

//cg_sector : 103~110 까지는 동영상 기사단 
//$result=sql_query("SELECT * FROM {$my_db}.te_lecture WHERE hide=0 AND price > 0");
//while($rs=mysql_fetch_array($result))
$stmt=$pdo->prepare("SELECT * FROM {$my_db}.te_lecture WHERE hide=0 AND price > 0 and pay_on=1");
$stmt->execute();
while($rs=$stmt->fetch())
{
	$price_lec_arr[$rs[lec_code]]=array("course"=>urlencode($rs[course]), "days"=>$rs[days], "price"=>$rs[price]);
	echo "
	<tr  height='40'>
		<td class='center'><input type='checkbox' id='chk_{$rs[lec_code]}_{$rs[days]}' name='' value='{$rs[lec_code]}'></td>
		<td>[ {$rs[teacher]} ]<br>{$rs[course]}</td>
		<td class='center'>{$rs[lec_cnt]}</td>
		<td class='center'>{$rs[days]}</td>
		<td class='right'>".number_format($rs[price])."</td>
	</tr>";
}
?>
</table>
<!-- 요금테이블(오프라인강좌) -->

<table class="tbl_grid" id="tbl_price_off" style="display:none">
	<tr  height='40'>
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
$stmt=$pdo->prepare("SELECT * FROM {$my_db}.tl_edu");
$stmt->execute();
$html=array();
while($rs=$stmt->fetch())
{
    $html[]="<tr height='40' id='row_{$rs[edu_code]}'>";
    $html[]="<td>{$rs['edu_title']} {$on_off_ment}</td>";
    $html[]="<td class='center'>{$rs['edu_teacher']}</td>";
    $html[]="<td class='center'>".substr($rs['sdate'],5,5)."</td>";
    $html[]="<td class='center'>".substr($rs['edate'],5,5)."</td>";
    $html[]="<td class='right'>".number_format($rs['edu_pay'])."</td>";
    $html[]="<td class='center'>{$rs['pay_people']}/{$rs['edu_people']}</td>";
    if($rs['on_off']==0){
        $html[]="<td class='center'><input type='radio' id='chk_{$rs[edu_code]}_{$rs['on_off']}' name=\"edu\" value='{$rs['edu_pay']}' data-code='{$rs['edu_title']}'  disabled></td>";
        if($rs['pay_people']==$rs['edu_people']){$html[]="<td class='center'><input type='radio' id='chk_{$rs[edu_code]}_{$rs['on_off']}' name=\"edu\" value='{$rs['edu_pay']}' data-code='{$rs['edu_title']}'disabled></td>";}
        else{$html[]="<td class='center'><input type='radio' id='chk_{$rs[edu_code]}_{$rs['on_off']}' name=\"edu\" value='{$rs['edu_pay']}' data-code='{$rs['edu_title']}'></td>";}
    }elseif($rs['on_off']==1){
		$html[]="<td class='center'><input type='radio' id='chk_{$rs[edu_code]}_{$rs['on_off']}' name=\"edu\" value='{$rs['edu_pay']}' data-code='{$rs['edu_title']}'></td>";
        $html[]="<td class='center'><input type='radio' id='chk_{$rs[edu_code]}_{$rs['on_off']}' name=\"edu\" value='{$rs['edu_pay']}'  data-code='{$rs['edu_title']}' disabled></td>";
	}else{
		$html[]="<td class='center'><input type='radio' id='chk_{$rs[edu_code]}_1' name=\"edu\" value='{$rs['edu_pay']}' data-code='{$rs['edu_title']}'></td>";
        if($rs['pay_people']==$rs['edu_people']){$html[]="<td class='center'><input type='radio' id='chk_{$rs[edu_code]}_{$rs['on_off']}' name=\"edu\" value='{$rs['edu_pay']}' data-code='{$rs['edu_title']}'  disabled></td>";}
        else{$html[]="<td class='center'><input type='radio' id='chk_{$rs[edu_code]}_0' name=\"edu\" value='{$rs['edu_pay']}' data-code='{$rs['edu_title']}'></td>";}
	}
    
    $html[]="</tr>";
}
$html[]="</table>";
$html=implode("",$html);
echo $html;
?>
<!-- //요금테이블(강좌) -->
<br>
<table class="tbl_grid inputWrap">
	<tr  height='40'>
		<th width="80px">선택강의</th>
		<td id="area_info" class="bold blue">선택전</td>
	</tr>
	<tr  height='40'>
		<th>이 용 료</th>
		<td id="amt_info" class="bold orange no">0 원</td>
	</tr>
	<tr  height='40'>
		<th>결제방법</th>
		<td>
		  <label for="pay_opt1" class="hand fleft" style="width:100px"><input type="radio" id="pay_opt1" name="pay_opt" value="1" class='rdo'> <span class="rdo_ment">카드결제</span></label>
			<label id='pay4' for="pay_opt4" class="hand fleft" style="width:100px"><input type="radio" id="pay_opt4" name="pay_opt" value="4" class='rdo'> <span class="rdo_ment">가상계좌</span></label>
			<!-- <label for="pay_opt2" class="hand fleft" style="width:100px"><input type="radio" id="pay_opt2" name="pay_opt" value="2" checked class='rdo'> <span class="rdo_ment">통장입금</span></label> &nbsp; -->
			<? if(!$mobile_agent) : ?>
			<!-- <label for="pay_opt3" class="hand fleft"><input type="radio" id="pay_opt3" name="pay_opt" value="3"  class='rdo'> <span class="rdo_ment">실시간 계좌이체</span></label> -->
			<label style='display:inline-block'>
				<span class="pay_opt_ment1 blue bold pay_opt_ment" >(카드결제 승인과 동시에 <span class='red'>자동으로 오픈</span>됩니다.)</span>
				<span class="pay_opt_ment4 blue bold pay_opt_ment" style='display:none'>(지정된 계좌 입금 후 <span class='red'>자동으로 오픈</span>됩니다.)</span>
				<!--<span class="pay_opt_ment3 blue bold pay_opt_ment" style='dispaly:none'>(이체 후 자동으로 오픈되고 공인인증서가 필요 : <span class='red'>법인계좌 불가</span>)</span>-->
			</label>
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
<div class="center"><a href="javascript:pay()"><span class="btn_box_ss btn_tank radius_10" style="width:130px">신청 및 결제하기</span></a></div>
	<input type="hidden" id="amt" name="amt" value="0">
	<input type="hidden" id="smp" name="smp" value="">
	<input type="hidden" id="order_no" name="order_no" value="<?=$order_no?>">
	<input type="hidden" id="epoint" name="epoint" value="<?=$eff_point?>">
	<input type="hidden" id="dc_rate" name="dc_rate" value="<?=$dc_rate?>">
</form>
</div>
<!--
<div id="bank_info" style="display:none">
<?
//계좌 안내
//bank_info();
?>
</div>
<div id="bank_info_mv" style="">
<?
//동영상 계좌 안내
//bank_info_mv();
?>
</div>
-->
<?
$price_json=urldecode(json_encode($price_arr));
$price_lec_json=urldecode(json_encode($price_lec_arr));
include($_SERVER["DOCUMENT_ROOT"]."/lec/inc/footer.php");
?>

<script type="text/javascript">
var pj=<?=$price_json?>;
var pj_lec=<?=$price_lec_json?>;

$(document).ready(function()
{
  $(".pay_opt_ment4,.pay_opt_ment3").hide();	
	$("#tbl_price_auct input:checkbox").click(function(){calc(this);});
	$("#tbl_price_lect input:checkbox").click(function(){calc_lect();});
    $("#tbl_price_off input:radio").click(function(){calc_off(this);});

	$("input:radio[name=pay_opt]").click(function()
	{
	  (this.value==2) ? $("tr[name=row_bank]").show() : $("tr[name=row_bank]").hide();
	  $(".pay_opt_ment").hide();
		$(".pay_opt_ment"+this.value).show();
	});
	
	$("tr[name=row]").mouseover(function(){$(this).css({"background":"#fff6d7"});}).mouseout(function(){$(this).css({"background":""});});
  //결제방법 최초선택
  $("#pay_opt1").prop("checked", true);
  $("tr[name=row_bank]").hide();	
});

function payform_ctrl(pay_code)
{
	if(pay_code==100)
	{   
    $("#tbl_price_lect input:checkbox:checked").prop("checked",false);
		$("#tbl_price_lect").hide();
        $("#tbl_price_off").hide();
		$("#tbl_price_auct").show();

	}
	else if(pay_code==101)
	{	
    $("#tbl_price_auct input:checkbox:checked").prop("checked",false).parent().removeClass("orange bold");	
		$("#tbl_price_auct").hide();
        $("#tbl_price_off").hide();
		$("#tbl_price_lect").show();
        $("#pay4").show();

	}
    else if(pay_code==102)
    {
        $("#tbl_price_off input:checkbox:checked").prop("checked",false);
        $("#tbl_price_auct").hide();
		$("#tbl_price_lect").hide();
        $("#tbl_price_off").show();
        $("#pay4").hide();

    }
	
	$("#area_info").text("선택전");
	$("#amt_info").text("0 원");
	
	$("#smp").val("");
	$("#amt").val(0);
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
//IE version
var ieVersion=(function()
{
 var version=-1;
 if(navigator.appName == 'Microsoft Internet Explorer' && navigator.userAgent.toLowerCase().indexOf('msie') != -1 && new RegExp('MSIE ([0-9]{1,}[\./0-9]{0,})').exec(navigator.userAgent) != null){version = parseInt(RegExp.$1);}
 return version;
});

function pay()
{
	//결제창에서 뒤로가기시
	if($("#area_info").text()=="선택전" && $("#amt").val() > 0){location.reload();}
	if($("#amt").val()==0){alert("선택한 항목이 없습니다.");	return;}	
    
	//가상계좌분기
  if($("#pay_opt4").is(":checked") == true){$("#fm_pay").attr("action","Tosspay_order.php");}
	else
	{
	  if($("#pay_opt1").is(":checked") == true || $("#pay_opt3").is(":checked") == true)
	  {
	    //IE10이하는 구모듈로 분기
	    if(ieVersion !== -1 && ieVersion < 11){$("#fm_pay").attr("action","XPay_order.php");}
	    else{$("#fm_pay").attr("action","Tosspay_order.php");}
	  }
	}	
	$("#fm_pay").submit();
}
<? if($ref=="aply_lect") : ?>
	$("#pay_code_101").trigger("click");
	$("#tbl_price_lect input:checkbox").each(function()
	{
		if(this.value==<?=$lec_cd?>)
		{
			var chkLec=this.id;
			setTimeout(function(){$("#"+chkLec).trigger("click");},1000);
		}
	});
<? endif; ?>
</script>