<?
//10메가
if($_FILES['file']['size']>10240000){
    echo "-1";
    exit;
}

$ext = substr(strrchr($_FILES['file']['name'],"."),1);
$ext = strtolower($ext);
if ($ext != "jpg" and $ext != "png" and $ext != "jpeg" and $ext != "gif")
{
    echo "-1";
    exit;
}

$name = "mp_".$now3.substr(rand(),0,4);
$filename = $name.'.'.$ext;
$destination = '/var/www/public_html/board/upImages/'.$filename;
$location =  $_FILES["file"]["tmp_name"];
move_uploaded_file($location,$destination);

echo '/kko/img/'.$filename;


?>