<?
if($_COOKIE["GAC_ID"] || $_SESSION["GAS_ID"])
{
	$client_id=($_COOKIE["GAC_ID"]) ? $_COOKIE["GAC_ID"] : $_SESSION["GAS_ID"];
	$client_name=($_COOKIE["GAC_NAME"]) ? $_COOKIE["GAC_NAME"] : $_SESSION["GAS_NAME"];
	$client_ssid=($_COOKIE["GAC_SSID"]) ? $_COOKIE["GAC_SSID"] : $_SESSION["GAS_SSID"];
	$client_level=($_COOKIE["GAC_LEVEL"]) ? $_COOKIE["GAC_LEVEL"] : $_SESSION["GAS_LEVEL"];
	$client_cpn_code=($_COOKIE["GAC_CPN_CODE"]) ? $_COOKIE["GAC_CPN_CODE"] : $_SESSION["GAS_CPN_CODE"];
	$client_ptnr_pm=($_COOKIE["GAC_PTNR_PM"]) ? $_COOKIE["GAC_PTNR_PM"] : $_SESSION["GAC_PTNR_PM"];
	$client_id=base64_decode($client_id);
	$client_name=base64_decode($client_name);
	$client_ssid=base64_decode($client_ssid);
	$client_level=base64_decode($client_level);
	$client_cpn_code=base64_decode($client_cpn_code);
	$client_ptnr_pm=base64_decode($client_ptnr_pm);
}

if($member_only==true && !$client_id)
{
	echo "
	<script type='text/javascript'>
		alert('로그인 후 이용해 주세요~');
		if(window.opener) window.close();
		else history.back();
	</script>";
	exit;
}

if($cpn_deny==true && $client_id=="CPN_USER")
{
	echo "
	<script type='text/javascript'>
		alert('쿠폰사용자는 제한된 컨텐츠 입니다.');
		if(window.opener) window.close();
		else history.back();
	</script>";
	exit;
}
?>
