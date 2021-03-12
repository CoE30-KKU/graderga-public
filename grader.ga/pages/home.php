<?php if (in_array((int) date("dm"), array(2212,1103,810))) { ?><div id="emojiRain"></div><script src="../vendor/emojirain.js"></script><?php } ?>
<div class="homepage" style="padding-top: 64px;">
    <div class="container-fluid h-100 w-100">
        <div class="h-100 w-100 row align-items-center">
            <div class="d-none d-md-block col-md-1"></div>
            <div class="col-12 col-md-5">
                <div class="bounceInDown animated">
                    <h1 class="font-weight-bold text-coekku display-4">Grader.ga</h1>
                    <h4 class="font-weight-normal">The Computer Engineering of <b class="text-nowrap">Khon Kaen University</b><br>Student-Made grader.</h4>
                    <a class="btn btn-coekku" href="../problem/">เริ่มทำโจทย์กันเลย !</a>
                    <a class="btn btn-coekku" target="_blank" href="https://drive.google.com/file/d/19aNSPCPxMvg8BQVI9z_P9ELP4OmLSEtO/view?usp=drivesdk">วิธีการใช้งาน Grader.ga</a>
                    <?php
                    if ($stmt = $conn -> prepare("SELECT `codename`,`id`,`name`,`properties` FROM `problem` WHERE JSON_EXTRACT(`properties`,'$.hide') = 0 AND UNIX_TIMESTAMP() - JSON_EXTRACT(`properties`,'$.last_hide_updated') <= 604800 AND JSON_EXTRACT(`properties`,'$.last_hide_updated') > 0 ORDER BY JSON_EXTRACT(`properties`,'$.last_hide_updated') DESC limit 7")) {
                        $stmt->execute();
                        $result = $stmt->get_result();
                        if ($result->num_rows > 0) { $html = "";?>
                            <div class="bounceInLeft delay-1s animated">
                            <h5 class="rainbow mt-3">โจทย์มาใหม่!!</h5>
                            <div class="table-responsive">
                                <table class="table table-hover table-sm d-block d-md-table" id="problemTable">
                                    <thead>
                                        <tr class="text-nowrap">
                                            <th scope="col" class="font-weight-bold text-coekku text-right">ID</th>
                                            <th scope="col" class="font-weight-bold text-coekku">Task</th>
                                            <th scope="col" class="font-weight-bold text-coekku">Rate</th>
                                        </tr>
                                    </thead>
                            <?php while ($row = $result->fetch_assoc()) {
                                $id = $row['id']; $name = $row['name']; $codename = $row['codename']; 
                                    
                                    $prop = json_decode($row['properties'],true);
                                    $rate = array_key_exists("rating", $prop) ? $prop["rating"] : 0;
                                
                                    $html .= "<tr onclick='window.open(\"../problem/$id\")'>
                                        <th class='text-right' scope='row'><a href=\"../problem/$id\" target=\"_blank\">$id</a></th>
                                        <td><a href=\"../problem/$id\" target=\"_blank\">$name <span class='badge badge-coekku'>$codename</span></a></td>
                                        <td data-order='".$rate."'>".rating($rate)."</td>
                                    </tr>";
                                }
                                echo $html; ?>
                            </table></div></div>
                            <?php }
                            $stmt->free_result();
                            $stmt->close();  
                        } ?>
                </div>
                <div class="fadeIn animated">
                    <?php
                        $files = glob("../static/elements/index/*.*", GLOB_BRACE);
                        $targetSrc = $files[rand(0,count($files)-1)];
                    ?>
                    <img src="<?php echo $targetSrc; ?>" class="mt-3 img-fluid w-100 d-block d-md-none">
                </div>
            </div>
            <div class="col-12 col-md-6 d-none d-md-block">
                <div class="fadeIn animated">
                    <img src="<?php echo $targetSrc; ?>" class="img-fluid w-100">
                </div>
            </div>
        </div>
    </div>
</div>