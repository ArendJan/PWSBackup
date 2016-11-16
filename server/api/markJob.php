<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//Alles wat nodig is require_once
require_once('../php/start.php');
require_once("include/checkUserId.php");
require_once("include/checkJobId.php");

$conn = db();

if (!isset($_POST['JSON'])){
  die("You have to post your values in _POST['JSON']");
}

$data = json_decode($_POST['JSON'],true);

$userId = $data['UserId'];

require_once("include/log.php");
logging(basename($_SERVER['PHP_SELF']),$_POST['JSON'],$userId);

$jobId = $data['JobId'];

if (!isset($data["Status"]) || $data["Status"] == ""){
  $status = "done";
} else {
  $status = $data["Status"];
}

if ($status == "new" || $status == "done"){
  if (checkUserId($userId) == false){
    die ('You forgot your userId, or gave an invalid userId!');
  }
  if (checkJobId($jobId) == false){
    die ('You forgot your jobId, or gave an invalid jobId!');
  }

try{
  $upstmt = $conn->prepare('UPDATE jobs SET status = ? WHERE userId = ? AND ID = ?');
  $upstmt->execute(array($status, $userId, $jobId));
}
//Wanneer er een error komt met de query, komt dit in de erroLogging tabel dmv de functie errorLogging in log.php
catch (PDOException $e){
  errorLogging(basename($_SERVER['PHP_SELF']), $_POST['JSON'], $userId, $e);
  die;
}

} else {
  die ('You gave an invalid status!');
}

$arr = array('JobId' => $jobId, 'Status' => $status);
echo json_encode($arr);

?>
