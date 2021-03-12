<?php require_once 'connect.php'; ?>
<?php require_once 'function.php'; ?>
<?php 
    if (isDarkmode()) {
        $_SESSION['dark_mode'] = false;
    } else {
        $_SESSION['dark_mode'] = true;
    }
    back();
?>