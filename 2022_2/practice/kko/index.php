<?
include($_SERVER["DOCUMENT_ROOT"]."/kko/inc/header.php");
?>
<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script type="text/javascript" src="/js/jquery-3.5.1.min.js"></script>

    <title>지도</title>
    <style>
    body {margin: 0px;}


    </style>
</head>

<body>
    <div style="position:absolute; z-index:9;">
        <button style="padding:10px"onclick="viewMarker()">viewMarker</button>
        <button style="padding:10px"onclick="delMarker()">delMarker</button>
    </div>
    <div id="map" style="width:100vw;height:100vh;"></div>
    <!-- 로딩 -->
    <div id="loading" class="loading"></div>
</body>

</html>
<script type="text/javascript" src="//dapi.kakao.com/v2/maps/sdk.js?appkey=a30c6fd53e4bf3803fa62cc90efc4d13&libraries=clusterer"></script>
<script>
var mapContainer = document.getElementById('map'),mapOption = {center: new kakao.maps.LatLng(37.566826, 126.9786523),level: 5};
var map = new kakao.maps.Map(mapContainer, mapOption);
var clusterer = new kakao.maps.MarkerClusterer({map: map,averageCenter: true,minLevel: 10 });
var markers = [];
$(document).ready(function() {

$('#loading').hide();

$('#myform').submit(function(){
    $("#loading").css({
        "top": (($(window).height()-$("#loading").outerHeight())/2+$(window).scrollTop())+"px",
        "left": (($(window).width()-$("#loading").outerWidth())/2+$(window).scrollLeft())+"px"
     }); 
    $('#loading').show();
    return true;

});

});
function delMarker()
{
    let cnt = markers.length;
    for(i=0; i<cnt; i++){markers[i].setMap(null);}
} 
function viewMarker() 
{
    $.get("/kko/kkoXY.php", function(positions) {

        // var imageSrc = "https://t1.daumcdn.net/localimg/localimages/07/mapapidoc/markerStar.png"; 
        // for (var i = 1; i < positions.length; i++) {            
        //     var imageSize = new kakao.maps.Size(24, 35);
        //     var markerImage = new kakao.maps.MarkerImage(imageSrc, imageSize);
        //     var latlng = new kakao.maps.LatLng(positions[i].y, positions[i].x);
        //     var marker = new kakao.maps.Marker({
        //         map: map, // 마커를 표시할 지도
        //         position: latlng, // 마커를 표시할 위치
        //         title: positions[i].si_nm, // 마커의 타이틀, 마커에 마우스를 올리면 타이틀이 표시됩니다
        //         image: markerImage // 마커 이미지 
        //     });
        // }
        $(positions).map(function(i, position) {
            var val = new kakao.maps.Marker({
                map:map,
                position : new kakao.maps.LatLng(position.y, position.x)
            });
            markers.push(val);
            return val;
        });
        // 클러스터러에 마커들을 추가합니다
        // clusterer.addMarkers(markers);
    });
}
</script>