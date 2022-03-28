<?
#CASTLE
define("__CASTLE_PHP_VERSION_BASE_DIR__", "/var/www/html/_CTL_TK~");
include_once(__CASTLE_PHP_VERSION_BASE_DIR__ . "/castle_referee.php");

#DB_SLAVER
list($dbHost,$dbName,$dbUser,$dbPass)=explode("|",file_get_contents("/cfg/dbTank.cfg"));
$dbPass=trim($dbPass);
try
{
	$pdo=new PDO("mysql:host={$dbHost};port=3307;dbname={$dbName};charset=utf8",$dbUser,$dbPass);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, FALSE);
	//$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
	$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_BOTH);
	$pdo->setAttribute(PDO::MYSQL_ATTR_FOUND_ROWS, TRUE);
}
catch(Exception $e)
{
	//echo "DB-연결 실패";
	//die("오류 : ".$e->getMessage());
	//exit;
}
$my_db="db_main";
?>