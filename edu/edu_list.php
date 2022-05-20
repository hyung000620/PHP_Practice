<?
$page_code=1463;
include($_SERVER["DOCUMENT_ROOT"]."/SuperAdmin/common/header.php");

$edu_title=trim($edu_title);
$today=date("Y-m-d");


if($edu_title)		$condiArr[]="edu_title LIKE '%{$edu_title}%'";

$list_scale_arr=array(30,50,100,200,300);

$condition=($condiArr) ? implode(" AND ",$condiArr) : "1";
$order=($order_type) ? $order_type : " idx DESC ";
$SQL="SELECT COUNT(*) FROM {$my_db}.tl_edu WHERE {$condition}";
//$result=sql_query($SQL);
//$rs=mysql_fetch_row($result);
$stmt=$pdo->prepare($SQL);
$stmt->execute();
$rs=$stmt->fetch();
		
$total_record=$rs[0];
$list_scale=($list_scale) ? $list_scale : 30;
$page_scale=10;
$start=($start) ? $start : 0;

$SQL="SELECT * FROM {$my_db}.tl_edu WHERE {$condition} ORDER BY {$order} LIMIT {$start}, {$list_scale}";
//$result=sql_query($SQL);
$stmt=$pdo->prepare($SQL);
$stmt->execute();

?>
<link rel="stylesheet" type="text/css" href="/css/btn_box.css">
<div class="bg_green fleft red bold" style='border:1px solid #eee;width:60px;padding:10px 30px'>관리목록</div>
<div class="fleft" style='padding:5px 0 0 50px'><a href="http://www.tankauction.com/lecture/off_line/edu_list.php" target="_blank"><span class='btn_box_sss btn_tank radius_10'>강좌확인</span></a></div>

<div class="cl" style="border-top:1px solid #eee;margin-bottom:20px"></div>
<form name="fmSrch" id="fmSrch" action="<?=$PHP_SELF?>" method="post">
<table class="tbl_grid">
	<tr>
		<th width="80px">교육제목</th>
		<td><input type="text" name="edu_title" id="edu_title" value="<?=$edu_title?>" class="tx150 han"></td>
		<th>보기</th>
		<td>
			<select name="list_scale" id="list_scale">
			<?
			foreach($list_scale_arr as $val)
			{
				echo "<option value='{$val}'";if($list_scale==$val) echo " selected"; echo ">{$val}</option>";	
			}
			?>
			</select>개
		</td>
		<td class="center">
			<input type="submit" value=" 검 색 ">
			&nbsp;&nbsp;&nbsp;
			<input type="button" id="btnRest" value="리셋" onclick="location.href='<?=$PHP_SELF?>'">
		</td>
		<td class="center"><a href="edu_write.php" class="bold orange">+ 오프라인교육 등록 &gt;</a></td>
	</tr>
</table>
</form>
<br>
<table class="tbl_list">
	<tr>
		<th width="50">No</th>
		<th width="200">이미지</th>
		<th width="100">강사명</th>
		<th width="300">교육제목</th>
		<th>주소</th>
		<th width="100">인원/비용</th>
		<th width="100">교육일시</th>
		<th width="100">등록일자</th>
		<th width="70">반영/진행</th>
	</tr>
<?
$hbquery_arr=array();
foreach($_GET as $key => $val)
{
	if(!$val || $key=="idx") continue;
	$hbquery_arr[$key]=$val;
}
foreach($_POST as $key => $val)
{
	if(!$val || $key=="idx") continue;
	$hbquery_arr[$key]=$val;
}
$params=http_build_query($hbquery_arr);

$lineNo=$total_record-$start;
$curr_stamp=time();

//while($rs=mysql_fetch_array($result))
while($rs=$stmt->fetch())
{	
	
	if($rs[dp_off]==1 || $rs[edate] < $today){
		if($rs[dp_off]==1){
			$dp="gray";
			$status="미노출";	
		}else{
			$dp="bg_red gray";	
			$status="<span class='red'>종료</span>";
		}
	}elseif ($rs[sdate] > $today){
		$dp="bg_yellow";
		$status="<span class='ltblue'>모집중</span>";
	}else{
		$dp="bg_green";
		$status="<span class='ltblue'>진행</span>";
	}
	if($rs[edu_pay]>0){
		$pay=number_format($rs[edu_pay])."원";
	}else{
		$pay="<span class='red'>무료</span>";
	}
	if($rs[sdate]==$rs[edate]){
		$edu_date="<span class='red'>일일특강</span> <br> $rs[sdate]";
	}else{
		$edu_date="{$rs[sdate]} <br>~ {$rs[edate]}";
	}
	if($rs[on_off]==0){
		$on_off_ment="<span class='span_block white bold' style='padding:3px 10px;background:#7153FF'>off</span><br><br>";	
	}elseif($rs[on_off]==1){
		$on_off_ment="<span class='span_block white bold' style='padding:3px 10px;background:#FF5357'>on</span><br><br>";
	}else{
		$on_off_ment="<span class='span_block white bold' style='padding:3px 10px;background:#000000'>on/off</span><br><br>";
	}
	if($rs[edu_people]>0){
		$edu_people="<span class='blue bold' >($rs[edu_people] 명)</span><br>";
	}else{
		$edu_people="<br>";
	}
	
	foreach($ary_educode as $edu_val => $edu_name){
		if($rs[edu_zone]==$edu_val){
			$edu_name1=$edu_name;
		}
	}	
		echo "
		<tr height='60' name='row' class='{$dp}' onclick=\"location.href='edu_write.php?idx={$rs[idx]}&{$params}'\">
			<td class='no center'>{$lineNo}</td>
			
			<!--<td class='center bold'>{$edu_name1}</td>-->
			";
			if($rs[photo_main]){
				echo "<td class='center'><img src='/lecture/off_line/photo/{$rs[photo_main]}' align='bottom' width='150'></td>";
			}else{
				echo "<td class='center'>no - image</td>";
			}echo "
			<td class='center bold'>$rs[edu_teacher]</td>
			<td class='left'>{$on_off_ment}$rs[edu_title]</td>
			<td><div class='ellipsis' style='width:200px'>{$rs[edu_addr]}{$rs[edu_area]}</div></td>
			<td class='center'>{$edu_people}{$pay}</td>
			<td class='center no'>{$edu_date}</td>
			<td class='center no'>".substr($rs[wdate],0,10)."</td>
			<td class='bold center'>{$status}</td>
		</tr>";
	$lineNo--;
}
?>
</table>

<?
if(!$total_record) echo "<div class='no_result'><span>검색 결과가 없습니다.</span></div>";
include $_SERVER["DOCUMENT_ROOT"]."/inc/PageNavi.php";
include($_SERVER["DOCUMENT_ROOT"]."/SuperAdmin/common/footer.php");
?>

<script type="text/javascript">
$(document).ready(function(){	
	/*
	$("#btnRest").click(function(){
		formInit("#fmSrch");
	});	
	$(".tx_date").mask("9999-99-99");
	*/
	$("tr[name=row]").mouseover(function(){
		$(this).css({"background":"#e3eefb","cursor":"pointer"});
	}).mouseout(function(){
		$(this).css({"background":""});
	});
	
	//선택한 검색 조건 강조
	formSrchStyle(fmSrch);
	
	//전체 선택/해제
	$("#chk_all").click(function(){
		var bool=(this.checked==true) ? true : false;
		$("input:checkbox[name=chk_idx]").each(function(){
			this.checked=bool;
		})
	});
});
</script>