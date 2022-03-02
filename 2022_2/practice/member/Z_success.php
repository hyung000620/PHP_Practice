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
$isSuccess = $httpCode == 200;
$responseJson = json_decode($response);

// $account_info = array(
//     "accountNumber" => $responseJson->virtualAccount->accountNumber, //계좌번호  
//     "customerName" => $responseJson->virtualAccount->customerName, //입금자 명
//     "dueDate" => $responseJson->virtualAccount->dueDate, // 입금 기한
//     "totalAmount" => $responseJson->totalAmount, // 금액
// );

$method=$responseJson->method;
$bank = $method=="카드"?$responseJson->card->company:$responseJson->virtualAccount->bank;

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

$date=date("Y-m-d H:i:s",time());
$status=$responseJson->status;

//로그 처리

if($status == 'DONE')
{
        //$status_arr[$status];
        
        $SQL="INSERT INTO {$my_db}.tm_pay_log (order_type, order_no, id, name, bank , return_status, status_message, wdate)";
        $SQL.="VALUES ('{$method}', '{$orderId}', '{$client_id}', '{$client_name}', '{$bank}', '{$status}', '{$status_arr[$status]}', '{$date}');";
        $stmt=$pdo->prepare($SQL);
        $stmt->execute();

        $today=date("Y-m-d");
       	$smp_arr=explode(",",$smp);
       	$dc_flag=false;
		foreach($smp_arr as $v)
		{
			list($state,$month,$price)=explode(":",$v);
			switch ($pay_code)
			{
				case 100 :
					$memo="";
					$partner_pm=0;
					$sql="SELECT * FROM {$my_db}.tm_member WHERE id='{$client_id}'";
					$stmt=$pdo->prepare($sql);
					$stmt->execute();
					$rs=$stmt->fetch();
					if($rs)
					{
						if($rs[ptnr_code]==20 && $rs[partner_pm]<=0){$partner_pm=1;}
					}

					$stmt=$pdo->prepare("SELECT * FROM {$my_db}.tm_pay_result WHERE id='{$client_id}' AND pay_code='{$pay_code}' AND state='{$state}' LIMIT 0,1");
					$stmt->execute();
					$rs=$stmt->fetch();
					if($rs)
					{
						$SQL ="INSERT INTO {$my_db}.tm_pay_history(order_no,id,pay_code,paykind,months,paydate,paytime,payname,bankcode,money,state,sector,validity,tempdate,startdate,staff,memo) ";
						$SQL.="VALUES('{$rs[order_no]}','{$rs[id]}','{$rs[pay_code]}','{$rs[paykind]}','{$rs[months]}','{$rs[paydate]}','{$rs[paytime]}','{$rs[payname]}','{$rs[bankcode]}','{$rs[money]}','{$rs[state]}',";
						$SQL.="'{$rs[sector]}','{$rs[validity]}','{$rs[tempdate]}','{$rs[startdate]}','{$rs[staff]}','{$rs[memo]}')";
						$stmt=$pdo->prepare($SQL);
						$stmt->execute();
							
						$expire=($rs[validity] > $today) ? "DATE_ADD(validity,INTERVAL {$month} MONTH)" : "DATE_ADD(CURDATE(),INTERVAL {$month} MONTH)";
						$SQL ="UPDATE {$my_db}.tm_pay_result SET order_no='{$order_no}',paykind='{$pay_opt}',months='{$month}',paydate=CURDATE(),paytime=CURTIME(),bankcode='',payname='{$payname}',";
						$SQL.="money='{$price}',state='{$state}',validity={$expire},startdate=CURDATE(),staff='',memo='{$memo}' WHERE idx='{$rs[idx]}' AND id='{$client_id}'";
						$stmt=$pdo->prepare($SQL);
						$stmt->execute();
					}
					else	//신규결제
					{
						$SQL ="INSERT INTO {$my_db}.tm_pay_result(order_no,id,pay_code,paykind,months,paydate,paytime,money,state,validity,startdate,memo) ";
						$SQL.="VALUES('{$order_no}','{$client_id}','{$pay_code}','{$pay_opt}','{$month}',CURDATE(),CURTIME(),'{$price}','{$state}',DATE_ADD(CURDATE(),INTERVAL {$month} MONTH),CURDATE(),'{$memo}')";
						$stmt=$pdo->prepare($SQL);
						$stmt->execute();
					}
					if($state==99 && $month >= 12) $dc_flag=true;
					break;
				
				case 101 :
					$stmt=$pdo->prepare("SELECT * FROM {$my_db}.tm_pay_result WHERE id='{$client_id}' AND pay_code='{$pay_code}' AND sector='{$state}' LIMIT 0,1");
					$stmt->execute();
					$rs=$stmt->fetch();
					if($rs)
					{
						$SQL ="INSERT INTO {$my_db}.tm_pay_history(order_no,id,pay_code,paykind,months,paydate,paytime,payname,bankcode,money,state,sector,validity,tempdate,startdate,staff,memo) ";
						$SQL.="VALUES('{$rs[order_no]}','{$rs[id]}','{$rs[pay_code]}','{$rs[paykind]}','{$rs[months]}','{$rs[paydate]}','{$rs[paytime]}','{$rs[payname]}','{$rs[bankcode]}','{$rs[money]}','{$rs[state]}',";
						$SQL.="'{$rs[sector]}','{$rs[validity]}','{$rs[tempdate]}','{$rs[startdate]}','{$rs[staff]}','{$rs[memo]}')";
						$stmt=$pdo->prepare($SQL);
						$stmt->execute();
							
						$expire=($rs[validity] > $today) ? "DATE_ADD(validity,INTERVAL {$month} DAY)" : "DATE_ADD(CURDATE(),INTERVAL {$month} DAY)";
						$SQL ="UPDATE {$my_db}.tm_pay_result SET order_no='{$order_no}',paykind='{$pay_opt}',months='{$month}',paydate=CURDATE(),paytime=CURTIME(),bankcode='',payname='{$payname}',";
						$SQL.="money='{$price}',state='',sector='{$state}',validity={$expire},startdate=CURDATE(),staff='',memo='' WHERE idx='{$rs[idx]}' AND id='{$client_id}'";
						$stmt=$pdo->prepare($SQL);
						$stmt->execute();
					}
					else	//신규결제
					{
						$expire="DATE_ADD(CURDATE(),INTERVAL {$month} DAY)";
						$SQL ="INSERT INTO {$my_db}.tm_pay_result(order_no,id,pay_code,paykind,months,paydate,paytime,money,sector,validity,startdate,memo) ";
						$SQL.="VALUES('{$order_no}','{$client_id}','{$pay_code}','{$pay_opt}','{$month}',CURDATE(),CURTIME(),'{$price}','{$state}',{$expire},CURDATE(),'')";
						$stmt=$pdo->prepare($SQL);
						$stmt->execute();
					}
					break;
			}			
		}

        $dir = $_SERVER["DOCUMENT_ROOT"]."/member/log/tossLog";
        $fileName = date("Ymd").".log";
        $logfile = fopen($dir."/".$fileName,'a');
        fwrite($logfile,"==============================================\r\n");
        fwrite($logfile, "생성 시간 : ".date("YmdHis")."\r\n");
        fwrite($logfile, "DB :".$SQL."\r\n");
        fwrite($logfile, print_r($smp_arr)."\r\n");
        fwrite($logfile, "결과 데이터 :".json_encode($json, JSON_UNESCAPED_UNICODE));
        fwrite($logfile,"\r\n==============================================\r\n");
        fwrite($logfile,"\r\n");
        fclose($logfile);
}
?>
<div class="lh18">
    <p><?= json_encode($responseJson, JSON_UNESCAPED_UNICODE);?></p>
    <div><?=$pay_code?></div>
	-<span class="f18 bold"> 결제요청이 <span class="red">완료</span> 되었습니다.<br></span><br>
	- 아래 지정된 계좌로 입금 후 전화(<?=$cfg_phone?>) 주시면, 확인 후 개통<br>
	- 개통 가능시간 안내 : 월 ~ 금 / 오전09:00 ~ 오후06:00 ( 점심시간 12시00분 ~ 오후 1시00분 )<br><br>


    <div class='center' style='padding:20px'><a href='/'><span class='btn_box_ss btn_tank radius_10' style='width:110px'>홈으로 가기</span></a></div>
</div>    

<? include($_SERVER["DOCUMENT_ROOT"]."/inc/footer.php");
?>