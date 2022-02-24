<?
$snb=false;
include_once($_SERVER["DOCUMENT_ROOT"]."/inc/header.php");

if($client_id){echo "<script type='text/javascript'>location.href='/';</script>";	exit;}
?>

<form name="fm_login" id="fm_login" method="post">
<div class="section_login">
  <div class="center" style="padding:50px"><img src="/img/common/top_left.png" alt="탱크로고" width='90'></div>
  	<div class="txt input_box">
  		<div>
  			<span class='span_block' style="width:100px">회원 ID</span>
  			<input type="text" name="client_id" id="client_id" placeholder="아이디 입력" style='font-weight:100;width:200px;border-color:#FFF'>
  		</div>
  		<div>	
  			<span class='span_block' style="width:100px">비밀번호</span>
  			<input type="password" name="passwd" id="passwd" class="tx100" placeholder="비밀번호 입력" style="font-weight:100;width:200px;border-color:#FFF;">
  		</div>
  		<div class='center'>
  		  <span id="btn_login" class="btn_box_ss btn_white radius_10" onclick="fmLoginCheck()">로그인</span>
  		</div>
  	</div>
  	<div style='padding:5px 20px'>
  		<label class='inputWrap'>
  		   <input type="checkbox" name="chk_save_id" id="chk_save_id" value="save_id" class='input_chk chk'>
  		   <span class="f14 chk_ment">ID저장</span>
  		</label> &nbsp;&nbsp;
  		<label class='inputWrap'>
  		  <input type="checkbox" name="chk_save_pwd" id="chk_save_pwd" value="save_pwd" class='input_chk chk'>
  		  <span class="f14 chk_ment">PW저장 </span>
  		</label>
  		<span class="f14" style="padding-left:160px">
  		  <a href="javascript:findPasswd()">비번찾기</a>&nbsp;&nbsp;
  		  <span class='span_block gray' style='font-weight:100'>|</span>&nbsp;&nbsp;
  		  <a href="/member/agree.php">회원가입</a>
  		</span>
  	</div>
  <div class="tble_view_mask" style="display:none" onclick="ly_findpw_close()"></div>
</div>
</form>

<? include($_SERVER["DOCUMENT_ROOT"]."/inc/footer.php"); ?>

<script type="text/javascript">
  var server_date=<?=date('Ymd')?>;
  $("#passwd").bind("keypress",function(e){if(e.which==13){fmLoginCheck();}});

  <?
  if(!$client_id)
  {
  	if($_COOKIE[GAC_SAVED_ID])
  	{
  		echo "
  		$('#client_id').val('{$_COOKIE[GAC_SAVED_ID]}');
  		$('#chk_save_id').attr('checked',true);";
  	}
  	if($_COOKIE[GAC_SAVED_PWD])
  	{
  		echo "
  		$('#passwd').val('{$_COOKIE[GAC_SAVED_PWD]}');
  		$('#chk_save_pwd').attr('checked',true);";
  	}
  }
  ?>
  //로그인폼 체크
  function fmLoginCheck()
  {
  	var cert_url="";
  	if($("#client_id").val()=="" || $("#client_id").val()=="아이디")
  	{
  		alert("아이디를 입력 하세요.");
  		$("#client_id").focus();
  		return;
  	}
  	if($("#passwd").val()=="")
  	{
  		alert("비밀번호를 입력 하세요.");
  		$("#passwd").focus();
  		return;
  	}
  	
  	$("#fm_login").attr("action","/member/cert.php");
  	$("#fm_login").submit();
  }
</script>