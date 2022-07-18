<?
#회사
define("TK_CEO", "이현민", true);
define("TK_SITE_NAME", "TANKEDU", true);
define("TK_COMPANY", "탱크교육", true);
define("TK_DOMAIN", "www.tankauction.com", true);
define("TK_PHONE", "02-456-1544", true);
define("TK_MAIL", "contact@tankauction.com", true);
define("TK_BIZNO", "477-87-01272", true);
define("TK_ADDR", "서울시 광진구 광나루로56길 85, 11층3호(구의동, 테크노마트)", true);
define("TK_XY", "37.5357010613,127.0957646564", true);

#cert
$_site_name="탱크교육";
$_domain_ssl="http://www.withtank.com";

#전자지도(이수원키)
$_daumKey="801d6429922f618c759bf61bb183d915";

#SMS
$_sms_site="tankauction";
$_sms_callback="02-456-1544";

/*
#결제 이니시스로 가능하다고 함
$tk_bank_own="사단법인 위드탱크";
$bank_arr=array(10 => array("name"=>"국민은행","no"=>" 361401-04-164149"));
$tossPayIP=array("13.124.18.147","13.124.108.35","3.36.173.151","3.38.81.32","222.104.203.3");
*/

$tk_bank_own="탱크교육";
$bank_arr=array(10 => array("name"=>"국민은행","no"=>" 361401-04-164149"));

//모바일 관련 판단
$mobile_agent=false; $ios_agent=false; $chrome_agent=false;
if(preg_match('/(iphone|ipad|ipod)/i',$_SERVER["HTTP_USER_AGENT"])){$ios_agent=true;}
if(preg_match('/(iphone|ipad|ipod|samsung|lgtel|mobile|android|symbian|blackberry)/i',$_SERVER["HTTP_USER_AGENT"])){$mobile_agent=true;}
if(preg_match('/Chrome/i',$_SERVER["HTTP_USER_AGENT"])){$chrome_agent=true;}

$_ver="1.000";
?>