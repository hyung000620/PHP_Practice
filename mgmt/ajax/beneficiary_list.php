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
        //이름,아이디,폰 검색
        if($sname){$cdtnArr[]="A.name LIKE '%{$sname}%'";}
        if($sid){$cdtnArr[]="A.id LIKE '%{$sid}%'";}
        if($sphone){$cdtnArr[]="A.phone LIKE '%{$sphone}%'";}
        if($smobile){$cdtnArr[]="A.mobile LIKE '%{$smobile}%'";}
        $cdtn=($cdtnArr) ? implode(" AND ",$cdtnArr) : "1";

        $sq=0;
        $SQL="SELECT A.idx,A.id,A.name,A.mobile,A.email,IFNULL(B.pay_price,0) AS pay_price, IFNULL(B.memo,'') AS memo";
        $SQL.=" FROM {$my_db}.tm_member AS A LEFT JOIN {$my_db}.tm_pay_list AS B ON A.id = B.id WHERE {$cdtn}";
        $stmt=$pdo->prepare($SQL);
        $stmt->execute();
        $totCnt=$stmt->fetchColumn();

        $SQL.=" ORDER BY A.idx DESC LIMIT {$start}, {$dataSize}";
        $stmt=$pdo->prepare($SQL);
        $stmt->execute();
        while($rs=$stmt->fetch())
        {
            $no=$totCnt-$sq;
            $dataArr['item'][]=
            [
                "idx"=>$rs['idx'],
                "no"=>$no,
                "name"=>$rs['name'],
                "id"=>$rs['id'],
                "mobile"=>$rs['mobile'],
                "email"=>$rs['email'],
                "memo"=>$rs['memo'],
                "pay_price"=>$rs['pay_price']
            ];
            $sq++;
        }
        $dataArr['totCnt']=$totCnt;
	} break;		
}



$result=json_encode($dataArr,JSON_UNESCAPED_UNICODE);
echo($result);	
?>