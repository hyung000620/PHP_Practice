<?
$debug=true;
$page_code=1070;
include($_SERVER["DOCUMENT_ROOT"]."/SuperAdmin/common/header.php");
if($client_level<5) 
{
  echo "<script type='text/javascript'>alert('권한이 없습니다.');location.href=''/';</script>";
  exit;
}

## 페이지,목록수,페이지수
$pageNo=($pageNo>0)? $pageNo : 1;
$dataSize=($dataSize>0)? $dataSize : 30;
$pageSize=($pageSize>0)? $pageSize : 10;
$today=date("Y-m-d");

$SQL="SELECT * FROM {$my_db}.tl_edu WHERE edate >= '{$today}'";
$stmt=$pdo->prepare($SQL);
$stmt->execute();
$edu_arr=array();
while($rs=$stmt->fetch()) 
{
    $edu_arr[$rs['idx']]=[
         'edu_title'=>$rs['edu_title'],
         'edu_code'=>$rs['edu_code'],
         'edu_people'=>$rs['edu_people'],
         'pay_people'=>$rs['pay_people']
    ];
}
?>
<!-- 검색 -->
<form name="fmSearch" id="fmSearch" method="post">
  <input type="hidden" name="pageNo" id="pageNo" value="<?=$pageNo?>">
  <input type="hidden" name="dataSize" id="dataSize" value="<?=$dataSize?>">
  <input type="hidden" name="pageSize" id="pageSize" value="<?=$pageSize?>">
<table class="tbl_grid">
	<tr>
		<th>회 원 명</th>
		<td><input type="text" name="user_name" id="user_name" value="<?=$user_name?>" class="tx80 han"></td>
		<th>아 이 디</th>
		<td><input type="text" name="user_id" id="user_id" value="<?=$user_id?>" class="tx80 ieng"></td>
		<th>상태</th>
		<td>    
		  <select name="state" id="state">
		    <option value=0 selected> 전체 </option>
		    <option value=1 > 결제대기 </option>
		    <option value=2> 기한만료 </option>
		    <option value=3> 결제완료 </option>
			<option value=4> 결제취소 </option>
		  </select>
		</td>
        <th>강좌 선택</th>
        <td>
            <select name="estate" id="estate">
                <option value=0 selected>전체(모집정원)</option>
                <?
                    foreach($edu_arr as $k => $v) {
                        echo "<option value='{$v['edu_code']}'>{$v['edu_title']}({$v['edu_people']})</option>";
                    }
                ?>
            </select>
        </td>
        <th>온/오프라인</th>
        <td>
            <select name="ostate" id="ostate">
                <option value=2>전체</option>
                <option value=0>오프라인</option>
                <option value=1>온라인</option>
            </select>
        </td>
		<td class="center"><input type="button" value="검색하기" style="cursor:pointer" onclick="list_();"></td>
		<td class="last center"><input type="button" value="초기화" onclick="reset_();"  style="cursor:pointer"></td>
	</tr>
</table>
</form>
<!-- 검색 -->

<!-- 목록 -->
<br>
<div class='move_position'></div>
<div id="tosspay_block">
  <div id="naviHead"></div>	
  <table class="tbl_grid border">
     <thead id="vsThead"></thead>
     <tbody id="vsTbody"></tbody>
  </table>
  <div id="paging" class="pagn"></div>
</div> 
  
<? include($_SERVER["DOCUMENT_ROOT"]."/SuperAdmin/common/footer.php"); ?>

<script type="text/javascript">
$(document).ready(function()
{
  //가상계좌 결제대기
	list_();
  //list>mouseover
  setTimeout(function(){listMouseOver($(".list"));},80); 
  
	//history
	(function(window)
	{
		var History=window.History;
		if(!History.enabled){return false;}		
		History.pushState({section:1,page_index:1}, "탱크옥션", "?page=1");
		History.Adapter.bind(window,'statechange',function()
		{
			//페이지 선택시 실행
			var State = History.getState();
			if(manualChange) return false;
			if(State.data['page_index']>0)
			{
				$("#pageNo").val(State.data['page_index']);
				list_();
			}
		});
	})(window);	
});

function list_()
{
  //history
	manualChange=true;
  var arr_head=[];
  var arr_body=[];
  var navi=[];
	$("#tosspay_block").show();
  $.ajax(
	{
	  type: "POST",
	  data: $('#fmSearch').serialize(),
		url: "/res/payVWait.php?mode=2",
		dataType: "JSON",
		success: function(data)
		{
		  if(typeof data.pay_people != "undefined"){
			navi.push("<div style='float:right' class='bold f15'>인원:"+data.pay_people+"/"+data.edu_people+"</div>");
		  }
		  arr_head.push("<tr>");
		  arr_head.push(" <th>No</th>");
		  arr_head.push(" <th>회원명</th>");
		  arr_head.push(" <th>아이디</th>");
          arr_head.push(" <th>협력업체</th>");
		  arr_head.push(" <th>신청강좌</th>");
		  arr_head.push(" <th>결제금액(원)</th>");
		  arr_head.push(" <th>결제방법</th>");
		  arr_head.push(" <th>등록일</th>");
		  arr_head.push(" <th>결제상태</th>")
		  //arr_head.push(" <th>전달</th>");
		  arr_head.push("</tr>");
		  $("#vsThead").html(arr_head.join(""));
		  if(typeof data.item!="undefined")
			{
			  $.each(data.item,function()
				{
				  let status =this.status;
				  if(status=="DONE"){status="<span class='bold'>결제완료</span>";}
				  else if(status=="WAITING_FOR_DEPOSIT"){status="<span class='bold blue'>결제대기</span>";}
				  else {status="<span class='bold red'>결제취소</span>";}
				  let payopt = (this.pay_opt==1)?"카드":"가상계좌";	
				  arr_body.push("<tr onclick=\"location.href='/SuperAdmin/member/member_detail.php?id="+this.id+"'\" style='cursor:pointer' class='list'>");
				  arr_body.push(" <td class='center'>"+this.no+"</td>");
				  arr_body.push(" <td class='center'>"+this.name+"</td>");
				  arr_body.push(" <td>"+this.id+"</td>");
                  arr_body.push(" <td>"+this.ptnr+"</td>")
				  arr_body.push(" <td>"+this.goods+"</td>");
				  arr_body.push(" <td class='right bold red'>"+this.pay_price+"</td>");
				  arr_body.push(" <td class='bold'>"+payopt+"</td>");
				  arr_body.push(" <td class='center'>"+this.wdate+"</td>");
				  arr_body.push(" <td class='center'>"+status+"</td>");
				  //arr_body.push(" <td class='center'><span style='padding:5px 10px;color:#fff;background-color:blue;cursor:pointer;-moz-border-radius:12px;-webkit-border-radius:12px;border-radius:12px;'>SMS</span></td>");
				  arr_body.push("</tr>");
				});				 
			}
      $("#vsTbody").html(arr_body.join(""));
	  if($('#etate').val()!=0){$('#naviHead').html(navi.join(""));}
      // history push
			$("#paging").html(paging2(data.rowCnt, $("#pageNo").val(), loadPage, $("#dataSize").val(), $("#pageSize").val()));
			History.pushState({section:1,page_index:$("#pageNo").val()}, "탱크옥션", "?page="+$("#pageNo").val());
			manualChange=false
		}
	});
	//list>mouseover
	setTimeout(function(){listMouseOver($(".list"));},80); 
}

function reset_()
{
  $("#user_name").val("");
  $("#user_id").val("");
  setTimeout(function(){list_();},80); 
}

function loadPage(){list_();}
function paging2(totalCnt, pageNo, func, dataSize, pageSize)
{
	var pageCnt, pageGrp, bgn, end, prev, next;
	var arr=[];
	var pagnHtml;
	$("#pageNo").val(pageNo);
	if(totalCnt==0){pagnHtml="<div class='no_result' style='color:#777;font-size:14px;font-weight:bold'>검색 결과가 없습니다.</div>";	return pagnHtml;	}
	pageCnt=Math.ceil(totalCnt / dataSize);
	pageGrp=Math.ceil(pageNo / pageSize);
	end=pageGrp * pageSize;
	if(end > pageCnt){end=pageCnt;}
	bgn=end-(pageSize-1);
	if(bgn < 1){bgn=1;}
	prev=bgn-1;
	next=end+1;
	
	if(prev > 0)
	{
		arr.push("<a href='javascript:gotoPage2("+func+","+1+")' class='pre_end' style='background-color:#FFF'>처음</a>");
		arr.push("<a href='javascript:gotoPage2("+func+","+prev+")' class='pre' style='background-color:#FFF'>이전</a>");
	}
	for(var i=bgn; i <= end; i++)
	{
		if(pageNo==i){
			arr.push("<strong >"+i+"</strong>");
			$("#pageNo_ment").html("<strong class='orange'>"+i+"</strong>page");
		}	else {arr.push("<a href='javascript:gotoPage2("+func+","+i+")' class='pageNo'>"+i+"</a>");}
	}
	if(end < pageCnt)
	{
		arr.push("<a href='javascript:gotoPage2("+func+","+next+")' class='next' style='background-color:#FFF'>다음</a>");
		arr.push("<a href='javascript:gotoPage2("+func+","+pageCnt+")' class='next_end' style='background-color:#FFF'>마지막</a>");
	}
	pagnHtml=arr.join("");
	return pagnHtml;
}

function gotoPage2(func, pageNo)
{	
	var offset=$(".move_position").offset();
  $('html,body').animate({scrollTop:offset.top-200},100);
	//$('html,body').animate({scrollTop:'0'},100);
	$("#pageNo").val(pageNo);
	manualChange=true;
	return func();
}	

function listMouseOver(row)
{
  row.mouseover(function(){$(this).css({"background":"#e3eefb","cursor":"pointer"});}).mouseout(function(){$(this).css({"background":"#fff"});});
}
//#### 페이징####
</script>