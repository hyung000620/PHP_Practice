<?
$HEADER_INFO = apache_request_headers();

//print_r( $headers );
//echo $HEADER_INFO["CALL-FROM"];
//echo $HEADER_INFO["CALL-TIME"];
$CALL_FROM=$HEADER_INFO["CALL-FROM"];
if(!$CALL_FROM) $CALL_FROM="NONE";

$bgn_stmp=strtotime("-5 minutes");
$end_stmp=strtotime("+5 minutes");
$sol_stmp=$HEADER_INFO["CALL-TIME"];

if($sol_stmp > $bgn_stmp && $sol_stmp < $end_stmp) $AUTH="YES"; 
else $AUTH="NO";

define("CALL_FROM",$CALL_FROM);
define("SOLAR_AUTH",$AUTH);

//echo CALL_FROM;
//echo SOLAR_AUTH;
$url=urldecode($_GET['url']);
$_path=parse_url($url,PHP_URL_PATH);
$_query=parse_url($url,PHP_URL_QUERY);
$_queryArr=explode("&", $_query);
foreach($_queryArr as $q)
{
	list($k,$v)=explode("=",$q);
	${$k}=$v;
}

$ref_from="solar";
$client_id="solar";

include $_SERVER["DOCUMENT_ROOT"].$_path; 
?>
