<?php
include($_SERVER["DOCUMENT_ROOT"]."/inc/header.php");

$postData = file_get_contents('php://input');
$json = json_decode($postData);

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

$status=$json->status;
$orderId=$json->orderId; 
if ($status == 'DONE') 
{
    $SQL="UPDATE {$my_db}.tm_pay_log";
    $SQL.=" SET return_status = '{$status}',";
    $SQL.=" status_message = '{$status_arr[$status]}'";
    $SQL.=" WHERE order_no = '{$orderId}'";
    $stmt=$pdo->prepare($SQL);
    $stmt->execute();
    
    //로그 파일 생성 - 함수
    $dir = $_SERVER["DOCUMENT_ROOT"]."/member/log/tossLog";
    $fileName = date("Ymd").".log";
    $logfile = fopen($dir."/".$fileName,'a');
    fwrite($logfile,"==============================================\r\n");
    fwrite($logfile, "생성 시간 : ".date("YmdHis")."\r\n");
    fwrite($logfile, "결과 데이터 :".json_encode($json, JSON_UNESCAPED_UNICODE));
    fwrite($logfile,"\r\n==============================================\r\n");
    fwrite($logfile,"\r\n");
    fclose($logfile);

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
else
{
    $SQL="UPDATE {$my_db}.tm_pay_log";
    $SQL.=" SET return_status = '{$status}',";
    $SQL.=" status_message = '{$status_arr[$status]}'";
    $SQL.=" WHERE order_no = '{$orderId}'";
    $stmt=$pdo->prepare($SQL);
    $stmt->execute();
}

?>