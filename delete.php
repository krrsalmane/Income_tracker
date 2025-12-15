<?php


include "config/DB.php";

$id = $_GET["id"];

mysqli_query($conn, "DELETE FROM income WHERE id=$id");


header("Location: index.php");











?>