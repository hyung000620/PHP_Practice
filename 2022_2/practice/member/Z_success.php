<?
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
$remoteip=$_SERVER['REMOTE_ADDR']; 
 
#결제 > 카드(1), 가상계좌(4), 계좌이체(3)
if($mode==1 || $mode==4)
{
  $paymentKey = $_GET['paymentKey'];
  $order_no = $_GET['orderId'];
  $amount = $_GET['amount'];
  $tossURL = "https://api.tosspayments.com/v1/payments/{$paymentKey}";
  $data = ['orderId' => $order_no, 'amount' => $amount];

  #QueryString > 파일로그기록
  $toss->getServerQueryString($_SERVER,"result");
  
  #toss결제 내용
  $res=$toss->curl_post($tossURL, $data);
  $tossData=$res['resData'];
  $code=$res['resCode'];
  
  #파일로그기록(카드,가상계좌,실패)
  $toss_data=json_encode($tossData, JSON_UNESCAPED_UNICODE);
  $mstr=($mode==1)? "카드" : "가상계좌";
  $toss->fileLog("[result] > {$mstr} > 1",json_encode($tossData,JSON_UNESCAPED_UNICODE));
  ###############################################################
  
  # [공통]
  $tosspaymentKey=$tossData['paymentKey'];
  $orderId=$tossData['orderId'];
  #결제주문명
  $orderName=$tossData['orderName'];
  #결제수단(카드,가상계좌)
  $method=$tossData['method'];
  #상태 (정상결제 > 입금완료:DONE, 입금대기: WAITING_FOR_DEPOSIT, 준비: READY, 취소: CANCELED, 중단: ABORTED, 부분취소: PARTIAL_CANCELED)
  $status=$tossData['status'];
  #에스크로
  $useEscrow=$tossData['useEscrow'];
  #요청일
  $requestedAt=$tossData['requestedAt'];
  //$requestedAt=date("Y.m.d H:i:s", strtotime($requestedAt_tmp));
  #승인일
  $approvedAt=$tossData['approvedAt'];
  //$approvedAt=date("Y.m.d H:i:s", strtotime($approvedAt_tmp));  
  #카드결제 제공정보 (없으면 null)
  $card=$tossData['card'];
  #가상계좌 제공정보 (없으면 null)
  $virtualAccount=$tossData['virtualAccount'];
  #현금영수증
  $cashReceipt=$tossData['cashReceipt'];
  #총결제금액
  $totalAmount=$tossData['totalAmount'];
  #취소할수 있는 금액
  $balanceAmount=$tossData['balanceAmount'];
  ###############################################################

  #smp, pay_code, pay_opt
  $SQL="SELECT * FROM {$my_db}.tm_pay_log WHERE order_no='{$order_no}' AND id='{$client_id}'";
  $stmt=$pdo->prepare($SQL);
  $stmt->execute();
  $rs=$stmt->fetch();
  $pay_opt=$rs['pay_opt'];
  $pay_code=$rs['pay_code'];
  $name=$rs['name'];
  $smp=$rs['smp'];
  $bank_code=$rs['bank'];
  $amount=$rs['pay_price'];
  $dc_rate=$rs['dc_rate'];

  #파일로그
  if($flogFlag==0){$toss->fileLog("[result] > log 2", $SQL);}   
}
else if($mode == 6)
{
  ## 카드 등록 요청 > 빌링키 발급
  $customerKey = $_GET['customerKey'];
  $authKey = $_GET['authKey'];
  $url="https://api.tosspayments.com/v1/billing/authorizations/{$authKey}";
  $data=['customerKey'=>$customerKey];
  $res=$toss->curl_post($url, $data);
  $tossData=$res['resData'];  
  $code=$res['resCode'];
}
switch ($mode) 
{
  case  1  :
  {
    #카드 결제 > 결과 (결제완료)
    if($code==200 && $method=="카드" && !is_null($card))
    {
      #카드사코드(string)
      $company=$tossData['card']['company'];
      #카드번호(string)
      $number=$tossData['card']['number'];
      #할부개월수(integet)_
      $installmentPlanMonths=$tossData['card']['installmentPlanMonths'];
      #무이자 할부 적용여부(boolean)
      $isInterestFree=$tossData['card']['isInterestFree'];
      #카드사승인번호(string)
      $approveNo=$tossData['card']['approveNo'];
      #카드사포인트사용(boolean)
      $useCardPoint=$tossData['card']['useCardPoint'];
      #카드타입 (string > 신용,체크,기프트)
      $cardType=$tossData['card']['cardType'];
      #카드소유자타입(string > 개인,법인)
      $ownerType=$tossData['card']['ownerType'];
      #카드매출전표 조회 페이지주소(string)
      $receiptUrl=$tossData['card']['receiptUrl'];
      #카드결제 매입 상태(string > READY:매입대기,REQUEST:매입요청,COMPLETED:매입완료,CANCEL_REQUESTED:매입취소요청,CANCELED:매입취소완료)
      $acquireStatus=$tossData['card']['acquireStatus'];
     
      #db > log  
      $USQL="UPDATE {$my_db}.tm_pay_log SET paymentkey='{$tosspaymentKey}', status='{$status}', secret='{$secret}', mobile='{$mobilePhone}', receipt_type='카드매출전표', receipt_url='{$receiptUrl}', wdate='{$requestedAt}', order_ip='{$remoteip}', result_log='{$toss_data}' WHERE order_no='{$orderId}' AND id='{$client_id}'";
      
      $stmt=$pdo->prepare($USQL);
      $stmt->execute();

      #파일로그
      if($flogFlag==0){$toss->fileLog("[result] 카드결제 > log 40", $USQL);}
      
      #db처리  
      $toss->dbPay($status, $smp, $pay_code, $order_no, $pay_opt, $client_id); 
     
      #html
      $html[]="<div class='center'><img src='/img/member/extend.png' alt='결제성공'></div>";
      $html[]="<div style='padding:10px 20px 20px 20px'>";
      $html[]=" <div style='color:#fff;font-size:14px;font-weight:bold;width:100%;text-align:center'>";
      $html[]="   <span>결제영수증 (매출전표) &gt;</span>";
      $html[]="   <span><a href='{$receiptUrl}' target='_blank'>새창으로 열기</a></span>";
      $html[]="</div>";
    } 
    else
    {
      #결제오류
      failPay($code,$tossData['message']);
    }    
  } break;
  case 6 : 
  {
    #자동결제 > 결과(결제완료)
    if($code==200)
    {
      $billingKey=$tossData['billingKey'];
      $customerKey=$tossData['customerKey'];
      $order_no=str_replace($client_id,'',$customerKey);

      $SQL="SELECT * FROM {$my_db}.tm_pay_log WHERE order_no = '{$order_no}' AND id='{$client_id}'";
      $stmt=$pdo->prepare($SQL);
      $stmt->execute();
      $rs=$stmt->fetch();
      $pay_opt=$rs['pay_opt'];
      $pay_code=$rs['pay_code'];
      $name=$rs['name'];
      $smp=$rs['smp'];
      $bank_code=$rs['bank'];
      $amount=$rs['pay_price'];
      $dc_rate=$rs['dc_rate'];

      $data=['customerKey'=>$customerKey, 'amount'=>$amount, 'orderId'=>$order_no];
      $url="https://api.tosspayments.com/v1/billing/{$billingKey}";
      $res=$toss->curl_post($url,$data);
      $tossData=$res['resData'];  
      $code=$res['resCode'];

      $status=$tossData['status'];
      $tosspaymentKey=$tossData['paymentKey'];
      $requestedAt=$tossData['requestedAt'];

      $USQL="UPDATE {$my_db}.tm_pay_log SET paymentkey='{$tosspaymentKey}', status='{$status}', mobile='{$mobilePhone}', receipt_type='카드매출전표', receipt_url='{$receiptUrl}', wdate='{$requestedAt}', order_ip='{$remoteip}', result_log='{$toss_data}' WHERE order_no='{$order_no}' AND id='{$client_id}'";
      $stmt=$pdo->prepare($USQL);
      $stmt->execute();

      $USQL="UPDATE {$my_db}.tm_member SET billing_key='{$billingKey}', billing_month='{$month}', pay_custom=6 WHERE id='{$client_id}'";
      $stmt=$pdo->prepare($USQL);
      $stmt->execute();

      #파일로그
      if($flogFlag==0){$toss->fileLog("[result] 자동결제 > log 40", $USQL);}
      
      #db처리  
      $toss->dbPay($status, $smp, $pay_code, $order_no, $pay_opt, $client_id); 
     
      #html
      $html[]="<div class='center'><img src='/img/member/extend.png' alt='결제성공'></div>";
      $html[]="<div style='padding:10px 20px 20px 20px'>";
      $html[]=" <div style='color:#fff;font-size:14px;font-weight:bold;width:100%;text-align:center'>";
      $html[]="   <span>결제영수증 (매출전표) &gt;</span>";
      $html[]="   <span><a href='{$receiptUrl}' target='_blank'>새창으로 열기</a></span>";
      $html[]="</div>";
    }
    else
    {
      failPay($code,$tossData['message']);
    }
  } break;
  case 4  :
  {
    ###가상계좌신청 > 결과 (결제대기)
    #가상계좌 제공정보 (없으면 null)
    if($code==200 && $tossData['method']=="가상계좌" && !is_null($virtualAccount))
    {
      #발급된 계좌번호(string)
      $accountNumber=$tossData['virtualAccount']['accountNumber'];
      #가상계좌타입(string > 일반,고정)
      $accountType=$tossData['virtualAccount']['accountType'];
      #가상계좌 발급은행(string)
      $accountBank=$tossData['virtualAccount']['bank'];
      #가상계좌 발급한 고객명(string)
      $customerName=$tossData['virtualAccount']['customerName'];
      #입금기한(string)
      $dueDate=$tossData['virtualAccount']['dueDate'];
      //$dueDate=date("Y.m.d H:i:s", strtotime($dueDate_tmp)); 
      #가상계좌 만료여부(boolean)
      $expired=$tossData['virtualAccount']['expired'];
      #정산상태(string > 미정산:INCOMPLETE,정산:COMPLETE
      $settlementStatus=$tossData['virtualAccount']['settlementStatus'];
      #환불처리상태(string >  NONE:해당없음, FAILED:환불실패, PENDING:환불처리중, PARTIAL_FAILED:부분환불실패,COMPLETED:환불완료
      $refundStatus=$tossData['virtualAccount']['refundStatus'];
      
      #영수증이 있다면
      $rec_type="";
      $rec_issueNumber="";
      $rec_receiptUrl="";
      $rec_amount="";
      $rec_taxFreeAmount="";
      if(!is_null($cashReceipt))
      {
        $rec_type=$tossData['cashReceipt']['type'];
        $rec_issueNumber=$tossData['cashReceipt']['issueNumber'];
        $rec_receiptUrl=$tossData['cashReceipt']['receiptUrl'];
        $rec_amount=$tossData['cashReceipt']['amount'];
        $rec_taxFreeAmount=$tossData['cashReceipt']['taxFreeAmount'];
      }
           
      ### pay_log > update
      $USQL="UPDATE {$my_db}.tm_pay_log SET paymentkey='{$tosspaymentKey}', status='{$status}', secret='{$secret}', mobile='{$mobilePhone}', receipt_type='{$rec_type}', receipt_url='{$rec_receiptUrl}', accountNumber='{$accountNumber}', accountType='{$accountType}', accountBank='{$accountBank}', customerName='{$customerName}', dueDate='{$dueDate}', wdate='{$requestedAt}', order_ip='{$remoteip}', result_log='{$toss_data}' WHERE order_no='{$orderId}' AND id='{$client_id}'";
      $stmt=$pdo->prepare($USQL);
      $stmt->execute();  
      #파일로그
      if($flogFlag==0){$toss->fileLog("[result] 가상계좌결제신청 > log 40", $USQL);}
      ### pay_log > update
      
      ### db > tm_pay_wait 
      $CSQL="SELECT * FROM {$my_db}.tm_pay_wait WHERE id='{$client_id}' AND order_no={$order_no} LIMIT 0,1";
      $stmt=$pdo->prepare($CSQL);
      $stmt->execute(); 
      $rrs=$stmt->fetch();
      #파일로그
      if($flogFlag==0){$toss->fileLog("[result] pay_wait select > log 41", $CSQL);}
      
      $rtdata=json_decode($virtualAccount,true);
      if($rrs)
      {
        //update
        $XSQL="UPDATE {$my_db}.tm_pay_wait SET id='{$client_id}', order_no='{$orderId}', pay_code={$pay_code}, paykind={$pay_opt}, srv_price={$amount},  pay_price={$amount}, apm='{$smp}', bankcode={$bank_code}, payname='{$customerName}', toss=1, wtime=NOW() WHERE id='{$client_id}' AND order_no='{$order_no}'";
        $stmt=$pdo->prepare($XSQL);
        $stmt->execute(); 
        
        #파일로그
        if($flogFlag==0){$toss->fileLog("[result] pay_wait update > log 42", $XSQL);}
      }
      else
      {
         //insert 
         $ISQL="INSERT INTO {$my_db}.tm_pay_wait SET id='{$client_id}', order_no='{$orderId}', pay_code={$pay_code}, paykind={$pay_opt}, srv_price={$amount}, pay_price={$amount}, apm='{$smp}', bankcode={$bank_code}, payname='{$customerName}',toss=1, wtime=NOW()";
         $stmt=$pdo->prepare($ISQL);
         $stmt->execute(); 
         
         #파일로그
         if($flogFlag==0){$toss->fileLog("[result] pay_wait insert > log 43", $ISQL);}
      }
      ### db > tm_pay_wait 
      #html
      $dueDate_=date("Y.m.d H:i:s", strtotime($dueDate));
      $html[]="- <span class='f18 bold'> 결제요청이 <span class='red'>완료</span> 되었습니다.<br></span><br>";
      $html[]="- <span class='f18 bold'> 가상계좌 정보는 아래와 같습니다.<br></span><br>";
      $html[]="<table class='tbl_list'>";
      $html[]=" <tr height='40'>";
      $html[]="   <th>은행</th>";
      $html[]="   <th>계좌번호</th>";
      $html[]="   <th>입금자명</th>";
      $html[]="   <th>입금기한</th>";
      $html[]="   <th>금액</th>";
      $html[]=" </tr>";
      $html[]=" <tr height='40'>";
      $html[]="   <td class='center'>{$accountBank}</td>";
      $html[]="   <td class='center'>{$accountNumber}</td>";
      $html[]="   <td class='center'>{$rs['name']}</td>";
      $html[]="   <td class='center bold orange'>{$dueDate_}</td>";
      $html[]="   <td class='center'>{$amount}</td>";
      $html[]=" </tr>";
      $html[]=" <tr height='50'>";
      $html[]="   <td colspan='5' style='font-size:17px;text-align:center'>가상 계좌번호는 결제 내역에서 한번 더 확인하실 수 있습니다.</td>";
      $html[]=" </tr>";
      $html[]="</table>";
      
      if($flogFlag==0){$toss->fileLog("[Tosspay_result]", "가상계좌 신청완료");}
    }
    else
    {
      #결제오류
      failPay($code,$tossData['message']);
    }
    
  } break; 
  case 10 :
  {
    #callback
    #가상계좌 Callback > toss ip만 허용(/inc/cfg) 
    $chk=in_array($_SERVER["REMOTE_ADDR"],$tossPayIP);
    if($chk==0)
    {
      $toss->fileLog("[result callback] > log 10", "IP : {$_SERVER['REMOTE_ADDR']}");
      alertHref("/","비정상 경로로 접근하셨습니다.");
    }

    #callback
    $json = file_get_contents("php://input");
    $tossData = json_decode($json);    
    $status=$tossData->status;
    $order_no=$tossData->orderId;  
    $orderName=$tossData->customerName;
    $tossDT=json_encode($tossData,JSON_UNESCAPED_UNICODE);
    
    #파일로그
    $toss->fileLog("[result callback] ", "가상계좌 입금처리 시작");
    $toss->fileLog("[result callback]  > log 11", json_encode($tossData,JSON_UNESCAPED_UNICODE));
    $SQL="SELECT * FROM {$my_db}.tm_pay_log WHERE order_no='{$order_no}'";
    $stmt=$pdo->prepare($SQL);
    $stmt->execute(); 
    $rs=$stmt->fetch();
    $pay_opt=$rs['pay_opt'];
    $pay_code=$rs['pay_code'];
    $smp=$rs['smp'];
    $amount=$rs['pay_price'];
    $paymentKey=$rs['paymentkey'];
    $id=$rs['id'];
    
    #파일로그
    if($flogFlag==0){$toss->fileLog("[result callback] > log 12", $SQL);}
     
    if ($status == 'DONE') 
    { 
      if($flogFlag==0){$toss->fileLog("{$status} {$smp} {$pay_code} {$order_no} {$pay_opt} {$id}", "test");}
      
      #Toss.php > dbPay
      $toss->dbPay($status, $smp, $pay_code, $order_no, $pay_opt, $id);

      #pay_log > update  ,/arr/arrPay.php
      $USQL="UPDATE {$my_db}.tm_pay_log SET status='{$status}' WHERE order_no='{$order_no}' AND id='{$id}'";
      $stmt=$pdo->prepare($USQL);
      $stmt->execute();
      
      $toss->fileLog("[result callback] ", "가상계좌 입금처리 완료");
    }
    else if($status == 'CANCELED')
    {
      //결제취소
      //payResult > mode 20 실행
       $orderId=$tossData->orderId;
       $toss->fileLog("[result callback] {$orderId} > log 20", "결제취소");
    }
    
    ###Callback > status update
    $USQL="UPDATE {$my_db}.tm_pay_log SET status='{$status}' WHERE order_no='{$order_no}' AND id='{$id}'";
    $stmt=$pdo->prepare($USQL);
    $stmt->execute();
    #파일로그
    if($flogFlag==0){$toss->fileLog("[result callback] status 업데이트> log 13", $USQL);}
    ###Callback > status update
  } break;
}

if($mode!=10)
{
  $bind_html=implode("",$html);
  echo $bind_html;
  include($_SERVER["DOCUMENT_ROOT"]."/inc/footer.php");
}
?>