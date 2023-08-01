<?php

include_once ('includes/header.php');

if(isset($_POST['add']) || isset($_POST['view'])) {
//    var_dump($_POST);
//    die();
    $columns = $_POST['columns'];
    $location_codes = $_POST['location_codes'];
    $op_names = $_POST['op_names'];
    $op_types = $_POST['op_types'];
    $flow_id = 0;
    $type = $_POST['flowPrePost'];

    $img_name = null;
    $uploadCheck = isset($_FILES['img']['name']) && !empty($_FILES['img']['name']);
    if($uploadCheck) {
        $target_dir = "../uploads/";
        $img_name = time() . '-' . $_FILES["img"]["name"];
        $target_file = $target_dir . $img_name;
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        move_uploaded_file($_FILES["img"]["tmp_name"], $target_file);
    }

    if(isset($_POST['category']) && $_POST['category'] == 1) {
        $ppv_manufacturing_flow_std_id = $_POST['ppv_flow'];
        $ppv_type = $_POST['ppv_type'];
        $platform_name = $_POST['platform_name'];

        $query = "INSERT INTO ppvs (ppv_tester_platform_id, ppv_manufacturing_flow_std_id, ppv_type, platform_name, img) VALUES (?,?,?,?,?)";
        $stmt = $conn->prepare($query);
        $res = $stmt->execute([1, $ppv_manufacturing_flow_std_id, $ppv_type, $platform_name, $img_name]);

        $ppv_id = $conn->lastInsertId();

        foreach ($columns as $key => $column) {
            if((!isset($location_codes[$key]) || empty($location_codes[$key])) && (!isset($op_names[$key]) || empty($op_names[$key])) && (!isset($op_types[$key]) || empty($op_types[$key]))) {
                continue;
            }

            $location_code = isset($location_codes[$key]) && !empty($location_codes[$key]) ? $location_codes[$key] : null;
            $op_name = isset($op_names[$key]) && !empty($op_names[$key]) ? $op_names[$key] : null;
            $op_type = isset($op_types[$key]) && !empty($op_types[$key]) ? $op_types[$key] : null;

            $query = "INSERT INTO ppv_data(column_id, ppv_id, location_code, opname, optype) VALUES (?,?,?,?,?)";
            $stmt = $conn->prepare($query);
            $res = $stmt->execute([$column, $ppv_id, $location_code, $op_name, $op_type]);

            if($res) {
                $message = '<p class="alert alert-success">Added successfully!</p>';
            }
        }
    } else {
        $capability_id = $_POST['capability'];
        $bi_id = $_POST['bi'];
        $hvm_id = $_POST['hvm'];
        $flow = $_POST['flow'];

        $flowData = findByQuery("SELECT * FROM flow_types WHERE name = '{$flow}'");
        if(empty($flowData)) {
            $query = "INSERT INTO flow_types(name) VALUES (?)";
            $stmt = $conn->prepare($query);
            $res = $stmt->execute([$flow]);

            $flow_id = $conn->lastInsertId();
        } else {
            $flow_id = $flowData->id;
        }

        $query = "INSERT INTO locations(type, capability_standard_id, bi_type_id, hvm_flow_id, flow_type_id, img) VALUES (?,?,?,?,?,?)";
        $stmt = $conn->prepare($query);
        $res = $stmt->execute([$type, $capability_id, $bi_id, $hvm_id, $flow_id, $img_name]);

        $location_id = $conn->lastInsertId();

        foreach ($columns as $key => $column) {
            if((!isset($location_codes[$key]) || empty($location_codes[$key])) && (!isset($op_names[$key]) || empty($op_names[$key])) && (!isset($op_types[$key]) || empty($op_types[$key]))) {
                continue;
            }
            $location_code = isset($location_codes[$key]) && !empty($location_codes[$key]) ? $location_codes[$key] : null;
            $op_name = isset($op_names[$key]) && !empty($op_names[$key]) ? $op_names[$key] : null;
            $op_type = isset($op_types[$key]) && !empty($op_types[$key]) ? $op_types[$key] : null;

            $query = "INSERT INTO locations_data(column_id, location_id, location_code, opname, optype) VALUES (?,?,?,?,?)";
            $stmt = $conn->prepare($query);
            $res = $stmt->execute([$column, $location_id, $location_code, $op_name, $op_type]);

            if($res) {
                $message = '<p class="alert alert-success">Added successfully!</p>';
            }
        }
    }

    if(isset($_POST['view'])) {
        redirect("output.php?id={$location_id}");
    }

    // redirect("index.php");
}

$capability_standards = findAll('capability_standards');
if(!$capability_standards) {
    alert('No Class Tester Platforms added yet!');
    redirect('index.php');
}

$bi_types = findAll('bi_types');
if(!$bi_types) {
    alert('No Burn-IN Type added yet!');
    redirect('index.php');
}

$hvm_flow_standards = findAll('hvm_flow_standards');
if(!$hvm_flow_standards) {
    alert('No FaCT Types added yet!');
    redirect('index.php');
}

$columnsPre = findAllByQuery("SELECT * FROM columns WHERE flow = '0' OR flow = '0,1'");
if(!$columnsPre) {
    alert('No Columns added yet!');
    redirect('index.php');
}

$columnsPost = findAllByQuery("SELECT * FROM columns WHERE flow = '1' OR flow = '0,1'");
if(!$columnsPost) {
    alert('No Columns added yet!');
    redirect('index.php');
}

$columnsPpv = findAllByQuery("SELECT * FROM columns WHERE flow = '2' OR flow = '0,1,2'");
if(!$columnsPpv) {
    alert('No Columns added yet!');
    redirect('index.php');
}

$ppv_manufacturing_flow_stds = findAll('ppv_manufacturing_flow_std');
if(!$ppv_manufacturing_flow_stds) {
    alert('No PPV Manufacturing Flow STD added yet!');
    redirect('index.php');
}

$ppv_types = findAll('ppv_type');
if(!$ppv_types) {
    alert('No PPV Type added yet!');
    redirect('index.php');
}

?>

<main class="content">
    <div class="row">
        <div class="col-lg-12 ">
            <div class="main-heading d-flex justify-content-between w-100">
                <h1 class=" my-4">New Location</h1>
            </div>
            <div class="box shadow">
                <?php echo isset($message) ? $message : '' ?>
                <form id="form-location" class="row" method="post" enctype="multipart/form-data">
                    <div class="col-lg-12">
                        <p class="f-14 mb-0 pb-0 w-500">Select Flow</p>
                        <select class="form-select mb-3" name="flowPrePost" id="flowPrePost">
                            <option selected disabled value="">Select Flow</option>
                            <option value="0">Pre SFGI (Test)</option>
                            <option value="1">Post SFGI (Backend)</option>
                        </select>
                    </div>
                    <div class="col-lg-12 d-none category">
                        <p class="f-14 mb-0 pb-0 w-500">Category</p>
                        <select disabled class="form-select mb-3" name="category" id="category">
                            <option selected disabled value="">Select Category</option>
                            <option value="0">Class</option>
                            <option value="1">PPV</option>
                        </select>
                    </div>
                    <div class="inner-fields">
                        <div class="col-lg-12">
                            <p class="f-14 mb-0 pb-0 w-500">Class Tester Platform</p>
                            <select class="form-select mb-3" name="capability" id="">
                                <?php foreach ($capability_standards as $capability_standard): ?>
                                    <option value="<?php echo $capability_standard->id ?>"><?php echo $capability_standard->name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-lg-12">
                            <p class="f-14 mb-0 pb-0 w-500">Burn-IN Type</p>
                            <select class="form-select mb-3" name="bi" id="">
                                <?php foreach ($bi_types as $bi_type): ?>
                                    <option value="<?php echo $bi_type->id ?>"><?php echo $bi_type->name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-lg-12">
                            <p class="f-14 mb-0 pb-0 w-500">FaCT Type</p>
                            <select class="form-select mb-3" name="hvm" id="select-milestone">
                                <?php foreach ($hvm_flow_standards as $hvm_flow_standard): ?>
                                    <option value="<?php echo $hvm_flow_standard->id ?>"><?php echo $hvm_flow_standard->name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-lg-12">
                            <p class="f-14 mb-0 pb-0 w-500">Flow Name</p>
                            <input type="text" name="flow" required class="sign-input w-100 mb-3" placeholder="Manufacturing Flow STD Name ">
                        </div>
                        <div class="col-lg-12 mb-2 img-div">
                            <p class="f-14 mb-0 pb-0 w-500">Image</p>
                            <input style="padding: 4px" type="file" name="img" required class="sign-input w-100 ">
                        </div>
                    </div>

                    <div class="inner-fields-ppv" style="display: none">
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
                                    <option value="<?php echo $ppv_manufacturing_flow_std->id ?>"><?php echo $ppv_manufacturing_flow_std->name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-lg-12">
                            <p class="f-14 mb-0 pb-0 w-500">PPV Type</p>
                            <input type="text" name="ppv_type" required class="sign-input w-100 mb-3" placeholder="PPV Type ">
                        </div>
                        <div class="col-lg-12">
                            <p class="f-14 mb-0 pb-0 w-500">Platform Name</p>
                            <input type="text" name="platform_name" required class="sign-input w-100 mb-3" placeholder="Platform Name ">
                        </div>
<!--                        <div class="col-lg-12 mb-2 img-div">-->
<!--                            <p class="f-14 mb-0 pb-0 w-500">Image</p>-->
<!--                            <input style="padding: 4px" type="file" name="img" required class="sign-input w-100 ">-->
<!--                        </div>-->
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


<div class="pre-sfgi row d-none">
    <?php foreach ($columnsPre as $column): ?>
        <p class="f-14 mb-0 pb-0 w-500"><?php echo $column->name ?></p>
        <input type="hidden" name="columns[]" value="<?php echo $column->id ?>">
        <div class="col-lg-6">
            <input type="text" name="location_codes[]" class="sign-input w-100 mb-3" placeholder="Location Code ">
        </div>
        <div class="col-lg-3">
            <input type="text" name="op_names[]" class="sign-input w-100 mb-3" placeholder="Operation Name ">
        </div>
        <div class="col-lg-3">
            <input type="text" name="op_types[]" class="sign-input w-100 mb-3" placeholder="Operation Type ">
        </div>
    <?php endforeach; ?>
</div>

<div class="post-sfgi row d-none">
    <?php foreach ($columnsPost as $column): ?>
        <p class="f-14 mb-0 pb-0 w-500"><?php echo $column->name ?></p>
        <input type="hidden" name="columns[]" value="<?php echo $column->id ?>">
        <div class="col-lg-6">
            <input type="text" name="location_codes[]" class="sign-input w-100 mb-3" placeholder="Location Code ">
        </div>
        <div class="col-lg-3">
            <input type="text" name="op_names[]" class="sign-input w-100 mb-3" placeholder="Operation Name ">
        </div>
        <div class="col-lg-3">
            <input type="text" name="op_types[]" class="sign-input w-100 mb-3" placeholder="Operation Type ">
        </div>
    <?php endforeach; ?>
</div>

<div class="post-ppv row d-none">
    <?php foreach ($columnsPpv as $column): ?>
        <p class="f-14 mb-0 pb-0 w-500"><?php echo $column->name ?></p>
        <input type="hidden" name="columns[]" value="<?php echo $column->id ?>">
        <div class="col-lg-6">
            <input type="text" name="location_codes[]" class="sign-input w-100 mb-3" placeholder="Location Code ">
        </div>
        <div class="col-lg-3">
            <input type="text" name="op_names[]" class="sign-input w-100 mb-3" placeholder="Operation Name ">
        </div>
        <div class="col-lg-3">
            <input type="text" name="op_types[]" class="sign-input w-100 mb-3" placeholder="Operation Type ">
        </div>
    <?php endforeach; ?>
</div>

<?php

include_once ('includes/footer.php');

?>

<script>
    var preSfgi = $('.pre-sfgi');
    var postSfgi = $('.post-sfgi');
    var postPpv = $('.post-ppv');
    $('#flowPrePost').on('change', function () {
        $("#category option:eq(0)").prop("selected", true)
        if($(this).val() == 0) {
            $(".category").addClass("d-none")
            $('#form-location').find('.post-sfgi').remove();
            $('#form-location').find('.post-ppv').remove();
            $(".inner-fields").css("display", "block")
            $(".inner-fields").find("select").each(function () {
                $(this).prop('disabled', false);
            })
            $(".inner-fields").find("input").each(function () {
                $(this).prop('disabled', false);
            })

            $(".inner-fields-ppv").css("display", "none")
            $(".inner-fields-ppv").find("select").each(function () {
                $(this).prop('disabled', true);
            })
            $(".inner-fields-ppv").find("input").each(function () {
                $(this).prop('disabled', true);
            })

            $(preSfgi).insertAfter('.img-div');
            $('.pre-sfgi').removeClass('d-none');
            $('.post-sfgi').addClass('d-none');
            $('.post-ppv').addClass('d-none');

            $('.post-sfgi').find('input:not([type=hidden])').each(function () {
                $(this).prop('disabled', true);
            })
            $('.post-ppv').find('input:not([type=hidden])').each(function () {
                $(this).prop('disabled', true);
            })

            $('.pre-sfgi').find('input:not([type=hidden])').each(function () {
                $(this).prop('disabled', false);
            })
        } else if ($(this).val() == 1) {
            $(".category").removeClass("d-none")
            $(".category").find("select").prop("disabled", false)
            $(".inner-fields").css("display", "none")

            // $('#form-location').find('.pre-sfgi').remove();
            // $(postSfgi).insertAfter('.img-div');
            // $('.post-sfgi').removeClass('d-none');
            // $('.pre-sfgi').addClass('d-none');
            //
            // $('.pre-sfgi').find('input:not([type=hidden])').each(function () {
            //     $(this).prop('disabled', true);
            // })
            //
            // $('.post-sfgi').find('input:not([type=hidden])').each(function () {
            //     $(this).prop('disabled', false);
            // })
        }

        $('#form-location').find('input:not([type=hidden])').each(function () {
            $(this).val('');
        })
    })

    $("#category").change(function () {
        var cat = $(this).val()
        if(cat == 0) {
            $(".inner-fields-ppv").css("display", "none")
            $(".inner-fields-ppv").find("select").each(function () {
                $(this).prop('disabled', true);
            })
            $(".inner-fields-ppv").find("input").each(function () {
                $(this).prop('disabled', true);
            })

            $(".inner-fields").css("display", "block")
            $(".inner-fields").find("select").each(function () {
                $(this).prop('disabled', false);
            })
            $(".inner-fields").find("input").each(function () {
                $(this).prop('disabled', false);
            })

            $('#form-location').find('.pre-sfgi').remove();
            $('#form-location').find('.post-ppv').remove();
            $(postSfgi).insertAfter('.img-div');
            $('.post-sfgi').removeClass('d-none');
            $('.pre-sfgi').addClass('d-none');
            $('.post-ppv').addClass('d-none');

            $('.pre-sfgi').find('input:not([type=hidden])').each(function () {
                $(this).prop('disabled', true);
            })

            $('.post-ppv').find('input:not([type=hidden])').each(function () {
                $(this).prop('disabled', true);
            })

            $('.post-sfgi').find('input').each(function () {
                $(this).prop('disabled', false);
            })
        } else {
            $(".inner-fields").css("display", "none")
            $(".inner-fields").find("select").each(function () {
                $(this).prop('disabled', true);
            })
            $(".inner-fields").find("input").each(function () {
                $(this).prop('disabled', true);
            })

            $(".inner-fields-ppv").css("display", "block")
            $(".inner-fields-ppv").find("select").each(function () {
                $(this).prop('disabled', false);
            })
            $(".inner-fields-ppv").find("input").each(function () {
                $(this).prop('disabled', false);
            })
            $(".post-ppv").append(`<div class="col-lg-12 mb-2 img-div">
                            <p class="f-14 mb-0 pb-0 w-500">Image</p>
                            <input style="padding: 4px" type="file" name="img" required class="sign-input w-100 ">
                        </div>`)
            // $(".inner-fields").css("display", "none")
            // $(".inner-fields").find("select").each(function () {
            //     $(this).prop('disabled', true);
            // })

            $('#form-location').find('.pre-sfgi').remove();
            $('#form-location').find('.post-sfgi').remove();
            // $(postPpv).insertAfter('.img-div');
            $(".inner-fields-ppv").append(postPpv)
            $('.post-ppv').removeClass('d-none');
            $('.pre-sfgi').addClass('d-none');
            $('.post-sfgi').addClass('d-none');

            $('.pre-sfgi').find('input:not([type=hidden])').each(function () {
                $(this).prop('disabled', true);
            })

            $('.post-sfgi').find('input:not([type=hidden])').each(function () {
                $(this).prop('disabled', true);
            })

            $('.post-ppv').find('input').each(function () {
                $(this).prop('disabled', false);
            })
        }
    })
</script>
