<?
//Board-리스트
$boardArr=array(
    'news'=>'뉴스사항',
    'mreport'=>'결산관리',
    'ydonate'=>'연간기부금',
    'result'=>'활용실적',
    'ldonate'=>'후원금내용',
    'gnews'=>'새소식',
    'gdstory'=>'후원이야기'
);
//페이지 리스트
$pageArr=array(
    'news'=>10,
    'mreport'=>11,
    'ydonate'=>12,
    'result'=>13,
    'ldonate'=>40,
    'gnews'=>50,
    'gdstory'=>51
);
//게시판 읽기, 쓰기 권한
function boardAuth($board_id)
{
    global $pdo, $my_db, $client_id;
    $SQL="SELECT * FROM {$my_db}.tc_board_admin WHERE board_id ='{$board_id}'";
    $stmt=$pdo->prepare($SQL);
    $stmt->execute();
    $rs=$stmt->fetch();

    if($rs['r_member']==1 && !$client_id){alertBack('위드탱크 회원전용 서비스입니다. 로그인 해주세요!');}
    //if($rs['r_guest']==0 && $rs['r_member']==0){alertBack('권한이 없습니다.');}
}

//네이버 API (뉴스검색)
function naverNewsResult($query='', $sort='', $display=0, $start=0) {
 
    $api_url = "";
    $client_id = "9W86IeUvVCsODeNbzWt0";
    $client_secret = "Sx9Bq34ZJB";
    // 요청 URL
    $api_url .= "https://openapi.naver.com/v1/search/news.json"; // 뉴스 검색 결과 json
    // 검색어, 필수 입력
    $api_url .= "?query=".urlencode($query);
    // 정렬, sim (정확도순) or date(최신순). 없으면 default 값인 sim 으로 적용됨
    if($sort != ""){$api_url .= "&sort=".$sort;}
    // 검색 시작 위치, 없으면 기본값    
    if($start > 0){$api_url .= "&start=".$start;}
    // 한 페이지에 보여줄 개수, 없으면 기본값
    if($display > 0){$api_url .= "&display=".$display;}
    $ch = curl_init();
    $ch_headers[] = "X-Naver-Client-Id: ".$client_id;
    $ch_headers[] = "X-Naver-Client-Secret: ".$client_secret;
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $ch_headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);
 
    return $result;
}
?>