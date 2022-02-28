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

$bank = $responseJson->virtualAccount->bank;
$accountNumber = $responseJson->virtualAccount->accountNumber;
$customerName = $responseJson->virtualAccount->customerName;
$dueDate = $responseJson->virtualAccount->dueDate;
$totalAmount = $responseJson->totalAmount;
?>
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
            <td><?=$bank?></td>
            <td><?=$accountNumber?></td>
            <td><?=$customerName?></td>
            <td><?=$dueDate?></td>
            <td><?=$totalAmount?></td>
        </tr>
    </table>
    <div class='center' style='padding:20px'><a href='/'><span class='btn_box_ss btn_tank radius_10' style='width:110px'>홈으로 가기</span></a></div>
</div>    

<? include($_SERVER["DOCUMENT_ROOT"]."/inc/footer.php");
?>