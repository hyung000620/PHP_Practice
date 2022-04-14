<?
$member_only=true;
include($_SERVER["DOCUMENT_ROOT"]."/inc/header.php");
$mobileArr=array('010','011','016','017','018','019');

$SQL="SELECT * FROM {$my_db}.tm_member WHERE id='{$client_id}' LIMIT 0,1";
$stmt=$pdo->prepare($SQL);
$stmt->execute();
$rs=$stmt->fetch();

list($phone1,$phone2,$phone3)=explode("-",$rs['mobile']);
$r_mail=(int)$rs['r_mail'];
$sms=(int)$rs['sms'];
?>
<link rel="stylesheet" href="register.css"> <!-- 이준형 CSS -->
<div class='center f18 gray'>
    우리가 사는 세상이 두루 아름다워질 수 있다고 믿습니다.<br>
    위드탱크와 함께 걸어주세요<br>
</div>
<div style="position:relative;">
    <div><img src='/img/sample/with_sample_1.jpg' style='width:100%'></div>
</div>
<div>
<form id="signup_form" class="member_join_form" method="POST">  
    <table class="tbl07">
        <tbody>
            <tr>
                <td>
                    <div class="required join_tit" title="필수입력"><label for="user_id">아이디</label></div>
                    <div class="isb_wrap_id">
                        <input class="check_id" type="text" id="user_id" value="<?=$client_id?>" disabled>
                    </div>
                    <div id="id_check"></div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="required join_tit" title="필수입력"><label for="existing_pw">현재 비밀번호</label></div>
                    <input type="password" id="existing_pw" class="isw07" name="existing_pw" required>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="required join_tit" title="필수입력"><label for="mj_pw01">새 비밀번호</label></div>
                    <input type="password" id="mj_pw01" class="isw07" name="password" required>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="required join_tit" title="필수입력"><label for="mj_pw02">새 비밀번호 확인</label></div>
                    <input type="password" id="mj_pw02" class="isw07" name="password_confirm" required>
                    <div id="pw_check"></div>
                </td>
            </tr>
            <tr class="member_join_one" data-filed="member_join">
                <td>
                    <div class="required join_tit" title="필수입력"><label for="mj_name">성명·단체명</label></div>
                    <input type="text" id="mj_name" class="isw07" name="user_name" value="<?=$client_name?>"disabled>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="required join_tit">휴대폰 번호</div>
                    <div class="isb_wrap_phone ty02">
                        <label for="slt_phone" class="select">
                            <select id="slt_phone" name="phone1">
                                <?foreach($mobileArr as $v){echo "<option value='{$v}'";if($v==$phone1) echo "selected"; echo">{$v}</option>";}?>
                            </select>
                        </label>
                        <span>-</span>
                        <label for="inp_phone_first"><input type="text" id="inp_phone_first" class="phone_input"
                                name="phone2" value='<?=$phone2?>' maxlength="4" ></label>
                        <span>-</span>
                        <label for="inp_phone_last"><input type="text" id="inp_phone_last" class="phone_input"
                                name="phone3" value='<?=$phone3?>'  maxlength="4" ></label>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div scope="row" class="required join_tit" title="필수입력">이메일</div>
                    <div class="isb_wrap_email">
                        <input class="check" type="email" id="user_email" name="user_email" value='<?=$rs['email']?>' required>
                    </div>
                    <div id="email_check"></div>
                </td>
            </tr>
        </tbody>
    </table>
    <div class="agree_chk">
    <div scope="row" class="join_tit">메일 수신동의</div><br>
    <div class="btn_rdo_area">
        
        <span class="btn_rdo ty01">
            <input type="checkbox" name="r_mail" value="1" id="btn_rdo_b01" <?if($r_mail==1){echo"checked";}?> />
            <label for="btn_rdo_b01">동의</label>
        </span>
        <span class="btn_rdo ty02">
            <input type="checkbox" name="r_mail" id="btn_rdo_b02" value="0" <?if($r_mail==0){echo"checked";}?>/>
            <label for="btn_rdo_b02">미동의</label>
        </span>
    </div><br>          
    <div scope="row" class="join_tit">문자 수신동의</div><br>
    <div class="btn_rdo_area">
        <span class="btn_rdo ty03">
            <input type="checkbox" name="sms" value="1" id="btn_rdo_b03" <?if($sms==1){echo"checked";}?> />
            <label for="btn_rdo_b03">동의</label>
        </span>
        <span class="btn_rdo ty04">
            <input type="checkbox" name="sms" id="btn_rdo_b04" value="0" <?if($sms==0){echo"checked";}?>/>
            <label for="btn_rdo_b04">미동의</label>
        </span>
    </div><br>
    <div class="agree_check_area">
        <input type="hidden" name="user_name" value="<?=$client_name?>">
        <input type="hidden" name="user_id" value="<?=$client_id?>">
        <input type="hidden" name="mode" value="modify">
        <div class="btn_area"><span id="join_btn" class="btn_box_ss btn_tank radius_20" style="width:470px;font-size:25px">정보 수정</span>
        </div>
    </div>
    <div>
</form>
</div>
<? include($_SERVER["DOCUMENT_ROOT"]."/inc/footer.php"); ?>
<script>
$(document).ready(function(e){
    $("#join_btn").click(function(){frmChk();});
    //실시간 비밀번호 체크
    pw_check();
    // 이메일,문자 수신동의 체크
    agreeChk();
});
//실시간 비밀번호 체크
function pw_check()
{
    $('#mj_pw02').on('keyup',function(){
        let self = $(this).val();
        let pw01 = $('#mj_pw01').val();
        if(self != pw01){$('#pw_check').html('비밀번호가 일치하지 않습니다.');$('#pw_check').css('color','#F00');}
        else{$('#pw_check').html("");}
    });
}
// 이메일,문자 수신동의 체크
function agreeChk() 
{
    $("#btn_rdo_b02").click(function(){$("#btn_rdo_b02").prop('checked', true);$("#btn_rdo_b01").prop('checked', false);});
    $("#btn_rdo_b01").click(function(){$("#btn_rdo_b01").prop('checked', true);$("#btn_rdo_b02").prop('checked', false);});
    $("#btn_rdo_b03").click(function(){$("#btn_rdo_b03").prop('checked', true);$("#btn_rdo_b04").prop('checked', false);});
    $("#btn_rdo_b04").click(function(){$("#btn_rdo_b04").prop('checked', true);$("#btn_rdo_b03").prop('checked', false);});
}
//폼 체크
function frmChk()
{   
    if($('#existing_pw').val().trim()==""){alert("현재 비밀번호를 입력하세요.");return;}
    if($('#mj_pw01').val().trim()==$('#existing_pw').val().trim()){alert("기존 비밀번호와 동일합니다.");return;}
    if($('#mj_pw01').val().trim()==""){alert("새 비밀번호를 입력하세요.");return;}
    if($('#mj_pw02').val().trim()==""){alert("새 비밀번호 확인을 입력하세요.");return;}
    if($('#mj_pw01').val().trim()!=$('#mj_pw02').val().trim()){alert("비밀번호가 일치하지 않습니다.");return;}

    $('#signup_form').attr('action',"register_db.php")
    $('#signup_form').submit();
}
</script>
