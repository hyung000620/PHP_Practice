<?
$page_code="1750";
include_once($_SERVER["DOCUMENT_ROOT"]."/inc/header.php");
?>
<!--  -->
<link rel="stylesheet" href="./css/cal.css?ver=<?=$_ver?>">
<div class='clear'></div>
<div id='edu_navi' style='display:flex; gap:15px'>
    <div class='eduSch' style='cursor:pointer;'>교육 일정</div>
    <div class='eduApp' style='cursor:pointer;'>교육 신청</div>
</div>
<div id='cal'></div>
<form id='calFrm'>
    <input type='hidden' name='mode' id='mode' value='1'>
    <input type='hidden' name='idx' id='idx' value=''>
</form>
<!--  -->
<? include_once($_SERVER["DOCUMENT_ROOT"]."/inc/footer.php"); ?>
<script>
$(function() {
    calendarInit();
    $('.eduSch').click(function(){calendarInit();});
    $('.eduApp').click(function(){boardInit();});
});

//calendar
function calendarInit() 
{
    $('#mode').val(1);
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
  
    let thisMonth = new Date(today.getFullYear(), today.getMonth(), today.getDate());
    // 달력에서 표기하는 날짜 객체
  
    
    let currentYear = thisMonth.getFullYear(); // 달력에서 표기하는 연
    let currentMonth = thisMonth.getMonth(); // 달력에서 표기하는 월
    let currentDate = thisMonth.getDate(); // 달력에서 표기하는 일

    // 캘린더 렌더링
    renderCalender(thisMonth);

    function renderCalender(thisMonth) 
    {
        let calendar = [];
        $.ajax(
        {
            type: "POST",
            url:"./ajax.php",
            data:$('#calFrm').serialize(),
            dataType: 'JSON',
            success: function(data) 
            {
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
                
                // 오늘 날짜 표기
                if (today.getMonth() == currentMonth) {
                    todayDate = today.getDate();
                    let currentMonthDate = document.querySelectorAll('.dates .current');
                    currentMonthDate[todayDate -1].classList.add('today');
                }
                
                $.each(data.item,function(){
                    let teacher = this.edu_teacher;
                    $.each(this.open_date, function(idx,val){
                        let d = new Date(val);
                        if(d.getMonth()==currentMonth && d.getFullYear() == currentYear)
                        {
                            $(`.dates .current:eq(${d.getDate() -1})`).append("<span class='bold' style='color:black;'>"+teacher+"</span>");
                        }
                    });
                });
            }
        });
        
    }

    // 이전달로 이동
    $('.go-prev').on('click', function() {
        thisMonth = new Date(currentYear, currentMonth - 1, 1);
        renderCalender(thisMonth);
    });

    // 다음달로 이동
    $('.go-next').on('click', function() {
        thisMonth = new Date(currentYear, currentMonth + 1, 1);
        renderCalender(thisMonth); 
    });
}
//board
function boardInit()
{
    let wrap = [];
    $('#mode').val(1);
    $.ajax(
    {
        type: "POST",
        url:"./ajax.php",
        data:$('#calFrm').serialize(),
        dataType: 'JSON',
        success:function(data){
            wrap.push("<div class='sec_cal'><table class='sec_tbl'><tbody>");
            wrap.push("<tr><th>구분</th><th>교육명</th><th>교육장</th><th>시작일시</th><th>종료일시</th>");
            wrap.push("<th>상태<select><option>전체</option><option>진행중</option><option>마감</option><option>모집중</option></select></th></tr>");
            $.each(data.item, function(){
            wrap.push("<tr onclick='boardView("+this.idx+")'>");
            wrap.push("<td>정기</td>");
            wrap.push("<td>"+this.edu_title+"</td>");
            wrap.push("<td>"+this.edu_addr+"</td>");
            wrap.push("<td>"+this.sdate+"</td>");
            wrap.push("<td>"+this.edate+"</td>");
            wrap.push("<td>"+this.state+"</td>");
            wrap.push("</tr>");
        })
        wrap.push("</tbody></table></div>");
        $('#cal').html(wrap.join(""));
    }
    })
}

function boardView(idx)
{
    let arr_list=[];
    $('#mode').val(2);
    $('#idx').val(idx);
    $.ajax(
    {
        type: "POST",
        url: "./ajax.php",
        data: $('#calFrm').serialize(),
        dataType: "JSON",
        success:function(data)
        {
            if(typeof data != 'undefined')
            {
                //$('#edu_navi').css('display','none');
                arr_list.push("<div><img src='https://www.tankauction.com/lecture/off_line/photo/"+data.photo_edu+"'></div>");
                arr_list.push("<div class='center'><span onclick='edu_pay()' class='btn_box_ss btn_tank radius_10' style='width:250px'>다음</span></div>")
                console.log(data);
                $('#cal').html(arr_list.join(""));
            }
        }
    });
}

function edu_pay()
{
    alert('로그인이 필요한 서비스입니다.');
}
</script>  