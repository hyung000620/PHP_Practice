<?
function validChk($str)
{
	$attack=false;
	
	$pattern ="/(database\(\))|([,\(]0x\w{2,})|(0x\w{2,}[,\)])|(\/\*s\*\/)|(information_schema.tables)|(hex\()|(char\(\d+.*?\))|(chr\(\d+\))";
	$pattern.="|(sleep\(\d+\))|(benchmark\(\d+)|(case\s+when\s+)|(convert\(int)|(if.*?\(\d+=\d+)|(then.*else)|(drop.*function)|(cast\(.*?\))|(select.*?from.*\w+)|(union.*?all)";
	$pattern.="|(\d+['\\\].*?\(\)\(\))";
	$pattern.="/i";
	
	$str=preg_replace("/\s+\(\s+/","(",$str);
	$str=preg_replace("/\s+\)\s+/",")",$str);
	
	preg_match_all($pattern,$str,$match1);
	if($match1[0])
	{
		goto_error_page();
		exit;
	}
	//echo urldecode($str)."<br>";
	preg_match_all($pattern,urldecode($str),$match2);
	if($match2[0])
	{
		goto_error_page();
		exit;
	}
}


function goto_error_page(){echo "<script type='text/javascript'>location.href='/error.php?error_msg=SI';</script>";}

$param_arr=array();
if($_GET)
{
	foreach($_GET as $k => $v)
	{
		$val=trim($v);
		validChk($val);
		${$k}=$val;
		if($val!="") $param_arr[]="{$k}={$val}";
	}	
}

if($_POST)
{
	foreach($_POST as $k => $v)
	{
		$val=trim($v);
		validChk($val);
		${$k}=$val;
		if($val!="") $param_arr[]="{$k}={$val}";
	}	
}
$param_str=implode("&",$param_arr);
$PHP_SELF=htmlentities($_SERVER['PHP_SELF']);
?>
