<?php

include_once "includes/header.php";

if(!isset($_GET['id'])) {
    redirect("index.php");
}

$query = "SELECT *,l.id As lId,cs.name AS csName, ft.name AS ftName, bi.name AS biName, hfs.name AS hfsName FROM locations l ";
$query .= "INNER JOIN capability_standards cs ON l.capability_standard_id = cs.id ";
$query .= "INNER JOIN bi_types bi ON l.bi_type_id = bi.id ";
$query .= "INNER JOIN hvm_flow_standards hfs ON l.hvm_flow_id = hfs.id ";
$query .= "INNER JOIN flow_types ft ON l.flow_type_id = ft.id ";
$query .= "WHERE l.id = {$_GET['id']}";

$location = findByQuery($query);

$query = "SELECT *,c.name AS cName FROM locations_data ld ";
$query .= "INNER JOIN columns c ON c.id = ld.column_id ";
$query .= "WHERE ld.location_id = {$location->lid} ";

$locations_data = findAllByQuery($query);

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
                        <p class="f-12 w-400 mb-0 pb-0 fw-bold text-dark-blue">Class Tester Platform</p>
                        <h1 class="f-20 w-500 mb-4"><?php echo $location->csname ?></h1>
                        <p class="f-12 w-400 mb-0 pb-0 fw-bold text-dark-blue">Burn-IN Type</p>
                        <h1 class="f-20 w-500 mb-4"><?php echo $location->biname ?></h1>
                        <p class="f-12 w-400 mb-0 pb-0  fw-bold text-dark-blue">FaCT Type</p>
                        <h1 class="f-20 w-500 mb-4"><?php echo $location->hfsname ?></h1>
                        <p class="f-12 w-400 mb-0 pb-0 fw-bold text-dark-blue">Manufacturing Flow STD</p>
                        <h1 class="f-20 w-500 mb-4"><?php echo $location->ftname ?></h1>
                        <p class="f-12 w-400 mb-0 pb-0 fw-bold text-dark-blue">Locations</p>
                        <?php if(!empty($locations_data)): ?>
                        <?php foreach ($locations_data as $location_data): ?>
                            <p class="f-14 w-500 text-dark-blue mb-0 pb-0"><?php echo $location_data->cname ?></p>
                                <?php if(!empty($location_data->location_code) || !empty($location_data->opname) || !empty($location_data->optype)): ?>
                                    <h1 class="f-20 mt-2 w-500">
                                        <?php echo $location_data->location_code ?> -
                                        <?php echo $location_data->opname ?>
                                        <?php if(!empty($location_data->optype)): ?>
                                            <small style="font-style: italic; margin-left: 5px">Optype:</small> <?php echo $location_data->optype ?>
                                        <?php endif; ?>
                                    </h1>
                                <?php endif; ?>
                        <?php endforeach; ?>
                        <?php endif; ?>
                        <div class="col-lg-12 mt-3 justify-content-center">
                            <img class=" img-responsive w-100" src="../uploads/<?php echo !empty($location) ? $location->img : '' ?>" alt="">
                        </div>

                    </div>
            </div>
        </div>

    </div>

</main>

<?php

include_once "includes/footer.php";

?>