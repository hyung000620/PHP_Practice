<?
  $page_code="9016";
  $member_only=true;
  $cpn_deny = true;
  include($_SERVER["DOCUMENT_ROOT"]."/inc/header.php");
  
  #param check
  $order_no=(int)$order_no;
  $pay_code=(int)$pay_code;
  $pay_opt=(int)$pay_opt;
  $amt=(int)$amt; 
  $bank_code=(int)$bank_code;
  $paramFlag=($order_no==0 || $pay_code==0 || $pay_opt==0 || $amt==0 || $bank_code==0)? 1 : 0;
  
  #cd to str
  $paycd_gb=($pay_code==100)? "해당지역"  : "강사명";
  $paycd_str=($pay_opt==1)? "카드" : "가상계좌";
  
  #파일로그기록
  $tmp = array("USER_AGENT" => $_SERVER["HTTP_USER_AGENT"], "POST" => $_POST);
  fileLog("[ {$order_no} ] - {$paycd_str} order page 21 Line", $tmp); // 줄바뀜 주의
  
  #결제금액(amt) 1000원이상
  if($amt < 1000){alertBack("최소 결제금액은 1000원 이상입니다.");}
    
  #결제구분 > 신처항목,(해당지역 or 강사명)
  if($pay_code==100)
  {
    #경매결제
  	$stmt=$pdo->prepare("SELECT * FROM {$my_db}.tc_price WHERE use_key=1 ORDER BY sort_num");
  	$stmt->execute();
  	while($rs=$stmt->fetch()){$pi[$rs[state]]=array("area" => $rs[area], "srv_area" => $rs[service_area]);}	 
  }
  elseif($pay_code==101)
  {
    #강의결제
  	$stmt=$pdo->prepare("SELECT * FROM {$my_db}.te_lecture WHERE 1");
  	$stmt->execute();
  	while($rs=$stmt->fetch()){$pi[$rs[lec_code]]=array("area" => $rs[course], "srv_area" => $rs[teacher]);}
  }

  ##validCheck (사용자 지정결제는 예외)
  #IE 10 이하
  if(strpos(strtoupper($_SERVER["HTTP_USER_AGENT"]), "MSIE")){alertBack("IE 10 이하에서는 사용할 수 없습니다.");}

  #선택지역과 결제금액 비교(100, 101)
  $totamt=0;
  foreach(explode(",",$smp) as $v){list($state,$month,$price)=explode(":",$v); $totamt=$totamt+$price; }
  if($totamt!=$amt){alertBack("결제지역의 금액이 올바르지 않습니다.");}

  #smp는 :,숫자만 허용됨
  if(preg_match("/[^0-9]/i", preg_replace("/\:|\,/i","",$smp))){alertBack("결제지역과 금액을 확인해주세요.");}

  #param checkdate
  if($paramFlag==1){alertBack("결제 방식이 잘못 되었습니다. 다시 시도해 주시기 바랍니다.");}

  #DB > log
  $rtn=array();
  if(gettype($_POST)=="array")
  {
    foreach( $_POST AS $key => $val ){$rtn[$key]=$val;}
    $log_data=json_encode($rtn);
  }

  $SQL="SELECT COUNT(*) FROM {$my_db}.tm_pay_log WHERE order_no={$order_no} AND id='{$client_id}'";
  $stmt=$pdo->prepare($SQL);
  $stmt->execute();
  $rowCnt=$stmt->fetchColumn();

  if($rowCnt==0)
  {
    $ISQL="INSERT INTO {$my_db}.tm_pay_log SET order_type = '{$pay_opt}',pay_code = '{$pay_code}', wdate=NOW(), order_no={$order_no},id='{$client_id}', name='{$client_name}',amt='{$amt}' ,log_text='{$log_data}', order_ip='{$_SERVER['REMOTE_ADDR']}'";
    $stmt=$pdo->prepare($ISQL);
    $stmt->execute();
  }
  else
  {
    $USQL="UPDATE {$my_db}.tm_pay_log SET , wdate=NOW(),amt ='{$amt}', log_text='{$log_data}', order_ip='{$_SERVER['REMOTE_ADDR']}' WHERE order_no={$order_no} AND id='{$client_id}'"; 
    $stmt=$pdo->prepare($USQL);
    $stmt->execute();
  }

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
	echo "<tr height='40'>
		      <td class='center'>{$n}</td>
		      <td class='center blue bold'>{$pi[$state][area]}</td>
		      <td class='center'>{$pi[$state][srv_area]}</td>
		      <td class='center bold'>{$month}</td>
		      <td class='right bold orange'>".number_format($price)." 원</td>
	      </tr>";
}
?>    
</table>
<br>
<div class="center"><span class='btn_box_ss btn_tank radius_10'  id="payment-button">결제하기</span></div>

<? include($_SERVER["DOCUMENT_ROOT"]."/inc/footer.php"); ?>

<!-- 결제창을 연동할 HTML페이지에 일반 결제 JavaScript 파일을 추가 -->
<script src="https://js.tosspayments.com/v1"></script>
<script>
    var tossPayments = TossPayments("test_ck_XLkKEypNArWaNyp1leA3lmeaxYG5");
    $("#payment-button").click(function () 
    {
        let customerTaxType = "소득공제";      
        let method = '<?=$paycd_str?>';    
        if(method==="카드")
        {
          let returnURL = "https://" + window.location.hostname + "/member/_tospay_result.php?mode=1";
          let failURL = "https://" + window.location.hostname + "/member/_tospay_result.php?mode=9";
          tossPayments.requestPayment(method, 
          {
            amount: <?=$amt?>,
            orderId: '<?=$order_no?>',
            orderName: '탱크옥션',
            customerName: "<?=$client_name?>",
            successUrl: returnURL,
            failUrl: returnURL
          }).catch(function(error) {alert(error.message);});
        }
        else if(method==="가상계좌")
        {
          let returnURL = "https://" + window.location.hostname + "/member/_tospay_result.php?mode=4";
          let failURL = "https://" + window.location.hostname + "/member/_tospay_result.php?mode=9";
          let virtualURL = "https://" + window.location.hostname + "/member/success_test.php?mode=10";
          tossPayments.requestPayment(method, 
          {
            amount: <?=$amt?>,
            orderId: '<?=$order_no?>',
            orderName: '탱크옥션',
            useEscrow: false,
            customerName: '<?=$client_name?>',
            validHours: 12,
            cashReceipt: {type: customerTaxType},
            successUrl: returnURL,
            failUrl: returnURL,
            virtualAccountCallbackUrl: virtualURL
          }).catch(function(error){alert(error.message);});          
        }
    });  
</script>