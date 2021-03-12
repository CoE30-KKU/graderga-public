<div class="container mb-3" style="padding-top: 88px;" id="container">
    <h1 class="font-weight-bold text-coekku text-center">Scoreboard</h1>
    <?php 
        if (!isset($_GET['id'])) header("Location: ../scoreboard/1");
        $select = "";
        if ($stmt = $conn -> prepare("SELECT `id`,`name` FROM `contest` WHERE CURRENT_TIMESTAMP() < endTime ORDER BY id DESC")) {
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $id = $row['id']; $name = $row['name'];
                    $select .= "<option value=$id>[$id] - $name</option>";
                }
            }
            $stmt->free_result();
            $stmt->close();  
        }

        if (isset($_GET['id'])) {
            if ($stmt = $conn -> prepare("SELECT `problem` FROM `contest` WHERE id = ?")) {
                $stmt->bind_param('i', $_GET['id']);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $probList = "(" . implode(",",json_decode($row['problem'])) . ")";
                    }
                }
                $stmt->free_result();
                $stmt->close();  
            }
        }
    ?>
    <?php if (isset($_GET['id'])) { ?>
    <div class="row">
        <div class="col-12 col-md-auto">
        <div class="table-responsive">
        <table class="table table-hover w-100 d-block d-md-table" id="problemTable">
            <thead>
                <tr class="text-nowrap">
                    <th scope="col" class="font-weight-bold text-coekku text-right">ID</th>
                    <th scope="col" class="font-weight-bold text-coekku">Task</th>
                    <th scope="col" class="font-weight-bold text-coekku">Rate</th>
                    <th scope="col" class="font-weight-bold text-coekku">Result</th>
                </tr>
            </thead>
            <tbody class="text-nowrap">
                <?php
                $html = "";
                #Get latest of each -> SELECT MAX(`id`) id,`user`,`problem`,`score`,`maxScore` FROM `submission` WHERE problem IN (1,2,3) GROUP by user, problem, score, maxScore
                # SELECT MAX(`id`) as id, user, (CASE WHEN problem = 1 THEN IFNULL(score,0) END) as prob_1 FROM `submission` WHERE problem IN (1) GROUP by id, user, prob_1
                $userList = array();
                if ($stmt = $conn -> prepare("SELECT id,name,properties,codename FROM `problem` WHERE id IN $probList ORDER BY id")) {
                    //$stmt->bind_param('s', $probList);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $id = $row['id']; $name = $row['name']; $codename = $row['codename'];
                            
                            $prop = json_decode($row['properties'],true);
                            $hide = array_key_exists("hide", $prop) ? $prop["hide"] : false;
                            $rate = array_key_exists("rating", $prop) ? $prop["rating"] : 0;

                            if (!$hide || (isLogin() && isAdmin($_SESSION['id'], $conn))) {
                                $lastResult = isLogin() ? lastResult($_SESSION['id'], $id, $conn) : "";
                                $html .= "<tr onclick='window.open(\"../problem/$id\")'>
                                    <th class='text-right' scope='row'><a href=\"../problem/$id\" target=\"_blank\">$id</a></th>
                                    <td><a href=\"../problem/$id\" target=\"_blank\">$name <span class='badge badge-coekku'>$codename</span></a></td>
                                    <td data-order='".$rate."'>".rating($rate)."</td>
                                    <td><code>$lastResult</code></td>
                                </tr>";
                            }
                        }
                        $stmt->free_result();
                        $stmt->close();  
                    }
                    echo $html;
                }
                ?>
            </tbody>
        </table>
    </div>
        </div>
        <div class="col-12 col-md-auto">
            <select class="select" data-filter="true">
                <?php echo $select; ?>
            </select>
            <div class="card mb-3">
                <div class="card-body">
                    <div class="card-text">
                        <h4 class="font-weight-bold text-coekku">Task</h4>
                        <ul>
                            <li>ตัวเลขที่สวยงาม</li>
                            <li>โรงแรมในฝัน</li>
                            <li>พ่อสั่งน้องขอ</li>
                        </ul>
                        //TODO Problem Link
                    </div>
                </div>
            </div>
            <div class="card mb-3">
                <div class="card-body">
                    <div class="card-text">
                        <h4 class="font-weight-bold text-coekku">Score</h4>
                        <table class="table table-responsive table-sm w-100 d-block d-md-table">
                            <thead>
                                <tr class="text-nowrap">
                                    <th scope="col" class="font-weight-bold text-coekku">User</th>
                                    <th scope="col" class="font-weight-bold text-coekku">Score</th>
                                </tr>
                            </thead>
                            <tbody class="text-nowrap">
                                <tr>
                                    <th scope="row">Nepumi</th>
                                    <td>100/100/10</td>
                                </tr>
                                <tr>
                                    <th scope="row">PondJa</th>
                                    <td>100/90/10</td>
                                </tr>
                                <tr>
                                    <th scope="row">...</th>
                                    <td>...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php } else if (empty($select)) { ?>
    <div class="center">
        <h1>ไม่เคยจัดการแข่งขันเลยยยยยยยยย</h1>
        <h4 class="text-center"><?php echo randomErrorMessage(); ?>
    </div>
    <?php } ?>
</div>