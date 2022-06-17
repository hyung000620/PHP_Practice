<?
$member_only=true;
include($_SERVER["DOCUMENT_ROOT"]."/inc/xmlHeader.php");

if(!preg_match("/".$_SERVER['HTTP_HOST']."/i",$_SERVER['HTTP_REFERER'])){exit;}

$mode=(int)$mode;
$state=(int)$state;
$dataArr=array();
$cdtnArr=array(); 

#page 
$dataSize=(int)$dataSize;
$pageSize=(int)$pageSize;
$pageNo=(int)$pageNo;
$start=($pageNo) ? (($pageNo-1)*$dataSize) : 0;

switch ($mode)
{
  case 1	:
	{
	  $cdtnArr[]="pay_opt=4";
	  $cdtnArr[]="paymentkey!=''";
	  if($state==1)
	  {
	    $cdtnArr[]="status='WAITING_FOR_DEPOSIT'";
	    $cdtnArr[]="DATE_FORMAT(dueDate, '%Y-%m-%d %H:%i:%s')>=NOW()";
	  }
	  else if($state==2)
	  {
	    $cdtnArr[]="status='WAITING_FOR_DEPOSIT'";
	    $cdtnArr[]="DATE_FORMAT(dueDate, '%Y-%m-%d %H:%i:%s')<NOW()";
	  }
	  else if($state==3)
	  {
	    $cdtnArr[]="status='DONE'";
	  }
	  #�˻�
	  if($user_name){$cdtnArr[]="name='{$user_name}'";}
	  if($user_id){$cdtnArr[]="id='{$user_id}'";}
	  $cdtn=($cdtnArr) ? implode(" AND ",$cdtnArr) : "1";
	  
	  $stmt=$pdo->prepare("SELECT COUNT(*) FROM db_tank.tm_pay_log WHERE {$cdtn}");
    $stmt->execute();        
    $rowCnt=$stmt->fetchColumn();
	  
    $SQL="SELECT * FROM db_tank.tm_pay_log WHERE {$cdtn} ORDER BY dueDate DESC LIMIT {$start}, {$dataSize}";
    $stmt=$pdo->prepare($SQL);
    $stmt->execute(); 
    
    $sq=0;
    while($rs=$stmt->fetch())
    {
      $no=$rowCnt-$sq;
      $duedate=date("Y.m.d H:i:s", strtotime($rs['dueDate'])); 
      $dataArr["item"][]=
      [
        "no"=>$no,
        "id"=>$rs['id'],
        "name"=>$rs['name'],
        "orderId"=>$rs['order_no'], 
        "goods"=>$rs['goods'],
        "srv_price"=>number_format($rs['srv_price']),
        "pay_price"=>number_format($rs['pay_price']),
        "paybank"=>$rs['accountBank'],
        "payaccount"=>$rs['accountNumber'],
        "payname"=>$rs['customerName'],
        "duedate"=>$duedate,
        "wdate"=>$rs['wdate']
      ];
      $sq++;
    }  
  } break;
  
  case 2 :
    {
        $cdtnArr[]="paymentkey!=''";
        if($state==1)
        {
            $cdtnArr[]="status='WAITING_FOR_DEPOSIT'";
            $cdtnArr[]="DATE_FORMAT(dueDate, '%Y-%m-%d %H:%i:%s')>=NOW()";
        }
        else if($state==2)
        {
            $cdtnArr[]="status='WAITING_FOR_DEPOSIT'";
            $cdtnArr[]="DATE_FORMAT(dueDate, '%Y-%m-%d %H:%i:%s')<NOW()";
        }
        else if($state==3)
        {
            $cdtnArr[]="status='DONE'";
        }
        else if($state==4)
        {
            $cdtnArr[]="status='CANCELED'";
        }
        else if($state==5)
        {
            $cdtnArr[]="status='SPARE'";
        }
        $edu_code="";
        if($estate!=0)
        {
            $cdtnArr[]="SUBSTRING_INDEX(smp,':',1)='{$estate}'";
            $edu_code=$estate;
        }
        if($ostate!=2)
        {
            $cdtnArr[]="SUBSTRING_INDEX(SUBSTRING_INDEX(smp,':',2),':',-1)='{$ostate}'";
        } 
        if($user_name){$cdtnArr[]="name='{$user_name}'";}
        if($user_id){$cdtnArr[]="id='{$user_id}'";}
        $cdtn=($cdtnArr) ? implode(" AND ",$cdtnArr) : "1";

        $stmt=$pdo->prepare("SELECT COUNT(*) FROM db_tank.tl_attend WHERE {$cdtn}");
        $stmt->execute();        
        $rowCnt=$stmt->fetchColumn();

        $SQL="SELECT * FROM db_tank.tl_attend WHERE {$cdtn} ORDER BY wdate DESC LIMIT {$start}, {$dataSize}";
        $stmt=$pdo->prepare($SQL);
        $stmt->execute(); 

        $sq=0;
        while($rs=$stmt->fetch())
        {
            $no=$rowCnt-$sq;
            $duedate=date("Y.m.d H:i:s", strtotime($rs['dueDate'])); 
            $dataArr["item"][]=
            [
                "no"=>$no,
                "id"=>$rs['id'],
                "idx"=>$rs['idx'],
                "name"=>$rs['name'],
                "orderId"=>$rs['order_no'], 
                "goods"=>$rs['goods'],
                "ptnr"=>$rs['sangho'],
                "pay_price"=>number_format($rs['pay_price']),
                "paybank"=>$rs['accountBank'],
                "payaccount"=>$rs['accountNumber'],
                "duedate"=>$duedate,
                "wdate"=>$rs['wdate'],
                "pay_opt"=>$rs['pay_opt'],
                "status"=>$rs['status'],
                "smp"=>$rs['smp']
            ];
            $sq++;
        }
        
        if($edu_code!="")
        {
          $SQL="SELECT * FROM db_tank.tl_edu WHERE edu_code='{$edu_code}'";
          $stmt=$pdo->prepare($SQL);
          $stmt->execute();
          $rs=$stmt->fetch();
          $dataArr['pay_people']=$rs['pay_people'];
          $dataArr['edu_people']=$rs['edu_people'];
        }
    } break;
}  
$dataArr["rowCnt"]=$rowCnt;
$result=json_encode($dataArr,JSON_UNESCAPED_UNICODE);
echo($result);	
?>