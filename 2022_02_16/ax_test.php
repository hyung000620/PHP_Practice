<?
include($_SERVER["DOCUMENT_ROOT"] ."/_test/inc/xmlHeader.php");

/*
    MYSQL 에서 where = 1의 의미는 말그대로 참, True를 의미한다.
    항상 true 가 되어 실행된다.
    나중에 where 절 뒤에 and 나 if을 쓰기에도 유연하게 수정이 가능하다.
*/
$stmt=$pdo->prepare("SELECT count(*) FROM db_dev.ta_board WHERE 1");
$stmt=$pdo->prepare("SELECT count(*) FROM db_dev.ta_board WHERE 1");
$stmt->execute();
$rowCnt=$stmt->fetchColumn();
    
$SQL="SELECT * FROM db_dev.ta_board WHERE 1";
$stmt=$pdo->prepare($SQL);
$stmt->execute();

switch($mode){
    case "xml"  :
        {
          echo "<result>";
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
          echo "
                 <rowCnt><![CDATA[{$rowCnt}]]></rowCnt>
                     </result>";
        } break;
    case "json" :
        {
            $dataArr = array();
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
            $dataArr["rowCnt"]=$rowCnt;

            /*
                json_encode() 는 배열 똔느 문자열을 json 문자열로 변환하는 함수. 
                ex) 1. echo json_encode("로그인");
                // \ub85c\uadf8\uc778
                    2. echo json_encode("로그인", JSON_UNESCAPED_UNICODE);
                // 로그인    
                => json_encode 실행시 한글 부분이 유니코드화 되버리는 경우가 발생한다. 
                JSON_UNESCAPED_UNICODE을 설정하면 유니코드화가 적용되지 않고,
                정상적으로 encode가 된다.
            */
            $result=json_encode($dataArr, JSON_UNESCAPED_UNICODE);
            if($dataArr){echo($result);}
        }
        break;
}
?>
