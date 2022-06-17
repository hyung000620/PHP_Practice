<?
$page_code=9030;
$new_page_code=9030;
$member_only=true;
$cpn_deny=true;
$today=date('Y-m-d');
$dtm=date("Y-m-d H:i:s");
include($_SERVER["DOCUMENT_ROOT"]."/lec/inc/header.php");

//주문번호
srand((double)microtime()*1000000);
$order_no=date("YmdHis").rand(1000,9999);

//이용료 할인
$allow_pay_custom=false;
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
if($rs[edu_dc])
{
    $edu_dc=30;
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

if($on_off == '오프라인 강의신청') {
    $on_off = 0;
} else{
    $on_off = 1;
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
		<th>(오프라인)예비접수
			<span class="tooltip blue-tooltip" style="position:relative;top:0px;"><p class="btn_whitegray radius_30 center" style='width:17px;font-size:11px'>?</p><span style="position:absolute;left:100px;width:330px;">
				결원이 생길 시 차순으로 <br>기재 된 연락처로 연락드립니다. <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font class='red'> ★ </font>(회원정보수정에서 꼭 <font class='red'>번호 기재</font> 부탁드립니다!)
			</span></span>
		</th>
	</tr>
<?
$stmt=$pdo->prepare("SELECT * FROM {$my_db}.tl_edu WHERE rdate<='{$dtm}'");
$stmt->execute();
$html=array();
while($rs=$stmt->fetch())
{
    if($edu_dc && $rs['dc_temp']==1){
        $pay=round($rs[edu_pay]*(1-$edu_dc*0.01)/1000)*1000;
    }else{
        $pay=$rs[edu_pay];
    }
    $tr="<tr height='40' id='row_{$rs[edu_code]}'>";
    $tr.="<td>{$rs['edu_title']} {$on_off_ment}</td>";
    $tr.="<td class='center'>{$rs['edu_teacher']}</td>";
    $tr.="<td class='center'>".substr($rs['sdate'],5,5)."</td>";
    $tr.="<td class='center'>".substr($rs['edate'],5,5)."</td>";
    if($edu_dc && $rs['dc_temp']==1){
        $tr.="<td class='right'><span class='gray'><strike>".number_format($rs[edu_pay])."</strike></span><br>".number_format($pay)."</td>";
    }else{
        $tr.="<td class='right'>".number_format($pay)."</td>";
    }
    $tr.="<td class='center'>{$rs['pay_people']}/{$rs['edu_people']}</td>";

    $off_o="<td class='center'><input type='radio' id='chk_{$rs[edu_code]}_0' name=\"edu\" value='{$pay}' data-code='{$rs['edu_title']}'></td>"; //오프라인 오픈
    $off_x="<td class='center'><input type='radio' id='chk_{$rs[edu_code]}_0' name=\"edu\" value='{$pay}' data-code='{$rs['edu_title']}' disabled></td>"; //오프라인 오프
    $on_o="<td class='center'><input type='radio' id='chk_{$rs[edu_code]}_1' name=\"edu\" value='{$pay}' data-code='{$rs['edu_title']}'></td>"; //온라인 오픈
    $on_x="<td class='center'><input type='radio' id='chk_{$rs[edu_code]}_1' name=\"edu\" value='{$pay}' data-code='{$rs['edu_title']}' disabled></td>"; //온라인 오프
    $spare_o="<td class='center'><input type='radio' id='chk_{$rs[edu_code]}_2' name=\"edu\" value='{$pay}' data-code='{$rs['edu_title']}'></td>";
	$spare_x="<td class='center'><input type='radio' id='chk_{$rs[edu_code]}_2' name=\"edu\" value='{$pay}' data-code='{$rs['edu_title']}' disabled></td>";
    if($rs['state']==0 && $rs['sdate']<$today){$tr="";}
    $html[]=$tr;
    switch($rs['state'])
    {
        ##자동
        case 0 :
        {
            if($rs['sdate']>=$today)
            {
                if($rs['on_off']==0){
					$html[]=$on_x;
					$html[]=($rs['pay_people']>=$rs['edu_people'])?$off_x:$off_o;
					$html[]=($rs['pay_people']>=$rs['edu_people'] && $rs['can_people']<=$rs['spare_people'])?$spare_o:$spare_x;
				}
                elseif($rs['on_off']==1){
					$html[]=$on_o;
					$html[]=$off_x;
					$html[]=$spare_x;
				}
                else{
					$html[]=$on_o;
					$html[]=($rs['pay_people']>=$rs['edu_people'])?$off_x:$off_o;
					$html[]=($rs['pay_people']>=$rs['edu_people'] && $rs['can_people']<=$rs['spare_people'])?$spare_o:$spare_x;
				}
            }
        }break;
        ##전체 마감(수동)
        case 1 : 
        {
            $html[]=$on_x;
            $html[]=$off_x;
			$html[]=$spare_x;
        }break;
        ##오프라인 마감(수동)
        case 2:
        {
            if($rs['on_off']==0){$html[]=$on_x;$html[]=$off_x;}
            else{$html[]=$on_o;$html[]=$off_x;$html[]=$spare_x;}
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
		  <label id='pay1' for="pay_opt1" class="hand fleft" style="width:100px"><input type="radio" id="pay_opt1" name="pay_opt" value="1" class='rdo'> <span class="rdo_ment">카드결제</span></label>
			<label id='pay4' for="pay_opt4" class="hand fleft" style="width:100px"><input type="radio" id="pay_opt4" name="pay_opt" value="4" class='rdo'> <span class="rdo_ment">가상계좌</span></label>
			<label id='pay5'for="pay_opt5" class="hand fleft" style="width:100px; display:none;"><input type="radio" id="pay_opt5" name="pay_opt" value="5" class='rdo'> <span class="rdo_ment">예비접수</span></label>
			<? if(!$mobile_agent) : ?>
			<label style='display:inline-block'>
				<span class="pay_opt_ment1 blue bold pay_opt_ment" >(카드결제 승인과 동시에 <span class='red'>자동으로 오픈</span>됩니다.)</span>
				<span class="pay_opt_ment4 blue bold pay_opt_ment" style='display:none'>(지정된 계좌 입금 후 <span class='red'>자동으로 오픈</span>됩니다.)</span>
			</label>
			<? endif; ?>
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

  // view.php -> 경매교육신청 체크이벤트
  let pay_code = '<?=$pay_code?>';
  let edu_code = '<?=$edu_code?>';
  let on_off = '<?=$on_off?>';

  if(pay_code != '' && edu_code != '') {
    payform_ctrl(pay_code);
    $('#pay_code_'+pay_code).prop('checked',true);
    $('#chk_'+edu_code+"_"+on_off).prop('checked',true);
    calc_off($('#chk_'+edu_code+"_"+on_off)[0]);
  }
});

function payform_ctrl(pay_code)
{
	if(pay_code==101)
	{
        $("#tbl_price_lect input:checkbox:checked").prop("checked",false);	
        $("#tbl_price_off").hide();
		$("#tbl_price_lect").show();
		$('#pay1,#pay4').show();
		$("#pay5").hide();

	}
    else if(pay_code==102)
    {
        $("#tbl_price_off input:radio:checked").prop("checked",false);
		$("#tbl_price_lect").hide();
        $("#tbl_price_off").show();
		$('#pay1,#pay4').show();
		$("#pay5").hide();
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
            if(on_off == 0){
				area_arr.push(title+"(오프라인)");
				$('#pay_opt1').prop('checked',true);
				$('#pay1,#pay4').show();
				$('#pay5').hide();
			}
            else if(on_off == 1){
				area_arr.push(title+"(온라인)");
				$('#pay_opt1').prop('checked',true);
				$('#pay1,#pay4').show();
				$('#pay5').hide();
			}
			else{
				area_arr.push(title+"(오프라인)_예비접수");
				$('#pay1,#pay4').hide();
				$('#pay5').show();
				$('#pay_opt5').prop('checked',true);
			}
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
    
	if($("#pay_opt5").is(":checked")==true){
		$("#fm_pay").attr("action","Tosspay_lec_spare.php");
	}
	else{
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