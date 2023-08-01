<?php

include_once ('includes/functions.php');

if(isset($_SESSION['user'])) {
    redirect('index.php');
}

signup();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">

    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <link rel="stylesheet" href="assets/css/typograpgy.css">
    <title>Sign-Up</title>
</head>

    <body>
        <div class="container m-0 p-0 w-100 mw-100">
            <div class="row m-0 p-0 w-100 mw-100">
                <div class="col-lg-6 m-0 p-0 mx-auto">
                    <div class="container">
                        <div class="row sign-row d-flex justify-content-center align-items-center">
                            <form method="post" class="col-lg-9 sign-form mx-auto">
                                <?php echo isset($message) ? $message : '' ?>
                                <h4 class="">Sign-Up</h4>
                                <label >Username</label>
                                <input required type="text" name="username" class=" w-100 mb-3" placeholder="Username ">
                                <label >Email</label>
                                <input required type="email" name="email" class=" w-100 mb-3" placeholder="Email Address ">
                                <div class="d-flex align-items-center justify-content-between">
                                    <label >Password</label>
                                </div>
                                <div class="password">
                                    <input type="password" name="password" autocomplete="current-password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[\W_]).{12,}" required="" id="id_password" class="w-100">
                                    <i class="far fa-eye" id="togglePassword" style="margin-left: -30px; cursor: pointer;"></i>
                                </div>
                                <small class="f-12">Password must contain upper case, lower case, number and symbol</small>
                                 <a><button type="submit" name="signup" class="mt-4 w-600 f-15 w-100">Sign up</button></a>
                                <small class="f-14">Already registered? <a href="signin.php">Login</a></small>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>


        <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>

        <script>
            // Password
            const togglePassword = document.querySelector('#togglePassword');
            const password = document.querySelector('#id_password');

            togglePassword.addEventListener('click', function (e) {
                // toggle the type attribute
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                // toggle the eye slash icon
                this.classList.toggle('fa-eye-slash');
            });
        </script>
    </body>

</html>