<?
class Toss 
{
    public function __construct($client, $secret)
    {   
        $this->clientKey=$client;
        $this->secretKey=$secret;
        $this->credential=base64_encode($this->secretKey . ':');
    }

    ##curl
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
            CURLOPT_POSTFIELDS => json_encode($data) //curl에 post값 세팅
        ]);
        $response = curl_exec($curlHandle);
        $httpCode = curl_getinfo($curlHandle, CURLINFO_HTTP_CODE);
        curl_close($curlHandle);
        return ['resCode'=>$httpCode, 'resData'=>json_decode($response)];
    }

    ##결제정보 일치여부 확인
    public function samePay($pay_code, $smp_arr, $amt)
    {   
      global $pdo, $my_db;
      
      $total=0;
      $smp_arr=explode(",",$smp_arr);       
      foreach($smp_arr as $smp)
      {
        list($state,$month,$price)=explode(":",$smp);
        $month=sprintf("%02d",$month);
        $total+=$price;
        if($pay_code == 100)     {$SQL="SELECT * FROM {$my_db}.tc_price WHERE use_key=1 AND state = {$state} AND price_{$month}= {$price}";}
        else if($pay_code == 101){$SQL="SELECT * FROM {$my_db}.te_lecture WHERE lec_code = {$state} AND days = {$month} AND price = {$price}";}
        $stmt=$pdo->prepare($SQL);
        $stmt->execute();
        $rs=$stmt->fetchColumn();
        if(!$rs)
        {
          //alertBack('결제 정보가 일치하지 않습니다.');
          fileLog("[Toss] >[{$pay_code}] 정보일치오류-1",$SQL); 
          //SMS발송추가 필요함
          break;
        }
      }
      if($total!=$amt)
      {
        //alertBack('결제 정보가 일치하지 않습니다.');
        fileLog("[Toss] >[{$pay_code}] 정보일치오류-2",$smp_arr); 
      }
    }
    ##DB
    public function dbPay($status, $smp, $pay_code, $order_no, $pay_opt, $client_id)
    {
      global $my_db, $pdo;
      if($status == 'DONE')
      { 
        $SQL="UPDATE {$my_db}.tm_pay_log SET return_status = 'DONE' WHERE order_no= {$order_no} AND id = '{$client_id}'";
        $stmt=$pdo->prepare($SQL);
        $stmt->execute();
          
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
                    /*
                     $partner_pm=0; <javacript>alert(1);</javascript>
                     $sql="SELECT * FROM {$my_db}.tm_member WHERE id='{$client_id}'";
                     $stmt=$pdo->prepare($sql);
                     $stmt->execute();
                     $rs=$stmt->fetch();
                     if($rs){if($rs[ptnr_code]==20 && $rs[partner_pm]<=0){$partner_pm=1;}}
                    */ 

                    $stmt=$pdo->prepare("SELECT * FROM {$my_db}.tm_pay_result WHERE id='{$client_id}' AND pay_code='{$pay_code}' AND state='{$state}' LIMIT 0,1");
                    $stmt->execute();
                    $rs=$stmt->fetch();
                    if($rs)
                    {
                        $SQL ="INSERT INTO {$my_db}.tm_pay_history(order_no,id,pay_code,paykind,months,paydate,paytime,payname,bankcode,money,state,sector,validity,tempdate,startdate,staff,memo,toss) ";
                        $SQL.="VALUES('{$rs[order_no]}','{$rs[id]}','{$rs[pay_code]}','{$rs[paykind]}','{$rs[months]}','{$rs[paydate]}','{$rs[paytime]}','{$rs[payname]}','{$rs[bankcode]}','{$rs[money]}','{$rs[state]}',";
                        $SQL.="'{$rs[sector]}','{$rs[validity]}','{$rs[tempdate]}','{$rs[startdate]}','{$rs[staff]}','{$rs[memo]}',1)";
                        $stmt=$pdo->prepare($SQL);
                        $stmt->execute();
                        fileLog("[Toss] > 경매(100) 기간연장 History > log 1",$SQL); 
                        
                        $expire=($rs[validity] > $today) ? "DATE_ADD(validity,INTERVAL {$month} MONTH)" : "DATE_ADD(CURDATE(),INTERVAL {$month} MONTH)";
                        $SQL ="UPDATE {$my_db}.tm_pay_result SET order_no='{$order_no}',paykind='{$pay_opt}',months='{$month}',paydate=CURDATE(),paytime=CURTIME(),bankcode='',payname='{$payname}',";
                        $SQL.="money='{$price}',state='{$state}',validity={$expire},startdate=CURDATE(),staff='',memo='{$memo}' WHERE idx='{$rs[idx]}' AND id='{$client_id}' AND toss=1";
                        $stmt=$pdo->prepare($SQL);
                        $stmt->execute();
                        fileLog("[Toss] > 경매(100) 기간연장 > log 2",$SQL); 
                    }
                    else	//신규결제
                    {
                        $SQL ="INSERT INTO {$my_db}.tm_pay_result(order_no,id,pay_code,paykind,months,paydate,paytime,money,state,validity,startdate,memo,toss) ";
                        $SQL.="VALUES('{$order_no}','{$client_id}','{$pay_code}','{$pay_opt}','{$month}',CURDATE(),CURTIME(),'{$price}','{$state}',DATE_ADD(CURDATE(),INTERVAL {$month} MONTH),CURDATE(),'{$memo}',1)";
                        $stmt=$pdo->prepare($SQL);
                        $stmt->execute();
                        fileLog("[Toss] > 경매(100) 신규결제 > log 3",$SQL); 
                    }
                    if($state==99 && $month >= 12) $dc_flag=true;
                }   break;
                case 101 :
                {    
                    $stmt=$pdo->prepare("SELECT * FROM {$my_db}.tm_pay_result WHERE id='{$client_id}' AND pay_code='{$pay_code}' AND sector='{$state}' LIMIT 0,1");
                    $stmt->execute();
                    $rs=$stmt->fetch();
                    if($rs)
                    {
                        $SQL ="INSERT INTO {$my_db}.tm_pay_history(order_no,id,pay_code,paykind,months,paydate,paytime,payname,bankcode,money,state,sector,validity,tempdate,startdate,staff,memo,1) ";
                        $SQL.="VALUES('{$rs[order_no]}','{$rs[id]}','{$rs[pay_code]}','{$rs[paykind]}','{$rs[months]}','{$rs[paydate]}','{$rs[paytime]}','{$rs[payname]}','{$rs[bankcode]}','{$rs[money]}','{$rs[state]}',";
                        $SQL.="'{$rs[sector]}','{$rs[validity]}','{$rs[tempdate]}','{$rs[startdate]}','{$rs[staff]}','{$rs[memo]}',1)";
                        $stmt=$pdo->prepare($SQL);
                        $stmt->execute();
                        fileLog("[Toss] > 동영상강좌(101) 기간연장 History > log 1",$SQL);  
                            
                        $expire=($rs[validity] > $today) ? "DATE_ADD(validity,INTERVAL {$month} DAY)" : "DATE_ADD(CURDATE(),INTERVAL {$month} DAY)";
                        $SQL ="UPDATE {$my_db}.tm_pay_result SET order_no='{$order_no}',paykind='{$pay_opt}',months='{$month}',paydate=CURDATE(),paytime=CURTIME(),bankcode='',payname='{$payname}',";
                        $SQL.="money='{$price}',state='',sector='{$state}',validity={$expire},startdate=CURDATE(),staff='',memo='' WHERE idx='{$rs[idx]}' AND id='{$client_id}' AND toss=1";
                        $stmt=$pdo->prepare($SQL);
                        $stmt->execute();
                        fileLog("[Toss] > 동영상강좌(101) 기간연장  > log 2",$SQL); 
                    }
                    else	//신규결제
                    {
                        $expire="DATE_ADD(CURDATE(),INTERVAL {$month} DAY)";
                        $SQL ="INSERT INTO {$my_db}.tm_pay_result(order_no,id,pay_code,paykind,months,paydate,paytime,money,sector,validity,startdate,memo,toss) ";
                        $SQL.="VALUES('{$order_no}','{$client_id}','{$pay_code}','{$pay_opt}','{$month}',CURDATE(),CURTIME(),'{$price}','{$state}',{$expire},CURDATE(),'',1)";
                        $stmt=$pdo->prepare($SQL);
                        $stmt->execute();
                        fileLog("[Toss] > 동영상강좌(101) 신규결제 > log 3",$SQL); 
                    }
                }   break;
            }			
        }
        if(!$dc_flag) $dc_rate=0;
        $ISQL="INSERT INTO {$my_db}.tm_pay_list(order_no,id,pay_price,dc_rate,wdate) VALUES('{$order_no}','{$client_id}','{$amt}','{$dc_rate}',CURDATE())";
        $stmt=$pdo->prepare($ISQL);
    		$stmt->execute();
    		#파일로그
    		fileLog("[Toss] > 결제최종이력 > log 5",$ISQL); 
    		//DB 처리 - End 
      }
    }

    ##결제취소
    public function cancelPayment($paymentKey)
    {
        return self::curl_post("https://api.tosspayments.com/v1/payments/".$paymentKey."/cancel",['cancelReason' => "고객이 취소를 원함"]);
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
