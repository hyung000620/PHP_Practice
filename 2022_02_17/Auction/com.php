<?
    $page = 1;
    include_once($_SERVER["DOCUMENT_ROOT"]."/practice/inc/header.php");
    include_once($_SERVER["DOCUMENT_ROOT"]."/practice/inc/asd.php");

?>
<main>
    <div class="main">
        <span>경매>종합검색</span>
        <hr>
        <form name="c_search1" id="c_search1" class="search_form">
            <input type="hidden" name="mode" id="mode" value="json">
                <br>
                <div>날짜로 검색</div>
                <br>
                <input class="in" type="text" id="datepicker1" name="datepicker1">
                ~
                <input class="in" type="text" id="datepicker2" name="datepicker2">
        </form>
        <button class="search" id="csBtn" >검색</button>
        <div class="list_box">
            <table class="tbl_c_list">
                <thead id="lsThead"></thead>
                <tbody id="lsTbody"></tbody>
            </table>
        </div>
    </div>
</main>
<?
    include_once($_SERVER["DOCUMENT_ROOT"]."/practice/inc/footer.php");

?>
<script src="../js/calendar.js"></script>
<script src="../js/c_search.js?ver=<?=$_ver?>"></script>
