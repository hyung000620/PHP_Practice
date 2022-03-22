<?
$page_code="9016";
include_once($_SERVER["DOCUMENT_ROOT"]."/inc/header.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/inc/snb.php");
include($_SERVER["DOCUMENT_ROOT"]."/member/Toss.php");
switch($mode)
{
    case "modal":
    {   
        $orderId=(int)$orderId;
        $dataArr=array();
        $SQL="SELECT * FROM {$my_db}.tm_pay_log WHERE order_no={$orderId} LIMIT 0,1";
        $stmt=$pdo->prepare($SQL);
        $stmt->execute(); 
        $rs=$stmt->fetch();
        $res=json_decode($rs['result_data'],true);
        $dataArr['amount']=$rs['amt'];
        $dataArr['accountNumber']=$res['accountNumber'];
        $dataArr['accountType']=$res['accountType'];
        $dataArr['bank']=$res['bank'];
        $dataArr['customerName']=$res['customerName'];
        $dataArr['dueDate']=$res['dueDate'];
        $dataArr['expired']=$res['expired'];
        $dataArr['settlementStatus']=$res['settlementStatus'];
        $dataArr['refundStatus']=$res['refundStatus'];

        $result=json_encode($dataArr,JSON_UNESCAPED_UNICODE);
        echo($result);
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
        $html = "";
        // #가상계좌(날짜비교)
        // $today=strtotime(date('Y-m-d'));
        // $target=strtotime('2022-03-21');
        // if($today>=$target){echo "기간만료";}

        // $html .="<span class='f18 bold'>거래 정산</span>";
        // $date="2022-03-22";
        // $res=$toss->search_transaction($date);
        // echo json_encode($res['resData'], JSON_UNESCAPED_UNICODE);
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
        $html.="<th>영수증발행</th>";
        $html.="<th>결제취소</th>";
        $html.="</tr>";
        
        while($rs=$stmt->fetch())
        {
            $n++;
            $log=json_decode($rs['log_text'],true);
			      $rs_data = json_decode($rs['result_data'],true);  // 추가
			      $accountNumber = $rs_data['accountNumber'];  // 추가  
			      $accountType = $rs_data['accountType'];  // 추가
            foreach($log as $k=>$v){${$k}=$v;} //변경

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
            if ($status_arr[$rs['return_status']]) {
            $html.="<tr height='40'>";
            $html.="<td style='width:25px;'class='center'><input type='checkbox' name='order_no' class='order_no' value='{$rs['order_no']}'><label>{$n}</label></td>";
            if($pay_opt=='카드'){$html.="<td style='width:50px;'class='center pay_opt'>{$pay_opt}</td>";}
            elseif($pay_opt=='가상계좌'){$html.="<td style='width:50px;'class='center pay_opt'><a href='#ex1' onclick='vir_result({$rs['order_no']});' rel='modal:open' data-value='{$rs['order_no']}'>{$pay_opt}</a></td>";}
            $html.="<td class='center str'>{$str}</td>";
            $html.="<td style='width:130px;' class='center wdate'>{$rs['wdate']}</td>";
            $html.="<td style='width:100px;' class='center bold orange status'>".$status_arr[$rs['return_status']]."</td>";
            $html.="<td style='width:100px;' class='center amt'>".number_format($amt)."</td>";
            if($rs['return_status'] == 'DONE' && $rs['receipt'] != null) {      // 추가 (영수증)
				    $html .= "<td><button><a href='{$rs['receipt']}' target='_blank'>영수증 출력</a></button></td>";
			      } else if($rs['return_status'] == 'DONE' && $rs['receipt'] == null) {
            $html .= "<td><button><a>현금영수증 발행</a></button></td>";
			      } else {
                $html .= "<td class='center'> - </td>";
			      }
            if($rs['return_status'] == 'WAITING_FOR_DEPOSIT') {      // 추가 (결제취소)
            $html.="<td style='width:100px;' class='center amt'>{$accountNumber}</td>";
			      } else if ($rs['return_status'] == 'DONE'){
            $html.="<td style='width:100px;' class='center amt'>결제취소</td>";
            }
            $html.="</tr>";
            }
        }
        if($col == 0){$html .= "<div class='center'>결제하신 내역이 없습니다.</div>";}
    $html.="</table>";
    $html.="</form>";
    echo $html;
    ?>
    <!-- modal -->
    <div id='ex1' class='modal center'>
        <div id="ex1_content">
        </div>
    </div>

    <div class="refund_modal">
        <div class="refund_modal_content">
            <a href="javascript:;" class="close">X</a>
            <form>
                <table class='tbl_grid border'>
                    <tr>
                        <th colspan="2"><span style='font-size:30px;font-weight:bold;margin-right:20px' class='f18 bold'>결제취소</span></th>
                    </tr>
                    <tr>
                        <td>NO</td>
                        <td><input type='text' id='model_no' name ='order_no' value='' disabled></td>
                    </tr>
                    <tr>
                        <td>구분</td>
                        <td><input type='text' id='model_no'name ='pay_opt' value='' disabled></td>
                    </tr>
                    <tr>
                        <td>구매내역</td>
                        <td><input type='text' id='model_no'name ='str' value='' disabled></td>
                    </tr>
                    <tr>
                        <td>구매일시</td>
                        <td><input type='text' id='model_no'name ='order_no' value='' disabled></td>
                    </tr>
                    <tr>
                        <td>구매상태</td>
                        <td><input type='text' id='model_no'name ='order_no' value='' disabled></td>
                    </tr>
                    <tr>
                        <td>금액</td>
                        <td><input type='text' id='model_no'name ='order_no' value='' disabled></td>
                    </tr>
                    <tr>
                        <td>환불사유</td>
                        <td>
                            <sapn name="refund_reason"></sapn>
                            <select>
                            <? foreach($refund_arr as $k => $v){echo "<option value='{$k}'>".$v."</option>";} ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="center"><span> 더 나은 탱크옥션이 되게 노력하겠습니다.</span></td>
                    </tr>
                </table>
            </form>
            <div class='center' style='padding:20px'><span id='cancel_success' class='btn_box_ss btn_tank radius_10' style='width:110px;'>결제 취소</span></div>
        </div>
    </div>
    <div class='center' style='padding:20px'><span id='cancel' class='btn_box_ss btn_tank radius_10' style='width:110px'>결제 취소</span></div>
    <!-- <div class='center' style='padding:20px'><span id='refund' class='btn_box_ss btn_tank radius_10' style='width:110px'>환불 요청</span></div> -->
    <div class='center' style='padding:20px'><a href='/'><span class='btn_box_ss btn_tank radius_10' style='width:110px'>홈으로 가기</span></a></div>

</article>

<? include_once($_SERVER["DOCUMENT_ROOT"]."/inc/footer.php"); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.css" />
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
                $("#model_no").val(this.value);
                console.log(this);
                $(".refund_modal").fadeIn(300);
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

function vir_result(n)
{
    $.ajax(
    {
        type: "POST",
        url: "/member/ZPay_result.php?mode=modal&orderId="+n,
        dataType: "JSON",
        success: function(data)
        {
            //console.log(data);
            if(typeof data!="undefined")
            {
                var arr_html=[];
                arr_html.push("<div style='padding:10px 10px 0 10px;'>");
                arr_html.push("  <div style='height:50px;text-align:center'>");
                arr_html.push("     <div style='font-size:14px'>아래 가상계좌로 입금해 주시면 <br>정상적으로 결제 완료처리가 됩니다.</div>");
                arr_html.push("   </div>");
                arr_html.push("  <table class='tbl_grid'>");
                arr_html.push("   <tr><th style='width:100px'>구매자명</th><td style='width:200px'>"+data.customerName+"</td></tr>");
                arr_html.push("   <tr><th>가상계좌 정보</th><td>"+data.accountNumber+"</td></tr>");
                arr_html.push("   <tr><th>은행</th><td>"+data.bank+"</td></tr>");
                arr_html.push("   <tr><th>예금주</th><td>(주)탱크옥션</td></tr>");
                arr_html.push("   <tr><th>결제금액</th><td>"+number_format(data.amount)+" 원</td></tr>");
                arr_html.push("   <tr><th>입금기일</th><td>"+data.dueDate+"</td></tr>");
                arr_html.push("  </table>");
                arr_html.push("</div>");
                $("#ex1_content").html(arr_html.join(""))
            }
        }
        
    });
}
</script>