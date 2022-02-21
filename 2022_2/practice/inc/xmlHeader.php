<?
session_cache_limiter('no-cache, must-revalidate');
session_start();

header("Content-type: application/xml; charset=utf-8");
include($_SERVER["DOCUMENT_ROOT"]."/inc/paramChk.php");
include($_SERVER["DOCUMENT_ROOT"]."/inc/pdo.php");

setlocale(LC_ALL, "ko_KR.utf-8");
?>