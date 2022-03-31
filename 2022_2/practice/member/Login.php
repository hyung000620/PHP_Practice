<!-- 로그인요청 -->
<?
// 네이버 로그인 접근토큰 요청 ========================================
//   $client_id = "PoskWV9GbR_omejAmIRx";
//   $redirectURI = urlencode("https://kb.tankauction.com/member/naverLogin_callback.php");
//   $state = "RAMDOM_STATE";
//   $apiURL = "https://nid.naver.com/oauth2.0/authorize?response_type=code&client_id=".$client_id."&redirect_uri=".$redirectURI."&state=".$state;

// 카카오 로그인 접근토큰 요청 ========================================
$client_id = "2168332007ec077a155573867ada4195";
$redirectURI = urlencode("https://kb.tankauction.com/member/socialLogin_callback.php");
$state = "RAMDOM_STATE";
$apiURL = "https://kauth.kakao.com/oauth/authorize?client_id=".$client_id."&redirect_uri=".$redirectURI."&response_type=code&state=".$state;

?>
<!-- <a href="<? //=$apiURL ?>"><img height="50" src="http://static.nid.naver.com/oauth/small_g_in.PNG"/></a> -->
<a href="<?=$apiURL;?>">카카오 로그인</a>


<!-- 콜백페이지 -->

<?php
// 네이버 ==============================================
//   $client_id = "PoskWV9GbR_omejAmIRx";
//   $client_secret = "wHJEK2bRFQ";
//   $code = $_GET["code"];;
//   $state = $_GET["state"];;
//   $redirectURI = urlencode("https://kb.tankauction.com/member/naverLogin_callback.php");
//   $url = "https://nid.naver.com/oauth2.0/token?grant_type=authorization_code&client_id=".$client_id."&client_secret=".$client_secret."&redirect_uri=".$redirectURI."&code=".$code."&state=".$state;
//   $is_post = false;
//   $ch = curl_init();
//   curl_setopt($ch, CURLOPT_URL, $url);
//   curl_setopt($ch, CURLOPT_POST, $is_post);
//   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//   $headers = array();
//   $response = curl_exec ($ch);
//   $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
//   echo "status_code:".$status_code."";
//   curl_close ($ch);
//   if($status_code == 200) {
//     echo $response;
//   } else {
//     echo "Error 내용:".$response;
//   }

// 카카오 =================================================================
  $client_id = "2168332007ec077a155573867ada4195";
//   $client_secret = "wHJEK2bRFQ";
  $code = $_GET["code"]; //사용자 토큰 정보
  $state = $_GET["state"];
  $redirectURI = urlencode("https://kb.tankauction.com/member/socialLogin_callback.php");
  $url = "https://nid.naver.com/oauth2.0/token?grant_type=authorization_code&client_id=".$client_id."&client_secret=".$client_secret."&redirect_uri=".$redirectURI."&code=".$code."&state=".$state;
  $is_post = false;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_POST, $is_post);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $headers = array();
  $response = curl_exec ($ch);
  $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  echo "status_code:".$status_code."";
  curl_close ($ch);
  if($status_code == 200) {
    echo $response;
  } else {
    echo "Error 내용:".$response;
  }


?>


