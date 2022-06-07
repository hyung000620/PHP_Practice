<?
$page_code=9010;
include_once($_SERVER["DOCUMENT_ROOT"]."/lec/inc/header.php");

//동영상 강좌 구분
$stmt=$pdo->prepare("SELECT idx,lec_code,course,teacher FROM {$my_db}.te_lecture WHERE ctgr BETWEEN 20 AND 33");
$stmt->execute();
while($rs=$stmt->fetch()){$lect_arr[$rs[lec_code]]=array('idx'=>$rs['idx'],'title'=>$rs[course],'teacher'=>$rs['teacher']);}

//탱크교육 구분
$stmt=$pdo->prepare("SELECT idx,edu_code,edu_title,edu_teacher FROM {$my_db}.tl_edu");
$stmt->execute();
while($rs=$stmt->fetch()){$edu_arr[$rs[edu_code]]=array('idx'=>$rs['idx'],'title'=>$rs[edu_title],'teacher'=>$rs['edu_teacher']);}

//결제 내역
$stmt=$pdo->prepare("SELECT sector,pay_code FROM {$my_db}.tm_pay_result WHERE id='{$client_id}' AND pay_code IN (101, 102)");
$stmt->execute();
?>
<div class='wrap'>
	<table class="tbl_new_list">
        <?
            $html=array();
            $lect=0;$edu=0;
            while($rs=$stmt->fetch())
            {
                $total_record=$rs[0];
                if($rs[pay_code]==101)
                {
                    $lect++;
                    if($lect==1){$html[]="<tr><td colspan='2' style='padding:20px 0 10px' class='f24 bold gray'>동영상 강좌</td></tr>";}
                    $html[]="<tr onclick='location.href=\"/lec/mov/mov_view.php?idx={$lect_arr[$rs['sector']]['idx']}\"'>";
                    $html[]="<td class='center'>{$lect_arr[$rs['sector']]['title']}</td>";
                    $html[]="<td class='center'>{$lect_arr[$rs['sector']]['teacher']}</td>";
                    $html[]="</tr>";
                }
                else
                {
                    $edu++;
                    if($edu==1){$html[]="<tr><td colspan='2' style='padding:20px 0 10px' class='f24 bold gray'>탱크교육</td></tr>";}
                    $html[]="<tr onclick='location.href=\"/lec/edu/edu_view.php?idx={$edu_arr[$rs['sector']]['idx']}\"'>";
                    $html[]="<td class='center'>{$edu_arr[$rs['sector']]['title']}</td>";
                    $html[]="<td class='center'>{$edu_arr[$rs['sector']]['teacher']}</td>";
                    $html[]="</tr>";
                }
            }
            $html=implode("",$html);
            echo $html;
        ?>
	</table>
	<?
	if($total_record==0)
	{
		echo "<div class='no_result'><span>결제한 강의가 없습니다.</span></div>";
	}
	?>
</div>
<?
include_once($_SERVER["DOCUMENT_ROOT"]."lec/inc/footer.php");
?>