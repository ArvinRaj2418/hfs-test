<?php

include_once "includes/header.php";

if(isset($_GET['logout'])) {
    unset($_SESSION['admin']);
    redirect('../signin.php');
}

if(isset($_GET['a']) && isset($_GET['id'])) {
    $query = "DELETE FROM ppvs WHERE id = ?";
    $stmt = $conn->prepare($query);
    $res = $stmt->execute([$_GET['id']]);
    if($res) {
        redirect("ppv.php");
    }
}

$query = "SELECT *, ppvs.id AS pid FROM ppvs";
$query .= " INNER JOIN ppv_manufacturing_flow_std pmfs on ppvs.ppv_manufacturing_flow_std_id = pmfs.id";

$ppvs = findAllByQuery($query);

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
                                    <th>PPV Tester Platform</th>
                                    <th>PPV Manufacturing Flow STD</th>
                                    <th>PPV Type</th>
                                    <th>Platform Name</th>
                                    <th class="text-center">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if(!empty($ppvs)): ?>
                                    <?php foreach ($ppvs as $ppv): ?>
                                        <tr>
                                            <td><?php echo "PPV Platform" ?></td>
                                            <td><?php echo $ppv->name ?></td>
                                            <td><?php echo $ppv->ppv_type ?></td>
                                            <td><?php echo $ppv->platform_name ?></td>
                                            <td class="text-center">
                                                <div class="dropdown profile-dropdown ">
                                                    <button class=" " type="button" id="dropdownMenuButton1"
                                                            data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="fa-solid fa-ellipsis-vertical"></i>
                                                    </button>
                                                    <ul class="dropdown-menu shadow-sm"
                                                        aria-labelledby="dropdownMenuButton1">
                                                        <li class="mb-2"><a class="dropdown-item f-14" href="output-ppv.php?id=<?php echo $ppv->pid ?>"><i
                                                                        class="bi bi-eye me-2 f-16 w-600"></i>View</a></li>
                                                        <li class="mb-2"><a class="dropdown-item f-14" href="update-ppv.php?id=<?php echo $ppv->pid ?>"><i
                                                                        class="bi bi-pencil-square w-600 me-2 f-16"></i>
                                                                Update</a></li>
                                                        <li class="mb-2"><a class="dropdown-item f-14" href="ppv.php?a=delete&id=<?php echo $ppv->pid ?>"><i
                                                                        class="bi bi-trash me-2 f-16 w-600"></i>Delete</a>
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