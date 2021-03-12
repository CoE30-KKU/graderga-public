<?php
    require_once '../static/functions/connect.php';
    require_once '../static/functions/function.php';

    if (isLogin() && isset($_POST['id'])) {
        $id = $_POST['id'];

        $name = $_POST['name'];

        $email = $_POST['email'];
        $real_email = $_POST['real_email'];

        $profile_url = $_POST['real_profile_url'];

        $pass = $_POST['password'];

        print_r($_POST);

        if (!empty($pass)) {
            $pass = md5($pass);
            $stmt = $conn->prepare("UPDATE `user` SET displayname=?, email=?, profile=?, password=? WHERE id = ?");
            $stmt->bind_param('ssssi', $name, $email, $profile_url, $pass, $id);
        } else {
            $stmt = $conn->prepare("UPDATE `user` SET displayname=?, email=?, profile=? WHERE id = ?");
            $stmt->bind_param('sssi', $name, $email, $profile_url, $id);
        }
        
        if ($stmt->execute()) {
            $_SESSION['swal_success'] = "สำเร็จ!";
            $_SESSION['swal_success_msg'] = "แก้ไขโปรไฟล์ #$id สำเร็จแล้ว!";
            echo "Edited";
        } else {
            $_SESSION['swal_error'] = "พบข้อผิดพลาด";
            $_SESSION['swal_error_msg'] = "ERROR 40 : ไม่สามารถ Query Database ได้\n$conn->error";
            echo "Can't establish database";
        }
        
    }
    header("Location: ../profile/");
?>