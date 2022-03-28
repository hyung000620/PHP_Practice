<?
    header("Content-type: application/json");
    include($_SERVER["DOCUMENT_ROOT"]."/kko/inc/header.php");

    $SQL="SELECT * FROM {$my_db}.tx_cd_adrs WHERE 1";
    $stmt=$pdo->prepare($SQL);
    $stmt->execute();
    
    $result=array();
    while($rs=$stmt->fetch())
    {
        $result[$rs['idx']]=array("si_nm"=>$rs['si_nm'],"gu_nm"=>$rs['gu_nm'],"dn_nm"=>$rs['dn_nm'],"x"=>$rs['x'],"y"=>$rs['y']);
    }
    echo json_encode(array_values($result),JSON_UNESCAPED_UNICODE);

?>