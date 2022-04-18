<?
include_once($_SERVER["DOCUMENT_ROOT"]."/inc/header.php");
$page_code=($page_code)?$page_code:"5010";
$board_id=($board_id)?$board_id:"notice1";
$start=(int)$start;
$list_scale=(int)$list_scale;

$page_scale=10;
$start=($start) ? $start : 0;
$list_scale=($list_scale) ? $list_scale : 20;
$page=($page)?$page : 1;

$SQL="SELECT * FROM {$my_db}.tc_board WHERE board_id='{$board_id}'";
if(!empty($keyword)){
    $SQL.=" AND (title LIKE '%{$keyword}%')";
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
    <a href='/sam_board/board_50_view.php?board_id={$board_id}&page_code={$page_code}&idx={$rs['idx']}' style='padding:12px; display:block;'><span class='bold'style='font-size:15px'>{$rs['title']}</span></br>
    <span>{$rs['wdate']}<span></a></li>";
}
if($total_record==0)
{
    $html.="<div style='text-align:center;'>";
    $html.="<img src='data:image/png;base64,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'>";
    $html.="</div>";
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
<link rel="stylesheet" href="/member/register.css">
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
            <?
                $pre=($page>1)?$page-1:1;
                echo "<a href='$PHP_SELF?board_id={$board_id}&page_code={$page_code}&page={$pre}' style='display:inline-block; border:1px solid #cccccc; font-size:10px; width:30px; height:30px; line-height:28px'>◀</a>";    
            ?>
            <div class='paging_navi' style="margin:0 6px; width: auto; line-height:0; display:inline-block;">
                <!-- 페이징 -->
            <?=$paging?>
            </div>
            <?
                $next=($page<$total_page)?$page+1:$total_page;
                echo "<a href='$PHP_SELF?board_id={$board_id}&page_code={$page_code}&page={$next}' style='display:inline-block; border:1px solid #cccccc; font-size:10px; width:30px; height:30px; line-height:28px'>▶</a>";    
            ?>
        </div>
    </div>
    
</div>
<? include_once($_SERVER["DOCUMENT_ROOT"]."/inc/footer.php");?>
<!---------------------------------------------------------------------------------------------------------------->

<script>
$(document).ready(function(){
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
    $('#srchFrm').attr('action',location.href);
    $('#srchFrm').submit();
}

function ulTebClk()
{
    $('.ul_teb >li').click(function(){
        $('.ul_teb li').removeClass('on');
        let board = $(this).attr('name');
        let code = $(this).attr('value');
        window.location = '<?=$PHP_SELF;?>?board_id='+board+'&page_code='+code+'&page=1';
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


</script>