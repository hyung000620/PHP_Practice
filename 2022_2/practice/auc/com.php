<?
include($_SERVER["DOCUMENT_ROOT"]."/practice/inc/xmlHeader.php");

// datepicker;

$SQL = "SELECT * FROM db_dev.ta_board WHERE DATE(regdate) BETWEEN '{$datepicker1}' AND '{$datepicker2}'";

$stmt=$pdo->prepare($SQL);
$stmt->execute();
    
$dataArr=array();
switch ($mode)
{
  case "xml"  :
  {
    while($rs=$stmt->fetch())
    {
      echo "
      <item>
        <title><![CDATA[{$rs['title']}]]></title>
        <uname><![CDATA[{$rs['user_name']}]]></uname>
        <content><![CDATA[{$rs['content']}]]></content>
        <view><![CDATA[{$rs['view']}]]></view>
        <rdate><![CDATA[{$rs['regdate']}]]></rdate>
      </item>";
    }
  } break;
  case  "json"  :
  {
    $dataArr=array();
    while($rs=$stmt->fetch())
    {
      $dataArr["item"][]=array(
        "title"=>$rs['title'],
        "uname"=>$rs['user_name'],
        "content"=>$rs['content'],
        "view"=>$rs['view'],
        "rdate"=>$rs['regdate']
      );
    }
    //$dataArr['sql'] = $SQL;   
    $result=json_encode($dataArr,JSON_UNESCAPED_UNICODE);
    if($dataArr){echo($result);}
  } break;
} 
?>