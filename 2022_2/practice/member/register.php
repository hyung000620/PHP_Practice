<?
$page_code=9010;
include_once($_SERVER["DOCUMENT_ROOT"]."/inc/header.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/inc/snb.php");

if($client_id){echo "<script type='text/javascript'>location.href='/member/modify_profile.php';</script>"; exit;}
$mailCompArr=array("naver.com","hanmail.net","hotmail.com","nate.com","yahoo.co.kr","empas.com","dreamwiz.com","paran.com","freechal.com","lycos.co.kr","korea.com","gmail.com","hanmir.com");
?>

<div>
	<table class=' tbl_noline'>
		<tr>
			<td colspan='3' class='center'><img src="/img/member/member_title02.png" alt="이용약관 동의"></td>
		</tr>
		<tr>
			<td class='center f11' style='width:180px'><span class='f14 bold'>이용약관 동의</span><br>이용약관 / 개인정보 취급안내 동의</td>
			<td class='center f11' style='width:600px'><span class='f14 bold'>회원가입 신청</span><br>회원정보 입력 및 가입신청</td>
			<td class='center f11' style='width:180px'><span class='f14 bold'>회원가입 완료</span><br>회원가입 완료 </td>
		</tr>
	</table>
</div>

<form id="fmRegister" name="fmRegister" action="" method="post">
<input type="hidden" id="id_chk" name="id_chk" placeholder="id_chk">
<input type="hidden" id="id_exist" name="id_exist" placeholder="id_exist">
<input type="hidden" id="id_tmp" name="id_tmp" placeholder="id_tmp">
<input type="hidden" id="ps_chk" name="ps_chk" placeholder="ps_chk">
<input type="hidden" id="psv_chk" name="psv_chk" placeholder="psv_chk">	
<input type="hidden" name="mode" value="new_member">

  <div style="padding:100px 0 50px;width:650px;margin:0 auto">
  	<table class="tbl_noline input_box f14">
  		<tr height="35">
  			<th width="100px" class='bold left'>회원명</th>
  			<td><input type="text" name="user_name" id="user_name" value="<?=$user_name?>" style="ime-mode:active;width:240px;background-color:#fff" placeholder="회원명(실명) 한글 또는 영문"></td>
  		</tr>
  		<tr>
  			<th class='bold left'>아이디</th>
  			<td>
  				<input type="text" name="user_id" id="user_id" value="<?=$user_id?>" style="ime-mode:inactive;width:240px" placeholder="영문 또는 숫자 (4~20자)">
  				<span style="position:relative;top:2px;left:-25px;"><img id="ment_img_1" src="/img/icon/icon_lock_red.png" style="position:relative;top:2px"></span>
  				<span style="position:relative;left:-10px"><span class='btn_box_sss btn_white radius_5' id="btnIdCheck">id 중복검사</span></span>
  				<span id="ment_1"></span>
  			</td>
  		</tr>
  		<tr height="35">
  			<th class='bold left'>비밀번호</th>
  			<td>
  				<input type="password" name="passwd" id="pwd" value="<?=$passwd?>"  style='width:240px' placeholder="영문,숫자 특수문자 일부허용 (6~30자)">
  				<span style="position:relative;top:2px;left:-25px;"><img id="ment_img_1" src="/img/icon/icon_lock_red.png" style="position:relative;top:2px"></span>
  				<span class="tooltip blue-tooltip" style="position:relative;top:0px;left:-10px;"><p class="btn_red radius_30 icon_q" style="line-height:17px;height:17px">?</p><span style="position:absolute;left:90px;width:370px;line-heihgt:20px">✷ 3자리 이상 연속적인 숫자(123) 반복문자(ccc)는 사용금지<br>✷ 특수문자는 <font class="red">( ) @ _ ^ - ! ~ $ </font> 만 사용 가능합니다.</span></span>
  				<span id="ment_2"></span>			
  			</td>
  		</tr>
  		<tr height="35">
  			<th class='bold left'>비밀번호 확인</th>
  			<td>
  					<input type="password" name="passwd_verify" id="pwd_verify" style="width:240px"  placeholder="비밀번호 확인">
  					<span style="position:relative;top:2px;left:-25px;"><img id="ment_img_3" src="/img/icon/icon_lock_red.png" style="position:relative;top:2px"></span>
  					<span id="ment_3"></span>
  			</td>
  		</tr>			
  		<tr height="35">
  			<th class='bold left'>이메일<span style="font-weight:normal;color:#ccc">(선택)</span></th>
  			<td>
  				<input type="text" name="email_id" id="email_id" value="<?=$email_id?>" style="ime-mode:disabled;width:110px">
  				@
  				<span id="spanMailComp" style="display:none"><input type="text" name="txtMailComp" id="txtMailComp"  style='width:100px'></span>
  				<select name="selMailComp" id="selMailComp" onchange="mail_ctrl(this)">
  					<option value="0">직접입력</option>
  				<?
  					foreach($mailCompArr as $val)
  					{
  						echo "<option value='{$val}'";if($val=="naver.com") echo " selected"; echo ">{$val}</option>";	
  					}
  				?>
  				</select>
  				<span style="padding-left:30px"><label class='inputWrap'><input type="checkbox" name="r_mail" value="1" class='chk'><span class="f14 chk_ment">메일 수신동의</span></label></span>
  			</td>
  		</tr>
  		<tr height="35">
  			<th class='bold left'>휴대폰</th>
  			<td>
  				<input type="text" name="mobile1" id="mobile1" value="<?=$mobile1?>" maxlength="3" class="tx30 num">-
  				<input type="text" name="mobile2" id="mobile2" value="<?=$mobile2?>" maxlength="4" class="tx30 num">-
  				<input type="text" name="mobile3" id="mobile3" value="<?=$mobile3?>" maxlength="4" class="tx30 num">
  				<span style="padding-left:126px"><label class='inputWrap'><input type="checkbox" name="sms" value="1" class='input_chk chk'><span class="f14 chk_ment">문자 수신동의</span></label></span>
  			</td>
  		</tr>
  		<tr style="height:60px">
  			<th class='bold left'>주 소</th>
  			<td>
  				<br><br>
  				<input type="text" name="address1" id="address1"  value="<?=$address1?>" placeholder="주소찾기를 클릭"  style='width:350px' readonly>
  				<span id="btnSrchAddr"  class="btn_box_sss btn_white radius_5" style="padding:5px 0" onclick="execDaumPostcode()">주소찾기</span> <br>
  				<input type="text" name="address2" id="address2" value="<?=$address2?>"  placeholder="주소 뒷부분을 입력"  style='width:350px'>
  				<input type="hidden" name="zipcode" id="zipcode" value="<?=$zipcode?>">
  			</td>
  		</tr>
  	</table>
  </div>
	<div class="register_fm_btn" style="text-align:center"><span class="btn_box_ss btn_tank radius_10" id="btnReg" onclick="fmCheck()">회원가입</span></div>
</form>

<? include_once($_SERVER["DOCUMENT_ROOT"]."/inc/footer.php"); ?>  

<script src="https://t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>
<script type="text/javascript">
$(document).ready(function($)
{
  //ID 중복검사
	$("#btnIdCheck").click(function(){id_exist();});
	//Tel(숫자)
	$('#mobile1,#mobile2,#mobile3').keyup(function(event){if(!(event.keyCode >=37 && event.keyCode<=40)){var inputVal = $(this).val(); $(this).val(inputVal.replace(/[^0-9]/gi,''));}});
	//회원명(한글,영문)
	$('#user_name').keypress(function(event){if(!(event.keyCode >=37 && event.keyCode<=40)){var inputVal = $(this).val(); $(this).val(inputVal.replace(/[^ㄱ-힣A-Za-z0-9]/gi,''));}});
	//id(영문,숫자,일부특수문자)
	$('#user_id').keyup(function(event){if(!(event.keyCode >=37 && event.keyCode<=40)){var inputVal = $(this).val(); $(this).val(inputVal.replace(/[^A-Za-z0-9]/gi,''));}});
	//ID 체크
	$('#user_id').bind('keyup mouseenter mouseleave', function(e){if($('#user_id').val()!=""){id_check($("#user_id").val(),1);}});
	//passwd 체크
	$('#pwd').bind('keyup mouseenter mouseleave', function(e){if($('#pwd').val()!=""){ps_check($("#pwd").val(),2);}});
	//passwd 확인
	$('#pwd_verify').bind('keyup mouseenter mouseleave', function(e){if($('#pwd_verify').val()!="" && $("#ps_chk").val()==1){psv_check($("#pwd_verify").val(),3);}});		
});

function fmCheck()
{	
	if($("#user_name").val().trim()==""){alert("회원명을 입력 하세요."); return;}
	if($("#user_id").val().trim()=="")	{alert("아이디를 입력 하세요."); return;}
	var idRegex=/^[A-za-z0-9]{4,20}$/gi;
	if(!idRegex.test($("#user_id").val())){alert("아이디는 6~20자 영문 또는 숫자만 가능합니다.");	return;}
	if($("#id_chk").val()!=1){alert("아이디를 확인하세요."); $('#user_id').focus();	return;}
	if($("#id_exist").val()!=1){alert("아이디 중복확인을 하세요.");	$('#user_id').focus(); return;}		
	if($("#pwd").val()==""){alert("비밀번호를 입력 하세요.");	return;}
	if($("#ps_chk").val()!=1){ alert("비밀번호 확인하세요.");	$('#pwd').focus(); return;}
	if($("#pwd_verify").val()==""){alert("비밀번호 확인을 하세요."); return;}
	if($("#psv_chk").val()!=1)
	{
		//입력 후 비밀번호 수정시			
		var pwd_=$("#pwd").val();
		var pwd_verify_=$("#pwd_verify").val();
		if(pwd_!=pwd_verify_)
		{
			$("#ment_3")[0].className="red";
			$("#ment_3").html("불일치");
			$("#ment_img_3").attr("src", "/img/icon/icon_lock_check_red.png");
			$("#psv_chk").val("");
		}			
		alert("비밀번호 확인하세요");
		$('#pwd_verify').focus();			
		return;
	}	
	if($.trim($("#mobile1").val())=="" || $.trim($("#mobile2").val())=="" || $.trim($("#mobile3").val())=="") {alert("휴대폰 번호를 입력 해 주세요."); return;}
	if($("#mobile1").val().length < 3 || $("#mobile2").val().length < 3 || $("#mobile3").val().length < 4)    {alert("올바른 휴대폰 번호를 입력 해 주세요."); return;}
	if($("#address1").val()==""){alert("주소를 입력 하세요."); return;}
	$("#fmRegister").attr("action","register_db.php");
	$("#fmRegister").submit();
}
//아이디 체크
function id_check(m,s)
{
	var ptn1 = /^[a-zA-Z0-9_]{4,20}$/;
	var ptn2 = /phpsessid|xor|asc|desc|version|like| and| or| union| where| limit|group by|select |substr|apos;|onmouse|onclick|alert|database|information_schema|sleep|benchmark|drop|function|onabort|onactivate|onblur|onbounce|oncellchange|onchange|onclick|oncontextmenu|oncontrolselect|oncopy|oncut|ondblclick|ondeactivate|ondrop|onfilterchange|onfinish|onhelp|onlayoutcomplete|onload|onlosecapture|onpaste|onpropertychange|onreadystatechange|onreset|onscroll|onstart|onstop|onsubmit|onunload|onafter|onbefore|ondata|ondrag|onerror|onfocus|onkey|onmouse|onmove|onresize|onrow|onselect|script|confirm|apos;|alert\[|sleep\(|benchmark|hex\(|chr\(/i;
	var ptn3 = /xor|cmd|having|wcrtest|tinput|uname|telnet|netstat|netcat|sysobjects|syscolumns|syslogins|sysxlogins|sp_oacreate|sp_oamethos|sp_oasetproperty|sp_addextendedproc|sp_addsrvrolemember|sp_login|sp_password|sp_droplogin|sp_configure/i;
	if(ptn1.test(m)==1 && ptn2.test(m)==0 && ptn3.test(m)==0)
	{
		if($("#id_tmp").val()!=m)
		{
			$("#ment_img_"+s).attr("src", "/img/icon/icon_lock_check_red.png");
			$("#ment_"+s)[0].className="red";
			$("#ment_"+s).html("아이디 중복확인 하세요");
			$("#id_chk").val(1);
			$("#id_exist").val("");
		}
	}
	else
	{
		$("#ment_img_"+s).attr("src", "/img/icon/icon_lock_check_red.png");
		$("#ment_"+s)[0].className="red";
		$("#ment_"+s).html("사용불가");
		$("#id_chk").val("");
		$("#id_exist").val("");
	}	
}
function id_exist()
{
	if($("#id_chk").val()!=1){alert("아이디를 확인하세요."); return false;}
	else
	{
		$.ajax(
		{
			type: "post",
			url: "/res/id_check.php",
			data: "tmp_id="+$("#user_id").val(),
			dataType: "xml",
			beforeSend: function(){$("#ment_1").html("검사중...");},
			success: function(xml)
			{		
	    	if($(xml).find("res").text()==1)
	    	{
					$("#ment_img_1").attr("src", "/img/icon/icon_lock_check_blue.png");
					$("#ment_1").html("사용가능");	
					$("#ment_1")[0].className="blue";
					$("#id_exist").val(1);
					$("#id_tmp").val($("#user_id").val());
	    	}
	    	else
	    	{
	    		var ment_=$(xml).find("ment").text();
					$("#ment_img_1").attr("src", "/img/icon/icon_lock_check_red.png");
					$("#ment_1").html(ment_);
					$("#id_exist").val("");
					$("#id_tmp").val("");
	    	}
			}
		});	
	}
}
function ps_check(m,s)
{
	var ptn1 = /^.*.{6,30}/g;	
	var ptn2 = /(\w)\1\1/;
	var ptn3 = /(012)|(123)|(234)|(345)|(456)|(567)|(678)|(789)|(890)/;
	var ptn4 = /([가-힣ㄱ-ㅎㅏ-ㅣ\x20])\1\1/;	
	var ptn5 = /([\{\}\[\]\/?.,;:|\)*~`!^\-_+<>@\#$%&\\\=\(\'\"])\1\1/;	
	var ptn6 = /%|\#|&|\*|&|\+|\=|\\|{|}|\[|\]|\/|\?|\.|,|;|:|\||`|\+|<|>|\=|\'|\"|\-\-/;
	var ptn7 = /phpsessid|asc|desc|version|like| and| or| union| where| limit|group by|select |substr|apos;|onmouse|onclick|alert|database|information_schema|sleep|benchmark|drop|function|onabort|onactivate|onblur|onbounce|oncellchange|onchange|onclick|oncontextmenu|oncontrolselect|oncopy|oncut|ondblclick|ondeactivate|ondrop|onfilterchange|onfinish|onhelp|onlayoutcomplete|onload|onlosecapture|onpaste|onpropertychange|onreadystatechange|onreset|onscroll|onstart|onstop|onsubmit|onunload|onafter|onbefore|ondata|ondrag|onerror|onfocus|onkey|onmouse|onmove|onresize|onrow|onselect|script|confirm|apos;|alert\[|sleep\(|benchmark|hex\(|chr\(/i;
	var ptn8 = /xor|cmd|having|wcrtest|tinput|uname|telnet|netstat|netcat|sysobjects|syscolumns|syslogins|sysxlogins|sp_oacreate|sp_oamethos|sp_oasetproperty|sp_addextendedproc|sp_addsrvrolemember|sp_login|sp_password|sp_droplogin|sp_configure/i;
	var ptn9 = /(abc)|(bcf)|(cfg)|(fgh)|(ghi)|(hij)|(ijk)|(jkl)|(klm)|(lmn)|(mno)|(nop)|(opq)|(pqr)|(qrs)|(rst)|(stu)|(tuv)|(uvw)|(uvx)|(vxy)|(xyz)/i;
	
	var pwd_=$("#pwd").val();
	var pwd_verify_=$("#pwd_verify").val();
	if(pwd_!=pwd_verify_)
	{
		$("#ps_chk").val("");
		$("#pwd_verify").val("");
	}
	
	if(ptn1.test(m)==1 && ptn2.test(m)==0 && ptn3.test(m)==0 && ptn4.test(m)==0 && ptn5.test(m)==0 && ptn6.test(m)==0 && ptn7.test(m)==0 && ptn8.test(m)==0 && ptn9.test(m)==0)
	{
		$("#ment_img_"+s).attr("src", "/img/icon/icon_lock_check_blue.png");
		$("#ment_"+s)[0].className="blue";
		$("#ment_"+s).html("사용가능");
	  $("#ps_chk").val(1);
	}
	else
	{
		if(m.length>1)
		{
			$("#ment_img_"+s).attr("src", "/img/icon/icon_lock_check_red.png");
			$("#ment_"+s).html("사용불가");
		}
		else
		{
			$("#ment_img_"+s).attr("src", "/img/icon/icon_lock_red.png");
			$("#ment_"+s).html("");
		}
		$("#ment_"+s)[0].className="red";
		$("#ps_chk").val("");
	}
	
	if($("#user_id").val()==$("#pwd").val())
	{
		$("#ment_img_"+s).attr("src", "/img/icon/icon_lock_red.png");
		$("#ment_"+s)[0].className="red";
		$("#ment_"+s).html("사용불가(ID와 동일)");
		$("#ps_chk").val("");
	}
}
//비밀번호 재확인
function psv_check(m,s)
{
	if($("#ps_chk").val()==1)
	{
		if($("#pwd").val()==$("#pwd_verify").val())
		{
			$("#ment_"+s)[0].className="blue";
			$("#ment_"+s).html("일치");
			$("#ment_img_"+s).attr("src", "/img/icon/icon_lock_check_blue.png");
			$("#psv_chk").val(1);	
		}
		else
		{
			if(m.length>1)
			{
				$("#ment_img+"+s).attr("src", "/img/icon/icon_lock_check_red.png");
				$("#ment_"+s).html("불일치");
			}
			else
			{
				$("#ment_img_"+s).attr("src", "/img/icon/icon_lock_red.png");
				$("#ment_"+s).html("");
			}
			$("#ment_"+s)[0].className="red";
			$("#psv_chk").val("");	
		}
	}
	else
	{
		$("#ment_"+s).html("비밀번호를 입력하세요!");
		$("#pwd_verify").val("");
	}	
}

function mail_ctrl(sel)
{
	$("#spanMailComp").css("display",(sel.value==0) ? "" : "none");
}
</script>
 