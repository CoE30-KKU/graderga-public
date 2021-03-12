<?php 
    require_once '../static/functions/connect.php';
    require_once '../static/functions/function.php';
    require_once '../vendor/parsedown/Parsedown.php';
?>

<!DOCTYPE html>
<html lang="th" prefix="og:http://ogp.me/ns#">

<head>
    <?php require_once '../static/functions/head.php'; ?>
</head>
<?php require_once '../static/functions/navbar.php'; ?>
<body>
    <?php   if (isDarkmode()) { ?>
                <script>document.body.setAttribute("data-theme", "dark")</script>
    <?php   } else { ?>
                <script>document.body.removeAttribute("data-theme")</script>
    <?php   } ?>
    <?php if (isset($_GET['target']) && file_exists($_GET['target'])) require_once $_GET['target']; ?>
    <?php require_once '../static/functions/popup.php'; ?>
    <?php require_once '../static/functions/footer.php'; ?>
    <?php if (date("dm") == 2212) { ?>
    <div id="watermark" class="text-right text-danger"><small>Happy birthday Grader.ga!</small><br>♪ (｡´＿●`)ﾉ┌iiii┐ヾ(´○＿`*) ♪</div>
    <?php } ?>
</body>

</html>