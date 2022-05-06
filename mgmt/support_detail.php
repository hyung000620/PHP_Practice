<?
$page_code=1010;
include($_SERVER["DOCUMENT_ROOT"]."/SuperAdmin/common/header.php");

if(!$idx){exit;}

$idx=(int)$idx;
$SQL="SELECT * FROM {$my_db}.tm_member WHERE idx={$idx}";
$stmt=$pdo->prepare($SQL);
$stmt->execute();    
$rs=$stmt->fetch();
$mail_chk=($rs['r_mail']==1)? "checked" : "";
$sms_chk=($rs['sms']==1)? "checked" : "";

//주문번호
srand((double)microtime()*1000000);
$order_no=date("YmdHis").rand(1000,9999);
?>

<div id="pwrap">
 <!-- STAFF  등록 -->
 <form name="fmMEM" id="fmMEM">
    <div style="font-weight:bold;font-size:14px">[정보수정]</div>
    <input type="hidden" name="mode" id="mode">
    <input type="hidden" name="idx" id="idx" value="<?=$idx?>">
    <table class="tbl_grid">
      <tr>
        <th>아이디</th><td style="width:320px"><?=$rs['id']?></td> 
        <th>이름</th><td style="width:170px"><input type="text" name="name" id="name" class="lh25" value="<?=$rs['name']?>" style="width:150px"></td> 
        <th>비밀번호</th>
        <td>
          <input type="password" name="pw" id="pw" class="lh25" style="width:230px" placeholder="영문,숫자 특수문자 일부허용 (6~30자)">
          <label style="cursor:pointer"><input type="checkbox" name="chgpw" id="chgpw" value=1 style="position:relative;top:5px;width:20px;height:20px"> 변경</label>
        </td>
        
      </tr>
      <tr>
        <th>휴대폰</th><td><input type="tel" name="mobile" id="mobile" class="lh25" style="width:150px" value="<?=$rs['mobile']?>"></td> 
        <th>email</th><td><input type="email" name="email" id="email" class="lh25" style="width:230px" value="<?=$rs['email']?>"></td> 
        <th>정보수신</th>
        <td>
          <label id="gomail"  style="cursor:pointer"><input type="checkbox" name="r_mail" id="r_mail" value=1 style="width:20px;height:20px" <?=$mail_chk?>> <span style="position:relative;top:-5px">메일수신</span> <label> &nbsp; &nbsp;
          <label id="gosms"  style="cursor:pointer"><input type="checkbox" name="r_sms" id="r_sms" value=1  style="width:20px;height:20px" <?=$sms_chk?>> <span style="position:relative;top:-5px">SMS수신</span> <label>
        </td>
        
      </tr>
      <tr class="lh30">
        <th>가입일일</th><td><?=$rs['reg_date']?></td>
        <th>최근접속</th><td><?=$rs['login']?></td>
        <th></th><td></td>
      </tr>    
    </table>
    <div style="margin:20px 0;width:100%">
      <div style="width:50%;text-align:right;display:inline-block;"><span class="btn_rd_blue" onclick="mem_save(<?=$idx?>);">수 정</span></div>
      <div style="display:inline-block;width:48%;text-align:right"><span class="btn_rd_blue" onclick="listgo();";>목 록</span></div>
    </div>
 </form>
 <!-- STAFF  등록 --> 
 
 <!-- history 등록 -->
 <form name="fmHIS" id="fmHIS">
  
 </form>
<div class="list_box" id="his_block">
  <table class="tbl_grid_orange">
  	<thead id="hiThead"></thead>
  	<tbody id="hiTbody"></tbody>
  </table>
	</div>
 <!-- history 등록 -->
 
 <!-- 결제등록 -->
 <form name="fmPAY" id="fmPAY" enctype="multipart/form-data">
  <input type="hidden" name="fidx" id="fidx" value="<?=$idx?>">
  <div style="font-weight:bold;font-size:14px">[결제 등록]</div>
  <table  class="tbl_grid_orange">
    <tr>
        <th style="width:100px">주문번호</th>
        <td>
          <input type="text" name="order_no" id="order_no" value="<?=$order_no?>" class="lh25">
        </td>
        <th style="width:100px">결제방법</th>
        <td>
          <label class="hand"><input type="radio" name="pay_option" value=1> 카드</label> &nbsp; 
          <label class="hand"><input type="radio" name="pay_option" value=2> 통장</label> &nbsp; 
          <label class="hand"><input type="radio" name="pay_option" value=3> 가상계좌</label>
        </td>
        <th style="width:100px">결제방식</th>
        <td>
          <label class="hand"><input type="radio" name="pay_type" value=1> 정기</label> &nbsp; 
          <label class="hand"><input type="radio" name="pay_type" value=2> 일시</label>
        </td>
        <td rowspan="2" style="width:120px;text-align:center" onclick="pay_save();";> <span class="btn_rd_blue">저장</span> </td>
    </tr>
    <tr>
       <th style="width:100px">결제금액</th>
        <td>
          <input type="text" name="money" id="money" class="lh25">
        </td>     
        <th>영수증(pdf)</th>
        <td colspan="3"><input type='file' name='receipt' id='receipt'></td>
    </tr>
  </table>
 </form>
 	<div class="list_box" id="pay_block">
		<table class="tbl_grid_orange">
			<thead id="pyThead"></thead>
			<tbody id="pyTbody"></tbody>
		</table>
	</div>
 <!-- 결제등록 -->
 
 <!-- 결제목록 -->
	<div class="list_box" id="paylist_block">
		<table class="tbl_grid_orange">
			<thead id="plThead"></thead>
			<tbody id="plTbody"></tbody>
		</table>
	</div>
 <!-- 결제목록 -->
 
</div>

<? include($_SERVER["DOCUMENT_ROOT"]."/SuperAdmin/common/footer.php"); ?>

<script type="text/javascript">
 
 //수정 > 저장
 function mem_save(i)
 {
  $("#mode").val(5);
  $.ajax(
	  {
	    type: "POST",
		  url: "/SuperAdmin/xml/staff.php",
		  data: $("#fmMEM").serialize(),
		  dataType: "JSON",
		  success: function(data)
		  {
		   if(data.success==1){location.href="support_detail.php?idx="+data.idx;}
		  }
	  });
 }
 function listgo(){location.href="support_list.php";}     
 
 //history > 등록

 //history > 삭제

 //결제정보 등록
 function pay_save()
 {
    //결제방법 > 체크
    if($('input:radio[name=pay_option]').is(':checked')===false){alert("결제방법을 선택해주세요"); return false;}
    //결제방식 > 체크
    if($('input:radio[name=pay_type]').is(':checked')===false){alert("결제방식을 선택해주세요"); return false;}
    //결제금액 > 체크
    if(Number($("#money").val())==0){alert("결제 금액을 확인해주세요"); $("#money").focus(); return false;}
    //영수증파일 > 체크
    if($("input[id='receipt']").val()==""){alert("영수증 파일을 확인해주세요"); return false;}
    
    var form = $('#fmPAY')[0]; 
    var formData = new FormData(form);
    formData.append('receipt', $('[name="receipt"]')[0].files[0]);
    
    //for (var key of formData.keys()) { console.log(key);}
    //for (var value of formData.values()) {console.log(value);}

    $.ajax(
    {
	    type: "POST",
		  url: "/SuperAdmin/xml/staff.php?mode=50",
		  data: formData,
		  processData: false, 
		  contentType: false,
		  cache: false,
		  dataType: "JSON",
		  success: function(data)
		  {
		   console.log(data);
		  }      
    });
 }
 
 //결제목록
 
</script>