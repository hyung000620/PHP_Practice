<?
$page_code=9010;
include($_SERVER["DOCUMENT_ROOT"]."/lec/inc/header.php");
//동영상 강좌 구분
$stmt=$pdo->prepare("SELECT lec_code,course FROM {$my_db}.te_lecture WHERE ctgr BETWEEN 20 AND 33");
$stmt->execute();
while($rs=$stmt->fetch())
{
	$lect_arr[$rs[lec_code]]=$rs[course];
}

//2008-05월 이전 데이터 구조변경으로 표현 안함
$condition="P.order_no=L.order_no AND P.id='{$client_id}'";
$SQL ="(SELECT P.order_no,P.idx,P.id,pay_code,state,sector,months,money,paykind,paydate,validity,point,pay_price,bankcode,payname,startdate,vp_sdate,vp_edate,sp_sdate,sp_edate,memo,1 AS tbl_key FROM {$my_db}.tm_pay_result  P , {$my_db}.tm_pay_list L WHERE {$condition}) ";
$SQL.="UNION ALL ";
$SQL.="(SELECT P.order_no,P.idx,P.id,pay_code,state,sector,months,money,paykind,paydate,validity,point,pay_price,bankcode,payname,startdate,vp_sdate,vp_edate,sp_sdate,sp_edate,memo,2 AS tbl_key FROM {$my_db}.tm_pay_history P , {$my_db}.tm_pay_list L WHERE {$condition}) ";
$SQL.="ORDER BY order_no DESC";

//echo $SQL;

$stmt=$pdo->prepare($SQL);
$stmt->execute();
while($rs=$stmt->fetch())
{
	if($rs[paykind]==2)		$PKIND[]=$bank_arr[$rs[bankcode]][name]."-".$rs[payname];
	elseif($rs[paykind]==1)	$PKIND[]="카드";
	elseif($rs[paykind]==3)	$PKIND[]="실시간이체";
	elseif($rs[paykind]==4)	$PKIND[]="계좌이체";
	else					$PKIND[]=$pay_kind_arr[$rs[paykind]];
	
	$special_s=""; $special_e="";
	if($rs[vp_edate] >= date('Y-m-d'))
	{
		$special_s="<br><span class='orange f11'>({$rs[vp_sdate]})</span>";
		$special_e="<br><span class='orange f11'>({$rs[vp_edate]})</span>";
	}
	elseif($rs[sp_edate] >= date('Y-m-d'))
	{
		$special_s="<br><span class='blue f11'>({$rs[sp_sdate]})</span>";
		$special_e="<br><span class='blue f11'>({$rs[sp_edate]})</span>";
	}
	
	$TBL[]=$rs[tbl_key];			//table 구분키(tm_pay_result,tm_pay_history)
	$IDX[]=$rs[idx];				//일련번호
	$ID[]=$rs[id];					//회원아이디
	$STATE[]=$rs[state];			//결제지역
	$SECTOR[]=$rs[sector];			//가맹지역
	$MONTH[]=$rs[months];			//신청기간
	$PDATE[]=$rs[paydate];			//결제일
	$START[]=$rs[startdate];		//서비스 시작일
	$EXPIRE[]=$rs[validity];		//만료일
	$MONEY[]=$rs[money];			//이용료
	$POINT[]=$rs[point];			//사용 포인트
	$PTIME[]=$rs[paytime];			//결제시간
	$PAY_CODE[]=$rs[pay_code];		//결제 타입 ()
	$REC_ID[]=$rs[rec_id];			//추천인ID
	$MEMO[]=$rs[memo];				//결제 메모
	$ORDER_NO[]=$rs[order_no];		//주문번호
	$PMONEY[]=$rs[pay_price];		//결제금액
	$SPECIAL_S[]=$special_s;
	$SPECIAL_E[]=$special_e;
}
?>
<div class='wrap'>
	<div class='li_teb'>
		<ul>
			<li class='on'><a href='/lec/member/mov_mypage.php'>동영상강좌</a></li>
			<li onclick="location.href='/lec/member/edu_mypage.php'"><a href='/lec/member/edu_mypage.php'>탱크교육</a></li>
		</ul>
	</div>
</div>
<?
include($_SERVER["DOCUMENT_ROOT"]."/lec/inc/footer.php");
?>