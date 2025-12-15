<?php
include "config/DB.php"; 
if($conn){

   
    $sql_query = "SELECT id,amount, description, categorie,my_date FROM expense order by id desc";
    $spent = mysqli_query($conn,$sql_query);
    if (!$spent) {
        die("Error retrieving data: " . mysqli_error($conn));
    }

    $row_count = mysqli_num_rows($spent);
} else {
    die("Database connection failed.");
}



?>
