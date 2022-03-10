<?
$page_code="9016";
include_once($_SERVER["DOCUMENT_ROOT"]."/inc/header.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/inc/snb.php");
include($_SERVER["DOCUMENT_ROOT"]."/member/Toss.php");
switch($mode)
{
    case "refund" :
    {
        $SQL="UPDATE {$my_db}.tm_pay_log SET return_status = 'REFUND' WHERE order_no = '{$order_no}'";
        $stmt=$pdo->prepare($SQL);
        $stmt->execute();
    }break;

    case "cancel" :
    {   
        $SQL="SELECT * FROM {$my_db}.tm_pay_log WHERE order_no = '{$order_no}'";
        $stmt=$pdo->prepare($SQL);
        $stmt->execute();
        $rs=$stmt->fetch();
        $paymentKey=$rs['paymentkey'];

        $res=$toss->cancelPayment($paymentKey);
        $tossData=$res['resData'];
        $status=$tossData->status;
        echo $status;
        if($res['resCode']==200)
        {
            $SQL="UPDATE {$my_db}.tm_pay_log SET return_status = '{$status}' WHERE order_no = '{$order_no}'";
            $stmt=$pdo->prepare($SQL);
            $stmt->execute();
        }
    }break;
}
?>
<article>

    <?  
        
        #경매결제
        $stmt=$pdo->prepare("SELECT * FROM {$my_db}.tc_price WHERE use_key=1 ORDER BY sort_num");
        $stmt->execute();
        while($rs=$stmt->fetch()){$pi[$rs['state']]=array("area" => $rs['area'], "srv_area" => $rs['service_area']);}	 
        
    
        #강의결제
        $stmt=$pdo->prepare("SELECT * FROM {$my_db}.te_lecture WHERE 1");
        $stmt->execute();
        while($rs=$stmt->fetch()){$pi[$rs['lec_code']]=array("area" => $rs['course'], "srv_area" => $rs['teacher']);}
        

        $SQL="SELECT * FROM {$my_db}.tm_pay_log WHERE id ='{$client_id}' AND return_status = 'DONE' OR return_status = 'REFUND' OR return_status = 'CANCELED'";
        $stmt=$pdo->prepare($SQL);
        $stmt->execute();        
        $col=$stmt->fetchColumn();
        $html = "";
        $html.= "<form id='refund_frm' method='post'>";
        $html.="<span class='f18 bold'>결제 내역</span>";
        $html.="<table class='tbl_grid'>";
        $html.="<tr height='40'>";
        $html.="<th>No</th>";
        $html.="<th>구분</th>";
        $html.="<th>구매내역</th>";
        $html.="<th>구매일시</th></th>";
        $html.="<th>구매상태</th></th>";
        $html.="<th>금액</th>";
        $html.="</tr>";
        
        while($rs=$stmt->fetch())
        {
            $n++;
            $log=json_decode($rs['log_text']);
            $amt=$log->amt;
            $smp=$log->smp;
            $pay_code=$log->pay_code;
            $pay_opt=$log->pay_opt;
            $pay_opt=($pay_opt==1)?"카드":"가상계좌";
            $smp_arr=explode(",",$smp);
            $arr=array();
            foreach($smp_arr as $v)
            {
                list($state,$month,$price)=explode(":",$v);
                $month=($pay_code==100)? "{$month} 개월" : "{$month} 일";
                array_push($arr,$pi[$state]['area'].">". $month." ");
            }
            $str=implode(",",$arr);
            $html.="<tr height='40'>";
            $html.="<td style='width:25px;'class='center'><input type='checkbox' name ='order_no' value='{$rs['order_no']}'><label>{$n}</label></td>";
            $html.="<td style='width:50px;'class='center'>{$pay_opt}</td>";
            $html.="<td class='center'>{$str}</td>";
            $html.="<td style='width:130px;' class='center'>{$rs['wdate']}</td>";
            $html.="<td style='width:100px;' class='center bold orange'>".$status_arr[$rs['return_status']]."</td>";
            $html.="<td style='width:100px;' class='center'>".number_format($amt)."</td>";
            $html.="</tr>";
        }
        if($col == 0){$html .= "<div class='center'>결제하신 내역이 없습니다.</div>";}
    $html.="</table>";
    $html.="</form>";
    echo $html;
    ?>
    <div class='center' style='padding:20px'><span id='cancel' class='btn_box_ss btn_tank radius_10' style='width:110px'>결제 취소</span></div>
    <div class='center' style='padding:20px'><span id='refund' class='btn_box_ss btn_tank radius_10' style='width:110px'>환불 요청</span></div>
    <div class='center' style='padding:20px'><a href='/'><span class='btn_box_ss btn_tank radius_10' style='width:110px'>홈으로 가기</span></a></div>

</article>

<? include_once($_SERVER["DOCUMENT_ROOT"]."/inc/footer.php"); ?>

<script>
    $(document).ready(function(){
        //클릭 하나만.
        $('input[type="checkbox"][name="order_no"]').click(function(){
        if($(this).prop('checked')){
        $('input[type="checkbox"][name="order_no"]').prop('checked',false);
        $(this).prop('checked',true);
        }
        });

        $("#refund").click(function(){
            if(confirm('선택하신 건에 대하여 환불요청을 보내시겠습니까?')){
                refund();
            }
        });
    
        $("#cancel").click(function(){
            if(confirm('결제 취소를 도와드릴까요?')){
            }
        });
        
    })

    //환불 요청
    function refund()
    {
        $("#refund_frm").attr("action","pay_history.php?mode=refund");
        $("#refund_frm").submit();
    }

    //취소 처리
    function cancel()
    {
        $("#refund_frm").attr("action","pay_history.php?mode=cancel");
        $("#refund_frm").submit();
    }

    
</script>