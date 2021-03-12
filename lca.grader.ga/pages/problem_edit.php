<?php needLogin();
    $probName = "";$probCodename = ""; $probScore = 100; $probRate = ""; $id = -1; $accept = ""; $hide = 0; $last_hide_updated = time(); $answer = "";
    if (isset($_GET['id'])) {
        needOwner($_GET['id'], $conn);
        $id = (int) $_GET['id'];
        if ($stmt = $conn -> prepare("SELECT * FROM `problem` WHERE id = ?")) {
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows == 1) {
                while ($row = $result->fetch_assoc()) {
                    $probName = $row['name']; $probCodename = $row['codename']; $probScore = $row['score']; $probAuthor = $row['author'];
                    
                    $prop = json_decode($row['properties'], true);

                    $hide = array_key_exists("hide", $prop) ? $prop["hide"] : false;
                    $last_hide_updated = array_key_exists("last_hide_updated", $prop) ? $prop["last_hide_updated"] : time();
                    $probRate = array_key_exists("rating", $prop) ? $prop["rating"] : 0;
                }
                $stmt->free_result();
                $stmt->close();  
            } else {
                header("Location: ../problem/");
            }
        } else {
            header("Location: ../problem/");
        }
        $answer = fread(fopen("../file/judge/prob/$id/answer.txt","r"), filesize("../file/judge/prob/$id/answer.txt"));

    }
    $probDoc = "static/elements/demo.pdf";
    if (isset($probCodename) && !empty($probCodename)) {
        $probDoc = "doc/$id-$probCodename";
    }

?>
<div class="container" style="padding-top: 88px;">
    <div class="container mb-3" id="container">
        <form method="post" action="../pages/problem_save.php<?php if (isset($_GET['id'])) echo '?id=' . $_GET['id']; ?>" enctype="multipart/form-data">
            <div class="font-weight-bold text-coekku">
                <div class="row">
                    <div class="col-12 col-md-6">
                        <div class="md-form">
                            <input type="text" id="name" name="name" class="form-control" value="<?php echo $probName; ?>" required/>
                            <label class="form-label" for="name">Problem Name</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="md-form">
                            <input type="text" id="codename" name="codename" class="form-control" value="<?php echo $probCodename; ?>" required />
                            <label class="form-label" for="codename">Codename</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="md-form">
                            <input type="text" id="author" name="author" class="form-control" disabled value="<?php if (!empty($probAuthor)) echo $probAuthor; else echo $_SESSION['name']; ?>"/>
                            <label class="form-label" for="author">Author</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-md-8">
                    <input type="file" accept=".pdf" class="mb-1" name="pdfPreview" id="pdfPreview">
                    <input type="hidden" name="probDoc" id="probDoc" value="<?php echo $probDoc; ?>"/></input>
                    <script>
                    $("#pdfPreview").on('change', function(){
                        var fd = new FormData();
                        var files = $('#pdfPreview')[0].files;
                        if(files.length > 0 ){
                            fd.append('file',files[0]);
                            $.ajax({
                                url: '../pages/upload.php',
                                type: 'post',
                                data: fd,
                                contentType: false,
                                processData: false,
                                success: function(response){
                                    if(response != 0){
                                        $("#pdfViewer").attr("src",response);
                                        $("#probDoc").val(response); 
                                    }else{
                                        alert('file not uploaded');
                                    }
                                },
                            });
                        }
                    });
                    </script>
                    <iframe
                        src="../vendor/pdf.js/web/viewer.html?file=../../../<?php echo $probDoc?>"
                        width="100%" height="600" class="z-depth-1" id="pdfViewer" name="pdfViewer"></iframe>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="font-weight-bold text-coekku">Language</h5>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="TXT" id="Plain Text" disabled checked name="lang[]">
                                <label class="form-check-label" for="Plain Text">LCA Custom Template</label>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="font-weight-bold text-coekku">Answer</small>&nbsp;
                            <a href="#drive_url" target="_blank"><i class="fas fa-question-circle"></i></a></h5>
                            <textarea class="form-control" id="answer" name="answer" rows="8" style="white-space: pre;" required><?php echo $answer; ?></textarea>
                            <input type="hidden" name="rating" id="rating" value="<?php echo $probRate; ?>"/>
                            <input type="hidden" name="hide" id="hide" value="<?php echo $hide; ?>"/>
                            <input type="hidden" name="last_hide_updated" id="last_hide_updated" value="<?php echo $last_hide_updated; ?>"/>
                        </div>
                    </div>
                    <button class="btn btn-coekku btn-block" type="submit" name="problem" value="<?php if (isset($_GET['id'])) echo "edit"; else echo "create"; ?>">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>