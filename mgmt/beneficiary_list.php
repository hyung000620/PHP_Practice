<?
$page_code=101000;
include($_SERVER["DOCUMENT_ROOT"]."/SuperAdmin/common/header.php");

$idx=($idx>0)? $idx : "";
$mode=($mode>0)? $mode : 1;

## 페이지,목록수,페이지수
$pageNo=($pageNo>0)? $pageNo : 1;
$dataSize=($dataSize>0)? $dataSize : 30;
$pageSize=($pageSize>0)? $pageSize : 10;
?>

<div id="pwrap">
    <form name="fmStaff" id="fmStaff">
        <input type="hidden" name="mode" id="mode" value=<?=$mode?>>
        <input type="hidden" name="idx" id="idx" value=<?=$idx?>>
        <input type="hidden" name="pageNo" id="pageNo" value=<?=$pageNo?>>
        <input type="hidden" name="dataSize" id="dataSize" value=<?=$dataSize?>>
        <input type="hidden" name="pageSize" id="pageSize" value=<?=$pageSize?>>

        <div id="search">
            <table class="tbl_grid">
                <tr class="lh35">
                    <th style="width:70px">회원명</th>
                    <td><input type="text" name="sname" id="sname" class="lh25"></td>
                    <th style="width:90px">아이디</th>
                    <td><input type="text" name="sid" id="sid" class="lh25"></td>
                    <th style="width:80px">휴대폰</th>
                    <td><input type="text" name="smobile" id="smobile" class="lh25"></td>
                    <td rowspan="5" class="center" id="nodrag">
                        <span class="btn_rd_green" onclick="list_();" style="margin-right:10px">검색</span>
                        <span class="btn_rd_green" onclick="cancelro();" style="margin-right:10px">취소</span>
                    </td>
                </tr>
            </table>
</form>
<!-- 후원  등록 -->
 <form name="fmMEM" id="fmMEM" style="display:none" enctype="multipart/form-data">
    <input type="hidden" name="idchk" id="idchk" value=0>
    <input type="hidden" name="pwchk" id="pwchk" value=0>
    <div style="font-weight:bold;font-size:14px">[후원 등록]</div>
    <table class="tbl_grid">
      <tr>
        <th>이름</th><td style="width:170px"><input type="text" name="name" id="name" class="lh25" placeholder="한글,영문 또는 숫자" style="width:150px"></td> 
        <th>아이디</th>
        <td style="width:320px">
            <input type="text" name="id" id="id" class="lh25" style="width:150px" placeholder="영문 또는 숫자 (4~20자)"> 
            </td> 
        <th>비밀번호</th>
        <td style="width:350px">
          <input type="password" name="pw" id="pw" class="lh25" style="width:230px" placeholder="영문,숫자 특수문자 일부허용 (6~30자)">
          <span id="pwchk_msg" style='color:red'></span>
        </td> 
        <td rowspan="3" class="center">
            <span class="btn_rd_blue" onclick="findmem();">회원 찾기</span>
            <span class="btn_rd_blue" onclick="regdone();">후원 등록</span>
        </td>
      </tr>
      <tr>
        <th>휴대폰</th><td><input type="tel" name="mobile" id="mobile" class="lh25" style="width:150px"></td> 
        <th>email</th><td><input type="email" name="email" id="email" class="lh25" style="width:230px"></td> 
        <th>정보수신</th>
        <td>
          <label id="gomail"  style="cursor:pointer"><input type="checkbox" name="r_mail" id="r_mail" value=1 style="width:20px;height:20px"> <span style="position:relative;top:-5px">메일수신</span> <label> &nbsp; &nbsp;
          <label id="gosms"  style="cursor:pointer"><input type="checkbox" name="r_sms" id="r_sms" value=1  style="width:20px;height:20px"> <span style="position:relative;top:-5px">SMS수신</span> <label>
        </td>        
      </tr>
      <tr>
        <th>후원 금액</th><td><input type="text" class="lh25"></td>
        <th>영수증 </th><td><input type="file" id="outdate" name="outdate" class="lh25"></td>
        <th>메모</th><td><textarea style="width: 350px; height: 100px;"></textarea></td>
      </tr>
    </table>
 </form>
 <br><br>
 <div class="list_box" id="list_block">
		<table class="tbl_grid_orange">
			<thead id="ltThead"></thead>
			<tbody id="ltTbody"></tbody>
		</table>
	</div>
	<div id="paging" class="pagn"></div>
</div>
<?
include($_SERVER["DOCUMENT_ROOT"]."/SuperAdmin/common/footer.php");
?>
<script type="text/javascript">
$(function(){
    list_();
});
//리스트
function list_()
  {
    manualChange=true;
    var arr_head=[];
    var arr_body=[];
	  $("#list_block").show();
	  $.ajax(
	  {
	    type: "POST",
		  url: "/SuperAdmin/xml/_beneficiary_list.php",
		  data: $("#fmStaff").serialize(),
		  dataType: "JSON",
		  success: function(data)
		  {
        arr_head.push("<tr>");
        arr_head.push(" <th>no</th>");
        arr_head.push(" <th>아이디</th>");
        arr_head.push(" <th>이름</th>");
        arr_head.push(" <th>휴대폰</th>");
        arr_head.push(" <th>메일</th>");
        arr_head.push(" <th>후원금액</th>");
        arr_head.push(" <th>메모</th>");
        arr_head.push("</tr>");
        $("#ltThead").html(arr_head.join(""));
        
        if(typeof data.item!="undefined")
        {
          $.each(data.item,function()
          {
            arr_body.push("<tr class='list' onclick='detail_("+this.idx+")'>");
            arr_body.push(" <td class='center'>"+this.no+"</td>");
            arr_body.push(" <td class='center' style='font-weight:bold'  onclick='readro("+this.idx+")'>"+this.id+"</td>");
            arr_body.push(" <td class='center'>"+this.name+"</td>");
            arr_body.push(" <td class='center'>"+this.mobile+"</td>");
            arr_body.push(" <td class='center'>"+this.email+"</td>");
            arr_body.push(" <td class='center'>"+this.pay_price+"</td>");
            arr_body.push(" <td class='center'>"+this.memo+"</td>");
            arr_body.push("</tr>");
          }); 
        }
        else{arr_body.push("<tr style='line-height:200px'><td colspan='7' style='text-align:center;font-size:14px;color:#555'>검색 결과가 없습니다.</td></tr>");}
		    $("#ltTbody").html(arr_body.join(""));
        // history push
  			$("#paging").html(paging(data.totCnt, $("#pageNo").val(), loadPage, $("#dataSize").val(), $("#pageSize").val()));
  			History.pushState({section:1,page_index:$("#pageNo").val()}, "위드탱크", "?page="+$("#pageNo").val());
  			manualChange=false		    
		  },
		  error: function(xhr,status,error){}
	  });
  }
</script>