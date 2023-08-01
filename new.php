<?php

include_once "includes/header.php";

if(isset($_POST['confirm'])) {
//    var_dump($_POST);
//    die();

    if(isset($_POST['ptp']) && $_POST['ptp'] == 1) {
        if(isset($_POST['capability'])) {
            $product = $_POST['product'];
            $milestone = $_POST['milestone'];
            $module = $_POST['module'];
            $capability_standard = $_POST['capability'];
            $bi_type = $_POST['bi'];
            $hvm_flow_standard = $_POST['hvm'];
            $flow_type = $_POST['flow'];
            $date = $_POST['date'];

            $query = "INSERT INTO orders 
              (user_id, product, milestone, module, capability_standard, bi_type, hvm_flow_standard, flow_type, created)
              VALUES (?,?,?,?,?,?,?,?,?)";
            $stmt = $conn->prepare($query);
            $res = $stmt->execute([$_SESSION['user']->id, $product, $milestone, $module, $capability_standard, $bi_type, $hvm_flow_standard, $flow_type, $date]);

            if($res) {
                $id = $conn->lastInsertId();

                $ppv_flow = $_POST['ppv_flow'];
                $ppv_types = $_POST['ppv_type'];
                $platform_names = $_POST['platform_names'];

                $query = "INSERT INTO ppv_orders (location_order_id, user_id, product, milestone, module, created) VALUES (?,?,?,?,?,?)";
                $stmt = $conn->prepare($query);
                $res = $stmt->execute([$id, $_SESSION['user']->id, $product, $milestone, $module, $date]);

                $ppv_order_id = $conn->lastInsertId();

                foreach ($ppv_flow as $key => $value) {
                    $query = "INSERT INTO ppv_orders_data (ppv_order_id, ppv_manufacturing_flow_std_id, ppv_id) VALUES (?,?,?)";
                    $stmt = $conn->prepare($query);
                    $res = $stmt->execute([$ppv_order_id, $value, implode(",", $ppv_types[$key + 1])]);

                    $ppv_orders_data_id = $conn->lastInsertId();

                    foreach ($ppv_types[$key+1] as $ppv_type) {
                        $query = "INSERT INTO ppv_types_orders (ppv_orders_data_id, ppv_id) VALUES (?,?)";
                        $stmt = $conn->prepare($query);
                        $res = $stmt->execute([$ppv_orders_data_id, $ppv_type]);
                    }
                }

                $query = "INSERT INTO validations (order_id) VALUES (?)";
                $stmt = $conn->prepare($query);
                $res = $stmt->execute([$id]);

                redirect("output-ppv.php?id={$ppv_order_id}");
            } else {
                $message = "<p class='alert alert-danger'>Data insertion failed!</p>";
            }
        } else {
            $product = $_POST['product'];
            $milestone = $_POST['milestone'];
            $module = $_POST['module'];
            $date = $_POST['date'];

            $id = 0;

            $ppv_flow = $_POST['ppv_flow'];
            $ppv_types = $_POST['ppv_type'];
            $platform_names = $_POST['platform_names'];
            

            $query = "INSERT INTO ppv_orders (location_order_id, user_id, product, milestone, module, created) VALUES (?,?,?,?,?,?)";
            $stmt = $conn->prepare($query);
            $res = $stmt->execute([$id, $_SESSION['user']->id, $product, $milestone, $module, $date]);

            $ppv_order_id = $conn->lastInsertId();

            foreach ($ppv_flow as $key => $value) {
                $query = "INSERT INTO ppv_orders_data (ppv_order_id, ppv_manufacturing_flow_std_id, ppv_id) VALUES (?,?,?)";
                $stmt = $conn->prepare($query);
                $res = $stmt->execute([$ppv_order_id, $value, implode(",", $ppv_types[$key + 1])]);

                $ppv_orders_data_id = $conn->lastInsertId();

                foreach ($ppv_types[$key+1] as $ppv_type) {
                    $query = "INSERT INTO ppv_types_orders (ppv_orders_data_id, ppv_id) VALUES (?,?)";
                    $stmt = $conn->prepare($query);
                    $res = $stmt->execute([$ppv_orders_data_id, $ppv_type]);
                }
            }

            $query = "INSERT INTO validations_ppv (ppv_order_id) VALUES (?)";
            $stmt = $conn->prepare($query);
            $res = $stmt->execute([$ppv_order_id]);

            redirect("output-ppv.php?id={$ppv_order_id}");
        }
    } else {
        $product = $_POST['product'];
        $milestone = $_POST['milestone'];
        $module = $_POST['module'];
        $capability_standard = $_POST['capability'];
        $bi_type = $_POST['bi'];
        $hvm_flow_standard = $_POST['hvm'];
        $flow_type = $_POST['flow'];
        $date = $_POST['date'];

        $query = "INSERT INTO orders
              (user_id, product, milestone, module, capability_standard, bi_type, hvm_flow_standard, flow_type, created)
              VALUES (?,?,?,?,?,?,?,?,?)";
        $stmt = $conn->prepare($query);
        $res = $stmt->execute([$_SESSION['user']->id, $product, $milestone, $module, $capability_standard, $bi_type, $hvm_flow_standard, $flow_type, $date]);

        if($res) {
            $id = $conn->lastInsertId();

            $query = "INSERT INTO validations (order_id) VALUES (?)";
            $stmt = $conn->prepare($query);
            $res = $stmt->execute([$id]);

            redirect("output.php?id={$id}");
        } else {
            $message = "<p class='alert alert-danger'>Data insertion failed!</p>";
        }
    }

}

$milestones = findAll('milestones');
if(!$milestones) {
    alert('No milestones added yet!');
    redirect('index.php');
}

$modules = findAll('modules');
if(!$modules) {
    alert('No modules added yet!');
    redirect('index.php');
}

$capability_standards = findAll('capability_standards');
if(!$capability_standards) {
    alert('No Class Tester Platforms added yet!');
    redirect('index.php');
}

$hvm_flow_standards = findAll('hvm_flow_standards');
if(!$hvm_flow_standards) {
    alert('No FaCT Types added yet!');
    redirect('index.php');
}

$flow_types = findAll('flow_types');
if(!$flow_types) {
    alert('No Manufacturing Flow STDs added yet!');
    redirect('index.php');
}

$bi_types = findAll('bi_types');
if(!$bi_types) {
    alert('No Burn-IN Types added yet!');
    redirect('index.php');
}

$ppv_manufacturing_flow_stds = findAll('ppv_manufacturing_flow_std');
if(!$ppv_manufacturing_flow_stds) {
    alert('No PPV Manufacturing Flow STD added yet!');
    redirect('index.php');
}

$ppvs = findAll('ppvs');
if(!$ppvs) {
    alert('No PPV added yet!');
    redirect('index.php');
}

?>

<main class="content">
    <div class="row">
        <div class="col-lg-12 ">
            <div class="d-flex justify-content-between buttons-row">
                <div class="main-heading  w-100">
                    <h1 class="f-14 my-4">Please select a category</h1>
                </div>
            </div>

            <div class="box shadow">
                <?php echo isset($message) ? $message : '' ?>
                <form action="" method="post" id="category-form">
                    <div class="row p-0 m-0">
                        <div class="col-lg-12 px-0">
                            <p class="f-14 mb-0 pb-0 w-500">Product Name</p>
                            <input required type="text" name="product" class="sign-input w-100 mb-3" placeholder="Product Name ">
                        </div>

                        <div class="col-lg-12 px-0">
                            <p class="f-14 mb-0 pb-0 w-500">Milestone</p>
                            <select class="form-select mb-3" name="milestone" id="select-milestone">
                                <?php foreach ($milestones as $milestone): ?>
                                    <option value="<?php echo $milestone->id ?>"><?php echo $milestone->name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-lg-12 px-0">
                            <p class="f-14 mb-0 pb-0 w-500">Timeline</p>
                            <input required type="date" min="<?php echo date('Y-m-d') ?>" name="date" class="sign-input w-100 mb-3">
                        </div>
						
						<div class="col-lg-12 d-flex justify-content-center">
							<p class="f-14 mb-0 pb-0 w-500">Please select the button.🡫</p>
                        </div>
						
                        <div class="col-lg-12 d-flex justify-content-center">
                            <div class="module p-5 d-flex justify-content-center align-items-center">
                                <img src="assets/images/module.png" alt="module">
                            </div>
                        </div>

                        <!-- Module Modal -->
                        <div class="hidden modal fade" id="moduleModal" tabindex="-1" aria-labelledby="moduleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="moduleModalLabel">Select Module</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <select class="form-select mb-3" name="module" id="">
                                            <?php foreach ($modules as $module): ?>
                                                <option value="<?php echo $module->id ?>"><?php echo $module->name ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="button" id="moduleModal-select" class="btn-fill text-white">Select</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Flow Type Modal -->
                        <div class="hidden modal fade" id="flowtypeModal" tabindex="-1" aria-labelledby="moduleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="moduleModalLabel">Select Flow Type</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <select class="form-select mb-3" name="flowtype" id="flowtype-select">
                                                <option value="pre_sfgi">Pre SFGI (Test)</option>
                                                <option value="post_sfgi">Post SFGI (Backend)</option>
                                        </select>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="button" id="flowtypeModal-select" class="btn-fill text-white">Select</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Module Form Modal -->
                        <div class="hidden modal fade" id="moduleFormModal" tabindex="-1" aria-labelledby="moduleFormModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="moduleModalLabel">User Page (Pre SFGI - Test)</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p class="f-14 mb-0 pb-0 w-500">Class Tester Platform</p>
                                        <select class="form-select mb-3 select-check" name="capability" id="tp">
                                            <option disabled selected>Select</option>
                                            <?php foreach ($capability_standards as $capability_standard): ?>
                                                <option value="<?php echo $capability_standard->id ?>"><?php echo $capability_standard->name ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <p class="f-14 mb-0 pb-0 w-500">Burn-IN Type</p>
                                        <select class="form-select mb-3 select-check" name="bi" id="bt">
                                            <option disabled selected>Select</option>
                                            <?php foreach ($bi_types as $bi_type): ?>
                                                <option value="<?php echo $bi_type->id ?>"><?php echo $bi_type->name ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <p class="f-14 mb-0 pb-0 w-500">FaCT Type</p>
                                        <select class="form-select mb-3 select-check" name="hvm" id="ft">
                                            <option disabled selected>Select</option>
                                            <?php foreach ($hvm_flow_standards as $hvm_flow_standard): ?>
                                                <option value="<?php echo $hvm_flow_standard->id ?>"><?php echo $hvm_flow_standard->name ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <p class="f-14 mb-0 pb-0 w-500">Manufacturing Flow STD</p>
                                        <select class="form-select mb-3" name="flow" id="mfs">
                                            <option disabled selected>Select</option>
                                        </select>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" id="moduleFormModal-close" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="button" id="moduleFormModal-clear" class="btn btn-primary">Clear</button>
                                        <button type="button" id="moduleFormModal-add" class="btn-fill text-white">Add</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Module Form POST SFGI Modal -->
                        <div class="hidden modal fade" id="moduleForm2Modal" tabindex="-1" aria-labelledby="moduleForm2ModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="moduleModalLabel">User Page (Post SFGI - Backend)</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <label>
                                            <input type="checkbox" class="mb-2"> Class Tester Platform Not Applicable
                                        </label>
                                        <div id="class-fields">
                                            <p class="f-14 mb-0 pb-0 w-500">Class Tester Platform</p>
                                            <select class="form-select mb-3 select-check" name="capability" id="tp2">
                                                <option disabled selected>Select</option>
                                                <option value="not-applicable">Not Applicable</option>
                                                <?php foreach ($capability_standards as $capability_standard): ?>
                                                    <option value="<?php echo $capability_standard->id ?>"><?php echo $capability_standard->name ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <p class="f-14 mb-0 pb-0 w-500">Burn-IN Type</p>
                                            <select class="form-select mb-3 select-check" name="bi" id="bt2">
                                                <option disabled selected>Select</option>
                                                <?php foreach ($bi_types as $bi_type): ?>
                                                    <option value="<?php echo $bi_type->id ?>"><?php echo $bi_type->name ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <p class="f-14 mb-0 pb-0 w-500">FaCT Type</p>
                                            <select class="form-select mb-3 select-check" name="hvm" id="ft2">
                                                <option disabled selected>Select</option>
                                                <?php foreach ($hvm_flow_standards as $hvm_flow_standard): ?>
                                                    <option value="<?php echo $hvm_flow_standard->id ?>"><?php echo $hvm_flow_standard->name ?></option>
                                                <?php endforeach; ?>
                                            </select>

                                            <p class="f-14 mb-0 pb-0 w-500">Manufacturing Flow STD</p>
                                            <select class="form-select mb-3" name="flow" id="mfs2">
                                                <option disabled selected>Select</option>
                                            </select>
                                        </div>

                                        <p class="f-14 mb-0 pb-0 w-500">PPV Tester Platform</p>
                                        <select class="form-select mb-3" name="ptp" id="ptp">
                                            <option selected disabled>Select</option>
                                            <option value="0">Not Applicable</option>
                                            <option value="1">PPV Platform</option>
                                        </select>

                                        <div class="tester_platform d-none">
                                            <div class="tester_platform_inner">
                                                <p class="f-14 mb-0 pb-0 w-500">PPV Manufacturing Flow STD (Insertion 1)</p>
                                                <select class="form-select mb-3 ppv_selects ppv_flow" name="ppv_flow[]" id="pf">
                                                    <option disabled selected>Select</option>
                                                    <?php foreach ($ppv_manufacturing_flow_stds as $ppv_manufacturing_flow_std): ?>
                                                        <option value="<?php echo $ppv_manufacturing_flow_std->id ?>"><?php echo $ppv_manufacturing_flow_std->name ?></option>
                                                    <?php endforeach; ?>
                                                </select>

                                                <p class="f-14 mb-0 pb-0 w-500">PPV Type</p>
                                                <select class="form-select mb-3 ppv_selects ppv_type" name="ppv_type[1][]" id="pt">
                                                    <option value="0" disabled selected>Select</option>
                                                </select>

                                                <div class="add-ppv-type d-none">
                                                    <button class="mb-3" id="add-ppv-type" type="button" style="width: 30px; height: 30px; background-color: var(--yellow); border: none; outline: none; font-size: 20px; font-weight: bold; border-radius: 5px; color: #ffffff">+</button>
                                                </div>

                                                <p class="f-14 mb-0 pb-0 w-500">Platform Name</p>
                                                <select class="form-select mb-3 ppv_platform_name" name="platform_names[]" id="pn">
                                                    <option disabled selected>Select</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="add-insertion-div d-none">
                                            <button class="mb-3 btn fw-bold" id="add-insertion" type="button" >Proceed to next insertion</button>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" id="moduleForm2Modal-close" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="button" id="moduleForm2Modal-clear" class="btn btn-primary">Clear</button>
                                        <button type="button" id="moduleForm2Modal-add" class="btn-fill text-white">Add</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6 ">
                            <button type="button" onclick="location.href = 'index.php'" class="btn-fill ms-0 w-100 mt-4"><a href="index.php"
                                    class="f-14 w-500">Cancel</a></button>
                        </div>
                        <div class="col-lg-6 ">
                            <button name="confirm" id="btn-disabled" disabled class=" btn-fill ms-0 w-100 mt-4"><a
                                                                        class="f-14 w-500">Confirm</a></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>

</main>

<script>
    var ppv_flow_stds = <?php echo json_encode($ppv_manufacturing_flow_stds); ?>;
    var ppvs = <?php echo json_encode($ppvs); ?>;
</script>

<?php

include_once "includes/footer.php";

?>

<script>
    $('.select-check').change(function (e) {
        var typeSfgi = $("#flowtype-select").val()
        if(typeSfgi === "pre_sfgi") {
            var tp = $('#tp').children('option:selected').val()
            var bt = $('#bt').children('option:selected').val()
            var ft = $('#ft').children('option:selected').val()
            $.ajax({
                url: 'includes/ajax.php',
                type: 'post',
                data: {'mfs': true, tp, bt, ft, type: 0},
                success: function (res) {
                    // console.log(res)
                    if(res.length !== 0) {
                        var rows = JSON.parse(res)
                        $('#mfs').empty()
                        $('#mfs').append('<option selected disabled="disabled">Select</option>');
                        // $('#mfs').find('option:not(:first)').remove();

                        rows.forEach(function (row) {
                            $('#mfs').append($('<option>', {
                                value: row.flow_type_id,
                                text: row.name
                            }));
                        })
                    }
                }
            })
        } else {
            var tp = $('#tp2').children('option:selected').val()
            var bt = $('#bt2').children('option:selected').val()
            var ft = $('#ft2').children('option:selected').val()
            console.log(tp,bt,ft)
            $.ajax({
                url: 'includes/ajax.php',
                type: 'post',
                data: {'mfs': true, tp, bt, ft, type: 1},
                success: function (res) {
                    console.log(res)
                    if(res.length !== 0) {
                        var rows = JSON.parse(res)
                        $('#mfs2').empty()
                        $('#mfs2').append('<option selected disabled="disabled">Select</option>');
                        // $('#mfs').find('option:not(:first)').remove();

                        rows.forEach(function (row) {
                            $('#mfs2').append($('<option>', {
                                value: row.flow_type_id,
                                text: row.name
                            }));
                        })
                    }
                }
            })
        }

    })

    // $('.select-check').on('change', function () {
        // $('#mfs').prop('selectedIndex', 0);
        // $('#mfs').empty()
        // $('#mfs').prepend('<option id="select-disabled" selected disabled="disabled">Select</option>');
    // })

    // $("#tp2").on("change", function () {
    //     if($(this).val() === 'not-applicable') {
    //         // $("#class-fields").remove()
    //         $("#class-fields").addClass("d-none")
    //         $("#class-fields").find("select").attr("disabled", true)
    //     }
    // })

    $("input[type=checkbox]").on("change", function () {
        if($(this).is(":checked")) {
            // $("#class-fields").remove()
            $("#class-fields").addClass("d-none")
            $("#class-fields").find("select").attr("disabled", true)
        }else {
            $("#class-fields").removeClass("d-none")
            $("#class-fields").find("select").attr("disabled", false)
        }
    })

    $("body").on("change", ".ppv_selects", function () {
        var mainPlatform = $(this).closest(".tester_platform_inner")
        var ppv_flow = mainPlatform.find(".ppv_flow").val()
        var ppv_types = mainPlatform.find(".ppv_type")
        var flow_name = mainPlatform.find(".ppv_platform_name")

        if($(this).hasClass("ppv_flow")) {
            $.ajax({
                url: 'includes/ajax.php',
                type: 'post',
                data: {'pmfs': true, ppv_flow},
                success: function (res) {
                    console.log(res)
                    if(res.length !== 0) {
                        var rows = JSON.parse(res)
                        for(let i=0; i<ppv_types.length; i++) {
                            $(ppv_types[i]).empty()
                            $(ppv_types[i]).append('<option selected disabled="disabled">Select</option>');
                            // $('#mfs').find('option:not(:first)').remove();
                            ppvs = rows;

                            rows.forEach(function (row) {
                                $(ppv_types[i]).append($('<option>', {
                                    value: row.id,
                                    text: row.ppv_type
                                }));
                            })
                        }
                    }
                }
            })
        }

        ppv_types = mainPlatform.find(".ppv_type")

        var ppv_types_arr = []
        if(ppv_types.length > 0) {
            for(let i=0; i<ppv_types.length; i++) {
                ppv_types_arr.push($(ppv_types[i]).children('option:selected').text())
            }
        }

        $.ajax({
            url: 'includes/ajax.php',
            type: 'post',
            data: {'pmfs_flow_name': true, ppv_flow, ppv_types_arr},
            success: function (res) {
                console.log(res)
                if(res.length !== 0) {
                    var rows = JSON.parse(res)
                    flow_name.empty()
                    flow_name.append('<option selected disabled="disabled">Select</option>');
                    // $('#mfs').find('option:not(:first)').remove();
                    // ppvs = rows;

                    rows.forEach(function (row) {
                        flow_name.append($('<option>', {
                            value: row.id,
                            text: row.platform_name
                        }));
                    })
                }
            }
        })
    })
</script>
