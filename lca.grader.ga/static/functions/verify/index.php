<?php
    require_once '../connect.php';
    $key = $_GET['key'];
    $email = $_GET['email'];

    $query1 = "SELECT * FROM `user` WHERE email = '$email' AND password = '$key'";
    $result1 = mysqli_query($conn, $query1);
    if (! $result1) {
        die('Could not get data: ' . mysqli_error($conn));
    }
    $id = mysqli_fetch_array($result1, MYSQLI_ASSOC)['id'];
    if (mysqli_num_rows($result1) == 0) {
        $_SESSION['swal_error'] = "ยืนยันอีเมลไม่สำเร็จ";
        $_SESSION['swal_error_msg'] = "พบข้อผิดพลาด: ข้อมูลไม่ตรงกับฐานข้อมูล";
        header("Location: ../../../home/");
    } else if (getUserdata($id, 'isEmailVerify', $conn)) {
        $_SESSION['swal_warning'] = "คุณได้ยืนยันอีเมลไปแล้ว";
        $_SESSION['swal_warning_msg'] = "ไม่มีความจำเป็นที่จะต้องยืนยันอีเมลซ้ำอีกครั้ง";
        header("Location: ../../../home/");
    } else {
        $query = "UPDATE `user` SET isEmailVerify = true WHERE email = '$email'";
        $result = mysqli_query($conn, $query);
        if (! $result) {
            die('Could not get data: ' . mysqli_error($conn));
        }
        if (!isLogin()) {
            header("Location: ../auth/login.php?user=".getUserdata($id, 'username', $conn)."&pass=".getUserdata($id, 'password', $conn)."&method=email");
        } else {
            header("Location: ../../../home/");
        }
    }
?>