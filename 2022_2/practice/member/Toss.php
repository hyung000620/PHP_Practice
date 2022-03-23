<?
class Toss 
{   
    public function __construct($client, $secret)
    {   
        $this->clientKey=$client;
        $this->secretKey=$secret;
        $this->credential=base64_encode($this->secretKey . ':');
    }

    ##curl-post
    public function curl_post($url, $data) 
    {
        $curlHandle=curl_init($url);
        curl_setopt_array($curlHandle, 
        [
            CURLOPT_POST => TRUE, //post 전송 활성화
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPHEADER => [
                'Authorization: Basic ' . $this->credential,
                'Content-Type: application/json'
            ],
            CURLOPT_POSTFIELDS => json_encode($data,JSON_UNESCAPED_UNICODE) //curl에 post값 세팅
        ]);
        $response = curl_exec($curlHandle);
        $httpCode = curl_getinfo($curlHandle, CURLINFO_HTTP_CODE);
        $err = curl_error($curlHandle);
        curl_close($curlHandle);
        return ['resCode'=>$httpCode, 'resData'=>json_decode($response,true), 'err'=>$err];
    }
    ##curl-get
    public function curl_get($url)
    {
        $curl = curl_init();
        curl_setopt_array($curl,
        [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => [
            'Authorization: Basic ' . $this->credential
        ],
        ]);
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        return ['resData'=>json_decode($response,true), 'err'=>$err];
    }


    ##결제정보 일치여부 확인
    public function samePay($pay_code, $smp_arr, $amt, $dc_rate)
    {   
      global $pdo, $my_db , $flogFlag;
      
      ##########################################################################################
      ## 20220818 시점으로 지역결제할인(dc_rate)는 전국(99) 1년(12개월) 회원에게 적용되고 있음
      ## 할인정책 변경시 반드시 수정 필요함
      ## smp 배열 >  state:month:amount (20:6:97000,22:1:10000)
      ##########################################################################################
      
      if($pay_code==100)
      {
        #경매결제
      	$stmt=$pdo->prepare("SELECT * FROM {$my_db}.tc_price WHERE use_key=1 ORDER BY sort_num");
      	$stmt->execute();
      	while($rs=$stmt->fetch()){$pi[$rs[state]]=array("area" => $rs[area], "srv_area" => $rs[service_area], "price"=>array("1"=>$rs['price_01'],"3"=>$rs['price_03'],"6"=>$rs['price_06'],"12"=>$rs['price_12']));}	
      }
      elseif($pay_code==101)
      {
        #강의결제
      	$stmt=$pdo->prepare("SELECT * FROM {$my_db}.te_lecture WHERE 1");
      	$stmt->execute();
      	while($rs=$stmt->fetch()){$pi[$rs[lec_code]]=array("area" => $rs[course], "srv_area" => $rs[teacher], "price"=>$rs[price]);}
      }
      
      $totamt=0;
      foreach(explode(",",$smp_arr) as $v)
      {
        list($state,$month,$price)=explode(":",$v); 
        if($pay_code==100)
        {
          if($state==99 && $month==12 && $dc_rate>0)
          {
            $pr_tmp=(double)$pi[$state]['price'][$month];
            $price=$pr_tmp-($pr_tmp*($dc_rate/100));
          }
          else{$price=$pi[$state]['price'][$month];}
          $totamt=$totamt+$price;
        }
        else if($pay_code==101)
        {
          $pr_tmp=$pi[$state]['price'];
          $totamt=$totamt+$pr_tmp;      
        }
      }
      if($totamt!=$amt){$sucFlag=0; $sucMsg="가격비교 오류";}else{$sucFlag=1; $sucMsg="가격비교 OK";} 
      #파일로그
      if($flogFlag==0){$this->fileLog("[Toss] samePay {$sucFlag} > log 1", $sucMsg);}
      
      return ['resCode'=>$sucFlag, 'resData'=>$sucMsg];
    }
    ##DB
    public function dbPay($status, $smp, $pay_code, $order_no, $pay_opt, $client_id)
    {
      global $my_db, $pdo, $flogFlag;
      if($status == 'DONE')
      { 
        $USQL="UPDATE {$my_db}.tm_pay_log SET status='DONE' WHERE order_no='{$order_no}' AND id='{$client_id}'";
        $stmt=$pdo->prepare($USQL);
        $stmt->execute();
        //파일로그
        if($flogFlag==0){$this->fileLog("[Toss] > 경매(100) pay_log update > log 1",$USQL);}
        
        //DB 처리 - Begin
        $today=date("Y-m-d");
        $smp_arr=explode(",",$smp);
        $dc_flag=false;
        foreach($smp_arr as $v)
        {
            list($state,$month,$price)=explode(":",$v);
            switch ($pay_code)
            {
                case 100 :
                {
                    $memo="";
                    $TSQL="SELECT * FROM {$my_db}.tm_pay_result WHERE id='{$client_id}' AND pay_code='{$pay_code}' AND state='{$state}' LIMIT 0,1";
                    $stmt=$pdo->prepare($TSQL);
                    $stmt->execute();
                    $rs=$stmt->fetch();
                    if($flogFlag==0){$this->fileLog("[Toss] > 경매(100) 기간연장 > log 1",$TSQL);}
                    if($rs)
                    {
                        $SQL ="INSERT INTO {$my_db}.tm_pay_history(order_no,id,pay_code,paykind,months,paydate,paytime,payname,bankcode,money,state,sector,validity,tempdate,startdate,staff,memo,toss) ";
                        $SQL.="VALUES('{$rs[order_no]}','{$rs[id]}','{$rs[pay_code]}','{$rs[paykind]}','{$rs[months]}','{$rs[paydate]}','{$rs[paytime]}','{$rs[payname]}','{$rs[bankcode]}','{$rs[money]}','{$rs[state]}',";
                        $SQL.="'{$rs[sector]}','{$rs[validity]}','{$rs[tempdate]}','{$rs[startdate]}','{$rs[staff]}','{$rs[memo]}',1)";
                        $stmt=$pdo->prepare($SQL);
                        $stmt->execute();
                        if($flogFlag==0){$this->fileLog("[Toss] > 경매(100) 기간연장 History > log 2",$SQL);}
                        
                        $expire=($rs[validity] > $today) ? "DATE_ADD(validity,INTERVAL {$month} MONTH)" : "DATE_ADD(CURDATE(),INTERVAL {$month} MONTH)";
                        $SQL ="UPDATE {$my_db}.tm_pay_result SET order_no='{$order_no}',paykind='{$pay_opt}',months='{$month}',paydate=CURDATE(),paytime=CURTIME(),bankcode='',payname='{$payname}',";
                        $SQL.="money='{$price}',state='{$state}',validity={$expire},startdate=CURDATE(),staff='',memo='{$memo}' WHERE idx='{$rs[idx]}' AND id='{$client_id}' AND toss=1";
                        $stmt=$pdo->prepare($SQL);
                        $stmt->execute();
                        if($flogFlag==0){$this->fileLog("[Toss] > 경매(100) 기간연장 > log 3",$SQL);}
                    }
                    else	//신규결제
                    {
                        $SQL ="INSERT INTO {$my_db}.tm_pay_result(order_no,id,pay_code,paykind,months,paydate,paytime,money,state,validity,startdate,memo,toss) ";
                        $SQL.="VALUES('{$order_no}','{$client_id}','{$pay_code}','{$pay_opt}','{$month}',CURDATE(),CURTIME(),'{$price}','{$state}',DATE_ADD(CURDATE(),INTERVAL {$month} MONTH),CURDATE(),'{$memo}',1)";
                        $stmt=$pdo->prepare($SQL);
                        $stmt->execute();
                        if($flogFlag==0){$this->fileLog("[Toss] > 경매(100) 신규결제 > log 4",$SQL);}
                    }
                    if($state==99 && $month >= 12) $dc_flag=true;
                }   break;
                case 101 :
                {    
                    $TSQL="SELECT * FROM {$my_db}.tm_pay_result WHERE id='{$client_id}' AND pay_code='{$pay_code}' AND sector='{$state}' LIMIT 0,1";
                    $stmt=$pdo->prepare($TSQL);
                    $stmt->execute();                                                                             
                    $rs=$stmt->fetch();
                    if($flogFlag==0){$this->fileLog("[Toss] > 경매(101)  > 동영상 1",$TSQL);}
                    if($rs)
                    {
                        $SQL ="INSERT INTO {$my_db}.tm_pay_history(order_no,id,pay_code,paykind,months,paydate,paytime,payname,bankcode,money,state,sector,validity,tempdate,startdate,staff,memo,toss) ";
                        $SQL.="VALUES('{$rs[order_no]}','{$rs[id]}','{$rs[pay_code]}','{$rs[paykind]}','{$rs[months]}','{$rs[paydate]}','{$rs[paytime]}','{$rs[payname]}','{$rs[bankcode]}','{$rs[money]}','{$rs[state]}',";
                        $SQL.="'{$rs[sector]}','{$rs[validity]}','{$rs[tempdate]}','{$rs[startdate]}','{$rs[staff]}','{$rs[memo]}',1)";
                        $stmt=$pdo->prepare($SQL);
                        $stmt->execute();
                        if($flogFlag==0){$this->fileLog("[Toss] > 동영상강좌(101) 기간연장 History > log 1",$SQL);}
                            
                        $expire=($rs[validity] > $today) ? "DATE_ADD(validity,INTERVAL {$month} DAY)" : "DATE_ADD(CURDATE(),INTERVAL {$month} DAY)";
                        $SQL ="UPDATE {$my_db}.tm_pay_result SET order_no='{$order_no}',paykind='{$pay_opt}',months='{$month}',paydate=CURDATE(),paytime=CURTIME(),bankcode='',payname='{$payname}',";
                        $SQL.="money='{$price}',state='',sector='{$state}',validity={$expire},startdate=CURDATE(),staff='',memo='' WHERE idx='{$rs[idx]}' AND id='{$client_id}' AND toss=1";
                        $stmt=$pdo->prepare($SQL);
                        $stmt->execute();
                        if($flogFlag==0){$this->fileLog("[Toss] > 동영상강좌(101) 기간연장  > log 2",$SQL);}
                    }
                    else	//신규결제
                    {
                        $expire="DATE_ADD(CURDATE(),INTERVAL {$month} DAY)";
                        $SQL ="INSERT INTO {$my_db}.tm_pay_result(order_no,id,pay_code,paykind,months,paydate,paytime,money,sector,validity,startdate,memo,toss) ";
                        $SQL.="VALUES('{$order_no}','{$client_id}','{$pay_code}','{$pay_opt}','{$month}',CURDATE(),CURTIME(),'{$price}','{$state}',{$expire},CURDATE(),'',1)";
                        $stmt=$pdo->prepare($SQL);
                        $stmt->execute();
                        if($flogFlag==0){$this->fileLog("[Toss] > 동영상강좌(101) 신규결제 > log 3",$SQL);} 
                    }
                }   break;
            }			
        } 
        
        $SQL="SELECT * FROM {$my_db}.tm_pay_log WHERE order_no='{$order_no}' AND id='{$client_id}'";
        $stmt=$pdo->prepare($SQL);
    		$stmt->execute();
    		$res=$stmt->fetch();
    		$pay_price=$res['pay_price'];
    		$dc_rate=$res['dc_rate'];
    		
    		//파일로그
        if($flogFlag==0){$this->fileLog("[Toss] > 경매(100) pay_log select > log 2",$SQL);}
        
        if(!$dc_flag) $dc_rate=0;
        $ISQL="INSERT INTO {$my_db}.tm_pay_list(order_no,id,pay_price,dc_rate,wdate,toss) VALUES('{$order_no}','{$client_id}','{$pay_price}','{$dc_rate}',CURDATE(),1)";
        $stmt=$pdo->prepare($ISQL);
   		  $stmt->execute();

    		#파일로그
    		if($flogFlag==0){$this->fileLog("[Toss] > 결제최종이력 > log 5",$ISQL);} 
    		//DB 처리 - End 
      }
    }

    ##결제취소
    #data 
    public function cancelPayment($paymentKey, $data)
    {
        return Toss::curl_post("https://api.tosspayments.com/v1/payments/{$paymentKey}/cancel", $data);
    }

    ##현금영수증 발행
    #data
    public function issuePayment($data)
    {
        return Toss::curl_post("https://api.tosspayments.com/v1/cash-receipts", $data);
    }

    ##현금영수증 취소 (미발행결제 -> 현금영수증 발급 -> 결제 취소시 결제취소와 별개로 !!현금영수증도 취소!!)
    #data
    public function issueCancel($receiptKey, $data)
    {
        return Toss::curl_post("https://api.tosspayments.com/v1/cash-receipts/{$receiptKey}/cancel", $data);
    }

    ## 결제 > QueryString
    public function getServerQueryString($server,$info )
    {
      $rtn  = array();
      $tmp1 = "";
      $tmp2 = "";
      $val  = "";
      $tmp1 = explode( "&", $server["QUERY_STRING"] );
      foreach( $tmp1 AS $val )
      {
        $tmp2 = "";
        $val  = addslashes( htmlspecialchars( $val ) );
        $tmp2 = explode( "=", $val );
        $rtn["{$tmp2[0]}"] = $tmp2[1];
      }
      #파일로그
      $this->fileLog("[Toss] {$info} > SERVER QqueryString",$rtn);
      return;
    }

    ## 결제 > 파일로그
    public function fileLog($title,$data)
    {
      $dir=$_SERVER["DOCUMENT_ROOT"]."member/log/tossLog/";
      $fileName = date("Ymd").".log";
      $saveFile = $dir.$fileName;
      $time     = date( "Y.m.d H:i:s" );
      if(!is_dir($dir)){@mkdir( $dir, 0777 ); @chmod( $dir, 0744 );}
        
      $fp = fopen( "{$saveFile}", "a" );
      fwrite( $fp, "=======================================================================================\n" );
      fwrite( $fp, "[ {$time} ] - {$title}\n" );
      if(gettype($data)=="array")
      {
        foreach( $data AS $key => $val )
        {
          fwrite( $fp, "[ {$time} ]\t-\t[ {$key} ]\t:\t{$val}\n" );
          if(gettype($val=="array")){foreach($val AS $k => $v){fwrite( $fp, "[ {$time} ]\t-\t[ {$k} ]\t:\t{$v}\n" );}}
        } 
      }
      else {fwrite( $fp, "[ {$time} ]\t-\t{$data}\n" );}
      if (strpos(strtoupper($_SERVER["HTTP_USER_AGENT"]), "MSIE"))
      {
        fwrite( $fp, "[ {$time} ] - IE 10 이하\n" );
      }
      fwrite( $fp, "[ {$time} ] - {$title}\n" );
      fwrite( $fp, "=======================================================================================\n\n" );
      fclose( $fp );
      
      return;      
    }

    ## 결제 > 진행상태
    public function arr_payStatus($status)
    {
      switch ($status)
      {
        case "DONE" :               {$msg = "결제 완료됨";} break;
        case "READY" :              {$msg = "준비됨";} break;
        case "IN_PROGRESS" :        {$msg = "진행중";} break;
        case "WAITING_FOR_DEPOSIT": {$msg = "대기중";} break;
        case "CANCELED" :           {$msg = "결제가 취소됨";} break;
        case "ABORTED" :            {$msg = "카드 자동 결제 혹은 키인 결제를 할 때 결제 승인에 실패함";} break;
        case "PARTIAL_CANCELED" :   {$msg = "결제가 부분 취소됨";} break;
        case "EXPIRED" :            {$msg = "유효 시간(30분)이 지나 거래가 취소됨";} break;
        case "REFUND" :             {$msg = "결제 취소 요청됨";} break;
        default :                   {$msg = "알수없는 오류로 중지 되었습니다. 관리자에게 문의하여 주시기 바랍니다.";} break;
      }
      return $msg;     
    }
    ## 결제 > 취소 환불
    public function arr_payRefund($refund)
    {
      switch ($refund)
      {
        case 1 :  {$msg = "단순변심";} break;
        case 2 :  {$msg = "서비스 불만족";} break;
        case 3 :  {$msg = "광고와 다름";} break;
        case 4 :  {$msg = "타사이트를 이용중";} break;
        default : {$msg = "기타";} break;
      }
      return $msg;
    }
    ## 결제 > 은행(환불)
    public function arr_payBank($bank)
    {
      //$arr_payBank1=array("경남" => "경남은행","광주" => "광주은행","국민" => "KB국민은행","기업" => "IBK기업은행","농협" => "NH농협은행","대구" => "DGB대구은행","부산" => "부산은행","산업" => "KDB산업은행","새마을" => "새마을금고","산림" => "산림조합","수협" => "Sh수협은행","신한" => "신한은행","신협" => "신협","씨티" => "씨티은행","우리" => "우리은행","우체국" => "우체국예금보험","저축" => "저축은행중앙회","전북" => "전북은행","제주" => "제주은행","카카오" => "카카오뱅크", "케이" => "케이뱅크","토스" => "토스뱅크","하나" => "하나은행","SC제일" => "SC제일은행");
      $arr_payBank=array("10" => "국민","11" => "국민(동)","2" => "산업","3" => "기업","7" => "수협","18" => "농협","20" => "우리","23" => "SC제일","27" => "씨티","31" => "대구","32" => "부산","34" => "광주","35" => "제주","37" => "전북","39" => "경남","45" => "새마을","48" => "신협","50" => "저축","64" => "산림","71" => "우체국","81" => "하나","88" => "신한","89" => "케이","90" => "카카오","92" => "토스");
      return  $arr_payBank[$bank];
    }
    ## 결제 > 결제유형
    public function arr_payKind($paykind)
    {
       switch ($paykind)
      {
        case 1 :  {$kind = "카드";} break;
        case 2 :  {$kind = "통장입금";} break;
        case 3 :  {$kind = "실시간";} break;
        case 4 :  {$kind = "가상계좌";} break;
        default : {$kind = "";} break;
      }
      return $kind;     
    }

    ## 거래 정산 조회(하루 동안의 거래기록)
    public function search_transaction($date)
    {
        return Toss::curl_get("https://api.tosspayments.com/v1/transactions?startDate={$date}T00:00:00.0000&endDate={$date}T23:59:59.999");
    }

    ## 결제 조회
    public function search_payment($order_no)
    {
        return Toss::curl_get("https://api.tosspayments.com/v1/payments/orders/{$order_no}");
    }
}

try
{   
    //TODO: 추후 cfg파일 생성
    ##브라우저 체크
    if(preg_match("/MSIE*/",$_SERVER["HTTP_USER_AGENT"]))
    {
        throw new Exception('서비스를 정상적으로 이용하기 위해\nIE 11이상 버전으로 업데이트가 필요합니다.');
    }
    else
    {
       if($client_id=="daemon")
       {
         $toss=new Toss("test_ck_OEP59LybZ8BmOpKDwgJr6GYo7pRe","test_sk_OEP59LybZ8BmOwAwWakr6GYo7pRe");
       } else if ($client_id=="sweet123") {
         $toss=new Toss("test_ck_Z0RnYX2w532602zvA0g3NeyqApQE","test_sk_ODnyRpQWGrNG0mAaApe8Kwv1M9EN");
       }
       else
       {
        $toss=new Toss("test_ck_XLkKEypNArWaNyp1leA3lmeaxYG5","test_sk_7DLJOpm5QrlmRXDWwOL8PNdxbWnY");   
       }
       
    }
}
catch(Exception $e)
{
    $msg=$e->getMessage();
    alertBack($msg);
    exit;
}
?> 
