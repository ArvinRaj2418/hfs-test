<?php

include_once "includes/header.php";

if(isset($_GET['a']) && isset($_GET['id'])) {
    $query = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $res = $stmt->execute([$_GET['id']]);
    if($res) {
        redirect("users.php");
    }
}

$users = findAllByQuery('SELECT * FROM users WHERE admin = 0');

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
                                <th>Username</th>
                                <th>Email</th>
                                <th>Secondary Email</th>
                                <th class="text-center">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if(!empty($users)): ?>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?php echo $user->name ?></td>
                                    <td><?php echo $user->email ?></td>
                                    <td><?php echo $user->email2 ?></td>
                                    <td class="text-center">
                                        <div class="dropdown profile-dropdown ">
                                            <button class=" " type="button" id="dropdownMenuButton1"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fa-solid fa-ellipsis-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu shadow-sm"
                                                aria-labelledby="dropdownMenuButton1">
                                                <li class="mb-2"><a class="dropdown-item f-14" href="updateUser.php?id=<?php echo $user->id ?>"><i
                                                                class="bi bi-pencil-square w-600 me-2 f-16"></i>
                                                        Update</a></li>
                                                <li class="mb-2"><a class="dropdown-item f-14" href="users.php?a=delete&id=<?php echo $user->id ?>"><i
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