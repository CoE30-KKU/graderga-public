<?php $incoming = false; $contest = true; $countdown = 0; ?>
<div class="container mb-3" style="padding-top: 88px;">
    <?php
        if ($stmt = $conn -> prepare("SELECT * FROM `contest` WHERE CURRENT_TIMESTAMP() BETWEEN startTime AND endTime ORDER BY `id` LIMIT 1")) {
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $contest = true;
                $cont_title = $row['name'];
                $cont_prob = json_decode($row['problem']);
                $probList = "(" . implode(",",$cont_prob) . ")";
                $cont_start = $row['startTime'];
                $cont_end = $row['endTime'];
                $countdown = $cont_end;        
            } else {
                $contest = false;
            }
            $stmt->free_result();
            $stmt->close();  
        }

        if (!$contest) {
            if ($stmt = $conn -> prepare("SELECT * FROM `contest` WHERE CURRENT_TIMESTAMP() < startTime ORDER BY `startTime` LIMIT 1")) {
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $incoming = true;
                    $inc_cont_title = $row['name'];
                    $inc_cont_prob = $row['problem'];
                    $inc_cont_start = $row['startTime'];
                    $inc_cont_end = $row['endTime'];  
                    $countdown = $inc_cont_end;      
                } else {
                    $incoming = false;
                }
                $stmt->free_result();
                $stmt->close();  
            }
        }
        ?>
    <?php if ($contest) { ?>
    <div align="center">
        <h3 class="font-weight-bold text-coekku"><?php echo $cont_title; ?></h3>
        <h1 class="display-1 font-weight-bold" id="countdown">Loading..</h1>
        <p><?php echo $cont_start . ' GMT+7 to ' . $cont_end . ' GMT+7'; ?></p>
    </div>
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
    <script>
        $(document).ready(function () {
            $('#problemTable').DataTable({
                "lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "ทั้งหมด"] ],
                'columnDefs': [ {
                    'targets': [1,3], // column index (start from 0)
                    'orderable': false, // set orderable false for selected columns
                }]
            });
            $('.dataTables_length').addClass('bs-select');
        });
    </script>


    <?php } else if ($incoming) { ?>
    <div class="center" align="center">
        <h4>มีการแข่งขันที่กำลังจะเริ่มขึ้น...</h4>
        <h3 class="font-weight-bold text-coekku"><?php echo $inc_cont_title; ?></h3>
        <h1 class="display-1 font-weight-bold" id="countdown">Loading..</h1>
        <p><?php echo $inc_cont_start . ' GMT+7 to ' . $inc_cont_end . ' GMT+7'; ?></p>
        <a href="../scoreboard/" class="btn btn-coekku">ดูประวัติการแข่งขัน</a>
    </div>
    <?php } else { ?>
    <div class="center" align="center">
        <h1 class="font-weight-bold text-coekku">Contest</h1>
        <h4>ไม่มีการแข่งขันที่กำลังจะเกิดขึ้น</h4>
        <p>อย่าลืมฝึกทำโจทย์ด้วยแหละ !</p>
        <a href="../scoreboard/" class="btn btn-coekku">ดูประวัติการแข่งขัน</a>
    </div>
    <?php } ?>
</div>
<script>
    // Set the date we're counting down to
    var countDownDate = new Date("<?php echo $countdown; ?>").getTime();

    // Update the count down every 1 second
    var x = setInterval(function () {

        // Get today's date and time
        var now = new Date().getTime();

        // Find the distance between now and the count down date
        var distance = countDownDate - now;

        // Time calculations for days, hours, minutes and seconds
        var days = Math.floor(distance / (1000 * 60 * 60 * 24));
        var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((distance % (1000 * 60)) / 1000);

        // Display the result in the element with id="demo"
        var stackMessage = "";
        if (days) {
            stackMessage = days + "d " + hours + "h " + minutes + "m " + seconds + "s ";
        } else {
            if (hours < 10) hours = "0" + hours;
            if (minutes < 10) minutes = "0" + minutes;
            if (seconds < 10) seconds = "0" + seconds;
            stackMessage += hours + ":" + minutes + ":" + seconds;
        }

        document.getElementById("countdown").innerHTML = stackMessage;

        // If the count down is finished, write some text
        if (distance < 0) {
            clearInterval(x);
            document.getElementById("countdown").innerHTML = "EXPIRED";
            location.reload();
        }
    }, 1000);
</script>