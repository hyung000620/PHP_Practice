<?
class Toss {
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
        curl_setopt_array($curlHandle, [
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
        $smp_arr=explode(",",$smp_arr);
        
        $total=0;
        foreach($smp_arr as $smp)
        {
          list($state,$month,$price)=explode(":",$smp);
          if($month<10){$month='0'.$month;}
          $total+=$price;
          
          if($pay_code == 100)
          {
              $SQL="SELECT * FROM {$my_db}.tc_price WHERE use_key=1 AND state = {$state} AND price_".$month."= {$price}";
          }
          elseif($pay_code == 101)
          {
              $SQL="SELECT * FROM {$my_db}.te_lecture WHERE lec_code = {$state} AND days = {$month} AND price = {$price}";
          }
          $stmt=$pdo->prepare($SQL);
          $stmt->execute();
          $rs=$stmt->fetchColumn();
          if($rs==0){alertBack('결제 정보가 일치하지 않습니다.');}
        }
        if($total!=$amt){alertBack('결제 정보가 일치하지 않습니다.');}
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
        $toss=new Toss("test_ck_XLkKEypNArWaNyp1leA3lmeaxYG5","test_sk_7DLJOpm5QrlmRXDWwOL8PNdxbWnY");
    }
}
catch(Exception $e)
{
    $msg=$e->getMessage();
    alertBack($msg);
    exit;
}
?>
