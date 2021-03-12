<?php
    
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");

    require_once '../connect.php';
    require_once '../function.php';

    $id = isset($_GET['id']) ? "WHERE id = " . (int) $_GET['id'] : "";
    $arr = array();
    if ($stmt = $conn -> prepare("SELECT * FROM `problem` $id ORDER BY id")) {
        //$stmt->bind_param('ii', $page, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {

            while ($row = $result->fetch_assoc()) {
                $id = $row['id']; $name = $row['name']; $codename = $row['codename']; $score = $row['score']; $writer = $row['writer']; $memory = $row['memory']; $time = $row['time'];
                $prop = json_decode($row['properties'],true);
                $acceptType = array_key_exists("accept", $prop) ? $prop["accept"] : array("Python","Java","C","Cpp");
                $hide = array_key_exists("hide", $prop) ? $prop["hide"] : false;
                $rate = array_key_exists("rating", $prop) ? $prop["rating"] : 0;
                
                $e_arr = array(
                    "id" => $id,
                    "name" => $name,
                    "codename" => $codename,
                    "author" => $writer,
                    "score" => $score,
                    "memory" => $memory,
                    "time" => $time,
                    "properties" => array(
                    "accept" => $acceptType,
                    "hide" => $hide,
                    "rating" => array(
                        "value" => $rate,
                        "display" => rating($rate)
                    )),
                    "doc" => "../doc/$id-$codename.pdf",
                );

                array_push($arr, $e_arr);
            }
            $stmt->free_result();
            $stmt->close();  
        }
    }
    echo json_encode($arr, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

?>