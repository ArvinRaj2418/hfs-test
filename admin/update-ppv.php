<?php

include_once ('includes/header.php');

if(!isset($_GET['id'])) {
    redirect("index.php");
}

if(isset($_POST['update'])) {
    $ppv_manufacturing_flow_std_id = $_POST['ppv_flow'];
    $ppv_type = $_POST['ppv_type'];
    $platform_name = $_POST['platform_name'];

    $columns = $_POST['columns'];
    $location_codes = $_POST['location_codes'];
    $op_names = $_POST['op_names'];
    $op_types = $_POST['op_types'];
     $uploadCheck = isset($_FILES['img']['name']) && !empty($_FILES['img']['name']);
    if($uploadCheck) {
        $target_dir = "../uploads/";
        $img_name = time() . '-' . $_FILES["img"]["name"];
        $target_file = $target_dir . $img_name;
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        move_uploaded_file($_FILES["img"]["tmp_name"], $target_file);
    }

    if($uploadCheck) {
        $query = "UPDATE ppvs SET ppv_manufacturing_flow_std_id = ?, ppv_type = ?, platform_name = ?, img = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $res = $stmt->execute([$ppv_manufacturing_flow_std_id, $ppv_type, $platform_name, $img_name, $_GET['id']]);
    } else {
        $query = "UPDATE ppvs SET ppv_manufacturing_flow_std_id = ?, ppv_type = ?, platform_name = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $res = $stmt->execute([$ppv_manufacturing_flow_std_id, $ppv_type, $platform_name, $_GET['id']]);
    }

    foreach ($columns as $key => $column) {
        $query = "UPDATE ppv_data SET location_code = ?, opname = ?, optype = ? WHERE column_id = ? AND ppv_id = ?";
        $stmt = $conn->prepare($query);
        $res = $stmt->execute([$location_codes[$key], $op_names[$key], $op_types[$key], $column, $_GET['id']]);
    }

    $message = "<p class='alert alert-success'>Updated successfully!</p>";
}

$query = "SELECT *, ppvs.id AS pid, pmfs.id AS pmfsid FROM ppvs";
$query .= " INNER JOIN ppv_manufacturing_flow_std pmfs on ppvs.ppv_manufacturing_flow_std_id = pmfs.id";
$query .= " WHERE ppvs.id = {$_GET['id']}";

$ppv = findByQuery($query);

$query = "SELECT *,c.id AS cId,c.name AS cName FROM ppv_data pd ";
$query .= "INNER JOIN columns c ON c.id = pd.column_id ";
$query .= "WHERE pd.ppv_id = {$ppv->pid} ";

$ppvs_data = findAllByQuery($query);

$ppv_manufacturing_flow_stds = findAll('ppv_manufacturing_flow_std');
$columns = findAll('columns');

?>

<main class="content">
    <div class="row">
        <div class="col-lg-12 ">
            <div class="main-heading d-flex justify-content-between w-100">
                <h1 class=" my-4">Update Location</h1>
            </div>
            <div class="box shadow">
                <?php echo isset($message) ? $message : '' ?>
                <form id="form-location" action="" class="row" method="post" enctype="multipart/form-data">
                    <div class="col-lg-12">
                        <p class="f-14 mb-0 pb-0 w-500">PPV Tester Platform</p>
                        <select class="form-select mb-3" name="ppv_tester_platform" id="">
                            <option value="PPV Platform">PPV Platform</option>
                        </select>
                    </div>
                    <div class="col-lg-12">
                        <p class="f-14 mb-0 pb-0 w-500">PPV Manufacturing Flow STD (Insertion 1)</p>
                        <select class="form-select mb-3" name="ppv_flow" id="">
                            <?php foreach ($ppv_manufacturing_flow_stds as $ppv_manufacturing_flow_std): ?>
                                <option <?php echo $ppv_manufacturing_flow_std->id == $ppv->pmfsid ? 'selected' : '' ?> value="<?php echo $ppv_manufacturing_flow_std->id ?>"><?php echo $ppv_manufacturing_flow_std->name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-lg-12">
                        <p class="f-14 mb-0 pb-0 w-500">PPV Type</p>
                        <input type="text" value="<?php echo $ppv->ppv_type ?>" name="ppv_type" required class="sign-input w-100 mb-3" placeholder="PPV Type ">
                    </div>
                    <div class="col-lg-12">
                        <p class="f-14 mb-0 pb-0 w-500">Platform Name</p>
                        <input type="text" value="<?php echo $ppv->platform_name ?>" name="platform_name" required class="sign-input w-100 mb-3" placeholder="Platform Name ">
                    </div>
                    <div class="col-lg-12 mb-2 img-div">
                        <p class="f-14 mb-0 pb-0 w-500">Image</p>
                        <input style="padding: 4px" type="file" name="img"  class="sign-input w-100 ">
                    </div>

                    <?php if(!empty($ppvs_data)): foreach ($ppvs_data as $ppv_data): ?>
                        <p class="f-14 mb-0 pb-0 w-500"><?php echo $ppv_data->cname ?></p>
                        <input type="hidden" name="columns[]" value="<?php echo $ppv_data->cId ?>">
                        <div class="col-lg-6">
                            <input type="text" name="location_codes[]" value="<?php echo $ppv_data->location_code ?>"  class="sign-input w-100 mb-3" placeholder="Location Code ">
                        </div>
                        <div class="col-lg-3">
                            <input type="text" name="op_names[]" value="<?php echo $ppv_data->opname ?>"  class="sign-input w-100 mb-3" placeholder="Operation Name ">
                        </div>
                        <div class="col-lg-3">
                            <input type="text" name="op_types[]" value="<?php echo $ppv_data->optype ?>" class="sign-input w-100 mb-3" placeholder="Operation Type ">
                        </div>
                    <?php endforeach; endif; ?>

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