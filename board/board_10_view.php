<?
include $_SERVER["DOCUMENT_ROOT"]."/inc/header.php";
    
    $idx = $_GET['idx'];
    $sql = "SELECT A.*,B.sav_file FROM tc_board A, tc_inc_files B WHERE A.idx= B.ref_idx AND A.idx = {$idx} AND A.board_id = 'gnews' AND del = 0 AND B.seq = 1";
    $stmt = $pdo -> prepare($sql);
    $stmt -> execute();
    $rs = $stmt -> fetch();
    $date = explode(" ",$rs['wdate']);

    $title = $rs['title'];
    $content = $rs['content'];
    $content = $rs['content'];
    $wdate = $date[0];
?>
<style>
    #detail_container {
        width: 1200px;
        height: 100%;
        margin: 0 auto;
    }

    .detail_header {
        /* position: relative; */
        box-sizing: border-box;
        width: 100%;
        height: 140px;
        padding: 50px;
    }

    .detail_title{
        width: 80%;
        float: left;
        font-size: 28px;
        font-weight: bold;
    }

    .detail_wdate{
        width: 10%;
        position: relative;
        top:10px;
        float: right;
    }
    
    .detail_img{
        display: block;
        margin: 0 auto;
        margin-top: 120px;
    }

    .detail_text {
        margin: 100px 0 120px 0;
        font-size: 18px;
    }

    .hr{
        width: 100%;
        height: 1px;
        /* margin-bottom: 80px; */
        background-color: rgb(229, 230, 232);
    }
    .btn_wrap{
        text-align: center;
    }
    
    .board_prev_next{
        width: 100%;
        height: 140px;
        margin: 60px 0 200px 0;
    }

    .board_prev , .board_next{
        height: 50%;
        cursor: pointer;
    }

    .prev , .next {
        width: 10%;
        height: 100%;
        float: left;
        text-align: center;
        font-size: 16px;
        font-weight: bold;
        padding-top:22px;
        box-sizing: border-box;
    }
    .prev_text , .next_text {
        width: 10%;
        height: 100%;
        float: left;
        text-align: center;
        font-size: 16px;
        font-weight: bold;
        padding-top:22px;
        box-sizing: border-box;
    }
    .prev_title , .next_title {
        width: 65%;
        height: 100%;
        float: left;
        font-size: 16px;
        padding-top:22px;
        box-sizing: border-box;
    }
    .date{
        width: 15%;
        height: 100%;
        float: right;
        text-align: center;
        padding:24px;
        box-sizing: border-box;
    }
    #no {
        cursor: auto;
    }
</style>
<?
    // 이전글 , 다음글 SQL 문
    $pn_sql = "SELECT * FROM tc_board WHERE board_id = 'gnews' AND del = 0 AND idx IN((SELECT idx from tc_board where board_id = 'gnews' AND idx < {$idx} ORDER by idx desc LIMIT 1),(SELECT idx from tc_board where board_id = 'gnews' AND idx > {$idx} ORDER by idx LIMIT 1))";
    $pn_stmt = $pdo -> prepare($pn_sql);
    $pn_stmt -> execute();
    $prev_next = $pn_stmt -> fetchAll();

    // 앞뒤로 idx 가 있을 때
    if(!empty($prev_next[0]['idx']) && !empty($prev_next[1]['idx'])) {
        $prev_idx = $prev_next[0]['idx'];
        $next_idx = $prev_next[1]['idx'];
        
        $prev_title = $prev_next[0]['title'];
        $next_title = $prev_next[1]['title'];

        $date_1 = explode(' ',$prev_next[0]['wdate']);
        $date_2 = explode(' ',$prev_next[1]['wdate']);
        
        $prev_date = $date_1[0];
        $next_date = $date_2[0];
    } 
    // prev는 있고 next가 없을 때
    else if (!empty($prev_next[0]['idx']) && empty($prev_next[1]['idx'])) {
        // 이전글만
        if($idx > $prev_next[0]['idx']) {
            $prev_idx = $prev_next[0]['idx'];
            $prev_title = $prev_next[0]['title'];
            $date_1 = explode(' ',$prev_next[0]['wdate']);
            $prev_date = $date_1[0];
        // 다음글만
        } else {
            $next_idx = $prev_next[0]['idx'];
            $next_title = $prev_next[0]['title'];
            $date_1 = explode(' ',$prev_next[0]['wdate']);
            $next_date = $date_1[0];
        }
    } 
?>
<div id='detail_container'>
    <div>
        <div class='detail_header'>
            <div class='detail_title'><?=$title?></div>
            <div class='detail_wdate'><?=$wdate?></div>
        </div>
        <div class='hr'></div>
        <div class='detail_content'>
            <div class='detail_text'>
                <?= $content;?>
            </div>
        </div>
        <div class='hr'></div>
        <div class='btn_wrap'>
            <button class='btn_box_ss btn_tank radius_20' style="width:300px;font-size:25px; margin-top:60px;" onclick="location.href='board_10.php'">목록</button>
        </div>
        <div class='board_prev_next'>
            <div class='hr'></div>
            <?if(!$prev_idx) :?>
            <div class='board_prev' id="no">
                <div class='prev'>∧</div>
                <div class='prev_text'>다음글</div>
                <div class='prev_title'>다음글이 존재하지 않습니다.</div>
                <div class='date'></div>
            </div>
            <div class='hr'></div>
            <?else :?>
            <div class='board_prev' onclick="location.href='board_10_view.php?idx=<?=$prev_idx?>'">
                <div class='prev'>∧</div>
                <div class='prev_text'>다음글</div>
                <div class='prev_title'><?=$prev_title?></div>
                <div class='date'><?=$prev_date?></div>
            </div>
            <div class='hr'></div>
            <?endif;?>

            <?if(!$next_idx) :?>
            <div class='board_next' id="no">
                <div class='next'>∧</div>
                <div class='next_text'>이전글</div>
                <div class='next_title'>이전글이 존재하지 않습니다.</div>
                <div class='date'></div>
            </div>
            <div class='hr'></div>
            <?else :?>
            <div class='board_next' onclick="location.href='board_10_view.php?idx=<?=$next_idx?>'">
                <div class='next'>∨</div>
                <div class='next_text'>이전글</div>
                <div class='next_title'><?=$next_title?></div>
                <div class='date'><?=$next_date?></div>
            </div>
            <div class='hr'></div>
            <?endif;?>
        </div>
    </div>
</div>
<?
    include_once($_SERVER['DOCUMENT_ROOT']."/heon/inc/footer.php");
?>
