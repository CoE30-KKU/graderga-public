<?php
    require_once '../connect.php';
    require_once '../function.php';

if (isset($_POST['method']) && $_POST['method'] == 'loginPage') {
    $user = $_POST['login_username']; //Email
    $pass = md5($_POST['login_password']); //Student ID

    //ดึงข้อมูลมาเช็คว่า $User ที่ตั้งรหัสผ่านเป็น $Pass มีในระบบรึเปล่า
    if ($stmt = $conn -> prepare('SELECT * FROM `user` WHERE email = ? AND password = ? LIMIT 1')) {
        $stmt->bind_param('ss', $user, $pass);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows == 1) {
            while ($row = $result->fetch_assoc()) {
                $_SESSION['id'] = $row['id'];
                $_SESSION['std_id'] = $row['std_id'];
                $_SESSION['name'] = $row['name'];
                $_SESSION['swal_success'] = "เข้าสู่ระบบสำเร็จ";
                $_SESSION['swal_success_msg'] = "ยินดีต้อนรับ " . $row['name'] . "!";

                $_properties = json_decode($row['properties'], true);
                $_SESSION['admin'] = array_key_exists("admin", $_properties) ? $_properties["admin"] : false;
            }
        } else {
            $_SESSION['error'] = "ชื่อผู้ใช้งานหรือรหัสผ่านไม่ถูกต้อง";
        }
        $stmt->free_result();
        $stmt->close();
    } else {
        $_SESSION['error'] = "พบข้อผิดพลาดในการเข้าถึงฐานข้อมูล";
    }

    if (isset($_SESSION['error'])) {
        header("Location: ../../../login/");
    } else {
        if (isset($_POST['referent']) && !empty($_POST['referent']))
            header("Location: " . $_POST['referent']);
        else
            header("Location: ../../../");
    }

}

if (isset($_GET['user']) && isset($_GET['pass'])) {
    $user = $_GET['user'];
    $pass = md5($_GET['pass']);

    //ดึงข้อมูลมาเช็คว่า $User ที่ตั้งรหัสผ่านเป็น $Pass มีในระบบรึเปล่า
    if ($stmt = $conn -> prepare('SELECT id,displayname FROM `user` WHERE username = ? AND password = ? LIMIT 1')) {
        $stmt->bind_param('ss', $user, $pass);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows == 1) {
            while ($row = $result->fetch_assoc()) {
                $_SESSION['id'] = $row['id'];
                $_SESSION['name'] = $row['displayname'];
            }
            echo "ACCEPT";
        } else {
            echo "WRONG PASSWORD";    
        }
        $stmt->free_result();
        $stmt->close();
    } else {
        echo "DATABASE ERROR";
    }
}
?>