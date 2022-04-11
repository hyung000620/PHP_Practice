<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
    <form name="reqform" method="post" action="_test_ok.php" enctype="multipart/form-data">
        <input type='file' name="imgFile" onchange="readURL(this);" />
        <input type="submit" value="Submit">
    </form>
    <br>
    <!-- 미리보기 적용 -->
    <div style='width:300px; heigth:300px; border-radius:70%; overflow:hidden;'>
        <img id="blah" src="https://mblogthumb-phinf.pstatic.net/20150427_261/ninevincent_1430122791768m7oO1_JPEG/kakao_1.jpg?type=w2" style='width:100%; height:100%; object-fit:cover' />
    </div>
</body>
<script type="text/javascript" src="/js/jquery-3.5.1.min.js"></script>
<script type='text/javascript'>

//이미지 미리보기
function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#blah').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
</html>