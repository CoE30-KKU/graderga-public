<?php
    require_once '../static/functions/connect.php';
    require_once '../static/functions/function.php';
    $id = isset($_GET['id']) ? (int) $_GET['id'] : 1;
    if ($stmt = $conn -> prepare("SELECT * FROM `submission` WHERE id = ? LIMIT 1")) {
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows == 1) {
            while ($row = $result->fetch_assoc()) {
                $subID = $row['id'];
                $subUser = user($row['user'], $conn);
                $subProb = $row['problem'];
                $subLang = $row['lang'];
                $subResult = $row['result'] != 'W' ? $row['result']: 'รอผลตรวจ...';
                $subRuntime = $row['runningtime']; //ms
                $subMemory = $row['memory'] ? $row['memory'] . " MB": randomErrorMessage(); //MB
                $subUploadtime = $row['uploadtime']; ?>
                <p>User: <code><?php echo $subUser; ?></code>
                <br>Problem: <?php echo prob($subProb, $conn); ?>
                <br>Language: <code><?php echo $subLang; ?></code>
                <br>Result: <code <?php if ($row['result'] == 'W') echo "data-sub-id='$id' data-wait=true"; ?>><?php echo $subResult; ?></code>
                <br>Running Time: <code><?php echo $subRuntime; ?> ms</code>
                <!-- <br>Memory: <code><?php //echo $subMemory; ?></code> -->
                <br>Submit Time: <?php echo $subUploadtime; ?>
                </p>
            <?php }
            $stmt->free_result();
            $stmt->close();  
        }
    }
?>