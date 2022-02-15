<?
function validChk($str)
{
    $attack = false;

    $pattern ="/(database\(\))|([,\(]0x\w{2,})|(0x\w{2,}[,\)])|(\/\*s\*\/)|(information_schema.tables)|(hex\()|(char\(\d+.*?\))|(chr\(\d+\))";
	$pattern.="|(sleep\(\d+\))|(benchmark\(\d+)|(case\s+when\s+)|(convert\(int)|(if.*?\(\d+=\d+)|(then.*else)|(drop.*function)|(cast\(.*?\))|(select.*?from.*\w+)|(union.*?all)";
	$pattern.="|(\d+['\\\].*?\(\)\(\))";
	$pattern.="/i";

    /*
        '/\s+\(\s+/'에 해당하는 값들을 '(',')'로 대체한다.
    */
    $str=preg_replace("/\s+\(\s+/","(",$str);
	$str=preg_replace("/\s+\)\s+/",")",$str);

    /*
        '$str'에서 '$pattern' 패턴에 맞는 것을 $match1에 배열타입으로 추출.
    */
    preg_match_all($pattern, $str, $match1);

    if($match1[0])
	{
		//echo $str."<br>";
		//print_rs($match1[0]);
		goto_error_page();
		exit;
	}
    /*
        urldecode - URL 인코드된 문자열을 디코드.
        // 인코딩이란 정보를 부호화/ 암호화 시킨다, 디코딩은 그 부호화/암호화를 해제한다는 뜻을 가집니다.
    */
    preg_match_all($pattern,urldecode($str),$match2);
	if($match2[0])
	{
		//echo $str."<br>";
		//print_r($match2[0]);
		goto_error_page();
		exit;
	}
}

function goto_error_page(){echo "<script type='text/javascript'>location.href='/error.php?error_msg=SI';</script>";}

$param_arr = array();

if($_GET)
{
    /*
        $_GET이라는 배열의 키와 값을 모두 사용하고 싶을 때,
        $key => $value 이런식으로 사용하면 된다.
        여기서 $key는 배열의 변수명으로 보면되고,
        $value 는 그 변수명이 가지고 있는 값으로 보면 된다.
    */
    foreach($_GET as $k => $v)
    {
        $val = trim($v);
        validChk($val);
        ${$k}=$val; //가변 변수 . 변수의 이름을 동적으로 만들 수 있는 유연함을 가지고 있습니다.
        if($val !="") $param_arr[] = "{$k}={$val}";
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
/*
    implode 함수는 배열의 값들을 특정 구분자를 사용하여 문자열로 변환해주는 함수.
    explode 함수는 특정 구분자로 구분되어 있는 문자열을 구분자를 기준으로 나누어 배열로 변환해주는 함수.
*/
$param_str=implode("&",$param_arr);

/*
    $_SERVER['PHP_SELF']는 폼에서 데이터를 던지고 받을 때 다른 php 파일에 넘기는 것이 아니라
    현재 페이지에 데이터를 던지고 받을 때 사용.
*/
/*
    htmlentities는 말그대로 매개변수를 html엔티티 값으로 변환해주는 것을 의미한다.
    엔티티 코드를 사용하는 이유는 컴퓨터가 문서를 읽어들일 때 예약문자와 문서내용을 
    구분하지 못해 생기는 문제를 해결하기 위해서 입니다. 예를 들어 문서 내용에 꺽쇠 
    괄고(<) 가 붙어 있다면 컴퓨터는 문서를 읽어 들일 때 꺽쇠 괄호를 HTMl 태그 시작 기호로 받아 들입니다
*/
$PHP_SELF=htmlentities($_SERVER['PHP_SELF']);
?>