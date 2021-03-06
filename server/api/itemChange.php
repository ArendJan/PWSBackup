<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//Alles wat nodig is require_once
require_once('../php/start.php');
require_once("include/checkUserId.php");
require_once("include/log.php");
require_once("include/newItem.php");
require_once("include/delItem.php");
require_once("include/delOpen.php");
require_once("include/delClosed.php");
require_once("include/openItem.php");
require_once("include/checkUserId.php");

$conn = db();

if (!isset($_POST['JSON'])){
  errorLogging(basename($_SERVER['PHP_SELF']), $_POST['JSON'], "", "No _POST['JSON']");
  die;
}

$data = json_decode($_POST['JSON'],true);

if (checkUserId($data['UserId']) == false){
  errorLogging(basename($_SERVER['PHP_SELF']), $_POST['JSON'], "", "Forgot userId, or invalid userId");
  die;
}


$userId = $data['UserId'];


if (!isset($data["Barcode"])){
  errorLogging(basename($_SERVER['PHP_SELF']), $_POST['JSON'], $userId, "You forgot a barcode");
  die;
}

$code = $data["Barcode"];

if (!isset($data["Action"]) || $data['Action'] == ""){
  $action = "add";
} else {
  $action = $data["Action"];
}

if($action == "add"){
  addItem($code, $userId);
} else if ($action == "del") {
  delItem($code, $userId);
} else if ($action == "open") {
  openItem($code, $userId);
} else if ($action == "delOpen") {
  delOpen($code, $userId);
} else if ($action == "delClosed") {
  delClosed($code, $userId);
}else{
  die ("Not a correct action");
}

if ($GLOBALS['doLog'] ==  "y") {
  logging(basename($_SERVER['PHP_SELF']),$_POST['JSON'],$userId);
}

try{
  $returnstmt = $conn->prepare('SELECT * FROM products WHERE userId = ? AND barcode = ?');
  $returnstmt->execute(array($userId, $code));
}
//Wanneer er een error komt met de query, komt dit in de erroLogging tabel dmv de functie errorLogging in log.php
catch (PDOException $e){
  errorLogging(basename($_SERVER['PHP_SELF']), $_POST['JSON'], $userId, $e);
  die;
}

$result = $returnstmt -> fetch();

$return_arr = array();

$row_array['Id'] = intval($result['ID']);
$row_array['Name'] = $result['description'];
$row_array['Barcode'] = strval($result['barcode']);
$row_array['Closed'] = intval($result['closed']);
$row_array['Open'] = intval($result['open']);

array_push($return_arr,$row_array);
echo json_encode($return_arr);

?>
