<?
    $page = 5;
    include_once($_SERVER["DOCUMENT_ROOT"]."/practice/inc/header.php");
    include_once($_SERVER["DOCUMENT_ROOT"]."/practice/inc/asd.php");

    $dv = ((int)$dv>10)?$dv:10;
    $title = ${"header_arr_".$page}[$dv]['menu'];
?>
<main>
    <div class="main">홈 > 정보광장 > <?=$title?></div>
    <div>
        <form class="search_form" id="agora_form" name="agora_form" >
            <input type="hidden"  id="dvsn" value="<?=$dv?>">
            <input type="hidden" id="mode" value="all">
            <input type="text" id="ag_val" placeholder="검색어를 입력해주세요" >
        </form>
        <button class="search" id="agBtn">검색</button>
    </div>
    <div class="list_box">
        <table>
            <thead></thead>
            <tbody id="ag_ls"><?=$_not?></tbody>
        </table>
    </div>
</main>
<?
    include_once($_SERVER["DOCUMENT_ROOT"]."/practice/inc/footer.php");
?>
<script src="/practice/js/agora.js?ver=<?=$_ver?>"></script>