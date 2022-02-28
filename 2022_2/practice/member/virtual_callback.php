<?php
$postData = file_get_contents('php://input');
$json = json_decode($postData);
if ($json->status == 'DONE') {
}
elseif($json->status == 'WAITING_FOR_DEPOSIT'){
}
?>