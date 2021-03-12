<footer id="footer" class="footer">
    <div class="container-fluid">
        <hr>
        <div class="text-center mb-3">
            Grader.ga - Made with <b style="color: salmon; ">Meow🐾</b> by <a href="https://www.facebook.com/p0ndja/" class="font-weight-bold">PondJaᵀᴴ</a> & <a href="https://www.facebook.com/Neptune.dreemurr" class="font-weight-bold">Nepumi</a>
            <br><small class="text-muted" style="opacity:0.75;"><?php $target = "../version.txt"; if (file_exists($target)) echo "Version " . fread(fopen($target, "r"),filesize($target)); ?> | <a href="//m.me/p0ndja">Report a bug</a> | <?php $end_time = microtime(TRUE); $time_taken =($end_time - $start_time)*1000; $time_taken = round($time_taken,5); echo 'Page generated in ' . $time_taken . ' ms.';?></small>
        </div>
    </div>
</footer>
<script>hljs.initHighlightingOnLoad();</script>
<script>
    $('input[type=text], input[type=password], input[type=email], input[type=url], input[type=tel], input[type=number], input[type=search], input[type=date], input[type=time], textarea').each(function (element, i) {
        if ((element.value !== undefined && element.value.length > 0) || $(this).attr('placeholder') !== undefined) {
            $(this).siblings('label').addClass('active');
        } else {
            $(this).siblings('label').removeClass('active');
        }
        $(this).trigger("change");
    });
    $('input[type=email]').val('test').siblings('label').addClass('active');
    
    // Tooltips Initialization
    $(document).ready(function () {
        $('.mdb-select').materialSelect();
        $('[data-toggle="tooltip"]').tooltip();
        $('.btn-floating').unbind('click');
        $('.fixed-action-btn').unbind('click');
        attachFooter();
        checkResult();
    });

    function checkResult() {
        setTimeout(function() {
            ($('[data-wait=true]').each(function(index) {
                var subID = $(this).data('sub-id');
                $(this).load('../pages/prob_result.php?id='+subID+"&time");
                if ($(this).html().indexOf("รอผลตรวจ...") === -1) {
                    $(this).removeAttr("data-wait");
                    console.log("Finished Juding " + subID + " -> " + $(this).html());  
                } else {
                    console.log("Waiting for Juding " + subID);  
                }
            }));
            checkResult();
        }, 1100)
    }

    document.addEventListener('DOMContentLoaded', (event) => {
        document.querySelectorAll('pre code').forEach((block) => {
            hljs.highlightBlock(block);
        });
    });

    // create an Observer instance
    const resizeObserver = new ResizeObserver(entries => attachFooter());

    // start observing a DOM node
    resizeObserver.observe(document.body);

    function attachFooter() {
        console.log($(document.body).height() + " | " + $(window).height());
        if ($(document.body).height()*1.11 < $(window).height()) {
            $('#footer').attr('style', 'position: fixed!important; bottom: 0px;');
        } else {
            $('#footer').removeAttr('style');
        }
    }

    $('.dropdown-menu').find('form').click(function (e) {
        e.stopPropagation();
    });

    $('.carouselsmoothanimated').on('slide.bs.carousel', function(e) {
        $(this).find('.carousel-inner').animate({
            height: $(e.relatedTarget).height()
        }, 500);
    });
</script>
<?php $_SESSION['isDarkProfile'] = 0; ?>
<?php mysqli_close($conn); ?>