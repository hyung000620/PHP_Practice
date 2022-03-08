<?php
$page_code="9016";
$member_only = true;
$cpn_deny    = true;
include($_SERVER["DOCUMENT_ROOT"]."/inc/header.php");
include($_SERVER["DOCUMENT_ROOT"]."/member/Toss.php");

#카드, 가상계좌일 경우 실행
if($mode==1 || $mode==2)
{
  $SQS=getServerQueryString($_SERVER);
  $paymentKey = $_GET['paymentKey'];
  $order_no = $_GET['orderId'];
  $amount = $_GET['amount'];
  $tossURL = "https://api.tosspayments.com/v1/payments/{$paymentKey}";
  $data = ['orderId' => $order_no, 'amount' => $amount];
  #toss결제 내용
  //$tossData=tossData_vir($order_no,$amount,$paymentKey);
  $res=$toss->curl_post($tossURL, $data);
  $tossData=$res['resData'];
  $isSuccess=$res['resCode']==200;
  ###############################################################
  ### [공통]
  //$paymentKey=$tossData->paymentKey;
  //$orderId=$tossData->orderId;
  #결제주문명
  $orderName=$tossData->orderName;
  #결제수단(카드,가상계좌)
  $method=$tossData->method;
  #상태 (정상결제카드:DONE, 가상계좌신청: WAITING_FOR_DEPOSIT,가상계좌결제: DONE
  $status=$tossData->status;
  #에스크로
  $useEscrow=$tossData->useEscrow;
  #요청일
  $requestedAt=$tossData->requestedAt;
  #승인일
  $approvedAt=$tossData->approvedAt;
  #카드결제 제공정보 (없으면 null)
  $card=$tossData->card;
  #가상계좌 제공정보 (없으면 null)
  $virtualAccount=$tossData->virtualAccount;
  #총결제금액
  $totalAmount=$tossData->totalAmount;
  #취소할수 있는 금액
  $balanceAmount=$tossData->$balanceAmount;
  ###############################################################
  #결제정보확인 및 비교
  $SQL="SELECT * FROM {$my_db}.tm_pay_log WHERE order_no = {$order_no}";
  $stmt=$pdo->prepare($SQL);
  $stmt->execute();
  
  if($rs=$stmt->fetch())
  {
      $log=json_decode($rs['log_text']);
  }
  $pay_opt=$log->pay_opt;
  $smp=$log->smp;
  $pay_code=$log->pay_code;
  $amout=$log->amount;
}

#파일로그기록
//$jsData=json_encode($tossData, JSON_UNESCAPED_UNICODE);
//fileLog("",$jsData);

switch ($mode) 
{
    case  1  :
        {
    #카드 결제 > 결과 (결제완료)
    if($method=="카드" && !is_null($card))
    {
      #카드사코드(string)
      $company=$virtualAccount->card->company;
      #카드번호(string)
      $number=$virtualAccount->card->number;
      #할부개월수(integet)_
      $installmentPlanMonths=$virtualAccount->card->installmentPlanMonths;
      #무이자 할부 적용여부(boolean)
      $isInterestFree=$virtualAccount->card->isInterestFree;
      #카드사승인번호(string)
      $approveNo=$virtualAccount->card->approveNo;
      #카드사포인트사용(boolean)
      $useCardPoint=$virtualAccount->card->useCardPoint;
      #카드타입 (string > 신용,체크,기프트)
      $cardType=$virtualAccount->card->cardType;
      #카드소유자타입(string > 개인,법인)
      $ownerType=$virtualAccount->card->ownerType;
      #카드매출전표 조회 페이지주소(string)
      $receiptUrl=$virtualAccount->card->receiptUrl;
      #카드결제 매입 상태(string > READY:매입대기,REQUEST:매입요청,COMPLETED:매입완료,CANCEL_REQUESTED:매입취소요청,CANCELED:매입취소완료)
      $acquireStatus=$virtualAccount->card->acquireStatus;
      #db처리  
      $toss->dbPay($status, $smp, $pay_code, $order_no, $pay_opt, $client_id);
      
    }
}break;
case 2  :
    {
        ###가상계좌신청 > 결과 (결제대기)
        #toss ip만 허용(/inc/cfg) 
        // $chk=in_array($_SERVER["REMOTE_ADDR"],$tossPayIP);
        // if($chk==0)
        // {
            //   fileLog("Direct connection IP - VirtualAccount", "IP : {$_SERVER["REMOTE_ADDR"]}");
            //   alertHref("/","비정상 경로로 접근하셨습니다.");
            // } 
            
            #가상계좌 결제 신청
            //if($method=="가상계좌" && !@is_null($virtualAccount))
            if($method=="가상계좌" && !is_null($virtualAccount))
            {
                #발급된 계좌번호(string)
                $accountNumber=$responseJson->virtualAccount->accountNumber;
                #가상계좌타입(string > 일반,고정)
                $accountType=$responseJson->virtualAccount->accountType;
                #가상계좌 발급은행(string)
                $bank=$responseJson->virtualAccount->bank;
                #가상계좌 발급한 고객명(string)
                $customerName=$responseJson->virtualAccount->customerName;
                #입금기한(string)
                $dueDate=$responseJson->virtualAccount->dueDate;
                #가상계좌 만료여부(boolean)
                $expired=$responseJson->virtualAccount->expired;
                #정산상태(string > 미정산:INCOMPLETE,정산:COMPLETE
                $settlementStatus=$responseJson->virtualAccount->settlementStatus;
                #환불처리상태(string >  NONE:해당없음, FAILED:환불실패, PENDING:환불처리중, PARTIAL_FAILED:부분환불실패,COMPLETED:환불완료
                $refundStatus=$responseJson->virtualAccount->refundStatus;
                
                $log_bank=$bank."|".$accountNumber."|".$dueDate;                
                
                
            }
            
            if($isSuccess)
            {
                $SQL="UPDATE {$my_db}.tm_pay_log SET bank = '{$log_bank}' WHERE order_no= '{$order_no}' AND id = '{$client_id}'";
                $stmt=$pdo->prepare($SQL);
                $stmt->execute();  
            }
            
        } break; 
        
        case 9  :
            {
                //카드,가상계좌 > 오류
                $message = $_GET['message'];
                $code = $_GET['code'];
                alertBack($message);
                
            } break;
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