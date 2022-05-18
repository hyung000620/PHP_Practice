<?
include($_SERVER["DOCUMENT_ROOT"]."/inc/xmlHeader.php");

$dataArr=array();
$mode=(int)$mode;
switch($mode)
{
    case 1:
    {
        $SQL="SELECT * FROM {$my_db}.te_edu WHERE 1";
        $stmt=$pdo->prepare($SQL);
        $stmt->execute();
        $today=date('Y-m-d');
        $state="";
        while($rs=$stmt->fetch())
        {   
            //자동
            if($rs['dp_off']==0)
            {
                if($today>$rs['edate']){$state="마감";}
                else if($today<$rs['state']){$state="모집중";}
                else{$state="진행중";}
            }
            //수동
            //else{$state=$rs['state'];}
            $dataArr['item'][]=[
                'idx'=>$rs['idx'],
                'edu_title'=>$rs['edu_title'],
                'edu_teacher'=>$rs['edu_teacher'],
                'edu_addr'=>$rs['edu_addr'],
                'open_date'=>explode("|",$rs['open_date']),
                'sdate'=>$rs['sdate'],
                'edate'=>$rs['edate'],
                'state'=>$state
            ];  
        }
    }break;
    case 2 :
    {
        $SQL="SELECT * FROM {$my_db}.te_edu WHERE idx ={$idx}";
        $stmt=$pdo->prepare($SQL);
        $stmt->execute();
        $rs=$stmt->fetch();
        
        $dataArr['photo_edu']=$rs['photo_edu'];
        $dataArr['edu_title']=$rs['edu_title'];
        $dataArr['on_off']=$rs['on_off'];
        $dataArr['edu_teacher']=$rs['edu_teacher'];
    }break;
}

$result=json_encode($dataArr,JSON_UNESCAPED_UNICODE);
echo $result;
?>