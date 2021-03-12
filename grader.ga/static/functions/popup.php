<script>
    $('.launchModal').on('click', function () {
        $('#modalTitle').html('Loading...');
        $('#modalBody').html('<div class="d-flex justify-content-center"><img class="img-fluid" align="center" src="<?php echo randomLoading(); ?>"></div>');
        $('#modalBodyCode').html("<div></div>");
        var title = $(this).data('title');
        var subID = $(this).data('id');
        var userID = $(this).data('uid');
        var owner = $(this).data('owner');
            $.ajax({
                type: 'GET',
                url: '../pages/submission_gen.php',
                data: {
                    'id': subID
                },
                success: function (data) {
                    $('#modalBody').html(data);
                }
            }).then(function() {
                if (owner) {
                    $.ajax({
                        type: 'GET',
                        url: '../pages/submission_code.php?target=' + subID,
                        data: {
                            'id': subID
                        },
                        success: function (data) {
                            $('#modalBodyCode').html('<pre><code>' + data + '</code></pre>');
                            $('pre > code').each(function() {
                                hljs.highlightBlock(this);
                            });
                        }
                    });
                }
            }).then(function() {
                $('#modalTitle').html(title);
            });
        });
</script>
<!-- Popup Modal -->
<div class="modal animated fade" id="modalPopup" name="modalPopup" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-notify modal-coekku modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modalBodyBody">
                <div id="modalBody"></div>
                <div id="modalBodyCode"></div>
            </div>
        </div>
    </div>
</div>
<!-- Popup Modal -->
<?php 
    if (isset($_SESSION['swal_error']) && isset($_SESSION['swal_error_msg'])) { 
        errorSwal($_SESSION['swal_error'],$_SESSION['swal_error_msg']);
        $_SESSION['swal_error'] = null;
        $_SESSION['swal_error_msg'] = null;
    }
?>
<?php 
    if (isset($_SESSION['swal_warning']) && isset($_SESSION['swal_warning_msg'])) { 
        warningSwal($_SESSION['swal_warning'],$_SESSION['swal_warning_msg']);
        $_SESSION['swal_warning'] = null;
        $_SESSION['swal_warning_msg'] = null;
    }
?>
<?php 
    if (isset($_SESSION['swal_success'])) { 
        successSwal($_SESSION['swal_success'],$_SESSION['swal_success_msg']);
        $_SESSION['swal_success'] = null;
        $_SESSION['swal_success_msg'] = null;
    }
?>
<script>
    $("#logoutBtn").click(function () {
        swal({
            title: "ออกจากระบบ ?",
            text: "คุณต้องการออกจากระบบหรือไม่?",
            icon: "warning",
            buttons: true,
            dangerMode: true
        }).then((willDelete) => {
            if (willDelete) {
                window.location = "../logout/";
            }
        });
    });
</script>