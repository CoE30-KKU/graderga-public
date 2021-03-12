<?php if (isLogin()) header("Location: ../"); ?>
<div class="container" id="container" style="padding-top: 88px">
    <div class="center">
    <h1 class="display-5 font-weight-bold text-center text-coekku">LOGIN <i class="fas fa-sign-in-alt"></i></h1>
    <form method="post" action="../static/functions/auth/login.php" enctype="multipart/form-data">
        <div class="card z-depth-1">
            <!--Body-->
            <div class="card-body mb-1">
                <?php if (isset($_SESSION['error'])) {echo '<div class="alert alert-danger" role="alert">'. $_SESSION['error'] .'</div>'; $_SESSION['error'] = null;} ?>
                <div class="md-form form-sm mb-5">
                    <i class="fas fa-user prefix text-coekku"></i>
                    <input type="text" name="login_username" id="login_username"
                        class="form-control form-control-sm validate" required>
                    <label for="login_username">Username</label>
                </div>
                <div class="md-form form-sm mb-4">
                    <i class="fas fa-lock prefix text-coekku"></i>
                    <input type="password" name="login_password" id="login_password"
                        class="form-control form-control-sm validate" required>
                    <label for="login_password">Password</label>
                </div>
                <button type="submit" class="btn btn-block btn-coekku mb-3">Login</button>
                <input type="hidden" name="method" value="loginPage">
                <a href="../forgetpassword/" class="text-danger">ลืมรหัสผ่านหรอ?</a> หรือ <a href="../register/" class="text-pharm">สมัครเข้าใช้งานที่นี่!</a>
                <input type="hidden" name="referent" value="<?php $referent = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null; echo $referent;?>">
            </div>
        </div>
    </form>
    </div>
</div>