<?
    $page = 1;
    include_once($_SERVER["DOCUMENT_ROOT"]."/practice/inc/header.php");
    include_once($_SERVER["DOCUMENT_ROOT"]."/practice/inc/asd.php");
    include_once($_SERVER["DOCUMENT_ROOT"]."/practice/calendar/calendar.php");
    include_once($_SERVER["DOCUMENT_ROOT"]."/practice/calendar/holiday.php");

?>
<main>
    <div class="main">경매>기일별검색</div>
    <div class="cal_container">
        <form>
            <table class="tbl_req">
                <input type="hidden" value="<?=$month ?>" id="month">
                <tr>
                    <td>
                        <a href="<?='sho.php?year='.$prev_year.'&month='.$prev_month.'&day=1'; ?>">
                            <i class="fa-solid fa-chevron-left"></i>
                        </a>
                        <?=$year.="년 ". $month .="월" ?>
                        <a href="<?='sho.php?year='.$next_year.'&month='.$next_month.'&day=1'; ?>">
                            <i class="fa-solid fa-chevron-right"></i>
                        </a>
                    </td>
                    <td>
                        <select></select>
                    </td>
                </tr>
            </table>
        </form>
        <table class="tbl_response">
            <tr class="info">
                <th>일</th>
                <th>월</th>
                <th>화</th>
                <th>수</th>
                <th>목</th>
                <th>금</th>
                <th>토</th>
            </tr>
            <?  
                
                
                $html = "";
                for($day=1, $i=0; $i< $total_week; $i++)
                {
                    $html .= "<tr>";
                    for($j=0; $j<7; $j++)
                    {
                        $html .=  "<td ";
                        if(($day > 1 || $j >= $start_week) && ($day<=$max_day))
                        {   
                            
                            if($j == 0){$html .= " class='cal holy'>";}
                            else if($j==6){$html .=" class='cal blue'>";}
                            else if($j != 0 || $j!=6 ){$html .=" class='cal black'>";}
                            for($k=0; $k<count($Hoildays); $k++)
                            {
                                if($Hoildays[$k]['month'] == $month && $Hoildays[$k]['day'] == $day){
                                    $html .= "<div class='aa' style='color:red'>".$Hoildays[$k]['event']."</div>";
                                }
                            }

                            $html .= $day++;
                        }
                        $html .= "</td>";
                    }
                    $html .= "</tr>";
                }
                echo $html;
            ?>
        </table>
    </div>
    <div class="modal">
        <div class="modal_body">
            <h1>일정추가</h1>
        </div>
    </div>
</main>
<?
    include_once($_SERVER["DOCUMENT_ROOT"]."/practice/inc/footer.php");
?>
<script>
    var month = $("#month").val();
    $(".modal").hide();
    $(".cal").click( function(){
        var day = $(this).text();
        var rep = day.replace($(this).children('div').text(),'');
        // var re = prompt(month+"월"+day+"일에 일정을 입력해주세요");
        // $(this).append("<div>"+re+"</div>");
        $(".modal_body").append("<div>"+month+"월"+rep+"일"+"</div>");
        $(".modal").show();
    });
</script>