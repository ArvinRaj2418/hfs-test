<?php

include_once "includes/header.php";

if(isset($_GET['logout'])) {
    unset($_SESSION['admin']);
    redirect('../signin.php');
}

if(isset($_GET['a']) && isset($_GET['id'])) {
    $imgCol = $_GET['col'];
    $query = "UPDATE validations SET {$imgCol} = NULL WHERE order_id = ?";
    $stmt = $conn->prepare($query);
    $res = $stmt->execute([$_GET['id']]);
    redirect("validationResult.php");
}

$query = "SELECT *, validations.order_id as orderID FROM validations INNER JOIN orders o on validations.order_id = o.id INNER JOIN users u on o.user_id = u.id";

$validations = findAllByQuery($query);

?>

<main class="content">
    <div class="row">
        <div class="col-lg-12 ">
            <div class="main-heading  w-100">
                <h1 class="f-14 my-4">Dashboard</h1>
            </div>

            <div class="row mt-3">
                <div class="box shadow">
                    <div class="data-table">
                        <table id="dataTable" class="table" style="width: 100%">
                            <thead>
                            <tr>
                                <th>Product</th>
                                <th>User</th>
                                <th>Workstream Text</th>
                                <th>Workstream Image</th>
                                <th>Crystal Ball Text</th>
                                <th>Crystal Ball Image</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if(!empty($validations)): ?>
                            <?php foreach ($validations as $validation): ?>
                                <tr>
                                    <td style="vertical-align: middle"><?php echo $validation->product ?></td>
                                    <td style="vertical-align: middle"><?php echo $validation->name ?></td>
                                    <td class="dt-center">
                                        <div class="d-flex justify-content-center text-dark">
                                            <div class="select-icon-btn me-2">
                                                <form action="" method="post" enctype="multipart/form-data">
                                                    <input type="hidden" name="order_id" value="<?php echo $validation->orderid?>">
                                                    <input type="hidden" name="imgCol" value="workstream_text">
                                                    <input type="file" name="workstream_text" class="custom-file-input">
                                                </form>
                                                <i class="bi bi-pencil-square"></i>
                                            </div>
                                            <a class="f-14 text-dark" href="?col=workstream_text&a=delete&id=<?php echo $validation->orderid ?>"><i
                                                            class="bi bi-trash me-2 f-16 w-600"></i></a>
                                        </div>
                                        <a class="fileLink" href="../uploads/<?php echo $validation->workstream_text ?>"><?php echo $validation->workstream_text ?></a>
                                    </td>
                                    <td class="dt-center">
                                        <div class="d-flex justify-content-center text-dark">
                                            <div class="select-icon-btn me-2">
                                                <form action="" method="post" enctype="multipart/form-data">
                                                    <input type="hidden" name="order_id" value="<?php echo $validation->orderid ?>">
                                                    <input type="hidden" name="imgCol" value="workstream_img">
                                                    <input type="file" name="workstream_img" class="custom-file-input">
                                                </form>
                                                <i class="bi bi-pencil-square"></i>
                                            </div>
                                            <a class="f-14 text-dark" href="?col=workstream_img&a=delete&id=<?php echo $validation->orderid ?>"><i
                                                        class="bi bi-trash me-2 f-16 w-600"></i></a>
                                        </div>
                                        <img width="200px" class="img-fluid" src="../uploads/<?php echo $validation->workstream_img ?>" alt="">
                                    </td>
                                    <td class="dt-center">
                                        <div class="d-flex justify-content-center text-dark">
                                            <div class="select-icon-btn me-2">
                                                <form action="" method="post" enctype="multipart/form-data">
                                                    <input type="hidden" name="order_id" value="<?php echo $validation->orderid ?>">
                                                    <input type="hidden" name="imgCol" value="crystalball_text">
                                                    <input type="file" name="crystalball_text" class="custom-file-input">
                                                </form>
                                                <i class="bi bi-pencil-square"></i>
                                            </div>
                                            <a class="f-14 text-dark" href="?col=crystalball_text&a=delete&id=<?php echo $validation->orderid ?>"><i
                                                        class="bi bi-trash me-2 f-16 w-600"></i></a>
                                        </div>
                                        <a class="fileLink" href="../uploads/<?php echo $validation->crystalball_text ?>"><?php echo $validation->crystalball_text ?></a>
                                    </td>
                                    <td class="dt-center">
                                        <div class="d-flex justify-content-center text-dark">
                                            <div class="select-icon-btn me-2">
                                                <form action="" method="post" enctype="multipart/form-data">
                                                    <input type="hidden" name="order_id" value="<?php echo $validation->orderid ?>">
                                                    <input type="hidden" name="imgCol" value="crystalball_img">
                                                    <input type="file" name="crystalball_img" class="custom-file-input">
                                                </form>
                                                <i class="bi bi-pencil-square"></i>
                                            </div>
                                            <a class="f-14 text-dark" href="?col=crystalball_img&a=delete&id=<?php echo $validation->orderid ?>"><i
                                                        class="bi bi-trash me-2 f-16 w-600"></i></a>
                                        </div>
                                        <img width="200px" class="img-fluid" src="../uploads/<?php echo $validation->crystalball_img ?>" alt="">
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>

</main>


<!-- Image Zoom Modal -->
<div class="hidden modal fade " id="imgZoomModal" tabindex="-1" aria-labelledby="imgZoomModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
<!--                <h5 class="modal-title" id="imgZoomModalLabel">HVM Deployment</h5>-->
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body row justify-content-center">
                <div class="col-lg-12 col-md-12 col-12 mt-2 mx-auto text-center px-0">
                    <img id="imageZoom" class="img-fluid" src="" alt="">
                </div>
            </div>
        </div>
    </div>
</div>

<?php

include_once "includes/footer.php";

?>

<script>
    $('.custom-file-input').on('change', function () {
        var that = $(this)
        if (this.files && this.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                var fileType = e.target.result.split('/')[0];
                if(fileType.includes("image")) {
                    that.closest('td').find('img').attr('src', e.target.result);
                }

            }

            reader.readAsDataURL(this.files[0]);

            var formData = new FormData(that.closest('form')[0])
            console.log(that.closest('form')[0])
            $.ajax({
                method: 'post',
                url: '../includes/ajax.php',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                success: function (response) {
                    response = JSON.parse(response)
                    that.closest('td').find('.fileLink').attr('href', response.location);
                    that.closest('td').find('.fileLink').text(response.img);
                }
            })
        }
    })


    // Zoom Image
    $('.img-fluid').click(function () {
        $('#imgZoomModal').modal('show')
        console.log( $(this).prop('src'))
        $('#imageZoom').attr('src', $(this).prop('src'))
    })
</script>
