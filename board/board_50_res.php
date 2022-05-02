<?
include_once("$_SERVER[DOCUMENT_ROOT]/inc/xmlHeader.php");

$page=(int)$page;
$page=($page)?$page : 1; //현재 페이지
$page_scale=2;
$list_scale=4;
$start=($page-1)*$list_scale;
if($page>1){$list_scale=$page*5;}

$dataArr=array();
$mode=(int)$mode;

$page_code="50".$dv;
switch($mode)
{
    case 10:
    {
        $SQL="SELECT * FROM {$my_db}.tc_board WHERE board_id='{$board_id}' AND del=0";
        if(!empty($keyword)){$SQL.=" AND title LIKE '%{$keyword}%'";}
        $SQL.=" ORDER BY idx DESC";
        $SQL.=" LIMIT {$start},{$list_scale}";
        $stmt=$pdo->prepare($SQL);
        $stmt->execute();
        $total_record=$stmt->rowCount();
        $dataArr['total_record']=$total_record;
        
        while($rs=$stmt->fetch())
        {
            $dataArr['item'][]=
            [
                "wdate"=>$rs['wdate'],
                "title"=>$rs['title'],
                "idx"=>$rs['idx']
            ];
        }
        $total_page=ceil($total_record/$page_scale);
        $dataArr['total_page']=$total_page;
        $dataArr['page']=$page;
    }break;

    case 20:
    {
        $SQL="SELECT * FROM {$my_db}.tc_board WHERE board_id='{$board_id}' AND idx='{$idx}'";
        $stmt=$pdo->prepare($SQL);
        $stmt->execute();
        $rs=$stmt->fetch();
        
        $dataArr["del"]=$rs['del'];
        $dataArr["title"]=$rs['title'];
        $dataArr["content"]=htmlspecialchars_decode($rs['content']);

        $SQL="SELECT * FROM {$my_db}.tc_board_file WHERE board_id='{$board_id}' AND ref_idx ='{$idx}'";
        $stmt=$pdo->prepare($SQL);
        $stmt->execute();
        $file_record=$stmt->rowCount();
        $dataArr['file_record']=$file_record;
        $file=0;
        $pdf=0;
        
        while($rs=$stmt->fetch())
        {
            $dataArr['item'][]=
            [
                "idx"=>$rs['idx'],
                "org_file"=>$rs['org_file'],
                "sav_file"=>$rs['sav_file'],
                "file_size"=>$rs['file_size'],
                "mime_type"=>$rs['mime_type'],
            ];
            if($rs['mime_type']=="application/pdf"){$pdf++;}else{$file++;}
        }

        $dataArr['file']=$file;
        $dataArr['pdf']=$pdf;
    }break;
    
}

$result=json_encode($dataArr,JSON_UNESCAPED_UNICODE);
echo($result);
?>