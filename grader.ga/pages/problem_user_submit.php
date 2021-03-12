<?php
    require_once '../static/functions/connect.php';
    require_once '../static/functions/function.php';

    if (isLogin()) {
        $userID = $_SESSION['id'];
        $probID = $_POST['probID'];
        $probCodename = $_POST['probCodename'];
        $userCodeLang = $_POST['lang'];
        $fileName = time();
        if (isset($_FILES['submission']['name']) && $_FILES['submission']['name'] != "") {
            $file = $_FILES['submission']['name'];
            $tmp = $_FILES['submission']['tmp_name'];
            
            $ext = pathinfo($file, PATHINFO_EXTENSION);
            $name_file = "$probCodename-$fileName.$ext";
            
            $locate ="../file/judge/upload/$userID/";
            print_r($locate);
            if (!file_exists($locate)) {
                if (!mkdir($locate)) die("Can't mkdir");
            }
            if (!move_uploaded_file($tmp,$locate.$name_file)) die("Can't upload file");
            $userCodeLocate = $locate.$name_file;


            //INSERT INTO table (id, name, age) VALUES(1, "A", 19) ON DUPLICATE KEY UPDATE name="A", age=19
            if ($stmt = $conn -> prepare("INSERT INTO `submission` (user, problem, lang, script, result) VALUES (?,?,?,?,'W')")) {
                $stmt->bind_param('iiss', $userID, $probID, $userCodeLang, $userCodeLocate);
                if (!$stmt->execute()) {
                    $_SESSION['swal_error'] = "พบข้อผิดพลาด";
                    $_SESSION['swal_error_msg'] = "ไม่สามารถ Query Database ได้";
                } else {
                    $_SESSION['swal_success'] = "สำเร็จ!";
                    $_SESSION['swal_success_msg'] = "ส่งโค้ตสำเร็จ";
                }
            }

        } else {
            $_SESSION['swal_error'] = "พบข้อผิดพลาด";
            $_SESSION['swal_error_msg'] = "ไม่พบไฟล์";
        }
        header("Location: ../problem/$probID");
    }
?>