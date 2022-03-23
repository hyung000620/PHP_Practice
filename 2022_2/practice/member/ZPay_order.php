<?
  $page_code="9016";
  $member_only=true;
  $cpn_deny = true;
  include($_SERVER["DOCUMENT_ROOT"]."/inc/header.php");
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
  $paycd_str=($pay_opt==1)? "카드" : "가상계좌";
  
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

<div>회원님께서 신청하신 항목은 아래와 같습니다. 확인 후 결제를 진행 해 주세요.</div>
<table class="tbl_grid">
<tr height="40">
		<th>No</th>
		<th>신청 항목</th>
		<th><?=$paycd_gb?></th>
		<th>기간</th>
		<th>금액</th>
	</tr>
<?
$smp_arr=explode(",",$smp);
foreach($smp_arr as $v)
{
	$n++;
	list($state,$month,$price)=explode(":",$v);
	$month=($pay_code==100) ? "{$month} 개월" : "{$month} 일";
  $nfprice=number_format($price);
	echo "<tr height='40'>
		      <td class='center'>{$n}</td>
		      <td class='center blue bold'>{$pi[$state][area]}</td>
		      <td class='center'>{$pi[$state][srv_area]}</td>
		      <td class='center bold'>{$month}</td>
		      <td class='right bold orange'>{$nfprice} 원</td>
	      </tr>";
}
?>    
</table>
<br>

<form id="fm_data" name="fm_data" method="post">
  <input type='hidden' name='mode' id='mode' value="100">
  <input type='hidden' name='smp' id='smp' value="<?=$smp_fm?>">
  <input type='hidden' name='pay_opt' id='pay_opt' value="<?=$pay_opt?>">
  <input type='hidden' name='pay_code' id='pay_code' value="<?=$pay_code?>">
  <input type='hidden' name='order_no' id='order_no' value="<?=$order_no?>">
  <input type='hidden' name='amt' id='amt' value="<?=$amt?>">
  <input type='hidden' name='dc_rate' id='dc_rate' value="<?=$dc_rate?>">
  <input type='hidden' name='log_data' id='log_data' value="<?=$log_data_fm?>">
</form>

<div class="center"><span class='btn_box_ss btn_tank radius_10'  id="payment-button">결제하기</span></div>

<? include($_SERVER["DOCUMENT_ROOT"]."/inc/footer.php"); ?>

<!-- 결제창을 연동할 HTML페이지에 일반 결제 JavaScript 파일을 추가 -->
<script src="https://js.tosspayments.com/v1"></script>
<script> 
  var tossPayments = TossPayments("<?=$toss->clientKey?>");
  $("#payment-button").click(function () 
  {
    //price check, paylog save
    $.ajax(
    {
  	  type: "POST",
  	  data: $('#fm_data').serialize(),
  		url: "/res/payResult.php",
  		dataType: "JSON",
  		success: function(data)
  		{
  		  var success=data.success;
  		  if(success==1)
  		  { 		     
          let customerTaxType = "소득공제";      
          let method = '<?=$paycd_str?>';    
          if(method === "카드")
          {
            let returnURL = "https://" + window.location.hostname + "/member/_tospay_result.php?mode=1";
            tossPayments.requestPayment(method, 
            {
              amount: "<?=$amt?>",
              orderId: "<?=$order_no?>",
              orderName: "탱크옥션",
              customerName: "<?=$client_name?>",
              successUrl: returnURL,
              failUrl: returnURL
            }).catch(function(error) {alert(error.message);});
          }
          else if(method === "가상계좌")
          {
            let returnURL   = "https://" + window.location.hostname + "/member/_tospay_result.php?mode=4";   
            let virtualURL   = "https://" + window.location.hostname + "/member/_tospay_result.php?mode=10";   
            tossPayments.requestPayment(method, 
            {
              amount: "<?=$amt?>",
              orderId: "<?=$order_no?>",
              orderName: "탱크옥션",
              useEscrow: false,
              customerName: "<?=$client_name?>",
              validHours: 1,
              cashReceipt: {type: customerTaxType},
              successUrl: returnURL,
              failUrl: returnURL,
              virtualAccountCallbackUrl: virtualURL
            }).catch(function(error){alert(error.message);});          
          }  		    
  		  }
  		  else
  		  {
  		    alert('결제 오류가 발생되었습니다'); 
  		    history.back( -1 );
        }
  		}   
    });
  });  
</script>