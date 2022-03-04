<?php
$page_code="9016";
include($_SERVER["DOCUMENT_ROOT"]."/inc/header.php");

$smp= $_SESSION['smp'];
$pay_opt= $_SESSION['pay_opt'];
$pay_code= $_SESSION['pay_code'];

$paymentKey = $_GET['paymentKey'];
$order_no = $_GET['orderId'];
$amount = $_GET['amount'];

$log_text=$smp."|".$pay_opt."|".$pay_code;
$secretKey = 'test_sk_7DLJOpm5QrlmRXDWwOL8PNdxbWnY';
$url = 'https://api.tosspayments.com/v1/payments/' . $paymentKey;
$data = ['orderId' => $order_no, 'amount' => $amount];
$credential = base64_encode($secretKey . ':');

$curlHandle = curl_init($url); //curl 로딩
curl_setopt_array($curlHandle, [
    CURLOPT_POST => TRUE, //post 전송 활성화
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
    CURLOPT_POSTFIELDS => json_encode($data) //curl에 post값 세팅
]);

$response = curl_exec($curlHandle);
$httpCode = curl_getinfo($curlHandle, CURLINFO_HTTP_CODE);
$isSuccess = $httpCode == 200;
$responseJson = json_decode($response);

$account_info = array(
    "accountNumber" => $responseJson->virtualAccount->accountNumber, //계좌번호  
    "customerName" => $responseJson->virtualAccount->customerName, //입금자 명
    "dueDate" => $responseJson->virtualAccount->dueDate, // 입금 기한
    "totalAmount" => $responseJson->totalAmount, // 금액
);

$method=$responseJson->method;
$status=$responseJson->status;
$date=date("Y-m-d H:i:s",time());
if($method=="카드")
{
    $bank=$responseJson->card->company;
    $name=$client_name;
}
else
{
    $bank=($responseJson->virtualAccount->bank)."|".$account_info['accountNumber'];
    $name=$account_info['customerName'];
}
//로그 처리
if($isSuccess)
{
    $SQL="INSERT INTO {$my_db}.tm_pay_log (order_type, order_no, id, name, bank , return_status, status_message, wdate, log_text)";
    $SQL.="VALUES ('{$method}', '{$order_no}', '{$client_id}', '{$name}', '{$bank}', '{$status}', '{$status_arr[$status]}', '{$date}', '{$log_text}');";
    $stmt=$pdo->prepare($SQL);
    $stmt->execute();
}

if($status == 'DONE')
{
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
        //로그 저장
        // save_log(
        //     "==============================================\r\n"
        //     ."생성 시간 : ".date("YmdHis")."\r\n"
        //     ."DB :".$SQL."\r\n"
        //     ."결과 데이터 :".json_encode($json, JSON_UNESCAPED_UNICODE)
        //     ."\r\n==============================================\r\n\r\n"
        // );
}
?>

<div class="lh18">
	<?
    $html="";
	if($pay_opt==2)
	{	
        //SMS
        // $SQL="SELECT mobile FROM {$my_db}.tm_member WHERE id='{$client_id}' LIMIT 1";
        // $stmt=$pdo->prepare($SQL);
        // $stmt->execute();
        
        // list($day, $hour)=explode("T",$account_info['dueDate']);
        // $day=$day." 까지";
        // if($rs=$stmt->fetch())
        // {
        //     $msg="계좌번호 :".$account_info['accountNumber']."입금자 :".$account_info['customerName']."\\n";
        //     $msg="입금기한 :".$day."금액 :".$account_info['totalAmount']."\\n";
        //     $msg="▣탱크옥션▣\\r\\n연결되었습니다.\\r\\n결제,상담 등은 카톡 https://bit.ly/38LJNqZ";
        //     $mobile=str_replace("-", "", $rs[mobile]);
        //     send_sms($mobile, $msg, $client_id);
        // }

        //Virtual_Info

        // $SQL="SELECT * FROM {$my_db}.tm_pay_log WHERE id='{$client_id}' LIMIT 1";
        // $stmt=$pdo->prepare($SQL);
        // $stmt->execute();
        // if($rs=$stmt->fetch())
        // {
        //     list($bank, $account_info)=explode("|",$rs['bank']);
        //     $virtual_info=array("bank"=>$bank, "account_info"=>$account_info, "name"=>$rs['name']);
        // }

		$html.="-<span class='f18 bold'> 결제요청이 <span class='red'>완료</span> 되었습니다.<br></span><br>";
	}
	elseif($pay_opt==1)
	{
		$html.= "<div class='center'><img src='/img/member/extend.png' alt='결제성공'></div>";
	}
    echo $html;
	?>

    <div class='center' style='padding:20px'><a href='/'><span class='btn_box_ss btn_tank radius_10' style='width:110px'>홈으로 가기</span></a></div>
</div>    

<? include($_SERVER["DOCUMENT_ROOT"]."/inc/footer.php");
?>