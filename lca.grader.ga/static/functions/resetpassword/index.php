<?php
    require '../connect.php';
    require '../function.php';

    $key = $_GET['key'];
    $email = $_GET['email'];

    $query1 = "SELECT * FROM `user` WHERE email = '$email' AND password = '$key'";
    $result1 = mysqli_query($conn, $query1);
    if (! $result1) {
        die('Could not get data: ' . mysqli_error($conn));
    }
    $id = mysqli_fetch_array($result1, MYSQLI_ASSOC)['id'];
    if (mysqli_num_rows($result1) == 0) {
        $_SESSION['swal_error'] = "ไม่สามารถรีเซ็ตรหัสผ่าน";
        $_SESSION['swal_error_msg'] = "พบข้อผิดพลาด: ข้อมูลไม่ตรงกับฐานข้อมูล";
        header("Location: ../../../home/");
    } else {
        header("Location: ../auth/login.php?user=".getUserdata($id, 'username', $conn)."&pass=".getUserdata($id, 'password', $conn)."&method=reset");
    }
?>