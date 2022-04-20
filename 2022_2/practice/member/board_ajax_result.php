<?
include_once("$_SERVER[DOCUMENT_ROOT]/inc/xmlHeader.php");

$page_scale=10;
$start=($start) ? $start : 0;
$list_scale=($list_scale) ? $list_scale : 20;
$page=($page)?$page : 1;

$dataArr=array();
$mode=(int)$mode;

$page_code="50".$dv;
switch($mode)
{
    case 10:
    {
        $SQL="SELECT * FROM {$my_db}.tc_board WHERE board_id='{$board_id}'";
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
    }break;
}

$result=json_encode($dataArr,JSON_UNESCAPED_UNICODE);
echo($result);
?>