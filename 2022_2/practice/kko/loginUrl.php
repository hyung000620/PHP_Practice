<?
include($_SERVER["DOCUMENT_ROOT"]."/kko/Login.php");

  // 네이버 로그인 접근토큰 요청 예제
//   $client_id = "PoskWV9GbR_omejAmIRx";
//   $redirectURI = "https://kb.tankauction.com/kko/callback.php";
//   $state = "RAMDOM_STATE";
//   $apiURL = "https://nid.naver.com/oauth2.0/authorize?response_type=code&client_id=".$client_id."&redirect_uri=".$redirectURI."&state=".$state;

//   $restAPIKey = "2168332007ec077a155573867ada4195"; //본인의 REST API KEY를 입력해주세요
//   $callbacURI = urlencode("https://kb.tankauction.com/kko/callback.php"); //본인의 Call Back URL을 입력해주세요
//   $kakaoLoginUrl = "https://kauth.kakao.com/oauth/authorize?client_id=".$restAPIKey."&redirect_uri=".$callbacURI."&response_type=code";

?>
<a href="<?= $login->getLoginUrl("kakao") ?>">카카오톡으로 로그인</a>

<a href="<?= $login->getLoginUrl("naver") ?>"><img height="50" src="http://static.nid.naver.com/oauth/small_g_in.PNG"/></a>