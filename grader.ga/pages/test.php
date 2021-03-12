<?php
    require_once '../static/functions/function.php';
    $l = $_GET['lang']; 
    array_push($l, array("test","test2"));
    print_r($l);
    $s = arrToTxt($l);
    print_r($s);

    $arr = txtToArr($s);

    print_r($arr);
?>