<?
    $html = "<aside class='aside'>";

    foreach(${"header_arr_".$page} as $k => $v)
    {
        $html .= "<div><a href=\"{$v['url']}\">{$v['menu']}</a><div>";
    }
    $html .= "</aside>";
    
    echo $html;
?>