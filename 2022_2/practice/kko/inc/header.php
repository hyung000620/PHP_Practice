<?
session_cache_limiter('no-cache, must-revalidate');
session_start();
include($_SERVER["DOCUMENT_ROOT"]."/kko/inc/pdo.php");
include($_SERVER["DOCUMENT_ROOT"]."/kko/inc/MysqliDb.php");

## locale설정
setlocale(LC_ALL, "ko_KR.utf-8");
?>