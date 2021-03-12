<?php
    require_once '../static/functions/connect.php';
    require_once '../static/functions/function.php';
    $problem_id = "";
    if (isLogin() && isAdmin($_SESSION['id'], $conn) && isset($_GET['problem_id'])) {
        $problem_id = (int) $_GET['problem_id'];
        if ($stmt = $conn -> prepare("UPDATE `submission` SET result='W' WHERE problem = ?")) {
            $stmt->bind_param('i', $problem_id);
            if (!$stmt->execute()) {
                $_SESSION['swal_error'] = "พบข้อผิดพลาด";
                $_SESSION['swal_error_msg'] = "ไม่สามารถ Query Database ได้";
                echo $conn->error;
            } else {
                $_SESSION['swal_success'] = "สำเร็จ!";
                $_SESSION['swal_success_msg'] = "Rejudge โจทย์ข้อ #$problem_id แล้ว";
                echo "Created";
            }
        } else {
            echo "Can't establish database";
        }
    }
    header("Location: ../problem/$problem_id");
?>