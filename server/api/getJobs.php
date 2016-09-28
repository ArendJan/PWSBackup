<?php

include_once('../php/start.php');
require_once("include/checkUserId.php");

$conn = db();

if (!isset($_POST['JSON'])){
  die("You have to post your values in _POST['JSON']");
}

$data = json_decode($_POST['JSON'],true);

$userId = $data['UserId'];
$status = $data["Status"];
$type = $data["Type"];

$json_array = array();

if (checkUserId($userId) == false){
  die ("You forgot your UserId, or gave an invalid UserId!");
}

//Different statements if Type or Status is empty

if ($status == "" && $type == ""){
  $stmt = $conn->prepare('SELECT * FROM jobs WHERE userId = ? AND status = ?');
  $stmt->execute(array($userId, "new"));
} elseif ($status == ""){
  $stmt = $conn->prepare('SELECT * FROM jobs WHERE userId = ? AND type = ? AND status = ?');
  $stmt->execute(array($userId, $type, "new"));
} elseif ($type == "") {
  $stmt = $conn->prepare('SELECT * FROM jobs WHERE userId = ? AND status = ?');
  $stmt->execute(array($userId, $status));
}  else {
  $stmt = $conn->prepare('SELECT * FROM jobs WHERE userId = ? AND type = ? AND status = ?');
  $stmt->execute(array($userId, $type, $status));
}

$result = $stmt -> fetchAll();
foreach( $result as $row ) {

  switch ($type) {

    case "barcode":
    //echo "Type: " . $row['type'] . "<br>Code: " . $row['qrcode'] . "<br>Status: " . $row['status'] . "<br>JobId: " . $row['ID'] . "<br><br>";
    $row_array['Type'] = $row['type'];
    $row_array['Code'] = $row['barcode'];
    $row_array['Status'] = $row['status'];
    $row_array['JobId'] = $row['ID'];
    array_push($json_array,$row_array);
    break;

    case "text":
    //echo "Type: " . $row['type'] . "<br>Text: " . $row['text'] . "<br>Status: " . $row['status'] . "<br>JobId: " . $row['ID'] . "<br><br>";
    $row_array['Type'] = $row['type'];
    $row_array['Text'] = $row['text'];
    $row_array['Status'] = $row['status'];
    $row_array['JobId'] = $row['ID'];
    array_push($json_array,$row_array);
    break;

    case "list":
    break;

    case "reboot":
    $row_array['Type'] = $row['type'];
    $row_array['Status'] = $row['status'];
    $row_array['JobId'] = $row['ID'];
    array_push($json_array,$row_array);
    break;

    case "shutdown":
    $row_array['Type'] = $row['type'];
    $row_array['Status'] = $row['status'];
    $row_array['JobId'] = $row['ID'];
    array_push($json_array,$row_array);
    break;

    case "update":
    $row_array['Type'] = $row['type'];
    $row_array['Status'] = $row['status'];
    $row_array['JobId'] = $row['ID'];
    array_push($json_array,$row_array);
    break;

    default:
    $row_array['Type'] = $row['type'];
    $row_array['Code'] = $row['barcode'];
    $row_array['Text'] = $row['text'];
    $row_array['List'] = $row['list'];
    $row_array['Status'] = $row['status'];
    $row_array['JobId'] = $row['ID'];
    array_push($json_array,$row_array);
  }
}

echo json_encode($json_array);
?>