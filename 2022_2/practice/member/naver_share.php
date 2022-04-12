<button type="button" id="naver_share">네이버 공유 버튼</button>

<script>
$("#naver_share").click(function(){
    let title = "[탱크옥션] 공유";
    let share_url = '<?=$link?>';
    let url = 'http://share.naver.com/web/shareView.nhn?url='+encodeURIComponent(share_url)+'&title='+encodeURIComponent(title);
    window.open(url,'share',"width=980, height=700");
});
</script>  