<meta
    name="viewport"
    content="width=device-width, height=device-height, minimum-scale=1.0, maximum-scale=1.0, initial-scale=1.0">

<link rel="stylesheet" href="css/login.css">
<div class="main-container">
    <? include 'header.php' ?>
    <br>
    <div>
        <form method='post' action='login_action.php'>
            <div class="input-box">
                <input name="user_id" id="user_id" type="text" placeholder="ID">
                <label for="username">아이디</label>
            </div>
            <div class="input-box">
                <input name="user_pw" id="user_pw" type="password" placeholder="PW">
                <label for="username">비밀번호</label>
            </div>
            <input class="input" type="submit" value="로그인">
        </form>
        
        <div id="forgot"><a href="./join.php" class="a_tag">회원가입</a></div>
    </div>
    <?php
        define('NAVER_CLIENT_ID', 'PoskWV9GbR_omejAmIRx');
        define('NAVER_CLIENT_SECRET', 'wHJEK2bRFQ');
        define('NAVER_CALLBACK_URL', 'http://192.168.10.200/callback_member.php');
        $naver_URL = "https://nid.naver.com/oauth2.0/authorize?response_type=code&client_id=".NAVER_CLIENT_ID."&redirect_uri=".urlencode(NAVER_CALLBACK_URL);   
        ?>
    <br>

    <div>

        <a href="<?=$never_URL?>"><img src="./image/btnG_완성형.png" width="200" height="50"></a>
    </div>

</div>