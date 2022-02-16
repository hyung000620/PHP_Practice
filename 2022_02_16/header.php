<?
session_cache_limiter('no-cache','must-revalidate');
session_start();

include_once($_SERVER["DOCUMENT_ROOT"] . "/inc/cfg.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/inc/paramChk.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/inc/pdo.php");

##locale 설정
/*
    locale은 세계 각 국에서 사용하는 언어, 문자, 화폐 표시, 시간등에 대해
    국제화와 지역화를 통해 어떻게 표시할지 정의한 매개 변수의 모음
*/
setlocale(LC_ALL, "ko_KR.utf-8");
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <title>Document</title>
    <script type="text/javascript" src="/_test/js/jquery-3.5.1.min.js"></script>
</head>
<body>
    <header>
        <span>home</span>
        &nbsp; <!-- &nbsp는 띄어쓰기, 즉 공백을 나타내는 특수문자입니다. -->
    </header>
    <aisde>
        aside
    </aisde>