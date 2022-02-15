<?
#DB_SLAVER
list($dbHost,$dbName,$dbUser,$dbPass)=explode("|",file_get_contents("/volume1/web_packages/cfg/big.cfg"));

$dbPass = trim($dbPass);
$dbHost = "192.168.10.200";

try
{
    /*
        setAttribute(설정할 속성, 설정 값);
       
        - PDO::ATTR_ERRMODE : 이 속성은 PDO 객체가 에러를 처리하능 방식. 이 방식을 예외처리를 던지는 방식으로 설정합니다.
        
        - PDO::ATTR_EMULATE_PREPARES : 이 속성은 Preppared Statement 를 데이터베이스가 지원하지 않은 경우 에뮬레이션
        기능으로 false 를 지정해서 데이터베이스의 기능을 사용하도록 한다.
       
        - PDO::ATTR_DEFAULT_FETCH_MODE : 이 속성은 가져올 값에 대한 설정을 디폴트로 설정을 한다는 것이고. 
          - PDO::FETCH_NUM : 숫자 인덱스 배열 반환
          - PDO::FETCH_ASSOC : 컬럼명이 키인 연관배열 반환
          - PDO::FETCH_BOTH : 위 두가지 모두
          - PDO::FETCH_OBJ : 컬럼명이 프로퍼티인 인명 객체 반환
            
    */
    $pdo=new PDO("mysql:host={$dbHost};port=3307;dbname={$dbName};charset=utf8",$dbUser,$dbPass);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	//$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
	$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_BOTH);
}
catch(Exception $e)
{
    //echo "DB-연결 실패";
	// if($debug==1)
    // {
    //     die("오류 : ".$e->getMessage());
    // }
	// exit;
}
$my_db="db_big"; // db가 여러개일 경우 관리하기 편하도록 설정.
?>