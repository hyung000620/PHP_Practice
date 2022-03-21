<?php
if($_GET['mode']!=10)
{
  $page_code="9016";    
  $member_only = true;
  $cpn_deny    = true;
}
include($_SERVER["DOCUMENT_ROOT"]."/inc/header.php");
include($_SERVER["DOCUMENT_ROOT"]."/member/Toss.php");

$mode=(int)$mode;
$html=array();

if($mode==1 || $mode==4)
{
    $paymentKey = $_GET['paymentKey'];
    $order_no = $_GET['orderId'];
    $amount = $_GET['amount'];
    $tossURL = "https://api.tosspayments.com/v1/payments/{$paymentKey}";
    $data = ['orderId' => $order_no, 'amount' => $amount];
    #smp, pay_code, pay_opt
    // $SQL="SELECT * FROM {$my_db}.tm_pay_log WHERE order_no = '{$order_no}' AND id='{$client_id}'";
    // $stmt=$pdo->prepare($SQL);
    // $stmt->execute();
    
    // if($rs=$stmt->fetch()){$log=json_decode($rs['log_text']);}
    // $pay_opt=$log->pay_opt;
    // $smp=$log->smp;
    // $pay_code=$log->pay_code;
    // $amount=$log->amount;
    
    // #파일로그
    // fileLog("[result] > log 2", $SQL);    
    // fileLog("[result] > log 3", json_encode($log));    
}

// $url = 'https://api.tosspayments.com/v1/payments/{$paymentKey}';
// $data = ['orderId' => $order_no, 'amount' => $amount];
$toss->curl_post($tossURL,$data);
$credential = base64_encode($secretKey . ':');
$curlHandle = curl_init($tossURL);

curl_setopt_array($curlHandle, [
    CURLOPT_POST => TRUE,
    CURLOPT_RETURNTRANSFER => TRUE,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_HTTPHEADER => [
        'Authorization: Basic ' . $credential,
        'Content-Type: application/json'
    ],
    CURLOPT_POSTFIELDS => json_encode($data)
]);

$response = curl_exec($curlHandle);
$httpCode = curl_getinfo($curlHandle, CURLINFO_HTTP_CODE);
curl_close($curlHandle);
$isSuccess = $httpCode == 200;
$responseJson = json_decode($response,true);

$orderName = $responseJson['orderName'];// 상품이름
$method = $responseJson['method'];       // 결제방식
$status = $responseJson['status'];       // 결제진행상태
$statusMsg = $toss->arr_payStatus($status);
$requestedAt = $responseJson['requestedAt'];
$currency = $responseJson['currency']; // 거래화폐
$totalAmount = $responseJson['totalAmount']; // 판매가격
$balanceAmount = $responseJso['balanceAmount']; 
$suppliedAmount = $responseJson['suppliedAmount']; // 세 후 금액
$vat = $responseJson['vat']; // 세금
$secret = $responseJson['secret']; 

//  !카드!      ( 가상계좌 일 경우 null )
// $company = $responseJson -> card -> company; // 은행명
$number = $responseJson['card']['number']; // 카드번호
$installmentPlanMonths['card']['installmentPlanMonths']; // 할부 개월수
$cardType = $responseJson['card']['cardType']; // 기프트
$ownerType = $responseJson['card']['ownerType'];  // 개인 or 법인
$receiptUrl = $responseJson['card']['receiptUrl']; // 매출전표 url


//  !가상계좌!     ( 카드 일 경우 null )
$virtualAccount = $responseJson['virtualAccount'];
$accountNumber = $responseJson['virtualAccount']['accountNumber']; // 가상계좌번호
$accountType = $responseJson['virtualAccount']['accountType'];     // 결제 타입
// $bank = $responseJson -> virtualAccount -> bank;                   // 은행
$customerName = $responseJson['virtualAccount']['customerName'];   // 결제자 이름 (변경가능)
$dueDate = $responseJson['virtualAccount']['dueDate'];             // 결제 기한
$expired = $responseJson['virtualAccount']['expired'];             // false
$settlementStatus = $responseJson['virtualAccount']['settlementStatus']; 
$refundStatus = $responseJson['virtualAccount']['refundStatus'];

// 가상게좌 영수증출력시 (미발행시 cashReceipt = null)
$cashReceipt = $responseJson['cashReceipt']; 
$receiptKey  = $responseJson['receiptKey'] ;  // 현금영수증 발급키 ( 취소일때도 필요 )
$cashType = $responseJson['cashReceipt']['type']; // 소득공제
$accountReceiptUrl = $responseJson['cashReceipt']['receiptUrl']; // 영수증 url
$cashAmount = $responseJson['cashReceipt']['amount']; // 물품 가격
$approvedAt   = $responseJson['cashReceipt']['approvedAt']  ; // 현굼영수증이 발급된 날짜와시간
$canceledAt  = $responseJson['cashReceipt']['canceledAt'] ; // 현굼영수증이 취소된 날짜와시간

$virtual_data = array(
	"accountNumber" => $accountNumber , "accountType" => $accountType , "bank" => $bank ,
	"customerName" => $customerName , "dueDate" => $dueDate, "expired" => $expired ,
	"settlementStatus" => $settlementStatus , "refundStatus" => $refundStatus
);

$receipt_dataArr = array(
    "type" => $cashType , "issueNumber" => "" , "receiptUrl" => $accountReceiptUrl , "amount" => $cashAmount , "texFreeAmount" => 0, "receiptKey" => $receiptKey, "approvedAt" => $approvedAt , "canceledAt" => $canceledAt
);

if ($virtualAccount != null) {
	$result_data = json_encode($virtual_data, JSON_UNESCAPED_UNICODE);
}

if ($cashReceipt != null) {
    $receipt_data = json_encode($receipt_dataArr, JSON_UNESCAPED_UNICODE);
}

if($mode == 1) {
	$accType = '카드';
    $bank = $responseJson['card']['company'];
} else if($mode == 4) {
	$accType = '가상계좌';
	$bank = $responseJson['virtualAccount']['bank'];
	$receiptUrl = $accountReceiptUrl;
}

if($isSuccess) {
	$SQL = "UPDATE {$my_db}.tm_pay_log SET order_type = '{$mode}', accountee = '{$customerName}', paymentKey = '{$paymentKey}' , result_data = '{$result_data}', status_message = '{$statusMsg}',  receipt_data = '{$receipt_data}', return_status = '{$status}' , bank = '{$bank}' , receipt='{$receiptUrl}' , receipt_type = '{$cashType}'  WHERE order_no = '{$order_no}'";
	$stmt = $pdo -> prepare($SQL);
	$stmt -> execute();
	$toss->fileLog("[success_test] > isSuccess > log 1",$SQL);
} else {
	echo 
		"<h1>결제실패</h1>
		 <span>에러코드 : {$responseJson->code} </span>";
}

switch($mode) {
	case 1 : // 카드
    {    
			$sql ="SELECT * FROM {$my_db}.tm_pay_log WHERE order_no = '{$order_no}'";
			$stmt = $pdo -> prepare($sql);
			$stmt -> execute();
			$rs = $stmt -> fetch();
			$msg = "<span class='f18 bold'> 카드결제가 <span class='red'>완료</span> 되었습니다.<br</span>";
			$msg .= "<table class='tbl_grid'>";
			$msg .= "<tr>";
			$msg .= "<th>결제방식</th>";
			$msg .= "<th>거래일자</th>";
			$msg .= "<th>금액</th>";
			$msg .= "<th>영수증 출력</th>";
			$msg .= "</tr>";
			$msg .= "<tr>";
			$msg .= "<td>{$accType}</td>";
			$msg .= "<td>{$rs['wdate']}</td>";
			$msg .= "<td>{$amount}</td>";
			$msg .= "<td><button><a href='{$rs['receipt']}' target='_blank'>영수증 출력</a></button></td>";
			$msg .= "</tr>";
			$msg .= "</table>";
			echo $msg;
			$toss->dbPay($status, $smp, $pay_code, $order_no, $pay_opt, $client_id); 
				
		} break;
					
		case 4 : // 가상계좌

        {
            $chk=in_array($_SERVER["REMOTE_ADDR"],$tossPayIP);
            if($chk == 0) {
                alertBack('비정상적인 경로로 접근하였습니다.');
            }
			$sql ="SELECT * FROM {$my_db}.tm_pay_log WHERE order_no = '{$order_no}'";
			$stmt = $pdo -> prepare($sql);
			$stmt -> execute();
			$rs = $stmt -> fetch();

			$rs_data = json_decode($rs['result_data'],true);
			$dueDate = date("Y-m-d h:i:s",strtotime($rs_data['dueDate']));
			$accountNumber = $rs_data['accountNumber'];
			$accountType = $rs_data['accountType'];

			$msg = "<span class='f18 bold'> 가상계좌 결제신청 <span class='red'>완료</span> 되었습니다.<br</span>";
		    $msg .= "<span class='f18 bold'> <span class='red'>12시간 이내</span>에 이체 해주세요.<br</span>";
			$msg .= "<table class='tbl_grid'>";
			$msg .= "<tr>";
			$msg .= "<th>결제방식</th>";
			$msg .= "<th>거래일자</th>";
			$msg .= "<th>가상계좌번호</th>";
			$msg .= "<th>입금기한</th>";
			$msg .= "<th>금액</th>";
			$msg .= "<th>결제정보</th>";
			$msg .= "</tr>";
			$msg .= "<tr>";
			$msg .= "<td>{$accType}</td>";
			$msg .= "<td>{$rs['wdate']}</td>";
			$msg .= "<td>{$accountNumber}</td>";
			$msg .= "<td>{$dueDate}</td>";
			$msg .= "<td>{$amount}원</td>";
			if($rs['return_status'] == 'DONE' && $rs['receipt'] != null) {
				$msg .= "<td><button><a href='{$rs['receipt']}' target='_blank'>영수증 출력</a></button></td>";
			} else {
				$msg .= "<td>{$accountType}</td>";
			}
			$msg .= "</tr>";
			$msg .= "</table>";
			echo $msg;
			$toss->dbPay($status, $smp, $pay_code, $order_no, $pay_opt, $client_id);
        }
        break;

    case 9:
        {

        }
        break;

	case 10: 
		{
			$chk=in_array($_SERVER["REMOTE_ADDR"],$tossPayIP);
			if($chk==0)
			{
			fileLog("[result callback] > log 10", "IP : {$_SERVER['REMOTE_ADDR']}");
			alertHref("/","비정상 경로로 접근하셨습니다.");
			}

			#callback
			$json = file_get_contents("php://input");
			$tossData = json_decode($json);    
			$status=$tossData->status;
			$order_no=$tossData->orderId;  
			$orderName=$tossData->customerName;
			
			#파일로그
			fileLog("[result callback] > log 11", json_encode($tossData));
			if ($status == 'DONE') 
			{
			#smp, pay_code, pay_opt
			$SQL="SELECT * FROM {$my_db}.tm_pay_log WHERE order_no = '{$order_no}'";
			$stmt=$pdo->prepare($SQL);
			$stmt->execute(); 
			$rs=$stmt->fetch();
			if($rs){$log=json_decode($rs['log_text']);}
			$pay_opt=$log->pay_opt;
			$smp=$log->smp;
			$pay_code=$log->pay_code;
			$amount=$log->amt;
			$paymentKey = $rs['paymentkey'];
			$id=$rs['id'];
			
			#파일로그
			fileLog("[result callback] > log 13", $SQL);
			fileLog("[result callback] > log 14", json_encode($log));
			
			if($paymentKey)
			{
				$tossURL = "https://api.tosspayments.com/v1/payments/test_ck_OEP59LybZ8BmOpKDwgJr6GYo7pRe";
				$data = ['orderId' => $order_no, 'amount' => $amount];
				
				$res=$toss->curl_post($tossURL, $data);
				$tossData=$res['resData'];
				$code=$res['resCode'];

				#파일로그기록 > 가상계좌
				fileLog("[result] > 가상계좌 > 12",json_encode($tossData));
            }
       
            #파일로그
            fileLog("status:{$status} smp:{$smp} pay_code:{$pay_code} order_no:{$order_no} pay_opt:{$pay_opt} id:{$id}", 'test test test test');
            #Toss.php > dbPay
            $toss->dbPay($status, $smp, $pay_code, $order_no, $pay_opt, $id);

            #pay_log > update  ,/arr/arrPay.php                                                 
            $USQL="UPDATE {$my_db}.tm_pay_log SET return_status = '{$status}', status_message = '{$status_arr[$status]}' WHERE order_no = '{$order_no}' AND id='{$id}'";
            $stmt=$pdo->prepare($USQL);
            $stmt->execute();
            #파일로그
            fileLog("[result callback] > log 15", $USQL);
            }
		} break;
    
}   
?>

<section>
	<div class='center' style='padding:20px'><span id="his" class='btn_box_ss btn_tank radius_10' style='width:110px'>결제 관리</span></div>
</section>

<script>
	$('#his').click(()=>{
		location.href="./pay_history2.php";
	});
</script>