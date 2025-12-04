<?php


include "config/DB.php";

$id = $_GET["id"];
mysqli_query($conn,"DELETE from expense where id=$id ");

header("location: display_expenses.php");
