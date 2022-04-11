<?php 

print_r($_FILES);
// print_r($uploadfile = $uploaddir.basename($_FILES['imgFile']['name']));
echo "<hr>";
$tempFile = $_FILES['imgFile']['tmp_name'];
$fileTypeExt = explode("/", $_FILES['imgFile']['type']);
$fileType = $fileTypeExt[0];
$fileExt = $fileTypeExt[1];

$extStatus = false;

switch($fileExt){
	case 'jpeg':
	case 'jpg':
	case 'gif':
	case 'bmp':
	case 'png':
		$extStatus = true;
		break;
	
	default:
		echo "이미지 전용 확장자(jpg, bmp, gif, png)외에는 사용이 불가합니다."; 
		exit;
		break;
}

if($fileType == 'image'){
	if($extStatus){
        $oldmask=umask(0);
        $dir = $_SERVER["DOCUMENT_ROOT"]."member/img";
        if(is_dir($dir)==false)
        {
            
            mkdir($dir,0777,true);
        }
        umask($oldmask);
        if(is_writable($dir)){echo "쓰기 가능 <br>";}
        else{echo "쓰기 불가능 <br>";}

		$resFile = "{$dir}/{$_FILES['imgFile']['name']}";
		$imageUpload = move_uploaded_file($tempFile, $resFile);
		if($imageUpload == true){
			echo "파일이 정상적으로 업로드 되었습니다. <br>";
			echo "<img src='{$resFile}' width='100' />";
		}else{
			echo "파일 업로드에 실패하였습니다.";
		}
	}	
	else {
		echo "파일 확장자는 jpg, bmp, gif, png 이어야 합니다.";
		exit;
	}	
}
else {
	echo "이미지 파일이 아닙니다.";
	exit;
}



?><?php 

print_r($_FILES);
// print_r($uploadfile = $uploaddir.basename($_FILES['imgFile']['name']));
echo "<hr>";
$tempFile = $_FILES['imgFile']['tmp_name'];
$fileTypeExt = explode("/", $_FILES['imgFile']['type']);
$fileType = $fileTypeExt[0];
$fileExt = $fileTypeExt[1];

$extStatus = false;

switch($fileExt){
	case 'jpeg':
	case 'jpg':
	case 'gif':
	case 'bmp':
	case 'png':
		$extStatus = true;
		break;
	
	default:
		echo "이미지 전용 확장자(jpg, bmp, gif, png)외에는 사용이 불가합니다."; 
		exit;
		break;
}

if($fileType == 'image'){
	if($extStatus){
        $oldmask=umask(0);
        $dir = $_SERVER["DOCUMENT_ROOT"]."member/img";
        if(is_dir($dir)==false)
        {
            
            mkdir($dir,0777,true);
        }
        umask($oldmask);
        if(is_writable($dir)){echo "쓰기 가능 <br>";}
        else{echo "쓰기 불가능 <br>";}

		$resFile = "{$dir}/{$_FILES['imgFile']['name']}";
		$imageUpload = move_uploaded_file($tempFile, $resFile);
		if($imageUpload == true){
			echo "파일이 정상적으로 업로드 되었습니다. <br>";
			echo "<img src='{$resFile}' width='100' />";
		}else{
			echo "파일 업로드에 실패하였습니다.";
		}
	}	
	else {
		echo "파일 확장자는 jpg, bmp, gif, png 이어야 합니다.";
		exit;
	}	
}
else {
	echo "이미지 파일이 아닙니다.";
	exit;
}



?>