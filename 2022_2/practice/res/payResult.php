<?
$member_only=true;
include($_SERVER["DOCUMENT_ROOT"]."/inc/xmlHeader.php");
include($_SERVER["DOCUMENT_ROOT"]."/member/Toss.php");

$mode=(int)$mode;
$dataArr=array();

#page 
$page_scale=10;
$start=($start) ? $start : 0;
$list_scale=($list_scale) ? $list_scale : 20;

switch ($mode)
{
  case 1	:
	{
	  $today=date("Y-m-d");
	  
	  ##결제완료 리스트
	  #경매정보
    $stmt=$pdo->prepare("SELECT state,area FROM {$my_db}.tc_price");
    $stmt->execute();
    while($rs=$stmt->fetch()){$auctArr[$rs[state]]=$rs[area];}

    #동영상 강좌 구분
    $stmt=$pdo->prepare("SELECT lec_code,course FROM {$my_db}.te_lecture WHERE ctgr BETWEEN 20 AND 33");
    $stmt->execute();
    while($rs=$stmt->fetch()){$lect_arr[$rs[lec_code]]=$rs[course];}

    #영수증 배열
    $pay_arr=array();
    $CSQL="SELECT * FROM {$my_db}.tm_pay_log WHERE id='{$client_id}' AND receipt_url!=''";
    $stmt=$pdo->prepare($CSQL);
    $stmt->execute();
    while($rs=$stmt->fetch())
    {
      $pay_arr[$rs['order_no']]="{$rs['receipt_type']}||{$rs['receipt_url']}||{$rs['order_no']}||{$res['customerName']}||{$res['bank']}||{$res['accountNumber']}";
    }
    
    #가상계좌 정보
    //$CSQL="SELECT * FROM {$my_db}.tm_pay_log WHERE id='{$client_id}' AND receipt!=''";
    
    
    #파일로그
    //if($flogFlag==0){$toss->fileLog("[payResult  aa", json_encode($pay_arr));}
    
    ##rowspan 적용
    //은행정보-관리자용		
    $adate=explode("-",$search_date);    
    $condition="M.id=P.id AND P.id=L.id AND P.order_no=L.order_no AND L.id='{$client_id}'";
    $SQL ="SELECT COUNT(order_no) AS cnt,order_no,pay_code,paykind,pay_price,bankcode,payname,point,dc_rate FROM (";
    $SQL.="SELECT L.order_no,pay_code,pay_price,paykind,bankcode,payname,point,L.dc_rate FROM {$my_db}.tm_pay_result  P, {$my_db}.tm_pay_list L, {$my_db}.tm_member M WHERE {$condition} ";
    $SQL.="UNION ALL ";
    $SQL.="SELECT L.order_no,pay_code,pay_price,paykind,bankcode,payname,point,L.dc_rate FROM {$my_db}.tm_pay_history P, {$my_db}.tm_pay_list L, {$my_db}.tm_member M WHERE {$condition} ";
    $SQL.=") T GROUP BY order_no ORDER BY order_no DESC";
    $stmt=$pdo->prepare($SQL);
    $stmt->execute();
    $p_arr=array();
    while($rs=$stmt->fetch())
    {
    	if($rs[paykind]==2){$paykind=str_replace("은행","",$bank_arr[$rs[bankcode]][name])."-".$rs[payname];}
    	elseif($rs[paykind]==1){$paykind="카드";}
    	elseif($rs[paykind]==3){$paykind="이체";}
    	elseif($rs[paykind]==4){$paykind="가상계좌";}
    	else{$paykind=$pay_kind_arr[$rs[paykind]];}
    	$p_arr[$rs[order_no]]=array("rowspan" => $rs[cnt], "point" => $rs[point], "paykind" => $paykind, "amt" => $rs[pay_price], "payname" => $rs[payname], "dc_rate" => $rs[dc_rate]);
    	$row_no+=$rs[cnt];
    	$amt_sum+=$rs[pay_price];
    	${"sum_pay_".$rs[pay_code]."_".$rs[paykind]}=${"sum_pay_".$rs[pay_code]."_".$rs[paykind]}+$rs[pay_price];
    }
    
    #파일로그
    #if($flogFlag==0){$toss->fileLog("[payResult  bb", json_encode($p_arr, JSON_UNESCAPED_UNICODE));}
    ##rowspan 적용
    
    #$oneyear=strtotime("-1 year",time());
    #$condition="P.order_no=L.order_no AND P.id='{$client_id}' AND P.validity>={$today}";
    $condition="P.order_no=L.order_no AND P.id='{$client_id}'";
    $SQL ="(SELECT P.order_no,P.idx,P.id,pay_code,state,sector,months,money,paykind,paydate,validity,point,pay_price,bankcode,payname,startdate,vp_sdate,vp_edate,sp_sdate,sp_edate,memo,L.toss,1 AS tbl_key FROM {$my_db}.tm_pay_result  P , {$my_db}.tm_pay_list L WHERE {$condition}) ";
    $SQL.="UNION ALL ";
    $SQL.="(SELECT P.order_no,P.idx,P.id,pay_code,state,sector,months,money,paykind,paydate,validity,point,pay_price,bankcode,payname,startdate,vp_sdate,vp_edate,sp_sdate,sp_edate,memo,L.toss,2 AS tbl_key FROM {$my_db}.tm_pay_history P , {$my_db}.tm_pay_list L WHERE {$condition}) ";
    $SQL.="ORDER BY order_no DESC";
    $stmt=$pdo->prepare($SQL);
    $stmt->execute();
    
    #파일로그
    #if($flogFlag==0){$toss->fileLog("[payResult  result,list > log 1", $SQL);}
    
    while($rs=$stmt->fetch())
    {
      $rowspan=$p_arr[$rs['order_no']][rowspan];
      $paykind=$toss->arr_payKind($rs['paykind']);
    	//$arr_paykind=array(1=>"카드",2=>"무통장입금",3=>"실시간이체",4=>"가상계좌");
    	
    	$remain_day="";
    	if($rs['validity']>=$today)
    	{
    	  $expire_date=explode("-",$rs['validity']);
			  $expire_time=mktime(0,0,0,$expire_date[1],$expire_date[2],$expire_date[0]);
			  $remain_day=floor(($expire_time - time() + 86400) / 86400);
    	}
    	$sector=""; $sectorArr="";
		  if($rs['pay_code']==100){$sector=$auctArr[$rs['state']];}
    	elseif($rs['pay_code']==101){$sector=$lect_arr[$rs['sector']];}
    	$start=str_replace("-",".",$rs['startdate']);
		  $expire=str_replace("-",".",$rs['validity']);
    	
    	list($receipt_type,$receipt,$orderId,$customerName,$bank,$accountNumber)=explode("||",$pay_arr[$rs['order_no']]);   	
    	$dataArr["item"][]=
    	[
    	  "ROWSPAN"=>$rowspan,                    //rowspan
    	  "PREV"=>$prev,                          //이전 주문번호
    	  "NO"=>$row_no,                          //연번
    	  "ORDER_NO"=>$rs['order_no'],            //주문번호
		    "PKD"=>$paykind,                       //카드,가상계좌
		    "TBL"=>$rs['tbl_key'],                  //table 구분키(tm_pay_result,tm_pay_history)
		    "IDX"=>$rs['idx'],                      //일련번호
		    "ID"=>$rs['id'],                        //회원아이디
		    "STATE"=>$rs['state'],                  //결제지역
		    "SECTOR"=>$rs['sector'],                //가맹지역
		    "SECTOR"=>$sector,
		    "MONTH"=>$rs['months'],                 //신청기간
		    "PDATE"=>$rs['paydate'],                //결제일
		    "START"=>$start,                        //시작일
		    "EXPIRE"=>$expire,                      //만료일
		    "REMAIN"=>$remain_day,                  //사용일수
		    "MONEY"=>$rs['money'],                  //이용료
		    "POINT"=>$rs['point'],                  //사용 포인트
		    "PTIME"=>$rs['paytime'],                //결제시간
		    "PAY_CODE"=>$pay_code_arr[$rs['pay_code']], //결제 타입 arrCom 100:경매정보, 101:동영상강좌
		    "REC_ID"=>$rs['rec_id'],                //추천인ID
		    "MEMO"=>$rs['memo'],                    //결제 메모
		    "PMONEY"=>$rs['pay_price'],             //결제금액
		    "RECEIPT"=>$receipt,                    //영수증 URL
		    "RECEIPT_TYPE"=>$receipt_type,          //영수증(소득공제,지출증빙)
		    "REC_ORDERID"=>$orderId,                //ORDER_NO와 중복, 영수증신청시 비교          
		    "TBLKEY"=>$rs['tbl_key'],               //pay_result:1, pay_history:2
		    "CUSTOMERNAME"=>$customerName,          //가상계좌입금자명
		    "BANK"=>$bank,                          //가상계좌은행
		    "ACCOUNTNUMBER"=>$accountNumber,        //가상계좌번호
		    "SPECIAL_S"=>$special_s,                //
		    "SPECIAL_E"=>$special_e                 //
	    ];
	    $prev=$rs['order_no'];
	    $row_no--;
	  }    
  }  break;
  case 2	:
	{
	  #payresult toss
	  #경매결제
    $stmt=$pdo->prepare("SELECT * FROM {$my_db}.tc_price WHERE use_key=1 ORDER BY sort_num");
    $stmt->execute();
    while($rs=$stmt->fetch()){$pi[$rs['state']]=array("area" => $rs['area'], "srv_area" => $rs['service_area']);}	 
        
    #강의결제
    $stmt=$pdo->prepare("SELECT * FROM {$my_db}.te_lecture WHERE 1");
    $stmt->execute();
    while($rs=$stmt->fetch()){$pi[$rs['lec_code']]=array("area" => $rs['course'], "srv_area" => $rs['teacher']);}
    
    $stmt=$pdo->prepare("SELECT COUNT(*) FROM {$my_db}.tm_pay_log WHERE id ='{$client_id}' AND status!=''");
    $stmt->execute();        
    $totCnt=$stmt->fetchColumn();
    
    $SQL="SELECT * FROM {$my_db}.tm_pay_log WHERE id ='{$client_id}' AND status!='' ORDER BY wdate desc";
    $stmt=$pdo->prepare($SQL);
    $stmt->execute(); 
    
    $sq=0;
    while($rs=$stmt->fetch())
    {
      $no=$totCnt-$sq;
      //if($rs['order_type']==1)      {$paytype="카드";}
      //else if($rs['order_type']==4) {$paytype="가상계좌";}
      $paytype=$toss->arr_payKind($rs['order_type']);
      
      ##json_decode 두번째 인수 true > array
      #결제 > order 저장
      $log_text=json_decode($rs['log_text'],true);
      #결제 > 카드,가상(입금전)
      $result_data=json_decode($rs['result_data'],true);
      #결제 > 영수증 요청
      $receip_data=json_decode($rs['receip_data'],true);
      #결제지역,기간,금액
      $st_amt=$rs['amt'];
      $smp_arr=explode(",",$log_text['smp']);
      $arr=array();
      foreach($smp_arr as $v)
      {
        list($state,$month,$price)=explode(":",$v);
        $month=($rs['pay_code']==100)? "{$month}개월" : "{$month}일";
        array_push($arr,$pi[$state]['area']." > ". $month." ");
      }
      $smp_str=implode(",",$arr);
      #구매상태
      //$status=payStatus($rs['return_status']);
      $status=$toss->arr_payStatus($rs['return_status']);
            
      $dataArr["item"][]=
    	[
    	  "no"=>$no,                                      //no
    	  "orderId"=>$rs['order_no'],                    
    	  "paytype"=>$paytype,                            //구분
    	  "paylist"=>$smp_str,                            //구매내역
    	  "paydate"=>$rs['wdate'],                        //구매일시
    	  "paystatus"=>$rs['return_status'],
        "paystatus_str"=>$status,                        //구매상태
    	  "amount"=>$st_amt,                              //구매금액
    	  "receipt"=>$rs['receipt'],                      //영수증 URL
    	  "receipt_type"=>$rs['receipt_type'],            //소득공제, 지출증빙
        "order_no"=>$rs['order_no'],                    //주문번호
        "accountee"=>$rs['accountee'],                  //결제한 사람
        "dc_rate"=>$rs['dc_rate'],                       //할인율
        "logDt"=>$log_text,
        "resultDt"=>$result_data,
        "receipDt"=>$receip_data
      ];
      $sq++;
    }
    
    $dataArr['totCnt']=$totCnt;
	} break;
	case  3 :
	{
	  #무통장, 가상계좌 결제대기 > 최근 신청/접수 내역
	  $dataArr=array();
	  
	  #회원할인정보
	  $stmt=$pdo->prepare("SELECT dc_rate FROM {$my_db}.tm_member WHERE id='{$client_id}'");
	  $stmt->execute();
	  $rs=$stmt->fetch();
	  $dc_rate=$rs['dc_rate'];
	  
    #경매 가격 배열
  	$stmt=$pdo->prepare("SELECT * FROM {$my_db}.tc_price WHERE use_key=1 ORDER BY sort_num");
  	$stmt->execute();
  	while($rs=$stmt->fetch()){$pi[$rs['state']]=array("area" => $rs['area'], "srv_area" => $rs['service_area'], "price"=>array("1"=>$rs['price_01'],"3"=>$rs['price_03'],"6"=>$rs['price_06'],"12"=>$rs['price_12']));}	

    #강의 가격 배열
  	$stmt=$pdo->prepare("SELECT * FROM {$my_db}.te_lecture WHERE 1");
  	$stmt->execute();
  	while($rs=$stmt->fetch()){$pi[$rs['lec_code']]=array("area" => $rs['course'], "srv_area" => $rs['teacher'], "price"=>$rs['price']);}
	 
	  #tm_pay_wait JOIN tm_pay_log
	  $SQL="SELECT P.*,L.dueDate,L.status,L.accountNumber,L.accountBank,L.customerName FROM {$my_db}.tm_pay_wait  P , {$my_db}.tm_pay_log L WHERE P.order_no=L.order_no AND P.id='{$client_id}' AND L.status='WAITING_FOR_DEPOSIT'";
	  //$SQL="SELECT * FROM {$my_db}.tm_pay_wait WHERE id='{$client_id}' AND wtime > DATE_SUB(CURRENT_DATE(),INTERVAL 10 DAY) GROUP BY apm";
	  $stmt=$pdo->prepare($SQL);
    $stmt->execute();
    while($rs=$stmt->fetch())
    {
      $paykind=$pay_kind_arr[$rs['paykind']];
      $srv_price=$rs['srv_price'];
      $pay_price=$rs['pay_price'];
      $dueDate=date("Y.m.d H:i:s", strtotime($rs['dueDate'])); 
      $accountNumber=$rs['accountNumber'];
      $accountBank=$rs['accountBank'];
      $pay_code=$rs['pay_code'];//추가
      $customerName=$rs['customerName'];
    	$bank_info="{$accountBank}-{$customerName}";
      $smp_arr=explode(",",$rs['apm']);
      $arr=array();
      foreach($smp_arr as $v)
      {
        list($state,$month,$price)=explode(":",$v);
        $month=($pay_code==100)? "{$month} 개월" : "{$month} 일";
        array_push($arr,$pi[$state]['area'].">". $month." ");
      }
      $stateArea=implode(",",$arr);
      
      //접수시간
      $wdate=substr($rs['wtime'],5,11);
      $dataArr["item"][]=
    	[
    	  "order_no"=>$rs['order_no'],
    	  "paykind"=>$paykind,      //결제구분
    	  "srv_price"=>$srv_price,  //이용료
    	  "pay_price"=>$pay_price,  //결제금액
    	  "bank_info"=>$bank_info,  //은행정보
    	  "stateArea"=>$stateArea,  //결제지역
    	  "wdate"=>$wdate,          //접수시간
    	  "accountNumber"=>$accountNumber,    //가상계좌변호
    	  "accountBank"=>$accountBank,        //가상계좌은행
    	  "customerName"=>$customerName,      //가상계좌입금자
    	  "duedate"=>$dueDate
    	];
    }  
	} break;
	case  9 :
	{
	  #가상계좌 정보 >  pay_history
	  $orderId=(int)$orderId;
	  $SQL="SELECT * FROM {$my_db}.tm_pay_log WHERE order_no={$orderId} LIMIT 0,1";
	  $stmt=$pdo->prepare($SQL);
    $stmt->execute(); 
    $rs=$stmt->fetch();
    $res=json_decode($rs['result_data'],true);
    $dataArr['amount']=$rs['amt'];
    $dataArr['accountNumber']=$res['accountNumber'];
    $dataArr['accountType']=$res['accountType'];
    $dataArr['bank']=$res['bank'];
    $dataArr['customerName']=$res['customerName'];
    $dataArr['dueDate']=date("Y.m.d H:i:s", strtotime($res['dueDate']));
    $dataArr['expired']=$res['expired'];
    $dataArr['settlementStatus']=$res['settlementStatus'];
    $dataArr['refundStatus']=$res['refundStatus'];

	} break;
	case 10	:
	{
    #영수증 신청
    $orderId=(int)$orderId;
    $amount=(int)$amount;
    $type_=(int)$type;
    if($type_==1){$type="소득공제";}else if($type==2){$type="지출증빙";}else{exit;}
    $registrationNumber=(int)$registrationNumber;
    
    #CURLOPT_POSTFIELDS
    $data=array();      
    $data["orderId"]=$orderId;
    $data["orderName"]=$orderName;
    $data["amount"]=$amount;
    $data["type"]=$type;
    $data["registrationNumber"]=$registrationNumber;
  
    $res=$toss->issuePayment($data);
    $err=$res['err'];
    $code=$res['resCode'];
    $rs=$res['resData'];
    
    #파일로그
    $receipt_data=json_encode($rs, JSON_UNESCAPED_UNICODE);
    $toss->fileLog("[res payResult {$mode} > log 1", $receipt_data); 
    
    if(!$err)
    {
      if($rs['code'])
      {
        $code=$rs['code'];      
        $mesage=$rs['message'];
        $dataArr['success']=0;
        $dataArr['success_ment']="{$message}";
        
        #파일로그
        $toss->fileLog("[res payResult {$code}> log 1", $message);
      }
      else
      {
        $receipt_url=$rs['receiptUrl'];
        $receipt_type=$rs['type'];
        
        $USQL="UPDATE {$my_db}.tm_pay_log SET receipt_log='{$receipt_data}', receipt_url='{$receipt_url}', receipt_type='{$receipt_type}' WHERE id='{$client_id}' AND order_no={$orderId}";
        $stmt=$pdo->prepare($USQL);
        $stmt->execute();
        
        $dataArr['success']=1;
        $dataArr['success_ment']="{$receipt_type} 영수증 발급되었습니다.";
        $dataArr['data']=$receipt_data;
        #파일로그
        if($flogFlag==0){$toss->fileLog("[res payResult  OK > log 2", $USQL);}       
      }
    }
    else 
    {
      $dataArr['success']=0;
      $dataArr['success_ment']="영수증 발급 오류가 발생했습니다.";
      #파일로그
      $toss->fileLog("[res payResult err > log 3", json_encode($err,JSON_UNESCAPED_UNICODE));
    }
  }  break;
	case 20	:
	{
	  #결제 취소신청
	  $orderId=(int)$orderId;
    $stmt=$pdo->prepare("SELECT paymentkey FROM {$my_db}.tm_pay_log WHERE id='{$client_id}' AND order_no={$orderId}");
    $stmt->execute();
    $rs=$stmt->fetch();
    $paymentkey=$rs['paymentkey'];
    $receipt_type=$rs['receipt_type'];
    $receipt=$rs['receipt'];
    
    $amount=(int)$amount;
	  $refundableAmount=(int)$refundableAmount;
	  $cancelAmount=(int)$cancelAmount;
	  
	  $data=array();
	  //취소사유
	  $data["cancelReason"]=$cancelReason;
	  //취소금액
	  $data["cancelAmount"]=$cancelAmount;
	  //은행정보
	  $data['refundReceiveAccount']['bank']=$bank;
	  $data['refundReceiveAccount']['accountNumber']=$accountNumber;
	  $data['refundReceiveAccount']['holderName']=$holderName;
	  $data['refundableAmount']=$refundableAmount;
    
    $res=$toss->cancelPayment($paymentkey,$data);
    $err=$res['err'];
    $code=$res['resCode'];
    $rs=$res['resData'];
    
    #파일로그
    $cancel_data=json_encode($rs, JSON_UNESCAPED_UNICODE);
    $toss->fileLog("[res payResult {$mode} > log 1", $cancel_data); 
    ### 참고 ###
    #Toss.php > resData=>json_decode(response,true) > $re['receiptUrl]
    #Toss.php > resData=>json_decode(response) > $rs->receiptUrl
    ### 참고 ###
  
    if($err=="")
    {
      if($rs['code'])
      {
        $code=$rs['code'];      
        $mesage=$rs['message'];
        $dataArr['success']=0;
        $dataArr['success_ment']="{$message}";
        #파일로그
        $toss->fileLog("[res payResult {$mode} {$message} > log 2", $cancel_data);
      }
      else
      {
        #주의 > 현금영수증 미발행시 별도로 취소작업처리($receiptKey) > 동일한 값에 취소날짜 받음
        #paylog update
        $status=$rs['status'];
        $status_message=$status_arr[$status];
        $cancels=$rs['cancels'];
        $cancels_data=json_encode($cancels,JSON_UNESCAPED_UNICODE);
        
        $USQL="UPDATE {$my_db}.tm_pay_log SET status='CANCELED', cancels_log='{$cancel_data}',wdate=NOW() WHERE id='{$client_id}' AND order_no={$orderId}";
        $stmt=$pdo->prepare($USQL);
        $stmt->execute();
        
        #파일로그
        if($flogFlag==0){$toss->fileLog("[res payResult] {$mode} > log 3", $USQL);}
        
        #결제취소 :: 결제모두 취소(회원관리) > 재결제
        #참고 :: Superadmin > pay_edit[mode pay_del] > pay_edit_db
        #pay_result > validity 만료
        $beforeDay=date('Y-m-d', strtotime('-1 day'));
        $VSQL="UPDATE {$my_db}.tm_pay_result SET validity='{$beforeDay}' WHERE id='{$client_id}' AND order_no={$orderId}";
        //$stmt=$pdo->prepare($USQL);
        //$stmt->execute();
        #파일로그
        $toss->fileLog("[res payResult {$mode}> log 4", $VSQL);
        
        $dataArr['success']=1;
  	    $dataArr['success_ment']="취소신청 성공";
      }      
    }
    else
    {
       $dataArr['success']=0;
  	   $dataArr['success_ment']="취소신청 실패";    
    }
	} break; 
	case  100 :
	{
	  #Tosspay_order > tm_pay_log 업데이트
	  $pay_opt=(int)$pay_opt;
	  $pay_code=(int)$pay_code;
	  $order_no=(int)$order_no;
	  $amt=(int)$amt;
	  $dc_rate=(int)$dc_rate;
	  $log_data=base64_decode($log_data);
	  $lrs=json_decode($log_data,true);
    $smp=base64_decode($smp);
	  #flag
	  $sucFlag=0;
	  $sucMsg="";	  
	  
	  #경매 가격 배열
  	$stmt=$pdo->prepare("SELECT * FROM {$my_db}.tc_price WHERE use_key=1 ORDER BY sort_num");
  	$stmt->execute();
  	while($rs=$stmt->fetch()){$pi[$rs['state']]=array("area" => $rs['area'], "srv_area" => $rs['service_area'], "price"=>array("1"=>$rs['price_01'],"3"=>$rs['price_03'],"6"=>$rs['price_06'],"12"=>$rs['price_12']));}	

    #강의 가격 배열
  	$stmt=$pdo->prepare("SELECT * FROM {$my_db}.te_lecture WHERE 1");
  	$stmt->execute();
  	while($rs=$stmt->fetch()){$pi[$rs['lec_code']]=array("area" => $rs['course'], "srv_area" => $rs['teacher'], "price"=>$rs['price']);}
	  
	  $smp_arr=explode(",",$smp);
    $arr_=array();
    foreach($smp_arr as $v)
    {
      list($state,$month,$price)=explode(":",$v);
      $month=($pay_code==100)? "{$month}개월" : "{$month}일";
      array_push($arr_,$pi[$state]['area'].">". $month." ");
    }
    $stateArea=implode(",",$arr_);
	 
	  ## price 체크
	  $res=$toss->samePay($pay_code,$smp,$amt,$dc_rate);
	  $sucFlag=$res['resCode'];
    $sucMsg=$res['resData'];
    ## price 체크

	  ## paylog 기록 
	  $SQL="SELECT COUNT(*) FROM {$my_db}.tm_pay_log WHERE order_no={$order_no} AND id='{$client_id}'";
    $stmt=$pdo->prepare($SQL);
    $stmt->execute();
    $rowCnt=$stmt->fetchColumn();
    
    if($rowCnt==0)
    {
      #srv_price:이용료, goods 지역결제 몇개월 >> 누락확인
      //$bank_=$bank_arr[$lrs['bank_code']]['name'];
      $ISQL="INSERT INTO {$my_db}.tm_pay_log SET id='{$client_id}', order_no={$order_no}, pay_opt='{$pay_opt}', pay_code='{$pay_code}', goods='{$stateArea}', name='{$lrs[pay_name]}', smp='{$smp}', bank='{$lrs['bank_code']}', srv_price='', pay_price='{$lrs[amt]}', dc_rate='{$lrs[dc_rate]}', wdate=NOW(), order_ip='{$_SERVER[REMOTE_ADDR]}'";
      $stmt=$pdo->prepare($ISQL);
      $stmt->execute();
      
      $sucFlag=1;
      $sucMsg="paylog insert OK";
      #파일로그
      if($flogFlag==0){$toss->fileLog("[payResult] pay_order {$mode} > log 1", $ISQL);}
    }
    else
    {
      $USQL="UPDATE {$my_db}.tm_pay_log SET pay_opt='{$pay_opt}', pay_code='{$pay_code}', goods='{$stateArea}', name='{$lrs[pay_name]}', smp='{$smp}', bank='{$lrs['bank_code']}', srv_price='', pay_price='{$lrs[amt]}', dc_rate='{$lrs[dc_rate]}', wdate=NOW(), order_ip='{$_SERVER[REMOTE_ADDR]}' WHERE order_no={$order_no} AND id='{$client_id}'"; 
      $stmt=$pdo->prepare($USQL);
      $stmt->execute();
      
      $sucFlag=1;
      $sucMsg="paylog update OK";
      #파일로그
      if($flogFlag==0){$toss->fileLog("[payResult]  pay_orde {$mode} > log 2", $USQL);}
    }   
    ## paylog 기록
    $dataArr['success']=$sucFlag;
    $dataArr['sucMsg']=$sucMsg;
	} break; 
	/*
	case  101 :
	{
	  #Tosspay_order > tm_pay_log 업데이트
	  $pay_opt=(int)$pay_opt;
	  $pay_code=(int)$pay_code;
	  $order_no=(int)$order_no;
	  $amt=(int)$amt;
	  $dc_rate=(int)$dc_rate;
	  $log_data=base64_decode($log_data);
    $smp=base64_decode($smp);
	  #flag
	  $sucFlag=0;
	  $sucMsg="";	  
	  
	  ## price 체크
	
	  //$res=$toss->samePay($pay_code,$smp,$amt,$dc_rate);
	  //$sucFlag=$res['resCode'];
    //$sucMsg=$res['resData'];

    ## price 체크

	  ## paylog 기록 
	  $SQL="SELECT COUNT(*) FROM {$my_db}.tm_pay_log WHERE order_no={$order_no} AND id='{$client_id}'";
    $stmt=$pdo->prepare($SQL);
    $stmt->execute();
    $rowCnt=$stmt->fetchColumn();
    if($rowCnt==0)
    {
      $ISQL="INSERT INTO {$my_db}.tm_pay_log SET order_type='{$pay_opt}', pay_code='{$pay_code}', wdate=NOW(), order_no={$order_no}, id='{$client_id}', name='{$client_name}', amt='{$amt}', dc_rate='{$dc_rate}', log_text='{$log_data}', order_ip='{$_SERVER['REMOTE_ADDR']}'";
      $stmt=$pdo->prepare($ISQL);
      $stmt->execute();
      
      $sucFlag=1;
      $sucMsg="paylog insert OK";
      #파일로그
      if($flogFlag==0){$toss->fileLog("[payResult] pay_order {$mode} > log 1", $ISQL);}
    }
    else
    {
      $USQL="UPDATE {$my_db}.tm_pay_log SET order_type='{$pay_opt}', pay_code='{$pay_code}', wdate=NOW(), log_text='{$log_data}', amt='{$amt}', dc_rate='{$dc_rate}', order_ip='{$_SERVER['REMOTE_ADDR']}' WHERE order_no={$order_no} AND id='{$client_id}'"; 
      $stmt=$pdo->prepare($USQL);
      $stmt->execute();
      
      $sucFlag=1;
      $sucMsg="paylog update OK";
      #파일로그
      if($flogFlag==0){$toss->fileLog("[payResult]  pay_orde {$mode} > log 2", $USQL);}
    }   
    ## paylog 기록
    
    $dataArr['success']=$sucFlag;
    $dataArr['sucMsg']=$sucMsg;
	} break;	
	*/
}

$result=json_encode($dataArr,JSON_UNESCAPED_UNICODE);
echo($result);	
?>