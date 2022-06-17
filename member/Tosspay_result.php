<?
if($_GET['mode']!=10)
{
  $page_code="901600";    
  $member_only = true;
  $cpn_deny    = true;
}
include($_SERVER["DOCUMENT_ROOT"]."/inc/header.php");
include($_SERVER["DOCUMENT_ROOT"]."/member/Toss.php");

$mode=(int)$mode;
$html=array();
$remoteip=$_SERVER['REMOTE_ADDR']; 
 
#결제 > 카드(1), 계좌이체(3), 가상계좌(4)
if($mode==1 || $mode==3 || $mode==4)
{
  $paymentKey = $_GET['paymentKey'];
  $order_no = $_GET['orderId'];
  $amt = $_GET['amount'];
  $tossURL = "https://api.tosspayments.com/v1/payments/{$paymentKey}";
  $data = ['orderId' => $order_no, 'amount' => $amt];

  #QueryString 목적:: Toss->파일로그기록
  $toss->getServerQueryString($_SERVER,"result");
  
  #toss결제 내용
  $res=$toss->curl_post($tossURL, $data);
  $tossData=$res['resData'];
  $code=$res['resCode'];
  
  #파일로그기록(카드,가상계좌,실패)
  $toss_data=json_encode($tossData, JSON_UNESCAPED_UNICODE);
  $mstr=$pay_kind_arr[$mode];
  $toss->fileLog("[result] > {$mstr} > 1",json_encode($tossData,JSON_UNESCAPED_UNICODE));
  ###############################################################
  
  #[공통]
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
  #휴대폰
  $mobilePhone=$tossData['mobilePhone'];
  #가상계좌 제공정보 (없으면 null)
  $virtualAccount=$tossData['virtualAccount'];
  #계좌이체
  $transfer=$tossData['transfer'];
  #현금영수증
  $cashReceipt=$tossData['cashReceipt'];
  #secret
  $secret=$tossData['secret'];
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
  $goods = $rs['goods'];
  $first_goods = explode(',',$goods);
  $goods_count = count($first_goods)-1;

  #가격비교
  $cf_amount="가격비교 성공!";
  if($amt!=$amount)
  {
    alertHref("/member/pay.php","고객님의 주문/결제가 실패하였습니다.\r\n 다시 결제를 진행해 주세요.");
    $cf_amount="가격비교 실패!";
  }
  #파일로그
  if($flogFlag==0){$toss->fileLog("[result] {$cf_amount} > log 2", $SQL);}   
}

switch ($mode) 
{
  case  1  :
  {
    #카드 결제 > 결과 (결제완료)
    if($code==200 && $method=="카드" && !is_null($card) && $status=="DONE")
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
        //경매교육 > 수강신청 db 처리
      if($pay_code==102)
      {
        $SQL="UPDATE {$my_db}.tl_attend SET paymentkey='{$tosspaymentKey}', status='{$status}', mobile='{$mobilePhone}',wdate='{$requestedAt}' WHERE order_no={$orderId} AND id='{$client_id}'";
        $stmt=$pdo->prepare($SQL);
        $stmt->execute();
        
        if($flogFlag==0){$toss->fileLog("[result] 카드결제 > log 40-1", $SQL);}
        //오프라인 > 선착순 카운팅
        list($edu_code, $on_off, $price)=explode(":",$smp);
        if($on_off==0)
        {
            $SQL="UPDATE {$my_db}.tl_edu SET pay_people=(pay_people+1) WHERE edu_code = '{$edu_code}'";
            $stmt=$pdo->prepare($SQL);
            $stmt->execute();
        }
      }  
      #파일로그
      if($flogFlag==0){$toss->fileLog("[result] 카드결제 > log 40", $USQL);}
      
      #db처리  
      $toss->dbPay($status, $smp, $pay_code, $order_no, $pay_opt, $client_id); 
     
      #html
      $html[]="<div class='center'><img src='/img/member/extend.png' alt='결제성공'></div>";
      $html[]="<div style='padding:10px 20px 20px 20px'>";
      $html[]=" <div style='font-size:14px;font-weight:bold;width:100%;text-align:center'>";
      $html[]="   <span>결제영수증 (매출전표) &gt;</span>";
      $html[]="   <span><a href='{$receiptUrl}' target='_blank'>새창으로 열기</a></span>";
      $html[]=" </div>";
      $html[]="</div>";
      
      #SMS 문자발송(카드사에서 발송하므로 주석처리)
      $stmt=$pdo->prepare("SELECT mobile FROM {$my_db}.tm_member WHERE id='{$client_id}' LIMIT 1");
      $stmt->execute();
      $rs=$stmt->fetch();
      if($rs)
      {
        if($goods_count == 0) {$msg="▣탱크옥션▣\\r\\n카드 결제 완료.\\r\\n{$first_goods[0]}";} 
        else                  {$msg="▣탱크옥션▣\\r\\n카드 결제 완료.\\r\\n{$first_goods[0]} 외 {$goods_count}건";}
        $mobile=str_replace("-", "", $rs['mobile']);
        send_sms($mobile,$msg,$client_id);	
      }
    } 
    else
    {
      #결제오류
      $toss->failPay($code,$tossData['message']);
    }    
  } break;
  case 3  :
  {
    ##계좌이체
    if($code==200 && $method=="계좌이체" && !is_null($transfer) && $status=="DONE")
    {
      $toss->fileLog("[Tosspay_result]=========================================================", "계좌이체 시작");
      
      $bank=$tossData['transfer']['bank'];
      $settlementStatus=$tossData['transfer']['settlementStatus'];
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
      #db > log  
      $USQL="UPDATE {$my_db}.tm_pay_log SET paymentkey='{$tosspaymentKey}', status='{$status}', secret='{$secret}', mobile='{$mobilePhone}', receipt_type='{$rec_type}', receipt_url='{$rec_receiptUrl}', wdate='{$requestedAt}', order_ip='{$remoteip}', result_log='{$toss_data}' WHERE order_no='{$orderId}' AND id='{$client_id}'";
      $stmt=$pdo->prepare($USQL);
      $stmt->execute();
      
      #Toss > db update
      $toss->dbPay($status, $smp, $pay_code, $order_no, $pay_opt, $client_id); 
      
      #html
      $html[]="<div class='center'><img src='/img/member/extend.png' alt='결제성공'></div>";
      $html[]="<div class='center' style='padding:30px'><a href='/'><span class='btn_box_ss btn_tank radius_10' style='width:120px'>홈으로 이동</span></a></div>";

      #SMS 문자발송(카드사에서 발송하므로 주석처리)
      $stmt=$pdo->prepare("SELECT mobile FROM {$my_db}.tm_member WHERE id='{$client_id}' LIMIT 1");
      $stmt->execute();
      $rs=$stmt->fetch();
      if($rs)
      {
        if($goods_count == 0) {$msg="▣탱크옥션▣\\r\\n{$method} 결제 완료.\\r\\n{$first_goods[0]}";} 
        else                  {$msg="▣탱크옥션▣\\r\\n{$method} 결제 완료.\\r\\n{$first_goods[0]} 외 {$goods_count}건";}
        $mobile=str_replace("-", "", $rs['mobile']);
        send_sms($mobile,$msg,$client_id);	
      }   
      
      $toss->fileLog("[Tosspay_result]=========================================================", "계좌이체 {$settlementStatus}");     
    }
    else 
    {
      #결제오류
      $toss->failPay($code,$tossData['message']);
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
        //경매교육 > 수강신청 db 처리
        if($pay_code==102)
        {
            $SQL="UPDATE {$my_db}.tl_attend SET paymentkey='{$tosspaymentKey}', status='{$status}', mobile='{$mobilePhone}',wdate='{$requestedAt}',dueDate='{$dueDate}' WHERE order_no={$orderId} AND id='{$client_id}'";
            $stmt=$pdo->prepare($SQL);
            $stmt->execute();
            
            if($flogFlag==0){$toss->fileLog("[result] 가상계좌결제신청 > log 40-1", $SQL);}
            //오프라인 > 선착순 카운팅
            list($edu_code, $on_off, $price)=explode(":",$smp);
            if($on_off==0)
            {
                $SQL="UPDATE {$my_db}.tl_edu SET pay_people=(pay_people+1) WHERE edu_code = '{$edu_code}'";
                $stmt=$pdo->prepare($SQL);
                $stmt->execute();
            }
        }  
      #파일로그
      if($flogFlag==0){$toss->fileLog("[result] 가상계좌결제신청 > log 40", $USQL);}
      ### pay_log > update
      
      ### db > tm_pay_wait 
      /*
      $CSQL="SELECT * FROM {$my_db}.tm_pay_wait WHERE id='{$client_id}' AND order_no={$order_no} LIMIT 0,1";
      $stmt=$pdo->prepare($CSQL);
      $stmt->execute(); 
      $rrs=$stmt->fetch();
      #파일로그
      if($flogFlag==0){$toss->fileLog("[result] pay_wait select > log 41", $CSQL);}

      $bankCode=(int)$arr_payBank_b[$accountBank];
      $rtdata=json_decode($virtualAccount,true);
      if($rrs)
      {
        //update
        $XSQL="UPDATE {$my_db}.tm_pay_wait SET id='{$client_id}', order_no='{$orderId}', pay_code={$pay_code}, paykind={$pay_opt}, srv_price={$amount},  pay_price={$amount}, apm='{$smp}', bankcode={$bankCode}, payname='{$customerName}', toss=1, wtime=NOW() WHERE id='{$client_id}' AND order_no='{$order_no}'";
        $stmt=$pdo->prepare($XSQL);
        $stmt->execute(); 
        
        #파일로그
        if($flogFlag==0){$toss->fileLog("[result] pay_wait update > log 42", $XSQL);}
      }
      else
      {
         //insert 
         $ISQL="INSERT INTO {$my_db}.tm_pay_wait SET id='{$client_id}', order_no='{$orderId}', pay_code={$pay_code}, paykind={$pay_opt}, srv_price={$amount}, pay_price={$amount}, apm='{$smp}', bankcode={$bankCode}, payname='{$customerName}',toss=1, wtime=NOW()";
         $stmt=$pdo->prepare($ISQL);
         $stmt->execute(); 
         
         #파일로그
         if($flogFlag==0){$toss->fileLog("[result] pay_wait insert > log 43", $ISQL);}
      }
      */
      ### db > tm_pay_wait 
                    
      #html
      $amount_str=number_format($amount);
      $dueDate_=date("Y.m.d H:i:s", strtotime($dueDate));
      $html[]="- <span class='f18 bold'> 결제요청이 <span class='red'>완료</span> 되었습니다.<br></span><br>";
      $html[]="- <span class='f18 bold'> 가상계좌 정보는 아래와 같습니다.<br></span><br>";
      $html[]="<table class='tbl_list'>";
      $html[]=" <tr height='40'>";
      $html[]="   <th>은행</th>";
      $html[]="   <th>계좌번호</th>";
      $html[]="   <th>입금자명</th>";
      $html[]="   <th>입금기한</th>";
      $html[]="   <th>금액(원)</th>";
      $html[]=" </tr>";
      $html[]=" <tr height='40'>";
      $html[]="   <td class='center'>{$accountBank}</td>";
      $html[]="   <td class='center'>{$accountNumber}</td>";
      //$html[]="   <td class='center'>{$rs['name']}</td>";
      $html[]="   <td class='center'>{$customerName}</td>";
      $html[]="   <td class='center bold orange'>{$dueDate_}</td>";
      $html[]="   <td class='center'>{$amount_str}</td>";
      $html[]=" </tr>";
      $html[]=" <tr height='50'>";
      $html[]="   <td colspan='5' style='font-size:17px;text-align:center'>가상 계좌번호는 결제 내역에서 한번 더 확인하실 수 있습니다.</td>";
      $html[]=" </tr>";
      $html[]="</table>";
      
      #SMS 문자발송
      $stmt=$pdo->prepare("SELECT mobile FROM {$my_db}.tm_member WHERE id='{$client_id}' LIMIT 1");
      $stmt->execute();
      $rs=$stmt->fetch();
      if($rs)
      {
    	  //$msg="▣탱크옥션▣\\r\\n연결되었습니다.\\r\\n결제,상담 등은 카톡 https://bit.ly/38LJNqZ";
    	  //$msg="{$accountBank}-{$accountNumber} \\r\\n 입금요청";
    	  $amount=number_format($amount);
    	  $dueDate_=mb_substr($dueDate_,5);
    
    	  $msg="▣탱크옥션▣\\r\\n{$accountBank}☞{$accountNumber}\\r\\n☞금액{$amount}원\\r\\n◈{$dueDate_}까지\\r\\n◈입금후즉시이용";
    	  $mobile=str_replace("-", "", $rs['mobile']);
    	  send_sms($mobile,$msg,$client_id);	
      }
	
      if($flogFlag==0){$toss->fileLog("[Tosspay_result]=========================================================", "가상계좌 신청완료");}
    }
    else
    {
      #결제오류
      $toss->failPay($code,$tossData['message']);
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
    $toss->fileLog("[result callback]=========================================================", "가상계좌 입금처리 시작");
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
    $goods = $rs['goods'];
    $first_goods = explode(',',$goods);
    $goods_count = count($first_goods)-1;
    
    #파일로그
    if($flogFlag==0){$toss->fileLog("[result callback] > log 12", $SQL);}
     
    if ($status == 'DONE') 
    { 
      #Toss.php > dbPay
      $toss->dbPay($status, $smp, $pay_code, $order_no, $pay_opt, $id);

      #pay_log > update
      $USQL="UPDATE {$my_db}.tm_pay_log SET status='{$status}' WHERE order_no='{$order_no}' AND id='{$id}'";
      $stmt=$pdo->prepare($USQL);
      $stmt->execute();
      
      #SMS 문자(카드사에서 발송하므로 주석처리)
      $stmt=$pdo->prepare("SELECT mobile FROM {$my_db}.tm_member WHERE id='{$id}' LIMIT 1");
      $stmt->execute();
      $rs=$stmt->fetch();
      if($rs)
      {
        if($goods_count == 0) {$msg="▣탱크옥션▣\\r\\n가상계좌 결제 완료.\\r\\n{$first_goods[0]}";}
        else                  {$msg="▣탱크옥션▣\\r\\n가상계좌 결제 완료.\\r\\n{$first_goods[0]} 외 {$goods_count}건";}
        $mobile=str_replace("-", "", $rs['mobile']);
        send_sms($mobile,$msg,$client_id);	
      }
       //경매교육 > 수강신청 db 처리
       if($pay_code==102)
       {
         $SQL="UPDATE {$my_db}.tl_attend SET status='{$status}' WHERE order_no='{$order_no}' AND id='{$id}'";
         $stmt=$pdo->prepare($SQL);
         $stmt->execute();
       }  
      $toss->fileLog("[result callback]======================================================", "가상계좌 입금처리 완료");
    }
    else if($status == 'CANCELED')
    {
      #결제관리 > 취소신청
      //payResult > mode 20 실행
      $orderId=$tossData->orderId;
      $toss->fileLog("[result callback]===================================================", "{$orderId} 결제취소");

      ###Callback > status update
      $USQL="UPDATE {$my_db}.tm_pay_log SET status='{$status}' WHERE order_no='{$order_no}' AND id='{$id}'";
      $stmt=$pdo->prepare($USQL);
      $stmt->execute();
      #파일로그
      if($flogFlag==0){$toss->fileLog("[result callback] status 업데이트> log 13", $USQL);}
      #선착순 디카운팅
      $SQL="SELECT * FROM {$my_db}.tm_pay_log WHERE order_no='{$order_no}' AND id = '{$id}'";
      $stmt=$pdo->prepare($SQL);
      $stmt->execute();
      $rs=$stmt->fetch();
      $pay_code=$rs['pay_code'];
      list($edu_code,$on_off,$price)=explode(":",$rs['smp']);
      if($pay_code ==102)
      {
        $SQL="UPDATE {$my_db}.tl_attend SET status='{$status}' WHERE order_no='{$order_no}' AND id='{$client_id}'";
         $stmt=$pdo->prepare($SQL);
         $stmt->execute();
         if($on_off==0)
         {
            $SQL="UPDATE {$my_db}.tl_edu SET pay_people=(pay_people-1) WHERE edu_code = {$edu_code}";
            $stmt=$pdo->prepare($SQL);
            $stmt->execute();
         }
      }
    }
  } break;
}

if($mode!=10)
{
  $bind_html=implode("",$html);
  echo $bind_html;
  include($_SERVER["DOCUMENT_ROOT"]."/inc/footer.php");
}
?>