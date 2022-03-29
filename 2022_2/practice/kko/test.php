<?
// $eKey="gSWZvFxo%2BIU1Re2UFuFBG51U1NbimqJkvNzSq426%2Fy9fZiYWVlQCvM5rTuRQ3BFf5UUga%2FSjmgrFrHHoVeV9zw%3D%3D";
// $dKey="gSWZvFxo+IU1Re2UFuFBG51U1NbimqJkvNzSq426/y9fZiYWVlQCvM5rTuRQ3BFf5UUga/SjmgrFrHHoVeV9zw==";

// $ch = curl_init();
// $url = 'http://apis.data.go.kr/1611000/nsdi/EstateDevlopService/attr/getEDBusinessResultsInfo'; /*URL*/
// $queryParams = '?' . urlencode('serviceKey') . "=".$eKey; /*Service Key*/
// $queryParams .= '&' . urlencode('ldCode') . '=' . urlencode('41'); /**/
// $queryParams .= '&' . urlencode('estatedvlprCmpnm') . '=' . urlencode('에이치디씨아이앤콘스주식회사'); /**/
// $queryParams .= '&' . urlencode('rprsntv') . '=' . urlencode('육근양'); /**/
// $queryParams .= '&' . urlencode('format') . '=' . urlencode('xml'); /**/
// $queryParams .= '&' . urlencode('numOfRows') . '=' . urlencode('10'); /**/
// $queryParams .= '&' . urlencode('pageNo') . '=' . urlencode('1'); /**/
// $url.=$queryParams;
// curl_setopt($ch, CURLOPT_URL, $url);
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
// curl_setopt($ch, CURLOPT_HEADER, FALSE);
// curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

// $response = curl_exec($ch);
// curl_close($ch);

// $object = simplexml_load_string($response);
// $items = $object->fields->field;

// foreach ($items as $item) {
// 	echo($item->bsnsConfirmationPrmisnAdmns)."<br>";
//     echo($item->strwrkDe)."<br>"."<br>";
// }

class OpenData
{
    public function __construct($serviceKey)
    {
        $this->serviceKey=$serviceKey;
    }
    #curl-get
    public function curlGetXml($url, $data)
    {
        $queryParams='?' . urlencode('serviceKey') . "=".$this->serviceKey; /*Service Key*/
        foreach($data as $k=>$v)
        {
            $queryParams.= '&'.urlencode($k).'='.urlencode($v);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url. $queryParams);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        
        $response=curl_exec($ch);
        curl_close($ch);
        
        return simplexml_load_string($response);
    }

}
try
{
    define('SERVICE_KEY',"gSWZvFxo%2BIU1Re2UFuFBG51U1NbimqJkvNzSq426%2Fy9fZiYWVlQCvM5rTuRQ3BFf5UUga%2FSjmgrFrHHoVeV9zw%3D%3D");
    define('SERVICE_DKEY',"gSWZvFxo%2BIU1Re2UFuFBG51U1NbimqJkvNzSq426%2Fy9fZiYWVlQCvM5rTuRQ3BFf5UUga%2FSjmgrFrHHoVeV9zw%3D%3D");

    $openData=new OpenData(SERVICE_KEY);
}
catch(Exception $e)
{
    $msg=$e->getMessage();
    alertBack($msg);
    exit;
}

$url="http://apis.data.go.kr/1611000/nsdi/EstateDevlopService/attr/getEDBusinessResultsInfo";
$data=array(
    'ldCode'=>41,
    'estatedvlprCmpnm'=>"에이치디씨아이앤콘스주식회사",
    'rprsntv'=>'육근양',
    'format'=>'xml',
    'numOfRows'=>10,
    'pageNo'=>1
);

$object=$openData->curlGetXml($url,$data);
$items = $object->fields->field;
foreach($items as $item)
{
	echo($item->bsnsConfirmationPrmisnAdmns)."<br>";
    echo($item->strwrkDe)."<br>"."<br>";
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="/kko/js/Chart.min.js"></script>
    <script src="/kko/js/utils.js"></script>
    <title>Document</title>
</head>
<body>
<div id="container" style="width: 800px;height:480px;">
        <canvas id="canvas" ></canvas>
</div>
<div></div>
<script>
        var color = Chart.helpers.color;
        var ChartData = {            
            labels: ['1월', '2월', '3월', '4월', '5월', '6월', '7월', '8월', '9월', '10월', '11월', '12월'], // 챠트의 항목명 설정
            datasets: [{
                label: '영업1팀',  // 데이터셑의 이름
                pointRadius: 15, // 꼭지점의 원크기
                pointHoverRadius: 30, // 꼭지점에 마우스 오버시 원크기                                   
                backgroundColor: color(window.chartColors.red).alpha(0.5).rgbString(), // 챠트의 백그라운드 색상
                borderColor: window.chartColors.red, // 챠트의 테두리 색상
                borderWidth: 1, // 챠트의 테두리 굵기
                lineTension:0, // 챠트의 유연성( 클수록 곡선에 가깝게 표시됨)
                fill:false,  // 선챠트의 경우 하단 부분에 색상을 채울지 여부                  
                data: [18,21,13,44,35,26,54,17,32,23,22,35,0]  // 해당 데이터셋의 데이터 리스트
            }
            // , {
            //     label: '영업2팀', 
            //     pointRadius: 5,
            //     pointHoverRadius: 10,                                    
            //     backgroundColor: color(window.chartColors.blue).alpha(0.5).rgbString(), 
            //     borderColor: window.chartColors.green, 
            //     borderWidth: 1,
            //     lineTension:0, 
            //     fill:false,                   
            //     data: [31,24,23,42,25,14,37,21,13,44,35,23,0] // 해당 데이터셋의 데이터 리스트
            // }
        ]
 
        };
 
        window.onload = function() {
            var ctx = document.getElementById('canvas').getContext('2d');
            window.myHorizontalBar = new Chart(ctx, {
                // type 을 변경하면 선차트, 가로막대차트, 세로막대차트 등을 선택할 수 있습니다 
                // ex) horizontalBar, line, bar, pie, bubble
                type: 'line', 
                data: ChartData,
                options: {
                    responsive: true,                    
                    maintainAspectRatio: false    ,
                    title: {
                        display: true,
                        text: '2021년 영업현황'
                    }
                }
            });
 
        };
    </script>
</body>
</html>

