<?
function func_single_upload($f_tmp, $f_name, $f_size, $f_type, $dirPath, $forbidden_array=array(), $thum_width, $thum_height)
{
  #파일명체크
  if(!$f_name)   {alertBack("파일이 없습니다.");};
  #파일사이즈체크
  if($f_size==0) {alertBack("파일용량을 확인해주세요.");}
  #확장자체크
  $fexp=explode(".",$f_name);
  $real_filename=$fexp[0];
  $file_name=md5(uniqid());
  $fileName=$file_name.".".$fexp[1];
  $extension=$fexp[1];
  if (in_array($extension, $forbidden_array)){alertBack("업로드 불가능한 확장자입니다.");}
  #디렉토리생성    
  
  if (!is_dir($dirPath)){mkdir($dirPath,0777);  chmod($dirPath,0777);}
  #파일 저장
  $dest=$dirPath.$fileName;
  if(!move_uploaded_file($f_tmp, $dest)){alertBack("파일을 이동하는도중 에러가 발생했습니다.\\n관리자에게 문의하여 주십시오.");}
  
  #썸네일 생성
  $jgp=array("jpg","jpeg","gif","png");
	if(in_array($extension,$jgp))
	{ 
    if($thum_width>10 && $thum_height>10)
  	{
  		$sPath=$dirPath."thumnail/";
  		if(!is_dir($sPath)){mkdir($sPath,0777); chmod($sPath,0777);}		
  		$result=thumnail($dest, $fileName, $sPath, $thum_width, $thum_height);	
    }
  }
	return $fileName;	
}
##썸네일작업
function thumnail($file, $save_filename, $save_path, $max_width, $max_height) 
{ 
  #GD 버젼체크 
	$gd = gd_info(); 
	$gdver = substr(preg_replace("/[^0-9]/", "", $gd['GD Version']), 0, 1); 
	if(!$gdver){alertBack("GD 버젼체크 실패거나 GD 버젼이 1 미만입니다.");}
  #전송받은 이미지 정보(1:GIF,2:JPEG,3:PNG)
  $img_info = getImageSize($file);
  if($img_info[2] == 1){$src_img = ImageCreateFromGif($file);}
  else if($img_info[2] == 2){$src_img = ImageCreateFromJPEG($file);}
  else if($img_info[2] == 3){$src_img = ImageCreateFromPNG($file);}
  else{return 0;}  
  #전송받은 이미지의 실제 사이즈
  $img_width = $img_info[0]; 
  $img_height = $img_info[1]; 
  if($img_width <= $max_width){$max_width = $img_width; $max_height = $img_height;} 
  if($img_width > $max_width) {$max_height = ceil(($max_width / $img_width) * $img_height);} 
  #새로운 트루타입 이미지를 생성 
  $dst_img = imagecreatetruecolor($max_width, $max_height); 
  #R255, G255, B255 값의 색상 인덱스를 만든다 
  ImageColorAllocate($dst_img, 255, 255, 255); 
  #이미지를 비율별로 만든후 새로운 이미지 생성 
  ImageCopyResampled($dst_img, $src_img, 0, 0, 0, 0, $max_width, $max_height, ImageSX($src_img),ImageSY($src_img)); 
  #알맞는 포맷으로 저장 (1:GIF,2:JPEG,3:PNG)
  if($img_info[2] == 1)     {ImageInterlace($dst_img); ImageGif($dst_img, $save_path. $save_filename);}
  else if($img_info[2] == 2){ImageInterlace($dst_img); ImageJPEG($dst_img, $save_path. $save_filename);}
  else if($img_info[2] == 3){ImagePNG($dst_img, $save_path. $save_filename);} 
  
   // 임시 이미지 삭제 
   ImageDestroy($dst_img); 
   ImageDestroy($src_img); 
} 