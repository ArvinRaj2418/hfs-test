<?php

include_once "includes/header.php";

if(!isset($_GET['id'])) {
    redirect("index.php");
}

$query = "SELECT *, po.id AS poid, p.id as ppvid, m.name AS mName, mo.name AS moName FROM ppv_orders po INNER JOIN ppv_orders_data pod on po.id = pod.ppv_order_id ";
$query .= "INNER JOIN ppv_manufacturing_flow_std pmfs on pod.ppv_manufacturing_flow_std_id = pmfs.id ";
$query .= "INNER JOIN milestones m ON m.id = po.milestone ";
$query .= "INNER JOIN modules mo ON mo.id = po.module ";
$query .= "INNER JOIN ppvs p ON p.id = pod.ppv_id ";
$query .= "WHERE po.id = {$_GET['id']}";

$data = findByQuery($query);

$query = "SELECT *, pmfs.id as pmfsid, pod.id as podid FROM ppv_orders_data pod";
$query .= " INNER JOIN ppvs ON ppvs.id = pod.ppv_id";
$query .= " INNER JOIN ppv_manufacturing_flow_std pmfs ON pmfs.id = pod.ppv_manufacturing_flow_std_id";
$query .= " WHERE pod.ppv_order_id = {$data->poid}";

$ppv_data = findAllByQuery($query);

$query = "SELECT * FROM ppv_data INNER JOIN columns c ON c.id = ppv_data.column_id INNER JOIN ppvs ON ppv_data.ppv_id = ppvs.id WHERE ppv_id = {$data->ppvid}";
$locations = findAllByQuery($query);

$locations2 = array();
if($data->location_order_id != 0) {
    $query = "SELECT *,o.id as oID, cs.name AS csName, m.name AS mName, hfs.name AS hfsName, bi.name AS biName, ft.name AS ftName FROM orders o ";
    $query .= "INNER JOIN capability_standards cs ON o.capability_standard = cs.id ";
    $query .= "INNER JOIN bi_types bi ON o.bi_type = bi.id ";
    $query .= "INNER JOIN milestones m ON o.milestone = m.id ";
    $query .= "INNER JOIN hvm_flow_standards hfs ON o.hvm_flow_standard = hfs.id ";
    $query .= "INNER JOIN flow_types ft ON o.flow_type = ft.id ";
    $query .= "WHERE o.id = {$data->location_order_id}";

    $dataPost = findByQuery($query);

    $query = "SELECT *, l.id AS lID FROM locations l ";
    $query .= "INNER JOIN locations_data ld ON l.id = ld.location_id ";
    $query .= "INNER JOIN columns c ON c.id = ld.column_id ";
    $query .= "WHERE l.capability_standard_id = {$dataPost->capability_standard} ";
    $query .= "AND l.bi_type_id = {$dataPost->bi_type} ";
    $query .= "AND l.hvm_flow_id = {$dataPost->hvm_flow_standard} ";
    $query .= "AND l.flow_type_id = {$dataPost->flow_type}";

    $locations2 = findAllByQuery($query);
}

$locations = array_merge($locations, $locations2);

if(isset($_POST['submit'])) {
    $email = $_POST['email1'];
    $email2 = $_POST['email2'];
    $email3 = $_POST['email3'];
    $body = "<p>Product Name</p>
            <h4> $data->product </h4>
            ";

    if($data->location_order_id != 0) {
        $data .= "<p>Class Tester Platform</p>
            <h4> $dataPost->csname </h4>
            <p>Burn-IN Type</p>
            <h4> $dataPost->biname </h4>
            <p>Milestone</p>
            <h4> $dataPost->mname </h4>
            <p>FaCT Type</p>
            <h4> $dataPost->hfsname </h4>
            ";
    }

    $data .= "<p>Suggested Location Codes</p>";

    if(!empty($locations)) {
        foreach ($locations as $location) {
            if(!empty($location->location_code) || !empty($location->opname) || !empty($location->optype)){
                $body .= "<h4>{$location->location_code} - {$location->opname} <small>opType: {$location->optype}</small></h4>";
            }
        }
    }

//    if(!empty($ppvs)) {
//        foreach ($ppvs as $key => $ppv) {
//            $body .= "<p>PPV Manufacturing Flow STD (Insertion " . ($key+1) . " )</p>
//            <h4> $ppv->name </h4>
//            <p>PPV Type</p>
//            <h4> $ppv->type </h4>";
//        }
//    }

    $mailMessage = sendMail($body, $email, 'User', "HFS Locations", $email2, $email3);
}

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
                <?php echo isset($mailMessage) ? $mailMessage : '' ?>
                <div class="row">
                    <p class="f-12 w-400 mb-0 pb-0 fw-bold text-dark-blue">Product Name</p>
                    <h1 class="f-20 w-500 mb-4"><?php echo $data->product ?></h1>
                    <?php if($data->location_order_id != 0): ?>
                        <p class="f-12 w-400 mb-0 pb-0 fw-bold text-dark-blue">Class Tester Platform</p>
                        <h1 class="f-20 w-500 mb-4"><?php echo $dataPost->csname ?></h1>
                        <p class="f-12 w-400 mb-0 pb-0 fw-bold text-dark-blue">Burn-IN Type</p>
                        <h1 class="f-20 w-500 mb-4"><?php echo $dataPost->biname ?></h1>
                        <p class="f-12 w-400 mb-0 pb-0 fw-bold text-dark-blue">FaCT Type</p>
                        <h1 class="f-20 w-500 mb-4"><?php echo $dataPost->hfsname ?></h1>
                        <p class="f-12 w-400 mb-0 pb-0 fw-bold text-dark-blue">Manufacturing Flow STD</p>
                        <h1 class="f-20 w-500 mb-4"><?php echo $dataPost->ftname ?></h1>
                    <?php endif; ?>
                    <p class="f-12 w-400 mb-0 pb-0 fw-bold text-dark-blue">Timeline</p>
                    <h1 class="f-20 w-500 mb-4"><?php echo date('d M, Y', strtotime($data->created)) ?></h1>

                    <?php
                    if($data->location_order_id != 0):
                        ?>
                        <div class="col-lg-12 mt-3 justify-content-center">
                            <img class=" img-responsive w-100 mb-4" src="uploads/<?php echo !empty($locations2) ? $locations2[0]->img : '' ?>" alt="">
                        </div>
                    <?php endif; ?>

                    <?php if(!empty($ppv_data)): ?>
                        <?php foreach ($ppv_data as $key => $ppv_datum): ?>
                            <p class="f-12 w-400 mb-0 pb-0 fw-bold text-dark-blue">PPV Manufacturing Flow STD (Insertion <?php echo $key + 1 ?>) </p>
                            <h1 class="f-20 w-500 mb-4"><?php echo $ppv_datum->name ?></h1>
                            <p class="f-12 w-400 mb-0 pb-0 fw-bold text-dark-blue">Platform </p>
                            <h1 class="f-20 w-500 mb-4"><?php echo $ppv_datum->platform_name ?></h1>
                            <?php
                            $query = "SELECT * FROM ppv_types_orders INNER JOIN ppvs ON ppv_types_orders.ppv_id = ppvs.id WHERE ppv_orders_data_id = {$ppv_datum->podid}";
                            $types = findAllByQuery($query);
                            ?>
                            <?php if(!empty($types)): ?>
                                <?php foreach ($types as $key2 => $type): ?>
                                    <p class="f-12 w-400 mb-0 pb-0 fw-bold text-dark-blue">PPV Type <?php echo $key2 + 1 ?> </p>
                                    <h1 class="f-20 w-500 mb-4"><?php echo $type->ppv_type ?></h1>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <p class="f-12 w-400 mb-0 pb-0 fw-bold text-dark-blue">Suggested Location Codes</p>
                    <?php if(!empty($locations)): ?>
                        <?php foreach ($locations as $location): ?>
                            <?php if(!empty($location->location_code) || !empty($location->opname) || !empty($location->optype)): ?>
                                <h1 class="f-20 mt-2 w-500">
                                    <?php echo $location->location_code ?> -
                                    <?php echo $location->opname ?>
                                    <?php if(!empty($location->optype)): ?>
                                        <small style="font-style: italic; margin-left: 5px; color: #ff725e">Optype: <?php echo $location->optype ?></small>
                                    <?php endif; ?>
                                </h1>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <div class="col-lg-12 mt-3 justify-content-center">
                        <img class=" img-responsive w-100" src="uploads/<?php echo !empty($locations) ? $location->img : '' ?>" alt="">
                    </div>

                    <!--                            <div class="col-lg-12 mt-3">-->
                    <!--                                --><?php //if(!empty($ppvs)): ?>
                    <!--                                --><?php //foreach ($ppvs as $key => $ppv): ?>
                    <!--                                        <p class="f-12 w-400 mb-0 pb-0 fw-bold text-dark-blue">PPV Manufacturing Flow STD (Insertion --><?php //echo $key + 1 ?><!--)</p>-->
                    <!--                                        <h1 class="f-20 w-500 mb-4">--><?php //echo $ppv->name ?><!--</h1>-->
                    <!--                                        <p class="f-12 w-400 mb-0 pb-0 fw-bold text-dark-blue">PPV Type</p>-->
                    <!--                                        <h1 class="f-20 w-500 mb-4">--><?php //echo $ppv->type ?><!--</h1>-->
                    <!--                                --><?php //endforeach; ?>
                    <!--                                --><?php //endif; ?>
                    <!--                            </div>-->

                    <div class="col-lg-4 ">
                        <button type="button" onclick="exportExcel()" class="btn-fill ms-0 w-100 mt-4"><a class="f-14 w-500">Export</a></button>
                    </div>
                    <div class="col-lg-4 ">
                        <button type="button" class="btn-fill ms-0 w-100 mt-4 mail-btn"><a
                                    class="f-14 w-500">Send Email</a></button>
                    </div>
                    <div class="col-lg-4 ">
                        <button type="button" onclick="location.href = 'index.php'" class="btn-fill ms-0 w-100 mt-4"><a href="index.php"
                                                                                                                        class="f-14 w-500">Finish</a></button>
                    </div>

                    <!-- Table to Excel -->
                    <table id="export-table" class="d-none">
                        <thead>
                        <tr>
                            <th><b>Product Name</b></th>
                            <th><b>Class Tester Platform</b></th>
                            <th><b>Milestone</b></th>
                            <th><b>FaCT Type</b></th>
                            <?php if(!empty($locations)): ?>
                                <?php foreach($locations as $location): ?>
                                    <th><b><?php echo $location->name ?></b></th>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td id="excel-name"><?php echo $data->product ?></td>
                            <td><?php echo $data->csname ?></td>
                            <td><?php echo $data->mname ?></td>
                            <td><?php echo $data->hfsname ?></td>
                            <?php if(!empty($locations)): ?>
                                <?php foreach($locations as $location): ?>
                                    <td>
                                        <p>
                                            <?php
                                            $loc = $location->location_code . '-' . $location->opname;
                                            $loc .= !empty($location->optype) ? '-' . $location->optype : '';

                                            echo $loc;
                                            ?>
                                        </p>
                                    </td>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tr>
                        <tr>
                            <th><b>Image</b></th>
                            <td><p><?php echo SITE_URL ?>uploads/<?php echo !empty($locations) ? $locations[0]->img : '' ?></p></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

</main>

<!-- Email Modal -->
<div class="hidden modal fade" id="emailModal" tabindex="-1" aria-labelledby="emailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="output.php?id=<?php echo $_GET['id'] ?>" method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="emailModalLabel">Send Email</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="f-14 mb-0 pb-0 w-500">Enter Email 1</p>
                    <input type="email" name="email1" class="sign-input w-100" required placeholder="Email address">
                </div>
                <div class="modal-body">
                    <p class="f-14 mb-0 pb-0 w-500">Enter Email 2</p>
                    <input type="email" name="email2" class="sign-input w-100"  placeholder="Email address">
                </div>
                <div class="modal-body">
                    <p class="f-14 mb-0 pb-0 w-500">Enter Email 3</p>
                    <input type="email" name="email3" class="sign-input w-100"  placeholder="Email address">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" id="emailModal-select" name="submit" class="btn-fill text-white">Send</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php

include_once "includes/footer.php";

?>

<script src="assets/js/jquery.table2excel.js"></script>
<!--<script src="assets/js/table2excel.js"></script>-->
<script>
    function exportExcel() {
        $("#export-table").table2excel({
            // exclude CSS class
            exclude:".noExl",
            name:"Worksheet Name",
            filename:$('#excel-name').text(),//do not include extension
            fileext:".xlsx" // file extension
        });

        // var table2excel = new Table2Excel();
        // table2excel.export(document.querySelectorAll("table"));
    }
</script>