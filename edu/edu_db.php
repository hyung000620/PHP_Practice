<?
$debug=false;
$page_code=1463;
include $_SERVER["DOCUMENT_ROOT"]."/SuperAdmin/common/db_header.php";

set_time_limit(300);	
$path=$_SERVER["DOCUMENT_ROOT"]."/lecture/off_line/photo/";
$allow_ext=array("jpg","gif","png","pdf","zip");
$max_size=1024*1024*50;	

$link=str_replace("/board/staff/","/board/",$link);
$edu_zone="0";
switch($mode)
{
	case "new" :
    {
		$SQL ="INSERT INTO {$my_db}.tl_edu(edu_title,edu_teacher,edu_zone,edu_addr,edu_area,link,open_date,edu_content,sdate,edate,edu_time,edu_phone,edu_pay,edu_people,dp_off,on_off,wdate) ";
		$SQL.="VALUES('{$edu_title}','{$edu_teacher}','{$edu_zone}','{$edu_addr}','{$edu_area}','{$link}','{$open_date}','{$edu_content}','{$sdate}','{$edate}','{$edu_time}','{$edu_phone}','{$edu_pay}','{$edu_people}','{$dp_off}','{$on_off}',NOW())";
		$stmt=$pdo->prepare($SQL);
		$stmt->execute();
		
		$stmt=$pdo->prepare("SELECT LAST_INSERT_ID()");
		$stmt->execute();
		$rs=$stmt->fetch();
		$idx=$rs[0];
    }break;
		
	case "edit" :
    {
        $stmt=$pdo->prepare("SELECT * FROM {$my_db}.tl_edu WHERE idx='{$idx}' LIMIT 0,1");
        $stmt->execute();
        $rs=$stmt->fetch();
        if($chk_photo_teacher)
        {
            $file_del=", photo_teacher=''";
            File_Delete("{$rs[photo_teacher]}",0);
        }
        if($chk_photo_edu)
        {
            $file_del.=", photo_edu=''";
            File_Delete("{$rs[photo_edu]}",0);
        }

        if($chk_photo_screen)
        {
            $file_del.=", photo_screen=''";
            File_Delete("{$rs[photo_screen]}",0);
        }
        if($chk_photo_main)
        {
            $file_del.=", photo_main=''";
            File_Delete("{$rs[photo_main]}",0);
        }
        $SQL ="UPDATE {$my_db}.tl_edu SET edu_title='{$edu_title}',edu_teacher='{$edu_teacher}',edu_zone='{$edu_zone}',edu_addr='{$edu_addr}',edu_area='{$edu_area}',link='{$link}',open_date='{$open_date}',edu_content='{$edu_content}',";
        $SQL.="sdate='{$sdate}',edate='{$edate}',edu_time='{$edu_time}',edu_phone='{$edu_phone}',edu_pay='{$edu_pay}',edu_people='{$edu_people}',dp_off='{$dp_off}',on_off='{$on_off}'  {$file_del} WHERE idx='{$idx}'";
        $stmt=$pdo->prepare($SQL);
        $stmt->execute();
    }break;
    case "yoilChk" :
    {

    }break;

}


$fileArr=array("photo_teacher","photo_edu","photo_screen","photo_main");
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
		if($file_size > $max_size)	MessageBox("����ũ�Ⱑ ���ġ�� �ʰ��Ͽ����ϴ�!");
	
		$name_ext=explode(".",$real_name);
		$ext=strtolower($name_ext[1]);
		$file_name=strtolower("{$idx}_{$file}.{$ext}");

		if(!in_array($ext,$allow_ext))	MessageBox("������ �ʴ� �������� �Դϴ�!");
		if(!move_uploaded_file($temp_name,$path.$file_name))	MessageBox("���Ͼ��ε尡 ����� ���� �ʾҽ��ϴ�!");
		
		
		$upfileArr[$file]=$file_name;
	}
	if($upfileArr)
	{
		foreach($upfileArr as $key => $val)
		{
			$condiArr[]="{$key}='{$val}'";
		}
		$condition=implode("," , $condiArr);
		$SQL="UPDATE {$my_db}.tl_edu SET {$condition} WHERE idx='{$idx}'";
		//sql_query($SQL);
		$stmt=$pdo->prepare($SQL);
		$stmt->execute();
	}
}

function File_Delete($file_name,$key)
{
	global $path;

		if(file_exists($path.$file_name))
		{
			if(!unlink($path.$file_name)) MessageBox("���ϻ��� ����-{$file_name}");
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

header("Location:edu_list.php");

?>