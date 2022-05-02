<?
$page_code="4030";
include_once($_SERVER["DOCUMENT_ROOT"]."/inc/header.php");
$dv=30;
$board_id="gdstory";
$SQL="SELECT * FROM {$my_db}.tc_board AS A JOIN {$my_db}.tc_inc_files AS B ON A.idx = B.ref_idx WHERE A.board_id='{$board_id}' AND A.del=0";
$stmt=$pdo->prepare($SQL);
$stmt->execute();
$arr=array();
$html="";
while($rs=$stmt->fetch())
{
    $html.="<div class='box-wrap'>
                <div class='box'>
                    <div class='img'>
                        <a href='#'>
                            <img src='/data/board/{$board_id}/thumnail/{$rs['sav_file']}'>
                        </a>
                    </div>
                    <div class='info'>
                        <h3>{$rs['title']}</h3>
                        <p>{$rs['wdate']}</p>    
                    </div>
                </div>
            </div>";
}
?>
<style>
    .box-wrap {
  display: flex;
  justify-content: center;
  align-items: center
}
.box {
  position: relative;
  width: 400px; height: 300px;
  border: 7px solid #1B43A9;
  box-shadow: 1px 1px 3px rgba(0,0,0,0.4);
  overflow: hidden;
}
.box .img img {
    width:400px;
  height:300px;
}

.box .info {
  color: #fff;
  position: absolute; left: 0; bottom: 0;
  background: rgba(0,0,0,0.5);
  width: 100%;
  padding: 15px;
  box-sizing: border-box;
  opacity: 0;
  transition: opacity 0.35s ease-in-out;
}
.box:hover .info {
  opacity: 1;
}
.box .info h3 {
  font-size: 24px;
  padding-bottom: 0.4em;
  overflow:hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  text-transform: uppercase;
}
.box .info p {
  font-size: 20px;
  text-overflow: ellipsis;
  white-space: nowrap;
  text-transform: uppercase;
}
</style>
<link rel="stylesheet" href="/member/css/register.css">
<div class="wrap">
    <div class='li_teb'>
		<ul class='ul_teb'>
			<li name="10" <?if($dv==10){echo "class='on'";}?> >새소식</li>
            <li name="20" <?if($dv==20){echo "class='on'";}?> >뉴스사항</li>
            <li name="30" <?if($dv==30){echo "class='on'";}?> >후원이야기</li>
            <li name="40" <?if($dv==40){echo "class='on'";}?> >자유게시판</li>
		</ul>
	</div>
	<div class='clear'></div>
    <div>
    <form id='srchFrm' method="POST" style='margin-top:28px;'>
            <div class="search">
                <input type="hidden" id="page" name="page" value="1">
                <input type="hidden" name="board_id" value="<?=$board_id?>">
                <input type="hidden" name="mode" value="10">
                <input id='keyword' name='keyword' type="text" placeholder="검색어를 입력하세요.">
                <img id='srchBtn' src="/img/icon/ser-icon1.png">
            </div>
        </form>
        <br>
        <div style='display:grid; grid-template-columns: repeat(auto-fill, minmax(300px,1fr)); grid-gap:15px;'>
            <?=$html?>
        </div>
    </div>
</div>
//이것도 안되면 진짜 뭐지
<?
include_once($_SERVER["DOCUMENT_ROOT"]."/inc/footer.php");
?>
