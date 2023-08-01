<?php

include_once "includes/header.php";

if(!isset($_GET['id'])) {
    redirect("index.php");
}

$query = "SELECT *, ppvs.id AS pid FROM ppvs";
$query .= " INNER JOIN ppv_manufacturing_flow_std pmfs on ppvs.ppv_manufacturing_flow_std_id = pmfs.id";
$query .= " WHERE ppvs.id = {$_GET['id']}";

$ppv = findByQuery($query);

$query = "SELECT *,c.name AS cName FROM ppv_data pd ";
$query .= "INNER JOIN columns c ON c.id = pd.column_id ";
$query .= "WHERE pd.ppv_id = {$ppv->pid} ";
$ppvs_data = findAllByQuery($query);

?>

<main class="content">
    <div class="row">
        <div class="col-lg-12 ">
            <div class="d-flex justify-content-between buttons-row">
                <div class="main-heading  w-100">
                    <h1 class="f-14 my-4">Output</h1>
                </div>
            </div>

            <div class="box shadow">
                    <div class="row">
                        <p class="f-12 w-400 mb-0 pb-0 fw-bold text-dark-blue">PPV Tester Platform</p>
                        <h1 class="f-20 w-500 mb-4"><?php echo "PPV Platform" ?></h1>
                        <p class="f-12 w-400 mb-0 pb-0 fw-bold text-dark-blue">PPV Manufacturing Flow STD</p>
                        <h1 class="f-20 w-500 mb-4"><?php echo $ppv->name ?></h1>
                        <p class="f-12 w-400 mb-0 pb-0 fw-bold text-dark-blue">PPV Type</p>
                        <h1 class="f-20 w-500 mb-4"><?php echo $ppv->ppv_type ?></h1>
                        <p class="f-12 w-400 mb-0 pb-0  fw-bold text-dark-blue">Platform</p>
                        <h1 class="f-20 w-500 mb-4"><?php echo $ppv->platform_name ?></h1>
                        <p class="f-12 w-400 mb-0 pb-0 fw-bold text-dark-blue">Locations</p>
                        <?php if(!empty($ppvs_data)): ?>
                        <?php foreach ($ppvs_data as $ppv_data): ?>
                            <p class="f-14 w-500 text-dark-blue mb-0 pb-0"><?php echo $ppv_data->cname ?></p>
                                <?php if(!empty($ppv_data->location_code) || !empty($ppv_data->opname) || !empty($ppv_data->optype)): ?>
                                    <h1 class="f-20 mt-2 w-500">
                                        <?php echo $ppv_data->location_code ?> -
                                        <?php echo $ppv_data->opname ?>
                                        <?php if(!empty($ppv_data->optype)): ?>
                                            <small style="font-style: italic; margin-left: 5px">Optype:</small> <?php echo $ppv_data->optype ?>
                                        <?php endif; ?>
                                    </h1>
                                <?php endif; ?>
                        <?php endforeach; ?>
                        <?php endif; ?>
                        <div class="col-lg-12 mt-3 justify-content-center">
                            <img class=" img-responsive w-100" src="../uploads/<?php echo !empty($ppv) ? $ppv->img : '' ?>" alt="">
                        </div>

                    </div>
            </div>
        </div>

    </div>

</main>

<?php

include_once "includes/footer.php";

?>