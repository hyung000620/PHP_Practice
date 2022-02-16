<?
    include_once($_SERVER["DOCUMENT_ROOT"]. "/_test/inc/header.php");
?>
<main>
    <main>
        <section title="검색">
            <form name="fmSrch" id="fmSrch">
                <!--
                    ax_test에 mode 값을 보내기 위한 방법 중 하나. 
                    input type hidden으로 하여 값을 보내는 방식
                -->
                <input type="hidden" name="mode" id="mode" value="xml"> 
                <input type="hidden" name="test" id="test" value="xml value test">

                <div>
                    <span id="search1" class="search">검색1</span></div>
                <div>
                    <span id="search2" class="search">검색2</span></div>
            </form>
        </section>
        <section title="리스트">
            <div class="list_box">
                <table class="tbl_alarm_list">
                    <thead id="lsThead"></thead>
                    <tbody id="lsTbody"></tbody>
                </table>
            </div>
        </section>
    </main>

    <div class="clear"></div>
</main>
<?
    include_once($_SERVER["DOCUMENT_ROOT"]. "/_test/inc/footer.php");
?>
<!-- 
    eval() 함수는 문자열을 javascript로 치환해주는 것. 전역범위로 호출하는 것과 같음.
    해당 함수는 보안에 취약하여 종종 제거해달라고 요청이 들어오는 함수이다. 
    누군가 함부로 손댄 코드를 실행시키게 될수도 있기 때문.
    
    (웹 취약점 우회하기)
    // 기존 eval 함수
    var res = eval(result);

    // 웹 취약점 우회 함수 구현
    var res = (new Function('return'+result))();

-->
<script type="text/javascript">
    var TK = eval("("+"{'mode':'json','pnu':'','test':'xml value test'}"+")");
    //var TK = (new Function('return'+"("+"{'mode':'json','pnu':'','test':'xml value test'}"+")"))();
    console.log(TK);
</script>

<script type="text/javascript" src="/_test/js/test.js?ver=<?=$_ver?>"></script>