<?php
    require_once '../static/functions/connect.php';
    require_once '../static/functions/function.php';

    if (isLogin()) {
        $userID = (int) $_SESSION['id'];
        $probID = (int) $_POST['probID'];
        $probCodename = $_POST['probCodename'];
        $fileName = time();
        $answer = $_POST['answer'];

        //Check directory
        if (!file_exists("../file/judge/upload/$userID/"))
            mkdir("../file/judge/upload/$userID/");
        
        if (!empty($answer)) {
            $userCodeLocate = "../file/judge/upload/$userID/$probCodename-$fileName.txt";
            $file = fopen($userCodeLocate,"w");
            if (!fwrite($file,$answer)) {
                $_SESSION['swal_error'] = "พบข้อผิดพลาด!";
                $_SESSION['swal_error_msg'] = "ERROR 99 : ไม่สามารถเขียน/อ่านไฟล์ได้";
            } else {
                fclose($file);
                //INSERT INTO table (id, name, age) VALUES(1, "A", 19) ON DUPLICATE KEY UPDATE name="A", age=19
                if ($stmt = $conn -> prepare("INSERT INTO `submission` (user, problem, script, result) VALUES (?,?,?,'W')")) {
                    $stmt->bind_param('iis', $userID, $probID, $userCodeLocate);
                    if (!$stmt->execute()) {
                        $_SESSION['swal_error'] = "พบข้อผิดพลาด";
                        $_SESSION['swal_error_msg'] = "ERROR 40 : ไม่สามารถ Query Database ได้\n$conn->error";
                    } else {
                        $_SESSION['swal_success'] = "สำเร็จ!";
                        $_SESSION['swal_success_msg'] = "ส่งคำตอบสำเร็จ";
                    }
                }
            }
        } else {
            $_SESSION['swal_error'] = "พบข้อผิดพลาด";
            $_SESSION['swal_error_msg'] = "ERROR 00 : Empty Submission"; 
        }
        header("Location: ../problem/$probID");
    }
?>