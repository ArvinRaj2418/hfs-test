<?php

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

//include_once "connection.php";
include_once "functions.php";

global $pdo;
$conn = $pdo->open();

if(isset($_FILES['wdvt']['name']) && $_FILES['wdvt']['name'] != '') {
    global $conn;
    $wdvt = time() . $_FILES['wdvt']['name'];
    $wdvi = time() . $_FILES['wdvi']['name'];
    $table = $_POST['table'];
    $order_id = $_POST['order_id'];
    $order_col = $table === "validations" ? "order_id" : "ppv_order_id";

    $query = "SELECT *,o.id as oID, cs.name AS csName, m.name AS mName, hfs.name AS hfsName, bi.name AS biName, ft.name AS ftName FROM orders o ";
    $query .= "INNER JOIN capability_standards cs ON o.capability_standard = cs.id ";
    $query .= "INNER JOIN bi_types bi ON o.bi_type = bi.id ";
    $query .= "INNER JOIN milestones m ON o.milestone = m.id ";
    $query .= "INNER JOIN hvm_flow_standards hfs ON o.hvm_flow_standard = hfs.id ";
    $query .= "INNER JOIN flow_types ft ON o.flow_type = ft.id ";
    $query .= "WHERE o.id = {$order_id}";

    $data = findByQuery($query);

    $query = "SELECT *, l.id AS lID FROM locations l ";
    $query .= "INNER JOIN locations_data ld ON l.id = ld.location_id ";
    $query .= "INNER JOIN columns c ON c.id = ld.column_id ";
    $query .= "WHERE l.capability_standard_id = {$data->capability_standard} ";
    $query .= "AND l.bi_type_id = {$data->bi_type} ";
    $query .= "AND l.hvm_flow_id = {$data->hvm_flow_standard} ";
    $query .= "AND l.flow_type_id = {$data->flow_type}";

    $locations = findAllByQuery($query);

    $location_codes = [];
    if(!empty($locations)) {
        foreach ($locations as $location) {
            if(!empty($location->location_code) || !empty($location->opname)) {
                array_push($location_codes, $location->location_code . " " . $location->opname);
            }
        }
    }

    $spreadsheet = IOFactory::load($_FILES['wdvt']['tmp_name']);
    $worksheet = $spreadsheet->getActiveSheet();

    // Get the highest row number
    $highestRow = $worksheet->getHighestRow();

    // Loop through each row and retrieve the value from column A
    $columnAValues = array();
    for ($row = 3; $row <= $highestRow; $row++) {
        $columnAValues[] = $worksheet->getCell('A' . $row)->getValue();
    }

    if(json_encode($location_codes) == json_encode($columnAValues)) {
        $location = '../uploads/'.$wdvt;
        move_uploaded_file($_FILES['wdvt']['tmp_name'], $location);

        $location = '../uploads/'.$wdvi;
        move_uploaded_file($_FILES['wdvi']['tmp_name'], $location);

        $query = "UPDATE $table SET workstream_text = ?, workstream_img = ? WHERE $order_col = ?";
        $stmt = $conn->prepare($query);
        $res = $stmt->execute([$wdvt, $wdvi, $_POST['order_id']]);

        if($res) {
            echo "success";
        } else {
            echo "error";
        }
    } else {
        echo "no-match";
    }
}

if(isset($_POST['validationFiles'])) {
    global $conn;
    $id = $_POST['order_id'];
    $table = $_POST['table'];
    $order_col = $table === "validations" ? "order_id" : "ppv_order_id";

    $query = "SELECT * FROM $table WHERE $order_col = ? LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->execute([$id]);
    $row = $stmt->fetch();

    echo json_encode([$row]);
}

if(isset($_FILES['cbt']['name']) && $_FILES['cbi']['name'] != '') {
    global $conn;
    $cbt = time() . $_FILES['cbt']['name'];
    $cbi = time() . $_FILES['cbi']['name'];
    $table = $_POST['table'];
    $order_col = $table === "validations" ? "order_id" : "ppv_order_id";

    $location = '../uploads/'.$cbt;
    move_uploaded_file($_FILES['cbt']['tmp_name'], $location);

    $location = '../uploads/'.$cbi;
    move_uploaded_file($_FILES['cbi']['tmp_name'], $location);

    $query = "UPDATE $table SET crystalball_text = ?, crystalball_img = ? WHERE $order_col = ?";
    $stmt = $conn->prepare($query);
    $res = $stmt->execute([$cbt, $cbi, $_POST['order_id']]);

    if($res) {
        echo "success";
    } else {
        echo "error";
    }
}

if(isset($_POST['cbvalidationFiles'])) {
    global $conn;
    $id = $_POST['order_id'];
    $table = $_POST['table'];
    $order_col = $table === "validations" ? "order_id" : "ppv_order_id";

    $query = "SELECT * FROM $table WHERE $order_col = ? LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->execute([$id]);
    $row = $stmt->fetch();

    echo json_encode([$row]);
}

if(isset($_POST['validation-ppv'])) {
    $col = $_POST['validation-ppv'];
    $order_id = $_POST['order_id'];

    $file_name = uniqid() . "-" . $_FILES['file']['name'];
    $file_tmp = $_FILES['file']['tmp_name'];
    if(move_uploaded_file($file_tmp, "../uploads/" . $file_name)) {
        $query = "UPDATE validations_ppv SET {$col} = ? WHERE ppv_order_id = ?";
        $stmt = $conn->prepare($query);
        $res = $stmt->execute([$file_name, $order_id]);

        if($res) {
            echo "success";
        } else {
            echo "error";
        }
    } else {
        echo "error";
    }
}

if(isset($_POST['opCode'])) {
    global $conn;
    $opCode = $_POST['opCode'];

    $query = "SELECT * FROM search WHERE location_code = ? ;";
    $stmt = $conn->prepare($query);
    $stmt->execute([$opCode]);
    $search = $stmt->fetch();

    echo json_encode($search);
}

if(isset($_POST['mfs'])) {
    global $conn;
    $tp = $_POST['tp'];
    $bt = $_POST['bt'];
    $ft = $_POST['ft'];
    $type = $_POST['type'];

    $query = "SELECT * FROM locations INNER JOIN flow_types ft on locations.flow_type_id = ft.id WHERE capability_standard_id = ? AND bi_type_id = ? AND hvm_flow_id = ? AND type = ? ;";
    $stmt = $conn->prepare($query);
    $stmt->execute([intval($tp), intval($bt), intval($ft), intval($type)]);
    $rows = $stmt->fetchAll();

    echo json_encode($rows);
    
}

if(isset($_POST['pmfs'])) {
    global $conn;
    $ppv_flow = $_POST['ppv_flow'];

    $query = "SELECT * FROM ppvs WHERE ppv_manufacturing_flow_std_id = ? GROUP BY ppv_type";
    $stmt = $conn->prepare($query);
    $stmt->execute([$ppv_flow]);
    $rows = $stmt->fetchAll();

    echo json_encode($rows);
}

if(isset($_POST['pmfs_flow_name'])) {
    global $conn;
    $ppv_flow = $_POST['ppv_flow'];
    $ppv_types = $_POST['ppv_types_arr'];
    $ppv_types = "'" . implode("','", $ppv_types) . "'";

    $query = "SELECT * FROM ppvs WHERE ppv_manufacturing_flow_std_id = ? AND ppv_type IN ({$ppv_types}) GROUP BY platform_name";
    $stmt = $conn->prepare($query);
    $stmt->execute([$ppv_flow]);
    $rows = $stmt->fetchAll();

    echo json_encode($rows);
}

//if(isset($_FILES['rcs']['name']) && $_FILES['rcs']['name'] != '') {
//    global $conn;
//    $rcsName = time() . $_FILES['rcs']['name'];
//    $wtlName = time() . $_FILES['wtl']['name'];
//
//    $location = '../uploads/'.$rcsName;
//    move_uploaded_file($_FILES['rcs']['tmp_name'], $location);
//
//    $location = '../uploads/'.$wtlName;
//    move_uploaded_file($_FILES['wtl']['tmp_name'], $location);
//
//    $query = "UPDATE validations SET rcs = ?, wtl = ? WHERE order_id = ?";
//    $stmt = $conn->prepare($query);
//    $res = $stmt->execute([$rcsName, $wtlName, $_POST['order_id']]);
//
//    if($res) {
//        echo "success";
//    } else {
//        echo "error";
//    }
//}
//
//if(isset($_POST['validationFiles'])) {
//    global $conn;
//    $id = $_POST['order_id'];
//
//    $query = "SELECT * FROM validations WHERE order_id = ?";
//    $stmt = $conn->prepare($query);
//    $stmt->execute([$id]);
//    $rows = $stmt->fetchAll();
//
//    echo json_encode($rows);
//}

if(isset($_POST['imgCol'])) {
    global $conn;
    $imgCol = $_POST['imgCol'];
    $imgName = time() . $_FILES[$imgCol]['name'];

    $location = '../uploads/'.$imgName;
    move_uploaded_file($_FILES[$imgCol]['tmp_name'], $location);

    $imgCol = str_replace("Admin","", $imgCol);
    $query = "UPDATE validations SET {$imgCol} = ? WHERE order_id = ?";
    $stmt = $conn->prepare($query);
    $res = $stmt->execute([$imgName, $_POST['order_id']]);

    if($res) {
        echo json_encode(["location" => $location, "img" => $imgName]);
    } else {
        echo "error";
    }
}