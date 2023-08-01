<?php

include_once "includes/header.php";

$expiry = date('Y-m-d', strtotime(date('Y-m-d') . '-40 days'));

if(isset($_GET['id'])) {
    $query = "SELECT * FROM orders WHERE created > '{$expiry}' AND id = {$_GET['id']} AND user_id = {$_SESSION['user']->id}";
    $orders = findAllByQuery($query);
} else {
    $query = "SELECT * FROM orders WHERE created > '{$expiry}' AND user_id = {$_SESSION['user']->id}";
    $orders = findAllByQuery($query);
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
                        <?php if(empty($validation->rcs) || empty($validation->wtl) || empty($validation->md)): ?>
                            <a class="rev col-lg-2 col-md-2 f-14 m-0 p-2 text-center validate-link border border-light bg-danger text-light rounded mb-2">Rev0</a>
                        <?php else: ?>
                            <a class="rev col-lg-2 col-md-2 f-14 m-0 p-2 text-center validate-link border border-light bg-success text-light rounded mb-2">Rev1</a>
                        <?php endif; ?>
                        <?php if(empty($validation->rcs) || empty($validation->wtl)): ?>
                            <a class="validation col-lg-3 col-md-3 f-14 m-0 p-2 text-center validate-link border border-light bg-danger text-light rounded mb-2">Validation</a>
                        <?php else: ?>
                            <a class="validation col-lg-3 col-md-3 f-14 m-0 p-2 text-center validate-link border border-light bg-success text-light rounded mb-2">Validated</a>
                        <?php endif; ?>
                        <?php if(empty($validation->md)): ?>
                            <a class="hvm_dep col-lg-3 col-md-3 f-14 m-0 p-2 text-center validate-link border border-light bg-danger text-light rounded mb-2">HVM Deployment</a>
                        <?php else: ?>
                            <a class="hvm_dep col-lg-3 col-md-3 f-14 m-0 p-2 text-center validate-link border border-light bg-success text-light rounded mb-2">HVM Deployment</a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; endif; ?>
            </div>
        </div>

    </div>

</main>

    <!-- Validation Modal -->
    <div class="hidden modal fade" id="validationModal" tabindex="-1" aria-labelledby="validationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="validationModalLabel">Validation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" class="validation_form" method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="order_id">
                        <div class="col-lg-12 px-0 mb-3">
                            <p class="f-14 mb-0 pb-0 w-500">Route Creation Setup <small>(Please insert NPI Route Setup Validation)</small></p>
                            <input style="padding: 4px" type="file" name="rcs" required class="sign-input w-100 ">
                        </div>
                        <div class="col-lg-12 px-0">
                            <p class="f-14 mb-0 pb-0 w-500">Walk the Lot (WTL) Validation <small>(Please insert the WTL result)</small></p>
                            <input style="padding: 4px" type="file" name="wtl" required class="sign-input w-100 ">
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

    <!-- Validated Modal -->
    <div class="hidden modal fade " id="validatedModal" tabindex="-1" aria-labelledby="validatedModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="validatedModalLabel">Validation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                    <div class="modal-body validated-imgs row justify-content-between">
                        <input type="hidden" name="order_id">
                        <div class="col-lg-5 col-md-5 col-12 mt-2 mx-auto px-0">
                            <p class="f-14 mb-0 pb-0 w-500">Route Creation Setup</p>
                            <img class="img-fluid" src="" alt="">
                        </div>
                        <div class="col-lg-5 col-md-5 col-12 mt-2 mx-auto px-0">
                            <p class="f-14 mb-0 pb-0 w-500">Walk the Lot (WTL)</p>
                            <img class="img-fluid" src="" alt="">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
            </div>
        </div>
    </div>


    <!-- HVM Modal -->
    <div class="hidden modal fade" id="hvmModal" tabindex="-1" aria-labelledby="hvmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="hvmModalLabel">HVM Deployment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" class="hvm_form" method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="order_id">
                        <div class="col-lg-12 px-0 mb-3">
                            <p class="f-14 mb-0 pb-0 w-500">Milestone Data <small>(Please insert the milestone of your product)</small></p>
                            <input style="padding: 4px" type="file" name="hvmData" required class="sign-input w-100 ">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" id="hvmModal-add" class="btn-fill text-white">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- HVM Images Modal -->
    <div class="hidden modal fade " id="hvmImgsModal" tabindex="-1" aria-labelledby="hvmImgsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="hvmImgsModalLabel">HVM Deployment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body hvmImgs-imgs row justify-content-between">
                    <input type="hidden" name="order_id">
                    <div class="col-lg-5 col-md-5 col-12 mt-2 mx-auto px-0">
                        <p class="f-14 mb-0 pb-0 w-500">Milestone Data</p>
                        <img class="img-fluid" src="" alt="">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

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
                data: {'validationFiles': true, order_id},
                success: function (response) {
                    var data = JSON.parse(response);
                    var rcs = $('#validatedModal').find('img')[0]
                    var wtl = $('#validatedModal').find('img')[1]

                    rcs.src = 'uploads/' + data[0].rcs
                    wtl.src = 'uploads/' + data[0].wtl
                }
            })
        }
    })

    $('#validationModal-add').click(function () {
        var rcs = $(this).parents().eq(1).find('input[name="rcs"]').val();
        var wtl = $(this).parents().eq(1).find('input[name="wtl"]').val();

        if(rcs == "" || wtl == "") {
            alert('Please insert all fields.');
        } else {
            var formData = new FormData($(this).closest('form')[0])
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
                        row.find('.validation').removeClass('bg-danger')
                        row.find('.validation').addClass('bg-success')

                        if(row.find('.hvm_dep').hasClass('bg-success')) {
                            row.find('.rev').text('Rev1')
                            row.find('.rev').addClass('bg-success')
                            row.find('.rev').removeClass('bg-danger')
                        }
                    }
                }
            })
        }
    })


    $('.hvm_dep').click(function () {
        if(!$(this).hasClass('bg-success')) {
            $('#hvmModal').modal('show')
            var order_id = $(this).siblings('.order_id').val()

            $('.hvm_form').find('input[type="hidden"]').val(order_id);
            row = $(this).closest('.row');
            $('.hvm_form').find('input:not([type="hidden"])').each(function () {
                $(this).val('');
            })
        } else {
            $('#hvmImgsModal').modal('show')
            var order_id = $(this).siblings('.order_id').val()

            $('.hvmImgs-imgs').find('input[type="hidden"]').val(order_id);
            row = $(this).closest('.row');

            $.ajax({
                method: 'post',
                url: 'includes/ajax.php',
                data: {'hvmFiles': true, order_id},
                success: function (response) {
                    var data = JSON.parse(response);
                    var md = $('#hvmImgsModal').find('img')[0]

                    md.src = 'uploads/' + data[0].md
                }
            })
        }
    })

    $('#hvmModal-add').click(function () {
        var md = $(this).parents().eq(1).find('input[name="hvmData"]').val();

        if(md == "") {
            alert('Please insert all fields.');
        } else {
            var formData = new FormData($(this).closest('form')[0])
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
                            row.find('.rev').text('Rev1')
                            row.find('.rev').addClass('bg-success')
                            row.find('.rev').removeClass('bg-danger')
                        }
                    }
                }
            })
        }
    })
</script>
