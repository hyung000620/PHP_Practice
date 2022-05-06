<?
include($_SERVER["DOCUMENT_ROOT"]."/SuperAdmin/common/xml_header.php");
include $_SERVER["DOCUMENT_ROOT"]."/member/Cipher.php";

if(!preg_match("/".$_SERVER['HTTP_HOST']."/i",$_SERVER['HTTP_REFERER'])){exit;}

$mode=(int)$mode;
$dataArr=array();

#page 
$dataSize=(int)$dataSize;
$pageSize=(int)$pageSize;
$pageNo=(int)$pageNo;
$start=($pageNo) ? (($pageNo-1)*$dataSize) : 0;

switch ($mode)
{
  case 1	:
	{
	  #������ ����Ʈ
	  if($sname){$cdtnArr[]="name LIKE '%{$sname}%'";}
	  if($sid){$cdtnArr[]="id LIKE '%{$sid}%'";}
	  if($sphone){$cdtnArr[]="phone LIKE '%{$sphone}%'";}
	  if($smobile){$cdtnArr[]="mobile LIKE '%{$smobile}%'";}
	  $cdtn=($cdtnArr) ? implode(" AND ",$cdtnArr) : "1";
	  
    $sq=0;
	  $TSQL="SELECT COUNT(*) FROM {$my_db}.tz_staff WHERE {$cdtn}";
	  $stmt=$pdo->prepare($TSQL);
    $stmt->execute();        
    $totCnt=$stmt->fetchColumn();
    
	  $SQL="SELECT * FROM {$my_db}.tz_staff WHERE {$cdtn} ORDER BY idx DESC LIMIT {$start}, {$dataSize}";
    $stmt=$pdo->prepare($SQL);
    $stmt->execute();
	  while($rs=$stmt->fetch())
    {
      $no=$totCnt-$sq;
      $dataArr["item"][]=
    	[
    	"idx"=>$rs['idx'],
    	"no"=>$no, 
    	"name"=>$rs['name'],
    	"id"=>$rs['id'],
    	"level"=>$rs['level'],
    	"in"=>$rs['start_dt'],  
    	"out"=>$rs['resign_dt'] 
    	];
    	$sq++;
    }
    $dataArr['totCnt']=$totCnt;
	} break;
  case 2	:
	{
	  #ȸ������Ʈ
	  if($sname){$cdtnArr[]="name LIKE '%{$sname}%'";}
	  if($sid){$cdtnArr[]="id LIKE '%{$sid}%'";}
	  if($smobile){$cdtnArr[]="mobile LIKE '%{$smobile}%'";}
	  $cdtn=($cdtnArr) ? implode(" AND ",$cdtnArr) : "1";
	  
	  #������ ����Ʈ
    $sq=0;
	  $TSQL="SELECT COUNT(*) FROM {$my_db}.tm_member WHERE {$cdtn}";
	  $stmt=$pdo->prepare($TSQL);
    $stmt->execute();        
    $totCnt=$stmt->fetchColumn();
    
	  $SQL="SELECT * FROM {$my_db}.tm_member WHERE {$cdtn} ORDER BY idx DESC LIMIT {$start}, {$dataSize}";
    $stmt=$pdo->prepare($SQL);
    $stmt->execute();
	  while($rs=$stmt->fetch())
    {
      $no=$totCnt-$sq;
      $dataArr["item"][]=
    	[
    	"idx"=>$rs['idx'],
    	"no"=>$no, 
    	"name"=>$rs['name'],
    	"id"=>$rs['id'],
    	"mobile"=>$rs['mobile'],
    	"email"=>$rs['email'],
    	"login"=>$rs['login'],
    	"in"=>$rs['reg_date'],  
    	"out"=>$rs['out_date'],
    	"memo"=>$rs['m_memo']
    	];
    	$sq++;
    }
    $dataArr['totCnt']=$totCnt;
	} break;	
	case 5 :
	{
	  #support_detail > ȸ������ ����
	  $USQL="UPDATE {$my_db}.tm_member SET name='{$name}', mobile='{$mobile}', email='{$email}', r_mail='{$r_mail}', sms='{$r_sms}' ";
	  if($chgpw==1)
	  {
	    $passkey=$cipher->Encrypt($pw);
	    $USQL.=",passkey='{$passkey}' ";
	  }
	  $USQL.= "WHERE idx={$idx}";
	  $stmt=$pdo->prepare($USQL);
    $stmt->execute();
	  
    $dataArr['success']=1;
    $dataArr['idx']=$idx;
	} break;
	case 10 :
	{
	  #������ ���
	  #staff_menu �ӽ�����
	  $ISQL="INSERT INTO {$my_db}.tz_staff SET name='{$name}',id='{$id}',passwd=SHA2('{$pw}', 256),phone='{$phone}',mobile='{$mobile}',email='{$email}',level='{$level}',staff_menu='{$staff_menu}'";
	  
	  $dataArr['sql']=$ISQL;
	  $stmt=$pdo->prepare($ISQL);
    $stmt->execute();

    $dataArr['success']=1;
	} break;
	case 30 :
	{
	  #������ ����
	  $idx=(int)$idx;
	  #staff_menu �ӽ�����
	  $SQL ="UPDATE {$my_db}.tz_staff SET mobile='{$mobile}', phone='{$phone}', email='{$email}', level='{$level}', staff_menu='{$staff_menu}'";
    if($chk_new_pwd=="y" && trim($passwd)!=""){$SQL.=",passwd=SHA2('{$passwd}',256)";}
    $SQL.=" WHERE idx={$idx}";
	  $stmt=$pdo->prepare($SQL);
    $stmt->execute();

    $dataArr['idx']=$idx;
    $dataArr['success']=1;
	} break;
	case 20 :
	{
	  #ȸ�� ���
	  $passkey=$cipher->Encrypt($pw);
	  $ISQL="INSERT INTO {$my_db}.tm_member SET id='{$id}',name='{$name}',passkey='{$passkey}',mobile='{$mobile}',email='{$email}',r_mail='{$r_mail}',sms='{$r_sms}',reg_date='{$ipdate}',out_date='{$outdate}',pf_update=NOW()";
	  $stmt=$pdo->prepare($ISQL);
    $stmt->execute();

    $dataArr['success']=1;
	} break;	
	case 11 :
	{
	  #������ ID �ߺ�üũ
	  $SQL="SELECT COUNT(*) FROM {$my_db}.tz_staff WHERE id='{$id}'";
	  $stmt=$pdo->prepare($SQL);
    $stmt->execute();        
    $totCnt=$stmt->fetchColumn();
    
    $dataArr['totCnt']=$totCnt;
	} break;	
	case 12 :
	{
	  #ȸ��(support) ID �ߺ� üũ
	  $SQL="SELECT COUNT(*) FROM {$my_db}.tm_member WHERE id='{$id}'";
	  $stmt=$pdo->prepare($SQL);
    $stmt->execute();        
    $totCnt=$stmt->fetchColumn();
    
    $dataArr['totCnt']=$totCnt;
	} break;		
	case 50 :
	{
	  $success=0;
	  $msg="";
	  $idx=(int)$fidx;
	  $stmt=$pdo->prepare("SELECT * FROM {$my_db}.tm_member WHERE idx={$idx}");
	  $stmt->execute();        
	  $rs=$stmt->fetch();
  
	  #���� > �������(������)
    if($rs)
    {
      #pay_list
      $USQL="INSERT INTO {$my_db}.tm_pay_list SET pay_opt='{$pay_option}', pay_type='{$pay_type}', order_no='{$order_no}', id='{$rs[id]}', name='{$rs[name]}', pay_price='{$money}', wdate=NOW()";
      $stmt=$pdo->prepare($USQL);
      $stmt->execute();

      $stmt=$pdo->prepare("SELECT LAST_INSERT_ID()");
  		$stmt->execute();
  		$rs=$stmt->fetch();
  		$ref_idx=$rs[0];
  		               
      #���Ͼ��ε� > pdf�� ���ε�
      if($_FILES['receipt']['name'] )
			{ 
			   
        $file_dir=$_SERVER["DOCUMENT_ROOT"]."/data/receipt/";
        $allow_ext=array("pdf");
        $forbid_ext = array("php",".php","asp","jsp","inc","c","cpp","sh","exe","bmp","tiff");
        $max_size=1024*1024*50;	//�뷮 ����(50M)
                 
				$upfile=$_FILES['receipt']['tmp_name'];
				$upfile_name=$_FILES['receipt']['name'];
				$upfile_size=$_FILES['receipt']['size'];
				$upfile_type=$_FILES['receipt']['type'];
							
//$dataArr['f1']=$_FILES['receipt']['name']; 
				// ����Ÿ���̽� ����
				$fileName=func_single_upload($upfile, $upfile_name, $upfile_size, $upfile_type, $file_dir, $forbid_ext, $thum_width, $thum_height);
				$dataArr['f']=$fileName;
				$FSQL="UPDATE {$my_db}.tm_pay_list SET receipt='{$fileName}' WHERE idx={$ref_idx}";
				$stmt=$pdo->prepare($FSQL);
		    $stmt->execute();
		    
		    $success=1;
		    $msg="OK";
  
		  }
		  else 
		  {
        $success=0;
        $msg="pdf�� ���ε� �����մϴ�.";
      } 
    }
    $dataArr['success']=$success;
    $dataArr['msg']=$msg;
	} break;	
}



$result=json_encode($dataArr,JSON_UNESCAPED_UNICODE);
echo($result);	
?>