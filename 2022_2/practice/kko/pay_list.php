<?
$member_only=true;
$dv=($_GET['dv'])?$_GET['dv']:"20";
$page_code="90".$dv;
include($_SERVER["DOCUMENT_ROOT"]."/inc/header.php");

$SQL="SELECT * FROM {$my_db}.tm_pay_list WHERE id='{$client_id}'";
$stmt=$pdo->prepare($SQL);
$stmt->execute();
$cnt=$stmt->rowCount();

$html="";
$total_price=0;
if($cnt==0)
{
    $html.="<tr>";
    $html.="<td colspan='3' style='text-align:center; height:200px;'>신청하신 후원 내역이 없습니다.</td>";
    $html.="</tr>";
}
while($rs=$stmt->fetch())
{
    $html.="<tr style='text-align:center; border-bottom:1px solid #cccccc; height:30px;'>";
    $html.="<td>통장입금</td>";
    $html.="<td>{$rs['pay_price']}</td>";
    $html.="<td>{$rs['wdate']}</td>";
    $html.="</tr>";
    $total_price += $rs['pay_price'];
}
?>
<div class='wrap'>
    <div class='li_teb'>
		<ul class='ul_teb'>
			<li name="10" style='width:50%;' <?if($dv==20){echo "class='on'";}?> >후원내역</li>
            <li name="20" style='width:50%;' <?if($dv==30){echo "class='on'";}?> >영수증 출력</li>
		</ul>
	</div>
    <div class="right" style='padding:10px;'>
        <span class="bold" style='font-size:30px;'><?=number_format($total_price)?></span>
        <span>원</span>
        <span style="color:#8b8b8b">(총 건)</span>
    </div>
    <table style='border-collapse:collapse; width:100%'>
        <thead>
        <tr style='border-top:1px solid #1B43A9; border-bottom:2px solid #1B43A9; height:38px'>
            <th>결제방법</th>
            <th>후원금액</th>
            <th>신청일자</th>
        </tr>
        </thead>
        <tbody>
            <?=$html?>
        </tbody>
    </table>
</div>

<?include($_SERVER["DOCUMENT_ROOT"]."/inc/footer.php");?>