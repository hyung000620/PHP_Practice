<?
/**
 * Youtube 크롤링
 */
include($_SERVER["DOCUMENT_ROOT"]."/inc/header.php");
// $api_key="AIzaSyDPIzbYV2NtAix7xKTJ9FqPL9P7zoCXHNc";
// $channel_id="UC5I6TYGCaAj7nl0EPAfhd-Q";
// $max_result= 47;

// $url = 'https://www.googleapis.com/youtube/v3/search?order=date&part=snippet&channelId='.$channel_id.'&maxResults='.$max_result.'&key='.$api_key;
// $video_list = json_encode(file_get_contents($url),JSON_UNESCAPED_UNICODE);
// echo $video_list;
// $SQL="INSERT INTO {$my_db}.tx_movie SET you_json={$video_list}";
// $stmt=$pdo->prepare($SQL);
// $stmt->execute();
// $html=implode(" ",$html);
// $url = "https://www.youtube.com/feeds/videos.xml?channel_id=UC5I6TYGCaAj7nl0EPAfhd-Q";
// $curl_connection = curl_init($url);
// curl_setopt($curl_connection, CURLOPT_CONNECTTIMEOUT, 30);
// curl_setopt($curl_connection, CURLOPT_RETURNTRANSFER, true);
// curl_setopt($curl_connection, CURLOPT_SSL_VERIFYPEER, false);
// curl_setopt($curl_connection, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
// $result = curl_exec($curl_connection);
// curl_close($curl_connection);

// $video_list = simplexml_load_string($result);
// $video_list = json_encode($video_list,JSON_UNESCAPED_UNICODE);
// echo $video_list;
// $video_list =json_decode($video_list, true);
// print_r($video_list);
// for ($i=0; $i<4; $i++) {
//   echo $youtube_id = explode(":", $video_list->entry[$i]->id)[2];
//   echo $youtube_url = 'https://www.youtube.com/watch?v='.$youtube_id;
//   echo $youtube_src = 'https://i4.ytimg.com/vi/'.$youtube_id.'/hqdefault.jpg';
// }
// $html=array();
// foreach ($video_list['items'] as $item) {
//     if (isset($item['id']['videoId'])) {
//         $html[]='<div style=\'display:flex; flex-direction: column; background-color:#dadada; width:350px\'>
//                 <iframe width="350" height="200" src="https://www.youtube.com/embed/'.$item['id']['videoId'].'" frameborder="0" allowfullscreen></iframe>
//                 <span>'. $item['snippet']['title'] .'</span>
//             </div>';
//     } 
// }

/* json 내용*/
//{"kind":"youtube#searchListResponse","etag":"lM0LbIAzu0Hlwdkjl7yQHsHP3tA","nextPageToken":"CAEQAA","regionCode":"KR","pageInfo":{"totalResults":47,"resultsPerPage":1},"items":[{"kind":"youtube#searchResult","etag":"0hGiDGdz8cg0okCU-DkbrL6XFvk","id":{"kind":"youtube#video","videoId":"ZIhYTsTiO3s"},"snippet":{"publishedAt":"2020-02-27T11:00:06Z","channelId":"UC5I6TYGCaAj7nl0EPAfhd-Q","title":"'투자도 계획이다' part 03 국토종합계획속 불균형문제 해결방법 l 3주년 이현민대표 특별강연","description":"제5차국토종합계획 #투자도계획이다 #부동산투자 투자의 기본은 손해보지 않는 것입니다. 손해보지 않으려면 싸게 매입하고 가치가 ...","thumbnails":{"default":{"url":"https:\/\/i.ytimg.com\/vi\/ZIhYTsTiO3s\/default.jpg","width":120,"height":90},"medium":{"url":"https:\/\/i.ytimg.com\/vi\/ZIhYTsTiO3s\/mqdefault.jpg","width":320,"height":180},"high":{"url":"https:\/\/i.ytimg.com\/vi\/ZIhYTsTiO3s\/hqdefault.jpg","width":480,"height":360}},"channelTitle":"현민쌤 TV","liveBroadcastContent":"none","publishTime":"2020-02-27T11:00:06Z"}}]}
$SQL ="SELECT * FROM {$my_db}.tx_movie WHERE idx =1";
$stmt=$pdo->prepare($SQL);
$stmt->execute();
$rs=$stmt->fetch();
$html=array();
$video_list= json_decode($rs['you_json'],true);

// foreach($video_list['items'] as $item)
// {
//     print_r($item['snippet']['thumbnails']['medium']['url']);
// }
foreach ($video_list['items'] as $item) {
    // if (isset($item['thumbnails']['medium'])) {
    //     $html[]='<div style=\'display:flex; flex-direction: column; background-color:#dadada; width:350px\'>
    //             <iframe width="350" height="200" src="https://www.youtube.com/embed/'.$item['id']['videoId'].'" frameborder="0" allowfullscreen></iframe>
    //             <span>'. $item['snippet']['title'] .'</span></div>';
    //     echo $item['thumbnails']['medium']['url'];
    // }
    $html[]="<a href='#' class='banner_img'><img src='{$item['snippet']['thumbnails']['medium']['url']}'><p class='hover_text'>{$item['snippet']['title']}</p></a>"; 
}

$html=implode("",$html);

?>
<style>
.youCtn{display:grid;grid-template-columns: repeat(auto-fill, minmax(350px,1fr)); grid-gap:15px; width:100%; padding: 2%;}
img {vertical-align: top;}
.banner_img{display:inline-block;position: relative;}
.banner_img:hover:after,.banner_img:hover > .hover_text{display:block;}
.banner_img:after,.hover_text{display:none;}
.banner_img:after{content:'';position: absolute;top: 0;right: 0;bottom: 0;left: 0;background: rgba(0, 0, 0, 0.3);z-index: 10;}
.banner_img {overflow: hidden;}
/* .banner_img img{height: 340px;} */
.banner_img:hover img{transform: scale(1.2);transition: 1s;}
.hover_text {position: absolute;top: 40px;left: 25px;color: #fff;z-index: 20;font-weight: 600;font-size: 16px;}
</style>
<div class='wrap'>
    <div id=''></div>
    <div class='youCtn'>
    <?=$html?>
    </div>
</div>
<?include($_SERVER["DOCUMENT_ROOT"]."/inc/footer.php");?>
<script>
$(function(){

});

</script>