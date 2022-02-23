<?
$SQL="SELECT * FROM db_dev.ta_calender WHERE 1";
$stmt=$pdo->prepare($SQL);
$stmt->execute();

$Hoildays = array();

while($rs= $stmt->fetch())
{
    $Hoildays[] = array("month"=>$rs['month'],"day"=>$rs['day'],"event"=>$rs['event']);
}

?>