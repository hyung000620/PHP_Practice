<?
    session_cache_limiter('no-cache , must-revalidate');
    session_start();

    include_once($_SERVER["DOCUMENT_ROOT"]."/practice/inc/cfg.php");
    include_once($_SERVER["DOCUMENT_ROOT"]."/practice/inc/arr.php");
    include_once($_SERVER["DOCUMENT_ROOT"]."/practice/inc/pdo.php");
    include_once($_SERVER["DOCUMENT_ROOT"]."/practice/inc/paramChk.php");


    setlocale(LC_ALL, "ko_KR.utf-8")
?>
<!DOCTYPE html>
<html lang="ko">

<head>
    <link rel="stylesheet" href="/practice/css/common.css?ver=<?=$_ver ?>">
    <link rel="stylesheet" href="http://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script type="text/javascript" src="/practice/js/jquery-3.5.1.min.js"></script>
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <title>Document</title>
</head>

<body>
    <header>
        <?
            foreach($header_arr as $k => $v){
                list($title, $url) = explode("|",$v);

                switch($k){
                    case 4:
                    {
                        echo "<span style='padding:10px;maring:0 10px'><a href='{$url}' target='_blank'>{$title}</a></span>";
                        break;
                    }
                    default :
                    {
                    echo "<span style='padding:10px;maring:0 10px'><a href='{$url}'>{$title}</a></span>";
                    }
                }
            } 
        ?>
    </header>