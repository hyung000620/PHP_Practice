<!-- <h1>readfile을 이용한 텍스트 파일 로드</h1>
<div>
<? //readfile("data.txt") ?>
</div>
<br>
<br>
<? 
    // $result = "";
    // $lines = @file("data.txt") or $result ="파일을 읽을 수  없습니다.";
    // if($lines != null){
    //     for($i = 0; $i<count($lines); $i++){
    //         $result .= ($i + 1) . ":" . $lines[$i] . "<br>";
    //     }
    // }
?>
<h1>file를 사용한 파일의 텍스트를 한줄씩 처리</h1>
<div>
    <? //echo $result; ?>
</div>
<br>
<br>
<?
    // $lines = @file("data.txt") or $result = "파일을 읽을 수  없습니다.";
    // if($lines != null){
    //     $result = "<table border ='1'>";
    //     $result .="<tr> <th>NAME</th> <th>MAIL</th> <th>TEL</th> </tr>";

    //     for($i = 0; $i< count($lines); $i++){
    //         $result .= "<tr>";
    //         $arr = explode("," , $lines[$i]);
    //         for($j = 0; $j<3; $j++){
    //             $result .= "<td>{$arr[$j]}</td>";
    //         }
    //         $result .= "</tr>";
    //     }
    //     $result .= "</table>";
    // }
?>
<h1>텍스트를 분할하여 처리(explode, implode)</h1>
<div>
    <? //echo $result; ?>
</div>
<br>
<br>
<h1>fgets를 사용하여 파일 로드</h1>
<?
    // $f = @fopen("data.txt",'r') or exit("BREAK");
    // $result = '';
    // while(!feof($f)){
    //     $result .= fgets($f,10);
    // }
    // fclose($f);
?>
<div>
    <? //echo $result; ?>
</div>
<br>
<br>
<h1>fputs를 사용하여 파일 저장</h1>
<?
    // if($_POST != null){
    //     $f = @fopen("data.txt",'a') or exit("파일을 읽을 수 없습니다.");
    //     if($f !=null){
    //         $s = $_POST['text1'];
    //         fputs($f,$s . "\n");
    //         fclose($f);
    //     }
    // }

    // $f2 = @fopen("data.txt",'r') or exit("파일을 읽을 수 없습니다");
    // $result = '';
    // $i = 1;
    // while(!feof($f2)){
    //     $s2 = htmlspecialchars(fgets($f2));
    //     if($s2 != ""){
    //         $result = $i++ . ":" . $s2 . "<br>" . $result;
    //     }
    // }
    // fclose($f2);
    
?>

<form method="post" action="/practice/prac1.php">
    <input type="text" name="text1">
    <input type="submit">
</form>
<div>
    <? //echo $result; ?>
</div>
<br>
<br> -->

<!-- <?
    // if($_POST != null ){
    //     $url = $_POST['text1'];
    //     $lines = file($url);
    //     $result = implode($lines);
    // }
?>
<head> <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /> <title>sample page</title> </head>

<form method="post" action="/practice/prac1.php">
    <input type="text" name="text1" size="40" value="<? //echo htmlspecialchars($url); ?>">
    <br>
    <input type="submit">
</form>

<div>
    <? //echo htmlspecialchars($result); ?>
</div> -->
<? 
//  function getBoldStr($str){
//      $str = htmlspecialchars(strtoupper($str));
//      $res = str_replace('PHP', '<span style="font-weight:bold; color:red">PHP</span>',$str);
//      return $res;
//  }

//  if($_POST != null) {
//      $str = $_POST['text1'];
 
?>
<!-- <h1><? //echo getBoldStr("Hello PHP"); ?> </h1>
<p><? //echo getBoldStr($str); ?> </p>

<form method="post" action="/practice/prac1.php">
 <textarea name="text1" cols="40" rows="5"><? //echo $str ?></textarea>
 <br><input type="submit">
</form> -->

<?php
class TextModify {
    private $header = "<b>";
    private $footer = "</b>";
    private $body = "";
    private $find = "PHP";
    
    public function __construct($h, $f){
        $this -> setHeader($h);
        $this -> setFooter($f);
    }

    public function setHeader($s){
        $this ->header = $s;
    }

    public function setFooter($s){
        $this ->footer = $s;
    }

    public function setBody($s){
        $this ->body = htmlspecialchars(strtoupper($s));
    }
    public function setFind($s){
        $this ->find = $s;
    }

    function getRenderText(){
        $res = str_replace($this -> find, $this -> header . $this -> find . $this -> footer,$this -> body);
        return $res;
    }
    function writeRenderText(){
        echo $this->getRenderText();
    }
}

$title_obj = new TextModify('<span style="color:red;">','</span>');
$title_obj -> setFind("PHP");
$title_obj -> setBody('Hello PHP!');
$msg_obj = new TextModify('<span style="color:blue;">','</span>');
$msg_obj ->setBody('여기에 php라는 문자를 포함한 문장을 써주세요.');
$rep_obj = new TextModify("<b>","</b>");

if($_POST != null){
    $str = $_POST['text1'];
    $rep_obj -> setBody($str);
}
?>

<h1><?php $title_obj ->writeRenderText(); ?></h1>
<p><?php $msg_obj -> writeRenderText(); ?></p>
<hr>
<p><?php $rep_obj ->writeRenderText(); ?></p>
<form method="post" action="/practice/prac1.php">
    <textarea name="text1" cols="40" rows="5"><?php echo $str; ?></textarea>
    <br><input type="submit">
</form>