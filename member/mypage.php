<?
//$member_only=true;
$dv=($_GET['dv'])?$_GET['dv']:6;
$page_code="9010";
include($_SERVER["DOCUMENT_ROOT"]."/inc/header.php");
?>
<link rel="stylesheet" href="./css/member.css?ver=<?=$_ver?>">
<div id="myHead"></div>
<div id="myCtn" class='wrap'></div>
<form id="myFrm">
    <input type="hidden" id="mode" name="mode" value="<?=$dv?>">
    <input type="hidden" id="user_id" name="user_id" value="<?=$client_id?>">
    <input type="hidden">
</form>
<?include($_SERVER["DOCUMENT_ROOT"]."/inc/footer.php");?>
<script type="text/javascript" src="./js/member.js?ver=<?=$_ver?>"></script>
<script type="text/javascript">
$(function(){
    // mypage();
    let val = $('#mode').val();
    switch(val)
    {
        case '6': mypage();break;
        case '10': modify_profile(); break;
    }
})

function mypage()
{
    let arr_list=[];
    let arr_head=[];
    $.ajax(
    {
        type:"POST",
        url:"/member/ajax/memAjax.php",
        data: $('#myFrm').serialize(),
        dataType: "JSON",
        success:function(data)
        {
            if(typeof data != "undefined")
            {
                arr_head.push("<div style='padding:20px 0'>");
                arr_head.push("<img src='/img/sample/support_sample_1.jpg' style='width:100%'></div>");

                arr_list.push("<div class='center bold f15 gray lh20' >");
                arr_list.push(data.site_name+" 회원님이 되신것을 축하 드립니다. <br>");
                arr_list.push("마이페이지의 기능으로, 보다 편리하게 정보를 이용 할 수 있습니다. </div><br>");
                arr_list.push("<table class='mypage_tbl'><tbody><tr>");
                arr_list.push("<td onclick='location.href=\"/member/mypage.php?dv=10\"'><div class='mypage_title'>회원정보 수정</div></td>");
                arr_list.push("<td><div class='mypage_title'>후원내역</div></td>");
                arr_list.push("<td><div class='mypage_title'>회원탈퇴</div></td></tr><tr>");
                arr_list.push("<td onclick='location.href=\"/policy/privacy_guide.php\"'><div class='mypage_title'>개인정보 취급방침</div></td>");
                arr_list.push("<td onclick='location.href=\"/policy/service_guide.php\"'><div class='mypage_title'>이용약관</div></td>");
                arr_list.push("<td class='pop_email_btn hand'><div class='mypage_title' >이메일 무단수집거부</div></td></tr></tbody></table>");
                arr_list.push("<br><div class='center bold f15 gray lh20'>본 사이트에 적용되는 이용약관및 사이트 운영원칙을 참조 하시기 바랍니다.</div>");
            }
            $('#myHead').html(arr_head.join(""));
            $('#myCtn').html(arr_list.join(""));

            //이메일 수신거부
            $('.pop_email_btn').click(function(){
                $('#pop_email').show();
            });
            $('.btn_close').click(function(){
                $('#pop_email').hide();
            });
        }
    })
}

function modify_profile() 
{
    let arr_list=[];
    let arr_head=[];
    $.ajax(
    {
        type:"POST",
        url:"/member/ajax/memAjax.php",
        data: $('#myFrm').serialize(),
        dataType: "JSON",
        success: function(data)
        {
            if(typeof data != "undefined")
            {
                arr_head.push('<div class=\'center f18 gray\'>');
                arr_head.push('우리가 사는 세상이 두루 아름다워질 수 있다고 믿습니다.<br>위드탱크와 함께 걸어주세요<br></div>');
                
                arr_list.push("<form id=\"signup_form\" class=\"member_join_form\" method=\"POST\">");
                arr_list.push("<table class=\"tbl07\"><tbody><tr><td>");
                arr_list.push("<div class=\"required join_tit\" title=\"필수입력\"><label for=\"user_id\">아이디</label></div>");
                arr_list.push("<div class=\"isb_wrap_id\">");
                arr_list.push("<input class=\"check_id\" type=\"text\" id=\"user_id\" value='"+data.id+"' disabled>");
                arr_list.push("<div id='id_check'></div></td></tr><tr><td>");
                arr_list.push("<div class=\"required join_tit\" title=\"필수입력\"><label for=\"existing_pw\">현재 비밀번호</label></div>");
                arr_list.push("<input type=\"password\" id=\"existing_pw\" class=\"isw07\" name=\"existing_pw\" required></td></tr>");
                arr_list.push("<tr><td><div class=\"required join_tit\" title=\"필수입력\"><label for=\"mj_pw01\">새 비밀번호</label></div>");
                arr_list.push("<input type=\"password\" id=\"mj_pw01\" class=\"isw07\" name=\"password\" required></td></tr>");
                arr_list.push("<tr><td><div class=\"required join_tit\" title=\"필수입력\"><label for=\"mj_pw02\">새 비밀번호 확인</label></div>");
                arr_list.push("<input type=\"password\" id=\"mj_pw02\" class=\"isw07\" name=\"password_confirm\" required>");
                arr_list.push("<div id=\"pw_check\"></div></td></tr>");
                arr_list.push("<tr class=\"member_join_one\" data-filed=\"member_join\"><td>");
                arr_list.push("<div class=\"required join_tit\" title=\"필수입력\"><label for=\"mj_name\">성명·단체명</label></div>");
                arr_list.push("<input type=\"text\" id=\"mj_name\" class=\"isw07\" name=\"user_name\" value='"+data.name+"' disabled>");
                arr_list.push("</td></tr><tr><td><div class=\"required join_tit\">휴대폰 번호</div>");
                arr_list.push("<div class=\"isb_wrap_phone ty02\"><label for=\"slt_phone\" class=\"select\">");
                arr_list.push("<select id=\"slt_phone\" name=\"phone1\">");
                $.each(data.item,function(idx,val){
                    arr_list.push("<option value='"+val+"'");
                    if(val==data.phone1){arr_list.push(" selected");}
                    arr_list.push(">"+val);
                    arr_list.push("</option>");
                });
                arr_list.push("</select></label><span>-</span>");
                arr_list.push("<label for=\"inp_phone_first\"><input type=\"text\" id=\"inp_phone_first\" class=\"phone_input\" name=\"phone2\" value='"+data.phone2+"' maxlength=\"4\" onKeyup=\"this.value=this.value.replace(/[^-0-9]/g,'');\"></label>");
                arr_list.push("<span>-</span>");
                arr_list.push("<label for=\"inp_phone_last\"><input type=\"text\" id=\"inp_phone_last\" class=\"phone_input\" name=\"phone3\" value='"+data.phone3+"'  maxlength=\"4\" onKeyup=\"this.value=this.value.replace(/[^-0-9]/g,'');\"></label>");
                arr_list.push("</div></td></tr><tr><td>");
                arr_list.push("<div scope=\"row\" class=\"required join_tit\" title=\"필수입력\">이메일</div>");
                arr_list.push("<div class=\"isb_wrap_email\">");
                arr_list.push("<input class=\"check\" type=\"email\" id=\"user_email\" name=\"user_email\" value='"+data.email+"' required>");
                arr_list.push("</div><div id=\"email_check\"></div></td></tr></tbody></table>");
                arr_list.push("<div class=\"agree_chk\"><div scope=\"row\" class=\"join_tit\">메일 수신동의</div><br>");
                arr_list.push("<div class=\"btn_rdo_area\"><span class=\"btn_rdo ty01\">");
                let ch ="";
                data.r_mail==1? ch ="checked": ch="";
                arr_list.push("<input type=\"checkbox\" name=\"r_mail\" value=\"1\" id=\"btn_rdo_b01\" "+ch+"/>");
                arr_list.push("<label for=\"btn_rdo_b01\">동의</label></span>");
                arr_list.push("<span class=\"btn_rdo ty02\">");
                data.r_mail==0? ch ="checked": ch="";
                arr_list.push("<input type=\"checkbox\" name=\"r_mail\" id=\"btn_rdo_b02\" value=\"0\" "+ch+"/>");
                arr_list.push("<label for=\"btn_rdo_b02\">미동의</label></span></div><br>");
                arr_list.push("<div scope=\"row\" class=\"join_tit\">문자 수신동의</div><br>");
                arr_list.push("<div class=\"btn_rdo_area\"><span class=\"btn_rdo ty03\">");
                data.sms==1? ch="checked":ch="";
                arr_list.push("<input type=\"checkbox\" name=\"sms\" value=\"1\" id=\"btn_rdo_b03\" "+ch+" />");
                arr_list.push("<label for=\"btn_rdo_b03\">동의</label></span>");
                arr_list.push("<span class=\"btn_rdo ty04\">");
                data.sms==0? ch="checked":ch="";
                arr_list.push("<input type=\"checkbox\" name=\"sms\" id=\"btn_rdo_b04\" value=\"0\" "+ch+"/>");
                arr_list.push("<label for=\"btn_rdo_b04\">미동의</label></span></div><br>");
                arr_list.push("<div class=\"agree_check_area\">");
                arr_list.push("<div class=\"btn_area\"><span id=\"join_btn\" class=\"btn_box_ss btn_tank radius_20\" style=\"width:470px;font-size:25px\">정보 수정</span></div></div></form>");

                $('#myHead').html(arr_head.join(""));
                $('#myCtn').html(arr_list.join(""));

                $("#join_btn").click(function(){frmChk2();});pw_check2();agreeChk();
            }
        }
    });
}
</script>