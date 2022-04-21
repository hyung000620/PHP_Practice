<?
include($_SERVER["DOCUMENT_ROOT"]."/inc/header.php");
include($_SERVER["DOCUMENT_ROOT"]."/kko/Login.php");
$code = $_GET["code"];
$state = $_GET["state"];
$kakao = $login->getCurlInfo($code); // 카카오
$naver = $login->getCurlInfo($code,$state); //네이버

if($state){print_r($naver);}
else{print_r($kakao);}


/*********************************
 * 네이버 로그인
 * *******************************
 */
// $client_id = "PoskWV9GbR_omejAmIRx";
// $client_secret = "wHJEK2bRFQ";
// $redirectURI = "https://kb.tankauction.com/kko/callback.php";
// $url = "https://nid.naver.com/oauth2.0/token?grant_type=authorization_code&client_id=".$client_id."&client_secret=".$client_secret."&redirect_uri=".$redirectURI."&code=".$code."&state=".$state;
// $is_post = false;
// $ch = curl_init();
// curl_setopt($ch, CURLOPT_URL, $url);
// curl_setopt($ch, CURLOPT_POST, $is_post);
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// $headers = array();
// $response = curl_exec($ch);
// $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
// echo "status_code:".$status_code."<br>";
// curl_close ($ch);



// if($status_code == 200) {
//   $responseArr=json_decode($response,true);

//   $headers = array('Content-Type: application/json', sprintf('Authorization: Bearer %s',$responseArr['access_token']));
//   $is_post = false;
//   $me_ch= curl_init();
//   curl_setopt($me_ch, CURLOPT_URL, "https://openapi.naver.com/v1/nid/me"); 
//   curl_setopt($me_ch, CURLOPT_POST, $is_post ); 
//   curl_setopt($me_ch, CURLOPT_HTTPHEADER, $headers); 
//   curl_setopt($me_ch, CURLOPT_RETURNTRANSFER, true); 
//   $res = curl_exec ($me_ch); 
//   curl_close ($me_ch); 
//   $resData= json_decode($res,true);
//   $id=$resData['response']['id'];
//   $email=$resData['response']['email'];
//   $name=$resData['response']['name'];
//   $mobile=$resData['response']['mobile'];

//   if($id)
//   {
//     $SQL="SELECT * FROM {$my_db}.tm_member WHERE id = '{$email}'";
//     $stmt=$pdo->$prepare($SQL);
//     $stmt->execute();
//     $rs=$stmt->fetch();
//     if($rs)
//     {
//       //기존 회원 -> 바로 로그인
//       echo("<meta http-equiv='refresh' content='0;URL=/'>");
//       exit;
//     }
//     else
//     {
//       //신규 회원 -> 가입 후 로그인
//       $SQL="INSERT INTO {$my_db}.tm_member SET id='{$email}', email='{$email}', mobile='{$mobile}', name='{$name}'";
//       $stmt=$pdo->prepare($SQL);
//       $stmt->execute();
//       echo("<meta http-equiv='refresh' content='0;URL=/'>");
//       exit;
//     }
//   }
// } else {
//   echo "Error 내용:".$response;
// }
/*********************************
 * 카카오 로그인
 * *******************************
 */
// $returnCode = $_GET["code"]; 
// $restAPIKey = "2168332007ec077a155573867ada4195"; 
// $callbacURI = urlencode("https://kb.tankauction.com/kko/callback.php"); 
// //토큰
// $getTokenUrl = "https://kauth.kakao.com/oauth/token?grant_type=authorization_code&client_id=".$restAPIKey."&redirect_uri=".$callbacURI."&code=".$returnCode;

// $isPost = false;
// $ch = curl_init();                                    
// curl_setopt($ch, CURLOPT_URL, $getTokenUrl);          
// curl_setopt($ch, CURLOPT_POST, $isPost);              
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);      
// $headers = array();                                  
// $loginResponse = curl_exec ($ch);                               
// $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
// echo "status_code:".$status_code."<br>";
// curl_close ($ch);                                     

// //사용자 정보 요청
// $accessToken= json_decode($loginResponse)->access_token;     
// $header = "Bearer ".$accessToken; 
// $getProfileUrl = "https://kapi.kakao.com/v2/user/me"; 

// $ch = curl_init();
// curl_setopt($ch, CURLOPT_URL, $getProfileUrl);
// curl_setopt($ch, CURLOPT_POST, $isPost);
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// $headers = array();
// $headers[] = "Authorization: ".$header;
// curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

// $profileResponse = curl_exec ($ch);
// $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
// curl_close ($ch);

// $profileResponse = json_encode($profileResponse,JSON_UNESCAPED_UNICODE);

// echo($profileResponse);


?>  