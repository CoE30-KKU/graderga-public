<?php if (isLogin()) header("Location: ../"); ?>
<div class="container" id="container" style="padding-top: 88px">
    <div class="center">
    <h1 class="display-5 font-weight-bold text-center text-coekku text-uppercase">Register <i class="fas fa-edit"></i></h1>
    <form method="post" action="../static/functions/auth/login.php" enctype="multipart/form-data">
        <div class="card z-depth-1">
            <!--Body-->
            <div class="card-body mb-1">
                <?php if (isset($_SESSION['error'])) {echo '<div class="alert alert-danger" role="alert">'. $_SESSION['error'] .'</div>'; $_SESSION['error'] = null;} ?>
                <div class="md-form form-sm mb-5">
                    <i class="fas fa-user prefix text-coekku"></i>
                    <input type="text" name="register_username" id="register_username"
                        class="form-control form-control-sm validate" required>
                    <label for="register_username">Username</label>
                </div>
                <div class="md-form form-sm mb-4">
                    <i class="fas fa-lock prefix text-coekku"></i>
                    <input type="password" name="register_password" id="register_password"
                        class="form-control form-control-sm validate" required>
                    <label for="register_password">Password</label>
                </div>
                <div class="md-form form-sm mb-4">
                    <i class="fas fa-envelope prefix text-coekku"></i>
                    <input type="email" name="register_email" id="register_email"
                        class="form-control form-control-sm validate" required>
                    <label for="register_email">Email</label>
                </div>
                <div class="md-form form-sm mb-4">
                    <i class="fas fa-user-secret prefix text-coekku"></i>
                    <input type="text" name="register_name" id="register_name"
                        class="form-control form-control-sm validate" required>
                    <label for="register_name">Display name</label>
                </div>
                <button type="submit" class="btn btn-block btn-coekku mb-3">Register</button>
                <input type="hidden" name="method" value="registerPage">
                <a href="../forgetpassword/" class="text-danger">ลืมรหัสผ่านหรอ?</a> หรือ <a href="../login/" class="text-pharm">ต้องการเข้าสู่ระบบ!</a>
            </div>
        </div>
    </form>
    </div>
</div>