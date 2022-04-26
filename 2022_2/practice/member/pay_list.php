<?
$member_only=true;
$dv=($_GET['dv'])?$_GET['dv']:"20";
$page_code="90".$dv;
include($_SERVER["DOCUMENT_ROOT"]."/inc/header.php");
?>

<div class='wrap'>
    <div class='li_teb'>
		<ul class='ul_teb'>
			<li name="20" style='width:50%;' <?if($dv==20){echo "class='on'";}?> >후원내역</li>
            <li name="30" style='width:50%;' <?if($dv==30){echo "class='on'";}?> >영수증 출력</li>
		</ul>
	</div>
    <div id="price" style="float:right; margin-top:30px;"></div>
    <div id='contents'></div>
    <form id="payFrm">
        <input type="hidden" id='mode' name="mode" value="<?=$dv?>">
        <input type="hidden" name="user_id" value="<?=$client_id?>">
    </form>
</div>

<?include($_SERVER["DOCUMENT_ROOT"]."/inc/footer.php");?>
<script type="text/javascript">
$(function(){
    if($('#mode').val()==20){pay_list();}
    if($('#mode').val()==30){receipt_list();}
    ulTebClk();
});

function ulTebClk()
{
    $('.ul_teb >li').click(function(){
        $('.ul_teb li').removeClass('on');
        let dv = $(this).attr('name');
        window.location = '<?=$PHP_SELF;?>?dv='+dv;
    })
}

function pay_list()
{
    let tot_price=[];
    let arr_list=[];
    $.ajax(
    {
        type: "POST",
        url: "/member/pay_list_res.php",
        data: $('#payFrm').serialize(),
        dataType: "JSON",
        success: function(data)
        {

            arr_list.push("<table style='border-collapse:collapse; width:100%'>");
            arr_list.push("<thead>");
            arr_list.push("<tr style='border-top:1px solid #1B43A9; border-bottom:2px solid #1B43A9; height:38px'>");
            arr_list.push("<th>결제방법</th>");
            arr_list.push("<th>후원금액</th>");
            arr_list.push("<th>신청일자</th>");
            arr_list.push("</tr>");
            arr_list.push("</thead>");
            arr_list.push("<tbody>");
            
            if(data.total_record==0)
            {
                arr_list.push("<tr>");
                arr_list.push("<td colspan='3' style='text-align:center; height:200px;'>");
                arr_list.push("신청하신 후원 내역이 없습니다.");
                arr_list.push("</td>");
                arr_list.push("</tr>");
            }
            else
            {
                $.each(data.item,function()
                {
                    arr_list.push("<tr style='text-align:center; border-bottom:1px solid #cccccc; height:30px;'>");
                    arr_list.push("<td>통장입금</td>");
                    arr_list.push("<td>"+this.pay_price+"</td>");
                    arr_list.push("<td>"+this.wdate+"</td>");
                    arr_list.push("</tr>");
                });
            }
            arr_list.push("</tbody>");
            arr_list.push("</table>");
            let tot = data.total_price.toLocaleString();
            
            tot_price.push("<span class='bold' style='font-size:24px;'>"+tot+"</span>");
            tot_price.push("<span>원</span>");
            tot_price.push("<span style='color:#8b8b8b'>(총 건)</span>");



            $('#price').html(tot_price.join(""));
            $('#contents').html(arr_list.join(""))
        }
    })
}

function receipt_list()
{
    let arr_list=[];
    let price=[];
    arr_list.push("<table style='width:100%;'>");   
    arr_list.push("<tr><td>- 기부금영수증은 주민등록번호 13자리가 모두 등록되어 있어야 발행 가능합니다. (소득세법 160조의 3, 시행령 208조의 3)</td></tr>")
    arr_list.push("<tr><td>- 올 해 1월~12월 기부하신 내역은 내년 1월 중순부터 국세청 홈택스에서 확인 가능합니다.</td></tr>");
    arr_list.push("<tr><td>- 기부금영수증 관련 문의 사항은 위드탱크(02-3424-3233)로 연락 부탁드립니다</td></tr>")
    arr_list.push("</table>");
    price.push("<span class='bold' style='font-size:22px;'>기부금 영수증</span>");
    $('#price').html(price.join(""));
    $('#contents').html(arr_list.join(""));
}
</script>