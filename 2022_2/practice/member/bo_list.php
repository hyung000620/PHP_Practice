<?
include_once($_SERVER["DOCUMENT_ROOT"]."/inc/header.php");

$board_id=($board_id)?$board_id:"notice1";
$start=(int)$start;
$list_scale=(int)$list_scale;

$page_scale=10;
$start=($start) ? $start : 0;
$list_scale=($list_scale) ? $list_scale : 20;
$page=($page)?$page : 1;

$SQL="SELECT * FROM {$my_db}.tc_board WHERE board_id='{$board_id}'";
if(!empty($keyword)){
    $SQL.=" AND (title LIKE '%$keyword%')";
    $SQL.=" OR (content LIKE '%$keyword%')";
}
$SQL.=" ORDER BY idx DESC";
$SQL.=" LIMIT {$start},{$list_scale}";
$stmt=$pdo->prepare($SQL);
$stmt->execute();
$total_record=$stmt->rowCount();

$html="";
while($rs=$stmt->fetch())
{
    $html.="<li style='border-top:1px solid #cccccc'>
    <a href='#' style='padding:12px; display:block;'><span class='bold'style='font-size:15px'>{$rs['title']}</span></br>
    <span>{$rs['wdate']}<span></a></li>";
}

$total_page=ceil($total_record/$page_scale);

$paging="";
for($i=1; $i<=$total_page;$i++){
    $paging.="<span ";
    if($i==$page){$paging.="class='on'";} 
    $paging.="style='border:1px solid #cccccc; width: 30px; height:30px; line-height:28px; font-size:12px; display:inline-block;' >{$i}</span>";
}

?>

<!-------------------- HTML 영역 -------------------------------------------------------------------------------->
<link rel="stylesheet" href="register.css">
<div class='wrap'>
	<div class='li_teb'>
		<ul class='ul_teb'>
			<li value='5010' name='notice1' <?if($board_id=='notice1'){echo"class='on'";}?>>결산안내</li>
            <li value='5020' name='notice2' <?if($board_id=='notice2'){echo"class='on'";}?>>연간기부금</li>
            <li value='5030' name='notice3' <?if($board_id=='notice3'){echo"class='on'";}?>>활용실적</li>
            <li value='5040' name='notice4' <?if($board_id=='notice4'){echo"class='on'";}?>>후원금내용</li>
		</ul>
	</div>
	<div class='clear'></div>
    <div>
        <form id='srchFrm' method="POST" style='margin-top:28px;'>
        <!-- 검색 -->
            <div class="search">
                <input id='keyword' name='keyword' type="text" placeholder="검색어를 입력하세요.">
                <img id='srchBtn' src="/img/icon/ser-icon1.png">
            </div>
        </form>
        <ul style="margin-top:30px; border-top:2px solid #1B43A9; border-bottom:2px solid #cccccc">
        <!-- 목록 -->
            <?=$html;?>
        </ul>
        <div class='paging' style="text-align:center; margin-top:30px;">
            <span id='page_min' style="display:inline-block; border:1px solid #cccccc; font-size:10px; width:30px; height:30px; line-height:28px">◀</span>
            <div class='paging_navi' style="margin:0 6px; width: auto; line-height:0; display:inline-block;">
                <!-- 페이징 -->
            <?=$paging?>
            </div>
            <span id='page_max' style="display:inline-block; border:1px solid #cccccc; font-size:10px; width:30px; height:30px; line-height:28px">▶</span>
        </div>
    </div>
</div>
<? include_once($_SERVER["DOCUMENT_ROOT"]."/inc/footer.php");?>
<!---------------------------------------------------------------------------------------------------------------->

<script>
$(document).ready(function(){
    pageArrow();
    pagingNavi();
    ulTebClk();
    
    $('#srchBtn').click(function(){srchKeyword();});
    $('#keyword').keydown(function(keyNum){
        if(keyNum.keyCode==13){
            $('#srchBtn').click();
        }
    });
});

function srchKeyword()
{
    $('#srchFrm').attr('action','_test.php');
    $('#srchFrm').submit();
}

function ulTebClk()
{
    $('.ul_teb >li').click(function(){
        $('.ul_teb li').removeClass('on');
        let board = $(this).attr('name');
        let code = $(this).attr('value');
        window.location = '_test.php?board_id='+board+'&page_code='+code+'&page=1';
    })
}

function pagingNavi()
{
    $('.paging_navi span').click(function(){
        $('.paging_navi span').removeClass('on');
        let page = $(this).text();
        window.location = window.location.href+'&page='+page;
    });
}

function pageArrow()
{
    $('#page_min').click(function(){
        let url_href = window.location.href;
        let url = new URL(url_href);
        let now_page = url.searchParams.get('page');
        alert(page);
    });

    $('#page_max').click(function(){

    });
}
</script>