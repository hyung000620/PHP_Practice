<?

$thisyear = date('Y');
$thismonth = date('n');
$today = date('j');

$year = isset($year)? $year : $thisyear;
$month = isset($month)? $month : $thismonth;
$day = isset($day)? $day : $today;

$prev_month = $month - 1;
$next_month = $month + 1;
$prev_year = $next_year = $year;

if($month == 1)
{
    $prev_month = 12;
    $prev_year = $year - 1;
}
else if($month == 12)
{
    $next_month = 1;
    $next_year = $year + 1;
}
$prevYear = $year - 1;
$nextYear = $year + 1;

$preDate = date("Y-m-d", mktime(0,0,0,$month - 1, 1, $year));
$nextDate = date("Y-m-d", mktime(0,0,0,$month + 1, 1, $year));

$max_day = date('t', mktime(0,0,0,$month, 1 , $year)); // 총 일수 
$start_week = date('w', mktime(0,0,0,$month, 1 , $year)); // 시작 요일
$total_week = ceil(($max_day+$start_week) / 7);// 총 몇주
$last_week = date('w', mktime(0,0,0,$month, $max_day , $year)); // 마지막 요일
?>