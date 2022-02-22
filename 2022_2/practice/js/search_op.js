$(document).ready(function()
{   
    // 주소 / 법원 토글
    toggleCha();

    // 주소 변경
    addrInit();

    $(".chkMulti").hide();
});

function addrInit()
{
    $("#siCd").change(function(){loadAddr($(this));});
	$("#guCd").change(function(){loadAddr($(this));});
	loadAddr($("#siCd"));

}

function loadAddr(obj)
{   
    var arr=[], html="";
    if(obj.attr("id")=="siCd")
    {
        $("#guCd").children().remove();
		$("#dnCd").children().remove();
		$("#siCd").append("<option value='0'>-시/도-</option>");
		$("#guCd").append("<option value='0'>-구/군-</option>");
		$("#dnCd").append("<option value='0'>-읍/면/동-</option>");
    }
    if(obj.attr("id")=="guCd")
    {
        $("#dnCd").children().remove();
		$("#dnCd").append("<option value='0'>-읍/면/동-</option>");
    }

    $.ajax({
        type: "POST",
        url: "/practice/auc/com_ax.php",
        data: "queryType=addr&siCd_code="+$('#siCd').val()+"&guCd_code="+$('#guCd').val()+"&dnCd_code="+$('#dnCd').val(),
        dataType: "xml",
        beforeSend: function(){},
        success: function(xml)
        {
            $(xml).find("item").each(function(){
                var $entry = $(this);
                arr.push("<option value='"+$entry.find("addr_code").text()+"'>"+$entry.find("addr_name").text()+"</option>");
            });
            html=arr.join("");
            var obj_id=$(xml).find("obj_id").text();
            $("#"+obj_id).append(html);

            if(obj.attr("id")=="siCd" && $("#refSiCd").val() > 0)
            {
                setTimeout(function(){
                    $("#siCd option[value="+$("#refSiCd").val()+"]").attr("selected",true);
                    $("refSiCd").val(0);
                    loadAddr($("#guCd"));
                },10);
                return;
            }
            if(obj.attr("id")=="guCd" && $("#refGuCd").val() > 0)
            {
                setTimeout(function(){
                    $("#guCd option[value="+$("#refGuCd").val()+"]").attr("selected",true);
                    $("refGuCd").val(0);
                    loadAddr($("#dnCd"));
                },10);
                return;
            }
            if(obj.attr("id")=="dnCd" && $("#refDnCd").val() > 0)
            {
                setTimeout(function(){
                    $("#dnCd option[value="+$("#refDnCd").val()+"]").attr("selected",true);
                    $("refDnCd").val(0);
                },10);
            }
        }
    });
}
function toggleCha(){
    $('#row_s').hide();
    //주소
    $('#addr1').click( function(){
        $('#addr1').css('background','#1B43A9');
        $('#addr2').css('background','#e7e7e7');
        $('#addr_s').show();
        $('#row_s').hide();
    });
    // 법원
    $('#addr2').click( function(){
        $('#addr2').css('background','#1B43A9');
        $('#addr1').css('background','#e7e7e7');
        $('#addr_s').hide();
        $('#row_s').show();
    });
}

function chkCtgrMulti(k,depth)
{   
    if(depth==0)
    {
        $("#chkAllCtgr").prop("checked",true);
        $("input:checkbox[name=chkEaCtgr]").prop("checked",false);
        $("input:checkbox[name=chkGrpCtgr]").prop("checked",false);
    }
    else if(depth==1)
    {
        $("#chkAllCtgr").prop("checked",false);
        $("chkCtgr_"+k).prop("checked",true);
        $("[id ^='chkCtgr_"+k+"']").prop("checked",true);

    }
    else if(depth==2)
    {
        $("#chkAllCtgr").prop("checked",false);
        $("chkGrpCtgr_"+k).prop("checked",false);

    }
}

function multi(obj)
{
    if(obj.value == "복수선택")
    {
        $(".chkMulti").show();
        $("#sel_pord").attr("disabled",true);
        obj.value = "단일선택";
    }   
    else
    {
        $(".chkMulti").hide();
        $("#sel_pord").attr("disabled",false);
        obj.value = "복수선택";
    }
}
