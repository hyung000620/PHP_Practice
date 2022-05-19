<?
$page_code=1462;
include($_SERVER["DOCUMENT_ROOT"]."/SuperAdmin/common/header.php");

$hbquery_arr=array();
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
	$stmt=$pdo->prepare("SELECT * FROM {$my_db}.tl_teacher WHERE idx='{$idx}' LIMIT 0,1");
	$stmt->execute();
	$rs=$stmt->fetch();
	$mode="edit";
	$mode_ment="수정";
}
else
{
	$mode="new";
	$mode_ment="등록";
}
?>
<form name="fm" id="fm" action="teacher_db.php" method="post" enctype="multipart/form-data">
<div class="center bold f18" style='padding:10px'>강사 <?=$mode_ment?></div>
<table class="tbl_grid">
    <tr>
		<th>강사ID</th>
		<td><input type="text" id="teacher_id" name="teacher_id" value="<?=$rs[teacher_id]?>" class="tx300"></td>
	</tr>
    <tr>
		<th>회원ID</th>
		<td><input type="text" id="user_id" name="user_id" value="<?=$rs[id]?>" class="tx300"></td>
	</tr>
	<tr>
		<th>강사명</th>
		<td><input type="text" id="teacher_name" name="teacher_name" value="<?=$rs[name]?>" class="tx300"></td>
	</tr>
    <tr>
		<th>강사 닉네임</th>
		<td><input type="text" id="teacher_nickname" name="teacher_nickname" value="<?=$rs[nickname]?>" class="tx300"></td>
	</tr>
	<tr>
		<th>표시 여부</th>
		<td>
			<input type="radio" name="dp_off" value="0"<? if($rs[dp_off]==0) echo " checked"; ?>>표시함(O)
			&nbsp;&nbsp;
			<input type="radio" name="dp_off" value="1"<? if($rs[dp_off]==1) echo " checked"; ?>>표시안함(X)
		</td>
	</tr>
	<tr>
		<th>강사추가내용</th>
		<td>
			<textarea rows="5" name="teacher_content" class="ta500"><?=$rs[content]?></textarea>
		</td>
	</tr>
	<tr>
		<th style='background:#E1FFFF'>강사사진(Small)</th>
		<td>
			<div><input type="file" name="photo_s" class="tx500">	&nbsp;&nbsp;&nbsp;&nbsp;사이즈:100X140</div>
			<?
				if($rs[photo_s])
				{
					echo "
						<div>
							<img src='/lecture/teacher/photo/{$rs[photo_s]}' align='bottom'>
							<input type='checkbox' name='chk_photo_s' value='1'>삭제
							<a href='/lecture/teacher/photo/{$rs[photo_s]}' target='_blank' class='blue'>{$rs[photo_s]}</a>
						</div>";
				}
			?> 
		</td>
	</tr>
	
	<tr>
		<th style='background:#FFECE1'>강사사진(Big)</th>
		<td>
			<div><input type="file" name="photo_b" class="tx500" ></div>
			<?
				if($rs[photo_b])
				{
					echo "
						<div>
							<img src='/lecture/teacher/photo/{$rs[photo_b]}' align='bottom'>
							<input type='checkbox' name='chk_photo_b' value='1'>삭제
							<a href='/lecture/teacher/photo/{$rs[photo_b]}' target='_blank' class='blue'>{$rs[photo_b]}</a>
						</div>";
				}
			?> 
		</td>
	</tr>
</table>
<br>
<table class="tbl_noline">
	<tr>
		<td width="30%"></td>
		<td width="40%" class="center"><input type="button" id="btnSubmit" value=" 저장하기 "></td>
		<td width="30%" class="right"><input type="button" value="목록으로" onclick="location.href='teacher_list.php'"></td>
	</tr>
</table>
	<input type="hidden" name="idx" value="<?=$rs[idx]?>">
	<input type="hidden" name="mode" value="<?=$mode?>">
	<input type="hidden" name="params" value="<?=$params?>">
</form>

<?
include($_SERVER["DOCUMENT_ROOT"]."/SuperAdmin/common/footer.php");
?>

<script type="text/javascript">
$(document).ready(function(){
	$("#btnSubmit").click(function(){
		fm_check();
	});
});

function fm_check()
{
	if($("#teacher_id").val()=="")
	{
		alert("강사 아이디를 입력하세요.");
		return;
	}
	$("#fm").submit();
}
</script>