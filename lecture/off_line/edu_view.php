<?
$page_code=147000;
//include $_SERVER["DOCUMENT_ROOT"]."/common/header.php";
include $_SERVER["DOCUMENT_ROOT"]."/inc/header.php";

$weekString = array("일", "월", "화", "수", "목", "금", "토");
$hbquery_arr=array();
$today=date("Y-m-d");

foreach($_GET as $key => $val)
{
	if(!$val) continue;
	$hbquery_arr[$key]=$val;
}
foreach($_POST as $key => $val)
{
	if(!$val) continue;
	$hbquery_arr[$key]=$val;
}
$params=http_build_query($hbquery_arr);

if($idx)
{
	//$result=sql_query("SELECT * FROM {$my_db}.te_edu WHERE idx='{$idx}' LIMIT 0,1");
	//$rs=mysql_fetch_array($result);
	$stmt=$pdo->prepare("SELECT * FROM {$my_db}.te_edu WHERE idx='{$idx}' LIMIT 0,1");
	$stmt->execute();
	$rs=$stmt->fetch();
	$sday=$rs[sdate];
	$eday=$rs[edate];
	if($rs[edate] < $today){
		$dp="bg_gray gray";	
		$status="<span class='ltblue'>종료</span>";
	}elseif ($rs[sdate] > $today){
		$dp="bg_yellow";
		$status="<span class='red'>모집중</span>";
	}else{
		$dp="";
		$status="<span class='ltblue'>진행중</span>";
	}
	
	if($rs[edu_pay]>0){
		$pay=number_format($rs[edu_pay])."원";
	}else{
		$pay="<span class='red'>무료</span>";
	}
	if($rs[sdate]==$rs[edate]){
		$edu_date="$rs[sdate] ({$weekString[date('w', strtotime($sday))]}) <span class='red'>[일일특강]</span> ";
	}else{
		$edu_date="{$rs[sdate]} ({$weekString[date('w', strtotime($sday))]}) ~ {$rs[edate]}  ({$weekString[date('w', strtotime($eday))]}) ";
	}

	if($rs[sav_file])
	{
		$if_arr=(file_exists($_SERVER['DOCUMENT_ROOT']."/img/edu_pic/".$rs[sav_file])) ? getimagesize($_SERVER['DOCUMENT_ROOT']."/img/edu_pic/".$rs[sav_file]) : false;
		$img_size=($if_arr) ? ($if_arr[0]."*".$if_arr[1]) : "x ";
		$photo_arr=array("idx" => $idx, "org_file" => $rs[org_file], "sav_file" => $rs[sav_file], "file_size" => $rs[file_size]);	
	}
}
else
{
	echo "잘못된 접근 입니다. ";
}
?>
<div class="f_naum f20 fw_900"><span class='span_block <?=$dp?>' style='padding:5px 10px;border:1px solid #FF0000'><?=$status?></span>&nbsp;&nbsp;<?=$rs[edu_title]?></div><br>
<table class="tbl_grid">
	<tr height="35">
		<th width="100">교육장소</th>
		<td><?=$rs[edu_addr]?> <?=$rs[edu_area]?> &nbsp;&nbsp;&nbsp; 
			<span class='btn_box btn_lightgray' style='font-size:11px;padding:3px 0 2px;width:100px;' onclick="window.open('http://map.daum.net/?q=<?=$rs[edu_addr]?>');" class="blue">위치보기</span>
		</td>
	</tr>
	<tr height="35">
		<th>교육일정</th>
		<td>
			<?=$edu_date?>	&nbsp;&nbsp;&nbsp;(일시: <?=$rs[edu_time]?> )
		</td>
	</tr>
	<tr height="35">
		<th>교 육 비</th>
		<td><?=$pay?></td>
	</tr>
	<tr height="35">
		<th>문의전화</th>
		<td><?=$rs[edu_phone]?></td>
	</tr>
	<tr height="35">
		<th>기타안내</th>
		<td class="lh20"><div style="padding:10px 0"><?=$rs[edu_content]?></div></td>
	</tr>
</table>

<div class='center' style='margin-top:20px'>
	<img src='/img/edu_pic/<?=$photo_arr[sav_file]?>' width='800' style='border:1px #ccc solid'>
</div>
<br>
<table class="tbl_noline">
	<tr>
		<td class="center"><span class="btn_box btn_red" onclick="location.href='edu_list.php'">목록으로</span></span></td>
	</tr>
</table>
	<input type="hidden" name="params" value="<?=$params?>">
</form>

<?
include($_SERVER["DOCUMENT_ROOT"]."/inc/footer.php");
?>


