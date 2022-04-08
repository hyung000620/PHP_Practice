<?
$page_code="9010";
 include($_SERVER["DOCUMENT_ROOT"]."/inc/header.php");
 $mobileArr=array('010','011','016','017','018','019');
?>
<link rel="stylesheet" href="register.css"> <!-- 이준형 CSS -->
<div class='center f18 gray'>
    우리가 사는 세상이 두루 아름다워질 수 있다고 믿습니다.<br>
    위드탱크와 함께 걸어주세요<br>
</div>
<div style="position:relative;">
    <div><img src='/img/sample/with_sample_1.jpg' style='width:100%'></div>
</div>
<form id="signup_form" class="member_join_form" method="POST">
    <table class="tbl07">
        <tbody>
            <tr>
                <td>
                    <div class="required join_tit" title="필수입력"><label for="user_id">아이디</label></div>
                    <div class="isb_wrap_id">
                        <input class="check_id" type="text" id="user_id" name="user_id"  required>
                    </div>
                    <div id="id_check"></div>
                </td>
            </tr>

            <tr>
                <td>
                    <div class="required join_tit" title="필수입력"><label for="mj_pw01">비밀번호</label></div>
                    <input type="password" id="mj_pw01" class="isw07" name="password" required>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="required join_tit" title="필수입력"><label for="mj_pw02">비밀번호 확인</label></div>
                    <input type="password" id="mj_pw02" class="isw07" name="password_confirm" required>
                    <div id="pw_check"></div>
                </td>
            </tr>
            <tr class="member_join_one" data-filed="member_join">
                <td>
                    <div class="required join_tit" title="필수입력"><label for="mj_name">성명·단체명</label></div>
                    <input type="text" id="mj_name" class="isw07" name="user_name" required>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="required join_tit">휴대폰 번호</div>
                    <div class="isb_wrap_phone ty02">
                        <label for="slt_phone" class="select">
                            <select id="slt_phone" name="phone1">
                                <?foreach($mobileArr as $v){echo "<option value='{$v}'";if($v=="010") echo "selected"; echo">{$v}</option>";}?>
                            </select>
                        </label>
                        <span>-</span>
                        <label for="inp_phone_first"><input type="text" id="inp_phone_first" class="phone_input"
                                name="phone2" maxlength="4" ></label>
                        <span>-</span>
                        <label for="inp_phone_last"><input type="text" id="inp_phone_last" class="phone_input"
                                name="phone3" maxlength="4" ></label>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div scope="row" class="required join_tit" title="필수입력">이메일</div>
                    <div class="isb_wrap_email">
                        <input class="check" type="email" id="user_email" name="user_email" required>
                    </div>
                    <div id="email_check"></div>
                </td>
            </tr>
        </tbody>
    </table>
    <div class="agree_chk">
    <div scope="row" class="join_tit">메일 수신동의</div><br>
    <div class="btn_rdo_area">
        <span class="btn_rdo ty03">
            <input type="checkbox" name="r_mail" value="1" id="btn_rdo_b01" checked />
            <label for="btn_rdo_b01">동의</label>
        </span>
        <span class="btn_rdo ty03">
            <input type="checkbox" name="r_mail" id="btn_rdo_b02" value="0" />
            <label for="btn_rdo_b02">미동의</label>
        </span>
    </div><br>          
    <div scope="row" class="join_tit">문자 수신동의</div><br>
    <div class="btn_rdo_area">
        <span class="btn_rdo ty03">
            <input type="checkbox" name="sms" value="1" id="btn_rdo_b03" checked />
            <label for="btn_rdo_b03">동의</label>
        </span>
        <span class="btn_rdo ty02">
            <input type="checkbox" name="sms" id="btn_rdo_b04" value="0" />
            <label for="btn_rdo_b04">미동의</label>
        </span>
    </div><br>
    <div class="agree_check_area">
        <span class="chkrdo">
            <input type="checkbox" id="chk_agree" name="ag1" required>
            <label for="chk_agree">
                <a href="#" target="_blank">이용약관</a> 및 <a href="#" target="_blank">개인정보취급방침</a>에 동의합니다.
            </label>
        </span><br>
        <div class="btn_area"><span id="join_btn" class="btn_box_ss btn_tank radius_20" style="width:470px;font-size:25px">회원가입</span>
        </div>
    </div>
    <div>
</form>
<? include($_SERVER["DOCUMENT_ROOT"]."/inc/footer.php"); ?>
<script>
$(document).ready(function(e) {
    //실시간 아이디체크
    id_check();
    //실시간 비밀번호체크
    pw_check();
    // 이메일, 문자 수신동의 체크
    agreeChk();
});

//실시간 아이디 중복체크
function id_check() {
    $('.check_id').on('keyup', function() {
        let self = $(this);
        let user_id;
        if (self.attr('id') === user_id) {
            user_id = self.val();
        }
        $.post(
            "/res/id_check.php", 
            {user_id: user_id},
            function(data) {
                if (data) {
                    $('#id_check').html(data);
                    $('#id_check').css("color", "#F00");
                }
            }
        );
        
    })
}
//실시간 비밀번호 중복체크
function pw_check() {
    $('#mj_pw02').on('keyup', function() {
        let self = $(this).val();
        let pw01 = $('#mj_pw01').val();
        if (self != pw01) {
            $('#pw_check').html('비밀번호가 일치하지 않습니다.');
            $('#pw_check').css("color", "#F00");
        } else {
            $('#pw_check').html('');
        }
    });
}

// 이메일,문자 수신동의 체크
function agreeChk() {
    $("#btn_rdo_b02").click(function() {
        $("#btn_rdo_b02").prop('checked', true);
        $("#btn_rdo_b01").prop('checked', false);
    });
    $("#btn_rdo_b01").click(function() {
        $("#btn_rdo_b01").prop('checked', true);
        $("#btn_rdo_b02").prop('checked', false);
    });
    $("#btn_rdo_b03").click(function() {
        $("#btn_rdo_b03").prop('checked', true);
        $("#btn_rdo_b04").prop('checked', false);
    });
    $("#btn_rdo_b04").click(function() {
        $("#btn_rdo_b04").prop('checked', true);
        $("#btn_rdo_b03").prop('checked', false);
    });
}
// 이메일 형식 체크
function email_check( email ) {    
    var regex=/([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
    return (email != '' && email != 'undefined' && regex.test(email)); 
}
$("input[type=email]").blur(function(){
  var email = $(this).val();
  if( email == '' || email == 'undefined') return;
  if(! email_check(email) ) {
  	$("#email_check").html('이메일 형식으로 적어주세요');
    $('#email_check').css("color", "#F00");
    $(this).focus();
    return false;
  }else {
	$("#email_check").html('');
  }
});

//회원가입 폼 체크
$('#join_btn').click(function(){
    if($('#user_id').val().trim()==""){alert("회원명을 입력하세요.");return;}
    if($('#mj_pw01').val().trim()==""){alert("비밀번호를 입력하세요.");return;}
    if($('#mj_pw02').val().trim()==""){alert("비밀번호 확인을 입력하세요.");return;}
    if($('#mj_name').val().trim()==""){alert("이름을 입력하세요.");return;}
    if($('#user_email').val().trim()==""){alert("이메일을 입력하세요.");return;}
    if($('#chk_agree').is(':checked')==false){alert("이용약관을 동의 하세요.");return;}

    if($.trim($("#inp_phone_first").val())=="" || $.trim($("#inp_phone_last").val())=="") {alert("휴대폰 번호를 입력 해 주세요."); return;}
	if($("#inp_phone_first").val().length < 3 || $("#inp_phone_last").val().length < 4){alert("올바른 휴대폰 번호를 입력 해 주세요."); return;}

    $('#signup_form').attr('action','register_db.php')
    $('#signup_form').submit();
});


</script>