<?
 include($_SERVER["DOCUMENT_ROOT"]."/inc/dbHeader.php");
?>
<link rel="stylesheet" href="/css/btn_box.css">
<style>input[type="text"],input[type="email"]{border:1px solid #dadada;height:50px;width: 90%;border-radius:4px;margin: 5px;}</style>

<div style='height:auto;text-align:center;'>
    <h3>비밀번호 찾기</h3>
    <span style="font-size:11px; ">회원가입 시, 등록한 정보를 통해 비밀번호를 찾으실 수 있습니다.</span>
    <hr>
    <form id="find_pw_form" method='POST' style='display:flex; flex-direction:column; align-items:center;'>
        <span style="font-size:11px;">기업/단체는 기업(단체)명</span>
        <input type="text" id="find_pw_name" placeholder="이름" name="user_name">
        <input type="text" id="find_pw_id" placeholder="아이디" name="user_id">
        <input type="email" id="find_pw_email" placeholder="이메일" name="user_email">
    </form>
    <br>
    <div>
        <span id="find_pw_btn" class="btn_box_ss btn_white radius_10">확인</span>
    </div>
</div>
<script type="text/javascript" src="/js/jquery-3.5.1.min.js"></script>
<script type="text/javascript">
//한글입력방지
$('#find_pw_id').keyup(function(e){
    if(!(e.keycode>=37 && e.keycode<=40)){
        var inputVal = $(this).val();
        var check = /[ㄱ-ㅎ|ㅏ-ㅣ|가-힣]/;
        if(check.test(inputVal)){
            $(this).val("");
        }
    }
})
//폼체크
$('#find_pw_btn').click(function(){
    if($('#find_pw_name').val().trim()==""){alert("이름을 입력하세요");return;}
    if($('#find_pw_id').val().trim()==""){alert("아이디를 입력하세요");return;}
    if($('#find_pw_email').val().trim()==""){alert("이메일을 입력하세요");return;}

    $('#find_pw_form').attr('action','find_passwd_result.php')
    $('#find_pw_form').submit();
});
</script>
