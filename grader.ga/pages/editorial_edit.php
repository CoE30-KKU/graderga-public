<?php 
    needAdmin($conn);

    $id = -1; $title = ""; $article = ""; $prop = ""; $category = "general"; $hide = 0; $last_hide_updated = time(); $author = "";
    if (isset($_GET['id'])) {
        //Case edit
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
                    $author = array_key_exists("author", $prop) ? $prop["author"] : $_SESSION['name'];
                }
                $stmt->free_result();
                $stmt->close();  
            } else {
                header("Location: ../editorial/");
            }
        } else {
            header("Location: ../editorial/");
        }
    }
?>
<div class="container" style="padding-top: 88px;">
    <div class="container mb-3" id="container">
        <form action="../pages/editorial_save.php<?php if (isset($_GET['id'])) echo '?id=' . $_GET['id']; ?>" method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-12 col-md-8">
                    <div id="editormd">
                        <textarea id="AYAYA" name="article"><?php echo $article; ?></textarea>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="font-weight-bold text-coekku">Settings</h5>
                            <div class="md-form">
                                <input type="text" id="title" name="title" class="form-control" value="<?php echo $title; ?>" required/>
                                <label class="form-label" for="Title">Title</label>
                            </div>
                            <div class="md-form">
                                <label for="category">Category</label>
                                <select class="mdb-select md-form colorful-select dropdown-primary" id="category" name="category" required>
                                    <optgroup label="- หมวดทั่วไป -" id="general">
                                        <option value="General">General</option>
                                        <option value="Update">Update</option>
                                    </optgroup>
                                    <optgroup label="- หมวดโปรแกรมมิ่ง -" id="general">
                                        <option value="C">C และ C++</option>
                                        <option value="Java">Java</option>
                                        <option value="Python">Python</option>
                                        <option value="LCA">Linear Circuit Analysis</option>
                                    </optgroup>
                                </select>
                                <script>
                                    $('#category option[value=<?php echo $category; ?>]').attr('selected', 'selected');
                                </script>
                            </div>
                            <div class="md-form">
                                <input type="text" id="author" name="author" class="form-control" value="<?php echo $author; ?>"/>
                                <label class="form-label" for="author">Author</label>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="hide" id="hide" value="<?php echo $hide; ?>"/>
                    <input type="hidden" name="last_hide_updated" id="last_hide_updated" value="<?php echo $last_hide_updated; ?>"/>
                    <button class="btn btn-coekku btn-block" type="submit" name="editorial" value="<?php if (isset($_GET['id'])) echo "edit"; else echo "create"; ?>">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>


<script type="text/javascript">
    $(function () {
        var editor = editormd("editormd", {
            width: "100%",
            height: "700",
            path: "../vendor/editor.md/lib/",
            theme : "<?php if (isDarkmode()) echo "dark"; else echo "default"; ?>",
            previewTheme : "<?php if (isDarkmode()) echo "dark"; else echo "default"; ?>",
            editorTheme : "<?php if (isDarkmode()) echo "monokai"; else echo "default"; ?>",
            toolbarIcons : function() {
                return [
                    "undo", "redo", "|",
                    "bold", "del", "italic", "quote", "|",
                    "h1", "h2", "h3", "h4", "h5", "h6", "|",
                    "list-ul", "list-ol", "hr", "|",
                    "link", "reference-link", "image", "code", "preformatted-text", "code-block", "table", "|",
                    "watch", "preview", "search", "|",
                    "help", "info"
                ];
            }
        });
    });
</script>