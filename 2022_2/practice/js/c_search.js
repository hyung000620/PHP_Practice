$(document).ready(function ($) {
    $("#csBtn").click(function () {
        TK['datepicker1'] = $('#datepicker1').val();
        TK['datepicker2'] = $('#datepicker2').val();
        TK['mode'] = $('#mode').val();
        cal_ls();
    });
    // $("#datepicker1").click(function () {
    //     TK2['sdate']=this.value;
    //     console.log(TK2);
    // });

});

// this 나중에
function cal_ls() {
    var arr_head = [];
    var arr_body = [];
    console.log(TK);
    $.ajax({
        type: "POST",
        url: "/practice/auc/com.php",
        data: TK,
        dataType: "JSON",
        success: function(data)
      {
          console.log(data);
        if(data.item!="undefined")
			  {
			    arr_head.push("<tr>");
			    arr_head.push(" <th>제목</th>");
			    arr_head.push(" <th>글쓴이</th>");
			    arr_head.push(" <th>내용</th>");
			    arr_head.push(" <th>조회수</th>");
			    arr_head.push(" <th>등록일</th>");
			    arr_head.push("</tr>");
			    $.each(data.item,function()
          {
            arr_body.push("<tr>");
            arr_body.push(" <td>"+this.title+"</td>");
            arr_body.push(" <td>"+this.uname+"</td>");
            arr_body.push(" <td>"+this.content+"</td>");
            arr_body.push(" <td>"+this.view+"</td>");
            arr_body.push(" <td>"+this.rdate+"</td>");
            arr_body.push("</tr>");
          });

                $('#lsThead').html(arr_head.join(""));
                $('#lsTbody').html(arr_body.join(""));
            }

        }
        
    })
}
