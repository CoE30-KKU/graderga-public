<?php
    ob_start();
    session_start(); if (!isset($_SESSION['dark_mode'])) $_SESSION['dark_mode'] = false;
    $start_time = microtime(TRUE);
    $dbhost = "<<DATABASE IP>>";
    $dbuser = "<<DATABASE USERNAME>>";
    $dbpass = "<<DATABASE PASSWORD>>";
    $dbdatabase = "<<DATABASE DATATABLE>>";
    $conn = mysqli_connect($dbhost,$dbuser,$dbpass,$dbdatabase); 
    mysqli_set_charset($conn, 'utf8mb4');

    $private_key = md5("<<OWN PRIVATE KEY>>");

    if(!$conn)  die('Could not connect: ' . mysqli_error($conn));
    
    @ini_set('upload_max_size','128M');
    @ini_set('post_max_size','128M');
    @ini_set('max_execution_time','300');
    
    date_default_timezone_set('Asia/Bangkok');
?>
