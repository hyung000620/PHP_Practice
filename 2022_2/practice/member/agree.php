<?
$page_code="9010";
include_once($_SERVER["DOCUMENT_ROOT"]."/inc/header.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/inc/snb.php");
?>

<style>

	.agree_box .ta_title{}
	.agree_box .ta_box{height:200px;overflow-y:scroll;width:100%}
</style>
<div>
	<table class=' tbl_noline'>
		<tr>
			<td colspan='3' class='f14 bold' style='padding:20px'>회원가입</td>
		</tr>
		<tr>
			<td colspan='3' class='center'><img src="/img/member/member_title01.png" alt="이용약관 동의"></td>
		</tr>
		<tr>
			<td class='center f11' style='width:180px'><span class='f14 bold'>이용약관 동의</span><br>이용약관 / 개인정보 취급안내 동의</td>
			<td class='center f11' style='width:600px'><span class='f14 bold'>회원가입 신청</span><br>회원정보 입력 및 가입신청</td>
			<td class='center f11' style='width:180px'><span class='f14 bold'>회원가입 완료</span><br>회원가입 완료 </td>
		</tr>
	</table>
</div>

<form name="fmAgree" id="fmAgree" action="/member/register.php" method="post">
  <div class="agree_box" style="padding:20px 40px;">
  	<div class="f18 bold" style="padding:20px 0 15px"><?=TK_COMPANY?> 이용약관</div>
  	<div class="lh20 readonly" style="height:200px;overflow-y:scroll;width:100%"><? include $_SERVER["DOCUMENT_ROOT"]."/member/user_agreement_text.php";?></div>
  	<br>
  	<div class="right"><label class='inputWrap'><input type="checkbox" name="chk_agree1" id="chk_agree1" value="y" class='input_chk chk'><span class="f14 chk_ment"> 이용약관에 동의합니다</span></label></div>
  </div>

  <div class="agree_box" style="padding:20px 40px;">
  	<div class="f18 bold"  style="padding:20px 0 15px"><?=TK_COMPANY?> 개인정보취급방침</div>
  	<div class="lh20 readonly" style="height:200px;overflow-y:scroll;width:100%"><? include $_SERVER["DOCUMENT_ROOT"]."/member/privacy_agreement_text.php";?></div>
  	<br>
  	<div class='right'><label class='inputWrap'><input type="checkbox" name="chk_agree2" id="chk_agree2" value="y" class='input_chk chk'><span class="f14 chk_ment"> 개인정보취급방침에 동의합니다 </span></label></div>
  </div>
</form>

<div class="register_fm_btn center"><span id="btn_register" class="btn_box_ss btn_white radius_10">다음</span></div>

<?
//회원가입 또는 약관동의 페이지 내부변수지정
$http_SO = "REGC";
include_once($_SERVER["DOCUMENT_ROOT"]."/inc/footer.php");
?>

<script type="text/javascript">
$(document).ready(function($)
{
	$("#btn_register").click(function()
	{
		if($("#chk_agree1").is(":checked")==false || $("#chk_agree2").is(":checked")==false){alert("이용약관 및 개인정보제공/수집에 모두 동의 해 주세요."); return;}
		else{$("#fmAgree").submit();}
	});
});
</script>

