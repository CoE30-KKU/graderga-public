<?php
    require_once '../static/functions/connect.php';
    $subID = (int) $_GET['target'];
    $r = "FILE NOT FOUND";
    if ($stmt = $conn -> prepare("SELECT `result`,`comment`,`script` FROM `submission` WHERE id = ? LIMIT 1")) {
        $stmt->bind_param('i', $subID);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows == 1) {
            while ($row = $result->fetch_assoc()) {
                if (file_exists($row['script'])) {
                    $r = file_get_contents($row['script']);
                    $r = str_replace("<", "&lt;", $r); //Make browser don't think < is the start of html tag
                    $r = str_replace(">", "&gt;", $r); //Make browser don't think < is the end of html tag
                    echo ($r);
                    if ($row['comment'] != "End of Test") {
                        echo "\n\n\n//--------------------[JUDGE RESPONSE]--------------------\n" . $row['comment'] . "\n//--------------------[JUDGE RESPONSE]--------------------";
                    }
                    die();
                } else {
                    die("FILE NOT FOUND.");
                }
            }
        } else {
            die("FILE NOT FOUND.");
        }
    }
    echo $r;
?>