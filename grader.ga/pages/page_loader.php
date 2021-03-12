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
    <?php if ((int) date("dm") == 2212) { ?>
    <div id="watermark" class="text-right text-danger">Happy birthday Grader.ga!<br>♪ (｡´＿●`)ﾉ┌iiii┐ヾ(´○＿`*) ♪</div>
    <?php } ?>
    <?php if ((int) date("dm") == 1103) { ?>
    <div id="watermark" class="text-right text-danger">Happy Birthday PondJaᵀᴴ!<br>♪ (｡´＿●`)ﾉ┌iiii┐ヾ(´○＿`*) ♪</div>
    <?php } ?>
    <?php if ((int) date("dm") == 810) { ?>
    <div id="watermark" class="text-right text-danger">Happy Birthday Nepumi!<br>♪ (｡´＿●`)ﾉ┌iiii┐ヾ(´○＿`*) ♪</div>
    <?php } ?>
</body>

</html>