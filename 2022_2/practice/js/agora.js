$(document).ready(function ($) {
    $('#agBtn').click(function () {
        ago_ls();
    });
});

function ago_ls() {
    var arr_body = [];
    $.ajax({
            type: "POST",
            url: "/practice/Edu/agora_ax.php",
            data: {
                dvsn : $('#dvsn').val(),
                text : $('#ag_val').val()
            },
            dataType: "JSON",
            success: function (data) {
                console.log(data);
                if (data.item != "undefined") {
                    $.each(data.item, function () {
                        arr_body.push("<tr>");
                        arr_body.push(" <td><img width =150px src='../../FILE/NEWS/cover/" + this.cover_img + "'><img></td>");
                        arr_body.push(" <td>" + this.title + "</td>");
                        arr_body.push(" <td>" + this.rdt + "</td>");
                        arr_body.push("</tr>");
                    });

                    $('#ag_ls').html(arr_body.join(""));
                }
            }
        });
}