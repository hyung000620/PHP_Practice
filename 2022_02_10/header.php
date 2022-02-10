<?
    session_cache_limiter('no-cache, must-revalidate');
    session_start();
    include $_SERVER["DOCUMENT_ROOT"] . "/inc/cfg.php";
    include $_SERVER["DOCUMENT_ROOT"] . "/inc/pdo.php";

?>
<link rel="stylesheet" href="../../css/header.css">

<div class="header">
    <div>
        <a href="/">
            <img src="../../image/tank.png">
        </a>
    </div>
    <div>
        <?
            if(!isset($_SESSION['user_id'])){
                echo "<a href='/view/section/login.php'>로그인</a>";
            }else{
                $user_id = $_SESSION['user_id'];
                $user_name = $_SESSION['user_name'];
                echo "<a href='../../user/login/logout.php'>로그아웃</a>";
            }
        ?>
        <a href="/view/section/join.php">회원가입</a>
    </div>
</div>