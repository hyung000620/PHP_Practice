<?php
$page_code="9016";
include($_SERVER["DOCUMENT_ROOT"]."/inc/header.php");
include($_SERVER["DOCUMENT_ROOT"]."/member/Toss.php");

$smp= $_SESSION['smp'];
$pay_opt= $_SESSION['pay_opt'];
$pay_code= $_SESSION['pay_code'];

$paymentKey = $_GET['paymentKey'];
$order_no = $_GET['orderId'];
$amount = $_GET['amount'];

$log_text=$smp."|".$pay_opt."|".$pay_code;
$url = 'https://api.tosspayments.com/v1/payments/' . $paymentKey;
$data = ['orderId' => $order_no, 'amount' => $amount];

#toss
$res=$toss->curl_post($url, $data);
$isSuccess=$res['resCode']==200;
$responseJson=$res['resData'];

$account_info = array(
    "accountNumber" => $responseJson->virtualAccount->accountNumber, //계좌번호  
    "customerName" => $responseJson->virtualAccount->customerName, //입금자 명
    "dueDate" => $responseJson->virtualAccount->dueDate, // 입금 기한
    "totalAmount" => $responseJson->totalAmount, // 금액
);

$endDate=date("Y-m-d", strtotime($account_info['dueDate']));

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
    $bank=($responseJson->virtualAccount->bank)."|".$account_info['accountNumber']."|".$endDate;
    $name=$account_info['customerName'];
    $date.="|".$endDate;
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
}
?>

<div class="lh18">
	<?
    $html="";
	if($pay_opt==4)
	{	

        $SQL="SELECT * FROM {$my_db}.tm_pay_log WHERE id='{$client_id}' AND order_no = '{$order_no}'";
        $stmt=$pdo->prepare($SQL);
        $stmt->execute();
        if($rs=$stmt->fetch())
        {
            list($bank,$info,$endDate)=explode("|",$rs['bank']);
            $endDate=trim($endDate);
            $html.="-<span class='f18 bold'> 결제요청이 <span class='red'>완료</span> 되었습니다.<br></span><br>";
            $html.="-<span class='f18 bold'> 가상계좌 정보는 아래와 같습니다.<br></span><br>";
            $html.="<table class='tbl_grid'>";
            $html.="<tr height='40'>";
            $html.="<th>은행</th>";
            $html.="<th>계좌번호</th>";
            $html.="<th>입금자명</th>";
            $html.="<th>입금기한</th>";
            $html.="<th>금액</th>";
            $html.="</tr>";
            $html.="<tr height='40'>";
            $html.="<td class='center'>{$bank}</td>";
            $html.="<td class='center'>{$info}</td>";
            $html.="<td class='center'>{$rs['name']}</td>";
            $html.="<td class='center bold orange'>{$endDate}</td>";
            $html.="<td class='center'>{$amount}</td>";
            $html.="</tr>";
            $html.="</table>";
        }
	}
	elseif($pay_opt==1)
	{
		$html.= "<div class='center'><img src='/img/member/extend.png' alt='결제성공'></div>";
	}
    echo $html;
	?>

    <div class='center' style='padding:20px'><a href='/'><span class='btn_box_ss btn_tank radius_10' style='width:110px'>홈으로 가기</span></a></div>
</div>    

<? include($_SERVER["DOCUMENT_ROOT"]."/inc/footer.php");?>