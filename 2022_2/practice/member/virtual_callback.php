<?php
include($_SERVER["DOCUMENT_ROOT"]."/inc/header.php");

$postData = file_get_contents('php://input');
$json = json_decode($postData);

$status=$json->status;
$order_no=$json->orderId; 
$orderName=$json->customerName;

save_log("vittual : ".json_encode($json, JSON_UNESCAPED_UNICODE));
if ($status == 'DONE') 
{
    $SQL="SELECT * FROM {$my_db}.tm_pay_log WHERE order_no = '{$order_no}'";
    $stmt=$pdo->prepare($SQL);
    $stmt->execute();
    $rs=$stmt->fetch();
    if($rs)
    {
        list($smp,$pay_opt,$pay_code)=explode("|",$rs[log_text]);
        trim($pay_code);
    }
    $SQL="UPDATE {$my_db}.tm_pay_log";
    $SQL.=" SET return_status = '{$status}',";
    $SQL.=" status_message = '{$status_arr[$status]}'";
    $SQL.=" WHERE order_no = '{$order_no}'";
    $stmt=$pdo->prepare($SQL);
    $stmt->execute();
    
    $client_id = $rs[id];
    
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
                $SQL="SELECT * FROM {$my_db}.tm_member WHERE id='{$client_id}'";
                $stmt=$pdo->prepare($SQL);
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
        
    //save_log($SQL);
    
}
elseif($staus == 'WAITING_FOR_DEPOSIT')
{
    $SQL="UPDATE {$my_db}.tm_pay_log";
    $SQL.=" SET return_status = '{$status}',";
    $SQL.=" status_message = '{$status_arr[$status]}'";
    $SQL.=" WHERE order_no = '{$orderId}'";
    $stmt=$pdo->prepare($SQL);
    $stmt->execute();
}

?>