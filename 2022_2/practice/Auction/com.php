<?
    $page = 1;
    include_once($_SERVER["DOCUMENT_ROOT"]."/practice/inc/header.php");
    include_once($_SERVER["DOCUMENT_ROOT"]."/practice/inc/asd.php");

?>
<main>
    <div class="main">
        <span>경매>종합검색</span>
        <hr>
        <div></div>
        <form name="c_search1" id="c_search1" class="search_form">
            <table class="tbl_all">
                <tbody>
                    <tr>
                        <th>주소선택</th>
                        <td>
                            <span id="addr1" class="btn_box_l btn_lightgray" onclick="addr_change(0)" style="min-width:40px">주소</span>
                            <span id="addr2" class="btn_box_r btn_lightgray" onclick="addr_change(1)" style="min-width:40px">법원</span>
                        </td>
                        <th>날짜로 검색</th>
                        <td>
                            <br>
                            <input type="hidden" id="mode" name="mode" value="json">
                            <input class="in" type="text" id="datepicker1" name="datepicker1">
                            ~
                            <input class="in" type="text" id="datepicker2" name="datepicker2">
                        </td>
                    </tr>
                </tbody>
            </table>
        </form>
        <button class="search" id="csBtn">검색</button>
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
<script>
    var TK = eval("({})");
</script>
<script>
    $('#addr1').click( function(){
        $('#addr1').css('background','#1B43A9');
        $('#addr2').css('background','#e7e7e7');
    });
    $('#addr2').click( function(){
        $('#addr2').css('background','#1B43A9');
        $('#addr1').css('background','#e7e7e7');
    });
</script>
<script src="../js/calendar.js"></script>
<script src="../js/c_search.js?ver=<?=$_ver?>"></script>