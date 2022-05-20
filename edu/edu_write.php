<?
$page_code=1463;
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

$SQL="SELECT * FROM {$my_db}.tl_teacher WHERE 1";
$stmt=$pdo->prepare($SQL);
$tArr=array();
$stmt->execute();
while($rs=$stmt->fetch())
{
	array_push($tArr,$rs['nickname']);
}

if($idx)
{
	//$result=sql_query("SELECT * FROM {$my_db}.tl_edu WHERE idx='{$idx}' LIMIT 0,1");
	//$rs=mysql_fetch_array($result);
	$stmt=$pdo->prepare("SELECT * FROM {$my_db}.tl_edu WHERE idx='{$idx}' LIMIT 0,1");
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
<style>
.sec_cal {width: 100%;margin: 0 auto;}
.sec_cal .cal_nav {display: flex;justify-content: center;align-items: center;font-weight: 700;font-size: 18px;line-height: 78px;}
.sec_cal .cal_nav .year-month {width: 200px;text-align: center;line-height: 1;}
.sec_cal .cal_nav .nav {display: flex;border: 1px solid #333333;border-radius: 5px;}
.sec_cal .cal_nav .go-prev,.sec_cal .cal_nav .go-next {display: block;width: 50px;height: 78px;font-size: 0;display: flex;justify-content: center;align-items: center;}
.sec_cal .cal_nav .go-prev::before,.sec_cal .cal_nav .go-next::before {content: "";display: block;width: 20px;height: 20px;border: 3px solid #000;border-width: 3px 3px 0 0;transition: border 0.1s;}
.sec_cal .cal_nav .go-prev:hover::before,.sec_cal .cal_nav .go-next:hover::before {border-color: #ed2a61;}
.sec_cal .cal_nav .go-prev::before {transform: rotate(-135deg);}
.sec_cal .cal_nav .go-next::before {transform: rotate(45deg);}
.sec_cal .cal_wrap {padding-top: 40px;position: relative;margin: 0 auto;}
.sec_cal .cal_wrap .days {display: flex;margin-bottom: 20px;padding-bottom: 20px;border-bottom: 1px solid #ddd;}
.sec_cal .cal_wrap::after {top: 368px;}
.sec_cal .cal_wrap .day {display:flex;align-items: center;justify-content: center;width: calc(100% / 7);text-align: left;color: #999;font-size: 12px;text-align: center;border-radius:5px}
.current.today {background: rgb(242 242 242);}
.sec_cal .cal_wrap .dates {display: flex;flex-flow: wrap;height: 290px;}
.sec_cal .cal_wrap .day:nth-child(7n) {color: #3c6ffa;}
.sec_cal .cal_wrap .day:nth-child(7n-6) {color: #ed2a61;}
.sec_cal .cal_wrap .day.disable {color: #ddd;}
</style>
<form name="fm" id="fm" action="edu_db.php" method="post" enctype="multipart/form-data">
<div class="center bold f18" style='padding:10px'>오프라인 교육 <?=$mode_ment?></div>
<table class="tbl_grid">
	<!--
	<tr>
		<th>교육지역</th>
		<td>
			<? foreach($ary_educode as $edu_val => $edu_name) : ?>
				<input type="radio" name="edu_zone" value="<?=$edu_val?>"<? if($rs[edu_zone]==$edu_val || ($edu_val==11 && !$rs[edu_zone])) {echo " checked";} ?>> <?=$edu_name?>
			<? endforeach; ?>
		</td>
	</tr>
	-->
	<tr>
		<th>교육제목</th>
		<td><input type="text" id="edu_title" name="edu_title" value="<?=$rs[edu_title]?>" class="tx300"></td>
	</tr>
	<tr>
		<th>강사명</th>
		<td><input type="text" id="edu_teacher" name="edu_teacher" value="<?=$rs[edu_teacher]?>" class="tx300">
		&nbsp;&nbsp; 기존 강사
		<select id='sel_teacher'>
			<?
				$html="";
				$html.="<option value='0'>-선택-</option>";
				foreach($tArr as $v){
					$html.= "<option value='{$v}'";
					if($idx && $v == $rs[edu_teacher]){$html.="selected";}
					$html.= ">{$v}</option>";
				}
				echo $html;
			?>
			</select>
		</td>
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
		<th>온/오프라인</th>
		<td>
			<input type="radio" name="on_off" value="0"<? if($rs[on_off]==0) echo " checked"; ?>>오프라인
			&nbsp;&nbsp;
			<input type="radio" name="on_off" value="1"<? if($rs[on_off]==1) echo " checked"; ?>>온라인
			&nbsp;&nbsp;
			<input type="radio" name="on_off" value="2"<? if($rs[on_off]==2) echo " checked"; ?>>온/오프라인
		</td>
	</tr>
	<tr>
		<th>모집/마감</th>
		<td>
			<input type="radio" name="receipt" value="0"<? if($rs[receipt]==0) echo " checked"; ?>>마감
			&nbsp;&nbsp;
			<input type="radio" name="receipt" value="1"<? if($rs[receipt]==1) echo " checked"; ?>>모집중
			&nbsp;&nbsp;
			<input type="radio" name="receipt" value="2"<? if($rs[receipt]==2) echo " checked"; ?>>진행중
		</td>
	</tr>
	<tr>
		<th>교육 주소</th>
		<td><input type="text" id="edu_addr" name="edu_addr" value="<?=$rs[edu_addr]?>" class="tx300">
			&nbsp; &gt;
			<a href="javascript:link_view()" class="blue">링크 확인</a>
		</td>
	</tr>
	<tr>
		<th>교육 장소</th>
		<td><input type="text" id="edu_area" name="edu_area" value="<?=$rs[edu_area]?>" class="tx300"></td>
	</tr>
	<tr>
		<th>접수 일자</th>
		<td>
			<input type="text" name="rdate" id="rdate" class="tx_date" autocomplete='off'>
		</td>
	</tr>
	<tr>
		<th>교육 일정</th>
		<td>
			<input type="text" name="sdate" id="sdate" value="<?=$rs[sdate]?>" class="tx_date" autocomplete='off'>
			~
			<input type="text" name="edate" id="edate" value="<?=$rs[edate]?>" class="tx_date" autocomplete='off'>
			&nbsp;&nbsp;&nbsp;일시
			<input type="text" name="edu_time" id="edu_time" value="<?=$rs[edu_time]?>" class="tx200">
		</td>
	</tr>
	<tr>
		<th>교육 비용</th>
		<td><input type="text" id="edu_pay" name="edu_pay" value="<?=$rs[edu_pay]?>" class="tx100">원</td>
	</tr>
	<tr>
		<th>모집정원</th>
		<td><input type="numbere" id="edu_people" name="edu_people" value="<?=$rs[edu_people]?>" class="tx50">명</td>
	</tr>
	<tr>
		<th>문의전화</th>
		<td><input type="text" id="edu_phone" name="edu_phone" value="<?=$rs[edu_phone]?>" class="tx100"></td>
	</tr>
	<tr>
		<th>강좌 요일</th>
		<td>
			<input type="text" id="open_date" name="open_date" value="<?=$rs[open_date]?>" class="tx500" disabled>
			<div id='cal'></div>
		</td>
	</tr>
	
	<tr>
		<th>상세페이지</th>
		<td>
			<input type="text" id="link" name="link" value="<?=$rs[link]?>" class="tx500">
			&nbsp; &gt;
			<a href="javascript:link_view2()" class="blue">링크 확인</a>
		</td>
	</tr>


	<tr>
		<th>교육추가내용</th>
		<td>
			<textarea rows="5" name="edu_content" class="ta500"><?=$rs[edu_content]?></textarea>
		</td>
	</tr>
	<tr>
		<th>스크린채널</th>
		<td>
			<div><input type="file" name="photo_main" class="tx500"></div>
			<?
				if($rs[photo_main])
				{
					echo "
						<div>
							<img src='/lecture/off_line/photo/{$rs[photo_main]}' align='bottom'>
							<input type='checkbox' name='chk_photo_main' value='1'>삭제
							<a href='/lecture/off_line/photo/{$rs[photo_main]}' target='_blank' class='blue'>{$rs[photo_main]}</a>
						</div>";
				}
			?>	
		</td>
	</tr>
	<tr>
		<th style='background:#E1FFFF'>강사사진</th>
		<td>
			<div><input type="file" name="photo_teacher" class="tx500">	&nbsp;&nbsp;&nbsp;&nbsp;사이즈:100X140</div>
			<?
				if($rs[photo_teacher])
				{
					echo "
						<div>
							<img src='/lecture/off_line/photo/{$rs[photo_teacher]}' align='bottom'>
							<input type='checkbox' name='chk_photo_teacher' value='1'>삭제
							<a href='/lecture/off_line/photo/{$rs[photo_teacher]}' target='_blank' class='blue'>{$rs[photo_teacher]}</a>
						</div>";
				}
			?> 
		</td>
	</tr>
	<tr>
		<th style='background:#FFECE1'>강의사진</th>
		<td>
			<div><input type="file" name="photo_edu" class="tx500"></div>
			<?
				if($rs[photo_edu])
				{
					echo "
						<div>
							<img src='/lecture/off_line/photo/{$rs[photo_edu]}' align='bottom'>
							<input type='checkbox' name='chk_photo_edu' value='1'>삭제
							<a href='/lecture/off_line/photo/{$rs[photo_edu]}' target='_blank' class='blue'>{$rs[photo_edu]}</a>
						</div>";
				}
			?> 
		</td>
	</tr>
	<tr>
		<th style='background:#FFECE1'>강의사진2</th>
		<td >
			<div><input type="file" name="photo_screen" class="tx500"></div>
			<?
				if($rs[photo_screen])
				{
					echo "
						<div>
							<img src='/lecture/off_line/photo/{$rs[photo_screen]}' align='bottom'>
							<input type='checkbox' name='chk_photo_screen' value='1'>삭제
							<a href='/lecture/off_line/photo/{$rs[photo_screen]}' target='_blank' class='blue'>{$rs[photo_screen]}</a>
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
		<td width="30%" class="right"><input type="button" value="목록으로" onclick="location.href='edu_list.php'"></td>
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
	calendarInit();
	$("#sdate,#edate,#rdate").datepicker({
		changeMonth:true,
		changeYear:true,
		showButtonPanel:true
	});	
	$("#btnSubmit").click(function(){
		fm_check();
	});
	$('#sel_teacher').on('change', function(){
		let t_val = $('#sel_teacher').val();
		$('#edu_teacher').val((t_val==0)?"":t_val);
	});
});
function link_view()
{
	var link_url="";
	link_url=$("#edu_addr").val();
	
	if(link_url=="")
	{
		alert("링크 주소를 입력 해 주세요.");
		return;
	}
	
	window.open("http://map.daum.net/?q="+link_url);
}

function link_view2()
{
	var link_view2="";
	link_view2=$("#link").val();
	
	if(link_view2=="")
	{
		alert("링크 주소를 입력 해 주세요.");
		return;
	}
	
	window.open(link_view2);
}
function fm_check()
{
	if($("#edu_title").val()=="")
	{
		alert("교재제목 입력하세요.");
		return;
	}
	$("#fm").submit();
}
//calendar
function calendarInit() 
{
	let arr_day = [];
    let wrap = [];
    wrap.push("<div class=\"sec_cal\">");
    wrap.push("<div class=\"cal_nav\">")
    wrap.push("<a href=\"javascript:;\" class=\"nav-btn go-prev\"></a>");
    wrap.push("<div class=\"year-month\"></div>");
    wrap.push("<a href=\"javascript:;\" class=\"nav-btn go-next\"></a></div>");
    wrap.push("<div class=\"cal_wrap\"><div class=\"days\">");
    wrap.push("<div class=\"day\">일</div>");
    wrap.push("<div class=\"day\">월</div>");
    wrap.push("<div class=\"day\">화</div>");
    wrap.push("<div class=\"day\">수</div>");
    wrap.push("<div class=\"day\">목</div>");
    wrap.push("<div class=\"day\">금</div>");
    wrap.push("<div class=\"day\">토</div></div><div class=\"dates\"></div></div></div>");
    $('#cal').html(wrap.join(""));
    // 날짜 정보 가져오기
    let date = new Date(); // 현재 날짜(로컬 기준) 가져오기
    let utc = date.getTime() + (date.getTimezoneOffset() * 60 * 1000); // uct 표준시 도출
    let kstGap = 9 * 60 * 60 * 1000; // 한국 kst 기준시간 더하기
    let today = new Date(utc + kstGap); // 한국 시간으로 date 객체 만들기(오늘)
	let thisMonth;
	if($('#sdate').val()==""){thisMonth= new Date(today.getFullYear(), today.getMonth(), today.getDate());}
	else{thisMonth= new Date($('#sdate').val());}
	
    // 달력에서 표기하는 날짜 객체
	
    
    let currentYear = thisMonth.getFullYear(); // 달력에서 표기하는 연
    let currentMonth = thisMonth.getMonth(); // 달력에서 표기하는 월
    let currentDate = thisMonth.getDate(); // 달력에서 표기하는 일
	let urlParams = new URLSearchParams(window.location.search); //현재 url params
	if(urlParams.has('idx')){renderCalender_ajax(thisMonth);}
	else{renderCalender(thisMonth);}
	
	
    function renderCalender(thisMonth) 
    {
		let calendar = [];
		
		// 렌더링을 위한 데이터 정리
		currentYear = thisMonth.getFullYear();
		currentMonth = thisMonth.getMonth();
		currentDate = thisMonth.getDate();
		
		// 이전 달의 마지막 날 날짜와 요일 구하기
		let startDay = new Date(currentYear, currentMonth, 0);
		let prevDate = startDay.getDate();
		let prevDay = startDay.getDay();
		
		// 이번 달의 마지막날 날짜와 요일 구하기
		let endDay = new Date(currentYear, currentMonth + 1, 0);
		let nextDate = endDay.getDate();
		let nextDay = endDay.getDay();
		
		//console.log(prevDate, prevDay, nextDate, nextDay);
		// 현재 월 표기
		$('.year-month').text(currentYear + '.' + (currentMonth + 1));
		
		// 렌더링 html 요소 생성
		// 지난달
		for (let i = prevDate - prevDay; i <= prevDate; i++) {
			calendar.push("<div class=\"day prev disable\">" + i + "</div>");
		}
		// 이번달
		if(prevDay == 6){calendar=[];}
		for (let i = 1; i <= nextDate; i++) {
			calendar.push("<div class=\"day current\">" + i + "</div>");
		}
		// 다음달
		for (let i = 1; i <= (7 - nextDay == 7 ? 6 : 6 - nextDay); i++) {
			calendar.push("<div class=\"day next disable\">" + i + "</div>")
		}
			
		$('.dates').html(calendar.join(""));


		$('.dates .current').click(function(){
			let mon = currentMonth+1;
			let day = $(this).text();
			if(mon<10){mon="0"+mon;}
			if(day<10){day="0"+day;}
			let val = currentYear+"-"+mon+"-"+day;
			if($(this).hasClass('today')){
				$(this).removeClass('today');
				for(let i=0; i< arr_day.length; i++){
					if(arr_day[i]== val){
						arr_day.splice(i, 1);
						i--;
					}
				}
			}else{
				arr_day.push(val);
				$(this).addClass('today');
			}
			$('#open_date').val(arr_day.join("|"));
		});
		
    }

	function renderCalender_ajax(thisMonth)
	{
		let calendar = [];
		$.ajax(
		{
			type:"POST",
			url: "/SuperAdmin/xml/edu_write.php",
			data: $('#fm').serialize(),
			dataType: "JSON",
			success:function(data){
				console.log(data);
				currentYear = thisMonth.getFullYear();
				currentMonth = thisMonth.getMonth();
				currentDate = thisMonth.getDate();
				
				let startDay = new Date(currentYear, currentMonth, 0);
				let prevDate = startDay.getDate();
				let prevDay = startDay.getDay();
				
				let endDay = new Date(currentYear, currentMonth + 1, 0);
				let nextDate = endDay.getDate();
				let nextDay = endDay.getDay();
				
				$('.year-month').text(currentYear + '.' + (currentMonth + 1));
				for (let i = prevDate - prevDay; i <= prevDate; i++) {
					calendar.push("<div class=\"day prev disable\">" + i + "</div>");
				}

				if(prevDay == 6){calendar=[];}
				for (let i = 1; i <= nextDate; i++) {
					calendar.push("<div class=\"day current\">" + i + "</div>");
				}

				for (let i = 1; i <= (7 - nextDay == 7 ? 6 : 6 - nextDay); i++) {
					calendar.push("<div class=\"day next disable\">" + i + "</div>")
				}
					
				$('.dates').html(calendar.join(""));
				
				$.each(data.open_date, function(idx,val){
					for(let i=0; i< arr_day.length; i++){
						if(arr_day[i]== val){
							arr_day.splice(i, 1);
							i--;
						}
					}
					arr_day.push(val);
				});

				$('.dates .current').click(function(){
					let mon = currentMonth+1;
					let day = $(this).text();
					if(mon<10){mon="0"+mon;}
					if(day<10){day="0"+day;}
					let val = currentYear+"-"+mon+"-"+day;
					if($(this).hasClass('today')){
						$(this).removeClass('today');
						for(let i=0; i< arr_day.length; i++){
							if(arr_day[i]== val){
								arr_day.splice(i, 1);
								i--;
							}
						}
					}else{
						arr_day.push(val);
						$(this).addClass('today');
					}
					$('#open_date').val(arr_day.join("|"));
				});

				reloadCalendar();
			}
		});
		
	}
	
    // 이전달로 이동
    $('.go-prev').on('click', function() {
		thisMonth = new Date(currentYear, currentMonth - 1, 1);
        if(urlParams.has('idx')){renderCalender_ajax(thisMonth);}
		else{renderCalender(thisMonth);}
		reloadCalendar();
    });
	
    // 다음달로 이동
    $('.go-next').on('click', function() {
		thisMonth = new Date(currentYear, currentMonth + 1, 1);
        if(urlParams.has('idx')){renderCalender_ajax(thisMonth);}
		else{renderCalender(thisMonth);}
		reloadCalendar();
    });
	
	function reloadCalendar()
	{
		$.each(arr_day, function(idx, val){
			let d = new Date(val);
			if(d.getMonth()==currentMonth && d.getFullYear() == currentYear)
			{
				$(`.dates .current:eq(${d.getDate() -1})`).addClass("today");
			}
		});
	}
}

</script>

