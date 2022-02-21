<?
#DB_SLAVER
list($dbHost,$dbName,$dbUser,$dbPass)=explode("|",file_get_contents("/volume1/web_packages/cfg/big.cfg"));
$dbPass=trim($dbPass);

$dbHost="192.168.10.200";
try
{
	$pdo=new PDO("mysql:host={$dbHost};port=3307;dbname={$dbName};charset=utf8",$dbUser,$dbPass);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_BOTH);
}
catch(Exception $e)
{
	echo "DB-연결 실패";
}
$my_db="db_big";
?>