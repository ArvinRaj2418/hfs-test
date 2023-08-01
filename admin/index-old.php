<?php

include_once "includes/header.php";

if(isset($_GET['logout'])) {
    unset($_SESSION['admin']);
    redirect('../signin.php');
}

$query = "SELECT *,cs.name AS csName,hfs.name AS hfsName,ft.name AS ftName, c.name AS cName FROM locations_data ld ";
$query .= "INNER JOIN columns c ON ld.column_id = c.id ";
$query .= "INNER JOIN locations l ON ld.location_id = l.id ";
$query .= "INNER JOIN capability_standards cs ON l.capability_standard_id = cs.id ";
$query .= "INNER JOIN hvm_flow_standards hfs ON l.hvm_flow_id = hfs.id ";
$query .= "INNER JOIN flow_types ft ON l.flow_type_id = ft.id";

$locations = findAllByQuery($query);

$columns = array();
if(!empty($locations)) {
    foreach ($locations as $location) {
        $operation = $location->location_code . '-' . $location->opname;
        $operation .= !empty($location->optype) ? '-' . $location->optype : '';
        $columns += [$location->cname => $operation];
    }
}

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
                                <th>Class Tester Platform</th>
                                <th>FaCT Type</th>
                                <th>Manufacturing Flow STD</th>
                                <?php if(!empty($columns)): ?>
                                <?php foreach ($columns as $key => $column): ?>
                                    <th><?php echo $key ?></th>
                                <?php endforeach; ?>
                                <?php endif; ?>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if(!empty($locations)): ?>
                            <?php foreach ($locations as $location): ?>
                                <tr>
                                    <td><?php echo $location->csname ?></td>
                                    <td><?php echo $location->hfsname ?></td>
                                    <td><?php echo $location->ftname ?></td>
                                    <?php if(!empty($columns)): ?>
                                        <?php foreach ($columns as $key => $column): ?>
                                            <th><?php echo $column ?></th>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                    <td class="text-center">
                                        <div class="dropdown profile-dropdown ">
                                            <button class=" " type="button" id="dropdownMenuButton1"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fa-solid fa-ellipsis-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu shadow-sm"
                                                aria-labelledby="dropdownMenuButton1">
                                                <li class="mb-2"><a class="dropdown-item f-14" href="#"><i
                                                                class="bi bi-eye me-2 f-16 w-600"></i>View</a></li>
                                                <li class="mb-2"><a class="dropdown-item f-14" href="#"><i
                                                                class="bi bi-pencil-square w-600 me-2 f-16"></i>
                                                        Edit</a></li>
                                                <li class="mb-2"><a class="dropdown-item f-14" href="#"><i
                                                                class="bi bi-person-x me-2 f-16 w-600"></i>Delete</a>
                                                </li>
                                            </ul>
                                        </div>
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

<?php

include_once "includes/footer.php";

?>