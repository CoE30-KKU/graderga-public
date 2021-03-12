<?php 
    require_once '../static/functions/connect.php';
    require_once '../static/functions/function.php';

    if (isset($_POST['setNewPassword']) && isLogin()) {
        $password = $_POST['setNewPassword'];
        $md5_pass = md5($password);
        $id = $_SESSION['id'];

        $query = "UPDATE `user` SET password = '$md5_pass' WHERE id = '$id'";
        $result = mysqli_query($conn, $query);
        if (! $result) {
            die('Could not get data: ' . mysqli_error($conn));
        }

        $_SESSION['swal_success'] = "เปลี่ยนรหัสผ่านสำเร็จ";
        header("Location: ../home/");
    }
?>