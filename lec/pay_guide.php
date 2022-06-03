<?
$page_code="40".$_GET['dv'];
$cpn_deny=true;
$today=date('Y-m-d');
$to_day=date('Ymd');
include($_SERVER["DOCUMENT_ROOT"]."/lec/inc/header.php");
## 요금테이블(동영상강좌)
$html10=array();
$html10[]="<table class=\"tbl_grid inputWrap f14\" id=\"tbl_price_lect\">";
$html10[]="<tr height='40'>";
$html10[]=" <th>강좌명</th>";
$html10[]=" <th>강사</th>";
$html10[]=" <th>강좌수</th>";	
$html10[]=" <th>수강일수</th>";
$html10[]=" <th>금액</th>";
$html10[]="</tr>";

//$stmt=$pdo->prepare("SELECT * FROM {$my_db}.te_lecture WHERE hide=0 AND cg_kind_mv =0 AND price > 0 AND ctgr BETWEEN 20 AND 22");
$stmt=$pdo->prepare("SELECT * FROM {$my_db}.te_lecture WHERE hide=0 AND price > 0 and pay_on=1");
$stmt->execute();
while($rs=$stmt->fetch())
{
	$price_lec_arr[$rs[lec_code]]=array("course"=>urlencode($rs[course]), "days"=>$rs[days], "price"=>$rs[price]);
	$html10[]="<tr height='40'>";
	$html10[]=" <td>{$rs[course]}</td>";
	$html10[]=" <td class='center'>{$rs[teacher]}</td>";
	$html10[]=" <td class='center'>{$rs[lec_cnt]}</td>";
	$html10[]=" <td class='center'>{$rs[days]}</td>";
	$html10[]=" <td class='right'>".number_format($rs[price])."</td>";
	$html10[]="</tr>";
}
$html10[]="</table>";
$html10=implode("",$html10);


## 요금테이블(경매교육)
$html30=array();
$html30[]="<table class=\"tbl_grid inputWrap f14\" id=\"tbl_price_off\">";
$html30[]="	<tr  height='40'>";
$html30[]="		<th>강좌명</th>";
$html30[]="		<th>강사</th>";
$html30[]= "    <th>시작일자</th>";
$html30[]= "    <th>종료일자</th>";
$html30[]="		<th>금액</th>";
$html30[]="     <th>(오프라인)모집정원</th>";
$html30[]="     <th>강의보기</th>";
$html30[]="	</tr>";

$stmt=$pdo->prepare("SELECT * FROM {$my_db}.tl_edu");
$stmt->execute();
$html=array();
while($rs=$stmt->fetch())
{
    $html30[]="<tr height='40' id='row_{$rs[edu_code]}'>";
    $html30[]="<td>{$rs['edu_title']} {$on_off_ment}</td>";
    $html30[]="<td class='center'>{$rs['edu_teacher']}</td>";
    $html30[]="<td class='center'>".substr($rs['sdate'],5,5)."</td>";
    $html30[]="<td class='center'>".substr($rs['edate'],5,5)."</td>";
    $html30[]="<td class='right'>".number_format($rs['edu_pay'])."</td>";
    $html30[]="<td class='center'>{$rs['pay_people']}/{$rs['edu_people']}</td>";
    $html30[]="<td class='center'><a href='#'><span class='btn_box_ssss btn_white radius_5' style='line-height:16px'>강의보기</span></a></td>";
    // if($rs['on_off']==0){
    //     $html30[]="<td class='center'><input type='checkbox' id='chk_{$rs[edu_code]}_{$rs['on_off']}' name=\"{$rs['edu_title']}\" value='{$rs['edu_pay']}'  disabled></td>";
    //     if($rs['pay_people']==$rs['edu_people']){$html30[]="<td class='center'><input type='checkbox' id='chk_{$rs[edu_code]}_{$rs['on_off']}' name=\"{$rs['edu_title']}\" value='{$rs['edu_pay']}' disabled></td>";}
    //     else{$html30[]="<td class='center'><input type='checkbox' id='chk_{$rs[edu_code]}_{$rs['on_off']}' name=\"{$rs['edu_title']}\" value='{$rs['edu_pay']}' ></td>";}
    // }elseif($rs['on_off']==1){
	// 	$html30[]="<td class='center'><input type='checkbox' id='chk_{$rs[edu_code]}_{$rs['on_off']}' name=\"{$rs['edu_title']}\" value='{$rs['edu_pay']}' ></td>";
    //     $html30[]="<td class='center'><input type='checkbox' id='chk_{$rs[edu_code]}_{$rs['on_off']}' name=\"{$rs['edu_title']}\" value='{$rs['edu_pay']}'  disabled></td>";
	// }else{
	// 	$html30[]="<td class='center'><input type='checkbox' id='chk_{$rs[edu_code]}_1' name=\"{$rs['edu_title']}\" value='{$rs['edu_pay']}' ></td>";
    //     if($rs['pay_people']==$rs['edu_people']){$html30[]="<td class='center'><input type='checkbox' id='chk_{$rs[edu_code]}_{$rs['on_off']}' name=\"{$rs['edu_title']}\" value='{$rs['edu_pay']}'  disabled></td>";}
    //     else{$html30[]="<td class='center'><input type='checkbox' id='chk_{$rs[edu_code]}_0' name=\"{$rs['edu_title']}\" value='{$rs['edu_pay']}'></td>";}
	// }
    
    $html30[]="</tr>";
}
$html30[]="</table>";
$html30=implode("",$html30);
?>
<div class='wrap'>
<?
if($_GET['dv']==10){echo $html10;}
elseif($_GET['dv']==30){echo $html30;}
?>
</div>
<br>
<br>
<? if($client_id) :?>
<div class="center"><a href="/member/Toss_pay_lec.php"><span class='btn_box_ss btn_tank radius_10' style='width:110px'>결제하기</span></a></div>
<? else :?>
<div class="center"><a href="/inc/login_box.php"><span class='btn_box_ss btn_tank radius_10' style='width:110px'>로그인 후 결제</span></a></div>
<? endif ;?>
<?
//계좌 안내
$price_json=urldecode(json_encode($price_arr));
$price_lec_json=urldecode(json_encode($price_lec_arr));
include($_SERVER["DOCUMENT_ROOT"]."/lec/inc/footer.php");
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
		$("#bank_info").show();
		$("#bank_info_mv").hide();
	}
	else if(pay_code==101)
	{		
		$("#tbl_price_auct").hide();
		$("#tbl_price_lect").show();
		$("#bank_info").hide();
		$("#bank_info_mv").show();
	}
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
