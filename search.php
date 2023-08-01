<?php

include_once "includes/header.php";

?>

<main class="content">
    <div class="row">
        <div class="col-lg-12 ">
            <div class="d-flex justify-content-between buttons-row">
                <div class="main-heading  w-100">
                    <h1 class="f-14 my-4">Search</h1>
                </div>
            </div>

            <div class="box shadow">
                <form action="">
                    <div class="row justify-content-center p-0 m-0">
                        <div class="col-lg-12 px-0">
                            <p id="opcode-error" class="alert alert-danger d-none">Please enter operation code</p>
                            <p id="opcode-error-result" class="alert alert-danger d-none">No data found</p>
                            <p class="f-14 mb-1 pb-0 w-500">Operation Code</p>
                            <input type="text" id="op_code" class="sign-input w-100 mb-3" placeholder="Insert Operation Code">
                        </div>

                        <div class="col-lg-6 ">
                            <button type="button" onclick="location.href = 'index.php'" class="btn-fill ms-0 w-100 mt-4"><a href="index.php"
                                    class="f-14 w-500">Cancel</a></button>
                        </div>
                        <div class="col-lg-6 ">
                            <button type="button" id="show-result" class="btn-fill ms-0 w-100 mt-4"><a
                                                                        class="f-14 w-500">Confirm</a></button>
                        </div>

                        <!--Result-->
                        <div id="result-card" class="d-none col-xl-6 col-lg-6 col-md-6 my-4">
                            <div style="min-height: 200px" class="card d-flex flex-column ">
                                <h1 class="f-20 w-500 mb-4 text-center text-success">Result</h1>
                                <p class="f-12 w-400 mb-0 pb-0 text-success">Operation Code</p>
                                <h1 id="opcode-result" class="f-20 w-500 mb-4 text-success"></h1>
                                <p class="f-12 w-400 mb-0 pb-0 text-success">Optype</p>
                                <h1 id="optype-result" class="f-20 w-500 mb-4 text-success"></h1>
                                <p class="f-12 w-400 mb-0 pb-0 text-success">Description</p>
                                <h1 id="desc-result" class="f-20 w-500 text-success"></h1>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>

</main>

<?php

include_once "includes/footer.php";

?>