<?php

include_once "includes/header.php";

if(!isset($_SESSION['user'])) {
    redirect('signin.php');
}

if(isset($_GET['logout'])) {
    unset($_SESSION['user']);
    redirect('signin.php');
}

$page = 0;
$perPage = 6;

if(isset($_GET['page'])) {
    if($_GET['page'] == '' || $_GET['page'] == 1) {
        $page = 0;
    } else {
        $page = ($_GET['page'] * $perPage) - $perPage;
    }
}

$expiry = date('Y-m-d', strtotime(date('Y-m-d') . '-40 days'));

$query = "(SELECT id, NULL AS location_order_id, product, milestone, capability_standard, flow_type, created, 'order' as type FROM orders WHERE created > '{$expiry}' AND user_id = {$_SESSION['user']->id})
          UNION ALL
          (SELECT id, location_order_id, product, milestone, NULL AS capability_standard, NULL AS flow_type, created, 'ppv_order' as type FROM ppv_orders WHERE created > '{$expiry}' AND user_id = {$_SESSION['user']->id}) 
          ORDER BY created DESC";

$orders = findAllByQuery($query);

if(!empty($orders)) {
    $totalResults = count($orders);
    $pages = ceil($totalResults / $perPage);

    $query = "(SELECT id, NULL AS location_order_id, product, milestone, capability_standard, flow_type, created, 'order' as type FROM orders WHERE created > '{$expiry}' AND user_id = {$_SESSION['user']->id})
              UNION ALL
              (SELECT id, location_order_id, product, milestone, NULL AS capability_standard, NULL AS flow_type, created, 'ppv_order' as type FROM ppv_orders WHERE created > '{$expiry}' AND user_id = {$_SESSION['user']->id} AND location_order_id = 0) 
              ORDER BY created DESC LIMIT {$page}{$perPage}";

    $orders = findAllByQuery($query);
}


?>

<main class="content">
    <div class="row">
        <div class="col-lg-12 ">
            <div class="main-heading  w-100">
                <h1 class="f-14 my-4">Dashboard</h1>
            </div>

<!--            --><?php //echo "<pre>";
//            print_r($orders);
//            echo "</pre>"; ?>

            <div class="row mt-3">
                <?php if(!empty($orders)): ?>
                <?php foreach ($orders as $order):
                ?>
                    <?php if($order->type == "order"):
                        $capabilityStandard = findByQuery("SELECT * FROM capability_standards WHERE id = {$order->capability_standard}");
                        $milestone = findByQuery("SELECT * FROM milestones WHERE id = {$order->milestone}");
                        $flow_type = findByQuery("SELECT * FROM flow_types WHERE id = {$order->flow_type}");
                        $validation = findByQuery("SELECT * FROM validations WHERE order_id = {$order->id}");

                        $ppv_exists = findByQuery("SELECT id FROM ppv_orders WHERE location_order_id = {$order->id}")
                        ?>
                        <div class="col-xl-3 col-lg-4 col-md-4 mb-3">
                            <div class="card  d-flex flex-column ">
                                <?php
                                if(!empty($validation->workstream_text) && !empty($validation->workstream_img) && !empty($validation->crystalball_text)  && !empty($validation->crystalball_img)):
                                    ?>
                                    <a href="validationResult.php?id=<?php echo $order->id ?>" class="f-12 m-0 p-1 align-self-end validate-link border border-light bg-success text-light rounded mb-2">Validated</a>
                                <?php else: ?>
                                    <a href="validationResult.php?id=<?php echo $order->id ?>" class="f-12 m-0 p-1 align-self-end validate-link border border-light bg-danger text-light rounded mb-2">Not Validated</a>
                                <?php endif; ?>
                                <p class="f-12 w-400 mb-0 pb-0">Product Name</p>
                                <h1 class="f-20 w-500 mb-4"><?php echo $order->product ?></h1>
                                <p class="f-12 w-400 mb-0 pb-0">Class Tester Platform</p>
                                <h1 class="f-20 w-500 mb-4"><?php echo $capabilityStandard->name ?></h1>
                                <p class="f-12 w-400 mb-0 pb-0">Milestone</p>
                                <h1 class="f-20 w-500"><?php echo $milestone->name ?></h1>
                                <p class="f-12 w-400 mb-0 pb-0">Manufacturing Flow STD</p>
                                <h1 class="f-20 w-500"><?php echo $flow_type->name ?></h1>
                                <a href="output<?php echo !empty($ppv_exists) ? '-ppv' : '' ?>.php?id=<?php echo !empty($ppv_exists) ? $ppv_exists->id : $order->id ?>"><button class="card-btn text-white btn-fill-2 w-100 mx-0 px-0">Detail</button></a>
                            </div>
                        </div>
                    <?php else:
                        $milestone = findByQuery("SELECT * FROM milestones WHERE id = {$order->milestone}");
                        $validation = findByQuery("SELECT * FROM validations_ppv WHERE ppv_order_id = {$order->id}");
//                        $pmfs = findByQuery("SELECT * FROM ppv_orders_data pod INNER JOIN ppv_manufacturing_flow_std pmfs ON pmfs.id = pod.ppv_manufacturing_flow_std_id WHERE pod.ppv_order_id = {$order->id}");
                        ?>
                        <div class="col-xl-3 col-lg-4 col-md-4 mb-3">
                            <div class="card  d-flex flex-column ">
                                <?php
                                if(!empty($validation->workstream_text) && !empty($validation->workstream_img) && !empty($validation->crystalball_text)  && !empty($validation->crystalball_img)):
                                    ?>
                                    <a href="validationResult-ppv.php?id=<?php echo $order->id ?>" class="f-12 m-0 p-1 align-self-end validate-link border border-light bg-success text-light rounded mb-2">Validated</a>
                                <?php else: ?>
                                    <a href="validationResult-ppv.php?id=<?php echo $order->id ?>" class="f-12 m-0 p-1 align-self-end validate-link border border-light bg-danger text-light rounded mb-2">Not Validated</a>
                                <?php endif; ?>
                                <p class="f-12 w-400 mb-0 pb-0">Product Name</p>
                                <h1 class="f-20 w-500 mb-4"><?php echo $order->product ?></h1>
                                <p class="f-12 w-400 mb-0 pb-0">PPV Tester Platform</p>
                                <h1 class="f-20 w-500 mb-4"><?php echo "PPV Platform" ?></h1>
                                <p class="f-12 w-400 mb-0 pb-0">Milestone</p>
                                <h1 class="f-20 w-500"><?php echo $milestone->name ?></h1>
<!--                                <p class="f-12 w-400 mb-0 pb-0">PPV Manufacturing Flow STD</p>-->
<!--                                <h1 class="f-20 w-500">--><?php //echo $pmfs->name ?><!--</h1>-->
                                <a href="output-ppv.php?id=<?php echo $order->id ?>"><button class="card-btn text-white btn-fill-2 w-100 mx-0 px-0">Detail</button></a>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
<!--                --><?php //else: ?>
<!--                        <div style="min-height: 63vh" class="d-flex flex-column justify-content-center align-items-center">-->
<!--                            <img width="200px" src="assets/images/no-data.svg" alt="">-->
<!--                            <p class="p-0 pt-3 m-0 f-16">No data found</p>-->
<!--                        </div>-->
                <?php endif; ?>

                <?php if(!empty($ppv_orders)): ?>
                    <?php foreach ($ppv_orders as $order):
                        $ppv = findByQuery("SELECT * FROM ppvs WHERE id = {$order->milestone}");
                        $milestone = findByQuery("SELECT * FROM milestones WHERE id = {$order->milestone}");
                        $validation = findByQuery("SELECT * FROM validations_ppv WHERE ppv_order_id = {$order->id}");
                        ?>
                        <div class="col-xl-3 col-lg-4 col-md-4 mb-3">
                            <div class="card  d-flex flex-column ">
                                <?php
                                if(!empty($validation->rcs) && !empty($validation->wtl) && !empty($validation->md)):
                                    ?>
                                    <a href="validationResult.php?id=<?php echo $order->id ?>" class="f-12 m-0 p-1 align-self-end validate-link border border-light bg-success text-light rounded mb-2">Validated</a>
                                <?php else: ?>
                                    <a href="validationResult.php?id=<?php echo $order->id ?>" class="f-12 m-0 p-1 align-self-end validate-link border border-light bg-danger text-light rounded mb-2">Not Validated</a>
                                <?php endif; ?>
                                <p class="f-12 w-400 mb-0 pb-0">Product Name</p>
                                <h1 class="f-20 w-500 mb-4"><?php echo $order->product ?></h1>
                                <p class="f-12 w-400 mb-0 pb-0">PPV Tester Platform</p>
                                <h1 class="f-20 w-500 mb-4"><?php echo $order->name ?></h1>
                                <p class="f-12 w-400 mb-0 pb-0">Milestone</p>
                                <h1 class="f-20 w-500"><?php echo $milestone->name ?></h1>
                                <p class="f-12 w-400 mb-0 pb-0">Manufacturing Flow STD</p>
                                <h1 class="f-20 w-500"><?php echo $flow_type->name ?></h1>
                                <a href="output.php?id=<?php echo $order->id ?>"><button class="card-btn text-white btn-fill-2 w-100 mx-0 px-0">Detail</button></a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <!--                --><?php //else: ?>
                    <!--                        <div style="min-height: 63vh" class="d-flex flex-column justify-content-center align-items-center">-->
                    <!--                            <img width="200px" src="assets/images/no-data.svg" alt="">-->
                    <!--                            <p class="p-0 pt-3 m-0 f-16">No data found</p>-->
                    <!--                        </div>-->
                <?php endif; ?>
            </div>
        </div>

    </div>

    <?php if(!empty($orders)): ?>
        <nav aria-label="Page navigation example">
            <ul class="pagination justify-content-center">
                <?php
                $firstPage = false;
                if(!isset($_GET['page']) || $_GET['page'] == 1) {
                    $firstPage = true;
                }
                ?>
                <li class="page-item <?php echo $firstPage ? 'disabled' : '' ?>">
                    <a class="page-link" href="index.php?page=<?php echo isset($_GET['page']) ? $_GET['page'] - 1 : '' ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                        <span class="sr-only">Previous</span>
                    </a>
                </li>
                <?php if($pages == 0): ?>
                    <li class="page-item"><a class="page-link" href="#">1</a></li>
                <?php else: ?>
                    <?php for ($i = 1; $i <= $pages; $i++): ?>
                        <li class="page-item <?php echo (isset($_GET['page']) && $_GET['page'] == $i) || (!isset($_GET['page']) && $i == 1) ? 'active' : '' ?>"><a class="page-link" href="index.php?page=<?php echo $i ?>"><?php echo $i ?></a></li>
                    <?php endfor; ?>
                <?php endif; ?>
                <?php
                $lastPage = false;
                if((isset($_GET['page']) && $_GET['page'] == $pages) || $pages == 1) {
                    $lastPage = true;
                }
                ?>
                <li class="page-item <?php echo $lastPage ? 'disabled' : '' ?>">
                    <a class="page-link" href="index.php?page=<?php echo !isset($_GET['page']) ? 2 : $_GET['page'] + 1 ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                        <span class="sr-only">Next</span>
                    </a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>

</main>

<?php

include_once "includes/footer.php";

?>