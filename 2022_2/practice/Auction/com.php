<?
    $page = 1;
    include_once($_SERVER["DOCUMENT_ROOT"]."/practice/inc/header.php");
    include_once($_SERVER["DOCUMENT_ROOT"]."/practice/inc/asd.php");
    include_once($_SERVER["DOCUMENT_ROOT"]."/practice/inc/arrAuct.php");

?>
<main>
    <div class="main">
        <span>경매>종합검색</span>
        <hr>
        <form name="c_search1" id="c_search1" class="search_form">
            <input type="hidden" name="refSiCd" id="refSiCd" value="<?=$siCd?>">
            <input type="hidden" name="refGuCd" id="refGuCd" value="<?=$guCd?>">
            <input type="hidden" name="refDnCd" id="refDnCd" value="<?=$dnCd?>">
            <table class="sel_tbl">
                <tbody class="sel_tbody">
                    <tr>
                        <th>주소선택</th>
                        <td>
                            <span id="addr1" class="btn_box_l btn_lightgray" style="min-width:40px">주소</span>
                            <span id="addr2" class="btn_box_r btn_lightgray" style="min-width:40px">법원</span>
                            <span id="addr_s">
                                <select name="siCd" id="siCd"></select>
                                <select name="guCd" id="guCd"></select>
                                <select name="dnCd" id="dnCd"></select>
                            </span>
                            <span id="row_s">
                                <select name="rowCd" id="rowCd">
                                    <option value="0" selected>-선택-</option>
                                    <?  $option ="";
                                        foreach($arrSrchCS as $k => $v)
                                        {
                                            $option .= "<option value='$k' ";
                                            if((int)$k<30){
                                                $option .= "style='background-color:yellow;'";
                                            }
                                            $option .= ">$v</option>";
                                        }
                                        echo $option;
                                    ?>
                                </select>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>날짜로 검색</th>
                        <td>
                            <input type="hidden" id="mode" name="mode" value="json">
                            <input class="in" type="text" id="datepicker1" name="datepicker1">
                            ~
                            <input class="in" type="text" id="datepicker2" name="datepicker2">
                        </td>
                    </tr>
                    
                        <th>물건종류</th>
                        <td>
                            <select id="sel_pord">
                                <option value="0" selected>-선택-</option>
                                <?
                                    $srchCtrg = "";
                                    foreach($arrSrchCtgr as $k => $v)
                                    {
                                        $srchCtrg .= "<option value='$k' style='background-color:yellow;>$v</option>";
                                        
                                        foreach($arrSrchCtgrSub[$k] as $k => $v)
                                        {
                                            $srchCtrg .= "<option value='$k'>$v</option>";
                                        }
                                    }
                                    echo $srchCtrg;
                                ?>
                            </select>
                            <!-- <button class="search" id="multi_btn" value="0">복수선택</button> -->
                            <input type="button" id="multi_btn" onclick="multi(this)" class="search" value="복수선택">
                        </td>
                    <tr class="chkMulti">
                        <th>물건종류 복수선택</th>
                        <td>
                            <label for="chkAllCtgr" style="cursor:pointer" class="inputWrap">
                                <input type="checkbox" id="chkAllCtgr" name="chkAllCtgr" value="0" checked=""
                                    class="input_chk chk" onclick="chkCtgrMulti(0,0)">
                                <span class="chk_ment">전체보기</span>
                            </label>
                        </td>
                    </tr>
                    <?
				foreach($arrSrchCtgr as $k => $v)
				{
				  
					$th="<th class='cl_gt'>
							  <label for='chkGrpCtgr_{$k}' style='cursor:pointer' class='inputWrap'>
								  <input type='checkbox' id='chkGrpCtgr_{$k}' name='chkGrpCtgr' value='{$k}' class='input_chk chk' onclick='chkCtgrMulti({$k},1)'>
								  <span class='chk_ment'>{$v}</span>
							  </label>
						  </th>";
						
						
					$td="<td class='cl_gc'>";
					      foreach($arrSrchCtgrSub[$k] as $sk => $sv)
					      {
						     // $chk_id_Nm='chkEaCtgr_' . str_replace(',','_',$sk);
						      $chk_id_Nm="chkEaCtgr_{$k}_{$sk}";
						      $td.="<label for='{$chk_id_Nm}' onclick='' style='cursor:pointer' class='inputWrap'>
								          <input type='checkbox' id='chkCtgr_{$k}_{$sk}' name='chkEaCtgr' value='{$sk}' class='input_chk chk' onclick='chkCtgrMulti({$k},2)'>
								          <span class='chk_ment'>{$sv}</span>
							          </label>";
					      }
					$td.="</td>";
					echo "<tr class='chkMulti'>{$th}{$td}</tr>";
				}
				?>
                </tbody>
            </table>
        </form>
        <div class="search_result">
            <button class="search" id="csBtn">검색</button>
            <button class="search" id="test">TEST</button>
            <div class="list_box">
                <table class="tbl_c_list">
                    <thead id="lsThead"></thead>
                    <tbody id="lsTbody"></tbody>
                </table>
            </div>
        </div>
    </div>
</main>
<?
    include_once($_SERVER["DOCUMENT_ROOT"]."/practice/inc/footer.php");

?>
<script>
var TK = eval("({})");
</script>
<script src="/practice/js/calendar.js"></script>
<script src="/practice/js/search_op.js"></script>
<script src="/practice/js/c_search.js"></script>
<script>
    $("#test").click( function(){
        console.log($("#c_search1").serialize());
        console.log(1);
    });
</script>