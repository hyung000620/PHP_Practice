<?php
$page_code="9016";
include($_SERVER["DOCUMENT_ROOT"]."/inc/header.php");
$paymentKey = $_GET['paymentKey'];
$orderId = $_GET['orderId'];
$amount = $_GET['amount'];

$secretKey = 'test_sk_7DLJOpm5QrlmRXDWwOL8PNdxbWnY';

$url = 'https://api.tosspayments.com/v1/payments/' . $paymentKey;

$data = ['orderId' => $orderId, 'amount' => $amount];

$credential = base64_encode($secretKey . ':');

$curlHandle = curl_init($url);
 
curl_setopt_array($curlHandle, [
    CURLOPT_POST => TRUE,
    CURLOPT_RETURNTRANSFER => TRUE,
    CURLOPT_HTTPHEADER => [
        'Authorization: Basic ' . $credential,
        'Content-Type: application/json'
    ],
    CURLOPT_POSTFIELDS => json_encode($data)
]);

$response = curl_exec($curlHandle);

$httpCode = curl_getinfo($curlHandle, CURLINFO_HTTP_CODE);
$isSuccess = $httpCode == 200;
$responseJson = json_decode($response);

$account_info = array(
    "bank" => $responseJson->virtualAccount->bank, //은행 명
    "accountNumber" => $responseJson->virtualAccount->accountNumber, //계좌번호  
    "customerName" => $responseJson->virtualAccount->customerName, //입금자 명
    "dueDate" => $responseJson->virtualAccount->dueDate, // 입금 기한
    "totalAmount" => $responseJson->totalAmount, // 금액
);

$method=$responseJson->method;

//status
$status_arr=array(
    "READY" => "준비됨",
    "IN_PROGRESS" => "진행중",
    "WAITING_FOR_DEPOSIT" => "가상계좌 입금 대기 중",
    "DONE" => "결제 완료됨",
    "CANCELED" => "결제가 취소됨",
    "PARTIAL_CANCELED" => "결제가 부분 취소됨",
    "ABORTED" => "카드 자동 결제 혹은 키인 결제를 할 때 결제 승인에 실패함",
    "EXPIRED" => "유효 시간(30분)이 지나 거래가 취소됨",
);
$bank=$responseJson->card->company;
$date=date("Y-m-d H:i:s",time());
$status=$responseJson->status;

$status_arr['{$status}'];
switch($method)
{
    case "카드":
        $SQL="INSERT INTO {$my_db}.tm_pay_log (order_type, order_no, id, name, bank , return_status, status_message, wdate)";
        $SQL.="VALUES ('카드', '{$orderId}', '{$client_id}', '{$client_name}', '{$bank}', '{$status}', '{$status_arr['$status']}');";
        $stmt=$pdo->prepare($SQL);
        $stmt->execute();
        break;
    case "가상계좌":
    break;
}

?>
<div><?php echo json_encode($responseJson, JSON_UNESCAPED_UNICODE); ?></div>
<div class="lh18">
	-<span class="f18 bold"> 결제요청이 <span class="red">완료</span> 되었습니다.<br></span><br>
	- 아래 지정된 계좌로 입금 후 전화(<?=$cfg_phone?>) 주시면, 확인 후 개통<br>
	- 개통 가능시간 안내 : 월 ~ 금 / 오전09:00 ~ 오후06:00 ( 점심시간 12시00분 ~ 오후 1시00분 )<br><br>
    <table class="tbl_grid">
        <tr height="40">
            <th>은행</th>
            <th>계좌번호</th>
            <th>입금자명</th>
            <th>입금기한</th>
            <th>금액</th>
        </tr>
        <tr height="40">
            <td><?=$account_info["bank"]?></td>
            <td><?=$account_info["accountNumber"]?></td>
            <td><?=$account_info["customerName"]?></td>
            <td><?=$account_info["dueDate"]?></td>
            <td><?=$account_info["totalAmount"]?></td>
        </tr>
    </table>
    <div class='center' style='padding:20px'><a href='/'><span class='btn_box_ss btn_tank radius_10' style='width:110px'>홈으로 가기</span></a></div>
</div>    

<? include($_SERVER["DOCUMENT_ROOT"]."/inc/footer.php");
?>