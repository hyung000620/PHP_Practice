<?
$page_code="9010";
 include($_SERVER["DOCUMENT_ROOT"]."/inc/header.php");
?>
<div class='center f18 gray'>
    우리가 사는 세상이 두루 아름다워질 수 있다고 믿습니다.<br>
    위드탱크와 함께 걸어주세요<br>
</div>
<div style="position:relative;">
    <div><img src='/img/sample/support_sample_1.jpg' style='width:100%'></div>
    <form id='chk_form' method='POST' style='margin: 0 auto; width:600px;'>
        <br>
        <div class='left'><label class='inputWrap'><input type="checkbox" name="chk_agree_all" id="chk_agree_all" value="1" class='input_chk chk'><span class="f14 chk_ment"> 모두 동의합니다 </span></label></div>
        <hr>
        <div><?=TK_COMPANY?> 이용약관</div>
        <div class="lh20 readonly" style="height:200px;overflow-y:scroll;width:100%"><? include($_SERVER["DOCUMENT_ROOT"]."/policy/service_guide_text.php"); ?></div>
        <div class="left"><label class='inputWrap'><input type="checkbox" name="chk_agree1" id="chk_agree1" value="1" class='input_chk chk'><span class="f14 chk_ment"> 이용약관에 동의합니다</span></label></div>
        <br>
        <div><?=TK_COMPANY?> 개인정보취급방침</div>
        <div class="lh20 readonly" style="height:200px;overflow-y:scroll;width:100%"><? include($_SERVER["DOCUMENT_ROOT"]."/policy/privacy_guide_text.php");?></div>
        <div class='left'><label class='inputWrap'><input type="checkbox" name="chk_agree2" id="chk_agree2" value="1" class='input_chk chk'><span class="f14 chk_ment"> 개인정보취급방침에 동의합니다 </span></label></div>
        <br>
        <div class="register_fm_btn center"><span id="btn_register" class="btn_box_ss btn_white radius_10">다음</span></div>
    </form>
</div>
<?
 include($_SERVER["DOCUMENT_ROOT"]."/inc/footer.php");
?>
<script>
$(document).ready(function(){
    agree_all();
    agree_necessary();
});

//모두 동의
function agree_all()
{
    $('#chk_agree_all').click(function(){
    if($(this).is(":checked")){
        $('#chk_agree1').prop('checked',true);
        $('#chk_agree2').prop('checked',true);
    }
    });
    $('#chk_agree_all').click(function(){
    if($(this).is(":checked")==false){
        $('#chk_agree1').prop('checked',false);
        $('#chk_agree2').prop('checked',false);
    }
    });
    $('#chk_agree1').click(function(){
        if($('#chk_agree_all').is(':checked')==true){
            $('#chk_agree_all').prop('checked',false);
        }
    });
    $('#chk_agree2').click(function(){
        if($('#chk_agree_all').is(':checked')==true){
            $('#chk_agree_all').prop('checked',false);
        }
    });
}

//필수동의 여부 체크
function agree_necessary()
{
    $('#btn_register').click(function(){
        if($("#chk_agree1").is(":checked")==false || $("#chk_agree2").is(":checked")==false){alert("이용약관 및 개인정보제공/수집에 모두 동의 해 주세요."); return;}
		else{$('#chk_form').attr('action','register.php');$("#chk_form").submit();}
    });
}
</script>