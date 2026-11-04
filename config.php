<?php
try{
    $db = new PDO("mysql:host=localhost;dbname=marketmap;charset=utf8","root","1972");
    $db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    // session_start();
}catch(PDOException $e){
    die("ERREUR : " . $e->getMessage());
}

?>