<?
  $page_code="9016";
  $member_only=true;
  include_once($_SERVER["DOCUMENT_ROOT"]."/inc/header.php");
  include_once($_SERVER["DOCUMENT_ROOT"]."/inc/snb.php");
  include_once($_SERVER["DOCUMENT_ROOT"]."/member/Toss.php");
?>
  <div id="paylist_block" style='float:left;width:100%;margin:20px 0'>
    <div class="bold left f18"> 결제 내역</div>
    <table class="tbl_grid border">
       <thead id="rsThead"></thead>
       <tbody id="rsTbody"></tbody>
    </table>
  </div> 
  <div id="paywait_block" style='float:left;width:100%;margin-bottom:25px'>
    <div class="bold left f18"> 최근 신청/접수 내역</div>
    <table class="tbl_grid border">
       <thead id="wtThead"></thead>
       <tbody id="wtTbody"></tbody>
    </table>
  </div>
  <div id="tosspaylist_block" style='float:left;width:100%;margin-bottom:25px;display:none'>
    <div class="bold left f18"> 토스결제 내역</div>
    <table class="tbl_grid border">
       <thead id="rtThead"></thead>
       <tbody id="rtTbody"></tbody>
    </table>
  </div> 
  
  <div class="center" style="padding:20px">
     <a href="/member/pay.php"><span class="btn_box_ss btn_tank radius_10" style="width:110px;-moz-border-radius:20px;border-radius:20px;-webkit-border-radius: 20px;box-shadow: 0 2px 10px 0 rgba(0, 0, 0, 0.50)">결제하기</span></a>
  </div>
  
<!-- pop -->
<div id="POP_dialog" style="display:none" onclick="tools_etc_cls_();">
	<div class="POP_dialog_mask"></div>
	<div class="POP_dialog_block"><div id="POP_content" class="POP_content" onclick="event.cancelBubble=true"></div></div>
</div>

<div id="bank_info">
<?
//계좌 안내
bank_info();
?>
</div>
<div id="bank_info_mv" style="display:none">
<?
//동영상 계좌 안내
bank_info_mv();
?>
</div>
<? include_once($_SERVER["DOCUMENT_ROOT"]."/inc/footer.php"); ?>

<script>
$(document).ready(function() 
{
  //결제리스트
  payresult_();
  
  //가상결제, 무통장 대기리스트
  paywait_();
  
  //toss 결제
  //payresult_toss();
  //$("#tosspaylist_block").hide();
})

//number_format
function number_format(n){return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g,",");}

//pay_wait (무통장, 가상계좌)
function paywait_()
{
  var arr_head=[];
  var arr_body=[];
  $.ajax(
	{
	  type: "POST",
		url: "/res/payResult.php?mode=3",
		dataType: "JSON",
		success: function(data)
		{
		  arr_head.push("<tr>")
		  arr_head.push(" <th>결제구분</th>");
		  arr_head.push(" <th style='width:120px'>결제지역</th>");
		  //arr_head.push(" <th>이용료</th>");
		  arr_head.push(" <th>결제(예정)금액</th>");
		  arr_head.push(" <th>입금정보</th>");
		  arr_head.push(" <th>접수시간</th>");
		  arr_head.push(" <th>입금만료시간</th>");
		  arr_head.push("</tr>")
		  $("#wtThead").html(arr_head.join(""));
		  if(typeof data.item!="undefined")
			{
			  $("#paywait_block").show();
  		  $.each(data.item,function()
  			{
  			  var stateArea=this.stateArea.replace(/\,/g,"<br>");
  			  arr_body.push("<tr>");
  			  //if(this.paykind=='가상계좌'){arr_body.push(" <td class='center' onclick='vinfo("+this.order_no+");' style='color:blue;cursor:pointer'>"+this.paykind+"</td>");}
  			  //else{arr_body.push(" <td class='center'>"+this.paykind+"</td>");}
  			  arr_body.push(" <td class='center'>"+this.paykind+"</td>");
  			  arr_body.push(" <td class='left'>"+stateArea+"</td>");
  			  //arr_body.push(" <td class='center'>"+this.srv_price+"</td>");
  			  arr_body.push(" <td class='center'>"+number_format(this.pay_price)+" 원</td>");
  			  if(this.paykind=="가상계좌")
  			  {
  			    arr_body.push(" <td class='center'>가상계좌번호<br>"+this.accountBank+"("+this.customerName+") - <span class='bold'>"+this.accountNumber+"<span></td>");
  			  }
  			  else
  			  {
           arr_body.push(" <td class='center'>무통장입금 계좌안내</td>");
          }
  			  arr_body.push(" <td class='center'>"+this.wdate+"</td>");	
  			  arr_body.push(" <td class='center'>"+this.duedate+"</td>");			  
  			  arr_body.push("</tr>");
  			});
			  $("#wtTbody").html(arr_body.join(""));
			}
			else
			{
        $("#paywait_block").hide();
      }
		}
	});
}

//pay result & pay list
function payresult_()
{
  var arr_head=[];
  var arr_body=[];

  $.ajax(
	{
	  type: "POST",
		url: "/res/payResult.php?mode=1",
		dataType: "JSON",
		success: function(data)
		{
		  arr_head.push("<tr>");
		  arr_head.push(" <th>No</th>");
		  arr_head.push(" <th>결제지역</th>");
		  arr_head.push(" <th>결제구분</th>");
		  arr_head.push(" <th>구분</th>");
		  arr_head.push(" <th>결제일</th>");
		  arr_head.push(" <th>시작일</th>");
		  arr_head.push(" <th>만료일</th>");
		  arr_head.push(" <th>잔여일</th>");
		  arr_head.push(" <th>결제금액 (원)</th>");
		  arr_head.push(" <th>영수증</th>");
		  arr_head.push("</tr>");
		  $("#rsThead").html(arr_head.join(""));
		  if(typeof data.item!="undefined")
			{
			  $.each(data.item,function()
				{
          var vinfo=(this.ACCOUNTNUMBER)? "<span onclick='vinfo("+this.ORDER_NO+");'  style='padding:3px;background-color:#2879FF;color:#fff;cursor:pointer;-moz-border-radius:10px;border-radius:10px;-webkit-border-radius: 10px;box-shadow: 0 2px 10px 0 rgba(0, 0, 0, 0.50)'>가상계좌정보</span>" : this.PKD ;		  
				  arr_body.push("<tr>");
				  arr_body.push(" <td class='center'>"+this.NO+"</td>");
				  arr_body.push(" <td class='center'>"+this.SECTOR+"</td>");
				  arr_body.push(" <td class='center'>"+this.PKD +"</td>");
				  arr_body.push(" <td class='center'>"+this.PAY_CODE+"</td>");
				  arr_body.push(" <td class='center'>"+this.PDATE+"</td>");
				  arr_body.push(" <td class='center'>"+this.START+"</td>");
				  arr_body.push(" <td class='center'>"+this.EXPIRE+"</td>");
				  var remain=(this.TBLKEY==1)? number_format(this.REMAIN) : " - ";
				  arr_body.push(" <td class='center'>"+remain+"</td>");		  
				  if(this.ORDER_NO!=this.PREV){arr_body.push(" <td class='right' rowspan='"+this.ROWSPAN+"'>"+number_format(this.PMONEY)+"</td>");}
				  if(this.ORDER_NO!=this.PREV)
				  {
				    if(this.RECEIPT_TYPE && this.REC_ORDERID==this.ORDER_NO){arr_body.push(" <td class='center' rowspan='"+this.ROWSPAN+"'><a href='"+this.RECEIPT+"' target='_blank' style='color:blue;cursor:pointer'>"+this.RECEIPT_TYPE+"</a></td>");}
				    else{arr_body.push(" <td class='center' rowspan='"+this.ROWSPAN+"'><span style='color:red;cursor:pointer' onclick='receipt_call(\""+this.ORDER_NO+"\","+this.PMONEY+",\"탱크옥션\");'>영수증신청</span></td>");}
				  }
				  arr_body.push("</tr>");
			  });
			}
			else{arr_body.push("<tr><td colspan='11' class='center' style='line-height:60px;vertical-align:middle;font-size:14px'>결제하신 지역이 없습니다.</td></tr>");}
      $("#rsTbody").html(arr_body.join(""));
		}
	});  
}
function vinfo(n)
{
  $.ajax(
  {
	  type: "POST",
		url: "/res/payResult.php?mode=9&orderId="+n,
		dataType: "JSON",
		success: function(data)
		{
      if(typeof data!="undefined")
      {
         //가상계좌 정보
        var arr_html=[];
        $("#POP_content").empty();
        $("#POP_dialog").show();
        arr_html.push("<div style='padding:10px 10px 0 10px;width:300px'>");
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
        arr_html.push("  <div style='width:100%;text-align:center;line-height:50px;vertical-align:middle;'><span onclick='tools_etc_cls_();' style='padding:3px 10px;background-color:#2879FF;color:#fff;cursor:pointer;-moz-border-radius:10px;border-radius:10px;-webkit-border-radius: 10px;box-shadow: 0 2px 10px 0 rgba(0, 0, 0, 0.50)'>닫기<span></div>");
        arr_html.push("</div>");
        $(".POP_content").html(arr_html.join(""));
      }
      else{}      
		}    
  });
}


function payresult_toss()
{
  var arr_head=[];
  var arr_body=[];
  
  $.ajax(
  {
	  type: "POST",
		url: "/res/payResult.php?mode=2",
		dataType: "JSON",
		success: function(data)
		{
		  if(typeof data.item!="undefined")
			{
			  $("#tosspaylist_block").show();
			  arr_head.push("<tr>");
			  arr_head.push(" <th>NO</th>");
			  arr_head.push(" <th>구분</th>");
			  arr_head.push(" <th>결제지역</th>");
			  arr_head.push(" <th>결제일</th>");
			  arr_head.push(" <th>결제상태</th>");
			  arr_head.push(" <th>결제금액 (원)</th>");
			  arr_head.push(" <th>영수증</th>");
			  arr_head.push("</tr>");			  
			  $.each(data.item,function()
				{
				  arr_body.push("<tr>");
				  arr_body.push(" <td class='center'>"+this.no+"</td>");
				  arr_body.push(" <td class='center'>"+this.paytype+"</td>");
				  arr_body.push(" <td class='center'>"+this.paylist+"</td>");
				  arr_body.push(" <td class='center'>"+this.paydate+"</td>");
				  arr_body.push(" <td class='center'>"+this.paystatus_str+"</td>");
				  arr_body.push(" <td class='right'>"+number_format(this.amount)+"</td>");
				  if(this.paystatus==="DONE")
				  {
				    if(this.receipt)
				    {
				      arr_body.push(" <td class='center'>");
				      arr_body.push("   <a href='"+this.receipt+"' target='_blank'  style='color:blue'>"+this.receipt_type+"</a>");
				      arr_body.push(" </td>");
				    }
				    else 
				    {
				      //영수증 없을때 1회 신청
              arr_body.push(" <td class='center'><span style='color:red;cursor:pointer' onclick='receipt_call(\""+this.orderId+"\","+this.amount+",\"탱크옥션\");'>영수증신청</span></td>"); 
            }
				  }
				  else{arr_body.push(" <td class='center'> - </td>");}			  
				  arr_body.push("</tr>");				  
				});
				//적용
        $("#rtThead").html(arr_head.join(""));
        $("#rtTbody").html(arr_body.join(""));
			}
			else{$("#tosspaylist_block").hide();}
		}    
  });
}

//영수증발급
function  receipt_call(o,a,n)
{
	var arr_html=[];
	$("#POP_content").empty();
	$("#POP_dialog").show();
	arr_html.push("<form id='fm_data' name='fm_data' method='post'>");
	arr_html.push(" <input type='hidden' name='amount' id='amount' value="+a+">");
	arr_html.push(" <input type='hidden' name='orderId' id='orderId' value="+o+">");
	arr_html.push(" <input type='hidden' name='orderName' id='orderName' value="+n+">");
	arr_html.push(" <div class='login_pop' style='width:420px;margin:20px;'>");
	arr_html.push(" <table class='tbl_grid'>");
	arr_html.push("   <tr><th>상품명</th><td>"+n+"</td></tr>");
	arr_html.push("   <tr><th>주문금액</th><td>"+number_format(a)+"원</td></tr>");
	arr_html.push("   <tr>");
	arr_html.push("    <th width='100px' class='center'>현금영수증</th>");
	arr_html.push("    <td>");
	arr_html.push("      <input type='radio' name='type' value='1' onclick='type_option(1);'> 소득공제용 &nbsp; &nbsp;");
	arr_html.push("      <input type='radio' name='type' value='2' onclick='type_option(2);'> 지출증빙용");
	arr_html.push("    </td>");
	arr_html.push("   </tr>");
	arr_html.push("   <tr>");
	arr_html.push("    <th>발급번호</th>");
	arr_html.push("    <td>");
	arr_html.push("       <select name='registration' id='registration' style='width:110px' onchange='registration_change(this.value);'></select>");
	arr_html.push("       &nbsp; <input type='text' name='registrationNumber' id='registrationNumber' style='width:170px'  onKeyup='onlyNumber(this);'>");
	arr_html.push("    </td>");
	arr_html.push("   </tr>");	
	arr_html.push(" </table>");
	arr_html.push(" <div style='width:100%;text-align:center;line-height:70px;vertical-align:middle'>");
	arr_html.push("  <span style='padding:3px 10px;-moz-border-radius:10px;-webkit-border-radius:10px;border-radius:10px;background-color:#007aff;color:#fff;font-size:14px;cursor:pointer' onclick='receipt_issue();'>영수증신청</span> &nbsp; &nbsp; ");
	arr_html.push("  <span style='padding:3px 10px;-moz-border-radius:10px;-webkit-border-radius:10px;border-radius:10px;background-color:#888;color:#fff;font-size:14px;cursor:pointer' onclick='tools_etc_cls_();'>닫기</span>");
	arr_html.push("</div>");
	arr_html.push("</form>");
	$(".POP_content").html(arr_html.join(""));
	$(":radio[name='type'][value='1']").attr('checked', true);
  type_option(1);
}

//취소신청
function cancel_call(t,o,a,n)
{
	var arr_html=[];
	$("#POP_content").empty();
	$("#POP_dialog").show();
	arr_html.push("<form id='fm_data' name='fm_data' method='post'>");
	arr_html.push(" <input type='text' name='amount' id='amount' value="+a+">");
	arr_html.push(" <input type='text' name='orderId' id='orderId' value="+o+">");
	arr_html.push(" <input type='text' name='refundableAmount' id='refundableAmount' value="+a+">");
	arr_html.push(" <div class='login_pop' style='width:420px;margin: 20px;'>");
	arr_html.push(" <table class='tbl_grid'>");
	arr_html.push("   <tr><th>상품명</th><td>"+n+"</td></tr>");
	arr_html.push("   <tr><th>주문금액</th><td>"+number_format(a)+"원</td></tr>");
	arr_html.push("   <tr><th>취소금액</th><td><input type='text' name='cancelAmount' id='cancelAmount' value='"+a+"' style='width:70px' onKeyup='onlyNumber(this);'> 원</td></tr>");
	arr_html.push("   <tr>");
	arr_html.push("     <th>취소사유</th>");
	arr_html.push("     <td><input type='text' name='cancelReason' id='cancelReason' placeholder='취소사유를 입력해주세요' style='padding-left:2px;width:345px;border:1px solid #777;margin-bottom:3px' onKeyup='strFilter(this);'></td>");
	arr_html.push("   </tr>");
	arr_html.push("   <tr>");
	arr_html.push("     <th>환불계좌</th>");
	arr_html.push("     <td>");
	arr_html.push("       <select name='bank' id='bank' style='width:70px;border:1px solid #777'>");
	//은행 > js/json
	for(key in Json_bank)
	{
	  var sel=(key==0)? "selected" : "";
	  arr_html.push("         <option value='"+Json_bank[key]+"' "+sel+">"+Json_bank[key]+"</option>");
	}
	arr_html.push("       </select>");
	arr_html.push("       <input type='text' name='accountNumber' id='accountNumber' placeholder='계좌번호를 입력해주세요' style='padding-left:2px;border:1px solid #777;margin:0 6px'  onKeyup='onlyNumber(this);'>");
	arr_html.push("       <input type='text' name='holderName' id='holderName' placeholder='예금주' style='padding-left:2px;border:1px solid #777;width:70px'>");
	arr_html.push("     </td>");
	arr_html.push("   </tr>");
	arr_html.push(" </table>");
	arr_html.push(" <div style='width:100%;text-align:center;line-height:70px;vertical-align:middle'>");
	arr_html.push("  <span style='padding:3px 10px;-moz-border-radius:10px;-webkit-border-radius:10px;border-radius:10px;background-color:#007aff;color:#fff;font-size:14px;cursor:pointer' onclick='pay_cancel();'>결제취소신청</span> &nbsp; &nbsp; ");
	arr_html.push("  <span style='padding:3px 10px;-moz-border-radius:10px;-webkit-border-radius:10px;border-radius:10px;background-color:#888;color:#fff;font-size:14px;cursor:pointer' onclick='tools_etc_cls_();'>닫기</span>");
	arr_html.push("</div>");
	arr_html.push("</form>");
	$(".POP_content").html(arr_html.join(""));
}

//숫자만입력
function onlyNumber(obj){obj.value = obj.value.replace(/[^0-9]/g, '');}

//입력문자제한
function strFilter(obj)
{
 //var re=/[#&<>~]/gi;
 var re=/[\#@$^!?%&*=+'\"\/<>(){}\[\]\:\;\\]/gi;
 obj.value=obj.value.replace(re,"");
}

//팝업닫기
function tools_etc_cls_()
{
	$("#POP_content").empty();
	$("#POP_dialog").hide();
}

//type click
function type_option(n)
{
  var arr_html=[];
  if(n==1)
  {
    arr_html.push("<option value='1'>휴대폰번호</option>");
    //arr_html.push("<option value='2'>주민번호</option>");
    arr_html.push("<option value='3'>현금영수증카드</option>");
    $("input#registrationNumber").attr("placeholder","휴대폰번호.");
  }
  else if(n==2)
  {
    arr_html.push("<option value='4'>사업자등록번호</option>");
    $("input#registrationNumber").attr("placeholder","사업자등록번호");
  }
  $("#registration").html(arr_html.join(""));
}

//onchange
function registration_change(n)
{
  $("#registrationNumber").val("");
  $("#registrationNumber").focus();
  if(n==1){var str="휴대폰번호";}
  else if(n==2){var str="주민번호를";}
  else if(n==3) {var str="현금영수증카드";}
  else if(n==4) {var str="사업자등록번호";}
  $("input#registrationNumber").attr("placeholder",str);

}
//현금영수증 발급
function receipt_issue()
{
  var str = $('#fm_data').serialize();
  var orderId=$("#orderId").val();
  var orderName=$("#orderName").val();
  var amount=$("#amount").val();
  var type=$("input[name='type']:checked").val();
  var registrationNumber=$("#registrationNumber").val();
  var registration=$("#registration").val();
  if(orderId=="" || orderName=="" || amount=="" || type=="" || registrationNumber==""){return false;}
  if(registrationNumber=='')
  {
    $("#registrationNumber").focus();
    if(registration==1){var str="휴대폰번호";}
    else if(registration==2){var str="주민번호";}
    else if(registration==3) {var str="현금영수증카드";}
    else if(registration==4) {var str="사업자등록번호를";}
    $("input#registrationNumber").attr("placeholder",str);
  }
  
  //전화번호
  if(registration==1)
  {
    var a=telValidator(registrationNumber);
    if(a==false)
    {
      alert("휴대폰 번호를 확인해주세요.");
      $("#registrationNumber").val("");
      $("#registrationNumber").focus();
      $("input#registrationNumber").attr("placeholder","휴대폰번호 오류!");
      return false;
    }
  }
  
  //사업자등록번호
  if(registration==4)
  {
    var b=checkCorporateRegiNumber(registrationNumber);
    if(b==false)
    {
      alert("사업자등록번호를 확인해주세요.");
      $("#registrationNumber").val("");
      $("#registrationNumber").focus();
      $("input#registrationNumber").attr("placeholder","사업자등록번호 오류!");
      return false;
    }
  }
  $.ajax(
  {
	  type: "POST",
	  data: $('#fm_data').serialize(),
		url: "/res/payResult.php?mode=10",
		dataType: "JSON",
		success: function(data)
		{
       if(data.success==1)
       {
          alert(data.success_ment);
          tools_etc_cls_();
          payresult_();
          //payresult_toss();
       }
		}    
  });
}

//결제신청취소
function pay_cancel()
{
  $.ajax(
  {
	  type: "POST",
	  data: $('#fm_data').serialize(),
		url: "/res/payResult.php?mode=20",
		dataType: "JSON",
		success: function(data)
		{
       if(data.success==1)
       {
          alert(data.success_ment);
          tools_etc_cls_();
          payresult_();
          //payresult_toss();
       }
       else{alert(data.success_ment);}
		}    
  });
}

//휴대폰번호
function chkform(a)
{
  var regPhone = /^01([0|1|6|7|8|9])-?([0-9]{3,4})-?([0-9]{4})$/;
  if (regPhone.test(a) === false) 
  {
     $("#registrationNumber").focus();
     $("input#registrationNumber").attr("placeholder","휴대폰번호 오류!");
     return false;
  }  
}

//전화번호
function telValidator(args)
{
  if (/^01([0|1|6|7|8|9])-?([0-9]{3,4})-?([0-9]{4})$/.test(args)) {return true;}
  else{return false;}
}

//사업자등록번호
function checkCorporateRegiNumber(number)
{
	var numberMap = number.replace(/-/gi, '').split('').map(function (d){return parseInt(d, 10);});
	if(numberMap.length == 10)
	{
		var keyArr = [1, 3, 7, 1, 3, 7, 1, 3, 5];
		var chk = 0;
		keyArr.forEach(function(d, i){chk += d * numberMap[i];});
		chk += parseInt((keyArr[8] * numberMap[8])/ 10, 10);
		return Math.floor(numberMap[9]) === ( (10 - (chk % 10) ) % 10);
	}
	return false;
}
</script>
