<?
$page_code="9010";
include $_SERVER["DOCUMENT_ROOT"]."/inc/header.php";
?>
<link rel="stylesheet" href="./css/member.css?ver=<?=$_ver?>">
<div id="memHead"></div>
<div class="wrap">
    <div id="memCtn"></div>
</div>
<form id="memFrm">
    <input type="hidden" name="mode" id="mode" value="<?=$dv?>">
    <input type="hidden" name="agm1" id="agm1" value="">
    <input type="hidden" name="agm2" id="agm2" value="">
    <input type="hidden" name="id" id="id" value="">
    <input type="hidden" name="pw" id="pw" value="">
    <input type="hidden" name="email" id="email" value="">
    <input type="hidden" name="name" id="name" value="">
    <input type="hidden" name="agm_mail" id="agm_mail" value="">
    <input type="hidden" name="agm_sms" id="agm_sms" value="">
    <input type="hidden" name="p1" id="p1" value="">
    <input type="hidden" name="p2" id="p2" value="">
    <input type="hidden" name="p3" id="p3" value="">
</form>
<?include $_SERVER["DOCUMENT_ROOT"]."/inc/footer.php";?>
<script type="text/javascript" src="./js/member.js?ver=<?=$_ver?>"></script>
<script type="text/javascript">
$(document).ready(function(){
    agreeList();
});

function agreeList()
{
    let arr_list=[];
    $('#mode').val(1);
    $.ajax(
    {
        type:"POST",
        url:"./ajax/memAjax.php",
        data: $('#memFrm').serialize(),
        dataType: 'JSON',
        success:function(data)
        {
            if(typeof data!="undefined")
            {
                arr_list.push("<div class='checkbox_group'>");
                arr_list.push("<div class='left'><label class='inputWrap'><input type='checkbox' name='chk_agree_all' id='chk_agree_all' value='1' class='input_chk chk'><span class='f14 chk_ment'> 모두 동의합니다 </span></label></div><hr>");
                arr_list.push("<div>"+data.company+" 이용약관</div>");
                arr_list.push("<div class='lh20 readonly' id='agree1' >"+data.service+"</div>");
                arr_list.push("<div class='left'><label class='inputWrap'><input type='checkbox' name='chk_agree1' id='chk_agree1' value='1' class='input_chk chk chk_normal'><span class='f14 chk_ment'> 이용약관에 동의합니다</span></label></div><br>");
                arr_list.push("<div>"+data.company+" 개인정보취급방침</div>")
                arr_list.push("<div class='lh20 readonly' id='agree2'>"+data.privacy+"</div>")
                arr_list.push("<div class='left'><label class='inputWrap'><input type='checkbox' name='chk_agree2' id='chk_agree2' value='1' class='input_chk chk chk_normal'><span class='f14 chk_ment'> 개인정보취급방침에 동의합니다 </span></label></div><br>");
                arr_list.push("<div class='register_fm_btn center'><span id='btn_register' onclick='joinList()' class='btn_box_ss btn_white radius_10'>다음</span></div>");
                arr_list.push("</div>");
            }
            $('#memCtn').html(arr_list.join(""));
            agree_all();
            agree_necessary();
        }
    });
}

function joinList()
{
    let arr_list=[];
    $('#mode').val(2);
    if($("#chk_agree1").is(":checked")){$('#agm1').val(1);}
    if($("#chk_agree2").is(":checked")){$('#agm2').val(1);}
    $.ajax(
    {
        type:"POST",
        url:"./ajax/memAjax.php",
        data: $('#memFrm').serialize(),
        dataType: 'JSON',
        success: function(data)
        {
            if(typeof data != 'undefined')
            {
                arr_list.push("<form id='signup_form' class='member_join_form' method='POST'>");
                arr_list.push("<table class='tbl07'>");
                arr_list.push("<tbody><tr><td>");
                arr_list.push("<div class='required join_tit' title='필수입력'><label for='user_id'>아이디</label></div>");
                arr_list.push("<div class='isb_wrap_id'><input class='check_id' type='text' id='user_id' name='user_id' required></div>");
                arr_list.push("<div id='id_check'></div>");
                arr_list.push("</td></tr>");
                arr_list.push("<tr><td>");
                arr_list.push("<div class='required join_tit' title='필수입력'><label for='mj_pw01'>비밀번호</label></div>");
                arr_list.push("<input type='password' id='mj_pw01' class='isw07' name='password' required>");
                arr_list.push("</td></tr>");
                arr_list.push("<tr><td>");
                arr_list.push("<div class='required join_tit' title='필수입력'><label for='mj_pw02'>비밀번호 확인</label></div>");
                arr_list.push("<input type='password' id='mj_pw02' class='isw07' name='password_confirm' required>");
                arr_list.push("<div id='pw_check'></div>");
                arr_list.push("</td></tr>");
                arr_list.push("<tr class='member_join_one' data-filed='member_join'><td>");
                arr_list.push("<div class='required join_tit' title='필수입력'><label for='mj_name'>성명·단체명</label></div>");
                arr_list.push("<input type='text' id='mj_name' class='isw07' name='user_name' required>");
                arr_list.push("</td></tr>");
                arr_list.push("<tr><td>");
                arr_list.push("<div class='required join_tit'>휴대폰 번호</div>");
                arr_list.push("<div class='isb_wrap_phone ty02'>");
                arr_list.push("<label for='slt_phone' class='select'><select id='slt_phone' name='phone1'>");
                $.each(data.item,function(idx,val){
                    arr_list.push("<option value='"+val+"'");
                    if(val=='010'){arr_list.push(" selected");}
                    arr_list.push(">"+val);
                    arr_list.push("</option>");
                });
                arr_list.push("</select></label><span>-</span>");
                arr_list.push("<label for='inp_phone_first'><input type='text' id='inp_phone_first' class='phone_input' name='phone2' maxlength='4' onKeyup='this.value=this.value.replace(/[^-0-9]/g,\"\");'></label>");
                arr_list.push("<label for='inp_phone_last'><input type='text' id='inp_phone_last' class='phone_input' name='phone3' maxlength='4' onKeyup='this.value=this.value.replace(/[^-0-9]/g,\"\");'></label>");
                arr_list.push("</div></td></tr>");
                arr_list.push("<tr><td>");
                arr_list.push("<div scope='row' class='required join_tit' title='필수입력'>이메일</div>");
                arr_list.push("<div class='isb_wrap_email'><input class='check' type='email' id='user_email' name='user_email' required></div>");
                arr_list.push("<div id='email_check'></div></td></tr></tbody></table>");
                arr_list.push("<div class='agree_chk'>");
                arr_list.push("<div scope='row' class='join_tit'>메일 수신동의</div><br>");
                arr_list.push("<div class='btn_rdo_area'><span class='btn_rdo ty03'><input type='checkbox' name='r_mail' value='1' id='btn_rdo_b01' checked /><label for='btn_rdo_b01'>동의</label></span><span class='btn_rdo ty03'><input type='checkbox' name='r_mail' id='btn_rdo_b02' value='0' /><label for='btn_rdo_b02'>미동의</label></span></div><br>");
                arr_list.push("<div scope='row' class='join_tit'>문자 수신동의</div><br>");
                arr_list.push("<div class='btn_rdo_area'><span class='btn_rdo ty03'><input type='checkbox' name='sms' value='1' id='btn_rdo_b03' checked /><label for='btn_rdo_b03'>동의</label></span><span class='btn_rdo ty02'><input type='checkbox' name='sms' id='btn_rdo_b04' value='0' /><label for='btn_rdo_b04'>미동의</label></span></div><br>");
                arr_list.push("<div class='agree_check_area'><div class='btn_area'><span id='join_btn' onclick='sucList()' class='btn_box_ss btn_tank radius_20' style='width:470px;font-size:25px'>회원가입</span></div></div></form>");
                
            }
            $('#memCtn').html(arr_list.join(""));
            id_check();
            pw_check();
            agreeChk();
            noKorea();
            email_check( email );
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
        }
    });
}
function sucList()
{
    if($('#id_check').text()=="존재하는 ID 입니다"){alert("다른 아이디를 입력하세요");return;}
    if($('#user_id').val().trim()==""){$('#id_check').html("");alert("회원아이디를 입력하세요.");return;}
    if($('#mj_pw01').val().trim()==""){alert("비밀번호를 입력하세요.");return;}
    if($('#mj_pw02').val().trim()==""){alert("비밀번호 확인을 입력하세요.");return;}
    if($('#mj_name').val().trim()==""){alert("이름을 입력하세요.");return;}
    if($('#user_email').val().trim()==""){alert("이메일을 입력하세요.");return;}

    if($.trim($("#inp_phone_first").val())=="" || $.trim($("#inp_phone_last").val())=="") {alert("휴대폰 번호를 입력 해 주세요."); return;}
	if($("#inp_phone_first").val().length < 3 || $("#inp_phone_last").val().length < 4){alert("올바른 휴대폰 번호를 입력 해 주세요."); return;}

    $('#id').val($('#user_id').val());
    $('#pw').val($('#mj_pw01').val());
    $('#name').val($('#mj_name').val());
    $('#email').val($('#user_email').val());
    $('#agm_mail').val($('#btn_rdo_b01').val());
    $('#agm_sms').val($('#btn_rdo_b03').val());

    $('#p1').val($('#slt_phone').val());
    $('#p2').val($('#inp_phone_first').val());
    $('#p3').val($('#inp_phone_last').val());
    $('#mode').val(3);
    let arr_list=[];
    let arr_head=[];
    $.ajax(
    {
        type: "POST",
        url: "./ajax/memAjax.php",
        data: $('#memFrm').serialize(),
        dataType: 'JSON',
        success: function(data)
        {
            if(data.success==1)
            {
                arr_head.push("<div style='position:relative;'>");
                arr_head.push("<div><img src='/img/sample/support_sample_1.jpg' style='width:100%'></div>");
                arr_head.push("</div>");

                arr_list.push("<div class='f30 center' style='padding:30px 0 50px 0'>회원가입 완료</div>");
                arr_list.push("<table class='tbl_noline'><tr><td class='f16 bold' style='padding:20px;line-height:25px'>");
                arr_list.push(data.site_name+" 회원으로 가입해주셔서 감사합니다. <br>");
                arr_list.push("항상 최상의 서비스를 위해 최선을 다하겠습니다. 더 많은 사랑과 격려를 부탁 드립니다.<br><br>");
                arr_list.push("<div class='register_fm_btn center' style='padding:30px 0'><a href='/'><span class='btn_box_ss btn_tank radius_10'>홈으로 이동</a></a></div>");
                arr_list.push("</td></tr></table><div style='padding:50px'></div>");
            }
            else
            {
                alert('이미 등록된 ID 입니다');
                return;
            }
            $('#memHead').html(arr_head.join(""));
            $('#memCtn').html(arr_list.join(""));
        }
    })
}
</script>