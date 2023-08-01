<?php

include_once ('includes/header.php');

if(!isset($_GET['id'])) {
    redirect("index.php");
}

if(isset($_POST['update'])) {
    $capability_id = $_POST['capability'];
    $hvm_id = $_POST['hvm'];
    $columns = $_POST['columns'];
    $location_codes = $_POST['location_codes'];
    $op_names = $_POST['op_names'];
    $op_types = $_POST['op_types'];
    $flow = $_POST['flow'];
    $flow_id = $_POST['flow_id'];
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
        $query = "UPDATE locations SET capability_standard_id = ?, hvm_flow_id = ?, img = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $res = $stmt->execute([$capability_id, $hvm_id, $img_name, $_GET['id']]);
    } else {
        $query = "UPDATE locations SET capability_standard_id = ?, hvm_flow_id = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $res = $stmt->execute([$capability_id, $hvm_id, $_GET['id']]);
    }

    $query = "UPDATE flow_types SET name = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $res = $stmt->execute([$flow, $flow_id]);

    foreach ($columns as $key => $column) {
        $query = "UPDATE locations_data SET location_code = ?, opname = ?, optype = ? WHERE column_id = ? AND location_id = ?";
        $stmt = $conn->prepare($query);
        $res = $stmt->execute([$location_codes[$key], $op_names[$key], $op_types[$key], $column, $_GET['id']]);
    }

    $message = "<p class='alert alert-success'>Updated successfully!</p>";
}

$query = "SELECT *,l.id As lId,cs.name AS csName, ft.name AS ftName, hfs.name AS hfsName FROM locations l ";
$query .= "INNER JOIN capability_standards cs ON l.capability_standard_id = cs.id ";
$query .= "INNER JOIN hvm_flow_standards hfs ON l.hvm_flow_id = hfs.id ";
$query .= "INNER JOIN flow_types ft ON l.flow_type_id = ft.id ";
$query .= "WHERE l.id = {$_GET['id']}";

$location = findByQuery($query);

$query = "SELECT *,c.id AS cId,c.name AS cName FROM locations_data ld ";
$query .= "INNER JOIN columns c ON c.id = ld.column_id ";
$query .= "WHERE ld.location_id = {$location->lid} ";

$locations_data = findAllByQuery($query);

$capability_standards = findAll('capability_standards');
$hvm_flow_standards = findAll('hvm_flow_standards');
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
                <form id="form-location" action="update.php?id=<?php echo $_GET['id'] ?>" class="row" method="post" enctype="multipart/form-data">
                    <div class="col-lg-12">
                        <p class="f-14 mb-0 pb-0 w-500">Class Tester Platform</p>
                        <select class="form-select mb-3" name="capability" id="">
                            <?php foreach ($capability_standards as $capability_standard): ?>
                                <option <?php echo $capability_standard->id == $location->capability_standard_id ? 'selected' : '' ?> value="<?php echo $capability_standard->id ?>"><?php echo $capability_standard->name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-lg-12">
                        <p class="f-14 mb-0 pb-0 w-500">FaCT Type</p>
                        <select class="form-select mb-3" name="hvm" id="select-milestone">
                            <?php foreach ($hvm_flow_standards as $hvm_flow_standard): ?>
                                <option <?php echo $hvm_flow_standard->id == $location->hvm_flow_id ? 'selected' : '' ?> value="<?php echo $hvm_flow_standard->id ?>"><?php echo $hvm_flow_standard->name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-lg-12">
                        <p class="f-14 mb-0 pb-0 w-500">Flow Name</p>
                        <input type="text" name="flow" value="<?php echo $location->ftname ?>" required class="sign-input w-100 mb-3" placeholder="Manufacturing Flow STD Name ">
                        <input type="hidden" name="flow_id" value="<?php echo $location->flow_type_id ?>">
                    </div>
                    <div class="col-lg-12 mb-2 img-div">
                        <p class="f-14 mb-0 pb-0 w-500">Image</p>
                        <input style="padding: 4px" type="file" name="img"  class="sign-input w-100 ">
                    </div>

                    <?php if(!empty($locations_data)): foreach ($locations_data as $location_data): ?>
                        <p class="f-14 mb-0 pb-0 w-500"><?php echo $location_data->cname ?></p>
                        <input type="hidden" name="columns[]" value="<?php echo $location_data->cid ?>">
                        <div class="col-lg-6">
                            <input type="text" name="location_codes[]" value="<?php echo $location_data->location_code ?>"  class="sign-input w-100 mb-3" placeholder="Location Code ">
                        </div>
                        <div class="col-lg-3">
                            <input type="text" name="op_names[]" value="<?php echo $location_data->opname ?>"  class="sign-input w-100 mb-3" placeholder="Operation Name ">
                        </div>
                        <div class="col-lg-3">
                            <input type="text" name="op_types[]" value="<?php echo $location_data->optype ?>" class="sign-input w-100 mb-3" placeholder="Operation Type ">
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