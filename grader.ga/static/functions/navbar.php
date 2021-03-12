<nav class="navbar navbar-expand-lg navbar-normal fixed-top navbar-transparent z-depth-0" id="nav" role="navigation">
    <a class="navbar-brand" href="../home/"><img src="../static/elements/logo/logo.png" width="32" alt="PharmMDKKU" align="center"></a>
    <button class="navbar-toggler navbar-<?php if (isDarkmode()) echo "dark"; else echo "light"; ?>" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <a class="nav-link" href="../home/">Home <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../problem/">Problem</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../submission/">Submission</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../contest/">Contest</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../editorial/">Editorial</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../donation/">Donation</a>
            </li>
        </ul>
        <div class="my-2 my-lg-0">
            <ul class="nav navbar-nav nav-flex-icons ml-auto">
                <li class="nav-item">
                    <?php if (isDarkmode()) { ?><a href="../static/functions/darkmode.php" class="nav-link"><i class="fas fa-sun"></i></a></a>
                    <?php } else { ?><a href="../static/functions/darkmode.php" class="nav-link"><i class="far fa-moon"></i></a><?php } ?>
                </li>
                <?php if (isset($_SESSION['id'])) { ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img src="<?php echo getProfileIMG($conn); ?>" class="rounded-circle" width="20" alt="Profile"> <?php echo $_SESSION['name']; ?> <?php if (isAdmin($_SESSION['id'], $conn)) echo "<span class='badge badge-coekku'>Admin</span>"; ?></a>
                    <div class="dropdown-menu dropdown-menu-left dropdown-menu-md-right dropdown-coekku z-depth-1" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="../profile/"> แก้ไขข้อมูลส่วนตัว <i class="fas fa-user"></i></a>
                        <div class="dropdown-divider"></div>
                        <button class="dropdown-item text-danger" id="logoutBtn">ออกจากระบบ <i class="fas fa-sign-out-alt"></i></button>
                    </div>
                </li>
                <?php } else { ?>
                <li class="nav-item">
                    <a class="nav-link" href="../login/">Login</a>
                </li>
                <?php } ?>
            </ul>
        </div>
    </div>
</nav>