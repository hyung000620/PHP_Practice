<?
$dv=($_GET['dv'])?$_GET['dv']:"10";
$page_code="50".$dv;
include_once($_SERVER["DOCUMENT_ROOT"]."/inc/header.php");
$boardArr=array('10'=>'mreport','20'=>'ydonate','30'=>'result','40'=>'ldonate');
$board_id=$boardArr[$dv];
?>
<link rel="stylesheet" href="/member/css/member.css">

<div class='wrap'>
	<div class='li_teb'>
		<ul class='ul_teb'>
			<li name="10" <?if($dv==10){echo "class='on'";}?> >결산안내</li>
            <li name="20" <?if($dv==20){echo "class='on'";}?> >연간기부금</li>
            <li name="30" <?if($dv==30){echo "class='on'";}?> >활용실적</li>
            <li name="40" <?if($dv==40){echo "class='on'";}?> >후원금내용</li>
		</ul>
	</div>
	<div class='clear'></div>
    <div>
        <form id='srchFrm' method="POST" style='margin-top:28px;'>
        <!-- 검색 -->
            <div class="search">
                <input type="hidden" id="page" name="page" value="1">
                <input type="hidden" name="board_id" value="<?=$board_id?>">
                <input type="hidden" name="mode" value="10">
                <input id='keyword' name='keyword' type="text" placeholder="검색어를 입력하세요.">
                <img id='srchBtn' src="/img/icon/ser-icon1.png">
            </div>
        </form>
        <ul id="htmlData" style="margin-top:30px; border-top:2px solid #1B43A9; border-bottom:2px solid #cccccc"><!-- 목록 --></ul>        
        <div class='paging' style="text-align:center; margin-top:30px;">
            <a href='#' id="minPage" style='display:inline-block; border:1px solid #cccccc; font-size:10px; width:30px; height:30px; line-height:28px'>◀</a>
            <div class='paging_navi' style="margin:0 6px; width: auto; line-height:0; display:inline-block;"><!-- 페이징 --></div>
            <a href='#' id="maxPage" style='display:inline-block; border:1px solid #cccccc; font-size:10px; width:30px; height:30px; line-height:28px'>▶</a>  
        </div>
    </div>
    <form id="frmData" method="POST">
        <input type="hidden" id="mode" name="mode" value="">
        <input type="hidden" id="board_id" name="board_id" value="<?=$board_id?>">
        <input type="hidden" id="idx" name="idx" value="">
        <input type="hidden" id="total_page" name="total_page" value="">
    </form>
</div>
<? include_once($_SERVER["DOCUMENT_ROOT"]."/inc/footer.php");?>
<script>
var url_default_naver = "http://share.naver.com/web/shareView.nhn?url=";
var title_default_naver = "&title=";
var url_this_page = location.href;
var title_this_page = document.title;

$(function(){
    paging();
    //카테고리
    ulTebClk();
    //로딩
    srchKeyword();
    //엔터키 및 검색 클릭 이벤트
    $('#srchBtn').click(function(){srchKeyword();});
    $('#keyword').on('keypress',function(keyNum){
        if(keyNum.keyCode==13){
            $('#srchBtn').click();
            return false;
        }
    });
    //idx 값이 있을 경우
    if(getQueryString('idx')){
        let idx = getQueryString('idx');
        frmView(idx);
    }
    page_navi();
});

function ulTebClk()
{
    $('.ul_teb >li').click(function(){
        $('.ul_teb li').removeClass('on');
        let dv = $(this).attr('name');
        window.location = '<?=$PHP_SELF;?>?dv='+dv;
    })
}
//키워드 검색
function srchKeyword()
{
    $.ajax({
        type: "POST",
        url: "/sam_board/board_50_res.php",
        data: $('#srchFrm').serialize(),
        dataType: "JSON",
        success: function(data){
            let arr_list=[];
            let arr_page=[];
            if(data.total_record==0)
            {
                arr_list.push("<div style='text-align:center;'>");
                arr_list.push("<img src='data:image/png;base64,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'>");
                arr_list.push("</div>");
            }
            else
            {
                let page = $('#page').val();
                $.each(data.item, function()
                {
                    arr_list.push("<li style='border-top:1px solid #cccccc'>");
                    arr_list.push("<a href='#' onclick=");
                    arr_list.push("frmView("+this.idx+")");
                    arr_list.push(" style='padding:12px; display:block;'>");
                    arr_list.push("<span class='bold' style='font-size:15px'>"+this.title+"</span></br>");
                    arr_list.push("<span>"+this.wdate+"<span></a></li>");
                });
                $('#total_page').attr('value',data.total_page);
                for(var i=1; i<=data.total_page; i++)
                {
                    arr_page.push("<span ");
                    if(i== page){arr_page.push(" class='on' ");}
                    arr_page.push(" onclick=page_navi("+i+")")
                    arr_page.push(" style='cursor:pointer; border:1px solid #cccccc; width: 30px; height:30px; line-height:28px; font-size:12px; display:inline-block;' >");
                    arr_page.push(i);
                    arr_page.push("</span>");
                }
            }
            $('.paging_navi').html(arr_page.join(""));
            $('#htmlData').html(arr_list.join(""));
        }
    })
}
//board_view
function frmView(idx)
{
    $('#mode').attr('value','20');
    $('#idx').attr('value',idx);
    let arr_list=[];
    $.ajax(
    {
        type: "POST",
        url: "/sam_board/board_50_res.php",
        data: $('#frmData').serialize(),
        dataType: "JSON",
        success: function(data)
        {
            if(data.del==1){alert('삭제된 글입니다.');}
            else
            {
                
                arr_list.push("<div style=' height:100%;'>");
                arr_list.push("<div class='right'><img style='cursor:pointer; width: 60px;' onclick='naver_share("+idx+")'");
                arr_list.push(" src='https://img1.daumcdn.net/thumb/R1280x0/?scode=mtistory2&fname=http%3A%2F%2Fcfile6.uf.tistory.com%2Fimage%2F2264613856C4A5060B02F8' ></div>");
                arr_list.push("<div>"+data.content+"</div>");
                if(data.file_record>0)
                {
                    let i = 0;
                    if(data.file>0)
                    {
                        arr_list.push("<div style='font-size:12px;width:100%;line-height:40px;margin:15px 0'>");
                        arr_list.push("<div style='border:1px solid #ddd;padding-left:3px;'>");
                        arr_list.push("<span style='margin-right:10px;background-color:#777;color:#fff;padding:10px;-moz-border-radius:10px;-webkit-border-radius:10px;border-radius:10px;'>FILE ></span>");
                        $.each(data.item,function()
                        {
                            i++;
                            if(this.mime_type!="application/pdf")
                            {
                                let bgcol="";
                                if(i==1){bgcol="#FF8C12";}else{bgcol="#1B43A9";}
                                arr_list.push("<a href='/board/inc/download.php?idx="+this.idx+"' style=';padding:8px;margin-right:15px;background-color:"+bgcol+";color:#fff;cursor:pointer;-moz-border-radius:15px;-webkit-border-radius:15px;border-radius:15px;-webkit-box-shadow:1px 1px 12px -3px rgba(0,0,0,0.74);-moz-box-shadow: 1px 1px 12px -3px rgba(0,0,0,0.74);box-shadow: 1px 1px 12px -3px rgba(0,0,0,0.74);'>"+this.org_file+"("+this.file_size+")</a>")
                            }
                        });
                        arr_list.push("</div>");
                        arr_list.push("</div>");
                    }
                    i=0;
                    if(data.pdf>0)
                    {
                        arr_list.push("<div style='font-size:12px;width:100%;line-height:40px;margin:15px 0'>");
                        arr_list.push("<div style='border:1px solid #ddd;padding-left:3px;'>");
                        arr_list.push("<span style='margin-right:10px;background-color:#777;color:#fff;padding:10px;-moz-border-radius:10px;-webkit-border-radius:10px;border-radius:10px;'>PDF ></span>");
                        $.each(data.item,function()
                        {
                            i++;
                            if(this.mime_type=="application/pdf")
                            {
                                let bgcol="";
                                if(i==1){bgcol="#FF8C12";}else{bgcol="#1B43A9";}
                                arr_list.push("<span class='bold ltblue' id='file_item"+this.idx+"' onclick=\"pdfVIEW("+this.idx+",'"+this.org_file+"','"+this.org_file+"','"+this.file_size+"');\" style='position:relative;top:2px;padding:8px;margin-right:15px;background-color:"+bgcol+";color:#fff;cursor:pointer;-moz-border-radius:15px;-webkit-border-radius:15px;border-radius:15px;-webkit-box-shadow:1px 1px 12px -3px rgba(0,0,0,0.74);-moz-box-shadow: 1px 1px 12px -3px rgba(0,0,0,0.74);box-shadow: 1px 1px 12px -3px rgba(0,0,0,0.74);'>"+this.org_file+"</span>")
                                arr_list.push("</div>");
                                arr_list.push("<iframe id='if_pdf' src='/API/PDF/web/pdf.php?file=/board/atfile/"+this.sav_file+"' style='width:100%;height:800px;border:none;border-bottom:2px solid #ccc'></iframe>");
                                arr_list.push("</div>");
                            }
                        });
                    }
                }
                arr_list.push("</div>");
                $('.search').hide();
                $('.paging').hide();
                $('#htmlData').html(arr_list.join(""));
            }
        }
    })
}
//네이버 공유
function naver_share(idx)
{
    url_this_page += "&idx="+idx;
    var url_combine_naver = url_default_naver + encodeURI(encodeURIComponent(url_this_page)) + title_default_naver + encodeURI(title_this_page);
    var popupWidth = 600;
    var popupHeight = 600;
    var popupX = (window.screen.width / 2) - (popupWidth / 2);
    var popupY = (window.screen.height / 2) - (popupHeight / 2);
    window.open(url_combine_naver, '', 'status=no, height=' + popupHeight + ', width=' +
        popupWidth + ', left=' + popupX + ', top=' + popupY);
}
//쿼리스트링 값 가져오기
function getQueryString(key) {
    var str = location.href;
    var index = str.indexOf("?") + 1;
    var lastIndex = str.indexOf("#") > -1 ? str.indexOf("#") + 1 : str.length;
    if (index == 0) {
        return "";
    }
    str = str.substring(index, lastIndex);
    str = str.split("&");
    var rst = "";

    for (var i = 0; i < str.length; i++) {
        var arr = str[i].split("=");
        if (arr.length != 2) {
            break;
        }
        if (arr[0] == key) {
            rst = arr[1];
            break;
        }
    }
    return rst;
}

function paging()
{
    $('#maxPage').click(function(){
        let page = $('#page').val();
        let total_page= $('#total_page').val();
        if(page<total_page){page++;}
        $('#page').attr('value',page);
        $('#mode').attr('value',10);
        srchKeyword();
    });
    $('#minPage').click(function(){
        let page = $('#page').val();
        if(page>1){page--;}
        $('#page').attr('value',page);
        $('#mode').attr('value',10);
        srchKeyword();
    });
    
}

function page_navi(page)
{
    $('#page').attr('value',page);
    srchKeyword();
}
</script>