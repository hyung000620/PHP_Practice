<?
include_once($_SERVER["DOCUMENT_ROOT"]. "/inc/header.php");
/*
    include_once()는 include()와 쓰임은 같으나 once를 보면 알 수 있듯이 지정한 파일을 한 번
    만 삽입한다는 의미이다. include가 여러번 호출될 때 중복되는 것을 방지하기 위해서
    include_once()를 사용하게 된다.
*/
?>
<main>
    main
    <section>1</section>
    <section>2</section>
</main>
<div> </div>

<? include_once($_SERVER["DOCUMENT_ROOT"] . "/inc/footer.php" ) ?>