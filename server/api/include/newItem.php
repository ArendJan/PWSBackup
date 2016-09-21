<?php
require_once("../../php/start.php");
require_once("getItem.php");
$conn = db();

function addItem($code, $userId){
  echo "Barcode: $code ";
  $desc = getTags($code);
  echo "Description: $desc ";
  
  //Check of product al bestaat
  $checkstmt = $conn->prepare("SELECT barcode FROM products WHERE barcode = ? AND userId = ?");
  $checkstmt->execute(array($code, $userId));

  if($checkstmt->rowCount() > 0){
    echo "Exists! ";
    //Doe +1 bij aantal van product
    $upstmt = $conn->prepare("UPDATE products SET ammount = ammount + 1 WHERE barcode = ? AND userID = ?");
    $upstmt->execute(array($code, $userId));
  } else {
    echo "Doesn't exist! ";
    //Voeg product toe
    try {
      $addstmt = $conn->prepare("INSERT INTO products (barcode, ammount, description, userId) VALUES (?,1,?,?)");
      $addstmt->execute(array($code,$desc,$userId));
    }
    catch(PDOException $e)
    {
      echo "n";
    }
    echo "y";
  }
}
?>