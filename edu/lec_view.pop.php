<?
$debug=false;
$snb=false;
$ctgr=(!$_GET[ctgr]) ? 11 : $_GET[ctgr];
$ctgr=(int)$ctgr;
$page_code="1650";
$cg_code=($cg_code)? $cg_code : "103";
$lec_list_arr_m=array();
include($_SERVER["DOCUMENT_ROOT"]."/lec/common/header_lec.php");

//볼 수 있는 강좌체크
$stmt=$pdo->prepare("SELECT lec_code,ctgr FROM {$my_db}.te_lecture WHERE hide=0");
$stmt->execute();
while($row=$stmt->fetch())
{
	$lectArr[$row['lec_code']]=$row['ctgr'];	
}

//관리자 체크
$isAdmin="N";
$stmt=$pdo->prepare("SELECT idx FROM {$my_db}.tz_staff WHERE id='{$client_id}' AND level > 0 AND resign_dt='0000-00-00' LIMIT 1");
$stmt->execute();
$ars=$stmt->fetch();
if($ars) $isAdmin="Y";
$idx=(int)$idx;

//강좌 정보
$stmt=$pdo->prepare("SELECT * FROM {$my_db}.te_lecture WHERE idx='{$idx}' LIMIT 0,1");
$stmt->execute();
$rs=$stmt->fetch();
$lec_code=$rs[lec_code];

//인증 정보
$myLecArr=[];
$_validity=false;
$stmt=$pdo->prepare("SELECT * FROM {$my_db}.tm_pay_result P , {$my_db}.tm_pay_list L WHERE P.order_no=L.order_no AND P.id='{$client_id}' AND validity >= CURDATE()");
$stmt->execute();
while($row=$stmt->fetch())
{
	//$_sector=$rs[sector];//강좌코드
	//$_validity=$rs[validity];//사용기간
	//ct=10 -> 경매유료회원, ct=11 -> 연간회원
	if($row['pay_code']==100)
	{
		$memo=str_replace(" ", "", $row['memo']);
		$forceAllow=strpos($memo,"연간동영상");
		if($row['paykind'] == 9)
		{
			if($forceAllow!==false)
		 	{
		 		foreach($lectArr as $cd => $ct)
				{
					if($ct==10 || $ct==11)
					{
						$myLecArr[]=$cd;
						if($lec_code==$cd) $_validity=$row['validity'];
					}
				}
		 	}
		}
		else 
		{
		 	if($row['pay_price'] >= 500000 || $forceAllow!==false)
		 	{
		 		foreach($lectArr as $cd => $ct)
				{
					if($ct==10 || $ct==11)
					{
						$myLecArr[]=$cd;
						if($lec_code==$cd) $_validity=$row['validity'];
					}
				}
		 	}
		 	else 
		 	{
		 	 	foreach($lectArr as $cd => $ct)
				{
					if($ct==10)
					{
						$myLecArr[]=$cd;
						if($lec_code==$cd) $_validity=$row['validity'];
					}
				}
		 	}
		}
	}
	elseif($row['pay_code']==101)
	{
	 	foreach($lectArr as $cd => $ct)
		{
			if($cd==$row['sector'])
			{
				$myLecArr[]=$cd;
				if($lec_code==$cd) $_validity=$row['validity'];
			}
		}
	}
}

$price=($rs[price]!=0) ? $rs[price]."원" : "무료";
$price_1=($rs[price]!=0) ? "￦ ".$rs[price] : "무료";

$_user = $client_id;
$ctgr=$rs[ctgr];
$book=($rs[book]) ? $rs[book] : "해당교재 없음";
$sort_num=$rs[sort_num];

if($rs[badge] != "0" ){$badge_ment="<span class='span_block title_ment_{$rs[badge]} white' style='font-size:50%;'>{$lec_badge_arr[$rs[badge]]}</span>";}
else{$badge_ment="";}

$viewAllow=false;
if(in_array($lec_code,$myLecArr) || $isAdmin=="Y") $viewAllow=true;
?>
<link rel="stylesheet" type="text/css" href="/css/btn_box.css">
<style>
	.mov_teb li{float:left;width:20%;text-align:center;font-size:14px;background:#f0f0f0}
	.mov_teb li div{padding:15px 0;border-right:1px solid #ddd;border-top:1px solid #ddd;cursor:pointer}
	.mov_teb li:first-child div{border-left:1px solid #ddd;}
	.mov_teb li div:hover{background:#599BD8;color:#fff}
	.no_img{background:#eee;text-align:center;width:100%;}
	.fw_900{font-weight:900}
	.lec_view_pop .view_btn{display:block;padding:0 10px;width:100px}
	.fm_talk{padding:20px}
	.fm_talk .fm_talk_box{padding:1% 2%;margin:5px;border:1px solid #999;background:#f8f8f8}
	.fm_talk .fm_talk_box .talk_title{float:left;width:14%;text-align:center;padding-top:15px;display:block}
	.fm_talk .fm_talk_box .talk_text{float:left;width:70%}
	.fm_talk .fm_talk_box .talk_btn{float:left;width:16%;text-align:center;padding-top:0px;}
	.fm_talk .fm_talk_box .talk_btn .btn_box_s{height:57px;padding-top:30px}
	.view_pop_content{float:left;width:75%}
	.view_pop_rigth{display:block;float:right;width:25%;}
	#view_pop_rigth{float:left;}
	.view_pop_center{display:none;}
@media all and (max-width: 1100px)
{ 
	.view_pop_content{width:100%}
	.view_pop_rigth{display:none}
	.view_pop_center{display:block;padding:10px;text-align:center}
}
@media all and (max-width: 670px)
{ 
	.lec_view_pop .view_btn{display:none}
	.fm_talk{padding:20px 5px}
	.fm_talk .fm_talk_box .talk_title{display:none}
	.fm_talk .fm_talk_box .talk_text{float:left;width:78%}
	.fm_talk .fm_talk_box .talk_btn .btn_box_s{width:58px;font-size:14px;padding-top:30px;margin-left:10px}
}
</style>

<div class='wrap lec_view_pop' style='margin-top:30px'>
<div class="lec_view_pop_title">
	<div class='fleft pop_title_ment'  style=""><?=$rs[course]?>&nbsp;</div><div class='fleft'><?=$badge_ment?></div>
	<div class='clear'></div>
	<div class='f13 gray' style='padding:5px'><?=$rs[lec_title]?></div>
</div><br>
	<!--// 기본 목록//-->
	<table class="tbl_grid">
		<tr height="35">
			<td rowspan="4" width="" class="center" style='padding:0'>
				<div class='photo_screen'>
				<?
				if($rs[photo_screen])
				{
					echo "
						<div>
							<img src='/lecture/photo/{$rs[photo_screen]}' align='bottom' width='250' height='170'>
						</div>";
				} 
				else
				{
					echo "
						<div class='no_img' style='height:150px;'>
							<div class='f16 bold' style='padding-top:70px;'>{$rs[course]}</div>
						</div>";
				}
				?>
				</div>
			</td>
			<th width="20%">수강금액</th>
			<td><?=$lec_ctgr_arr[$rs[ctgr]]?>(<?=$price?>)</td>
			<th width="20%">강사이름</th>
			<td><?=$rs[teacher]?></td>
		</tr>
		<tr height="35">	
			<th>강좌수</th>
			<td><?=$rs[lec_cnt]?>강</td>
			<th>수강일수</th>
			<td class='bold'><?=$rs[days]?>일</td>
		</tr>
		<tr height="35">
			<th>수강제한</th>
			<td>최대 <?=$rs[v_limit]?>회</td>
			<th>촬영일자</th>
			<td><?=date("Y년 m월 d일",strtotime($rs[sdate]))?></td>
		</tr>
		<tr height="35">	
			<th>강의교재</th>
			<td colspan="3"><?=$book?>
				<?
					if($rs[books_reference])
					{
						if($client_id)
						{
							if($viewAllow)
							{
								$link_book = "/lecture/file_view.php?&idx={$idx}";
								$link_book_target="target='_blank'";
							}
							else
							{
								$link_book="javascript:alert(\"교재파일은 해당 강의 수강자만 다운 받을 수 있습니다.\\r\\n관련문의 02-456-1544\")";
								$link_book_target="";
							}	
						}
						else
						{
							$link_book="javascript:alert(\"로그인 후 이용 해 주세요.\")";
							$link_book_target="";
						}
						echo "&nbsp;&nbsp;&nbsp;<a href='{$link_book}' {$link_book_target}><span class='btn_box_ss btn_lightgray radius_10'>교재다운받기</span></a>";
					}
				?>
			</td>
		</tr>
	</table>
	<div class="view_pop_center">
		<div style='padding:5px 0'>
		<?
			//if($_sector==$rs[lec_code]){echo "{$client_name}님께서는 {$_validity} 까지 시청 가능 합니다.";}
			if($_validity){echo "[{$client_name}]님은 {$_validity} 까지 시청 가능 합니다.";}
		?>
		</div>
		<input type="hidden" id="book_link" name="book_link" value="<?=$rs[book_link]?>">
		<?
		if($client_id){echo "<a href='/member/pay_lec.php?ref=aply_lect&lec_cd={$lec_code}'><span class='btn_box_s btn_tank_1 radius_10'>강의신청</span> </a>";}
		else{echo "<div style='padding:10px'>로그인 후 신청이 가능합니다.</div>";}
		if($rs[book]){echo "<a href='javascript:chkLnk()'><span class='btn_box_s btn_tank_1 radius_10'>교재구입</span></a>";}
		?>
		<a href="/lec/lec_list_pop.php?ctgr=<?=$ctgr?>&cg_code=<?=$cg_code?>"><span class="btn_box_s btn_tank_1 radius_10">목록으로</span></a>
	</div>
	<!--// 사용기간 //-->
	
	<!--// 내용 //-->
	<div class="clear" style="height:30px"></div>

	<!--// box 내용 //-->
	<div class='view_pop_content'>
		<div class="mov_teb" id="menu"> 
			<ul>
				<li><div onclick="fnMove('1')">강의목차</div></li>
				<? 
					if($rs[intro]){ echo "<li><div onclick=\"fnMove('2')\">강의소개</div></li>"; }
					if($rs[photo_teacher] || $rs[teacher]){ echo "<li><div onclick=\"fnMove('3')\">강사소개</div></li>"; }
				  if($rs[photo_book] || $rs[book] || $rs[book_intro]){ echo "<li><div onclick=\"fnMove('4')\">교재소개</div></li>"; } 
				?>
				<!--<li><div onclick="fnMove('5')">나도한마디</div></li>-->
			</ul>
		</div>
		<div class="clear"></div>
		<div style="border:1px solid #ddd;">
			<!--// 강좌 목록//-->
			<div style="padding:20px">
				<div style="padding:20px 0">
					<div class="fleft f18 bold" id="div1">강의목차</div>
					<div class='fright'>
						<a href="https://stway.net/user_data/data/BeatPlayerSetup.exe"><span class='btn_box_sss btn_red radius_5' style='padding:2px 0;width:95px'>뷰어(Win용)</span></a>
						<a href="https://stway.net/user_data/data/Beatplayer.Mac.Setup.zip"><span class='btn_box_sss btn_lightblack radius_5' style='padding:2px 0;width:95px'>뷰어(Mac용)</span></a>
					</div>
					<div class="clear"></div>
					<br>
					<table class="tbl_list">
							<tr height='50'>
								<th width="50">번호</th>
								<th>제목</th>
								<th width="70">시간</th>
								<th width='1%'><div class='view_btn'>강좌보기</div></th>
							</tr>
						<?
						//정렬순서
						if($sort_num == "1"){$sort_key="DESC";}
						else{$sort_key="ASC";}
						
						$stmt=$pdo->prepare("SELECT * FROM {$my_db}.te_movie WHERE lec_code='{$lec_code}' ORDER BY mov_no {$sort_key}");
						$stmt->execute();
						while($row=$stmt->fetch())
						{						
							if($client_id)
							{
								if($viewAllow || $row[free]==1){$link_url = "javascript:openPlayer2({$row[mov_code]})";}
								else{$link_url="javascript:alert(\"본 강의는 {$rs[lec_title]}(결제)만  이용가능합니다.\\r\\n관련문의 02-456-1544\")";}	
							}
							else{$link_url="javascript:alert(\"로그인 후 이용 해 주세요.\")";}
							$freeMark=(($ctgr > 10) && $row[free]==1) ? "<span class='red'>[무료보기]</span>" : "";
							echo "
							<tr height='40'>
								<td class='center no'>{$row[mov_no]} 강</td>
								<td><a href='{$link_url}'>{$row[title]}</a> {$freeMark}</td>
								<td class='center'>{$row[playtime]}</td>
								<td class='center'><div class='view_btn'><a href='{$link_url}'><span class='btn_box btn_lightblack  radius_5' style='font-size:11px;padding:3px 0 2px;width:100px;font-weight:normal;'>강좌보기</span></a></div></td>
							</tr>";
						}
						?>
					</table>
				</div>
			</div>
			<!--// 강좌소개//-->
			<? if($rs[intro]) : ?>
			<div style="padding:20px">
				<div class="f18 bold" id="div2">강의소개</div><br>
				<table width="100%">
					<tr>
						<td class="lh20">
							<div class="lh20" style="padding:0 10px">
								<?=nl2br($rs[intro])?>
							</div>
							<?
								if($rs[photo_lecture]){echo "<div class='center' style='padding:10px 0'><img src='/lecture/photo/{$rs[photo_lecture]}' align='bottom' width='950'></div>";}
								else{echo "";}
							?>
						</td>
					</tr>
				</table>
			</div>
			<? endif ; ?>
			<? if($rs[photo_teacher] || $rs[teacher]) : ?>
			<div class="clear" style="margin-top:20px;height:20px;border-top:1px solid #ddd"></div>
			<!--// 교수소개//-->
			<div style="padding:20px">
				<div class="f18 bold" id="div3">강사소개</div><br>
				<table width="100%">
					<tr>
						<?
						if($rs[photo_teacher])
						{
							echo "
							<td width='200' valign='top'>
								<div><img src='/lecture/photo/{$rs[photo_teacher]}' align='bottom' width='100'></div>
							</td>";
						}else{echo "<td width='20'></td>";}
						?>
						<td class="lh20" valign="top">
							<div class="f18 bold"><?=$rs[teacher]?></div><br>
							<div class="lh20">
								<?=nl2br($rs[profile])?>
							</div>
						</td>
					</tr>
				</table>
			</div>
			<? endif ; ?>
			<? if($rs[photo_book] || $rs[book] || $rs[book_intro]) : ?>
			<div class="clear" style="margin-top:20px;height:20px;border-top:1px solid #ddd"></div>
			<!--// 교재 소개//-->
			<div style="padding:20px">
				<div class="f18 bold" id="div4">교재소개</div><br>
				<table width="100%">
                    
					<tr>
						<?
							if($rs[photo_book])
							{
								echo "
										<td width='200' valign='top'>
												<div><img src='/lecture/photo/{$rs[photo_book]}' align='bottom' width='100'></div>
										</td>";
							}
							else{echo "<td width='20'></td>";}
							?>
						<td class="lh20" valign="top">
							<div class="f18 bold"><?=$rs[book]?></div><br>
							<div class="lh20">
								<?=nl2br($rs[book_intro])?>
							</div>
						</td>
					</tr>
				</table>
			</div>
			<? endif ; ?>
			<!--// 토크//-->
			<!--
			<div class="fm_talk">
				<div class="f18 bold" id="div5">나도한마디</div><br>
				<div>
					<form name="fm_talk" id="fm_talk">
					<div class="fm_talk_box">
						<div class="talk_title">
							<? if($client_id) : ?>
								<?=$client_name?> 님<br>(<?=$client_id?>)<br>
							<? endif ;?>
							댓글 등록</div>
						<div class="talk_text">
							<textarea rows="5" name="talk_memo" id="talk_memo" class="han f13" style="width:100%" placeholder="건전한 토론문화와 양질의 댓글 문화를 위해, 타인에게 불쾌감을 주는 욕설 또는 특정 계층/민족, 종교 등을 비하하는 단어들은 표시가 제한됩니다."></textarea>
						</div>
						<div class="talk_btn">
						<? if($client_id) : ?>	
							<span class="btn_box_s btn_white" id="btn_add_talk">등 록</span>
						<? else : ?>	
							<span class="btn_box_s btn_white" onclick="alert('로그인 후 이용 해 주세요.')"><span class="f14">로그인 후<br>등록</span></span>
						<? endif ; ?>
						</div>
						
						<div class="clear center" style="padding:5px 0"><span class='red'>* 댓글 수정시 - 해당목록의 내용을 지우고, 다시 댓글 등록 하시면 됩니다.</span></div>
					</div>	
					
					<div>	
						<table class="tbl_list">
							<thead>
								<tr>
									<th style="width:7%">No</th>
									<th style="">내용</th>
								</tr>
							</thead>
							<tbody id="list_body"></tbody>
						</table>
						<div id="page_navi"></div>
						<input type="hidden" name="talk_mode" id="talk_mode" value="">
						<input type="hidden" name="user_id" value="<?=$client_id?>">
						<input type="hidden" name="lec_code" value="<?=$lec_code?>">
						<input type="hidden" name="list_scale" id="list_scale" value="20">
						<input type="hidden" name="page_scale" id="page_scale" value="10">
						<input type="hidden" name="start" id="start" value="0">
						<input type="hidden" name="total_record" id="total_record" value="0">
						<input type="hidden" name="idx_arr" id="idx_arr" value="">
						<input type="hidden" name="ref_start" id="ref_start" value="">	
					</div>
					<div class="clear"></div>
					</form>
				</div>
				<br>
				<div class="clear"></div>
			</div>
			-->
			<!--// 토크//-->
		</div>
	</div>
	<!--// box 내용 //-->
	<!--// box right //-->
	<div class='view_pop_rigth'>
		<div class='center'  id="view_pop_rigth" style='float:left;background:#FFF;border:1px solid #ccc;border-top:2px solid #162F5F;margin:0 0 0 20px;width:260px;z-index:1'>
			<input type="hidden" id="book_link" name="book_link" value="<?=$rs[book_link]?>">
			<?
			if($client_id)
			{
				if($viewAllow)
				{
					if($_sector==$rs[lec_code])
					{
						echo "<div style='padding:10px 15px;height:50px;margin-bottom:10px;border-bottom:1px solid #999;background:#F7FBFF'><div style='padding-top:0px'>{$client_name}님</div><div class='f20 bold'>{$_validity}까지 시청</div></div>";
					}
				}
				else
				{
					echo "<div style='padding:10px 15px;height:35px;border-bottom:1px solid #999;background:#F7FBFF'><div class='fleft' style='padding-top:10px'>{$rs[days]}일</div><div class='right f30 bold_900'>{$price_1}</div></div>";
				}
				if($rs[price]!=0)
				{
					echo "
						<div style='padding:10px'>신청을 하셔야 이용 할 수 있습니다.</div>
						<div style='padding:3px 0;margin-right:10px'><a href='/member/pay_lec.php?ref=aply_lect&lec_cd={$lec_code}'><span class='btn_box_s btn_tank_1 radius_5' style='width:90%'>강의신청</span></a></div>";
				}
			}
			else
			{
				echo "
					<div style='padding:10px 15px;height:35px;border-bottom:1px solid #999;background:#F7FBFF'><div class='fleft' style='padding-top:10px'>{$rs[days]}일</div><div class='right f30 bold_900'>{$price_1}</div></div>
					<div style='padding:10px'>로그인 전 입니다.</div>
					<div style='padding:3px 0'><a href='/inc/login_box.php?kind_mode=lec'><span class='btn_box_s btn_white radius_5' style='width:90%'>로그인</span></a></div>
					<div style='padding:3px 0'><a href='/lec/mem_agree.php'><span class='btn_box_s btn_tank_1 radius_5' style='width:90%'>회원가입</span></a></div>";	
			}
			if($rs[book])
			{
				echo "<div style='padding:3px 0'><a href='javascript:chkLnk()'><span class='btn_box_s btn_tank_1 radius_5' style='width:90%'>교재구입</span></a></div>";
			}
			?>
			<div style='padding:3px 0'><a href="/lec/lec_list_pop.php?ctgr=<?=$ctgr?>&cg_code=<?=$cg_code?>"><span class="btn_box_s btn_lightgray radius_5" style='width:90%'>목록으로</span></a></div>
			<div class='left gray' style='padding:10px 10px'>
				- <?=$rs[lec_title]?><br>
				- <?=$rs[days]?>일동안 시청 가능<br>
 			
 				- 모바일에서 시청 가능<br>
			</div>
		</div>
	</div>	
	<!--// box right //-->
	
</div>
<? include($_SERVER["DOCUMENT_ROOT"]."/lec/common/footer_lec.php"); ?>

<script type="text/javascript">
	$(document).ready(function()
	{ 
		//teb메뉴 이동
		var floatPosition = parseInt($("#menu").css('top')); 
		$(window).scroll(function() 
		{ 
			var scrollTop = $(window).scrollTop(); 
			var newPosition = scrollTop + floatPosition + "px"; 
			$("#menu").stop().animate({"top":newPosition},500); 
		}).scroll(); 
		
		//	view_pop_rigth 이동
		$(window).scroll(function(event)
		{
			var scroll = $(window).scrollTop();
			if (scroll > 285){$("#view_pop_rigth").css({"position":"fixed","top":"120px","rgith":"0"});}
			else {$("#view_pop_rigth").css({"position":"static",});}
		});
	});
	function chkLnk()
	{
		var lnkUrl="";
		lnkUrl=$("#book_link").val();
		if(lnkUrl==""){alert("별도 구입이 없습니다.");	return;}
		window.open(lnkUrl);
	}
	function openPlayer(movieID)
	{
		window.open("/lecture/player.php?movieID="+movieID,"player","top=0,left=0,width=850,height=640,scrollbars=yes,resizable=yes").focus();	
	}
	function fnMove(seq)
	{ 
		var offset = $("#div" + seq).offset(); 
		$('html, body').animate({scrollTop : offset.top - 150}, 400); 
	}
</script>

<?php
################################### stway 추가 작업 2017-10-18 #########################
	/// HTTP_USER_AGENT
	$ua = $_SERVER['HTTP_USER_AGENT'];
	$ua = strtolower($ua);
	if(strpos($ua,"trident")>0 || strpos($ua,"msie")>0)
	{
		if(strpos($ua,"trident/7.0")>0){
			$html5 = "Y";
			$title = "IE 11";
		}
		else if(strpos($ua,"trident/6.0")>0){
			$html5 = "N";
			$title = "IE 10";
		}
		else if(strpos($ua,"trident/5.0")>0){
			$html5 = "N";
			$title = "IE 9 ";
		}
		else if(strpos($ua,"trident/4.0")>0){
			$html5 = "N";
			$title = "IE 8 ";
		}
		else {
			$html5 = "N";
			$title = "IE ";
		}
	}
	else if (strpos($ua, "Mac") >0 )
	{
	    $html5 = "N";
	    $title = "Mac";
  }
	else
	{
		$_agent = $_SERVER["HTTP_USER_AGENT"];
		$_other_os = stripos($_agent,"iPhone") +  stripos($_agent,"iPad") + stripos($_agent,"Mac") + stripos($_agent,"Android");
		if($_other_os>0)
		{
			$html5 = "N";
			$title = "mobile";
		}else
		{
			$html5 = "Y";
			$title = "other";
		}
	}
	// HTTP_USER_AGENT
	
	//pay_code >> 100=>"경매정보", 101=>"동영상강좌"
	//ctgr >> 10=>"유료회원무료", 11=>"연간회원무료", 20=>"초급지식", 21=>"고급지식", 22=>"실무지식", 30=>"수강생-초급", 31=>"수강생-중급", 32=>"수강생-고급"
	if($ctgr=='10')			{$pay_code="100";}
	elseif($ctgr=='11')	{$pay_code="100";}
	elseif($ctgr=='40')	{$pay_code="201";}
	elseif($ctgr=='20' || $ctgr=='21' || $ctgr=='22' || $ctgr=='30' || $ctgr=='31' || $ctgr=='32'){$pay_code="101";}
	else{$pay_code="";}

	if(!empty($pay_code))
	{
		$que = "SELECT idx FROM {$my_db}.tm_pay_result WHERE id='{$_user}' and pay_code='{$pay_code}' AND validity >= CURDATE() order by idx desc LIMIT 1";
		$stmt=$pdo->prepare($que);
		$stmt->execute();
		$rs=$stmt->fetch();
		$_payid = $rs[idx];
	}
	else{$_payid = "sample";}

	$_u = $_user;
	$_c = $lec_code;
	$_p = $_payid;
	$_s = $_payid;
 ?>
	<script>
		function openPlayer2(movieID)
		{
			var html5view = "<?=$html5?>";
			$.ajax(
			{ 
				type : "POST",
				url : "/beatplayer/config/beat_ajax.php",
				async: false,
				dataType : "json",
				data : "t=stream&u=<?=$_u?>&c=<?=$_c?>&l="+ movieID +"&s=<?=$_s?>&p=<?=$_p?>&i=<?=$isAdmin?>",
				success : function(r)
				{ 
					v3_url = r.v3;
					v2_url = r.v2;
				}
			}); //ajax

			if (html5view=="Y")
			{
				//beatplayer https지원 안됨> apache 예외처리
				popurl = "/beatplayer/html5skin/html5player.php?b="+v3_url;
				window.open(popurl,"player","top=0,left=0,width=720,height=500,scrollbars=yes,resizable=yes").focus();
			}
			else
			{
				beatPlayer(v2_url,'<?=$_c?>','<?=$_u?>');
			}
		}
	</script>

	<script src="/beatplayer/js/beat_setup.js?<?=date("Ymd");?>"></script>
  <script src="/beatplayer/js/protocolcheck.js?<?=date("Ymd");?>"></script>
  <script src="/beatplayer/js/beatPlayer.js?<?=date("Ymd");?>"></script>
  <script src="/beatplayer/js/spin.js?<?=date("Ymd");?>"></script>
<?php
################################### stway 추가 작업 #########################
$refArr=array("_user"=>$_user, "pay_code"=>$pay_code, "_u"=>$_user, "_c"=>$lec_code, "_p"=>$_payid, "_s"=>$_payid);
/*
foreach($refArr as $k => $v)
{
	//echo "{$k}: {$v}<br>";
}
*/
?>

<script>

/*
$(document).ready(function(){

	
	$("#btn_save_info").click(function(){
		$("#fm_user_info").submit();
	});
	
	$("#btn_add_talk").click(function(){
		add_talk();
	});
	load_lec_talk();
});

//토크 등록
function add_talk()
{
	if($.trim($("#talk_memo").val())=="")
	{
		alert("내용을 입력 해 주세요");
		$("#talk_memo").focus();
		return;
	}
	$("#talk_mode").val("add_talk");
	load_lec_talk();
}
//토크 삭제
function del_talk(idx)
{
	$("#tr_"+idx).css({"background":"#ddd"});
	var is_del=confirm("선택하신 내용을 삭제 하시겠습니까?");
	if(is_del==true)
	{
		$("#talk_mode").val("del_talk");
		$("#idx_arr").val(idx);
		load_lec_talk();
	}
	else
	{
		$("#tr_"+idx).css({"background":"#fff"});
	}
}


function load_lec_talk()
{
	var arr=[], html="", total=0, start=0;
	
	$.ajax({
		type: "post",
		url: "/xml/lec_talk.php",
		data: $("#fm_talk").serialize(),
		dataType: "xml",
		beforeSend: function(){
			//
		},
		success: function(xml){
			pageClear();
			$(xml).find("item").each(function(){
	    		var $entry=$(this);
	    		var line_no=$entry.find("line_no").text();
	    		var idx=$entry.find("idx").text();
	    		var r_id=$entry.find("r_id").text();
	    		var memo=$entry.find("memo").text();
	    		var lec_code=$entry.find("lec_code").text();
				var wdate=$entry.find("wdate").text();
				var mem_id=$entry.find("mem_id").text();
				var admin=$entry.find("admin").text();
				arr.push("<tr id='tr_"+idx+"'>");
				arr.push("<td class='center'>"+line_no+"</td>");
				//arr.push("<td class='center'>"+lec_code+"</td>");
				var memo_enc=escape(encodeURIComponent(memo));
				arr.push("<td>");
				arr.push("<div class='' style='padding:5px'><span class='bold f14'>"+mem_id+"</span>&nbsp;&nbsp;&nbsp;<span class='gray'>"+wdate+"</span>");
				
				if(r_id=="<?=$client_id?>"){
					arr.push("<span>&nbsp;&nbsp;&nbsp;<a href=\"javascript:del_talk('"+idx+"')\" class='red'><span class='btn_box_ssss btn_red' style='min-width:20px;padding:1px;font-size:10px'>X</span></a></span>");
					
				}else{
					arr.push("<span>&nbsp;</span>");
					
				}
				arr.push("</div><div style='padding:5px 0'>"+memo+"</div></td>");
				arr.push("</tr>");
	    	});
	    	html=arr.join("");
	    	$("#list_body").html(html);
	    	start=$(xml).find("start").text();
	    	total=$(xml).find("total_record").text();
	    	if(total==0)
	    	{
	    		$("#page_navi").html("<div class='no_result_s'><span>댓글이 없습니다.<br>첫번째 댓글을 남겨주세요.</span></div>");	
	    	}
	    	else
	    	{
	    		pageNavi(total,start,"load_lec_talk",0);	
	    	}	    	
	    	
	    	$("#talk_memo").val("");
	    	$("#idx_arr").val("");
	    	$("#talk_mode").val("");
			$("#ref_start").val(start);
			
			$("#chk_reserve").attr("checked",false);
			$("#rdate").val("");
		},
		error: function(xml,status,err){
			$("#list_body").html("<td colspan='4'><div class='center' style='padding:50px'>서버와의 통신이 실패했습니다. <a href=\"javascript:load_lec_talk()\" class='blue'>[새로 고침]</a></div></td>");
		},
		complete: function(){
			$("#loading").hide();
		}
	});
}
*/
</script>
