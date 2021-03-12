<?php
    require_once '../static/functions/connect.php';
    require_once '../static/functions/function.php';

    $id = "";
    if (isLogin() && isAdmin($_SESSION['id'], $conn)) {
        if (isset($_POST['editorial'])) {
            $isCreate = $_POST['editorial'] == "create" ? 1 : 0; //Create(true) or Edit(false)
            $title = $_POST['title'];
            $article = $_POST['article'];
            
            $properties = json_encode(array(
                "hide" => (bool) $_POST['hide'],
                "last_hide_updated" => (int) $_POST['last_hide_updated'],
                "category" => $_POST['category'],
                "author" => $_POST['author']
            ));
            
            $id = $isCreate ? latestIncrement($dbdatabase, 'editorial', $conn) : $_GET['id'];

            //INSERT INTO table (id, name, age) VALUES(1, "A", 19) ON DUPLICATE KEY UPDATE name="A", age=19
            print_r(array("ID"=>$id, "Title"=>$title, "Article"=>$article, "Properties"=>$properties));
            if ($isCreate) {
                if ($stmt = $conn -> prepare("INSERT INTO `editorial` (title, article, properties) VALUES (?,?,?)")) {
                    $stmt->bind_param('sss', $title, $article, $properties);
                    if (!$stmt->execute()) {
                        $_SESSION['swal_error'] = "พบข้อผิดพลาด";
                        $_SESSION['swal_error_msg'] = "ERROR 40 : ไม่สามารถ Query Database ได้\n$conn->error";
                        echo $conn->error;
                    } else {
                        $_SESSION['swal_success'] = "สำเร็จ!";
                        $_SESSION['swal_success_msg'] = "สร้าง Editorial #$id เรียบร้อยแล้ว!";
                        echo "Created";
                    }
                } else {
                    $_SESSION['swal_error'] = "พบข้อผิดพลาด";
                    $_SESSION['swal_error_msg'] = "ERROR 40 : ไม่สามารถ Query Database ได้\n$conn->error";
                    echo "Can't establish database";
                }
            } else {
                if ($stmt = $conn -> prepare("UPDATE `editorial` SET title=?, article=?, properties=? WHERE id = ?")) {
                    $stmt->bind_param('sssi', $title, $article, $properties, $id);
                    if (!$stmt->execute()) {
                        $_SESSION['swal_error'] = "พบข้อผิดพลาด";
                        $_SESSION['swal_error_msg'] = "ERROR 40 : ไม่สามารถ Query Database ได้";
                        die($conn->error);
                    } else {
                        $_SESSION['swal_success'] = "สำเร็จ!";
                        $_SESSION['swal_success_msg'] = "แก้ไข Editorial #$id เรียบร้อยแล้ว!";
                        echo "Created";
                    }
                } else {
                    $_SESSION['swal_error'] = "พบข้อผิดพลาด";
                    $_SESSION['swal_error_msg'] = "ERROR 40 : ไม่สามารถ Query Database ได้\n$conn->error";
                    echo "Can't establish database";
                }
            }
        }
    }
    header("Location: ../editorial/$id");
?>