<?php
    
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");

    require_once '../connect.php';
    require_once '../function.php';

    $query = "";
    $query_arg = array();
    $id = isset($_GET['id']) ? array_push($query_arg, "id=" . (int) $_GET['id']) : "";
    $onlyWait = isset($_GET['wait']) ? array_push($query_arg, "result='W'") : "";
    $desc = empty($onlyWait) ? "DESC" : "ASC";
    $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 100;
    for($i = 0; $i < count($query_arg); $i++) {
        if ($i == 0) $query .= "WHERE " . $query_arg[$i];
        else $query .= " AND " . $query_arg[$i];
    }

    $arr = array();
    if ($stmt = $conn -> prepare("SELECT * FROM `submission` $query ORDER BY id $desc LIMIT $limit")) {
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {

            while ($row = $result->fetch_assoc()) {
                $script = (isset($_GET['key']) && $_GET['key'] == $private_key) || (isLogin() && (isAdmin($_SESSION['id'], $conn) || $row['user'] == $_SESSION['id'])) ? $row['script'] : "";
                $e_arr = array(
                    "id" => $row['id'],
                    "user" => $row['user'],
                    "problem" => $row['problem'],
                    "lang" => $row['lang'],
                    "script" => $script,
                    "result" => array(
                        "result" => $row['result'],
                        "score" => $row['score'],
                        "maxScore" => $row['maxScore'],
                        "runningTime" => $row['runningtime'],
                        "memory" => $row['memory'],
                        "upload" => $row['uploadtime']
                    )
                );

                array_push($arr, $e_arr);
            }
            $stmt->free_result();
            $stmt->close();  
        }
    }
    echo json_encode($arr, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

?>