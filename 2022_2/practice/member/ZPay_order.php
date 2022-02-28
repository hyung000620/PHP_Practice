<?
$page_code="9016";

include($_SERVER["DOCUMENT_ROOT"]."/inc/header.php");
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

<script src="https://js.tosspayments.com/v1"></script>

<script>
    var tossPayments = TossPayments("test_ck_XLkKEypNArWaNyp1leA3lmeaxYG5");
    var orderId = new Date().getTime();
     // "카드 : 1" 혹은 "가상계좌 : 2"
    var name = <?="'".$client_name."'";?>;
    $("#payment-button").click(function () {
        var method = '<?=$pay_opt==1?'카드':'가상계좌';?>';    
        var paymentData = {
            amount: <?=$amt;?>,
            orderId: orderId,
            orderName: "탱크옥션",
            customerName: name,
            successUrl: "https://kb.tankauction.com/member/Z_success.php",
            failUrl: "https://kb.tankauction.com/member/fail_test.php",
        };

        if (method === '가상계좌') {
            paymentData.virtualAccountCallbackUrl = 'https://kb.tankauction.com/member/virtual_callback.php'
        }

        tossPayments.requestPayment(method, paymentData);
        
    });
</script>