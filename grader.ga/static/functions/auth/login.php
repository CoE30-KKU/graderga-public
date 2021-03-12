<?php
    require_once '../connect.php';
    require_once '../function.php';

if (isset($_POST['method']) && $_POST['method'] == 'loginPage') {
    $user = $_POST['login_username'];
    $pass = md5($_POST['login_password']);

    //ดึงข้อมูลมาเช็คว่า $User ที่ตั้งรหัสผ่านเป็น $Pass มีในระบบรึเปล่า
    if ($stmt = $conn -> prepare('SELECT id,displayname,properties FROM `user` WHERE username = ? AND password = ? LIMIT 1')) {
        $stmt->bind_param('ss', $user, $pass);
        $stmt->execute();
        $result = $stmt->get_result();
        $array = array();
        if ($result->num_rows == 1) {
            while ($row = $result->fetch_assoc()) {
                $_SESSION['id'] = $row['id'];
                $_SESSION['name'] = $row['displayname'];
                
                $_SESSION['swal_success'] = "เข้าสู่ระบบสำเร็จ";
                $_SESSION['swal_success_msg'] = "ยินดีต้อนรับ " . $row['displayname'] . "!";
                
                $rainbow = json_decode($row['properties'])->rainbow;
                if ($rainbow) $_SESSION['name'] = "<text class='rainbow'>" . $_SESSION['name'] . "</text>";
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

} else if (isset($_POST['method']) && $_POST['method'] == 'registerPage') {
    $user = $_POST['register_username'];
    $pass = md5($_POST['register_password']);
    $name = $_POST['register_name'];
    $email = $_POST['register_email'];

    if ($stmt = $conn -> prepare('SELECT id FROM `user` WHERE username = ? LIMIT 1')) {
        $stmt->bind_param('s', $user);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $_SESSION['error'] = "มีชื่อผู้ใช้งานหรืออีเมลนี้อยู่แล้วในระบบ";
            header("Location: ../../../register/");
        }
        $stmt->free_result();
        $stmt->close();
    } else {
        $_SESSION['error'] = "พบข้อผิดพลาดในการเข้าถึงฐานข้อมูล";
    }
    
    $id = latestIncrement($dbdatabase, 'user', $conn);
    if ($stmt = $conn -> prepare("INSERT INTO `user` (id, username, password, displayname, email) VALUES (?,?,?,?,?)")) {
        $stmt->bind_param('issss', $id, $user, $pass, $name, $email);
        if (!$stmt->execute()) {
            $_SESSION['swal_error'] = "พบข้อผิดพลาด";
            $_SESSION['swal_error_msg'] = "ไม่สามารถ Query Database ได้";
        } else {
            $_SESSION['id'] = $id;
            $_SESSION['name'] = $name;
            $_SESSION['swal_success'] = "สมัครสมาชิกสำเร็จ!";
            $_SESSION['swal_success_msg'] = "ยินดีต้อนรับ $name!";
        }
    } else {
        $_SESSION['error'] = "พบข้อผิดพลาดในการเข้าถึงฐานข้อมูล";
    }

    if (isset($_SESSION['error']))
        header("Location: ../../../register/");
    else
        header("Location: ../../../");    
}

if (isset($_GET['user']) && isset($_GET['pass'])) {
    $user = $_GET['user'];
    $pass = md5($_GET['pass']);
    if (isset($_GET['method']) && $_GET['method'] == "reset")
        $pass = $_GET['pass'];

    //ดึงข้อมูลมาเช็คว่า $User ที่ตั้งรหัสผ่านเป็น $Pass มีในระบบรึเปล่า
    if ($stmt = $conn -> prepare('SELECT id,displayname FROM `user` WHERE username = ? AND password = ? LIMIT 1')) {
        $stmt->bind_param('ss', $user, $pass);
        $stmt->execute();
        $result = $stmt->get_result();
        $array = array();
        if ($result->num_rows == 1) {
            while ($row = $result->fetch_assoc()) {
                $_SESSION['id'] = $row['id'];
                $_SESSION['name'] = $row['displayname'];
            }
            echo "ACCEPT";
            if (isset($_GET['method'])) {
                if ($_GET['method'] == "reset") {
                    header("Location: ../../../resetpassword/");
                }
            }
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