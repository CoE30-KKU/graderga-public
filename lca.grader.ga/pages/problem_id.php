<?php
    $id = $_GET['id'];
    if ($stmt = $conn -> prepare("SELECT * FROM `problem` WHERE id = ?")) {
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows == 1) {
            while ($row = $result->fetch_assoc()) {
                $id = $row['id']; $name = $row['name']; $codename = $row['codename']; $score = $row['score']; $author = $row['author']; 
            
                $prop = json_decode($row['properties'],true);
                $hide = array_key_exists("hide", $prop) ? $prop["hide"] : false;
                $rate = array_key_exists("rating", $prop) ? $prop["rating"] : 0;

                $owner = isOwner($id, $conn);

                if ($hide && (!isLogin() || (!isAdmin($_SESSION['id'], $conn) && strcmp($_SESSION['std_id'], $author) !== 0)))
                    header("Location: ../problem/");
            }
            $stmt->free_result();
            $stmt->close();  
        } else {
            header("Location: ../problem/");
        }
        
    }
?>
<div class="container mb-3" style="padding-top: 88px;" id="container">
    <h2 class="font-weight-bold text-coekku"><?php echo $name; ?> <span
            class='badge badge-coekku'><?php echo $codename; ?></span>
        <?php if ($owner) { echo '<a href="../pages/problem_toggle_view.php?problem_id='.$id.'&hide='.$hide.'">'; if ($hide) { echo '<i class="fas fa-eye-slash"></i>'; } else { echo '<i class="fas fa-eye"></i>'; } echo '</a>'; } ?>
    </h2>
    <small class="text-muted"><?php echo $author; ?></small>
    <hr>
    <div class="row">
        <div class="col-12 col-lg-8">
            <a href="../problem/" class="float-left"><i class="fas fa-arrow-left"></i> ย้อนกลับ</a>
            <a target="_blank" href="../doc/<?php echo $id; ?>-<?php echo $codename; ?>"
                class="float-right">เปิดในแท็บใหม่ <i class="fas fa-location-arrow"></i></a>
            <iframe src="../vendor/pdf.js/web/viewer.html?file=../../../doc/<?php echo $id; ?>-<?php echo $codename; ?>"
                width="100%" height="650" class="z-depth-1 mb-3"></iframe>
        </div>
        <div class="col-12 col-lg-4">
            <div id="adminZone" class="mb-3">
                <?php if ($owner) { ?>
                <a href="../problem/edit-<?php echo $id; ?>" class="btn btn-sm btn-primary">Edit</a>
                <a class="btn btn-sm btn-warning"
                    onclick='swal({title: "ต้องการจะ Rejudge ข้อ <?php echo $id; ?> หรือไม่ ?",text: "การ Rejudge อาจส่งผลต่อ Database และประสิทธิภาพโดยรวม\nความเสียหายใด ๆ ที่เกิดขึ้น ผู้ Rejudge เป็นผู้รับผิดชอบเพียงผู้เดียว\n\n**โปรดใช้สติและมั่นใจก่อนกดปุ่ม Rejudge**",icon: "warning",buttons: true,dangerMode: true}).then((willDelete) => { if (willDelete) { window.location = "../pages/rejudge.php?problem_id=<?php echo $id; ?>";}});'>Rejudge</a>
                <?php } ?>
            </div>
            <div class="card mb-3">
                <div class="card-body">
                    <div class="card-text">
                        <h4 class="text-center">Difficulty: <?php echo rating($rate); ?></h4>
                        <div class='rating-stars text-center'>
                                    <ul id='stars'>
                                    Rate this problem: 
                                        <li class='star' title='Easy' data-value='1'>
                                            <i class='fas fa-bolt'></i>
                                        </li>
                                        <li class='star' title='Normal' data-value='2'>
                                            <i class='fas fa-bolt'></i>
                                        </li>
                                        <li class='star' title='Hard' data-value='3'>
                                            <i class='fas fa-bolt'></i>
                                        </li>
                                    </ul>
                                </div>
                            <script>
                                $(document).ready(function () {
                                    /* 1. Visualizing things on Hover - See next part for action on click */
                                    $('#stars li').on('mouseover', function () {
                                        var onStar = parseInt($(this).data('value'), 10); // The star currently mouse on
                                        // Now highlight all the stars that's not after the current hovered star
                                        $(this).parent().children('li.star').each(function (e) {
                                            if (e < onStar) {
                                                $(this).addClass('hover');
                                            } else {
                                                $(this).removeClass('hover');
                                            }
                                        });
                                    }).on('mouseout', function () {
                                        $(this).parent().children('li.star').each(function (e) {
                                            $(this).removeClass('hover');
                                        });
                                    });

                                    /* 2. Action to perform on click */
                                    var rated = false;
                                    var rate = 0;
                                    $('#stars li').on('click', function () {
                                        var onStar = parseInt($(this).data('value'),10); // The star currently selected
                                        if (rate != 0) onStar = rate;
                                        else rate = onStar;

                                        var stars = $(this).parent().children('li.star');
                                        for (i = 0; i < stars.length; i++) {
                                            $(stars[i]).removeClass('selected');
                                        }
                                        for (i = 0; i < onStar; i++) {
                                            $(stars[i]).addClass('selected');
                                        }
                                        // JUST RESPONSE (Not needed)
                                        var ratingValue = parseInt($('#stars li.selected').last().data('value'), 10);
                                        if (rated)
                                            alert("You're already rated!");
                                        else
                                            alert("You rate " + ratingValue + " this problem\nBut nothing gonna be happened, we're currently working in progress in this feature!");
                                        rated = true;
                                    });
                                });
                            </script>
                    </div>
                </div>
            </div>
            <?php if (!isLogin()) { ?>
            <a href="../login/" class='btn btn-coekku btn-block'>Login</a>
            <?php } else {?>
            <div class="card mb-3">
                <div class="card-body">
                    <form method="post" action="../pages/problem_user_submit.php" enctype="multipart/form-data">
                        <h5 class="font-weight-bold text-coekku">Submission</h5>
                        <textarea class="form-control" id="answer" name="answer" rows="8" style="white-space: pre;"
                            required></textarea>
                        <button type="submit" id="submitbtn" value="prob" name="submit"
                            class="btn btn-block btn-coekku btn-md" disabled>Submit</button>
                        <script>
                            $("#answer").on('change keyup paste', function () {
                                var ans = $('#answer').val();
                                if (ans.length > 0)
                                    $("#submitbtn").removeAttr("disabled");
                                else
                                    $("#submitbtn").prop("disabled", "disabled");
                            });
                        </script>
                        <input type="hidden" name="probID" value="<?php echo $id; ?>"/>
                        <input type="hidden" name="probCodename" value="<?php echo $codename; ?>"/>
                    </form>
                </div>
            </div>
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="font-weight-bold text-coekku">History</h5>
                    <div class="table-responsive" style="max-height: 248px;">
                        <table class="table table-sm table-hover w-100 d-table">
                            <thead>
                                <tr>
                                    <th scope="col">Timestamp</th>
                                    <th scope="col">Result</th>
                                </tr>
                            </thead>
                            <tbody class="text-nowrap">
                                <?php
                                $html = "";
                                if ($stmt = $conn -> prepare("SELECT `submission`.`id` as id,`submission`.`score` as score,`submission`.`maxScore` as maxScore,`submission`.`uploadtime` as uploadtime,`submission`.`result` as result,`problem`.`score` as probScore FROM `submission` INNER JOIN `problem` ON `problem`.`id` = `submission`.`problem` WHERE user = ? and problem = ? ORDER BY `id` DESC LIMIT 5")) {
                                    $user = $_SESSION['id'];
                                    $stmt->bind_param('ii', $user, $id);
                                    $stmt->execute();
                                    $result = $stmt->get_result();
                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            $subID = $row['id'];
                                            $subResult = $row['result'] != 'W' ? $row['result']: 'รอผลตรวจ...';
                                            $subScore = ($row['score']/$row['maxScore'])*$row['probScore'];
                                            //$subRuntime = $row['runningtime']/1000;
                                            $subUploadtime = str_replace("-", "/", $row['uploadtime']); ?>
                                <tr style="cursor: pointer;" class='launchModal' onclick='javascript:;'
                                    data-owner='true' data-toggle='modal' data-target='#modalPopup'
                                    data-title='Submission #<?php echo $subID; ?>' data-id='<?php echo $subID; ?>'>
                                    <th scope='row'><?php echo $subUploadtime; ?></th>
                                    <td <?php if ($row['result'] == 'W') echo "data-wait=true data-sub-id=" . $subID; ?>>
                                        <code><?php echo "$subResult ($subScore)"; ?></code></td>
                                </tr>
                                <?php }
                                        $stmt->free_result();
                                        $stmt->close();  
                                    } else {
                                        echo "<tr><td colspan='2' class='text-center'>No submission yet!</td></tr>";
                                    }
                                    echo $html;
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php } //End of isLogin()?>
        </div>
    </div>
</div>