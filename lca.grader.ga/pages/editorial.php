<?php $cat = array("Java"=>countCategory("Java", $conn), "Python"=>countCategory("Python", $conn), "C"=>countCategory("C",$conn), "LCA"=>countCategory("LCA",$conn),"General"=>countCategory("General",$conn),"Update"=>countCategory("Update",$conn)); ?>

<div class="container" style="padding-top: 88px;">
    <div class="container mb-3" id="container">
        <h1 class="display-4 font-weight-bold text-center text-coekku">Editorial</h1>
        <?php if (isLogin()) { ?><a href="../editorial/create" class="btn btn-coekku btn-sm">+ Add new editorial</a><?php } ?>
        <div class="row">
            <div class="col-12">
                <form action="../pages/editorial_search.php" method="GET" class="form-inline">
                <i class="fas fa-search"></i>
                <div class="flex-fill ml-2 mr-2">
                    <div class="md-form active-purple active-purple-2 mb-3">
                        <input action="../pages/editorial_search.php" method="GET" class="form-control w-100" type="text"
                            placeholder="Type anything to search..." aria-label="Search Bar" id="search"
                            name="search" value="<?php if (isset($_GET['search'])) echo $_GET['search']; ?>">
                    </div>
                </div>
                <button type="submit" value="search_submit" class="btn btn-coekku btn-sm">Search</button>
                </form>
            </div>
        </div>
        <h3 class="font-weight-bold text-coekku">
            <?php   if (isset($_GET['category'])) echo "Category: " . $_GET['category'];
                    else if (isset($_GET['search'])) echo "Anything with '" . $_GET['search'] . "'";
                    else echo "Latest Article"; ?>
        </h3>
        <div class="row">
            <div class="col-12 col-lg-8">
                <?php
                if (isset($_GET['category'])) {
                    $category = $_GET['category'];
                    $stmt = $conn->prepare("SELECT * FROM `editorial` WHERE JSON_EXTRACT(`properties`,'$.hide') = false AND JSON_EXTRACT(`properties`,'$.category') = '$category' ORDER BY JSON_EXTRACT(`properties`,'$.last_hide_updated')");
                } else if (isset($_GET['search'])) {
                    $search = "%" . $_GET['search'] . "%";
                    $stmt = $conn->prepare("SELECT * FROM `editorial` WHERE JSON_EXTRACT(`properties`,'$.hide') = false AND (title LIKE ? OR article LIKE ? OR JSON_EXTRACT(`properties`,'$.category') LIKE ? OR JSON_EXTRACT(`properties`,'$.author') LIKE ?) ORDER BY JSON_EXTRACT(`properties`,'$.last_hide_updated')");
                    $stmt->bind_param("ssss", $search, $search, $search, $search);
                } else {
                    $stmt = $conn->prepare("SELECT * FROM `editorial` WHERE JSON_EXTRACT(`properties`,'$.hide') = false ORDER BY JSON_EXTRACT(`properties`,'$.last_hide_updated') DESC LIMIT 7");
                }
                
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $postid = $row['id'];
                        $posttitle = $row['title'];

                        $Parsedown = new Parsedown();
                        $html = $Parsedown->text($row['article']);
                        $html = str_replace("\n","<br>",$html);
                        $html = str_replace("<img ","<img class='img-fluid' ",$html);
                        $postarticle = explode("<br>", $html);

                        $prop = json_decode($row['properties'], true); 
                
                        $hide = array_key_exists("hide", $prop) ? $prop["hide"] : false;
                        $last_hide_updated = array_key_exists("last_hide_updated", $prop) ? $prop["last_hide_updated"] : time();
                        $category = array_key_exists("category", $prop) ? $prop["category"] : 0;
                        $author = array_key_exists("author", $prop) ? $prop["author"] : null;
                        
                        ?>
                        <a href="../editorial/<?php echo $postid; ?>">
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h5 class="font-weight-bold text-coekku"><?php echo $posttitle; ?> <span class='badge badge-coekku'><?php echo strtoupper($category); ?></span></h5>
                                    <p><?php echo $postarticle[0]; echo "...<a href=\"../editorial/$postid\">อ่านเพิ่มเติม</a>"; ?></p>
                                    <small class="text-muted"><?php echo $author; ?></small>
                                </div>
                            </div>
                        </a>
                        <?php
                    }
                } else {
                    echo "<i>No recent article </i>😢";
                }
                
                ?>
            </div>
            <div class="col-12 col-lg-4">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="font-weight-bold text-coekku">Category</h5>
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