<?php


include "config/DB.php";

$id = $_GET["id"];
mysqli_query($conn,"DELETE from expense where id=$id ");

header("location: index.php");
