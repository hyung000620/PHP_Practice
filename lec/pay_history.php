<?
$page_code=9020;
$new_page_code=9020;
$member_only=true;
$cpn_deny=true;
include($_SERVER["DOCUMENT_ROOT"]."/lec/inc/header.php");

//경매정보
$stmt=$pdo->prepare("SELECT state,area FROM {$my_db}.tc_price");
$stmt->execute();
while($rs=$stmt->fetch())
{
	$auctArr[$rs[state]]=$rs[area];
}

//동영상 강좌 구분
$stmt=$pdo->prepare("SELECT lec_code,course FROM {$my_db}.te_lecture WHERE ctgr BETWEEN 20 AND 33");
$stmt->execute();
while($rs=$stmt->fetch())
{
	$lect_arr[$rs[lec_code]]=$rs[course];
}
//경매교육 구분
$stmt=$pdo->prepare("SELECT edu_code,edu_title FROM {$my_db}.tl_edu");
$stmt->execute();
while($rs=$stmt->fetch())
{
	$edu_arr[$rs[edu_code]]=$rs[edu_title];
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
<style>
.pay_history_title{padding:0px 0px 20px 0}

.new_tbl {  display:block;text-align:left;width:100%;border-collapse:collapse;border-spacing:0;border:1px solid #ccc;border-bottom: none;}
.new_tbl col { }
.new_tbl thead { float: left; }
.new_tbl thead tr {}
.new_tbl thead tr th {border-right: 1px solid #ccc;  background-color: #eee;}
.new_tbl tbody {display: block;  overflow-x: auto;  white-space: nowrap;  font-size: 0;  -webkit-text-size-adjust: none;}
.new_tbl tbody tr {display: inline-block;}
.new_tbl th, .new_tbl td {display: block; height:40px;padding: 5px 16px;  border: none;  border-bottom: 1px solid #ccc; border-right:1px solid #ccc;  font-size: 13px;text-overflow:ellipsis; }
.new_tbl td {width:160px}

@media screen and (min-width: 700px) {
	.pay_history_title{padding:20px 0 20px 60px}
	
	.new_tbl {display: table;max-width: 1000px;margin: 0 auto;}
	.new_tbl col {width: 30%; }
	.new_tbl thead {display: table-header-group;float: none;}
	.new_tbl thead tr {display: table-row; }
	.new_tbl thead tr th {border-right: none; }
	.new_tbl tbody {display: table-row-group;white-space: initial;;  }
	.new_tbl tbody tr {display: table-row; }
	.new_tbl th, 	.new_tbl td { display: table-cell;text-align:center }
}

</style>
<div class="wrap" style="padding:20px 0">
	<div class="pay_history_title">
		<div class="f16 bold lh20">· 회원ID: <?=$client_id?></div>
		<div class="f16 bold lh20">· 회원명: <?=$client_name?></div>
		<div class='gray f14' style='padding-top:8px'>
			- 회원가입 / 회원정보수정 / 회원탈퇴는 탱크옥션(경매)의 <span class='blue bold'>통합 마이페이지</span>로 이동 하시어 이용 하시면 됩니다.&nbsp;
			<a href="https://www.tankauction.com/member/mypage.php"><span class='btn_box_ssss btn_tank_1' style="width:130px">마이페이지 바로가기</span></a>
		</div>
	</div>
	<table class="new_tbl" style='border-top:1px solid #000'>
		  <colgroup>
		    <col>
		  </colgroup>
		<thead>
			<tr>
				<th>결제지역</th>
				<th>기간(월/일)</th>
				<th>이용료</th>
				<th>결제금액</th>
				<th>결제일</th>
				<th>시작일<br>만료일</th>
				<th>잔여일</th>
			</tr>
		</thead>
		<tbody>

	<?
	
	$today=date("Y-m-d");
	if($ORDER_NO)
	{
		$RowNo=count($ORDER_NO);
		$rspanArr=array_count_values($ORDER_NO);
		foreach($ORDER_NO as $key => $val)
		{
			$rspan=($rspanArr[$val] > 1) ? $rspanArr[$val] : "";
			$remain_day="";
			$bgcolor="";
			if($EXPIRE[$key] >= $today)
			{
				$expire_date=explode("-",$EXPIRE[$key]);
				$expire_time=mktime(0,0,0,$expire_date[1],$expire_date[2],$expire_date[0]);
				$remain_day=floor(($expire_time - time() + 86400) / 86400);
				$remain_day="<span class='blue bold'>{$remain_day}</span>";
				$bgcolor="#eff9fb";
			}		
			
			$sector=""; $sectorArr="";
			if($PAY_CODE[$key]==100) 		$sector=$auctArr[$STATE[$key]];
			//elseif($PAY_CODE[$key]==101)	$sector="동영상강좌";
			
			elseif($PAY_CODE[$key]==101)	$sector="<span class='red'>[동영상강좌] </span>".$lect_arr[$SECTOR[$key]];
			elseif($PAY_CODE[$key]==102)    $sector="<span class='red'>[경매교육] </span>".$edu_arr[$SECTOR[$key]];
			$start=str_replace("-",".",$START[$key]);
			$expire=str_replace("-",".",$EXPIRE[$key]);
			$month=$MONTH[$key];
			echo "
			<tr>
				<!--<td class='no center'>{$RowNo}</td>-->
				<td class='center'><div style='white-space: initial;'>{$sector}</div></td>
				<td class='no center'>{$month}</td>
				<td class='no center'>".number_format($MONEY[$key])."</td>";
			if($prev != $val)
			{
				echo "
				<td rowspan='{$rspan}' class='money bold green center'>".number_format($PMONEY[$key])."</td>
				<td rowspan='{$rspan}' class='no center'>".str_replace("-",".",$PDATE[$key])."</td>";
			}
			echo "
				<td class='no center'>{$start}<br>{$expire}</td>
				
				<td class='money center'>{$remain_day}</td>
			</tr>";
			$prev=$val;
			$RowNo--;
		}
	}

	?>
		</tbody>
	</table>
</div>
<?
if(!$ORDER_NO) echo "<div class='no_result'><span>결제 내역이 없습니다.</span></div>";
?>
<div class="center" style="padding:50px"><a href="/member/Toss_pay_lec.php"><span class="btn_box_ss btn_tank radius_10">결제하기</span></a></div>
<?
include($_SERVER["DOCUMENT_ROOT"]."/lec/inc/footer.php");
?>
