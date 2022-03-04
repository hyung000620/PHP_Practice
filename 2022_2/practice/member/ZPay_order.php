<?
$page_code="9016";

include($_SERVER["DOCUMENT_ROOT"]."/inc/header.php");

$_SESSION['smp'] = $smp;
$_SESSION['pay_opt'] = $pay_opt;
$_SESSION['pay_code'] = $pay_code;
?>
<div>회원님께서 신청하신 항목은 아래와 같습니다. 확인 후 결제를 진행 해 주세요.</div>
<table class="tbl_grid">
<tr height="40">
		<th>No</th>
		<th>신청 항목</th>
		<th>
		<?
			echo ($pay_code==100) ? "해당 지역" : "강사명";
		?>
		</th>
		<th>기간</th>
		<th>금액</th>
	</tr>
<?
if($pay_code==100)
{
	//$result=sql_query("SELECT * FROM {$my_db}.tc_price WHERE use_key=1 ORDER BY sort_num");
	//while($rs=mysql_fetch_array($result))
	$stmt=$pdo->prepare("SELECT * FROM {$my_db}.tc_price WHERE use_key=1 ORDER BY sort_num");
	$stmt->execute();
	while($rs=$stmt->fetch())
	{
		$pi[$rs[state]]=array("area" => $rs[area], "srv_area" => $rs[service_area]);
	}	
}
elseif($pay_code==101)
{
	//$result=sql_query("SELECT * FROM {$my_db}.te_lecture WHERE 1");
	//while($rs=mysql_fetch_array($result))
	$stmt=$pdo->prepare("SELECT * FROM {$my_db}.te_lecture WHERE 1");
	$stmt->execute();
	while($rs=$stmt->fetch())
	{
		$pi[$rs[lec_code]]=array("area" => $rs[course], "srv_area" => $rs[teacher]);
	}
}

$smp_arr=explode(",",$smp);
foreach($smp_arr as $v)
{
	$n++;
	list($state,$month,$price)=explode(":",$v);
	$month=($pay_code==100) ? "{$month} 개월" : "{$month} 일";
	echo "
	<tr height='40'>
		<td class='center'>{$n}</td>
		<td class='center blue bold'>{$pi[$state][area]}</td>
		<td>{$pi[$state][srv_area]}</td>
		<td class='center bold'>{$month}</td>
		<td class='right bold orange'>".number_format($price)." 원</td>
	</tr>";
    
}
?>    
</table>
<br>
<div class="center"><span class='btn_box_ss btn_tank radius_10'  id="payment-button">결제하기</span></div>
<?
include($_SERVER["DOCUMENT_ROOT"]."/inc/footer.php");
?>
<!-- 결제창을 연동할 HTML페이지에 일반 결제 JavaScript 파일을 추가 -->
<script src="https://js.tosspayments.com/v1"></script>
<script>
    
    /** 
        TossPayments 함수로 SDK 초기화를 진행.
        클라이언트 키를 TossPayments 함수에 넣고 실행하면
        초기화 도니 객체가 생성됩니다.
    */ 
    var tossPayments = TossPayments("test_ck_XLkKEypNArWaNyp1leA3lmeaxYG5");
    $("#payment-button").click(function () {
        var method = '<?=$pay_opt==1?'카드':'가상계좌';?>';    
        var paymentData = {
            amount: <?=$amt?>,
            orderId: <?=$order_no?>,
            orderName:'탱크옥션',
            customerName: '<?=$client_name?>',
            successUrl: "https://kb.tankauction.com/member/ZPay_result.php",
            failUrl: "https://kb.tankauction.com/member/fail_test.php",
        };

        if (method === '가상계좌') {
            paymentData.virtualAccountCallbackUrl = 'https://kb.tankauction.com/member/virtual_callback.php'
        }

        tossPayments.requestPayment(method, paymentData);
    });
</script>