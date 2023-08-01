<?php

include_once ('includes/header.php');

if(isset($_POST['add']) || isset($_POST['view'])) {
    $location_code = $_POST['location_code'];
    $optype = $_POST['optype'];
    $desc = $_POST['desc'];

    $query = "INSERT INTO search(location_code, optype, description) VALUES (?,?,?)";
    $stmt = $conn->prepare($query);
    $res = $stmt->execute([$location_code, $optype, $desc]);

    $search_d = $conn->lastInsertId();

    redirect("viewSearch.php");
}

?>

<main class="content">
    <div class="row">
        <div class="col-lg-12 ">
            <div class="main-heading d-flex justify-content-between w-100">
                <h1 class=" my-4">New Search</h1>
            </div>
            <div class="box shadow">
                <?php echo isset($message) ? $message : '' ?>
                <form id="form-location" class="row" method="post" enctype="multipart/form-data">
                    <div class="col-lg-12">
                        <p class="f-14 mb-0 pb-0 w-500">Location Code</p>
                        <input type="text" name="location_code" required class="sign-input w-100 mb-3" placeholder="Location Code ">
                    </div>
                    <div class="col-lg-12">
                        <p class="f-14 mb-0 pb-0 w-500">Operation Type</p>
                        <input type="text" name="optype" required class="sign-input w-100 mb-3" placeholder="Operation Type ">
                    </div>
                    <div class="col-lg-12">
                        <p class="f-14 mb-0 pb-0 w-500">Description</p>
                        <input type="text" name="desc" required class="sign-input w-100 mb-3" placeholder="Description ">
                    </div>

                    <div class="col-lg-4">
                        <button type="submit" name="add" class="btn-fill w-100"><a>Add</a></button>
                    </div>
                    <div class="col-lg-4">
                        <button type="submit" name="view" class="btn-fill w-100"><a>View</a></button>
                    </div>
                    <div class="col-lg-4">
                        <button id="clear-admin" type="button" name="update" class="btn-fill w-100"><a>Clear</a></button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</main>

<?php

include_once ('includes/footer.php');

?>