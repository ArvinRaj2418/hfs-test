<?php

include_once ('includes/header.php');

if(!isset($_GET['id'])) {
    redirect("viewSearch.php");
}

if(isset($_POST['update'])) {
    $location_code = $_POST['location_code'];
    $optype = $_POST['optype'];
    $desc = $_POST['desc'];

    $query = "UPDATE search SET location_code = ?, optype = ?, description = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $res = $stmt->execute([$location_code, $optype, $desc, $_GET['id']]);

    if($res) {
        $message = "<p class='alert alert-success'>Updated successfully!</p>";
    } else {
        $message = "<p class='alert alert-danger'>Could not update!</p>";
    }
}

$search = findById("search", $_GET['id']);

?>

<main class="content">
    <div class="row">
        <div class="col-lg-12 ">
            <div class="main-heading d-flex justify-content-between w-100">
                <h1 class=" my-4">Update Search</h1>
            </div>
            <div class="box shadow">
                <?php echo isset($message) ? $message : '' ?>
                <form id="form-location" class="row" method="post" enctype="multipart/form-data">
                    <div class="col-lg-12">
                        <p class="f-14 mb-0 pb-0 w-500">Location Code</p>
                        <input type="text" value="<?php echo $search->location_code ?>" name="location_code" required class="sign-input w-100 mb-3" placeholder="Location Code ">
                    </div>
                    <div class="col-lg-12">
                        <p class="f-14 mb-0 pb-0 w-500">Operation Type</p>
                        <input type="text" value="<?php echo $search->optype ?>" name="optype" required class="sign-input w-100 mb-3" placeholder="Operation Type ">
                    </div>
                    <div class="col-lg-12">
                        <p class="f-14 mb-0 pb-0 w-500">Description</p>
                        <input type="text" value="<?php echo $search->description ?>" name="desc" required class="sign-input w-100 mb-3" placeholder="Description ">
                    </div>

                    <div class="col-lg-12">
                        <button type="submit" name="update" class="btn-fill w-100"><a>Update</a></button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</main>

<?php

include_once ('includes/footer.php');

?>