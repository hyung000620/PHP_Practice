<?
include $_SERVER["DOCUMENT_ROOT"]."/practice/inc/xmlHeader.php";
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
	<result>";
	
$siCd_code=(int)$siCd_code;
$guCd_code=(int)$guCd_code;
$dnCd_code=(int)$dnCd_code;
switch ($queryType)
{
	//주소 3단계 select
	case "addr" :
	 {
		if(!$siCd_code)
		{
			$SQL="SELECT si_cd AS addr_code, si_nm AS addr_name FROM db_main.tx_cd_adrs WHERE hide=0 GROUP BY si_cd ORDER BY si_cd";
			$obj_id="siCd";
		}
		if($siCd_code && !$guCd_code)
		{
			$SQL="SELECT gu_cd AS addr_code, gu_nm AS addr_name FROM db_main.tx_cd_adrs WHERE hide=0 AND si_cd='{$siCd_code}' AND gu_cd > 0 GROUP BY gu_cd ORDER BY gu_nm";
			$obj_id="guCd";
		}
		if($siCd_code && $guCd_code && !$dnCd_code)
		{
			$SQL="SELECT dn_cd as addr_code, dn_nm as addr_name FROM db_main.tx_cd_adrs WHERE hide=0 AND si_cd='{$siCd_code}' AND gu_cd='{$guCd_code}' AND dn_cd > 0 GROUP BY dn_cd ORDER BY dn_nm";
			$obj_id="dnCd";
		}

		$stmt=$pdo->prepare($SQL);
		$stmt->execute();
		echo "
		<code>success</code>
		<obj_id>{$obj_id}</obj_id>
		<addr_step>{$addr_step}</addr_step>";
		while($rs=$stmt->fetch())
		{
			echo "
			<item>
				<addr_code><![CDATA[{$rs[addr_code]}]]></addr_code>
				<addr_name><![CDATA[{$rs[addr_name]}]]></addr_name>
			</item>";
		}
	} break;  
}
echo "
</result>";
?>