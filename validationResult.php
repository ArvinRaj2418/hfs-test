<?php

include_once "includes/header.php";

//$expiry = date('Y-m-d', strtotime(date('Y-m-d') . '-40 days'));

if(isset($_GET['id'])) {
    $query = "SELECT * FROM orders WHERE id = {$_GET['id']} AND user_id = {$_SESSION['user']->id}";
    $orders = findAllByQuery($query);
} else {
    $query = "SELECT * FROM orders WHERE user_id = {$_SESSION['user']->id}";
    $orders = findAllByQuery($query);

    // PPV
//    $query = "SELECT * FROM ppv_orders WHERE user_id = {$_SESSION['user']->id} AND location_order_id = 0";
//    $orders2 = findAllByQuery($query);
}

?>

<style>
    .validate-link {
        height: fit-content !important;
        align-self: center;
    }
</style>
<main class="content">
    <div class="row">
        <div class="col-lg-12 ">
            <div class="d-flex justify-content-between buttons-row">
                <div class="main-heading  w-100">
                    <h1 class="f-14 my-4">Flow Validation Result</h1>
                </div>
            </div>

            <div class="box shadow">
                <?php if(!empty($orders)): ?>
                <?php foreach ($orders as $order): ?>
                    <?php
                        $query = "SELECT * FROM validations WHERE order_id = {$order->id}";
                        $validation = findByQuery($query);

                        $query = "SELECT * FROM flow_types WHERE id = {$order->flow_type}";
                        $flowType = findByQuery($query);

                        $query = "SELECT * FROM milestones WHERE id = {$order->milestone}";
                        $milestone = findByQuery($query);
                    ?>
                        <div class="row p-2 justify-content-between">
                            <input type="hidden" class="order_id" value="<?php echo $validation->order_id ?>">
                            <a class="col-lg-2 col-md-2 f-14 fw-bold m-0 p-2 text-center validate-link border border-primary text-dark rounded mb-2"><?php echo $order->product . '<br>' . $flowType->name . '<br>' . $milestone->name ?></a>
                            <?php if(empty($validation->workstream_text) || empty($validation->workstream_img) || empty($validation->crystalball_text) || empty($validation->crystalball_img)): ?>
                                <a class="rev col-lg-2 col-md-2 f-14 m-0 p-2 text-center validate-link border border-light bg-danger text-light rounded mb-2">Validation Status</a>
                            <?php else: ?>
                                <a class="rev col-lg-2 col-md-2 f-14 m-0 p-2 text-center validate-link border border-light bg-success text-light rounded mb-2">Pass Validation</a>
                            <?php endif; ?>
                            <?php if(empty($validation->workstream_text) || empty($validation->workstream_img)): ?>
                                <a class="validation col-lg-3 col-md-3 f-14 m-0 p-2 text-center validate-link border border-light bg-danger text-light rounded mb-2">Workstream Data Validation</a>
                            <?php else: ?>
                                <a class="validation col-lg-3 col-md-3 f-14 m-0 p-2 text-center validate-link border border-light bg-success text-light rounded mb-2">Workstream Data Validated</a>
                            <?php endif; ?>
                            <?php if(empty($validation->crystalball_text) || empty($validation->crystalball_img)): ?>
                                <a class="hvm_dep col-lg-3 col-md-3 f-14 m-0 p-2 text-center validate-link border border-light bg-danger text-light rounded mb-2">Crystal Ball Data Validation</a>
                            <?php else: ?>
                                <a class="hvm_dep col-lg-3 col-md-3 f-14 m-0 p-2 text-center validate-link border border-light bg-success text-light rounded mb-2">Crystal Ball Data Validated</a>
                            <?php endif; ?>
                        </div>
                <?php endforeach; endif; ?>

                <!-- PPV-->
<!--                --><?php //if(!empty($orders2)): ?>
<!--                    --><?php //foreach ($orders2 as $order): ?>
<!--                        --><?php
//                        $query = "SELECT * FROM validations_ppv WHERE ppv_order_id = {$order->id}";
//                        $validation = findByQuery($query);
//
//                        $query = "SELECT * FROM milestones WHERE id = {$order->milestone}";
//                        $milestone = findByQuery($query);
//                        ?>
<!--                        <div class="row p-2 justify-content-between">-->
<!--                            <input type="hidden" class="order_id" value="--><?php //echo $validation->ppv_order_id ?><!--">-->
<!--                            <a class="col-lg-2 col-md-2 f-14 fw-bold m-0 p-2 text-center validate-link border border-primary text-dark rounded mb-2">--><?php //echo $order->product . '<br>' . '' . '<br>' . $milestone->name ?><!--</a>-->
<!--                            --><?php //if(empty($validation->rcs) || empty($validation->wtl) || empty($validation->md)): ?>
<!--                                <a class="rev col-lg-2 col-md-2 f-14 m-0 p-2 text-center validate-link border border-light bg-danger text-light rounded mb-2">Validation Status</a>-->
<!--                            --><?php //else: ?>
<!--                                <a class="rev col-lg-2 col-md-2 f-14 m-0 p-2 text-center validate-link border border-light bg-success text-light rounded mb-2">Pass Validation</a>-->
<!--                            --><?php //endif; ?>
<!--                            --><?php //if(empty($validation->rcs)): ?>
<!--                                <a class="validation col-lg-2 col-md-2 f-14 m-0 p-2 text-center validate-link border border-light bg-danger text-light rounded mb-2">Workstream data validation</a>-->
<!--                                <input type="file" data-col="rcs" data-validation="validations_ppv" data-order-id="--><?php //echo $validation->ppv_order_id ?><!--" style="display: none">-->
<!--                            --><?php //else: ?>
<!--                                <a class="validation col-lg-2 col-md-2 f-14 m-0 p-2 text-center validate-link border border-light bg-success text-light rounded mb-2">Workstream data validation</a>-->
<!--                                <input type="file" data-file="--><?php //echo $validation->rcs ?><!--" style="display: none">-->
<!--                            --><?php //endif; ?>
<!--                            --><?php //if(empty($validation->wtl)): ?>
<!--                                <a class="validation col-lg-2 col-md-2 f-14 m-0 p-2 text-center validate-link border border-light bg-danger text-light rounded mb-2">Crsytal Ball data validation</a>-->
<!--                                <input type="file" data-col="wtl" data-validation="validations_ppv" data-order-id="--><?php //echo $validation->ppv_order_id ?><!--" style="display: none">-->
<!--                            --><?php //else: ?>
<!--                                <a class="validation col-lg-2 col-md-2 f-14 m-0 p-2 text-center validate-link border border-light bg-success text-light rounded mb-2">Crsytal Ball data validation</a>-->
<!--                                <input type="file" data-file="--><?php //echo $validation->wtl ?><!--" style="display: none">-->
<!--                            --><?php //endif; ?>
<!--                            --><?php //if(empty($validation->md)): ?>
<!--                                <a class="validation col-lg-2 col-md-2 f-14 m-0 p-2 text-center validate-link border border-light bg-danger text-light rounded mb-2">SQL PathFinder Data</a>-->
<!--                                <input type="file" data-col="md" data-validation="validations_ppv" data-order-id="--><?php //echo $validation->ppv_order_id ?><!--" style="display: none">-->
<!--                            --><?php //else: ?>
<!--                                <a class="validation col-lg-2 col-md-2 f-14 m-0 p-2 text-center validate-link border border-light bg-success text-light rounded mb-2">SQL PathFinder Data</a>-->
<!--                                <input type="file" data-file="--><?php //echo $validation->md ?><!--" style="display: none">-->
<!--                            --><?php //endif; ?>
<!--                        </div>-->
<!--                    --><?php //endforeach; endif; ?>
            </div>
        </div>

    </div>

</main>

    <!-- Workstream Validation Modal -->
    <div class="hidden modal fade" id="validationModal" tabindex="-1" aria-labelledby="validationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="validationModalLabel">Workstream Data Validation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" class="validation_form" method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="order_id">
                        <div class="col-lg-12 px-0 mb-3">
                            <p class="f-14 mb-0 pb-0 w-500">Workstream Text File <small>(Please insert Workstream data validation text file)</small></p>
                            <input style="padding: 4px" type="file" name="wdvt" required class="sign-input w-100 ">
                        </div>
                        <div class="col-lg-12 px-0">
                            <p class="f-14 mb-0 pb-0 w-500">Workstream Image <small>(Please insert Worksteam data validation image)</small></p>
                            <input style="padding: 4px" type="file" name="wdvi" required class="sign-input w-100 ">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" id="validationModal-add" class="btn-fill text-white">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Workstream Validated Modal -->
    <div class="hidden modal fade " id="validatedModal" tabindex="-1" aria-labelledby="validatedModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="validatedModalLabel">Workstream Data Validation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body validated-imgs row justify-content-between">
                    <input type="hidden" name="order_id">
                    <div class="col-lg-5 col-md-5 col-12 mt-2 mx-auto px-0">
                        <p class="f-14 mb-0 pb-0 w-500">Workstream Text File</p>
                        <a>No File added</a>
                    </div>
                    <div class="col-lg-5 col-md-5 col-12 mt-2 mx-auto px-0">
                        <p class="f-14 mb-0 pb-0 w-500">Workstream Image</p>
                        <img class="img-fluid" src="" alt="">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Crystal Ball Validation Modal -->
    <div class="hidden modal fade" id="cbvalidationModal" tabindex="-1" aria-labelledby="cbvalidationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cbvalidationModalLabel">Cystal Ball Data Validation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" class="cbvalidation_form" method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="order_id">
                        <div class="col-lg-12 px-0 mb-3">
                            <p class="f-14 mb-0 pb-0 w-500">Crystal Ball Text File <small>(Please insert Crystal Ball data validation text file)</small></p>
                            <input style="padding: 4px" type="file" name="cbt" required class="sign-input w-100 ">
                        </div>
                        <div class="col-lg-12 px-0">
                            <p class="f-14 mb-0 pb-0 w-500">Crystal Ball Image <small>(Please insert Crystal Ball data validation image)</small></p>
                            <input style="padding: 4px" type="file" name="cbi" required class="sign-input w-100 ">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" id="cbvalidationModal-add" class="btn-fill text-white">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Crystal Ball Validated Modal -->
    <div class="hidden modal fade " id="cbvalidatedModal" tabindex="-1" aria-labelledby="cbvalidatedModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cbvalidatedModalLabel">Crystal Ball Data Validation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body cbvalidated-imgs row justify-content-between">
                    <input type="hidden" name="order_id">
                    <div class="col-lg-5 col-md-5 col-12 mt-2 mx-auto px-0">
                        <p class="f-14 mb-0 pb-0 w-500">Crystal Ball Text File</p>
                        <a>No File added</a>
                    </div>
                    <div class="col-lg-5 col-md-5 col-12 mt-2 mx-auto px-0">
                        <p class="f-14 mb-0 pb-0 w-500">Crystal Ball Image</p>
                        <img class="img-fluid" src="" alt="">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Images Modal -->
<!--    <div class="hidden modal fade " id="ImgsModal" tabindex="-1" aria-labelledby="ImgsModalLabel" aria-hidden="true">-->
<!--        <div class="modal-dialog modal-xl modal-dialog-centered">-->
<!--            <div class="modal-content">-->
<!--                <div class="modal-header">-->
<!--                    <h5 class="modal-title" id="ImgsModalLabel"></h5>-->
<!--                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>-->
<!--                </div>-->
<!--                <div class="modal-body Imgs-imgs row justify-content-between">-->
<!--                    <div class="col-lg-5 col-md-5 col-12 mt-2 mx-auto px-0">-->
<!--                        <img width="100%" class="img-fluid modal-img" src="" alt="">-->
<!--                    </div>-->
<!--                </div>-->
<!--                <div class="modal-footer">-->
<!--                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->

<?php

include_once "includes/footer.php";

?>


<script>
    var row;
    $('.validation').click(function () {
        if(!$(this).hasClass('bg-success')) {
            $('#validationModal').modal('show')
            var order_id = $(this).siblings('.order_id').val()

            $('.validation_form').find('input[type="hidden"]').val(order_id);
            row = $(this).closest('.row');
            $('.validation_form').find('input:not([type="hidden"])').each(function () {
                $(this).val('');
            })
        } else {
            $('#validatedModal').modal('show')
            var order_id = $(this).siblings('.order_id').val()

            $('.validated-imgs').find('input[type="hidden"]').val(order_id);
            row = $(this).closest('.row');

            $.ajax({
                method: 'post',
                url: 'includes/ajax.php',
                data: {table: "validations", 'validationFiles': true, order_id},
                success: function (response) {
                    var data = JSON.parse(response);
                    var wdvt = $('#validatedModal').find('a')[0]
                    var wdvi = $('#validatedModal').find('img')[0]

                    $(wdvt).attr("href", 'uploads/' + data[0].workstream_text)
                    $(wdvt).text('uploads/' + data[0].workstream_text)
                    wdvi.src = 'uploads/' + data[0].workstream_img
                }
            })
        }
    })

    $('#validationModal-add').click(function () {
        var wdvt = $(this).parents().eq(1).find('input[name="wdvf"]').val();
        var wdvi = $(this).parents().eq(1).find('input[name="wdvi"]').val();

        if(wdvt == "" || wdvi == "") {
            alert('Please insert all fields.');
        } else {
            var formData = new FormData($(this).closest('form')[0])
            formData.append("table", "validations")
            var that = $(this)
            $.ajax({
                method: 'post',
                url: 'includes/ajax.php',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                success: function (response) {
                    console.log(response)
                    if(response == 'success') {
                        that.closest('.modal').modal('hide');
                        row.find('.validation').removeClass('bg-danger')
                        row.find('.validation').addClass('bg-success')

                        if(row.find('.hvm_dep').hasClass('bg-success')) {
                            row.find('.rev').text('Pass Validation')
                            row.find('.rev').addClass('bg-success')
                            row.find('.rev').removeClass('bg-danger')
                        }
                    } else if(response == "error") {
                        alert("There is some error.")
                    } else if(response == "no-match") {
                        alert("File does not match!")
                    }
                }
            })
        }
    })


    $('.hvm_dep').click(function () {
        if(!$(this).hasClass('bg-success')) {
            $('#cbvalidationModal').modal('show')
            var order_id = $(this).siblings('.order_id').val()

            $('.cbvalidation_form').find('input[type="hidden"]').val(order_id);
            row = $(this).closest('.row');
            $('.cbvalidation_form').find('input:not([type="hidden"])').each(function () {
                $(this).val('');
            })
        } else {
            $('#cbvalidatedModal').modal('show')
            var order_id = $(this).siblings('.order_id').val()

            $('.cbvalidated-imgs').find('input[type="hidden"]').val(order_id);
            row = $(this).closest('.row');

            $.ajax({
                method: 'post',
                url: 'includes/ajax.php',
                data: {table: "validations", 'cbvalidationFiles': true, order_id},
                success: function (response) {
                    var data = JSON.parse(response);
                    var cbt = $('#cbvalidatedModal').find('a')[0]
                    var cbi = $('#cbvalidatedModal').find('img')[0]

                    $(cbt).attr("href", 'uploads/' + data[0].crystalball_text)
                    $(cbt).text('uploads/' + data[0].crystalball_text)
                    cbi.src = 'uploads/' + data[0].crystalball_img
                }
            })
        }
    })

    $('#cbvalidationModal-add').click(function () {
        var cbt = $(this).parents().eq(1).find('input[name="wdvf"]').val();
        var cbi = $(this).parents().eq(1).find('input[name="wdvi"]').val();

        if(cbt == "" || cbi == "") {
            alert('Please insert all fields.');
        } else {
            var formData = new FormData($(this).closest('form')[0])
            formData.append("table", "validations")
            var that = $(this)
            $.ajax({
                method: 'post',
                url: 'includes/ajax.php',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                success: function (response) {
                    if(response == 'success') {
                        that.closest('.modal').modal('hide');
                        row.find('.hvm_dep').removeClass('bg-danger')
                        row.find('.hvm_dep').addClass('bg-success')

                        if(row.find('.validation').hasClass('bg-success')) {
                            row.find('.rev').text('Pass Validation')
                            row.find('.rev').addClass('bg-success')
                            row.find('.rev').removeClass('bg-danger')
                        } 
						
                    }
                }
            })
        }
    })
</script>


<!--<script>-->
<!--    var row;-->
<!--    $('.validation').click(function () {-->
<!--        if($(this).hasClass("bg-success")) {-->
<!--            var modal = $("#ImgsModal");-->
<!--            modal.modal('show')-->
<!--            $(".modal-title").text($(this).text())-->
<!--            $(".modal-img").attr("src", "uploads/" + $(this).next("input").data('file'))-->
<!--        } else {-->
<!--            $(this).next("input").click()-->
<!--        }-->
<!--    })-->
<!---->
<!--    $("input[type='file']").on("change", function () {-->
<!--        var col = $(this).data("col")-->
<!--        var order_id = $(this).data('order-id')-->
<!--        var validationType = $(this).data('validation')-->
<!--        var that = $(this)-->
<!---->
<!--        var file_data = $(this).prop('files')[0];-->
<!--        var form_data = new FormData();-->
<!--        form_data.append('file', file_data);-->
<!--        form_data.append('validation', col);-->
<!--        form_data.append('order_id', order_id);-->
<!--        form_data.append('validationType', validationType);-->
<!--        $.ajax({-->
<!--            url: 'includes/ajax.php',-->
<!--            dataType: 'text',-->
<!--            cache: false,-->
<!--            contentType: false,-->
<!--            processData: false,-->
<!--            data: form_data,-->
<!--            type: 'post',-->
<!--            success: function(response){-->
<!--                console.log(response)-->
<!--                var status = true;-->
<!--                if(response === "success") {-->
<!--                    that.prev(".validation").removeClass("bg-danger")-->
<!--                    that.prev(".validation").addClass("bg-success")-->
<!---->
<!--                    $(".validation").each(function () {-->
<!--                        if($(this).hasClass("bg-danger")) {-->
<!--                            status = false;-->
<!--                        }-->
<!--                    })-->
<!---->
<!--                    if(status === true) {-->
<!--                        $(".rev").removeClass("bg-danger")-->
<!--                        $(".rev").addClass("bg-success")-->
<!--                    }-->
<!--                } else {-->
<!--                    that.prev(".validation").removeClass("bg-success")-->
<!--                    that.prev(".validation").addClass("bg-danger")-->
<!--                }-->
<!--            }-->
<!--        });-->
<!--    })-->
<!--</script>-->
