<?php
    $id = $_GET['id'];
    if (isset($_GET['id'])) {
        $id = (int) $_GET['id'];
        if ($stmt = $conn -> prepare("SELECT * FROM `editorial` WHERE id = ?")) {
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows == 1) {
                while ($row = $result->fetch_assoc()) {
                    $title = $row['title']; $article = $row['article']; $prop = empty($row['properties']) ? array() : json_decode($row['properties'], true);

                    $hide = array_key_exists("hide", $prop) ? $prop["hide"] : false;
                    $last_hide_updated = array_key_exists("last_hide_updated", $prop) ? $prop["last_hide_updated"] : time();
                    $category = array_key_exists("category", $prop) ? $prop["category"] : 0;
                    $author = array_key_exists("author", $prop) ? $prop["author"] : null;
                }
                $stmt->free_result();
                $stmt->close();  
            } else {
                header("Location: ../editorial/");
            }
        } else {
            header("Location: ../editorial/");
        }
    } else {
        header("Location: ../editorial/");
    }
?>
<div class="container" style="padding-top: 88px;">
    <div class="container mb-3" id="container">
        <h2 class="font-weight-bold text-coekku"><?php echo $title; ?> <span
            class='badge badge-coekku'><?php echo strtoupper($category); ?></span>
            <?php if (isLogin() && isAdmin($_SESSION['id'], $conn)) { 
                echo '<a href="../pages/editorial_toggle_view.php?editorial_id='.$id.'&hide='.$hide.'">'; 
                if ($hide) { echo '<i class="fas fa-eye-slash"></i>'; } else { echo '<i class="fas fa-eye"></i>'; } echo '</a>'; 
                echo '&nbsp;<a href="../editorial/edit-'.$id.'"><i class="fas fa-pencil-alt"></i></a>';
            } ?>        </h2>
        <small class="text-muted"><?php echo $author; ?></small>
        <hr>
        <div class="row">
            <div class="col-12 col-lg-8">
                <a href="../editorial/" class="float-left"><i class="fas fa-arrow-left"></i> ย้อนกลับ</a><br>
                <div class="card mb-3">
                    <div class="card-body">
                        <?php $Parsedown = new Parsedown();
                        $html = $Parsedown->text($article);
                        $html = str_replace("\n","<br>",$html);
                        $html = str_replace("<img ","<img class='img-fluid' ",$html);
                        echo $html;
                        ?>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-4">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="font-weight-bold text-coekku">Category</h5>
                        <?php $cat = array("Java"=>countCategory("Java", $conn), "Python"=>countCategory("Python", $conn), "C"=>countCategory("C",$conn), "LCA"=>countCategory("LCA",$conn),"General"=>countCategory("General",$conn),"Update"=>countCategory("Update",$conn)); ?>
                        <p>
                            <ul>
                                <li><a href="../editorial/category=Java">Java (<?php echo $cat["Java"]; ?>)</a></li>
                                <li><a href="../editorial/category=Python">Python (<?php echo $cat["Python"]; ?>)</a></li>
                                <li><a href="../editorial/category=C">C/C++ (<?php echo $cat["C"]; ?>)</a></li>
                                <li><a href="../editorial/category=LCA">LCA (<?php echo $cat["LCA"]; ?>)</a></li>
                                <li><a href="../editorial/category=General">General (<?php echo $cat["General"]; ?>)</a></li>
                                <li><a href="../editorial/category=Update">Update (<?php echo $cat["Update"]; ?>)</a></li>
                            </ul>
                        </p>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h5 class="font-weight-bold text-coekku">Latest Article</h5>
                        <p>
                                <?php
                                if ($stmt=$conn->prepare("SELECT title,id FROM `editorial` WHERE JSON_EXTRACT(`properties`,'$.hide') = false ORDER BY JSON_EXTRACT(`properties`,'$.last_hide_updated') DESC LIMIT 7")) {
                                    $stmt->execute();
                                    $result = $stmt->get_result();
                                    if ($result->num_rows > 0) {
                                        echo '<ul>';
                                        while ($row = $result->fetch_assoc()) {
                                            $postid = $row['id'];
                                            $posttitle = $row['title'];
                                            echo "<li><a href=\"../editorial/$postid\">$posttitle</a></li>";
                                        }
                                        echo '</ul>';
                                    } else {
                                        echo "<i>No recent article</i> 😢";
                                    }
                                }
                                ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
