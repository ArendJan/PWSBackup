<?php

function connectDB(){

  include("settings.php");
try {
    $conn = new PDO("mysql:host=$servername;dbname=".$dbname, $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    }
catch(PDOException $e)
    {
      echo $e;
      echo "je fukking db functie doet het niet kut!";
    die();
    }

  return $conn;
}

?>
