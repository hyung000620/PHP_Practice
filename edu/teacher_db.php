<?
$debug=false;
$page_code=1462;
include $_SERVER["DOCUMENT_ROOT"]."/SuperAdmin/common/db_header.php";

set_time_limit(300);
$path=$_SERVER["DOCUMENT_ROOT"]."/lecture/teacher/photo/";
$allow_ext=array("jpg","gif","png","pdf","zip");
$max_size=1024*1024*50;

switch($mode)
{
	case "new" :
		$SQL ="INSERT INTO {$my_db}.tl_teacher(nickname,id,teacher_id,name,content,photo_s,photo_b,dp_off,wdate) ";
		$SQL.="VALUES('{$teacher_nickname}','{$user_id}','{$teacher_id}','{$teacher_name}','{$teacher_content}','{$photo_s}','{$photo_b}','{$dp_off}',NOW())";
		$stmt=$pdo->prepare($SQL);
		$stmt->execute();
		
		$stmt=$pdo->prepare("SELECT LAST_INSERT_ID()");
		$stmt->execute();
		$rs=$stmt->fetch();
		$idx=$rs[0];
		break;
		
	case "edit" :
		$stmt=$pdo->prepare("SELECT * FROM {$my_db}.tl_teacher WHERE idx='{$idx}' LIMIT 0,1");
		$stmt->execute();
		$rs=$stmt->fetch();
		if($chk_photo_b)
		{
			$file_del=", photo_b=''";
			File_Delete("{$rs[photo_b]}",0);
		}
		if($chk_photo_s)
		{
			$file_del.=", photo_s=''";
			File_Delete("{$rs[photo_s]}",0);
		}

			$SQL ="UPDATE {$my_db}.tl_teacher SET id='{$user_id}',nickname='{$teacher_nickname}',teacher_id='{$teacher_id}', name='{$teacher_name}', content='{$teacher_content}',dp_off='{$dp_off}' WHERE idx='{$idx}'";
			$stmt=$pdo->prepare($SQL);
			$stmt->execute();
		break;
}


$fileArr=array("photo_s","photo_b");
File_Upload($fileArr);

function File_Upload($fileArr)
{
	global $pdo,$path,$allow_ext,$max_size,$my_db,$idx,$_FILES;
	
	foreach($fileArr as $file)
	{
		$file_size=$_FILES[$file][size];
		$real_name=$_FILES[$file][name];
		$temp_name=$_FILES[$file][tmp_name];
		if(!$file_size) continue;
		if($file_size > $max_size)	MessageBox("파일크기가 허용치(5M)를 초과하였습니다.!");
	
		$name_ext=explode(".",$real_name);
		$ext=strtolower($name_ext[1]);	
		$file_name=strtolower("{$idx}_{$file}.{$ext}");

		if(!in_array($ext,$allow_ext))	MessageBox("허용되지 않는 파일 형식 입니다.!");
        if(!move_uploaded_file($temp_name, $path.$file_name))  MessageBox("파일이 정상적으로 저장이 안됬습니다!");
		
		
		$upfileArr[$file]=$file_name;
	}
	if($upfileArr)
	{
		foreach($upfileArr as $key => $val)
		{
			$condiArr[]="{$key}='{$val}'";
		}
		$condition=implode("," , $condiArr);
		$SQL="UPDATE {$my_db}.tl_teacher SET {$condition} WHERE idx='{$idx}'";
		$stmt=$pdo->prepare($SQL);
		$stmt->execute();
	}
}

function File_Delete($file_name,$key)
{
	global $path;

		if(file_exists($path.$file_name))
		{
			if(!unlink($path.$file_name)) MessageBox("파일이 삭제가 되지 않았습니다.-{$file_name}");
		}
}
function MessageBox($msg)
{
	echo "
	<script type='text/javascript'>
		alert('{$msg}');
		history.back();
	</script>";
	exit;
}

header("Location:teacher_list.php");
?>