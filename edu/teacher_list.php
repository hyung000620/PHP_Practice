<?
$page_code=1462;
include($_SERVER["DOCUMENT_ROOT"]."/SuperAdmin/common/header.php");
$list_scale_arr=array(30,50,100,200,300);

$t_name=trim($t_name);
$today=date("Y-m-d");


if($t_name)		$condiArr[]="name LIKE '%{$t_name}%'";

$list_scale_arr=array(30,50,100,200,300);

$condition=($condiArr) ? implode(" AND ",$condiArr) : "1";
$order=($order_type) ? $order_type : " idx DESC ";

$SQL="SELECT COUNT(*) FROM {$my_db}.tl_teacher WHERE {$condition}";
$stmt=$pdo->prepare($SQL);
$stmt->execute();
$rs=$stmt->fetch();
		
$total_record=$rs[0];
$list_scale=($list_scale) ? $list_scale : 30;
$page_scale=10;
$start=($start) ? $start : 0;

$SQL="SELECT * FROM {$my_db}.tl_teacher WHERE {$condition} ORDER BY {$order} LIMIT {$start}, {$list_scale}";
$stmt=$pdo->prepare($SQL);
$stmt->execute();
?>

<form name="fmSrch" id="fmSrch" action="<?=$PHP_SELF?>" method="post">
<table class="tbl_grid">
	<tr>
		<th width="80px">강사 명</th>
		<td><input type="text" name="t_name" id="t_name" value="<?=$t_name?>" class="tx150 han"></td>
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
		<td class="center"><a href="teacher_write.php" class="bold orange">+ 강사 등록 &gt;</a></td>
	</tr>
    
</table>
<br>
<table class="tbl_list">
	<tr>
		<th width="20%">No</th>
		<th width="20%">사진</th>
		<th width="20%">강사명(닉네임)</th>
		<th width="20%">등록일자</th>
        <th width="20%">표시여부</th>
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
    if($rs[dp_off]==0){
        $dp="gray";
        $status="O";	
    }else{
        $dp="bg_red gray";	
        $status="<span class='red'>X</span>";
    }
		
    echo "
    <tr height='60' name='row' class='{$dp}' onclick=\"location.href='teacher_write.php?idx={$rs[idx]}&{$params}'\">
        <td class='no center'>{$lineNo}</td>
        
        <!--<td class='center bold'>{$edu_name1}</td>-->
        ";
        if($rs[photo_s]){
            echo "<td class='center'><img src='/lecture/teacher/photo/{$rs[photo_s]}' align='bottom' width='150'></td>";
        }else{
            echo "<td class='center'>no - image</td>";
        }echo "
        <td class='center bold'>$rs[name]($rs[nickname])</td>
        <td class='center no'>".substr($rs[wdate],0,10)."</td>
        <td class='bold center'>{$status}</td>
    </tr>";
	$lineNo--;
}
?>
</table>