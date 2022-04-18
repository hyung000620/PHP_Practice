<?
include $_SERVER["DOCUMENT_ROOT"]."/inc/header.php";

$SQL="SELECT * FROM {$my_db}.tc_board WHERE board_id='{$board_id}' AND idx='{$idx}'";
$stmt=$pdo->prepare($SQL);
$stmt->execute();
$title="";
while($rs=$stmt->fetch())
{
    $title.=$rs['title'];
    $html.="<div style='text-align:center; height:800px;'>";
    $html.="<div class='right'><img style='cursor:pointer; width: 60px;' id='naver_share' src='https://img1.daumcdn.net/thumb/R1280x0/?scode=mtistory2&fname=http%3A%2F%2Fcfile6.uf.tistory.com%2Fimage%2F2264613856C4A5060B02F8' >
    </div>";
    $html.="<div>";
    $html.= htmlspecialchars_decode($rs['content']);
    $html.="</div>";
    $html.="</div>";
}
$stmt=$pdo->prepare("SELECT * FROM {$my_db}.tc_board WHERE board_id='{$board_id}'");
$stmt->execute();
$numArr=array();
while($rs=$stmt->fetch()){array_push($numArr,$rs['idx']);}
$min=min($numArr);
$max=max($numArr);

?>
<link rel="stylesheet" href="/member/register.css">
<div class='wrap'>
    <input type='button' onclick='changeMeta()'>
    <div class='li_teb'>
        <ul class='ul_teb'>
            <li value='5010' name='notice1' <?if($board_id=='notice1' ){echo"class='on'";}?>>결산안내</li>
            <li value='5020' name='notice2' <?if($board_id=='notice2'){echo" class='on'";}?>>연간기부금</li>
            <li value='5030' name='notice3' <?if($board_id=='notice3'){echo" class='on'";}?>>활용실적</li>
            <li value='5040' name='notice4' <?if($board_id=='notice4'){echo" class='on'";}?>>후원금내용</li>
		</ul>
	</div>
	<div class='clear'></div>
    <div>
        <ul style=" margin-top:30px; border-top:2px solid #1B43A9; border-bottom:2px solid #cccccc">
                <!-- 목록 -->
                <?=$html;?>
        </ul>
        <div class='paging' style="text-align:center; margin-top:30px;">
            <?
                $pre=($idx>$min)?$idx-1:$min;
                echo "<a id='minPaging' href='$PHP_SELF?board_id={$board_id}&page_code={$page_code}&idx={$pre}' style='display:inline-block; border:1px solid #cccccc; font-size:10px; width:30px; height:30px; line-height:28px'>◀</a>";    
            ?>
            <div class='paging_navi' style="margin:0 6px; width: auto; line-height:0; display:inline-block;">
                <!-- 페이징 -->
                <?=$paging?>
            </div>
            <?
                $next=($idx<$max)?$idx+1:$max;
                echo "<a id='maxPaging' href='$PHP_SELF?board_id={$board_id}&page_code={$page_code}&idx={$next}' style='display:inline-block; border:1px solid #cccccc; font-size:10px; width:30px; height:30px; line-height:28px'>▶</a>";    
            ?>
        </div>
    </div>

</div>
<?include $_SERVER["DOCUMENT_ROOT"]."/inc/footer.php";?>
<script>
// meta og 설정 동적으로 적용.
//$("meta[property='og\\:url']").attr("content", '주소넣기' );                    
// $("meta[property='og\\:image']").attr("content", '이미지주소넣기' );
// var url_default_ks = "https://story.kakao.com/share?url=";
// var url_default_fb = "https://www.facebook.com/sharer/sharer.php?u=";
// var url_default_tw_txt = "https://twitter.com/intent/tweet?text=";
// var url_default_tw_url = "&url=";
// var url_default_band = "http://band.us/plugin/share?body=";
// var url_route_band = "&route=";
var url_default_naver = "http://share.naver.com/web/shareView.nhn?url=";
var title_default_naver = "&title=";
var url_this_page = location.href;
var title_this_page = document.title;
// var url_combine_ks = url_default_ks + url_this_page;
// var url_combine_fb = url_default_fb + url_this_page;
// var url_combine_tw = url_default_tw_txt + document.title + url_default_tw_url + url_this_page;
// var url_combine_band = url_default_band + encodeURI(url_this_page) + '%0A' + encodeURI(title_this_page) + '%0A' +'&route=tistory.com';

$(function() {                                                                      
    pagingNavi();
    ulTebClk();
});

$('#naver_share').click(function() {
    var url_combine_naver = url_default_naver + encodeURI(encodeURIComponent(url_this_page)) + title_default_naver + encodeURI(title_this_page);
    var popupWidth = 600;
    var popupHeight = 600;
    var popupX = (window.screen.width / 2) - (popupWidth / 2);
    var popupY = (window.screen.height / 2) - (popupHeight / 2);
    window.open(url_combine_naver, '', 'status=no, height=' + popupHeight + ', width=' +
        popupWidth + ', left=' + popupX + ', top=' + popupY);
})
function ulTebClk() {
    $('.ul_teb >li').click(function() {
        $('.ul_teb li').removeClass('on');
        let board = $(this).attr('name');
        let code = $(this).attr('value');
        window.location = 'board_50.php?board_id=' + board + '&page_code=' + code + '&page=1';
    })
}

function pagingNavi() {
    let min = <?=$min?>;
    let max = <?=$max?>;
    let now = getQueryString('idx');
    $('#minPaging').click(function(){if(min==now){alert('최신 글입니다.');}});
    $('#maxPaging').click(function(){if(max==now){alert('마지막 글입니다.');}});
}

function getQueryString(key) {
    var str = location.href;
    var index = str.indexOf("?") + 1;
    var lastIndex = str.indexOf("#") > -1 ? str.indexOf("#") + 1 : str.length;
    if (index == 0) {
        return "";
    }
    str = str.substring(index, lastIndex);
    str = str.split("&");
    var rst = "";

    for (var i = 0; i < str.length; i++) {
        var arr = str[i].split("=");
        if (arr.length != 2) {
            break;
        }
        if (arr[0] == key) {
            rst = arr[1];
            break;
        }
    }
    return rst;
}

function changeMeta()
{
}
</script>