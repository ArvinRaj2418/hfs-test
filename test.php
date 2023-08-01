<?php
//phpinfo();

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

if(isset($_POST['submit'])) {
    $spreadsheet = IOFactory::load($_FILES['wdvt']['tmp_name']);
    $worksheet = $spreadsheet->getActiveSheet();

// Get the highest row number
    $highestRow = $worksheet->getHighestRow();

// Loop through each row and retrieve the value from column A
    $columnAValues = array();
    for ($row = 1; $row <= $highestRow; $row++) {
        $columnAValues[] = $worksheet->getCell('A' . $row)->getValue();
    }

// Print the retrieved values from column A
    foreach ($columnAValues as $value) {
        echo $value . "<br>";
    }
}

?>

<form action="" method="post" enctype="multipart/form-data">
    <input type="file" name="wdvt">
    <button name="submit">Submit</button>
</form>
