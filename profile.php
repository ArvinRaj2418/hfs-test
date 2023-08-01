<?php

include_once ('includes/header.php');

if(isset($_POST['update'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];

    $query = 'UPDATE users SET name = ?, email = ?';

    if(!empty($_POST['password'])) {
        $crypt_pass = password_hash($_POST['password'], PASSWORD_BCRYPT);

        $query .= ', pass = ? WHERE id = ?';
        $stmt = $conn->prepare($query);
        $res = $stmt->execute([$name, $email, $crypt_pass, $_SESSION['user']->id]);
    } else {
        $query .= " WHERE id = ?";

        $stmt = $conn->prepare($query);
        $res = $stmt->execute([$name, $email, $_SESSION['user']->id]);
    }

    if(!empty($res)) {
        $_SESSION['user']->name = $name;
        $_SESSION['user']->email = $email;
        $message = "<p class='alert alert-success'>Profile updated successfully!</p>";
    } else {
        $message = "<p class='alert alert-danger'>Could not update profile!</p>";
    }
}

$query = 'SELECT * FROM users WHERE id = ?';
$stmt = $conn->prepare($query);
$stmt->execute([$_SESSION['user']->id]);
$user = $stmt->fetch();
?>

<main class="content">
    <div class="row">
        <div class="col-lg-12 ">
            <div class="main-heading d-flex justify-content-between w-100">
                <h1 class=" my-4">Profile</h1>
            </div>
            <div class="box shadow">
                <?php echo isset($message) ? $message : '' ?>
                <form class="row" method="post" action="profile.php" enctype="multipart/form-data">
                    <div class="col-lg-6">
                        <p class="f-16 mb-0 pb-0 w-600 mt-2">Name</p>
                        <input type="text" readonly name="name" value="<?php echo $user->name ?>" required class="sign-input w-100 mb-3" placeholder="Name ">
                    </div>
                    <div class="col-lg-6">
                        <p class="f-16 mb-0 pb-0 w-600 mt-2">Email</p>
                        <input type="email" readonly name="email" value="<?php echo $user->email ?>" required class="sign-input w-100 mb-3" placeholder="Email Address ">
                    </div>
                    <div class="col-lg-12">
                        <p class="f-16 mb-0 pb-0 w-600 mt-2">Password</p>
                        <small>Leave empty to not change password</small>
                        <input type="password" autocomplete="off" name="password" class="sign-input w-100 mb-3" placeholder="Password">
                    </div>
                    <div class="col-lg-12 mt-2 mb-5 w-100 d-flex justify-content-end">
                        <button type="submit" name="update" class="btn-fill w-25 "><a>Update</a></button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</main>

<?php

include_once ('includes/footer.php');

?>