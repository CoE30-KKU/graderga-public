<?php declare(strict_types=1);

    require_once('connect.php');

    function latestIncrement($dbdatabase, $db, $conn) {
        return mysqli_fetch_array(mysqli_query($conn,"SELECT `AUTO_INCREMENT` FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '$dbdatabase' AND TABLE_NAME = '$db'"), MYSQLI_ASSOC)["AUTO_INCREMENT"];
    }
    function isLogin() {
        if (isset($_SESSION['id'])) return true;
        return false;
    }

    function isAdmin($id, $conn) {
        if (getUserdata($id, 'isAdmin', $conn)) return true;
        return false;
    }

    function countCategory($category, $conn) {
        if ($stmt = $conn-> prepare("SELECT count(id) AS cat FROM `editorial` WHERE JSON_EXTRACT(`properties`,'$.hide') = false AND JSON_EXTRACT(`properties`,'$.category') = ? ORDER BY JSON_EXTRACT(`properties`,'$.last_hide_updated')")) {
        $stmt->bind_param('s', $category);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows == 1) {
                while ($row = $result->fetch_assoc()) {
                    return $row["cat"];
                }
            }
        }
    }

    function getAnySQL($sql, $val, $key, $key_val, $conn) {
        if ($sql == null || $val == null || $key == null || $key_val == null || $conn == null) return false;
        return mysqli_fetch_array(mysqli_query($conn, "SELECT `$val` from `$sql` WHERE $key = '$key_val'"), MYSQLI_ASSOC)[$val];
    }

    function saveAnySQL($sql, $col, $val, $key, $key_val, $conn) {
        if ($sql == null || $key == null || $key_val == null || $conn == null) return false;
        return mysqli_query($conn, "UPDATE `$sql` SET `$col` = $val WHERE `$key` = '$key_val'");
    }

    function getUserdata($id, $data, $conn) {
        return getAnySQL('user', $data, 'id', $id, $conn);
    }
    //getUserdata('604019', 'username', $conn);

    function getUserID($input, $method, $conn) {
        return getAnySQL('user', 'id', $method, $input, $conn);
    }

    function saveUserdata($id, $data, $val, $conn) {
        if (saveAnySQL('user', $data, $val, 'id', $id, $conn)) return true;
        return false;
    }
    //saveUserdata('604019', 'username', 'PondJaTH', $conn);\

    function getProbdata($id, $data, $conn) {
        return getAnySQL('problem', $data, 'id', $id, $conn);
    }

    function saveProbdata($id, $data, $val, $conn) {
        if (saveAnySQL('problem', $data, $val, 'id', $id, $conn)) return true;
        return false;
    }

    function getSubmissiondata($id, $data, $conn) {
        return getAnySQL('submission', $data, 'id', $id, $conn);
    }

    function saveSubmissiondata($id, $data, $val, $conn) {
        if (saveAnySQL('submission', $data, $val, 'id', $id, $conn)) return true;
        return false;
    }

    /*
    function getContestdata($id, $data, $conn) {
        return getAnySQL('contest', $data, 'id', $id, $conn);
    }

    function saveContestdata($id, $data, $val, $conn) {
        if (saveAnySQL('contest', $data, $val, 'id', $id, $conn)) return true;
        return false;
    }
    */

    function isDarkmode() {
        if (isset($_SESSION['dark_mode']) && $_SESSION['dark_mode'] == true)
            return true;
        if (!isset($_SESSION['dark_mode']))
            $_SESSION['dark_mode'] = false;
        return false;
    }

    function isValidUserID($id, $conn) {
        $query = "SELECT `id` FROM `user` WHERE id = '$id'";
        $result = mysqli_query($conn, $query);
        if (mysqli_num_rows($result) > 0) return true;
        return false;
    }

    function isValidProbID($id, $conn) {
        $query = "SELECT `id` FROM `problem` WHERE id = '$id'";
        $result = mysqli_query($conn, $query);
        if (mysqli_num_rows($result) > 0) return true;
        return false;
    }

    function rating($rate) {
        switch($rate) {
            case 0:
                return "<text class='light-blue-text'>Peaceful</text>";
            case 1:
                return "<text class='text-primary'>Easy</text>";
            case 2:
                return "<text class='text-success'>Normal</text>";
            case 3:
                return "<text class='text-warning'>Hard</text>";
            case 4:
                return "<text class='text-danger'>Insane</text>";
            case 5:
                return "<text class='pink-text font-weight-bold'>Merciless</text>";
            default:
                return "<text class='text-muted'>Unrated</text>";
        }
    }

    function isPassed($uID, $pID, $conn) {
        $arr = lastSubmission($uID,$pID,$conn);
        if (!$arr) return 0; //Case not any submission yet.
        if ($arr['score'] == $arr['maxScore']) return 1; //Case full score.
        if ($arr['score'] != 0 && $arr['score'] < $arr['maxScore']) return -1;
    }

    function lastResult($uID, $pID, $conn) {
        $arr = lastSubmission($uID,$pID,$conn);
        if (!$arr) return " "; //Case not any submission yet.
        else return $arr['result'] . " (" . ($arr['score']/$arr['maxScore'])*$arr['probScore'] . ")";
    }

    function lastSubmission($uID, $pID, $conn) {
        if (!isValidUserID($uID, $conn) || !isValidProbID($pID, $conn)) return 0;
        if ($stmt = $conn -> prepare("SELECT `submission`.`result` AS result,`submission`.`score` AS score,`submission`.`maxScore` AS maxScore,`problem`.`score` AS probScore FROM `submission` INNER JOIN `problem` ON `submission`.`problem` = `problem`.`id` WHERE problem = ? AND user = ? ORDER BY `submission`.`id` DESC limit 1")) {
            $stmt->bind_param('ii', $pID, $uID);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows == 1) {
                while ($row = $result->fetch_assoc()) {
                    $arr = array();
                    $arr["score"] = $row['score'];
                    $arr["maxScore"] = $row['maxScore'];
                    $arr["result"] = $row['result'];
                    $arr["probScore"] = $row['probScore'];
                    return $arr;
                }
                $stmt->free_result();
                $stmt->close();  
            } else {
                return 0;
            }
        }
        
    }

    function user($id, $conn) {
        $rainbow = !empty(getUserdata($id,'properties',$conn)) ? json_decode(getUserdata($id,'properties',$conn))->rainbow : false;
        $name = getUserdata($id, 'displayname', $conn);
        if (isLogin() && isAdmin($_SESSION['id'], $conn))
            $name .= " (".getUserdata($id,'username', $conn).")";
        if ($rainbow)
            $name = '<text class="rainbow">'. $name . '</text>';
        return $name;
    }

    function prob($id, $conn) {
        return getProbdata($id, 'name', $conn)." <span class='badge badge-coekku'>".getProbdata($id, 'codename', $conn)."</span>";
    }

    function countScore($result, $full = 100) {
        return number_format((float) count_chars(strtoupper($result))[80]*($full/strlen($result)), 2, '.', '');
    }

    function randomLoading() {
        $targetDir = "light";
        if (isDarkmode()) $targetDir = "dark";
        $files = glob("../static/elements/loading/$targetDir/*.*", GLOB_BRACE);
        return $files[rand(0,count($files)-1)];
    }

    function getProfileIMG($conn) {
        if (!isLogin()) return "../static/elements/user.png";
        $uid = $_SESSION['id'];
        if ($stmt = $conn -> prepare("SELECT `profile` FROM `user` WHERE id = ?")) {
            $stmt->bind_param('i', $uid);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows == 1) {
                while ($row = $result->fetch_assoc()) {
                    return (!empty($row['profile']) ? $row['profile'] : "../static/elements/user.png");
                }
            }
        }
    }
?>
<?php
    function getClientIP() {
        if(!empty($_SERVER['HTTP_CLIENT_IP'])) return $_SERVER['HTTP_CLIENT_IP'];
        else if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) return $_SERVER['HTTP_X_FORWARDED_FOR'];
        return $_SERVER['REMOTE_ADDR'];
    }

    function randomErrorMessage() {
        $message = array(
            "(╯°□°）╯︵ ┻━┻",
            "┬─┬ ノ( ゜-゜ノ)",
            "ლ(ಠ益ಠლ)",
            "¯\_(ツ)_/¯",
            "‎(ﾉಥ益ಥ）ﾉ ┻━┻",
            "┬┴┬┴┤(･_├┬┴┬┴",
            "ᕙ(⇀‸↼‶)ᕗ",
            "(づ｡◕‿‿◕｡)づ",
            "(ノ^_^)ノ┻━┻ ┬─┬ ノ( ^_^ノ)",
            "(⌐■_■)","─=≡Σ(([ ⊐•̀⌂•́]⊐",
            "(　-_･)σ - - - - - - - - ･",
            "┌( ಠ_ಠ)┘",
            "♪ (｡´＿●`)ﾉ┌iiii┐ヾ(´○＿`*) ♪",
            "ᕙ( ͡° ͜ʖ ͡°)ᕗ",
            "(ÒДÓױ)"
        );
        return $message[rand(0,count($message)-1)];
    }

    function path_curTime() {
        date_default_timezone_set('Asia/Bangkok'); return date('Y/m/d', time());
    }

    function unformat_curTime() {
        date_default_timezone_set('Asia/Bangkok'); return date('YmdHis', time());
    }

    function curDate() {
        date_default_timezone_set('Asia/Bangkok'); return date('Y-m-d', time());
    }

    function curTime() {
        date_default_timezone_set('Asia/Bangkok'); return date('H:i:s', time());
    }

    function curFullTime() {
        date_default_timezone_set('Asia/Bangkok'); return date('Y-m-d H:i:s', time());
    }

    function sendFileToIMGHost($file) {
        $data = array(
            'img' => new CURLFile($file['tmp_name'],$file['type'], $file['name']),
        ); 
        
        //**Note :CURLFile class will work if you have PHP version >= 5**
        
         $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, 'https://img.p0nd.ga/upload.php');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_TIMEOUT, 86400); // 1 Day Timeout
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60000);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_REFERER, $_SERVER['HTTP_HOST']);
        
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            $msg = FALSE;
        } else {
            $msg = $response;
        }
        
        curl_close($ch);
        return $msg;
    }

    function generateRandom($length = 16) {
        $characters = md5(time());
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }
?>

<?php
    function needLogin() {
    if (!isLogin()) {?>
<script>
    swal({
        title: "ACCESS DENIED",
        text: "You need to logged-in!",
        icon: "error"
    }).then(function () {
        <?php $_SESSION['error'] = "กรุณาเข้าสู่ระบบก่อนดำเนินการต่อ"; ?>
        window.location = "../login/";
    });
</script>
<?php die(); }} ?>

<?php
    function needAdmin($conn) {
    if (!isLogin()) { needLogin(); die(); return false; }
    if (!isAdmin($_SESSION['id'], $conn)) { ?>
<script>
    swal({
        title: "ACCESS DENIED",
        text: "You don't have enough permission!",
        icon: "warning"
    });
</script>
<?php die(); return false;}
        return true;
    }
?>
<?php function back() {
    if (isset($_SERVER["HTTP_REFERER"])) {
        header("Location: " . $_SERVER["HTTP_REFERER"]);
    } else {
        home();
    }
    die();
    } ?>
<?php function home() {
    header("Location: ../home/");
} ?>
<?php function logout() { ?>
    <script>
        swal({
            title: "ออกจากระบบ ?",
            text: "คุณต้องการออกจากระบบหรือไม่?",
            icon: "warning",
            buttons: true,
            dangerMode: true}).then((willDelete) => {
                if (willDelete) {
                    window.location = "../logout/";
                }
            });
</script>
<?php } ?>

<?php function deletePost($id) { ?>
    <script>
        swal({
            title: "ลบข่าวหรือไม่ ?",
            text: "หลังจากที่ลบแล้ว ข่าวนี้จะไม่สามารถกู้คืนได้!",
            icon: "warning",
            buttons: true,
            dangerMode: true}).then((willDelete) => {
                if (willDelete) {
                    window.location = "../post/delete.php?id=<?php echo $id; ?>";
                }
            });
    </script>
<?php } ?>
<?php function warningSwal($title,$name) { ?>
    <script>
    swal({
        title: "<?php echo $title; ?>",
        text: "<?php echo $name; ?>",
        icon: "warning"
    });
    </script>
<?php } ?>
<?php function errorSwal($title,$name) { ?>
    <script>
    swal({
        title: "<?php echo $title; ?>",
        text: "<?php echo $name; ?>",
        icon: "error"
    });
    </script>
<?php } ?>
<?php function successSwal($title,$name) { ?>
    <script>
    swal({
        title: "<?php echo $title; ?>",
        text: "<?php echo $name; ?>",
        icon: "success"
    });
    </script>
<?php } ?>
<?php function debug($message) { echo $message; } ?>

<?php
    function startsWith($haystack, $needle) {
        return substr_compare($haystack, $needle, 0, strlen($needle)) === 0;
    }
    function endsWith($haystack, $needle) {
        return substr_compare($haystack, $needle, -strlen($needle)) === 0;
    }
    
    if (isLogin() && !isValidUserID($_SESSION['id'], $conn)) {
        session_destroy();
        header("Location: ../home/");
    }
?>
