<?
  $page_code="901600";
  $member_only=true;
  $cpn_deny = true;
  if($_POST['pay_type']=="edu")
  {
    include($_SERVER["DOCUMENT_ROOT"]."/lec/inc/header.php");
  }
  else
  {
    include($_SERVER["DOCUMENT_ROOT"]."/inc/header.php");
  }

  include($_SERVER["DOCUMENT_ROOT"]."/member/Toss.php");

  #param check
  $order_no=(int)$order_no;
  $pay_code=(int)$pay_code;
  $pay_opt=(int)$pay_opt;
  $amt=(int)$amt; 
  $epoint=(int)$epoint;
  $dc_rate=(int)$dc_rate;
  
  $bank_code=(int)$bank_code;
  $paramFlag=($order_no==0 || $pay_code==0 || $pay_opt==0 || $amt==0 || $bank_code==0)? 1 : 0;

  #cd to str
  $paycd_gb=($pay_code==100)? "해당지역"  : "강사명";
  $pay_pl=($pay_code==102)?"장소":"기간";
  //if($pay_opt>0){$paycd_str=$pay_kind_arr[$pay_opt];}
  if($pay_opt==1){$paycd_str="카드";}
  else if($pay_opt==3){$paycd_str="계좌이체";}
  else if($pay_opt==4){$paycd_str="가상계좌";}
  
  $stmt=$pdo->prepare("SELECT mobile FROM {$my_db}.tm_member WHERE id='{$client_id}' LIMIT 0,1");
  $stmt->execute();
  $rs=$stmt->fetch();
  $mobile=trim($rs[mobile]);
  $exmob=explode("-",$mobile);
  #파일로그기록
  if($flogFlag==0)
  {
    $tmp = array("USER_AGENT" => $_SERVER["HTTP_USER_AGENT"], "POST" => $_POST);
    $toss->fileLog("[Tosspay_order] {$order_no} - {$paycd_str}", $tmp);
  }
  
  #결제금액(amt) 1000원이상
  if($amt < 1000){alertBack("최소 결제금액은 1000원 이상입니다.");}
    
  #결제구분 > 신처항목,(해당지역 or 강사명)
  if($pay_code==100)
  {
    #경매결제
  	$stmt=$pdo->prepare("SELECT * FROM {$my_db}.tc_price WHERE use_key=1 ORDER BY sort_num");
  	$stmt->execute();
  	while($rs=$stmt->fetch()){$pi[$rs[state]]=array("area" => $rs[area], "srv_area" => $rs[service_area], "price"=>array("1"=>$rs['price_01'],"3"=>$rs['price_03'],"6"=>$rs['price_06'],"12"=>$rs['price_12']));}	
  }
  elseif($pay_code==101)
  {
    #강의결제
  	$stmt=$pdo->prepare("SELECT * FROM {$my_db}.te_lecture WHERE 1");
  	$stmt->execute();
  	while($rs=$stmt->fetch()){$pi[$rs[lec_code]]=array("area" => $rs[course], "srv_area" => $rs[teacher], "price"=>$rs[price]);}
  }
  elseif($pay_code==102)
  {
    #온/오프라인결제
  	$stmt=$pdo->prepare("SELECT * FROM {$my_db}.tl_edu WHERE 1");
  	$stmt->execute();
  	while($rs=$stmt->fetch()){$pi[$rs['edu_code']]=array("area" => $rs['edu_title'], "srv_area" => $rs['edu_teacher'], "price"=>$rs['edu_pay'],"place"=>$rs['edu_addr']);}
  }
  ##validCheck (사용자 지정결제는 예외)
  #IE 10 이하
  if(strpos(strtoupper($_SERVER["HTTP_USER_AGENT"]), "MSIE")){alertBack("IE 10 이하에서는 사용할 수 없습니다.");}

  #결제금액 비교(100, 101)
  $totamt=0;
  foreach(explode(",",$smp) as $v){list($state,$month,$price)=explode(":",$v);$totamt=$totamt+$price;}
  if($totamt!=$amt){alertBack("결제지역의 금액이 올바르지 않습니다.");}

  #smp는 :,숫자만 허용됨
  if(preg_match("/[^0-9]/i", preg_replace("/\:|\,/i","",$smp))){alertBack("결제지역과 금액을 확인해주세요.");}

  #param checkdate
  if($paramFlag==1){alertBack("결제 방식이 잘못 되었습니다. 다시 시도해 주시기 바랍니다.1");}

  /*
    Point 정책 적용시 고려사항
    Point (point, fee, amount) > 포인트는 양수, 1000원이상, 사용가능포인트체크
  */
      
  #DB > log
  $rtn=array();
  if(gettype($_POST)=="array")
  {
    foreach( $_POST AS $key => $val ){$rtn[$key]=$val;}
    $log_data=json_encode($rtn,JSON_UNESCAPED_UNICODE);
    $log_data_fm=base64_encode($log_data);
  }
  $smp_fm=base64_encode($smp);
?>
<?if($pay_type=="edu") echo "<div class='wrap'>"?>
<div class='f20 bold center' style='padding:100px 0 30px'>회원님께서 신청하신 항목은 아래와 같습니다. 확인 후 결제를 진행 해 주세요.</div>
<div class='f14 bold'>· 결제 항목</div>
<table class="tbl_new_grid" style='border-top:1px solid #000'>
<tr height="40">
		<th>No</th>
		<th>신청 항목</th>
		<th><?=$paycd_gb?></th>
		<th><?=$pay_pl?></th>
		<th>금액</th>
	</tr>
<?
$smp_arr=explode(",",$smp);
$html=[];
foreach($smp_arr as $v)
{
	$n++;
	list($state,$month,$price)=explode(":",$v);
    if($pay_code==100){$month="{$month} 개월";}
    elseif($pay_code==101){$month="{$month} 일";}
    elseif($pay_code==102){$month=($month==0)?"오프라인<br>({$pi[$state][place]})":"온라인";}
    $nfprice=number_format($price);
	$html[]="<tr height='60'>";
    $html[]="<td class='center'>{$n}</td>";
    $html[]="<td class='center blue bold'>{$pi[$state][area]}</td>";
    $html[]="<td class='center'>{$pi[$state][srv_area]}</td>";
    $html[]="<td class='center bold' style='font-size:18px'>{$month}</td>";
    $html[]="<td class='right bold orange' style='font-size:18px'>{$nfprice} 원</td></tr>";
}
$html=implode("",$html);
echo $html;
?>    
</table><br>
<form id="fm_data" name="fm_data" method="post">
  <input type='hidden' name='mode' id='mode' value="100">
  <input type='hidden' name='smp' id='smp' value="<?=$smp_fm?>">
  <input type='hidden' name='pay_opt' id='pay_opt' value="<?=$pay_opt?>">
  <input type='hidden' name='pay_code' id='pay_code' value="<?=$pay_code?>">
  <input type='hidden' name='order_no' id='order_no' value="<?=$order_no?>">
  <input type='hidden' name='amt' id='amt' value="<?=$amt?>">
  <input type='hidden' name='dc_rate' id='dc_rate' value="<?=$dc_rate?>">
  <input type='hidden' name='log_data' id='log_data' value="<?=$log_data_fm?>">
  <input type='hidden' name='pay_type' id='pay_type' value="<?=$pay_type?>">
</form>

<!-- 전화번호 수정 -->
<div class='tble_view_mask' style='display:none'>
  <div class='tble_view_write' style='transform: translateX(-30%);-ms-transform: translate(-30%, 0);top:30%;z-index:10'>
  	<div class='title'><span class='ment'>회원 정보수정 <span class='f13 bold_400'>(수정된 [휴대폰번호]는 <span style='color:red'>회원정보에 바로 반영</span>이 됩니다.)</span></span><span class="close" id="btnInter_close"><img src="/img/btn/btn_ly_close01.gif"></span></div>
  	<div style='padding:20px'>
  		<table class="tbl_noline input_box">
  			<tr height='80'>
  				<th width='20%' style="font-size:16px;font-weight:bold">휴대폰</th>
  				<td>
  					<input type="text" name="mobile1" id="mobile1" value="<?=$exmob[0]?>" maxlength="3" style='width:80px;padding:2px;font-size:16px'>-
  					<input type="text" name="mobile2" id="mobile2" value="<?=$exmob[1]?>" maxlength="4" style='width:80px;padding:2px;font-size:16px'>-
  					<input type="text" name="mobile3" id="mobile3" value="<?=$exmob[2]?>" maxlength="4" style='width:80px;padding:2px;font-size:16px'>
  				</td>
  				<th width='25%'><span class='btn_box_sss btn_tank radius_10'  id="mobile_edit">수정하기</span></th>	
  			</tr>
  		</table>
  	</div>
  </div>
</div>
<!-- 전화번호 수정 -->

<div class='f20 center' style='-moz-border-radius:10px;border-radius:10px;-webkit-border-radius: 10px;border:1px solid #ddd;background:#F7FBFF;padding:20px'>
	카드승인 안내 및 계좌전송 전화번호 : <span class='bold_900' style='color:#1B43A9' id='mobile_info'><?=$mobile?></span> 
	<span class='btn_box_sss btn_tank_1 radius_10' style='width:50px' id='btnInter_open'>수정</span>
</div>
<br><br>
<div class="center"><span class='btn_box_ss btn_tank radius_10'  id="payment-button">결제하기</span></div>
<!-- </div> -->
<?
if($pay_type=="edu")
{
    echo "</div>";
    include($_SERVER["DOCUMENT_ROOT"]."/lec/inc/footer.php");
    $pay_type=1;
} 
else
{
    include($_SERVER["DOCUMENT_ROOT"]."/inc/footer.php");
}
?>

<!-- 결제창을 연동할 HTML페이지에 일반 결제 JavaScript 파일을 추가 -->
<script src="https://js.tosspayments.com/v1"></script>
<script> 
  var tossPayments = TossPayments("<?=$toss->clientKey?>");
  $("#payment-button").click(function () 
  {
    $.ajax(
    {
  	  type: "POST",
  	  data: $('#fm_data').serialize(),
  		url: "/res/_payResult.php",
  		dataType: "JSON",
  		success: function(data)
  		{
  		   //price check, paylog save
  		  var success=data.success;
  		  if(success==1)
  		  { 
  		    let returnURL = "https://" + window.location.hostname + "/member/_Tosspay_result.php";		     
  		    let failURL   = "https://" + window.location.hostname + "/member/Tosspay_fail.php";
  		    let virtualURL= "https://" + window.location.hostname + "/member/_Tosspay_result.php?mode=10";
             
          let customerTaxType = "소득공제";      
          let method = '<?=$paycd_str?>';
          switch(method)
          {
            case '카드' :
            {
              tossPayments.requestPayment(method, 
              {
                amount: "<?=$amt?>",
                orderId: "<?=$order_no?>",
                orderName: "탱크옥션",
                customerName: "<?=$client_name?>",
                successUrl: returnURL+"?mode=1<?=$pay_type?>",
                failUrl: failURL
              }).catch(function(error) {alert(error.message);});
            } break;
            case '계좌이체' :
            {
              tossPayments.requestPayment(method, 
              {
                amount: "<?=$amt?>",
                orderId: "<?=$order_no?>",
                orderName: "탱크옥션",
                customerName: "<?=$client_name?>",
                successUrl: returnURL+"?mode=3<?=$pay_type?>",
                failUrl: failURL
              }).catch(function(error){alert(error.message);});                
            } break;
            case '가상계좌' :
            {
              tossPayments.requestPayment(method, 
              {
                amount: "<?=$amt?>",
                orderId: "<?=$order_no?>",
                orderName: "탱크옥션",
                useEscrow: false,
                customerName: "<?=$client_name?>",
                validHours: 12,
                cashReceipt: {type: customerTaxType},
                successUrl: returnURL+"?mode=4<?=$pay_type?>",
                failUrl: failURL,
                virtualAccountCallbackUrl: virtualURL
              }).catch(function(error){alert(error.message);});               
            } break;
          }  		    
  		  }
  		  else
  		  {
  		    alert('결제 오류가 발생되었습니다'); 
  		    //history.back( -1 );
        }
  		}   
    });
  }); 
  
  $("#mobile_edit").click(function ()
  {
    var mobile1=$("#mobile1").val();
    var mobile2=$("#mobile2").val();
    var mobile3=$("#mobile3").val();
    var mobile=mobile1+"-"+mobile2+"-"+mobile3;
    var telRegex=/^01([0|1|6:7|8|9])-?([0-9]{3,4})-?([0-9]{4})$/;
    var chk=telRegex.test(mobile);
    if(chk==false){alert("전화번호를 확인해주세요."); return false;}
    $.ajax(
    {
      type: "POST",
  		url: "/res/payResult.php?mode=50&mobile="+mobile,
  		dataType: "JSON",
  		success: function(data)
  		{
  		  if(data.success==1){$("#mobile_info").html(data.mobile);}
  		  else{alert(data.msg); return false;} 
        $(".tble_view_mask").hide(); 		  
  		}
    });
  });
  
  $("#btnInter_close").click(function (){$(".tble_view_mask").hide();}); 
  $("#btnInter_open").click(function (){
	  $(".tble_view_mask").show();
	  if("<?=$mobile_agent?>"){$(".tble_view_mask").css({"width":"1200px"});}
	  else{$(".tble_view_mask").css({"width":"100%"});}
  }); 
</script>