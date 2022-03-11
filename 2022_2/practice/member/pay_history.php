<?
$page_code="9016";
include_once($_SERVER["DOCUMENT_ROOT"]."/inc/header.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/inc/snb.php");
include($_SERVER["DOCUMENT_ROOT"]."/member/Toss.php");
switch($mode)
{
    case "modal":
    {
        
    }break;
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
        if($rs=$stmt->fetch())
        {
            $paymentKey=$rs['paymentkey'];
        }
        
        #카드(전액 취소)
        $data=['cancelReason'=>"고객이 취소를 원함"];
        #카드(부분 취소)-cancelAmount 추가. 
        $data=['cancelReason'=>"고객이 취소를 원함","cancelAmount"=>10000];
        
        #가상계좌-환불받을 계좌정보 추가.
        $refundReceiveAccount=array(
            "bank"=>"우리",
            "accountNumber"=>"1000123456789",
            "holderName"=>"김토페"
        ); 
        $data2=['cancelReason'=>"고객이 취소를 원함","cancelAmount"=>10000,"refundReceiveAccount"=>$refundReceiveAccount];
        #안전하게 취소 - refundableAmount 추가.
        $data2=['cancelReason'=>"고객이 취소를 원함","cancelAmount"=>10000,"refundReceiveAccount"=>$refundReceiveAccount,"refundableAmount"=>"전체금액"];
        #취소
        $res=$toss->cancelPayment($paymentKey,$data);
        $tossData=$res['resData'];
        $status=$tossData->status;

        if($res['resCode']==200)
        {
            $SQL="UPDATE {$my_db}.tm_pay_log SET return_status = '{$status}' WHERE order_no = {$order_no}";
            $stmt=$pdo->prepare($SQL);
            $stmt->execute();
        }
    }break;
}
?>
<link rel="stylesheet" type="text/css" href="/css/test.css?v=<?=$_ver?>">
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
        

        $SQL="SELECT * FROM {$my_db}.tm_pay_log WHERE id ='{$client_id}'";
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
            $html.="<td style='width:25px;'class='center'><input type='checkbox' name='order_no' class='order_no' value='{$rs['order_no']}'><label>{$n}</label></td>";
            $html.="<td style='width:50px;'class='center' name='pay_opt'>{$pay_opt}</td>";
            $html.="<td class='center' name='pay_opt'>{$str}</td>";
            $html.="<td style='width:130px;' class='center' name='wdate'>{$rs['wdate']}</td>";
            $html.="<td style='width:100px;' class='center bold orange' name='status'>".$status_arr[$rs['return_status']]."</td>";
            $html.="<td style='width:100px;' class='center' name='amt'>".number_format($amt)."</td>";
            $html.="</tr>";
        }
        if($col == 0){$html .= "<div class='center'>결제하신 내역이 없습니다.</div>";}
    $html.="</table>";
    $html.="</form>";
    echo $html;
    ?>
    <div class="refund_modal">
        <div class="refund_modal_content">
            <a href="javascript:;" class="close">X</a>
            <form>
                <table class='tbl_grid border'>
                    <tr>
                        <th colspan="2"><span style='font-size:30px;font-weight:bold;margin-right:20px'
                                class='f18 bold'>결제취소</span></th>
                    </tr>
                    <tr>
                        <td>NO</td>
                        <td><span class='modal_no' name="order_no"><?=$order_no?></span></td>
                    </tr>
                    <tr>
                        <td>구분</td>
                        <td><span name="pay_opt"></span><?=$pay_opt?></span></td>
                    </tr>
                    <tr>
                        <td>구매내역</td>
                        <td><span name="str"></span></td>
                    </tr>
                    <tr>
                        <td>구매일시</td>
                        <td><span name="wdate"></span></td>
                    </tr>
                    <tr>
                        <td>구매상태</td>
                        <td><span name="status"></span></td>
                    </tr>
                    <tr>
                        <td>금액</td>
                        <td><span name="amt"></span></td>
                    </tr>
                    <tr>
                        <td>환불사유</td>
                        <td>
                            <sapn name="refund_reason"></sapn>
                            <select>
                                <?
                            foreach($refund_arr as $k => $v)
                            {
                                echo "<option value='{$k}'>".$v."</option>";
                            }
                            ?>
                        </td>
                        </select>
                    </tr>
                    <tr>
                        <td colspan="2" class="center"><span> 더 나은 탱크옥션이 되게 노력하겠습니다.</span></td>
                    </tr>
                </table>
            </form>
            <div class='center' style='padding:20px'><span id='cancel_success' class='btn_box_ss btn_tank radius_10'
                    style='width:110px;'>결제 취소</span></div>
        </div>
    </div>
    <div class='center' style='padding:20px'><span id='cancel' class='btn_box_ss btn_tank radius_10'
            style='width:110px'>결제 취소</span></div>
    <!-- <div class='center' style='padding:20px'><span id='refund' class='btn_box_ss btn_tank radius_10' style='width:110px'>환불 요청</span></div> -->
    <div class='center' style='padding:20px'><a href='/'><span class='btn_box_ss btn_tank radius_10'
                style='width:110px'>홈으로 가기</span></a></div>

</article>

<? include_once($_SERVER["DOCUMENT_ROOT"]."/inc/footer.php"); ?>

<script>
$(document).ready(function() {
    //클릭 하나만.
    $('input[type="checkbox"][name="order_no"]').click(function() {
        if ($(this).prop('checked')) {
            $('input[type="checkbox"][name="order_no"]').prop('checked', false);
            $(this).prop('checked', true);
        }
    });

    $("#cancel").click(function() {
        $("#refund_frm input:checkbox").each(function() {
            if (this.checked) {
                // $('.modal_no').empty();
                $("#refund_frm").attr("action", "pay_history.php");
                $("#refund_frm").submit();
                setTimeout(function(){$(".refund_modal").fadeIn(300);},1000);
            }
        });
        if (!$(".order_no").is(':checked')) {
            alert('선택된 항목이 없습니다.');
        }

    });
    $("#refund").click(function() {
        if (confirm('선택하신 건에 대하여 환불요청을 보내시겠습니까?')) {
            refund();
        }
    });

    $("#cancel_success").click(function() {
        cancel();
    });


    $(".close").click(function() {
        $(".refund_modal").fadeOut(300);
    });
})

//환불 요청
function refund() {
    $("#refund_frm").attr("action", "pay_history.php?mode=refund");
    $("#refund_frm").submit();
}

//취소 처리
function cancel() {
    $("#refund_frm").attr("action", "pay_history.php?mode=cancel");
    $("#refund_frm").submit();
    $(".refund_modal").fadeOut(300);
}
</script>