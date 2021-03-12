<?php
    require_once '../static/functions/connect.php';
    require_once '../static/functions/function.php';

    $id = "";
    if (isLogin() && isAdmin($_SESSION['id'], $conn)) {
        if (isset($_POST['problem'])) {
            $isCreate = $_POST['problem'] == "create" ? 1 : 0; //Create(true) or Edit(false)
            $probName = $_POST['name'];
            $probCodename = $_POST['codename'];
            $probScore = $_POST['score'];
            $probTime = $_POST['time'];
            $probMemory = $_POST['memory'];
            $probAuthor = $_POST['writer'];
            
            $properties = json_encode(array(
                "hide" => (bool) $_POST['hide'],
                "last_hide_updated" => (int) $_POST['last_hide_updated'],
                "rating" => (int) $_POST['rating'],
                "accept" => $_POST['lang']
            ));
            
            $id = $isCreate ? latestIncrement($dbdatabase, 'problem', $conn) : $_GET['id'];

            if (isset($_FILES['pdfPreview']['name']) && $_FILES['pdfPreview']['name'] != "") {
                $name_file = $probCodename . ".pdf";
                $tmp_name = $_FILES['pdfPreview']['tmp_name'];
                $locate ="../file/judge/prob/$id/";
                if (!file_exists($locate))
                    if (!mkdir($locate))
                        die("Can't mkdir");
                
                if (!move_uploaded_file($tmp_name,$locate.$name_file)) die("Can't upload file");
            }

            //INSERT INTO table (id, name, age) VALUES(1, "A", 19) ON DUPLICATE KEY UPDATE name="A", age=19
            print_r(array($probName, $probCodename, $probScore, $probMemory, $probTime, $properties, $probAuthor));
            if ($isCreate) {
                if ($stmt = $conn -> prepare("INSERT INTO `problem` (name, codename, score, memory, time, writer, properties) VALUES (?,?,?,?,?,?,?)")) {
                    $stmt->bind_param('ssiiiss', $probName, $probCodename, $probScore, $probMemory, $probTime, $probAuthor, $properties);
                    if (!$stmt->execute()) {
                        $_SESSION['swal_error'] = "พบข้อผิดพลาด";
                        $_SESSION['swal_error_msg'] = "ไม่สามารถ Query Database ได้";
                        echo $conn->error;
                    } else {
                        $_SESSION['swal_success'] = "สำเร็จ!";
                        $_SESSION['swal_success_msg'] = "เพิ่มโจทย์ $probCodename แล้ว!";
                        echo "Created";
                    }
                } else {
                    $_SESSION['swal_error'] = "พบข้อผิดพลาด";
                    $_SESSION['swal_error_msg'] = "ไม่สามารถ Query Database ได้";
                    echo "Can't establish database";
                }
            } else {
                if ($stmt = $conn -> prepare("UPDATE `problem` SET name=?, codename=?, score=?, memory=?, time=?, writer=?, properties=? WHERE id = ?")) {
                    $stmt->bind_param('ssiiissi', $probName, $probCodename, $probScore, $probMemory, $probTime, $probAuthor, $properties, $id);
                    if (!$stmt->execute()) {
                        $_SESSION['swal_error'] = "พบข้อผิดพลาด";
                        $_SESSION['swal_error_msg'] = "ไม่สามารถ Query Database ได้";
                        die($conn->error);
                    } else {
                        $_SESSION['swal_success'] = "สำเร็จ!";
                        $_SESSION['swal_success_msg'] = "แก้ไขโจทย์ $probCodename แล้ว!";
                        echo "Created";
                    }
                } else {
                    $_SESSION['swal_error'] = "พบข้อผิดพลาด";
                    $_SESSION['swal_error_msg'] = "ไม่สามารถ Query Database ได้\n$conn->error";
                    echo "Can't establish database";
                }
            }

            if (isset($_FILES['testcase']['name']) && $_FILES['testcase']['name'] != "") {
                $name_file = $probCodename . ".zip";
                $tmp_name = $_FILES['testcase']['tmp_name'];
                $locate ="../file/judge/prob/$id/";
                if (!file_exists($locate)) {
                    if (!mkdir($locate)) die("Can't mkdir");
                } else {
                    $files = glob($locate . "*.{in,sol,zip}", GLOB_BRACE); // get all file names
                    foreach($files as $file){ // iterate files
                        if(is_file($file))
                            unlink($file); // delete file
                    }
                }
                if (!move_uploaded_file($tmp_name,$locate.$name_file)) die("Can't upload file");

                $zipFile = $locate.$name_file;

                $zip = new ZipArchive;
                $res = $zip->open($zipFile);
                if ($res === TRUE) {
                    $zip->extractTo($locate);
                    $zip->close();
                }
            }

        }
    }
    header("Location: ../problem/$id");
?>