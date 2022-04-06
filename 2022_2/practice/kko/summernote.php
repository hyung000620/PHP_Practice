<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <title>Summernote</title>
  <link href="/kko/css/test.css" rel="stylesheet">
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
</head>
<body>
<div class="contact-area section_padding_80">
<div class="container">
    <!-- Contact Form  -->
    <div class="contact-form-area">
        <div class="row">
            <div class="col-12 col-md-12 item">
                <div class="contact-form wow fadeInUpBig" data-wow-delay="0.6s">
                    <h2 class="contact-form-title mb-30">SUMMERNOTE</h2>
                    <!-- Contact Form -->
                    <form action="#" method="post">
                    <input type="hidden" name="imgUrl" id="imgUrl" value="">
                    <input type="hidden" name="attachFile" id="attachFile" value="">
                        <div class="form-group">
                            <input type="text" class="form-control" id="subject" placeholder="제목">
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control" id="childName" placeholder="이름">
                        </div>
                        <div class="form-group">
                            <div id="summernote"></div>
                        </div>
                        <div class="form-group">
                            <div id="attach_site">
                                <div id="attachFiles">
                                </div>
                                <input type="file" multiple class="form-input" name="afile" id="afile" />
                            </div>
                        </div>
                        <button type="button" class="btn contact-btn"  onclick="saveUp();">WRITE</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</div>
<script>
$(document).ready(function() {
    $('#summernote').summernote({
        codeviewFilter:false,
        codeviewframeFilter:true,
        height: 500,
        lang: "ko-KR",
        callbacks:{
            onImageUpload:function(files){
                for(var i=0; i<files.length; i++){
                    if(i>20){alert('20개까지만 등록할 수 있습니다.'); return;}
                }
                for(var i=0; i<files.length; i++){
                    if(i>20){alert('20개까지만 등록할 수 있습니다.'); return;}
                    sendFile($summernote, files[i]);
                }
            }
        }
    });
});
function sendFile($summernote, file) {

var formData = new FormData();
formData.append("file", file);
$.ajax({
    url: '/kko/ax.php',
    data: formData,
    cache: false,
    contentType: false,
    processData: false,
    type: 'POST',
    success: function (data) {
        if(data==-1){alert('용량이 너무크거나 이미지 파일이 아닙니다.');return;}
        else
        {
            $summernote.summernote('insertImage', data, function ($image) {
                $image.attr('src', data);
                $image.attr('class', 'childImg');
            });
            var imgUrl=$("#imgUrl").val();
            if(imgUrl){
                imgUrl=imgUrl+",";
            }
            $("#imgUrl").val(imgUrl+data);
        }
    }
});
}
</script>
</body>
</html>